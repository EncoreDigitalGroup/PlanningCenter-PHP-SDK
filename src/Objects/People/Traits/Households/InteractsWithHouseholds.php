<?php
/*
 * Encore Digital Group - Planning Center PHP SDK
 * Copyright (c) 2024. Encore Digital Group
 */

namespace EncoreDigitalGroup\PlanningCenter\Objects\People\Traits\Households;

use Illuminate\Support\Arr;

trait InteractsWithHouseholds
{
    protected ?string $householdId = null;
    private bool $isCreating = false;

    public function forHouseholdId(string $householdId): static
    {
        $this->householdId = $householdId;
        $this->attributes->householdId = $this->householdId;

        return $this;
    }

    private function prepareHouseholdPayload(array $household): array
    {
        $household["data"]["attributes"] = Arr::whereNotNull($household["data"]["attributes"]);
        $household["data"]["relationships"] = Arr::whereNotNull($household["data"]["relationships"]);

        if (is_null($household["data"]["relationships"]["primaryContact"]["data"]["id"])) {
            unset($household["data"]["relationships"]["primaryContact"]);
        }

        if (!$this->isCreating) {
            unset($household["data"]["relationships"]);
        }

        return $household;
    }
}