<?php

declare(strict_types=1);


namespace MeetupOrganizing\Entity;

use Doctrine\DBAL\Connection;

final class MeetupRepository
{
    private Connection $connection;
    
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    public function save(Meetup $meetup): Meetup
    {
        $this->connection->insert('meetups', $meetup->getData());
        $meetupId= (int)$this->connection->lastInsertId();
        $new = clone $meetup;
        $new->setId($meetupId);
        return $new;
    }
}
