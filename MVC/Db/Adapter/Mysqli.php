<?php
/**
 * MySQLi database adapter
 * @category    MVC
 * @package     Db
 * @author      Ross Masters <ross@php.net>
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 * @version     0.1
 */

namespace MVC\Db\Adapter;

/**
 * MySQLi database adapter
 * Extends database support to MySQL databases using the MySQL improved PHP extension.
 * @category    MVC
 * @package     Db
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 */
class Mysqli extends \MVC\Db\Adapter
{
    /**
     * Connect to the database
     * @throws  \MVC\Exception  If there is a connection error
     */
    protected function connect() {
        // Attempt to create a handle
        $this->handle = mysqli_connect($this->host,
            $this->user, $this->password, $this->name, $this->port, $this->socket);

        // Forward connection errors
        if (mysqli_connect_errno()) {
            throw new \MVC\Exception(mysqli_error(), mysqli_errno());
        }
    }

    /**
     * Create a prepared statement
     * @param   string  $query  SQL to prepare
     * @return  MySQLi_Stmt Prepared statement
     * @throws  MVC\Exception   If SQL errors were found
     */
    public function prepare($query) {
        // Catch SQL errors from the prepared statement
        if (!$stmt = mysqli_prepare($this->handle, $query)) {
            throw new \MVC\Exception($this->error());
        }

        return $stmt;
    }

    /**
     * Get the last error message from the database
     * @return  string  Error emssage
     */
    public function error() {
        return mysqli_error($this->handle);
    }

    /**
     * Get the last error code from the database
     * @return  int Error code
     */
    public function errno() {
        return mysqli_errno($this->handle);
    }
}
