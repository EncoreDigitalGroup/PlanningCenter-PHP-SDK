<?php

/*
 * Encore Digital Group - Planning Center PHP SDK
 * Copyright (c) 2023-2024. Encore Digital Group
 */

namespace EncoreDigitalGroup\PlanningCenter\Objects\People;

use EncoreDigitalGroup\PlanningCenter\Objects\People\Attributes\HouseholdAttributes;
use EncoreDigitalGroup\PlanningCenter\Objects\People\Attributes\HouseholdMembershipAttributes;
use EncoreDigitalGroup\PlanningCenter\Objects\People\Relationships\HouseholdMembershipRelationships;
use EncoreDigitalGroup\PlanningCenter\Objects\People\Relationships\HouseholdRelationships;
use EncoreDigitalGroup\PlanningCenter\Objects\People\Traits\HasEmails;
use EncoreDigitalGroup\PlanningCenter\Objects\People\Traits\Households\InteractsWithHouseholds;
use EncoreDigitalGroup\PlanningCenter\Objects\SdkObjects\ClientResponse;
use EncoreDigitalGroup\PlanningCenter\Objects\SdkObjects\Relationships\BasicRelationship;
use EncoreDigitalGroup\PlanningCenter\Objects\SdkObjects\Relationships\BasicRelationshipData;
use EncoreDigitalGroup\PlanningCenter\Support\AttributeMapper;
use EncoreDigitalGroup\PlanningCenter\Support\PlanningCenterApiVersion;
use EncoreDigitalGroup\PlanningCenter\Support\RelationshipMapper;
use EncoreDigitalGroup\PlanningCenter\Traits\HasPlanningCenterClient;
use Exception;
use Illuminate\Support\Arr;
use TypeError;

/** @api */
class HouseholdMembership
{
    use HasPlanningCenterClient;
    use InteractsWithHouseholds;

    public HouseholdMembershipAttributes $attributes;
    public HouseholdMembershipRelationships $relationships;
    private bool $isCreating = false;

    public static function make(?string $clientId = null, ?string $clientSecret = null): HouseholdMembership
    {
        $household = new self($clientId, $clientSecret);
        $household->attributes = new HouseholdMembershipAttributes;
        $household->relationships = new HouseholdMembershipRelationships;
        $household->setApiVersion(PlanningCenterApiVersion::PEOPLE_DEFAULT);

        return $household;
    }

    public function forHouseholdMembershipId(string $householdId): static
    {
        $this->attributes->householdMembershipId = $householdId;

        return $this;
    }

    public function forPersonId(string $personId): static
    {
        if (is_null($this->relationships->person->data)) {
            $this->relationships->person->data = new BasicRelationshipData("Person");
        }

        $this->relationships->person->data->id = $personId;

        return $this;
    }

    public function all(?array $query = null): ClientResponse
    {
        $http = $this->client()
            ->get($this->hostname() . Household::HOUSEHOLDS_ENDPOINT . "/{$this->householdId}/household_memberships", $query);

        return $this->processResponse($http);
    }

    public function create(): ClientResponse
    {
        $this->isCreating = true;

        $http = $this->client()
            ->post($this->hostname() . Household::HOUSEHOLDS_ENDPOINT . "/{$this->householdId}/household_memberships", $this->mapToPco());

        return $this->processResponse($http);
    }

    public function get(?array $query = null): ClientResponse
    {
        $http = $this->client()
            ->get($this->householdIdEndpoint() . "/household_memberships", $query);

        return $this->processResponse($http);
    }


    public function update(): ClientResponse
    {
        $http = $this->client()
            ->patch($this->householdIdEndpoint() . "/household_memberships/{$this->attributes->householdMembershipId}", $this->mapToPco());

        return $this->processResponse($http);
    }

    public function delete(): ClientResponse
    {
        $http = $this->client()
            ->delete($this->householdIdEndpoint());

        return $this->processResponse($http);
    }

    private function householdIdEndpoint(): string
    {
        return $this->hostname() . Household::HOUSEHOLDS_ENDPOINT . "/{$this->householdId}";
    }

    private function mapFromPco(ClientResponse $clientResponse): void
    {
        try {
            $records = objectify($clientResponse->meta->response->json("data", []));
        } catch (Exception|TypeError) {
            return;
        }

        if (!is_iterable($records)) {
            return;
        }

        foreach ($records as $record) {
            $this->attributes->householdMembershipId = $record->id;
            $attributeMap = [
                "personName" => "person_name",
                "pending" => "pending",
            ];

            AttributeMapper::from($record, $this->attributes, $attributeMap);

            $relationshipMap = [
                "person" => "person",
            ];

            RelationshipMapper::from($record, $this->relationships, $relationshipMap);

            $clientResponse->data->add($this);
        }

    }

    private function mapToPco(): array
    {
        $household = [
            "data" => [
                "attributes" => [
                    "personName" => $this->attributes->personName ?? null,
                    "pending" => $this->attributes->pending ?? null,
                ],
                "relationships" => [
                    "person" => [
                        "data" => [
                            "type" => $this->relationships->person->data->type ?? null,
                            "id" => $this->relationships->person->data->id ?? null,
                        ],
                    ],
                ],
            ],
        ];

        return $this->prepareHouseholdPayload($household);
    }
}
