<?php

namespace src\classes\exception;

class InvalidPropertyNameException extends \Exception
{
    public function __construct($property)
    {
        parent::__construct("Invalid property name: " . $property);
    }
}

