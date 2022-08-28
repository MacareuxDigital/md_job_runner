<?php

namespace Macareux\JobRunner\Job;

use Concrete\Core\Job\Job;
use Macareux\JobRunner\Queue\Message as ZendQueueMessage;
use Macareux\JobRunner\Queue\Queue as ZendQueue;

/**
 * Please extend this class instead of core QueueableJob class.
 */
abstract class QueueableJob extends Job
{
    abstract public function start(ZendQueue $q);

    abstract public function finish(ZendQueue $q);

    abstract public function processQueueItem(ZendQueueMessage $msg);

    public function run()
    {
    }
}
