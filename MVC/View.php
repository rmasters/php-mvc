<?php
/**
 * View manipulator
 * @category    MVC
 * @package     View
 * @author      Ross Masters <ross@php.net>
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 * @version     0.1
 */

namespace MVC;

/**
 * Load a view (template) and pass variables to it, while using PHP
 * as musch as possible for 'templating' features.
 * @category    MVC
 * @package     View
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 */
class View
{
    /**
     * Path to view script
     * @var string
     */
    private $path;

    /**
     * Template variable store
     * @var array
     */
    private static $variables = array();

    /**
     * Registry instance
     * @var Registry
     */
    private static $registry;

    /**
     * Constructor
     * @param   string  $name   Name of view to load
     */
    public function __construct($name) {
        $this->path = APPLICATION_PATH . '/views/' . $name;
        $this->checkPath();
    }

    /**
     * Set the registry instance
     * @param   MVC\Registry    $registry   Registry object
     */
    public static function setRegistry(Registry $registry) {
        self::$registry = $registry;
    }

    /**
     * Check the path of the view to check it exists
     * @throws  MVC\Exception   If view does not exist
     */
    private function checkPath() {
        if (!file_exists($this->path)) {
            throw new Exception("View '" . basename($this->path) . "' not found in '$this->path'.");
        }
    }

    /**
     * Template variable setter
     * @param   string  $name   Property name
     * @param   string  $value  Property value
     */
    public function __set($name, $value) {
        self::$variables[$name] = $value;
    }

    /**
     * Template variable getter
     * @param   string  $name   Property name
     * @return  mixed   Property value
     */
    public function __get($name) {
        return self::$variables[$name];
    }

    /**
     * Check if a template variable exists
     * @param   string  $name   Property name
     * @return  bool    True if property set
     */
    public function __isset($name) {
        return isset(self::$variables[$name]);
    }

    /**
     * Remove a property from the store
     * @param   string  $name   Property name
     */
    public function __unset($name) {
        unset(self::$variables[$name]);
    }

    /**
     * Return the template variables
     * @return  array
     */
    public function getVars() {
        return self::$variables;
    }

    /**
     * Output the template
     * If development mode is enabled include comments noting the 
     * start and end of templates.
     * Extract the template variables locally and include the view
     * executing any embedded PHP.
     * Finally flush output to the browser to speed up page delivery.
     */
    public function output() {
        if (APPLICATION_ENV == 'development') {
            echo "<!-- Start view '" . basename($this->path) . "' -->\n";
        }

        // define variables in the local scope
        extract(self::$variables);
        include $this->path;

        if (APPLICATION_ENV == 'development') {
            echo "<!-- End view '" . basename($this->path) . "' -->\n";
        }

        flush();
    }

    /**
     * Output the template as a string
     * @return  string  View contents
     * @see MVC\View::output()
     */
    public function __toString() {
        $this->output();
        $contents = ob_get_contents();
        ob_clean();
        return $contents;
    }
}
