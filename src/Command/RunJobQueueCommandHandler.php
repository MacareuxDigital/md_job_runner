<?php

namespace Macareux\JobRunner\Command;

use Concrete\Core\Job\Job;
use Macareux\JobRunner\Job\QueueableJob;
use Macareux\JobRunner\Queue\Message;

class RunJobQueueCommandHandler
{
    public function __invoke(RunJobQueueCommand $command)
    {
        $jobHandle = $command->getJobHandle();
        $job = Job::getJobObjByHandle($jobHandle);
        if ($job instanceof QueueableJob) {
            $queueBody = $command->getBody();
            $queueMessage = new Message($queueBody);
            $job->processQueueItem($queueMessage);
        }
    }
}
