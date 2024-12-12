<?php
/*
 * Encore Digital Group - Planning Center PHP SDK
 * Copyright (c) 2024. Encore Digital Group
 */

namespace EncoreDigitalGroup\PlanningCenter\Objects\People\Relationships;

use EncoreDigitalGroup\PlanningCenter\Objects\SdkObjects\Relationships\BasicRelationship;
use EncoreDigitalGroup\PlanningCenter\Objects\SdkObjects\Relationships\BasicRelationshipData;

class HouseholdMembershipRelationships
{
    public BasicRelationship $person;

    public function __construct()
    {
        $this->person = new BasicRelationship(new BasicRelationshipData("Person"));
    }
}