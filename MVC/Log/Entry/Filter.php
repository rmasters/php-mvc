<?php
/**
 * Filtered log entry
 * @category    MVC
 * @package     Log
 * @author      Ross Masters <ross@php.net>
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 * @version     0.1
 */

namespace MVC\Log\Entry;

/**
 * Filtered log entry
 * Provides an interface for reading plain-text log entries in a set format.
 * @category    MVC
 * @package     Log
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 */
class Filter extends \MVC\Log\Entry
{
    /**
     * Mask variables
     * @var array
     */
    private $variables = array();

    /**
     * Constructor
     * @param   string  $mask   Mask of filter
     * @param   string  $text   Text to parse
     */
    public function __construct($mask, $text) {
        $this->match($mask, $text);
        $this->logged = $this->variables['logged'];
        $this->message = $this->variables['message'];
    }

    /**
     * Match a mask and text
     * @param   string  $mask   Mask of filter
     * @param   string  $text   Text to parse
     */
    private function match($mask, $text) {
        // firstly match vars from the mask
        // In format of '%name' but only logged|message atm
        preg_match_all('/%(logged|message)/', $mask, $maskVars);

        // Build a regex match string
        $maskVars[0] = array_map(function ($var) {
            return "/$var/";
        }, $maskVars[0]);
        $regexMask = preg_replace($maskVars[0], '(.+)', $mask);

        // match vars from actual message
        // escape regex characters
        $regexMask = str_replace(array('[', ']'), array('\[', '\]'), $regexMask);
        // add regex newlines instead of actual new lines
        $regexMask = str_replace(array("\n", "\r"), array('\n', '\r'), $regexMask);
        // match
        preg_match("/$regexMask/", $text, $matches);

        // match variables
        // remove original regex string from matches and reindex
        array_shift($matches);
        $matches = array_values($matches);
        // join labels with variables
        foreach ($matches as $index => $value) {
            $this->variables[$maskVars[1][$index]] = $value;
        }
    }
}
