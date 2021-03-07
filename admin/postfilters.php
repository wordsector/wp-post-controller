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

            add_action('restrict_manage_posts', array($this, 'author_filters_admin_dropdown'),10,2);
            add_action('pre_get_posts', array($this, 'author_filters_query_action'));            

        }

        if( isset($this->wppc_setting['filter_post_option']['tag']) && $this->wppc_setting['filter_post_option']['tag'] == 1 ) {

            add_action( 'restrict_manage_posts' , array($this, 'tag_filters_admin_dropdown'),10,2);
            add_filter( 'pre_get_posts', array($this, 'tag_filters_query_action') , 10);

        }

        if( isset($this->wppc_setting['filter_post_option']['date_range']) && $this->wppc_setting['filter_post_option']['date_range'] == 1 ) {

            add_action( 'restrict_manage_posts' , array($this, 'date_range_filters_admin_form'),10,2);
            add_filter( 'pre_get_posts', array($this, 'date_range_filters_query_action') , 10);

            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        }
                
    }

    public function enqueue_scripts($hook){
        
        if( $hook == 'edit.php' ) {

            wp_enqueue_style( 'jquery-ui', WPPC_PLUGIN_URL . 'public/css/backend/jquery-ui.min.css', false , WPPC_VERSION );               
            wp_enqueue_style( 'wppc-filter-css', WPPC_PLUGIN_URL . 'public/css/backend/filter.css', false , WPPC_VERSION );               

            wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_enqueue_script( 'wppc-filter-js', WPPC_PLUGIN_URL . 'public/js/backend/filter.js', array('jquery') , WPPC_VERSION );               
        }
        
    }

    public function date_range_filters_query_action($query){        
        
        //we want to modify the query for the targeted custom post.
        if( 'post' !== $query->query['post_type'] ){
            return $query;
        }

		if ( is_admin() && $query->is_main_query() && ( ! empty( $_GET['wppc_filter_date_from'] ) || ! empty( $_GET['wppc_filter_date_to'] ) )) {
 
			$query->set(
				'date_query', 
                    array(
                        'after'     => sanitize_text_field( $_GET['wppc_filter_date_from'] ), 
                        'before'    => sanitize_text_field( $_GET['wppc_filter_date_to'] ),
                        'inclusive' => true,
                        'column'    => 'post_date' 
                    )
			);
 
		}
 
		return $query;
    }

    public function date_range_filters_admin_form ( $post_type, $which ){

        if('post' !== $post_type){
            return; 
        }

        $from = ( isset( $_GET['wppc_filter_date_from'] ) && $_GET['wppc_filter_date_from'] ) ? $_GET['wppc_filter_date_from'] : '';
		$to = ( isset( $_GET['wppc_filter_date_to'] ) && $_GET['wppc_filter_date_to'] ) ? $_GET['wppc_filter_date_to'] : '';                
 
		echo '<input type="text" name="wppc_filter_date_from" placeholder="Date From" value="' . esc_attr( $from ) . '" />';
        echo '<input type="text" name="wppc_filter_date_to" placeholder="Date To" value="' . esc_attr( $to ) . '" />';
        
    }

    public function tag_filters_query_action( $query ){

        //modify the query only if it is admin and main query.
        if( !( is_admin() AND $query->is_main_query() ) ){ 
            return $query;
        }
        //we want to modify the query for the targeted custom post.
        if( 'post' !== $query->query['post_type'] ){
            return $query;
        }

        if( isset($_REQUEST['tag_id']) &&  0 != $_REQUEST['tag_id']){
            $term =  intval($_REQUEST['tag_id']);
            $taxonomy_slug = 'post_tag';
            $query->query_vars['tax_query'] = array(
              array(
                  'taxonomy'  => $taxonomy_slug,
                  'field'     => 'ID',
                  'terms'     => array($term)
              )
            );
        }

        return $query;

    }

    public function tag_filters_admin_dropdown ( $post_type, $which ){

            if('post' !== $post_type){
                return; 
            }

            $taxonomy_slug = 'post_tag';
            $taxonomy = get_taxonomy($taxonomy_slug);
            $selected = '';
            $request_attr = 'tag_id'; //this will show up in the url
            if ( isset($_REQUEST[$request_attr] ) ) {
                $selected = $_REQUEST[$request_attr]; //in case the current page is already filtered
            }
            wp_dropdown_categories(array(
                'show_option_all' =>  __("Show All {$taxonomy->label}"),
                'taxonomy'        =>  $taxonomy_slug,
                'name'            =>  $request_attr,
                'orderby'         =>  'name',
                'selected'        =>  $selected,
                'hierarchical'    =>  true,
                'depth'           =>  3,
                'show_count'      =>  true, // Show number of post in parent term
                'hide_empty'      =>  false, // Don't show posts w/o terms
            ));

    }

    public function author_filters_admin_dropdown($post_type, $which) {

        if('post' !== $post_type){
            return; 
        }

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