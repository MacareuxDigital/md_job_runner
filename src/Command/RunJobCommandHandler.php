<?php

namespace Macareux\JobRunner\Command;

use Concrete\Core\Job\Job;

class RunJobCommandHandler
{
    public function __invoke(RunJobCommand $command)
    {
        /** @var Job $job */
        $job = Job::getJobObjByHandle($command->getJobHandle());
        if ($job) {
            $job->run();
        }
    }
}
