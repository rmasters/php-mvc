<?php
/**
 * Library and class loader
 * @category    MVC
 * @package     Loader
 * @author      Ross Masters <ross@php.net>
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 * @version     0.1
 */

namespace MVC;

/**
 * Manages paths to libraries and provides an autoloader to load 
 * classes and interfaces from them.
 * @category    MVC
 * @package     Loader
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 */
class Loader
{
    /**
     * Singleton instance
     * @var Loader|null
     */
    private static $instance;

    /**
     * Default library location
     * @var string|null
     */
    private $defaultLibraryPath;

    /**
     * Paths for specific libraries
     * @var array
     */
    private $paths = array();

    /**
     * Get the singleton instance
     * @return  Loader
     */
    private static function instance() {
        if (self::$instance == null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Autoloader callback
     * The autoloader looks for classes in Loader::defaultLibraryPath
     * and in specified library paths using namespaces as a directory
     * structure. For example:
     * \MVCWeb\Loader should be \defaultLibraryPath\MVCWeb\Loader.php
     * And so on with sub-namespaces.
     * Use with spl_register_autoload().
     * @param   string  $name   Name of class/interface to load
     * @throws  MVC\Exception   If the expected location couldn't be found
     * @throws  MVC\Exception   If the class/interface wasn't found in the location
     */
    public static function autoload($name) {
        $instance = self::instance();

        // Split the class name by namespaces
        $nameParts = explode('\\', $name);
        // Remove empty keys
        array_map('trim', $nameParts);
        foreach ($nameParts as $k => $part) {
            if (empty($part)) {
                unset($nameParts[$k]);
            }
        }
        $nameParts = array_values($nameParts);

        if (array_key_exists($nameParts[0], $instance->paths)) {
            // If the class is of a recognised namespace redirect to its known path
            $path = $instance->paths[array_shift($nameParts)] . '/' . implode('/', $nameParts) . '.php';
        } elseif (count($nameParts) == 1) {
            // If the class is in the global namespace assume it's located in APPLICATION_PATH
            $path = APPLICATION_PATH . "/$name.php";
        } else {
            $path = $instance->defaultLibraryPath . '/' . implode('/', $nameParts) . '.php';
        }

        // Check file exists
        if (!file_exists($path)) {
            throw new Exception("Couldn't find '$path' to load '$name'.");
        }

        require_once $path;

        // Check class/interface has been declared
        if (!class_exists($name) && !interface_exists($name)) {
            throw new Exception("Attempted to load '$name' but it wasn't declared in '$path'.");
        }
    }

    /**
     * Adds a lookup path for classes in a namespace
     * @param   string  $namespace  Namespace for path
     * @param   string  $path   Path to look in
     * @throws  MVC\Exception   If the path didn't exist
     */
    public static function addPath($namespace, $path) {
        $instance = self::instance();
        if (!is_dir($path)) {
            throw new Exception("The path given ('$path') does not exist.");
        }
        $instance->paths[$namespace] = $path;
    }

    /**
     * Sets the default path to libraries to look for classes
     * @param   string  $path   Where libraries are stored by default
     * @throws  MVC\Exception   If the path didn't exist
     */
    public static function setLibraryPath($path) {
        $instance = self::instance();
        if (!is_dir($path)) {
            throw new Exception("The path given ('$path') does not exist.");
        }
        $instance->defaultLibraryPath = $path;
    }
}
