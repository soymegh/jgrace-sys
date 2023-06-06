<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit90a7033022dbb3b28c22f6b4bb6e9a22
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
        'C' => 
        array (
            'Clockwork\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/src',
        ),
        'Clockwork\\' => 
        array (
            0 => __DIR__ . '/..' . '/itsgoingd/clockwork/Clockwork',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit90a7033022dbb3b28c22f6b4bb6e9a22::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit90a7033022dbb3b28c22f6b4bb6e9a22::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit90a7033022dbb3b28c22f6b4bb6e9a22::$classMap;

        }, null, ClassLoader::class);
    }
}