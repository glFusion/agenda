<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInited9808e69879d698c4c706bdf03d6adb
{
    public static $prefixLengthsPsr4 = array (
        'R' => 
        array (
            'RRule\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'RRule\\' => 
        array (
            0 => __DIR__ . '/..' . '/rlanvin/php-rrule/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInited9808e69879d698c4c706bdf03d6adb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInited9808e69879d698c4c706bdf03d6adb::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}