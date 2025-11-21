<?php

namespace App\Jobs;

use App\Services\ElasticsearchService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateMediaEsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $mediaIds;
    const INDEX = 'media_embeddings_test_v3';
    public function __construct(array $mediaIds)
    {
        $this->mediaIds = $mediaIds;
    }

    public function handle()
    {
        $es = ElasticsearchService::getInstance();

        foreach ($this->mediaIds as $mediaId) {
            $es->updateDocument(self::INDEX, $mediaId, [
                'is_deleted' => true,
                'updated_at' => now()->toDateTimeString(),
            ]);
        }
    }
}
