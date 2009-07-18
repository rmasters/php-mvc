<?php
/**
 * Configuration reader class
 * @category    MVC
 * @package     Config
 * @author      Ross Masters <ross@php.net>
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 * @version     0.1
 */

namespace MVC;

/**
 * Configuration reader
 * Reads configuration information from an adapter.
 * @category    MVC
 * @package     Config
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 */
abstract class Config
{    
    /**
     * Identifier of the config file
     * @var string
     */
    protected $name;

    /**
     * Whether the config file can be updated by manipulating the class
     * @var bool
     */
    protected $writable;

    /**
     * Constructor
     * @param   string  $name   Identifier of the config file
     * @param   bool    $writable   Whether the config can be updated
     */
    public function __construct($name, $writable = false) {
        $this->name = $name;
        $this->writable = (bool) $writable;
        $this->init();
    }

    /**
     * Initialisation hook for adapters
     */
    abstract protected function init();

    /**
     * Update method to save changes made to the configuration back
     * to the source file.
     */
    abstract public function save();
}
