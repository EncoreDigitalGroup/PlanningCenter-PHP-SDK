<?php
/*
 * Copyright (c) 2024. Encore Digital Group.
 * All Right Reserved.
 */

namespace Tests;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Facades\Facade;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Tests\Unit\Calendar\CalendarMocks;
use Tests\Unit\Groups\GroupMocks;
use Tests\Unit\People\HouseholdMocks;
use Tests\Unit\People\PeopleMocks;

class TestCase extends OrchestraTestCase
{
    protected const string HOSTNAME = "https://api.planningcenteronline.com";

    protected function setUp(): void
    {
        $this->enablesPackageDiscoveries = true;
        parent::setUp();

        PeopleMocks::setup();
        CalendarMocks::setup();
        GroupMocks::setup();
        HouseholdMocks::setup();
    }

    protected function getEnvironmentSetup($app): void
    {
        $app->singleton("http", function (): \Illuminate\Http\Client\Factory {
            return new HttpFactory();
        });

        Facade::setFacadeApplication($app);
    }
}
