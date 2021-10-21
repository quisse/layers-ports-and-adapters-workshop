<?php

declare(strict_types=1);


namespace MeetupOrganizing\Service;

use Assert\Assert;
use MeetupOrganizing\Entity\Meetup;
use MeetupOrganizing\Entity\MeetupRepository;
use MeetupOrganizing\Entity\ScheduledDate;
use MeetupOrganizing\Entity\UserId;

final class MeetupScheduler
{
    private MeetupRepository $meetupRepository;
    
    public function __construct(MeetupRepository $meetupRepository)
    {
        $this->meetupRepository = $meetupRepository;
    }
    
    public function schedule(int $organizerId, string $name, string $description, string $scheduledFor): int
    {
        $meetup = new Meetup(
            UserId::fromInt($organizerId),
            $name,
            $description,
            ScheduledDate::fromString($scheduledFor)
        );
    
        $meetup = $this->meetupRepository->save($meetup);
        
        Assert::that($meetup->getId())->integer();
        return $meetup->getId();
    }
}
