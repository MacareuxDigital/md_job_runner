<?php

namespace Macareux\JobRunner\Command;

use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\Job\Job;
use Macareux\JobRunner\Job\QueueableJob;
use Macareux\JobRunner\Queue\Message;

class RunJobQueueCommandHandler implements OutputAwareInterface
{
    use OutputAwareTrait;

    public function __invoke(RunJobQueueCommand $command)
    {
        $jobHandle = $command->getJobHandle();
        $job = Job::getJobObjByHandle($jobHandle);
        if ($job instanceof QueueableJob) {
            $this->output->write('Processing job queue: ' . $jobHandle);
            $queueBody = $command->getBody();
            $queueMessage = new Message($queueBody);
            $job->processQueueItem($queueMessage);
        }
    }
}
