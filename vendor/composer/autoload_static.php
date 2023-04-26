<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3e823388e18a3eda3d99e0e831b747d5
{
    public static $files = array (
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        '8372d8a4086b1ef22e65ef9236116971' => __DIR__ . '/..' . '/askupa-software/amarkal-admin-notification/composer.php',
        '790c4fc49287d71b55c311293201880d' => __DIR__ . '/../..' . '/src/function/http_build_url.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Component\\Translation\\' => 30,
        ),
        'H' => 
        array (
            'HynoTech\\' => 9,
        ),
        'C' => 
        array (
            'Curl\\' => 5,
        ),
        'A' => 
        array (
            'Amarkal\\Admin\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Component\\Translation\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/translation',
        ),
        'HynoTech\\' => 
        array (
            0 => __DIR__ . '/../..' . '/HynoTech',
        ),
        'Curl\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-curl-class/php-curl-class/src/Curl',
        ),
        'Amarkal\\Admin\\' => 
        array (
            0 => __DIR__ . '/..' . '/askupa-software/amarkal-admin-notification',
        ),
    );

    public static $prefixesPsr0 = array (
        'C' => 
        array (
            'Carbon' => 
            array (
                0 => __DIR__ . '/..' . '/nesbot/carbon/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3e823388e18a3eda3d99e0e831b747d5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3e823388e18a3eda3d99e0e831b747d5::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit3e823388e18a3eda3d99e0e831b747d5::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit3e823388e18a3eda3d99e0e831b747d5::$classMap;

        }, null, ClassLoader::class);
    }
}
