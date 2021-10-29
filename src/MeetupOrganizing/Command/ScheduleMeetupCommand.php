<?php

declare(strict_types=1);

namespace MeetupOrganizing\Command;

use Assert\Assert;
use MeetupOrganizing\ScheduleMeetup;
use MeetupOrganizing\Service\MeetupScheduler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ScheduleMeetupCommand extends Command
{
    
    private MeetupScheduler $meetupScheduler;
    
    public function __construct(MeetupScheduler $meetupScheduler)
    {
        parent::__construct();
        $this->meetupScheduler = $meetupScheduler;
    }
    
    protected function configure(): void
    {
        $this->setName('schedule')
            ->setDescription('Schedule a meetup')
            ->addArgument('organizerId', InputArgument::REQUIRED, 'Organizer ID')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the meetup')
            ->addArgument('description', InputArgument::REQUIRED, 'Description of the meetup')
            ->addArgument('scheduledFor', InputArgument::REQUIRED, 'Scheduled for');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // InputInterface isn't particularly well-typed, so we need to add some checks
        $organizerId = $input->getArgument('organizerId');
        Assert::that($organizerId)
            ->string();
        
        
        $name = $input->getArgument('name');
        Assert::that($name)
            ->string();
        
        $description = $input->getArgument('description');
        Assert::that($description)
            ->string();
        
        $scheduledFor = $input->getArgument('scheduledFor');
        Assert::that($scheduledFor)
            ->string();
        
        $this->meetupScheduler->schedule(
            new ScheduleMeetup((int)$organizerId, $name, $description, $scheduledFor)
        );
        
        $output->writeln('<info>Scheduled the meetup successfully</info>');
        
        return 0;
    }
}
