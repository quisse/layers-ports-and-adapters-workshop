<?php

declare(strict_types=1);


namespace MeetupOrganizing\Entity;

interface ListMeetupsRepositoryInterface
{
    public function listUpcomingMeetups():array;
    public function listPastMeetups():array;
}
