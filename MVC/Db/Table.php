<?php
/**
 * Database table class
 * @category    MVC
 * @package     Db
 * @author      Ross Masters <ross@php.net>
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 * @version     0.1
 */

namespace MVC\Db;
use MVC\Db as Db;

/**
 * Database table class
 * Used for data-table models - provides the default database adapter.
 * @category    MVC
 * @package     Db
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 */
class Table
{
    /**
     * Database adapter
     * @var MVC\Db\Adapter
     */
    protected $adapter;

    /**
     * Constructor
     * @param   MVC\Db\Adapter  $adapter    Database adapter
     */
    public function __construct($adapter = null) {
        // Set the default database adapter if none supplied
        if ($adapter == null) {
            $adapter = Db::getDefaultAdapter();
        }
        $this->adapter = $adapter;
    }
}
