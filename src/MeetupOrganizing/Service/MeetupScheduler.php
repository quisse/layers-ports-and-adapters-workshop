<?php

declare(strict_types=1);


namespace MeetupOrganizing\Service;

use Assert\Assert;
use MeetupOrganizing\Entity\Meetup;
use MeetupOrganizing\Entity\MeetupRepository;
use MeetupOrganizing\Entity\ScheduledDate;
use MeetupOrganizing\Entity\UserId;
use MeetupOrganizing\Entity\UserRepository;
use MeetupOrganizing\ScheduleMeetup;

final class MeetupScheduler
{
    private MeetupRepository $meetupRepository;
    private UserRepository $userRepository;
    
    public function __construct(MeetupRepository $meetupRepository, UserRepository $userRepository)
    {
        $this->meetupRepository = $meetupRepository;
        $this->userRepository = $userRepository;
    }
    
    public function schedule(ScheduleMeetup $command): int
    {
        $user = $this->userRepository->getById($command->organizerId());
        $meetup = new Meetup(
            $user->userId(),
            $command->name(),
            $command->description(),
            $command->scheduledFor()
        );
    
        $meetup = $this->meetupRepository->save($meetup);
        
        Assert::that($meetup->getId())->integer();
        return $meetup->getId();
    }
}
