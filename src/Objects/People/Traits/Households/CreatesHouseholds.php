<?php
/*
 * Encore Digital Group - Planning Center PHP SDK
 * Copyright (c) 2024. Encore Digital Group
 */

namespace EncoreDigitalGroup\PlanningCenter\Objects\People\Traits\Households;

use EncoreDigitalGroup\PlanningCenter\Objects\SdkObjects\Relationships\BasicRelationship;
use EncoreDigitalGroup\PlanningCenter\Objects\SdkObjects\Relationships\BasicRelationshipData;
use EncoreDigitalGroup\PlanningCenter\Support\Exceptions\DataConstraintViolation;
use Illuminate\Support\Collection;

trait CreatesHouseholds
{
    /** @var Collection<BasicRelationship> $people */
    protected Collection $people;
    protected BasicRelationship $primaryContact;
    private bool $isCreating = false;

    public function addPerson(string $personId): static
    {
        if ($this->personIdIsHouseholdMember($personId)) {
            throw new DataConstraintViolation("Person ID is already a household member");
        }

        $this->people->add(new BasicRelationship(new BasicRelationshipData("Person", $personId)));

        return $this;
    }

    public function setPrimaryContactId(string $primaryContactId): static
    {
        if (is_null($this->primaryContact->data)) {
            $this->primaryContact->data = new BasicRelationshipData("Person");
        }

        if ($this->personIdIsHouseholdMember($primaryContactId)) {
            $this->primaryContact->data->id = $primaryContactId;
        } else {
            $exceptionMessage = "Person ID is not a member of this household.";
            $exceptionMessage .= " Member must be added to household before they can be made the primary contact.";
            throw new DataConstraintViolation($exceptionMessage);
        }

        return $this;
    }

    private function personIdIsHouseholdMember(string $personId): bool
    {
        return $this->people->contains(fn(BasicRelationship $relationship) => !is_null($relationship->data) && $relationship->data->id === $personId);
    }
}