<?php

namespace Outl1ne\LaravelGoogleCloudQueues;

use Google\Cloud\Tasks\V2\CloudTasksClient;

class Queues
{
    /**
     * @var Client
     */
    protected $client;

    protected $queues;
    protected $config;

    public function __construct()
    {
        $this->client = new Client(config('google-cloud-queues'), new CloudTasksClient());
        $this->queues = $this->parseQueues(config('google-cloud-queues'), config('google-cloud-queues.queues'));
    }

    protected function parseQueues($config, $queuesConfig)
    {
        return collect($queuesConfig)->map(fn($queueConfig) => new Queue($queueConfig, $config));
    }

    public function sync()
    {
        $googleCloudQueues = $this->client->getQueues();

        $this->createMissingQueues($googleCloudQueues);
        $this->updateQueues();
    }

    protected function createMissingQueues($googleCloudQueues)
    {
        $missingQueues = $this->getMissingQueueNames(
            $this->queues,
            $this->getExistingQueueNames($googleCloudQueues),
        );

        foreach ($missingQueues as $missingQueue) {
            $this->client->createQueue($missingQueue->getFinalName());
        }
    }

    protected function updateQueues()
    {
        foreach ($this->queues as $queue) {
            $this->updateQueue($queue);
        }
    }

    protected function updateQueue($queue)
    {
        $this->client->updateQueue($queue->getFinalName(), $queue->getQueueConfig());
    }

    protected function getExistingQueueNames($queues)
    {
        $googleCloudQueueNames = [];

        foreach ($queues as $queue) {
            $googleCloudQueueNames[] = end(explode('/', $queue->getName()));
        }

        return $googleCloudQueueNames;
    }

    protected function getMissingQueueNames($configuredQueues, $existingQueueNames)
    {
        return $configuredQueues
            ->filter(fn($configuredQueue) => !in_array($configuredQueue->getFinalName(), $existingQueueNames));
    }
}