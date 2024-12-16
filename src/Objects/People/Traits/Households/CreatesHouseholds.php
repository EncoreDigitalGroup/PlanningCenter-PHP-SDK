<?php
/*
 * Encore Digital Group - Planning Center PHP SDK
 * Copyright (c) 2024. Encore Digital Group
 */

namespace EncoreDigitalGroup\PlanningCenter\Objects\People\Traits\Households;

use EncoreDigitalGroup\PlanningCenter\Objects\People\Attributes\HouseholdAttributes;
use EncoreDigitalGroup\PlanningCenter\Objects\People\Household;
use EncoreDigitalGroup\PlanningCenter\Objects\People\Relationships\HouseholdRelationships;
use EncoreDigitalGroup\PlanningCenter\Objects\SdkObjects\Relationships\BasicRelationship;
use EncoreDigitalGroup\PlanningCenter\Objects\SdkObjects\Relationships\BasicRelationshipData;
use EncoreDigitalGroup\PlanningCenter\Support\Exceptions\DataConstraintViolation;
use Illuminate\Support\Collection;

trait CreatesHouseholds
{
    /** @var Collection<BasicRelationship> $people */
    public Collection $people;
    public BasicRelationship $primaryContact;
    private bool $isCreating = false;

    public function addPerson(string $personId): static
    {
        $this->initialize();

        if ($this->personIdIsHouseholdMember($personId)) {
            throw new DataConstraintViolation("Person ID is already a household member");
        }

        $this->people->add(new BasicRelationship(new BasicRelationshipData("Person", $personId)));
        $this->relationships->people = $this->people;

        return $this;
    }

    public function setPrimaryContactId(string $primaryContactId): static
    {
        $this->initialize();

        if ($this->personIdIsHouseholdMember($primaryContactId)) {
            $this->primaryContact->data->id = $primaryContactId;
        } else {
            $exceptionMessage = "Person ID is not a member of this household.";
            $exceptionMessage .= " Member must be added to household before they can be made the primary contact.";
            throw new DataConstraintViolation($exceptionMessage);
        }

        $this->relationships->primaryContact = $this->primaryContact;
        $this->attributes->primaryContactId = $this->primaryContact->data->id;

        return $this;
    }

    private function personIdIsHouseholdMember(string $personId): bool
    {
        if (!isset($this->people)) {
            $this->people = new Collection;
        }

        return $this->people->contains(fn(BasicRelationship $relationship) => !is_null($relationship->data) && $relationship->data->id === $personId);
    }

    private function initialize(): void
    {
        if (!isset($this->relationships)) {
            $this->relationships = new HouseholdRelationships;
            $this->relationships->people = new Collection;
            $this->relationships->primaryContact = new BasicRelationship;
        }

        if (!isset($this->relationships->people)) {
            $this->relationships->people = new Collection;
        }

        if (!isset($this->relationships->primaryContact)) {
            $this->relationships->primaryContact = new BasicRelationship;
        }

        if (!isset($this->people)) {
            $this->people = new Collection;
        }

        if (!isset($this->primaryContact)) {
            $this->primaryContact = new BasicRelationship(new BasicRelationshipData("Person"));
        }

        if (is_null($this->primaryContact->data)) {
            $this->primaryContact->data = new BasicRelationshipData("Person");
        }

        if (!isset($this->attributes)) {
            $this->attributes = new HouseholdAttributes;
        }
    }
}