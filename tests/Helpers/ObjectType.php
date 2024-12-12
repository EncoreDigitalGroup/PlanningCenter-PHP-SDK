<?php
/*
 * Encore Digital Group - Planning Center PHP SDK
 * Copyright (c) 2024. Encore Digital Group
 */

namespace Tests\Helpers;

enum ObjectType: string
{
    case Email = "email";
    case Enrollment = "enrollment";
    case Event = "event";
    case EventInstance = "eventInstance";
    case Group = "group";
    case GroupMembers = "people";
    case GroupMembership = "membership";
    case Household = "household";
    case HouseholdMembership = "householdMembership";
    case Profile = "profile";
    case TagGroup = "tagGroup";
    case Tag = "tag";
}
