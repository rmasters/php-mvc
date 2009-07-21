<?php
/**
 * Configuration adapter for ini files
 * @category    MVC
 * @package     Config
 * @author      Ross Masters <ross@php.net>
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 * @version     0.1
 */

namespace MVC\Config;

/**
 * Configuration adapter for ini files
 * Extends Config to read and compile to ini files
 * @category    MVC
 * @package     Config
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 */
class Ini extends \MVC\Config
{
    /**
     * Path to the file
     * @var string
     */
    private $path;

    /**
     * File data parsed from the original file
     * @var array
     */
    private $file;

    /**
     * Location of config files
     * @var string
     */
    private static $configPath;

    /**
     * Initialisation hook
     * Loads the file and replaces user-defined PHP constants within it
     */
    protected function init() {
        $this->load();
    }

    /**
     * Loads the ini file
     * @throws  MVC\Exception   If file not found
     */
    private function load() {
        // Set path, depending on default path being set
        if (self::$configPath != null) {
            $this->path = self::$configPath . "/$this->name";
        } else {
            $this->path = $this->name;
        }

        // Throw an exception if the path couldn't be loaded
        if (!file_exists($this->path)) {
            throw new \MVC\Exception("Config file '$this->name' not found in '$this->path'.");
        }

        // Load in and parse the ini file
        $file = parse_ini_file($this->path);

        /**
         * Loop through each key and split it into an array based on
         * period syntax:
         *   database.user = ross
         * Equates to:
         *   Array (
         *     'database' => Array (
         *       'user' => 'ross'
         *   ))
         */
        foreach ($file as $key => $value) {
            // split '.' delimited keys as arrays
            if (strpos($key, '.')) {
                // Explode based on periods and keep the last element as the final key
                $keys = explode('.', $key);
                $lastKey = array_pop($keys);
                // Set a reference to the base array for easier appending
                $ref =& $file;
                // For each key
                for ($i = 0; $i < count($keys); $i++) {
                    // If no key exists make a new array at $ref
                    if (!array_key_exists($keys[$i], $ref)) {
                        $ref[$keys[$i]] = array();
                    }
                    // And update $ref to the new array
                    $ref =& $ref[$keys[$i]];
                }
                // Finally set the last key and value at $ref
                $ref[$lastKey] = $value;
                // And remove the old key
                unset($file[$key]);
            }
        }

        // Replace constants with values
        $this->file = $this->replaceConstants($file);        
    }

    /**
     * Find and replace user constants in the ini file
     */
    private function replaceConstants(array $data) {
        // Get an array of constants grouped into ext/php/user etc.
        $constants = get_defined_constants(true);

        // Find and replace in every value
        foreach ($data as $key => $value) {
            $data[$key] = str_replace(
                array_keys($constants['user']), 
                array_values($constants['user']), 
                $value
            );
        }

        return $data;
    }

    /**
     * Set values in the config instance
     * These don't take effect on the source file without save()ing first.
     * @param   string  $name   Name of property
     * @param   mixed   $value  Value to set property to
     */
    public function __set($name, $value) {
        $this->file[$name] = $value;
    }

    /**
     * Get values from the config instance
     * @param   string  $name   Name of property
     * @return  mixed|null   Value of property
     */
    public function __get($name) {
        return $this->file[$name];
    }

    /**
     * Check properties have been set
     * @param   string  $name   Name of property
     * @return  bool    True if property set
     */
    public function __isset($name) {
        return isset($this->file[$name]);
    }

    /**
     * Remove properties from the instance
     * @param   string  $name   Name of property
     */
    public function __unset($name) {
        unset($this->file[$name]);
    }

    /**
     * Save changes made to the instance
     * @throws  MVC\Exception   If attempted to save without making the instance writable
     */
    public function save() {
        // If the instance wasn't made writable when instatiated
        if (!$this->writable) {
            throw new \MVC\Exception("You must instantiate the config instance with \$writable set to true to save.");
        }

        // Compile the values
        $configString = $this->compile($this->file);

        // Save the string to file
        file_put_contents($this->path, $configString);
    }

    /**
     * Compile a data array into ini form
     * @param   array   $data   Data to compile
     * @param   string  $configString   String to append to
     * @return  string  Compiled string
     */
    private function compile(array $data, $configString = '') {
        foreach ($data as $key => $value) {
            // impossible to differentiate between sections and array-style values
            // so only sections - no array-style values
            if (is_array($value)) {
                $configString = $this->compile($value);
            } else {
                // Simple append the value
                if (is_string($value)) {
                    $value = "\"$value\"";
                }
                $configString .= "$key = $value\n";
            }
        }
        return $configString;
    }

    /**
     * Set a default path for config files to be located
     * @param string    $configPath Default path
     */
    public static function setConfigPath($configPath) {
        self::$configPath = $configPath;
    }
}
