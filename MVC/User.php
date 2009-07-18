<?php
/**
 * User class
 * @category    MVC
 * @package     User
 * @author      Ross Masters <ross@php.net>
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 * @version     0.1
 */

namespace MVC;

/**
 * User class
 * Used to track user information and authenticate them.
 * @category    MVC
 * @package     User
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 * @todo        Different adapters
 */
class User
{
    /**
     * User information retrieved from database
     * @var array
     */
    private $info = array();

    /**
     * Salts used in password hashing method
     * @var array
     * @see MVC\User::setSalts()
     * @see MVC\User::password()
     */
    private static $salts;

    /**
     * Constructor
     * @param   int $userId User to retrieve info from
     */
    public function __construct($userId = null) {
        if ($userId != null) {
            if (!$this->getInfo($userId)) {
                return false;
            }
        }
    }

    /**
     * Get information on a user
     * @param   int $userId User to retrieve information on
     * @return  false|void  False if no user found, otherwise info added to object
     */
    public function getInfo($userId) {
        // Query the database for information
        $check = \MVC\Db::prepare('SELECT id, username, email, registered FROM users WHERE id = ? LIMIT 1');
        $check->bind_param('i', $userId);
        $check->execute();
        $check->store_result();
        // Return false if no user found
        if ($check->num_rows == 0) {
            $check->close();
            return false;
        }

        // Store the information
        $check->bind_result(
            $this->info['id'],
            $this->info['username'],
            $this->info['email'],
            $this->info['registered']
        );
        $check->fetch();
        $check->close();
    }

    /**
     * Overloaded setter
     * Sets properties, but only once
     * @param   string  $name   Name of property to set
     * @param   mixed   $value  Value to set property to
     * @see MVC\User::authenticate()
     */
    public function __set($name, $value) {
        if (isset($this->$name)) {
            throw new Exception("Couldn't set property '$name' since it was already set to '$this->$name'.");
        }
        $this->$name = $value;
    }

    /**
     * Overloaded getter for user information
     * @param   string  $name   Property name
     * @return  mixed|null
     */
    public function __get($name) {
        return $this->info[$name];
    }

    /**
     * Staticly authenticate the user by checing a username/password match
     * @param   string  $username   Username to check
     * @param   string  $password   Coupling password
     * @return  false|MVC\User  False if no match, user instance if they do
     */
    public static function authenticate($username, $password) {
        // Prepare the password
        $password = self::password($password);
        // Retrieve data from the database
        $check = \MVC\Db::prepare('SELECT id, username, email, registered FROM users WHERE username = ? AND password = ? LIMIT 1');
        $check->bind_param('ss', $username, $password);
        $check->execute();
        $check->store_result();
        // Return false if no match found
        if ($check->num_rows == 0) {
            $check->close();
            return false;
        }

        // Setup a new user instance
        $user = new self;
        $check->bind_result(
            $userId,
            $userUsername,
            $userEmail,
            $userRegistered
        );
        $check->fetch();
        $check->close();

        $user->id = $userId;
        $user->username = $userUsername;
        $user->email = $userEmail;
        $user->registered = $userRegistered;
        return $user;
    }

    /**
     * Make a hash of the password so it is unreadable should the database be accessed
     * Salt with 2 user-defined salts
     * @param   string  $password   Password to hash
     * @return  string  Hashed password
     * @see MVC\User::setSalts()
     */
    public static function password($password) {
        if (!isset(self::$salts[1], self::$salts[0])) {
            throw new Exception("No salts defined for password hashing.");
        }

        return sha1(self::$salts[1] . $password . self::$salts[0]);
    }

    /**
     * Set the salts used in the password hashing method
     * @param   string  $salt1  First salt
     * @param   string  $salt2  Second salt
     * @see MVC\User::password()
     */
    public static function setSalts($salt1, $salt2) {
        self::$salts = array($salt1, $salt2);
    }
}
