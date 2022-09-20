<?php

namespace Outl1ne\LaravelGoogleCloudQueues;

use Google\Cloud\Tasks\V2\CloudTasksClient;

class Queue
{
    protected array $queue;
    protected array $config;

    public function __construct($queueConfig, $config)
    {
        $this->queue = $this->parse($queueConfig);
        $this->config = $config;
    }

    protected function parse($queueConfig)
    {
        return $queueConfig;
    }

    public function getFinalName()
    {
        return $this->config['queue_prefix']. $this->queue['name'];
    }

    public function getQueueConfig()
    {
        return [
            'rate_limits' => array_merge($this->config['rate_limits'] ?? [], $this->queue['rate_limits'] ?? []),
            'retry_config' => array_merge($this->config['retry_config'] ?? [], $this->queue['retry_config'] ?? []),
        ];
    }
}