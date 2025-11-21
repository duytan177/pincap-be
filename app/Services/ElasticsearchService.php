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
            ->setHosts([config('services.eslasticsearch.host')])
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
}
