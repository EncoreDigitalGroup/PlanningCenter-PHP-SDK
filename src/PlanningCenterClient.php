<?php
/*
 * Encore Digital Group - Planning Center PHP SDK
 * Copyright (c) 2023-2024. Encore Digital Group
 */

namespace EncoreDigitalGroup\PlanningCenter;

use EncoreDigitalGroup\PlanningCenter\Support\PlanningCenterApiVersion;
use PHPGenesis\Common\Container\PhpGenesisContainer;
use PHPGenesis\Http\HttpClientBuilder;

class PlanningCenterClient
{
    protected HttpClientBuilder $builder;
    protected string $baseUrl = "https://api.planningcenteronline.com/";

    public function __construct()
    {
        PhpGenesisContainer::getInstance()->singleton(PlanningCenterApiVersion::class, function (): PlanningCenterApiVersion {
            return new PlanningCenterApiVersion;
        });

        $this->builder = new HttpClientBuilder;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function setBaseUrl(string $baseUrl): static
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }
}
