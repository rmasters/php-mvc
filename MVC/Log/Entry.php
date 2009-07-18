<?php
/**
 * Log entry
 * @category    MVC
 * @package     Log
 * @author      Ross Masters <ross@php.net>
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 * @version     0.1
 */

namespace MVC\Log;

/**
 * Log entry
 * An individual entry, held in MVC\Log\Adapter's.
 * @category    MVC
 * @package     Log
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 */
class Entry
{
    /**
     * Log message
     * @var string
     */
    protected $message;

    /**
     * When the entry was logged (an RFC 2822 formatted date string)
     * @var string
     */
    protected $logged;

    /**
     * Constructor
     * @param   string  $message    Log message
     */
    public function __construct($message) {
        $this->message = $message;
        $this->logged = date('r');
    }

    /**
     * String representation of log entry
     * @return  string  Log line
     */
    public function __toString() {
        return "[$this->logged] $this->message";
    }
}
