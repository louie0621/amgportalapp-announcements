<?php

namespace AMGPortal\Announcements\Tests\Unit;

use Tests\TestCase;
use AMGPortal\Announcements\Announcement;

class AnnouncementTest extends TestCase
{
    /** @test */
    public function testParsedBody()
    {
        $announcement = new Announcement([
            'title' => 'foo',
            'body' => '# test'
        ]);

        $this->assertEquals("<h1>test</h1>\n", (string) $announcement->parsed_body);
    }
}
