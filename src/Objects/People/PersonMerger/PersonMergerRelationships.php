<?php

/*
 * Encore Digital Group - Planning Center PHP SDK
 * Copyright (c) 2024. Encore Digital Group
 */

namespace EncoreDigitalGroup\PlanningCenter\Objects\People\PersonMerger;

use EncoreDigitalGroup\PlanningCenter\Objects\SdkObjects\Relationships\BasicRelationshipData;

class PersonMergerRelationships
{
    public BasicRelationshipData $personToKeep;
    public BasicRelationshipData $personToRemove;
}
