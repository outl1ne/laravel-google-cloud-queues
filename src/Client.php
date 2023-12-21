<?php

namespace Outl1ne\LaravelGoogleCloudQueues;

use Google\Cloud\Tasks\V2\CloudTasksClient;
use Google\Cloud\Tasks\V2\Queue;
use Google\Cloud\Tasks\V2\RateLimits;
use Google\Cloud\Tasks\V2\RetryConfig;
use Google\Protobuf\Duration;

class Client
{
    /**
     * @var CloudTasksClient
     */
    protected $client;

    protected array $config;
    protected string $locationName;

    public function __construct(array $config, CloudTasksClient $client)
    {
        $this->client = $client;
        $this->config = $config;
        $this->locationName = $this->client::locationName($this->config['project_id'], $this->config['location']);
    }

    public function getQueues()
    {
        $pagedListResponse = $this->client->listQueues($this->locationName);
        $queues = [];

        foreach ($pagedListResponse->iteratePages() as $page) {
            foreach ($page as $queue) {
                $queues[] = $queue;
            }
        }

        return $queues;
    }

    public function createQueue($queueName)
    {
        $queue = new Queue([
            'name' => $this->client::queueName($this->config['project_id'], $this->config['location'], $queueName),
        ]);
        $this->client->createQueue($this->locationName, $queue);
    }

    public function updateQueue($queueName, $options)
    {
        $queue = new Queue;
        $queue->setName($this->client::queueName($this->config['project_id'], $this->config['location'], $queueName));

        $rateLimits = new RateLimits;
        $rateLimits->setMaxBurstSize($options['rate_limits']['max_burst_size']);
        $rateLimits->setMaxConcurrentDispatches($options['rate_limits']['max_concurrent_dispatches']);
        $rateLimits->setMaxDispatchesPerSecond($options['rate_limits']['max_dispatches_per_second'] ?? $options['rate_limits']['max_displatches_per_second']);
        $queue->setRateLimits($rateLimits);

        $retryConfig = new RetryConfig;
        $retryConfig->setMaxAttempts($options['retry_config']['max_attempts']);
        $retryConfig->setMaxRetryDuration((new Duration())->setSeconds($options['retry_config']['max_retry_duration']));
        $retryConfig->setMinBackoff((new Duration())->setSeconds($options['retry_config']['min_backoff']));
        $retryConfig->setMaxBackoff((new Duration())->setSeconds($options['retry_config']['max_backoff']));
        $retryConfig->setMaxDoublings($options['retry_config']['max_doublings']);
        $queue->setRetryConfig($retryConfig);

        $this->client->updateQueue($queue);
    }
}
