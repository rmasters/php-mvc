<?php
/**
 * Controller abstract
 * @category    MVC
 * @package     Controller
 * @author      Ross Masters <ross@php.net>
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 * @version     0.1
 */

namespace MVC;

/**
 * Abstract controller base
 * @category    MVC
 * @package     Controller
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 */
class Controller
{
    /**
     * Registry instance for easy manipulation
     * @var MVC\Registry|null
     */
    protected static $registry;

    /**
     * Router instance
     * @var MVC\Router|null
     */
    protected static $router;

    /**
     * An array of controller actions
     * @var array|null
     */
    private static $actions;

    /**
     * Constructor
     * Loads actions and calls initialisation hook
     */
    public function __construct() {
        $this->getActions();

        // Call controller initialisation hook
        if (method_exists($this, 'init')) {
            $this->init();
        }
    }

    /**
     * Get the actions the controller implements
     * Only check the action list once and store it statically
     * @return  array
     */
    public function getActions() {
        // If controller actions haven't been read yet
        if (self::$actions == null) {
            $methods = get_class_methods($this);
            self::$actions = array();
            foreach ($methods as $method) {
                // Read only methods ending in the 'Action' suffix
                if (substr($method, -6) == 'Action') {
                    self::$actions[] = substr($method, 0, -6);
                }
            }
        }
        
        return self::$actions;
    }

    /**
     * Set the registry instance
     * @var MVC\Registry
     */
    public static function setRegistry(Registry $registry) {
        self::$registry = $registry;
    }

    /**
     * Set the router instance
     * @var MVC\Router
     */
    public static function setRouter(Router $router) {
        self::$router = $router;
    }

    /**
     * Controller helper; redirects the user to another page
     */
    public function redirect($url, $httpStatus = 302) {
        header('Location: ' . $url, true, $httpStatus);
        // In case the client does not follow location headers
        die("<a href=\"$url\">You have been redirected</a>");
    }
}
