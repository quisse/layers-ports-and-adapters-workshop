<?php

declare(strict_types=1);


namespace MeetupOrganizing\Application;

use Assert\Assert;
use MeetupOrganizing\Domain\Meetup;
use MeetupOrganizing\Domain\MeetupRepository;
use MeetupOrganizing\Domain\UserRepository;
use MeetupOrganizing\Application\ScheduleMeetup;

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
