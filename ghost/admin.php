<?php

class WordpressGhostsAdmin
{

    public static function initialize() {
        self::load_menu();
    }

    public static function load_menu() {
        add_action('init', array('WordpressGhostsAdmin', 'check_roles'));
    }

    public static function check_roles() {
        add_action('admin_menu', array('WordpressGhostsAdmin', 'add_page'), 1);
    }

    public static function add_page() {
        add_menu_page(
          'Ghost users',
          'Ghost users',
          'create_users',
          'wordpress-ghosts',
          array('WordpressGhostsAdmin', 'load_page'),
          '',
          71
        );
    }

    public static function load_page(){
        require_once plugin_dir_path( __FILE__ ) .'admin-page.php';
    }

}
