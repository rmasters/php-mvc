<?php
/**
 * URI router
 * @category    MVC
 * @package     Router
 * @author      Ross Masters <ross@php.net>
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 * @version     0.1
 */

namespace MVC;

/**
 * URI router
 * Routes URIs using URL-based maps to controllers and actions
 * @category    MVC
 * @package     Router
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 */
class Router
{
    /**
     * URI to route from
     * @var string
     */
    private $uri;

    /**
     * Array of special maps for specific URIs
     * @var array
     */
    public $routes = array();

    /**
     * Data determined from examing the URI
     * @var array
     */
    private $request = array();

    /**
     * Constructor
     * @param   string  $uri    URI to route from
     */
    public function __construct($uri) {
        $this->uri = $uri;
    }

    /**
     * Initiate the router - using added routes and the default router
     */
    public function getRoute() {
        $match = false;
        $this->request = array(
            'controller' => '',
            'action' => '',
            'params' => array()
        );

        // If special routes have been added try to match the uri with them
        if (count($this->routes) != 0) {
            foreach ($this->routes as $name => $route) {
                // If a match is found break out of the loop
                $variables = $route['route']->match($this->uri);
                if(is_array($variables)) {
                    $match = $name;
                    break;
                }
            }
        }

        // Use the default router if no match is found
        if ($match) {
            $info = $this->routes[$name]['info'];
            $this->request['controller'] = (isset($info['controller'])) ? $info['controller'] : MVC\MVC::DEFAULT_CONTROLLER;
            $this->request['action'] = (isset($info['action'])) ? $info['action'] : MVC\MVC::DEFAULT_ACTION;
            $this->request['params'] = (isset($info['params'])) ? $info['params'] : array();
            $this->request['params'] = array_merge($this->request['params'], $variables);
        } else {
            $route = new Router\DefaultRoute($this->uri);
            $this->request = $route->getRequest();
        }

        // Merge in GET params with superglobal, giving preference to those from the URI
        if (count($this->request['params']) != 0) {
            $_GET = array_merge($_GET, $this->request['params']);
        }
    }

    /**
     * Add a special route for the router to match against
     * @param   string  $name   Name of the route
     * @param   MVC\Router\Route    $route  Route object to match against
     * @param   array   $info   Information (controller/action/params) about the route
     * @throws  MVC\Exception   If route name already used
     */
    public function addRoute($name, Router\Route $route, array $info) {
        // Check name is unique
        if (array_key_exists($name, $this->routes)) {
            throw new Exception("Route already exists with name '$name'.");
        }

        $this->routes[$name] = array('route' => $route, 'info' => $info);
    }

    /**
     * Get the controller name (after calling getRoute())
     * @return  string  Controller name
     */
    public function getController() {
        return $this->request['controller'];
    }

    /**
     * Get the action name (after calling getRoute())
     * @return  string  Action name
     */
    public function getAction() {
        return $this->request['action'];
    }
}
