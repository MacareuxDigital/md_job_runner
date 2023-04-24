<?php

namespace Macareux\JobRunner\Job;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Job\Job;
use Concrete\Core\Package\PackageService;

class Service implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /** @var PackageService */
    protected $packageService;

    /**
     * @param PackageService $packageService
     */
    public function __construct(PackageService $packageService)
    {
        $this->packageService = $packageService;
    }

    public function getJobByHandle(string $jHandle): ?Job
    {
        $jcl = $this->jobClassLocations();

        foreach ($jcl as $pkgHandle => $jobClassLocation) {
            //load the file & class, then run the job
            $path = $jobClassLocation.'/'.$jHandle.'.php';
            if (file_exists($path)) {
                $className = static::getClassName($jHandle, $pkgHandle);
                if (class_exists($className, true)) {
                    $j = $this->app->make($className);
                    $j->jHandle = $jHandle;

                    return $j;
                }
            }
        }

        return null;
    }

    /**
     * @return array
     */
    protected function jobClassLocations(): array
    {
        $locations = ['' => DIR_FILES_JOBS];
        foreach ($this->packageService->getInstalledHandles() as $pkgHandle) {
            $locations[$pkgHandle] = DIR_PACKAGES . '/' . $pkgHandle . '/' . DIRNAME_JOBS;
        }

        return $locations;
    }

    /**
     * Copied from Core Job class
     *
     * @param $jHandle
     * @param $pkgHandle
     * @return string
     */
    protected static function getClassName($jHandle, $pkgHandle = null): string
    {
        return overrideable_core_class('Job\\' . camelcase($jHandle), DIRNAME_JOBS . '/' . $jHandle . '.php', $pkgHandle);
    }
}