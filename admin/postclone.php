<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class WPPC_Post_Clone {

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
        
        if(isset($this->wppc_setting['clone_enable_on']) && !empty($this->wppc_setting['clone_enable_on'])){

            $clone_enabled_on = $this->wppc_setting['clone_enable_on'];

            foreach($clone_enabled_on as $key => $val) {

                if($val == 1) {
                    add_filter( $key.'_row_actions', array( $this, 'add_clone_link' ), 10, 2 );
                }

            }

        }
                
        add_action( 'admin_action_wppc_clone_post', array( $this, 'clone_post_action' ) );

    }

    public function clone_post_action(){

        global $wpdb;

        if ( ! (isset( $_GET['post']) || ( isset( $_REQUEST['action']) && 'wppc_clone_post' == $_REQUEST['action'] ) 
        ) ) {
            wp_die( wppc_escape_html('No post to be cloned!'));
        }
        
        if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'wppc_clone_nonce' ) ) {
            return;
        }
        
        $id = (int) ( isset( $_GET['post']) ? absint($_GET['post']) : absint($_REQUEST['post']));

        if ( $id ) {

            $post = get_post( $id );
            
            if ( isset( $post ) && $post != null ) {

                // args for new post
                $args = array(
                    'comment_status' => $post->comment_status,
                    'ping_status'    => $post->ping_status,
                    'post_author'    => $post->post_author,
                    'post_content'   => $post->post_content,
                    'post_excerpt'   => $post->post_excerpt,
                    'post_name'      => $post->post_name,
                    'post_parent'    => $post->post_parent,
                    'post_password'  => $post->post_password,
                    'post_status'    => $post->post_status,
                    'post_title'     => $post->post_title,
                    'post_type'      => $post->post_type,
                    'to_ping'        => $post->to_ping,
                    'menu_order'     => $post->menu_order
                );
                                
                $new_post_id = wp_insert_post( $args );

                $taxonomies = get_object_taxonomies( $post->post_type );

                // add the taxonomy terms to the new post
                foreach ( $taxonomies as $taxonomy ) {
                    $post_terms = wp_get_object_terms( $id, $taxonomy, array( 'fields' => 'slugs' ) );
                    wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
                }

            }
                
            $post_metas = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$id");

            if ( count( $post_metas )!=0 ) {
            
            $sql_query = "INSERT INTO $wpdb->postmeta ( post_id, meta_key, meta_value ) ";
            
            foreach ( $post_metas as $post_meta ) {

                $meta_key = $post_metas->meta_key;

                if( $meta_key == '_wp_old_slug' ) continue;
                    $meta_value = addslashes( $post_metas->meta_value);
                    $sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";

                }

                $sql_query.= implode(" UNION ALL ", $sql_query_sel);
                $wpdb->query( $sql_query );

            }
        
            $posttype = get_post_type( $id );
            wp_redirect( admin_url( 'edit.php?post_type=' . $posttype ) );
            exit;

        } else {
                wp_die( wppc_escape_html('Sorry, Post id is not there') );
        }

    }

    public function add_clone_link( $actions, $id ) {

        global $post;
        
        if ( ! current_user_can( 'manage_options' ) ) {
            return $actions;
        }
        
        $clone_link = $this->get_clone_post_link( $post->ID );
        
        if(!empty($clone_link)){

            $actions['wppc_clone_post'] = '<a href="' . esc_url($clone_link) 
            . '" title="'
            . esc_attr( wppc_escape_html('Make a duplicate of this post') ) 
            . '">' . wppc_escape_html('Clone') . '</a>';

        }
                
        return $actions;
    }

    public function get_clone_post_link( $id = 0 ) {

		if ( ! $post = get_post( $id ) ) {
			return NULL;
		}

		$post_type_object = get_post_type_object( $post->post_type );
		if ( ! $post_type_object ) {
			return NULL;
		}
		
		$clone_link = admin_url( 'admin.php?post=' . $post->ID . '&action=wppc_clone_post' );

		return apply_filters(
			'wppc_get_clone_post_link',
			wp_nonce_url( $clone_link, "wppc_clone_nonce" ),
			$post->ID
		);
	}
            
}

if(class_exists('WPPC_Post_Clone')){
    WPPC_Post_Clone::get_instance();
}