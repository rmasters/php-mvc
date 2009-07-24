<?php
/**
 * Database adapter
 * @category    MVC
 * @package     Db
 * @author      Ross Masters <ross@php.net>
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 * @version     0.1
 */

namespace MVC\Db;
use MVC\Exception as Exception;

/**
 * Abstract database adapter
 * @category    MVC
 * @package     Db
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 */
abstract class Adapter {
    /**
     * Hostname
     * @var string
     */
    protected $host;

    /**
     * Username
     * @var string
     */
    protected $user;

    /**
     * Password for user
     * @var string
     */
    protected $password;

    /**
     * Name of database
     * @var string
     */
    protected $name;

    /**
     * Port of database server
     * @var string
     */
    protected $port;

    /**
     * Socket for database server
     * @var string
     */
    protected $socket;

    /**
     * Database handle/resource
     * @var mixed
     */
    protected $handle;

    /**
     * Constructor
     * @param   array   $info   Connection information
     * @throws  Exception   If missing required credentials
     */
    public function __construct(array $info) {
        // Check required credentials are present
        if (!isset($info['host'], $info['user'], $info['password'], $info['name'])) {
            throw new Exception("A host, user, password and database must be specified.");
        }

        // Assign credentials
        $this->host = $info['host'];
        $this->user = $info['user'];
        $this->password = $info['password'];
        $this->name = $info['name'];
        if (isset($info['port'])) {
            $this->port = $info['port'];
        }
        if (isset($info['socket'])) {
            $this->socket = $info['socket'];
        }

        // Call connect hook
        $this->connect();
    }

    /**
     * Connect to the database
     */
    abstract protected function connect();

    /**
     * Create a prepared statement
     * @param   string  $query  SQL query to prepare
     */
    abstract public function prepare($query);

    /**
     * Return the last error message
     * @return  string  Error message
     */
    abstract public function error();

    /**
     * Return the last error code
     * @return  int Error code
     */
    abstract public function errno();
}
