<?php

/*
 * Encore Digital Group - Planning Center PHP SDK
 * Copyright (c) 2024. Encore Digital Group
 */

namespace EncoreDigitalGroup\PlanningCenter\Objects\People\Attributes;

use Illuminate\Support\Carbon;

class HouseholdAttributes
{
    public string $householdId;
    public ?string $name = null;
    public ?int $memberCount = 0;
    public ?string $primaryContactId = null;
    public ?string $primaryContactName = null;
    public ?Carbon $createdAt = null;
    public ?Carbon $updatedAt = null;
    public ?string $avatar = null;
}
