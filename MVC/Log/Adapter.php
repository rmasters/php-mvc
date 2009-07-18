<?php
/**
 * Logging adapter abstract
 * @category    MVC
 * @package     Log
 * @author      Ross Masters <ross@php.net>
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 * @version     0.1
 */

namespace MVC\Log;

/**
 * Logging adapter abstract
 * Provides a base for logging adapters.
 * @category    MVC
 * @package     Log
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 */
abstract class Adapter
{
    /**
     * Log identifer
     * @var string
     */
    protected $name;

    /**
     * Log entries (array of MVC\Log\Entry instances)
     * @var array
     */
    protected $entries = array();

    /**
     * Constructor
     * @param   string  $name   Identifer of log
     */
    public function __construct($name) {
        $this->name = $name;

        $this->open();
    }

    /**
     * Open log hook
     */
    abstract protected function open();

    /**
     * Get an entry by numerical index
     * @param   int $index  Numerical index
     * @return  MVC\Log\Entry|false False if no index set
     */
    public function getEntry($index) {
        if (isset($this->entries[$index])) {
            return $this->entries[$index];
        }
        return false;
    }

    /**
     * Return all entries
     * @return  array   Entry array
     */
    public function getEntries() {
        return $this->entries;
    }

    /**
     * Add an entry to the stack
     * @param   string  $message    Message to add
     */
    public function addEntry($message) {
        $this->entries[] = new Entry($message);
    }

    /**
     * Write entries to the log source
     */
    abstract public function save();
}
