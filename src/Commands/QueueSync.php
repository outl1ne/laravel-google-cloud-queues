<?php

namespace Outl1ne\LaravelGoogleCloudQueues\Commands;

use Illuminate\Console\Command;
use Outl1ne\LaravelGoogleCloudQueues\Queues;

class QueueSync extends Command
{
    protected $signature = 'gcloud-queues:sync';
    protected $description = 'Update queues in Google Cloud.';

    public function handle()
    {
        $this->info('Starting to update queues in Google Cloud.');
        (new Queues)->sync();
        $this->info('Queue sync done.');
    }
}
