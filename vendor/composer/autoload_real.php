<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitf890b5631fe68555a5b89f5cef4ce1e3
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInitf890b5631fe68555a5b89f5cef4ce1e3', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitf890b5631fe68555a5b89f5cef4ce1e3', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitf890b5631fe68555a5b89f5cef4ce1e3::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
