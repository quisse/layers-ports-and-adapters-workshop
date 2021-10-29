<?php

declare(strict_types=1);


namespace MeetupOrganizing\Domain;

use Assert\Assertion;

final class Meetup
{
    private ?int $id;
    private UserId $organizerId;
    private string $name;
    private string $description;
    private ScheduledDate $scheduledFor;
    private bool $wasCancelled = false;

    public function __construct(
        UserId $organizerId,
        string $name,
        string $description,
        ScheduledDate $scheduledFor
    ) {
        Assertion::notEmpty($name);
        Assertion::notEmpty($description);
        Assertion::true($scheduledFor->isInTheFuture(new \DateTimeImmutable()));
        
        $this->organizerId = $organizerId;
        $this->name = $name;
        $this->description = $description;
        $this->scheduledFor = $scheduledFor;
    }
    
    public function getData(): array
    {
        return [
            'organizerId' => $this->organizerId->asInt(),
            'name' => $this->name,
            'description' => $this->description,
            'scheduledFor' => $this->scheduledFor->asString(),
            'wasCancelled' => (int)$this->wasCancelled
        ];
    }
    
    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    
    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }
}
