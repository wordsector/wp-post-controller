<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class WPPC_Post_Views {

    private static $instance;   
    private $query = null;

    public static function get_instance() {
            
        if ( null == self::$instance ) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct() {             

        if($this->query == null){

            require_once WPPC_PLUGIN_DIR_PATH .'admin/dbquery.php';
            $this->query = new WPPC_Db_Query();

        }        
        
        add_action( 'wp_ajax_nopriv_wppc_set_post_views_ajax', array($this, 'set_post_views_via_ajax') );  
        add_action( 'wp_ajax_wppc_set_post_views_ajax', array($this, 'set_post_views_via_ajax') );  
        add_filter( 'the_content', array($this, 'filter_the_content' ));
        add_filter( 'manage_posts_columns', array($this, 'posts_column_views' ));
        add_action( 'manage_posts_custom_column', array($this, 'posts_custom_column_views' ));
        add_filter( 'manage_pages_columns', array($this, 'pages_column_views' ));
        add_action( 'manage_pages_custom_column', array($this, 'pages_custom_column_views' ));
        add_action( 'wp_enqueue_scripts', array($this, 'frontend_script_enqueue' ));

    }

    public function set_post_views_via_ajax(){

         if(!wppc_validate_nonce($_POST)){
            return;
         }
        
         if(isset($_POST['post_id'])){

            $post_id = $_POST['post_id'];
            $this->query->set_post_view($post_id);

         }
         
         wp_die();
                         
    }
    public function frontend_script_enqueue($hook){
        
        global $post;        

        $wppc_settings = get_option('wppc_settings');

        if($wppc_settings['counter_mode'] == 'ajax'){

            $local = array(     
                'current_url'                  => wppc_current_url(),             
                'ajax_url'                     => admin_url( 'admin-ajax.php' ),            
                'wppc_nonce'                   => wp_create_nonce('wppc_check_nonce'),
                'post_id'                      => get_the_ID()
            );            
    
            $local = apply_filters('wppc_filter_post_view_frontend_data',$local,'wppc_post_views_local');
    
            wp_register_script( 'wppc-post-views-front-js', WPPC_PLUGIN_URL . 'public/js/frontend/post-views.js', array('jquery'), WPPC_VERSION , true );                        
            wp_localize_script( 'wppc-post-views-front-js', 'wppc_post_views_local', $local );        
            wp_enqueue_script(  'wppc-post-views-front-js' );

        }        

    }
    public function posts_custom_column_views( $column ) {

        if ( $column === 'wppc_post_views') {
            
            $count = $this->query->get_total_count_by_post(get_the_ID());
    
            if($count > 0){
                echo esc_html($count);
            }else{
                echo "0";
            }
            
        }
    }
    
    public function posts_column_views( $columns ) {
    
        $wppc_settings = get_option('wppc_settings');
    
        $post_type = get_post_type();
    
        if( (isset($wppc_settings['views_column']) && $wppc_settings['views_column'] == 'enable') && (isset($wppc_settings['views_enable_on'][$post_type]) && $wppc_settings['views_enable_on'][$post_type] == 1) ){
            $columns['wppc_post_views'] = 'Views';
        }
        
        return $columns;
    }
    
    public function pages_custom_column_views( $column ) {
    
        if ( $column === 'wppc_post_views') {
            
            $count = $this->query->get_total_count_by_post(get_the_ID());
    
            if($count > 0){
                echo esc_html($count);
            }else{
                echo "0";
            }
            
        }
    }
    
    public function pages_column_views( $columns ) {
    
        $wppc_settings = get_option('wppc_settings');
        
        if( (isset($wppc_settings['views_column']) && $wppc_settings['views_column'] == 'enable') && (isset($wppc_settings['views_enable_on']['page']) && $wppc_settings['views_enable_on']['page'] == 1) ){
            $columns['wppc_post_views'] = 'Views';
        }
        
        return $columns;
    }
        
    public function filter_the_content($content){

        global $post;

        $post_type = get_post_type();

        $wppc_settings = get_option('wppc_settings');
        
        if( isset($wppc_settings['views_enable_on'][$post_type]) ){

            if( isset($wppc_settings['counter_mode']) && $wppc_settings['counter_mode'] == 'php' ){
                $this->query->set_post_view(get_the_ID());
            }            
            $wppc_settings = get_option('wppc_settings');
            $position = '';
            $views    = 0;
            $count    = $this->query->get_total_count_by_post(get_the_ID());
            
            if($count > 0){
                $views = '<div class="wppc-post-views-box"><span class="wppc-views-box-str">Post Views</span> : <span class="wppc-views-box-int">'.esc_html($count). "</span></div>";
                wp_enqueue_style( 'wppc-style-css', WPPC_PLUGIN_URL . 'public/css/frontend/style.css', false , WPPC_VERSION );   
            }
    
            if(isset($wppc_settings['views_position'])){
                $position = $wppc_settings['views_position'];
            }
            
            if($position == 'before_the_content'){
                $content = $views.$content;
            }
    
            if($position == 'after_the_content'){
                $content = $content.$views;
            }
            
        }
                
       return $content;

    }
        
    
}

if(class_exists('WPPC_Post_Views')){
    WPPC_Post_Views::get_instance();
}
