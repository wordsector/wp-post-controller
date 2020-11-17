<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_enqueue_scripts', 'wppc_enqueue_scripts' );

function wppc_enqueue_scripts( $hook ) {                                        
    
        wp_enqueue_style( 'wppc-common-admin-css', WPPC_PLUGIN_URL . 'public/css/backend/common-admin.css', false , WPPC_VERSION );   
}

add_filter( 'manage_posts_columns', 'wppc_posts_column_views' );
add_action( 'manage_posts_custom_column', 'wppc_posts_custom_column_views' );

add_filter( 'manage_pages_columns', 'wppc_pages_column_views' );
add_action( 'manage_pages_custom_column', 'wppc_pages_custom_column_views' );

function wppc_posts_custom_column_views( $column ) {

    if ( $column === 'wppc_post_views') {
        
        $count = get_post_meta( get_the_ID(), 'wppc_post_views_count', true );

        if($count > 0){
            echo esc_html($count. " views");
        }else{
            echo "0";
        }
        
    }
}

function wppc_posts_column_views( $columns ) {

    $wppc_settings = get_option('wppc_settings');

    if( (isset($wppc_settings['views_column']) && $wppc_settings['views_column'] == 'enable') && (isset($wppc_settings['views_enable_on']['post']) && $wppc_settings['views_enable_on']['post'] == 1) ){
        $columns['wppc_post_views'] = 'Views';
    }
    
    return $columns;
}

function wppc_pages_custom_column_views( $column ) {

    if ( $column === 'wppc_post_views') {
        
        $count = get_post_meta( get_the_ID(), 'wppc_post_views_count', true );

        if($count > 0){
            echo esc_html($count. " views");
        }else{
            echo "0";
        }
        
    }
}

function wppc_pages_column_views( $columns ) {

    $wppc_settings = get_option('wppc_settings');
    
    if( (isset($wppc_settings['views_column']) && $wppc_settings['views_column'] == 'enable') && (isset($wppc_settings['views_enable_on']['page']) && $wppc_settings['views_enable_on']['page'] == 1) ){
        $columns['wppc_post_views'] = 'Views';
    }
    
    return $columns;
}