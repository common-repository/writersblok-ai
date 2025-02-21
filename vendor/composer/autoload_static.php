<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit074e606de40a8802b23af173cb6a7e76
{
    public static $prefixLengthsPsr4 = array (
        'O' => 
        array (
            'Orhanerday\\OpenAi\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Orhanerday\\OpenAi\\' => 
        array (
            0 => __DIR__ . '/..' . '/orhanerday/open-ai/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit074e606de40a8802b23af173cb6a7e76::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit074e606de40a8802b23af173cb6a7e76::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit074e606de40a8802b23af173cb6a7e76::$classMap;

        }, null, ClassLoader::class);
    }
}
