<?php
/*
 * Encore Digital Group - Planning Center PHP SDK
 * Copyright (c) 2024. Encore Digital Group
 */

namespace EncoreDigitalGroup\PlanningCenter\Objects\People\Relationships;

use EncoreDigitalGroup\PlanningCenter\Objects\People\Traits\Households\CreatesHouseholds;
use EncoreDigitalGroup\PlanningCenter\Objects\SdkObjects\Relationships\BasicRelationship;
use EncoreDigitalGroup\PlanningCenter\Objects\SdkObjects\Relationships\BasicRelationshipData;
use Illuminate\Support\Collection;

class HouseholdRelationships
{
    use CreatesHouseholds;

    public function __construct()
    {
        $this->primaryContact = new BasicRelationship(new BasicRelationshipData("Person"));
        $this->people = new Collection;
    }

    /** @returns Collection<BasicRelationship> */
    public function people(): Collection
    {
        if (!isset($this->people)) {
            $this->people = new Collection;
        }

        return $this->people;
    }

    public function primaryContact(): BasicRelationship
    {
        return $this->primaryContact;
    }
}