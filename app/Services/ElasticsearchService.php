<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Log;

class ElasticsearchService
{
    protected $client;

    // Singleton instance
    protected static ?self $instance = null;

    // Private constructor
    private function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts([config('services.elasticsearch.host')])
            ->build();
    }

    // Lấy instance singleton
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Update document theo ID
     *
     * @param string $index
     * @param string $id
     * @param array $fields
     * @return array
     */
    public function updateDocument(string $index, string $id, array $fields): array
    {
        $response = $this->client->update([
            'index' => $index,
            'id' => $id,
            'body' => [
                'doc' => $fields
            ]
        ]);
        return $response->asArray();

    }

    // 🔥 Bulk Update
    public function bulkUpdate(array $body): array
    {
        return $this->client->bulk([
            'body' => $body
        ])->asArray();
    }

    /**
     * Delete document by ID
     *
     * @param string $index
     * @param string $id
     * @return array
     */
    public function deleteDocument(string $index, string $id): array
    {
        try {
            $response = $this->client->delete([
                'index' => $index,
                'id' => $id,
            ]);
            return $response->asArray();
        } catch (\Exception $e) {
            Log::error("Elasticsearch deleteDocument error: " . $e->getMessage());
            throw $e;
        }
    }

    public function getDocumentById(string $index, string $id): ?array
    {
        try {
            $response = $this->client->get([
                'index' => $index,
                'id' => $id,
            ]);

            return $response['_source'] ?? null;

        } catch (\Exception $e) {
            Log::error("Elasticsearch getDocumentById error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * KNN search — return raw ES data (no formatting)
     */
    public function searchKNN(
        string $index,
        array $embedding,
        int $page = 1,
        int $perPage = 20,
    ) {

        $from = ($page - 1) * $perPage;
        $k = $from + $perPage;
        $numCandidates = max($perPage, $k);
        $params = [
            'index' => $index,
            'body' => [
                // "from" => $from,
                "size" => 1000,
                "query" => [
                    "knn" => [
                        "field" => "embedding",
                        "query_vector" => $embedding,
                        "k" => 1000,
                        "num_candidates" => 5000
                    ]
                ]
            ]
        ];

        return $this->client->search($params);
    }

    public function formatEsResult($rawEsResult)
    {
        if (!isset($rawEsResult['hits']['hits'])) {
            return [];
        }

        $data = [];

        foreach ($rawEsResult['hits']['hits'] as $hit) {
            //
            $data[] = $hit['_source'] ?? [];
        }

        return $data;
    }


    public function formatEsResultWithScore($rawEsResult)
    {
        if (!isset($rawEsResult['hits']['hits'])) {
            return [];
        }

        $data = [];

        foreach ($rawEsResult['hits']['hits'] as $hit) {
            $data[] = [
                'score' => $hit['_score'] ?? null,
                'data' => $hit['_source'] ?? []
            ];
        }

        return $data;
    }
    /**
     * 🔹 Lấy media theo media_id
     *
     * @param string $index
     * @param string $mediaId
     * @param array|null $sourceFields
     * @return array|null
     */
    public function getMediaById(string $index, string $mediaId, ?array $sourceFields = null): ?array
    {
        $body = [
            'query' => [
                'term' => ['media_id' => $mediaId]
            ],
            'size' => 1
        ];

        if (!empty($sourceFields)) {
            $body['_source'] = $sourceFields;
        }

        $res = $this->client->search([
            'index' => $index,
            'body' => $body
        ])->asArray();

        if (!isset($res['hits']['hits'][0]['_source'])) {
            return null;
        }

        return $res['hits']['hits'][0]['_source'];
    }
    /**
     * Search media theo embedding vector
     *
     * @param string $index
     * @param array $queryVector
     * @param array|null $filters
     * @param array|null $mustNotFilters
     * @param float|null $minScore
     * @param int|null $from
     * @param int|null $size
     * @param array|null $sourceFields
     */
    public function searchEmbedding(
        string $index,
        array $queryVector,
        ?array $filters = null,
        ?array $mustNotFilters = null,
        ?float $minScore = 0.8,
        ?int $from = 0,
        ?int $size = 20,
        ?array $sourceFields = null
    ) {
        $body = ['query' => ['bool' => []]];

        if (!empty($filters)) {
            $body['query']['bool']['filter'] = $filters;
        }

        if (!empty($mustNotFilters)) {
            $body['query']['bool']['must_not'] = $mustNotFilters;
        }

        $body['query']['bool']['must'] = [
            'script_score' => [
                'query' => ['match_all' => new \stdClass()],
                'script' => [
                    'source' => '(cosineSimilarity(params.query_vector, "embedding") + 1.0) / 2',
                    'params' => ['query_vector' => $queryVector]
                ]
            ]
        ];

        if ($minScore !== null) {
            $body['min_score'] = $minScore;
        }
        if ($from !== null) {
            $body['from'] = $from;
        }
        if ($size !== null) {
            $body['size'] = $size;
        }
        if (!empty($sourceFields)) {
            $body['_source'] = $sourceFields;
        }

        $res = $this->client->search([
            'index' => $index,
            'body' => $body
        ]);

        return $res;
    }

    /**
     * Format Elasticsearch search result for get media_ids
     *
     * @param array $esResult
     * @return array
     */
    public function formatMediaIds(array $esResult): array
    {
        $ids = [];

        foreach ($esResult as $item) {
            if (isset($item['media_id'])) {
                // convert string to int
                $ids[] = $item['media_id'];
            }
        }

        return $ids;
    }
}
