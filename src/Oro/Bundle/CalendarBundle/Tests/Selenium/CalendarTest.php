<?php

namespace Oro\Bundle\CalendarBundle\Tests\Selenium;

use Oro\Bundle\CalendarBundle\Tests\Selenium\Pages\Calendars;
use Oro\Bundle\TestFrameworkBundle\Test\Selenium2TestCase;

/**
 * Class CalendarTest
 * @package Oro\Bundle\CalendarBundle\Tests\Selenium
 */
class CalendarTest extends Selenium2TestCase
{
    /**
     * Test creation of new calendar event
     * @return string
     */
    public function testAddEvent()
    {
        $eventName = 'Event_'.mt_rand();
        $login = $this->login();
        /* @var Calendars $login */
        $login->openCalendars('Oro\Bundle\CalendarBundle')
            ->assertTitle('My Calendar - John Doe')
            ->addEvent()
            ->setTitle($eventName)
            ->setStartDate('Apr 9, 2014 11:00 PM')
            ->setEndDate('Apr 9, 2015 12:00 PM')
            ->saveEvent()
            ->checkEventPresent($eventName);

        return $eventName;
    }

    /**
     * Test edit of existing event
     * @depends testAddEvent
     * @param string $eventName
     * @return string
     */
    public function testEditEvent($eventName)
    {
        $newEventTitle = 'Update_' . $eventName;
        $login = $this->login();
        /* @var Calendars $login */
        $login->openCalendars('Oro\Bundle\CalendarBundle')
            ->editEvent($eventName)
            ->setTitle($newEventTitle)
            ->setStartDate('Apr 9, 2014 11:30 PM')
            ->setEndDate('Apr 9, 2015 12:30 PM')
            ->saveEvent()
            ->assertTitle('My Calendar - John Doe')
            ->checkEventPresent($newEventTitle);

        return $newEventTitle;
    }

    /**
     * Test deletion of existing event
     * @depends testEditEvent
     * @param string $eventName
     */
    public function testDeleteEvent($eventName)
    {
        $login = $this->login();
        /* @var Calendars $login */
        $login->openCalendars('Oro\Bundle\CalendarBundle')
            ->editEvent($eventName)
            ->deleteEvent()
            ->assertTitle('My Calendar - John Doe')
            ->checkEventNotPresent($eventName);
    }
}
