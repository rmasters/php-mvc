<?php
/**
 * Special-case router
 * @category    MVC
 * @package     Router
 * @author      Ross Masters <ross@php.net>
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 * @version     0.1
 */

namespace MVC\Router;

/**
 * Special-case route
 * Routes based on matches with a pattern (also matches variables).
 * @category    MVC
 * @package     Router
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 */
class Route
{
    /**
     * Route URI to map against
     * @var string
     */
    private $route;

    /**
     * Variables found in the route
     * @var array
     */
    private $variables = array();

    /**
     * Parts of the route to match against
     * @var array
     */
    private $parts = array();

    /**
     * Constructor
     * @param   string  $route  Route URI
     */
    public function __construct($route) {
        $route = trim($route, '/ ');
        $this->route = $route;

        $this->parseRoute();
    }

    /**
     * Parse the route URI
     */
    private function parseRoute() {
        // Explode it by forward-slashes
        foreach (explode('/', $this->route) as $pos => $part) {
            // If te part is a variable
            if (substr($part, 0, 1) == ':' && substr($part, 1, 1) != ':') {
                // Collect the name and save it's position
                $var = substr($part, 1);
                $this->variables[$pos] = $var;
                $this->parts[$pos] = $var;
            } else {
                // Collect the part
                $this->parts[$pos] = $part;
            }
        }
    }

    /**
     * Match route against a URI
     * @param   string  $path   URI to match from
     * @return  false|array False if no match, otherwise matched variables
     */
    public function match($path) {
        $values = array();
        $path = trim($path, '/ ');
        $path = explode('/', $path);

        // Reset numerical indexes
        $path = array_values($path);

        foreach ($path as $pos => $pathPart) {
            // No match if path is longer than route
            if (!array_key_exists($pos, $this->parts)) {
                return false;
            }

            $var = (isset($this->variables[$pos])) ? $this->variables[$pos] : null;
            $routePart = $this->parts[$pos];

            // If the part isn't a variable compare it directly
            if ($var == null && $routePart != $pathPart) {
                return false;
            }

            // If the part is a variable store it
            if ($var != null) {
                $values[$var] = $pathPart;
            }
        }

        return $values;
    }
}
