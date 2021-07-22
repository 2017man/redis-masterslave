<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1bdc6ad65adea48ace407fc3bc9edde3
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Man\\RedisMasterslave\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Man\\RedisMasterslave\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1bdc6ad65adea48ace407fc3bc9edde3::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1bdc6ad65adea48ace407fc3bc9edde3::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1bdc6ad65adea48ace407fc3bc9edde3::$classMap;

        }, null, ClassLoader::class);
    }
}