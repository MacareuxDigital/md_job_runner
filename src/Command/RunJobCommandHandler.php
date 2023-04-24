<?php

namespace Macareux\JobRunner\Command;

use Concrete\Core\Support\Facade\Application;
use Macareux\JobRunner\Job\Service;

class RunJobCommandHandler
{
    public function __invoke(RunJobCommand $command)
    {
        $app = Application::getFacadeApplication();
        /** @var Service $service */
        $service = $app->make(Service::class);
        $job = $service->getJobByHandle($command->getJobHandle());
        if ($job) {
            $job->run();
        }
    }
}
