<?php
/**
 * Plain-text log file
 * @category    MVC
 * @package     Log
 * @author      Ross Masters <ross@php.net>
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 * @version     0.1
 */

namespace MVC\Log\Adapter;

/**
 * Plain-text log file
 * Stores logs in plain text - one entry per line.
 * @category    MVC
 * @package     Log
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 */
class File extends \MVC\Log\Adapter
{
    /**
     * Whether to automatically create log files if they don't exist
     * @var bool
     */
    private $autoCreate;

    /**
     * Default path for log files
     * @var string
     */
    private static $logPath;

    /**
     * Constructor
     * @param   string  $name   Name of log file
     * @param   bool    $autoCreate Whether to create the log file if it doesn't exist
     */
    public function __construct($name, $autoCreate = true) {
        $this->autoCreate = (bool) $autoCreate;
        // Call parent constructor
        parent::__construct($name);
    }

    /**
     * Set the default log path
     * @param   string  $path   Path to log files
     */
    public static function setPath($path) {
        self::$logPath = $path;
    }

    /**
     * Open hook
     * Opens file and parses
     * @throws  MVC\Exception   If file not found
     */
    protected function open() {
        // Set path
        $path = self::$logPath . "/$this->name";
        // Create an empty file if file doesn't exist or throw an exception
        if (!file_exists($path)) {
            if ($this->autoCreate) {
                file_put_contents($path, '');
            } else {
                throw new \MVC\Exception("No log file found in '$path'", 0, false);
            }
        }

        // Get and trim the log file
        $log = file_get_contents($path);
        $log = trim($log);

        // Parse log file
        $entries = explode("\n", $log);       
        foreach ($entries as $entry) {
            if (!empty($entry)) {
                $entry = trim($entry);
                // Use the Filter Entry class to read logs
                $this->entries[] = new \MVC\Log\Entry\Filter("[%logged] %message", $entry);
            }
        }
    }

    /**
     * Save method
     */
    public function save() {
        $path = self::$logPath . "/$this->name";
        $log = implode("\n", $this->entries);
        file_put_contents($path, $log);
    }
}
