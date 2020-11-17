<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

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