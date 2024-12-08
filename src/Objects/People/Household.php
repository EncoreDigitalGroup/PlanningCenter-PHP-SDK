<?php

/*
 * Encore Digital Group - Planning Center PHP SDK
 * Copyright (c) 2023-2024. Encore Digital Group
 */

namespace EncoreDigitalGroup\PlanningCenter\Objects\People;

use EncoreDigitalGroup\PlanningCenter\Objects\People\Attributes\HouseholdAttributes;
use EncoreDigitalGroup\PlanningCenter\Objects\People\Relationships\HouseholdRelationships;
use EncoreDigitalGroup\PlanningCenter\Objects\People\Traits\HasEmails;
use EncoreDigitalGroup\PlanningCenter\Objects\SdkObjects\ClientResponse;
use EncoreDigitalGroup\PlanningCenter\Support\AttributeMapper;
use EncoreDigitalGroup\PlanningCenter\Support\PlanningCenterApiVersion;
use EncoreDigitalGroup\PlanningCenter\Traits\HasPlanningCenterClient;
use Exception;
use Illuminate\Support\Arr;
use TypeError;

/** @api */
class Household
{
    use HasEmails, HasPlanningCenterClient;

    public const string HOUSEHOLDS_ENDPOINT = "/people/v2/households";

    public HouseholdAttributes $attributes;
    public HouseholdRelationships $relationships;

    public static function make(?string $clientId = null, ?string $clientSecret = null): Household
    {
        $household = new self($clientId, $clientSecret);
        $household->attributes = new HouseholdAttributes;
        $household->relationships = new HouseholdRelationships;
        $household->setApiVersion(PlanningCenterApiVersion::PEOPLE_DEFAULT);

        return $household;
    }

    public function forHouseholdId(string $householdId): static
    {
        $this->attributes->householdId = $householdId;

        return $this;
    }

    public function all(?array $query = null): ClientResponse
    {
        $http = $this->client()
            ->get($this->hostname() . self::HOUSEHOLDS_ENDPOINT, $query);

        return $this->processResponse($http);
    }

    public function create(): ClientResponse
    {
        $http = $this->client()
            ->post($this->hostname() . self::HOUSEHOLDS_ENDPOINT, $this->mapToPco());

        return $this->processResponse($http);
    }

    public function get(?array $query = null): ClientResponse
    {
        $http = $this->client()
            ->get($this->householdIdEndpoint(), $query);

        return $this->processResponse($http);
    }


    public function update(): ClientResponse
    {
        $http = $this->client()
            ->patch($this->householdIdEndpoint(), $this->mapToPco());

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
        return $this->hostname() . self::HOUSEHOLDS_ENDPOINT . "/{$this->attributes->householdId}";
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
            $this->attributes->householdId = $record->id;
            $attributeMap = [
                "avatar" => "avatar",
                "createdAt" => "created_at",
                "memberCount" => "member_count",
                "name" => "name",
                "primaryContactId" => "primary_contact_id",
                "primaryContactName" => "primary_contact_name",
                "updatedAt" => "updated_at",
            ];

            AttributeMapper::from($record, $this->attributes, $attributeMap, ["created_at", "updated_at",]);
            $clientResponse->data->add($this);
        }

    }

    private function mapToPco(): array
    {
        $household = [
            "data" => [
                "attributes" => [
                    "name" => $this->attributes->name ?? null,
                    "member_count" => $this->attributes->memberCount ?? null,
                    "avatar" => $this->attributes->avatar ?? null,
                    "primary_contact_id" => $this->attributes->primaryContactId ?? null,
                ],
                "relationships" => [
                    "people" => $this->relationships->people()->toArray() ?? null,
                    "primaryContact" => [
                        "data" => [
                            "type" => $this->relationships->primaryContact()->data->type,
                            "id" => $this->relationships->primaryContact()->data->id,
                        ],
                    ],
                ],
            ],
        ];

        $household["data"]["attributes"] = Arr::whereNotNull($household["data"]["attributes"]);
        $household["data"]["relationships"] = Arr::whereNotNull($household["data"]["relationships"]);

        if (is_null($household["data"]["relationships"]["primaryContact"]["data"]["id"])) {
            unset($household["data"]["relationships"]["primaryContact"]);
        }

        return $household;
    }
}
