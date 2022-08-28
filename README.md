# Concrete CMS add-on: Macareux Job Runner

Automated jobs has been deprecated in Concrete CMS 9.0 
and may be removed in the future versions.

The best way is convert your automated job to "Task", that 
is introduced in version 9. However, manual converting may 
take long time to do it or the author of the add-ons 
you're using now does not release a new version supports 
Concrete 9 yet.

This is a mitigation package to upgrade to version 9 as 
soon as possible. This package installs a Task to run 
automated jobs as it is.

## How to schedule automated jobs

1. Install this package
2. Go to [Dashboard > System & Settings > Automation > Tasks]
3. Select "Run automated job"
4. Select "Schedule Recurring Task" then input Cron Expression
5. Hit the "Set Task Options" button
6. Select a job to run, then hit the "Run Task" button

## How to convert queueable jobs

`ZendQueue` class has been removed since 9.0.0, so you
can't run queueable jobs. You have to fix imports of the 
class to run it without fatal error.

Here's a simple example class of Queueable Job.

```php
<?php
namespace Application\Job;

use Concrete\Core\Job\QueueableJob;
use Concrete\Core\Page\Page;
use ZendQueue\Message as ZendQueueMessage;
use ZendQueue\Queue as ZendQueue;

class ExampleQueueableJob extends QueueableJob
{
    public function getJobName()
    {
        return t('Example Queueable Job');
    }

    public function getJobDescription()
    {
        return t('Job description.');
    }

    public function start(ZendQueue $q)
    {
        $list = new PageList();
        $results = $list->executeGetResults();
        foreach ($results as $result) {
            $q->send($result['cID']);
        }
    }
    
    public function processQueueItem(ZendQueueMessage $msg)
    {
        $page = Page::getByID((int) $msg->body);
        // Do something
    }
    
    public function finish(ZendQueue $q)
    {
        return t('Done!');
    }
}
```

You have to convert `Concrete\Core\Job\QueueableJob` to 
`Macareux\JobRunner\Job\QueueableJob`, `ZendQueue\Message`
to `Macareux\JobRunner\Queue\Message`, `ZendQueue\Queue` 
to `Macareux\JobRunner\Queue\Queue`.

So updated class should be like this:

```php
<?php
namespace Application\Job;

use Macareux\JobRunner\Job\QueueableJob;
use Concrete\Core\Page\Page;
use Macareux\JobRunner\Queue\Message as ZendQueueMessage;
use Macareux\JobRunner\Queue\Queue as ZendQueue;

class ExampleQueueableJob extends QueueableJob
{
    public function getJobName()
    {
        return t('Example Queueable Job');
    }

    public function getJobDescription()
    {
        return t('Job description.');
    }

    public function start(ZendQueue $q)
    {
        $list = new PageList();
        $results = $list->executeGetResults();
        foreach ($results as $result) {
            $q->send($result['cID']);
        }
    }
    
    public function processQueueItem(ZendQueueMessage $msg)
    {
        $page = Page::getByID((int) $msg->body);
        // Do something
    }
    
    public function finish(ZendQueue $q)
    {
        return t('Done!');
    }
}
```

That's it! Now you can run this queueable job with 
"Run automated job" task.

You can fix imports with `md:convert-queueable-job` console 
command.

## License

MIT License