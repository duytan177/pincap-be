<?php

namespace App\Jobs;

use App\Services\KafkaProducerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendInstagramMediaEventsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $events;
    public const TOPIC = "user_behavior";

    public function __construct(array $events)
    {
        $this->events = $events;
    }

    public function handle()
    {
        try {
            $kafka = new KafkaProducerService(self::TOPIC);

            foreach ($this->events as $event) {
                $kafka->send(json_encode($event));
            }

        } catch (\Throwable $e) {
            Log::error("Kafka Event Send Failed", [
                "error" => $e->getMessage()
            ]);
        }
    }
}
