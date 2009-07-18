<?php
/**
 * User session handler
 * @category    MVC
 * @package     User
 * @author      Ross Masters <ross@php.net>
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 * @version     0.1
 */

namespace MVC\User;

/**
 * User session handler
 * Creates, resumes and closes user sessions. Prevents session hijacking.
 * @category    MVC
 * @package     User
 * @copyright   Copyright (c) 2009 Ross Masters.
 * @license     http://wiki.github.com/rmasters/php-mvc/license New BSD License
 */
class Session
{
    /**
     * Salts for key generation
     * @var array
     */
    private static $salts;
    
    /**
     * Create a new user session
     * @param   MVC\User    $user   User object to create session for
     * @return  MVC\User    Modified user instance
     */
    public static function create(\MVC\User $user) {
        // Prepare database params
        $userId = $user->id;
        $sessionKey = self::generateSessionKey($user->id);
        $logoutKey = self::generateLogoutKey($user->id);
        $expiry = 1; // 1 day
        // Prepare insert
        $session = \MVC\Db::prepare('INSERT INTO sessions (user, created, expires, session_key, logout_key) VALUES (?, NOW(), ADDDATE(NOW(), ?), ?, ?)');
        $session->bind_param('iiss', $userId, $expiry, $sessionKey, $logoutKey);
        $session->execute();
        $session->close();

        // Update user object with keys
        $user->sessionKey = $sessionKey;
        $user->logoutKey = $logoutKey;

        return $user;
    }

    /**
     * Set salts for session key and logout key
     * @param   string  $sessionKey Salt for session key
     * @param   string  $logoutKey Salt for logout key
     */
    public static function setSalts($sessionKey, $logoutKey) {
        self::$salts = array('session_key' => $sessionKey,
                             'logout_key' => $logoutKey);
    }

    /**
     * Generate a session key using a user-defined salt
     * @param   int $userId User id session for
     * @return  string  Session key
     */
    private static function generateSessionKey($userId) {
        return sha1(self::salts['session_key'] . time() . $userId);
    }

    /**
     * Generate a logout key using a user-defined salt
     * @param   int $userId User id session for
     * @return  string  Logout key
     */
    private static function generateLogoutKey($userId) {
        return sha1(self::salts['logout_key'] . time() . $userId);
    }

    /**
     * Resume a user session
     * @param   int $userId User id to resume
     * @param   string  $sessionKey Session key matched with user
     * @return  false|MVC\User  False if no session found or no user found, otherwise user object
     */
    public static function resume($userId, $sessionKey) {
        // Check a session exists
        if (!$session = self::exists($userId, $sessionKey)) {
            return false;
        }

        // Create a user instance
        $user = new \MVC\User($userId);
        // Return false if user not found
        if (!$user) {
            return false;
        }
        // Set session details with user
        $user->sessionKey = $session['session_key'];
        $user->logoutKey = $session['logout_key'];
        return $user;
    }

    /**
     * Close a user session
     * @param   MVC\User    $user   User instance
     * @return  bool    True if session closed successfully
     */
    public static function close(\MVC\User $user) {
        // Prepare params
        $userId = $user->id;
        $sessionKey = $user->sessionKey;
        $logoutKey = $user->logoutKey;

        // Prepare delete query
        $delete = \MVC\Db::prepare('DELETE FROM sessions WHERE user = ? AND session_key = ? AND logout_key = ? LIMIT 1');
        $delete->bind_param('iss', $userId, $sessionKey, $logoutKey);
        $delete->execute();
        $rows = $delete->affected_rows;
        $delete->close();

        // If no session found return false
        // Shouldn't happen unless this method allowed to be called by non-authed users
        if ($rows == 0) {
            return false;
        }
        return true;
    }

    /**
     * Check whether the session exists
     * @param   int $userId User id for session
     * @param   string  $sessionKey Associated session key
     * @return  false|array False if session didn't exist, otherwise array of keys
     */
    private static function exists($userId, $sessionKey) {
        // Prepare check query
        $query = 'SELECT session_key, logout_key FROM sessions WHERE user = ? AND session_key = ? AND expires > NOW() LIMIT 1';
        $check = \MVC\Db::prepare($query);
        $check->bind_param('is', $userId, $sessionKey);
        $check->execute();
        $check->store_result();
        // Return false if session didn't exist
        if ($check->num_rows == 0) {
            $check->close();
            return false;
        }

        // Return session and logout keys
        $return = array();
        $check->bind_result($return['session_key'], $return['logout_key']);
        $check->fetch();
        $check->close();

        return $return;
    }
}
