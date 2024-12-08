<?php
/*
 * Encore Digital Group - Planning Center PHP SDK
 * Copyright (c) 2024. Encore Digital Group
 */

namespace EncoreDigitalGroup\PlanningCenter\Support\Exceptions;


use Exception;
use Throwable;

class DataConstraintViolation extends Exception implements Throwable
{
    public function __construct(string $message = "DataConstraintViolation")
    {
        parent::__construct($message);
    }
}