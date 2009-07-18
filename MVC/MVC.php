<?php
/**
 * MVC core class
 * @category    MVC
 * @package     MVC
 * @author      Ross Masters <ross@php.net>
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 * @version     0.1
 */

namespace MVC;

/**
 * MVC core class
 * Contains version information, library constants and static methods
 * used throughout the library.
 * @category    MVC
 * @package     MVC
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 */
class MVC
{
    /**
     * The library version number.
     */
    const VERSION_NUMBER = 0.1;

    /**
     * Used by MVC\Router to define the default controller and action
     * to use if unable to determine one.
     */
    const DEFAULT_CONTROLLER = 'Index';
    const DEFAULT_ACTION = 'index';
}
