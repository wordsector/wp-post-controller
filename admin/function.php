<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

function wppc_set_post_view() {

    $key     = 'wppc_post_views_count';
    $post_id = get_the_ID();
    $count = (int) get_post_meta( $post_id, $key, true );    
    $count++;
    update_post_meta( $post_id, $key, $count );

}

add_filter('the_content', 'wppc_filter_the_content');

function wppc_filter_the_content($content){

        $post_type = get_post_type();

        $wppc_settings = get_option('wppc_settings');

        if(isset($wppc_settings['views_enable_on'][$post_type])){

            wppc_set_post_view();

            $wppc_settings = get_option('wppc_settings');
            $position = '';
            $views    = 0;
            $count    = get_post_meta( get_the_ID(), 'wppc_post_views_count', true );
            
            if($count > 0){
                $views = '<p>Post Views : '.esc_html($count). "</p>";
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