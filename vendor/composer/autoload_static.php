<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf28cd2e63036ad5fa931f12cf5971014
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WPackio\\' => 8,
        ),
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
            'ChurchPlugins\\' => 14,
            'CPNB\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WPackio\\' => 
        array (
            0 => __DIR__ . '/..' . '/wpackio/enqueue/inc',
        ),
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
        'ChurchPlugins\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes/ChurchPlugins',
        ),
        'CPNB\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf28cd2e63036ad5fa931f12cf5971014::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf28cd2e63036ad5fa931f12cf5971014::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf28cd2e63036ad5fa931f12cf5971014::$classMap;

        }, null, ClassLoader::class);
    }
}
