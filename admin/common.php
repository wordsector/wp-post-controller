<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_enqueue_scripts', 'wppc_enqueue_scripts' );

function wppc_enqueue_scripts( $hook ) {                                        
    
        wp_enqueue_style( 'wppc-common-admin-css', WPPC_PLUGIN_URL . 'public/css/backend/common-admin.css', false , WPPC_VERSION );   
}

function wppc_get_custom_post_types(){

    $post_types = array();
    $post_types = get_post_types( array( 'public' => true ), 'names' );    
    unset($post_types['attachment']);

    return $post_types;
    
}

function wppc_selected_tab( $default = '', $available = array() ) {

    $tab = isset( $_GET['tab'] ) ? sanitize_text_field(wp_unslash($_GET['tab'])) : $default;            
    if ( ! in_array( $tab, $available ) ) {
            $tab = $default;
    }

    return $tab;
}

function wppc_selected_tab_url($tab = '', $args = array()){

    $page = 'wp_post_controller_setting';

    if ( ! is_multisite() ) {
            $link = admin_url( 'admin.php?page=' . $page );
    }
    else {
            $link = admin_url( 'admin.php?page=' . $page );                    
    }

    if ( $tab ) {
            $link .= '&tab=' . $tab;
    }

    if ( $args ) {
            foreach ( $args as $arg => $value ) {
                    $link .= '&' . $arg . '=' . urlencode( $value );
            }
    }

    return esc_url($link);
}

function wppc_current_url(){
 
    $link = "http"; 
      
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
        $link = "https"; 
    } 
  
    $link .= "://"; 
    $link .= $_SERVER['HTTP_HOST']; 
    $link .= $_SERVER['REQUEST_URI']; 
      
    return $link;
}
/**
 * Function to escape all static labels used in wppc
 * since version 1.0
 */
function wppc_escape_html($string){

        return esc_html__( $string , 'wp-post-controller');
    
}

function wppc_validate_nonce($data){

        $response = true;

        if ( ! isset( $data['wppc_nonce'] ) ){
                $response = false; 
        }
        if ( !wp_verify_nonce( $data['wppc_nonce'], 'wppc_check_nonce' ) ){
                $response = false;
        }

        return $response;
}

function wppc_activation_action(){

        setup_database();

}