<?php

namespace src\classes\exception;

class InvalidPropertyValueException extends \Exception
{
    public function __construct($property, $value)
    {
        parent::__construct("Invalid value for property " . $property . ": " . $value);
    }
}