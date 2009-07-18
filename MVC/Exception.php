<?php
/**
 * Default library exception
 * @category    MVC
 * @package     Exception
 * @author      Ross Masters <ross@php.net>
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 * @version     0.1
 */

namespace MVC;

/**
 * Default library exception
 * Applies logging functionality to library exceptions.
 * @category    MVC
 * @package     Exception
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 */
class Exception extends \Exception
{
    /**
     * Constructor
     * @param   string  $message    Message to send
     * @param   int $code   Error code
     * @param   bool    $log    Whether to log the exception
     */
    public function __construct($message, $code = 0, $log = true) {
        // Make sure variables are assigned properly
        parent::__construct($message, $code);

        // Log the exception if enabled
        if ($log) {
            $this->log();
        }
    }

    /**
     * Log the exception
     * Use the file log adapter to save the exception to file.
     * Note that you should set the default path for logs to save in
     * using MVC\Log\Adapter\File::setPath().
     * @todo    Create a new adapter to save more information (serialize it)
     * @todo    User-defined logging method
     */
    private function log() {
        $log = new Log\Adapter\File('exception.log');
        // Log a brief message
        $log->addEntry("($this->code) $this->message");
        $log->save();
    }
}
