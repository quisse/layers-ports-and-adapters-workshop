<?php
declare(strict_types=1);

namespace MeetupOrganizing\Domain;

interface MeetupRepository
{
    /**
     * Watch out: saving for now means inserting!
     */
    public function save(Meetup $meetup): void;

    public function nextIdentity(): MeetupId;

    public function getById(MeetupId $fromString): Meetup;

    public function update(Meetup $meetup): void;
}
