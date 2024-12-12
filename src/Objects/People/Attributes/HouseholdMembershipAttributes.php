<?php

/*
 * Encore Digital Group - Planning Center PHP SDK
 * Copyright (c) 2024. Encore Digital Group
 */

namespace EncoreDigitalGroup\PlanningCenter\Objects\People\Attributes;

class HouseholdMembershipAttributes
{
    public string $householdId;
    public string $householdMembershipId;
    public ?string $personName = null;
    public ?bool $pending = null;

}
