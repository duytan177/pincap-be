<?php

namespace App\Services;

use RdKafka\Producer;
use RdKafka\TopicConf;

class KafkaProducerService
{
    protected $producer;
    protected $topic;

    public function __construct(string $topic = 'user_behavior')
    {
        $this->producer = new Producer();
        $this->producer->addBrokers(config('services.kafka.kafka_brokers'));

        $this->topic = $this->producer->newTopic($topic);
    }

    public function send(string $message): void
    {
        $this->topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);
        $this->producer->flush(1000);
    }
}
