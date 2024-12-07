<?php

/*
 * Encore Digital Group - Planning Center PHP SDK
 * Copyright (c) 2023-2024. Encore Digital Group
 */

namespace EncoreDigitalGroup\PlanningCenter\Objects\Calendar;

use EncoreDigitalGroup\PlanningCenter\Objects\Calendar\Attributes\EventAttributes;
use EncoreDigitalGroup\PlanningCenter\Objects\Calendar\Relationships\EventInstanceRelationships;
use EncoreDigitalGroup\PlanningCenter\Objects\Calendar\Relationships\EventRelationships;
use EncoreDigitalGroup\PlanningCenter\Objects\SdkObjects\ClientResponse;
use EncoreDigitalGroup\PlanningCenter\Objects\SdkObjects\Relationships\BasicRelationship;
use EncoreDigitalGroup\PlanningCenter\Objects\SdkObjects\Relationships\BasicRelationshipData;
use EncoreDigitalGroup\PlanningCenter\Support\AttributeMapper;
use EncoreDigitalGroup\PlanningCenter\Support\PlanningCenterApiVersion;
use EncoreDigitalGroup\PlanningCenter\Traits\HasPlanningCenterClient;

/** @api */
class Event
{
    use HasPlanningCenterClient;

    public const string EVENT_ENDPOINT = "/calendar/v2/events";

    public EventAttributes $attributes;
    public EventRelationships $relationships;

    private bool $withTags = false;

    public static function make(string $clientId, string $clientSecret): Event
    {
        $event = new self($clientId, $clientSecret);
        $event->attributes = new EventAttributes;
        $event->relationships = new EventRelationships;
        $event->setApiVersion(PlanningCenterApiVersion::CALENDAR_DEFAULT);

        return $event;
    }

    public function forEventId(string $eventId): static
    {
        $this->attributes->eventId = $eventId;

        return $this;
    }

    public function withTags(bool $withTags = true): static
    {
        $this->withTags = $withTags;

        return $this;
    }

    public function all(array $query = []): ClientResponse
    {
        $http = $this->client()
            ->get($this->hostname() . self::EVENT_ENDPOINT, $this->buildQuery($query));

        return $this->processResponse($http);
    }

    public function future(array $query = []): ClientResponse
    {
        $buildQuery = $this->buildQuery($query);
        $mergeQuery = array_merge(["filter" => "future"], $buildQuery);

        $http = $this->client()
            ->get($this->hostname() . self::EVENT_ENDPOINT, $mergeQuery);

        return $this->processResponse($http);
    }

    public function get(array $query = []): ClientResponse
    {
        $http = $this->client()
            ->get($this->hostname() . self::EVENT_ENDPOINT . "/" . $this->attributes->eventId, $this->buildQuery($query));

        return $this->processResponse($http);
    }

    public function instances(array $query = []): ClientResponse
    {
        $eventInstance = EventInstance::make($this->clientId, $this->clientSecret);

        $eventInstance->relationships = $eventInstance->relationships ?? new EventInstanceRelationships;
        $eventInstance->relationships->event = $eventInstance->relationships->event ?? new BasicRelationship;
        $eventInstance->relationships->event->data = $eventInstance->relationships->event->data ?? new BasicRelationshipData;

        $eventInstance->relationships->event->data->id = $this->attributes->eventId;

        return $eventInstance->all($query);
    }

    public function tags(array $query = []): ClientResponse
    {
        $http = $this->client()
            ->get($this->hostname() . self::EVENT_ENDPOINT . "/{$this->attributes->eventId}/tags", $query);

        $tagRecord = Tag::make($this->clientId, $this->clientSecret);
        $clientResponse = new ClientResponse($http);
        $tagRecord->mapFromPco($clientResponse);

        return $clientResponse;
    }

    private function mapFromPco(ClientResponse $clientResponse): void
    {
        $records = objectify($clientResponse->meta->response->json("data", []));

        if (!is_iterable($records)) {
            return;
        }

        foreach ($records as $record) {
            $this->attributes->eventId = $record->id;
            $attributeMap = [
                "approvalStatus" => "approval_status",
                "createdAt" => "created_at",
                "description" => "description",
                "featured" => "featured",
                "imageUrl" => "image_url",
                "name" => "name",
                "percentApproved" => "percent_approved",
                "percentRejected" => "percent_rejected",
                "registrationUrl" => "registration_url",
                "summary" => "summary",
                "updatedAt" => "updated_at",
                "visibleInChurchCenter" => "visible_in_church_center",
            ];

            AttributeMapper::from($record, $this->attributes, $attributeMap, ["created_at", "updated_at"]);
            $clientResponse->data->add($this);
        }
    }

    private function buildQuery(array $query): array
    {
        if ($this->withTags) {
            $withTagsQuery = [
                "include" => "tags",
            ];

            return array_merge($withTagsQuery, $query);
        }

        return $query;
    }
}
