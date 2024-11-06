<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf5b5a956bd275aaafeb33690757440ef
{
    public static $prefixLengthsPsr4 = array (
        's' => 
        array (
            'src\\classes\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'src\\classes\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/classes',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf5b5a956bd275aaafeb33690757440ef::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf5b5a956bd275aaafeb33690757440ef::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf5b5a956bd275aaafeb33690757440ef::$classMap;

        }, null, ClassLoader::class);
    }
}
