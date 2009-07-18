<?php
/**
 * MVC core class
 * @category    MVC
 * @package     Db
 * @author      Ross Masters <ross@php.net>
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 * @version     0.1
 */

namespace MVC;

/**
 * MVC database factory
 * Creates and handles database adapters.
 * @category    MVC
 * @package     Db
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 */
class Db
{
    /**
     * The default database adapter
     * @var MVC\Db\Adapter|null
     */
    private static $defaultAdapter;

    /**
     * Factory method to create an adapter
     * @param   string  $adapter    Adapter name
     * @param   array   $info   Connection information
     * @param   bool    $autoDefault    Automatically set the new adapter as the default database adapter
     * @return  MVC\Db\Adapter  The new database adapter
     * @throws  MVC\Exception   If connection information missing
     * @throws  MVC\Exception   If adapter not available
     */
    public static function create($adapter, array $info, $autoDefault = true) {
        // Throw an exception if the required connection info is missing
        if (!isset($info['host'], $info['user'], $info['password'], $info['name'])) {
            throw new Exception("A host, user, password and database must be specified.");
        }

        // Normalise the adapter name
        $adapter = ucfirst(strtolower($adapter));

        // Check the adapter is available in MVC\Db\Adapter
        $adapterFull = "MVC\Db\Adapter\\$adapter";
        if (!class_exists($adapterFull)) {
            throw new Exception("The '$adapter' database adapter is not loaded.");
        }

        // Instantiate the adapter
        $dbAdapter = new $adapterFull($info);

        // Automatically set the new adapter as the default adapter
        if ($autoDefault) {
            self::setDefaultAdapter($dbAdapter);
        }

        return $dbAdapter;
    }

    /**
     * Create a prepared statement using the default adapter
     * @param   string  $query  Query string to use
     * @return  The adapter's prepared statement
     */
    public static function prepare($query) {
        return self::$defaultAdapter->prepare($query);
    }

    /**
     * Set the default database adapter to a new adapter
     * @param   MVC\Db\Adapter  $database   Adapter
     */
    public static function setDefaultAdapter(Db\Adapter $database) {
        self::$defaultAdapter = $database;
    }

    /**
     * Fetch the default adapter
     * @return  MVC\Db\Adapter
     * @throw   MVC\Exception   If no adapter set
     */
    public static function getDefaultAdapter() {
        if (self::$defaultAdapter == null) {
            throw new Exception("No default database adapter set.");
        }
        return self::$defaultAdapter;
    }
}
