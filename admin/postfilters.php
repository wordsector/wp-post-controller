<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class WPPC_Post_Filters {

    private static $instance;       
    public $wppc_setting = array();

    public static function get_instance() {
            
        if ( null == self::$instance ) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct() {
        
        if(empty($wppc_setting)){
            $this->wppc_setting = get_option( 'wppc_setting');     
        }

        if( isset($this->wppc_setting['filter_post_option']['author']) && $this->wppc_setting['filter_post_option']['author'] == 1 ) {

            add_action('pre_get_posts', array($this, 'author_filters_query_action'));
            add_action('restrict_manage_posts', array($this, 'author_filters_admin_dropdown'));

        }
        
    }

    public function author_filters_admin_dropdown() {

        $types_array = $this->filters_post_types();
        
        global $post_type;

        if (in_array($post_type, $types_array)) {
            
            $user_args = array(
                'show_option_all'  => 'All Users',
                'orderby'          => 'display_name',
                'order'            => 'ASC',
                'name'             => 'author_admin_filter',
                'who'              => 'authors',
                'include_selected' => true
            );
            
            if (isset($_GET['author_admin_filter'])) {                
                $user_args['selected'] = intval($_GET['author_admin_filter']);
            }

            wp_dropdown_users($user_args); 
        }
    }

    public function author_filters_query_action($query) {

        global $post_type, $pagenow;
        
        $types_array = $this->filters_post_types();        

        if ('edit.php' === $pagenow && (in_array($post_type, $types_array))) {

            if (isset($_GET['author_admin_filter'])) {
                
                $author_id = intval($_GET['author_admin_filter']);
                
                if (0 !== $author_id) {
                    $query->query_vars['author'] = $author_id;
                }
            }
        }
    }

    public function filters_post_types() {

        $args = array(
            'public' => true,
            '_builtin' => true
        );

        $output = ''; 
        $operator = 'or'; 

        $post_types_arrays = get_post_types($args, $output, $operator); 

        $types_array = array(); 

        $exclude_post_types = array('attachment', 'revision', 'nav_menu_item'); 

        foreach ($post_types_arrays as $post_types_aray) { 
            
            if (!in_array($post_types_aray->name, $exclude_post_types)) {
            
                array_push($types_array, $post_types_aray->name);
            
            }
            
        } 

        return $types_array;
    }
            
}

if(class_exists('WPPC_Post_Filters')){
    WPPC_Post_Filters::get_instance();
}