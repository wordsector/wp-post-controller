<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('admin_init', 'setup_database');

function setup_database(){

    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $status = get_option('wppc_database_setup_done');        

    if($status !='done'){
        install_tables();
        update_option('wppc_database_setup_done', 'done'); 
    }

}

function install_tables(){

    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    
    global $wpdb;                

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    $charset_collate = $engine = '';
    
    if(!empty($wpdb->charset)) {
        $charset_collate .= " DEFAULT CHARACTER SET {$wpdb->charset}";
    } 
    if($wpdb->has_cap('collation') AND !empty($wpdb->collate)) {
        $charset_collate .= " COLLATE {$wpdb->collate}";
    }

    $found_engine = $wpdb->get_var("SELECT ENGINE FROM `information_schema`.`TABLES` WHERE `TABLE_SCHEMA` = '".DB_NAME."' AND `TABLE_NAME` = '{$wpdb->prefix}posts';");
        
    if(strtolower($found_engine) == 'innodb') {
        $engine = ' ENGINE=InnoDB';
    }

    $found_tables = $wpdb->get_col("SHOW TABLES LIKE '{$wpdb->prefix}wppc%';");	
                    
    if(!in_array("{$wpdb->prefix}wppc_post_views", $found_tables)) {
            
        dbDelta("CREATE TABLE `{$wpdb->prefix}wppc_post_views` (
            `post_id` bigint unsigned NOT NULL,
            `post_type` varchar(50) NOT NULL,			
            `count_type` varchar(20) NOT NULL,
            `count_period` varchar(8) NOT NULL,
            `count_number` bigint unsigned NOT NULL,                            
            PRIMARY KEY  (`post_id`, post_type, `count_type`, `count_period`),
            INDEX `post_id_post_type_count_type_count_period_count_number` (`post_id`, `post_type`, `count_type`, `count_period`, `count_number`)                
        ) ".$charset_collate.$engine.";");
                    
    }

}