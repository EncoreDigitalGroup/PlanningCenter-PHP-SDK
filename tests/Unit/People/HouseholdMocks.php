<?php
/*
 * Encore Digital Group - Planning Center PHP SDK
 * Copyright (c) 2024. Encore Digital Group
 */

namespace Tests\Unit\People;

use EncoreDigitalGroup\PlanningCenter\Objects\People\Email;
use EncoreDigitalGroup\PlanningCenter\Objects\People\Household;
use EncoreDigitalGroup\PlanningCenter\Objects\People\Person;
use EncoreDigitalGroup\PlanningCenter\Traits\HasPlanningCenterClient;
use PHPGenesis\Http\HttpClient;
use Tests\Helpers\BaseMock;
use Tests\Helpers\ObjectType;

class HouseholdMocks extends BaseMock
{
    use HasPlanningCenterClient;

    public const string HOUSEHOLD_ID = "1";
    public const string HOUSEHOLD_PRIMARY_CONTACT_NAME = "John Smith";
    public const string HOUSEHOLD_PRIMARY_CONTACT_ID = "1";
    public const int HOUSEHOLD_MEMBER_COUNT = 1;
    public const string HOUSEHOLD_MEMBERSHIP_ID = "1";

    public static function setup(): void
    {
        self::useHouseholdCollection();
        self::useSpecificHousehold();
        self::useHouseholdMembershipCollection();
        self::useSpecificHouseholdMembership();
    }

    public static function useHouseholdCollection(): void
    {
        HttpClient::fake([
            self::HOSTNAME . Household::HOUSEHOLDS_ENDPOINT => function ($request) {
                return match ($request->method()) {
                    "POST" => HttpClient::response(self::useSingleResponse(ObjectType::Household)),
                    "GET", => HttpClient::response(self::useCollectionResponse(ObjectType::Household)),
                    default => HttpClient::response([], 405),
                };
            },
        ]);
    }

    public static function useSpecificHousehold(): void
    {
        HttpClient::fake([
            self::HOSTNAME . Household::HOUSEHOLDS_ENDPOINT . "/1" => function ($request) {
                return match ($request->method()) {
                    "PUT", "PATCH", "GET", => HttpClient::response(self::useSingleResponse(ObjectType::Household)),
                    "DELETE" => HttpClient::response(self::deleteResponse()),
                    default => HttpClient::response([], 405),
                };
            },
        ]);
    }

    public static function useHouseholdMembershipCollection(): void
    {
        HttpClient::fake([
            self::HOSTNAME . Household::HOUSEHOLDS_ENDPOINT . "/1/household_memberships" => function ($request) {
                return match ($request->method()) {
                    "POST" => HttpClient::response(self::useSingleResponse(ObjectType::HouseholdMembership)),
                    "GET", => HttpClient::response(self::useCollectionResponse(ObjectType::HouseholdMembership)),
                    default => HttpClient::response([], 405),
                };
            },
        ]);
    }

    public static function useSpecificHouseholdMembership(): void
    {
        HttpClient::fake([
            self::HOSTNAME . Household::HOUSEHOLDS_ENDPOINT . "/1/household_memberships/1" => function ($request) {
                return match ($request->method()) {
                    "PUT", "PATCH", "GET", => HttpClient::response(self::useSingleResponse(ObjectType::HouseholdMembership)),
                    "DELETE" => HttpClient::response(self::deleteResponse()),
                    default => HttpClient::response([], 405),
                };
            },
        ]);
    }

    protected static function household(): array
    {
        return [
            "type" => "Household",
            "id" => self::HOUSEHOLD_ID,
            "attributes" => [
                "name" => "string",
                "member_count" => self::HOUSEHOLD_MEMBER_COUNT,
                "primary_contact_name" => self::HOUSEHOLD_PRIMARY_CONTACT_NAME,
                "created_at" => "2000-01-01T12:00:00Z",
                "updated_at" => "2000-01-01T12:00:00Z",
                "avatar" => "string",
                "primary_contact_id" => self::HOUSEHOLD_PRIMARY_CONTACT_ID,
            ],
            "relationships" => [
                "primary_contact" => [
                    "data" => [
                        "type" => "Person",
                        "id" => self::HOUSEHOLD_PRIMARY_CONTACT_ID,
                    ],
                ],
                "people" => [
                    "data" => [
                        [
                            "type" => "Person",
                            "id" => self::HOUSEHOLD_PRIMARY_CONTACT_ID,
                        ],
                    ],
                ],
            ],
        ];
    }

    protected static function householdMembership(): array
    {
        return [
            "type" => "HouseholdMembership",
            "id" => self::HOUSEHOLD_MEMBERSHIP_ID,
            "attributes" => [
                "person_name" => self::HOUSEHOLD_PRIMARY_CONTACT_NAME,
                "pending" => true,
            ],
            "relationships" => [
                "person" => [
                    "data" => [
                        "type" => "Person",
                        "id" => self::HOUSEHOLD_PRIMARY_CONTACT_ID,
                    ],
                ],
            ],
        ];
    }
}