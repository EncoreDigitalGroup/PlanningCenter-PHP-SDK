<?php
/*
 * Encore Digital Group - Planning Center PHP SDK
 * Copyright (c) 2024. Encore Digital Group
 */

namespace EncoreDigitalGroup\PlanningCenter\Objects\People\Traits\Households;

use EncoreDigitalGroup\PlanningCenter\Objects\People\Household;
use EncoreDigitalGroup\PlanningCenter\Objects\SdkObjects\ClientResponse;
use EncoreDigitalGroup\PlanningCenter\Support\AttributeMapper;
use Illuminate\Support\Arr;
use stdClass;

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

        $primaryContactId = Arr::get($household, "data.relationships.primaryContact.data.id");

        if (is_null($primaryContactId)) {
            unset($household["data"]["relationships"]["primaryContact"]);
        }

        if (!$this->isCreating) {
            unset($household["data"]["relationships"]);
        }

        return $household;
    }

    private function prepareIncomingHouseholdPayload(ClientResponse &$clientResponse, stdClass $payload)
    {
        $household = Household::make($this->clientId, $this->clientSecret);
        $household->forHouseholdId($payload->id);
        $attributeMap = [
            "avatar" => "avatar",
            "createdAt" => "created_at",
            "memberCount" => "member_count",
            "name" => "name",
            "primaryContactId" => "primary_contact_id",
            "primaryContactName" => "primary_contact_name",
            "updatedAt" => "updated_at",
        ];

        AttributeMapper::from($payload, $household->attributes, $attributeMap, ["created_at", "updated_at",]);

        foreach ($payload->relationships->people->data as $person) {
            $household->relationships->addPerson($person->id);
        }

        $household->relationships->setPrimaryContactId($payload->relationships->primary_contact->data->id);

        $clientResponse->data->add($household);
    }

    private function updateMemberCount(): void
    {
        $this->attributes->memberCount = $this->relationships->people->count();
    }
}