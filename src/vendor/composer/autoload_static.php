<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit180b5f6bc9c3f63756be585302a0a4ae
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Sms77\\Joomla\\' => 13,
        ),
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Sms77\\Joomla\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit180b5f6bc9c3f63756be585302a0a4ae::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit180b5f6bc9c3f63756be585302a0a4ae::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
