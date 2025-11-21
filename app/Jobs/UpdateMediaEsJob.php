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

    public function __construct(array $mediaIds)
    {
        $this->mediaIds = $mediaIds;
    }

    public function handle()
    {
        $es = ElasticsearchService::getInstance();
        $index = config('services.elasticsearch.index');

        $paramUpdate = [
            'is_deleted' => true,
            'updated_at' => now()->toDateTimeString(),
        ];

        // 🔥 Build bulk body
        $bulkBody = [];

        foreach ($this->mediaIds as $mediaId) {
            $bulkBody[] = [
                'update' => [
                    '_index' => $index,
                    '_id'    => $mediaId,
                ]
            ];

            $bulkBody[] = [
                'doc' => $paramUpdate
            ];
        }

        // 🔥 Execute bulk update
        $es->bulkUpdate($bulkBody);
    }
}
