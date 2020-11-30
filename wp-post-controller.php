<?php
/*
Plugin Name: WP Post Controller
Description: All in one solution for posts. Get Multiple features in one place
Version: 1.3
Text Domain: wp-post-controller
Domain Path: /languages
Author: WordSector
Author URI: http://wordsector.com
Donate link: https://www.paypal.me/wordsector
License: GPL2
*/
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

define('WPPC_VERSION', '1.3');
define('WPPC_DIR_NAME_FILE', __FILE__ );
define('WPPC_DIR_URI', plugin_dir_url(__FILE__));
define('WPPC_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ));

define('WPPC_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('WPPC_PLUGIN_BASENAME', plugin_basename(__FILE__));


register_activation_hook( __FILE__, 'wppc_activation_action' );

require_once WPPC_PLUGIN_DIR_PATH .'admin/common.php';
require_once WPPC_PLUGIN_DIR_PATH .'admin/setting.php';
require_once WPPC_PLUGIN_DIR_PATH .'admin/dbmapper.php';
require_once WPPC_PLUGIN_DIR_PATH .'admin/postviews.php';

require_once WPPC_PLUGIN_DIR_PATH .'admin/metaboxes/mpostviews.php';