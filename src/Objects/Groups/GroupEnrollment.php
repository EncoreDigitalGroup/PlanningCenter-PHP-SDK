<?php
/*
 * Encore Digital Group - Planning Center PHP SDK
 * Copyright (c) 2024. Encore Digital Group
 */

namespace EncoreDigitalGroup\PlanningCenter\Objects\Groups;

use EncoreDigitalGroup\PlanningCenter\Configuration\AuthorizationOptions;
use EncoreDigitalGroup\PlanningCenter\Configuration\ClientConfiguration;
use EncoreDigitalGroup\PlanningCenter\Objects\Groups\Attributes\GroupAttributes;
use EncoreDigitalGroup\PlanningCenter\Objects\Groups\Attributes\GroupEnrollmentAttributes;
use EncoreDigitalGroup\PlanningCenter\Objects\SdkObjects\ClientResponse;
use EncoreDigitalGroup\PlanningCenter\PlanningCenterClient;
use EncoreDigitalGroup\PlanningCenter\Traits\HasPlanningCenterClient;
use PHPGenesis\Common\Container\PhpGenesisContainer;
use PHPGenesis\Http\HttpClient;

/**
 * @internal
 * @api
 */
class GroupEnrollment
{
    use HasPlanningCenterClient;

    public GroupEnrollmentAttributes $attributes;
    protected AuthorizationOptions $auth;

    public static function make(string $clientId, string $clientSecret): GroupEnrollment
    {
        $group = new self($clientId, $clientSecret);
        $group->attributes = new GroupEnrollmentAttributes;

        return $group;
    }

    public function get(array $query = []): ClientResponse
    {
        $http = $this->client()
            ->get($this->hostname() . Group::GROUPS_ENDPOINT . $this->attributes->groupId . '/enrollment', $query);

        return $this->processResponse($http);
    }

    protected function mapFromPco(array $pco): void
    {
        $pco = objectify($pco);

        $this->attributes->groupId = $pco->id;
        $this->attributes->autoClosed = $pco->attributes->auto_closed;
        $this->attributes->autoClosedReason = $pco->attributes->auto_closed_reason;
        $this->attributes->dateLimit = $pco->attributes->date_limit;
        $this->attributes->dateLimitReached = $pco->attributes->date_limit_reached;
        $this->attributes->memberLimit = $pco->attributes->member_limit;
        $this->attributes->memberLimitReached = $pco->attributes->member_limit_reached;
        $this->attributes->status = $pco->attributes->status;
        $this->attributes->strategy = $pco->attributes->strategy;
    }
}