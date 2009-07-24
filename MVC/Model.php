<?php
/**
 * Data model abstract
 * @category    MVC
 * @package     Model
 * @author      Ross Masters <ross@php.net>
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 * @version     0.1
 */

namespace MVC;

/**
 * Model base for retrieving data from a database
 * @category    MVC
 * @package     Model
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 */
abstract class Model
{
    /**
     * Method to set properties of the model using the setName() methods
     * @param   array   $options    Key/Value array of properties and values
     * @throws  MVC\Exception   If property didn't exist/didn't have a setter method
     */
    public function setOptions(array $options) {
        // Rather than call method_exists each time check an array
        $methods = get_class_methods($this);
        // Loop through each option and set
        foreach ($options as $property => $value) {
            $method = 'set' . ucfirst($property);
            // If no setter method is found throw an exception
            if (!in_array($method, $methods)) {
                throw new Exception("Can't set value for $name.");
            }
            $this->$method($value);
        }
    }

    /**
     * Overloaded setter method
     * @param   string  $name   Name of property
     * @param   mixed   $value  Value to set property to
     * @throws  \MVC\Exception  If no setter method exists
     */
    public function __set($name, $value) {
        $method = 'set' . ucfirst($name);
        // If no setter method is found throw an exception
        if (!method_exists($this, $method)) {
            throw new Exception("Can't set value for $name.");
        }

        $this->$method($value);
    }

    /**
     * Overloaded getter method
     * @param   string  $name   Name of property
     * @throws  \MVC\Exception  If no getter method exists
     */
    public function __get($name) {
        $method = 'get' . ucfirst($name);
        // If no getter method is found throw an exception
        if (!method_exists($this, $method)) {
            throw new Exception("Can't get value for $name.");
        }

        return $this->$method();
    }
}
