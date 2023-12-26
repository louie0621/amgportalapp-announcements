<?php

namespace AMGPortal\Announcements\Events;

use Illuminate\Foundation\Events\Dispatchable;
use AMGPortal\Announcements\Announcement;

class EmailNotificationRequested
{
    use Dispatchable;

    /**
     * @var Announcement
     */
    public $announcement;

    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }
}
