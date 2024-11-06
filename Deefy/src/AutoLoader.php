<?php

namespace src;

class AutoLoader
{
    public function __construct()
    {
        spl_autoload_register([$this, 'autoload']);
    }

    public function autoload($class): void
    {
        $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

        $classPath =  $class . '.php';

        if (is_file($classPath)) {
            require_once($classPath);
        } else {
            throw new \Exception("Classe non trouvée : " . $classPath);
        }
    }

}

new AutoLoader();
