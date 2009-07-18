<?php
/**
 * Default routing class
 * @category    MVC
 * @package     Router
 * @author      Ross Masters <ross@php.net>
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 * @version     0.1
 */

namespace MVC\Router;

/**
 * Default routing class
 * Routes URIs if there is no match with other routes.
 * @category    MVC
 * @package     Router
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 */
class DefaultRoute
{
    /**
     * URI to route from
     * @var string
     */
    private $uri;

    /**
     * Variables determined from URI
     * @var string
     */
    private $request;

    /**
     * Constructor
     * @param   string  $uri    URI to route from
     */
    public function __construct($uri) {
        $this->uri = $uri;
        $this->parseUri();
    }

    /**
     * Parse the URI to read params from it
     */
    private function parseUri() {
        // Split the URI and clean it up, removing empty parts
        $this->uri = trim($this->uri, '/ ');
        $parts = array();
        if (!empty($this->uri)) {
            $parts = explode('/', $this->uri);
        }
        
        // Reset numerical indexes
        $parts = array_values($parts);

        // These are the defaults we'll assume in case of missing params
        $this->request['controller'] = \MVC\MVC::DEFAULT_CONTROLLER;
        $this->request['action'] = \MVC\MVC::DEFAULT_ACTION;
        $this->request['params'] = array();

        // Based on the number of parts we can detect controllers and
        // actions. If there are some missing we rely on the defaults
        $partCount = count($parts);
        switch ($partCount) {
            case 0:
                // nothing given so assume the default names
                break;
            case 1:
                // only one item, we'll assume it's the controller
                $this->request['controller'] = $parts[0];
                break;
            case 2:
                // two items, assumably controller and action
                $this->request['controller'] = $parts[0];
                $this->request['action'] = $parts[1];
                break;
            case 3:
                // three items, one controller and one key/value pair
                // of GET parameters
                $this->request['controller'] = $parts[0];
                $this->request['params'][$parts[1]] = $parts[2];
                break;
            default:
                // For everything onwards we follow a pattern:
                // The first element is always the controller
                // If there's an even number of parts we'll include
                // an action as well, if not we'll assume the default
                // action. For all remaining elements we'll assume 
                // they are GET parameters.

                $this->request['controller'] = array_shift($parts);

                // If the number of parts is even we have an action:
                if (($partCount % 2) == 0) {
                    $this->request['action'] = array_shift($parts);
                }

                // Reset numerical indexes
                $parts = array_values($parts);

                // Loop through remaining elements, assigning to 
                // key/value pairs
                for ($i = 0; $i < count($parts); $i++) {
                    $this->request['params'][$parts[$i]] = $parts[$i++];
                }
                break;
        }

        // Tidy up naming
        $this->request['controller'] = ucfirst(strtolower($this->request['controller']));
        $this->request['action'] = strtolower($this->request['action']);
    }

    /**
     * Return the request array
     * @return  array   URL parts
     */
    public function getRequest() {
        return $this->request;
    }
}
