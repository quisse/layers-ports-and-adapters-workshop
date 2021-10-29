<?php

declare(strict_types=1);


namespace MeetupOrganizing\Domain;

use Assert\Assert;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use MeetupOrganizing\Domain\ClockInterface;
use PDO;

final class MeetupRepository implements ListMeetupsRepositoryInterface
{
    private Connection $connection;
    private ClockInterface $clock;
    
    public function __construct(Connection $connection, ClockInterface $clock)
    {
        $this->connection = $connection;
        $this->clock = $clock;
    }
    
    public function save(Meetup $meetup): Meetup
    {
        $this->connection->insert('meetups', $meetup->getData());
        $meetupId= (int)$this->connection->lastInsertId();
        $new = clone $meetup;
        $new->setId($meetupId);
        return $new;
    }
    
    public function listUpcomingMeetups(): array
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('meetups')
            ->where('scheduledFor >= :now')
            ->setParameter(
                'now',
                $this->clock->currentTime()
                    ->format(ScheduledDate::DATE_TIME_FORMAT)
            )
            ->andWhere('wasCancelled = :wasNotCancelled')
            ->setParameter('wasNotCancelled', 0)
            ->execute();
        Assert::that($statement)
            ->isInstanceOf(Statement::class);
        
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        $meetups = [];
        
        foreach ($data as $meetup) {
            $meetups [] = MeetupForList::fromDatabaseRecord($meetup);
        }
        
        return $meetups;
    }
    
    public function listPastMeetups(): array
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('meetups')
            ->where('scheduledFor < :now')
            ->setParameter(
                'now',
                $this->clock->currentTime()
                    ->format(ScheduledDate::DATE_TIME_FORMAT)
            )
            ->andWhere('wasCancelled = :wasNotCancelled')
            ->setParameter('wasNotCancelled', 0)
            ->execute();
        Assert::that($statement)
            ->isInstanceOf(Statement::class);
        
        $pastMeetups = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $pastMeetups;
    }
}
