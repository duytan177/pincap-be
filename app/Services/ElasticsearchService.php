<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;


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
     * 🔹 Search media theo embedding vector 
     *
     * @param string $index
     * @param array $queryVector
     * @param array|null $filters
     * @param array|null $mustNotFilters
     * @param float|null $minScore
     * @param int|null $from
     * @param int|null $size
     * @param array|null $sourceFields
     * @return array
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
    ): array {
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

        return $res->asArray();
    }

    /**
     * Format Elasticsearch search result for get media_ids
     *
     * @param array $esResult
     * @return array
     */
    public function formatMediaIds(array $esResult): array
    {
        $mediaIds = [];

        if (!isset($esResult['hits']['hits'])) {
            return $mediaIds;
        }

        foreach ($esResult['hits']['hits'] as $hit) {
            if (isset($hit['_source']['media_id'])) {
                $mediaIds[] = $hit['_source']['media_id'];
            }
        }

        return $mediaIds;
    }
}
