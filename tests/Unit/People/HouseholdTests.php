<?php

namespace Tests\Unit\People;

use EncoreDigitalGroup\PlanningCenter\Objects\People\Household;
use EncoreDigitalGroup\PlanningCenter\Objects\People\Person;
use EncoreDigitalGroup\PlanningCenter\Objects\SdkObjects\ClientResponse;
use Illuminate\Support\Collection;
use Tests\Helpers\TestConstants;

describe("People Household Tests", function (): void {
    test("Household: Can Create Household", function (): void {
        $household = Household::make(TestConstants::CLIENT_ID, TestConstants::CLIENT_SECRET);

        $household->addPerson("1");
        $household->setPrimaryContactId("1");
        $household->attributes->name = "Smith Household";

        $response = $household->create();
        /** @var Household $pcoHousehold */
        $pcoHousehold = $response->data->first();

        expect($response)->toBeInstanceOf(ClientResponse::class)
            ->and($pcoHousehold->attributes->householdId)->toBe(HouseholdMocks::HOUSEHOLD_ID)
            ->and($pcoHousehold->attributes->name)->toBe(HouseholdMocks::HOUSEHOLD_NAME);
    });

    test("Households: Can List All", function (): void {
        $household = Household::make(TestConstants::CLIENT_ID, TestConstants::CLIENT_SECRET);

        $response = $household->all();

        expect($response)->toBeInstanceOf(ClientResponse::class)
            ->and($response->data)->toBeInstanceOf(Collection::class)
            ->and($response->data->count())->toBe(1);
    });

    test("Household: Can Get Household By ID", function (): void {
        $household = Household::make(TestConstants::CLIENT_ID, TestConstants::CLIENT_SECRET);

        $response = $household
            ->forHouseholdId("1")
            ->get();

        /** @var Household $pcoHousehold */
        $pcoHousehold = $response->data->first();

        expect($response)->toBeInstanceOf(ClientResponse::class)
            ->and($pcoHousehold->attributes->householdId)->toBe(HouseholdMocks::HOUSEHOLD_ID)
            ->and($pcoHousehold->attributes->name)->toBe(HouseholdMocks::HOUSEHOLD_NAME)
            ->and($pcoHousehold->attributes->primaryContactId)->toBe(HouseholdMocks::HOUSEHOLD_PRIMARY_CONTACT_ID);
    });

    test("Household: Can Update Household", function (): void {
        $household = Household::make(TestConstants::CLIENT_ID, TestConstants::CLIENT_SECRET);

        $response = $household
            ->forHouseholdId("1")
            ->update();

        /** @var Household $pcoHousehold */
        $pcoHousehold = $response->data->first();

        expect($response)->toBeInstanceOf(ClientResponse::class)
            ->and($pcoHousehold->attributes->householdId)->toBe(HouseholdMocks::HOUSEHOLD_ID)
            ->and($pcoHousehold->attributes->name)->toBe(HouseholdMocks::HOUSEHOLD_NAME)
            ->and($pcoHousehold->attributes->primaryContactId)->toBe(HouseholdMocks::HOUSEHOLD_PRIMARY_CONTACT_ID);
    });

    test("Households: Can Delete Household", function (): void {
        $household = Household::make(TestConstants::CLIENT_ID, TestConstants::CLIENT_SECRET);

        $response = $household
            ->forHouseholdId("1")
            ->delete();

        expect($response->data->isEmpty())->toBeTrue();
    });
})->group("people.households");

