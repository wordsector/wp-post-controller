<?php
/*
Plugin Name: WP Post Controller
Description: Plugin to control and add more advanced features in posts
Version: 1.0
Text Domain: wp-post-controller
Domain Path: /languages
Author: WordSector
Author URI: http://wordsector.com
Donate link: https://www.paypal.me/wordsector
License: GPL2
*/
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

define('WPPC_VERSION', '1.0');
define('WPPC_DIR_NAME_FILE', __FILE__ );
define('WPPC_DIR_URI', plugin_dir_url(__FILE__));
define('WPPC_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ));

define('WPPC_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('WPPC_PLUGIN_BASENAME', plugin_basename(__FILE__));


require_once WPPC_PLUGIN_DIR_PATH .'admin/function.php';
require_once WPPC_PLUGIN_DIR_PATH .'admin/setting.php';
require_once WPPC_PLUGIN_DIR_PATH .'admin/setup.php';