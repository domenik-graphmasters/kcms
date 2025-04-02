<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitdc9c71d111d6b8728f51975814621250
{
    public static $files = array (
        '6e3fae29631ef280660b3cdad06f25a8' => __DIR__ . '/..' . '/symfony/deprecation-contracts/function.php',
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        '89efb1254ef2d1c5d80096acd12c4098' => __DIR__ . '/..' . '/twig/twig/src/Resources/core.php',
        'ffecb95d45175fd40f75be8a23b34f90' => __DIR__ . '/..' . '/twig/twig/src/Resources/debug.php',
        'c7baa00073ee9c61edf148c51917cfb4' => __DIR__ . '/..' . '/twig/twig/src/Resources/escaper.php',
        'f844ccf1d25df8663951193c3fc307c8' => __DIR__ . '/..' . '/twig/twig/src/Resources/string_loader.php',
    );

    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Twig\\' => 5,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Polyfill\\Ctype\\' => 23,
        ),
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Twig\\' => 
        array (
            0 => __DIR__ . '/..' . '/twig/twig/src',
        ),
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'H' => 
        array (
            'Httpful' => 
            array (
                0 => __DIR__ . '/..' . '/nategood/httpful/src',
            ),
            'Hautelook' => 
            array (
                0 => __DIR__ . '/..' . '/bordoni/phpass/src',
            ),
        ),
        'C' => 
        array (
            'Console' => 
            array (
                0 => __DIR__ . '/..' . '/pear/console_getopt',
            ),
        ),
        'A' => 
        array (
            'Archive_Tar' => 
            array (
                0 => __DIR__ . '/..' . '/pear/archive_tar',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'OS_Guess' => __DIR__ . '/..' . '/pear/pear-core-minimal/src/OS/Guess.php',
        'PEAR' => __DIR__ . '/..' . '/pear/pear-core-minimal/src/PEAR.php',
        'PEAR_Error' => __DIR__ . '/..' . '/pear/pear-core-minimal/src/PEAR.php',
        'PEAR_ErrorStack' => __DIR__ . '/..' . '/pear/pear-core-minimal/src/PEAR/ErrorStack.php',
        'PEAR_Exception' => __DIR__ . '/..' . '/pear/pear_exception/PEAR/Exception.php',
        'System' => __DIR__ . '/..' . '/pear/pear-core-minimal/src/System.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitdc9c71d111d6b8728f51975814621250::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitdc9c71d111d6b8728f51975814621250::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitdc9c71d111d6b8728f51975814621250::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitdc9c71d111d6b8728f51975814621250::$classMap;

        }, null, ClassLoader::class);
    }
}
