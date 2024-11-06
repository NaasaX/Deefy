<?php

namespace src\classes\exception;



class AuthException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct("Auth error : " . $message);
    }


}


