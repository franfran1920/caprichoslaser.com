<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2624da5bc924f79f49e70d47a357d9e3
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'DgoraWcas\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'DgoraWcas\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit2624da5bc924f79f49e70d47a357d9e3::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2624da5bc924f79f49e70d47a357d9e3::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit2624da5bc924f79f49e70d47a357d9e3::$classMap;

        }, null, ClassLoader::class);
    }
}
