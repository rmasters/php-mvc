<?php
/**
 * Variable registry
 * @category    MVC
 * @package     Registry
 * @author      Ross Masters <ross@php.net>
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 * @version     0.1
 */

namespace MVC;

/**
 * An alternative to PHP globals. Variables are stored in a simple
 * key/value store.
 * @category    MVC
 * @package     Registry
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 */
class Registry
{
    /**
     * Singleton instance
     * @var Registry|null
     */
    private static $instance;

    /**
     * Variable array
     * @var array
     */
    private $variables = array();

    /**
     * Get the singleton instance
     * @return  Loader
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Retrieve a variable from the store
     * @param   string  $name   Key variable stored under
     * @return  mixed|null  Null if key not found
     */
    public function __get($name) {
        // Check key exists
        if (!array_key_exists($name, $this->variables)) {
            return null;
        }

        return $this->variables[$name];
    }

    /**
     * Store a variable
     * @param   string  $name   Key to store under
     * @param   mixed   $value  Value to store
     */
    public function __set($name, $value) {
        // Simply store the value, overwriting any previous value
        $this->variables[$name] = $value;
    }

    /**
     * Check whether a variable has been stored
     * @param   string  $name   Key to check under
     * @return  bool    Whether a variable has been stored
     */
    public function __isset($name) {
        return isset($this->variables[$name]);
    }

    /**
     * Remove a variable from the store
     * @param   string  $name   Key to remove under
     */
    public function __unset($name) {
        unset($this->variables[$name]);
    }

    /**
     * Statically get variables from the store
     * @param   string  $name   Key variable stored under
     * @return  mixed|null  Null if key not found
     */
    public static function get($name) {
        $instance = self::instance();
        return $instance->$name;
    }

    /**
     * Statically store variables in the store
     * @param   string  $name   Key to store under
     * @param   mixed   $value  Value to store
     */
    public static function set($name, $value) {
        $instance = self::instance();
        $instance->$name = $value;
    }

    /**
     * Statically check whether a variable has been stored
     * @param   string  $name   Key to check under
     * @return  bool    Whether a variable has been stored
     */
    public static function stored($name) {
        $instance = self::instance();
        return isset($instance->$name);
    }

    /**
     * Statically remove a variable from the store
     * @param   string  $name   Key to remove under
     */
    public static function remove($name) {
        $instance = self::instance();
        unset($instance->$name);
    }
}
