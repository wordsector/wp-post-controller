<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('admin_menu', 'wppc_setting_menu');

function wppc_setting_menu() {

	//create new top-level menu
    add_menu_page(
        esc_html__( 'WP Post Controller Settings', 'wp-post-controller' ), 
        esc_html__( 'WP Post Controller' , 'wp-post-controller' ), 
        'manage_options',      
        'wp_post_controller_setting',
        'wppc_settings_page_callback' , 
         WPPC_PLUGIN_URL.'public/images/post-controller.png' 
    );

	//call register settings function
    add_action( 'admin_init', 'wppc_register_settings' );
    
}

function wppc_register_settings() {
	//register our settings
    register_setting( 'wppc-settings-group', 'wppc_settings' );
    	
}

function wppc_settings_page_callback() {

if ( ! current_user_can( 'manage_options' ) ) {
        return;
}

global $wppc_settings;  
$wppc_settings = get_option('wppc_settings');

?>
<div class="wrap">
<h1> <?php echo esc_html__( 'WP Post Controller', 'wp-post-controller' ) ?></h1>

<form method="post" action="options.php">
    <?php settings_fields( 'wppc-settings-group' ); ?>
    <?php do_settings_sections( 'wppc-settings-group' ); ?>

<h3><?php echo esc_html__( 'Backend Settings', 'wp-post-controller' ) ?></h3>

    <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php echo esc_html__( 'Views Column', 'wp-post-controller' ) ?></th>
        <td>
        <input type="radio" id="enable_views_column" name="wppc_settings[views_column]" value="enable" <?php echo (isset($wppc_settings['views_column']) && $wppc_settings['views_column'] == 'enable' ? 'checked' : '') ?> > <?php echo esc_html__( 'Enable', 'wp-post-controller' ) ?>
        <input type="radio" id="disable_views_column" name="wppc_settings[views_column]" value="disable" <?php echo (isset($wppc_settings['views_column']) && $wppc_settings['views_column'] == 'disable' ? 'checked' : '') ?> > <?php echo esc_html__( 'Disable', 'wp-post-controller' ) ?>  
        </td>
        </tr>        
        <tr valign="top">
        <th scope="row"><?php echo esc_html__( 'Enable On', 'wp-post-controller' ) ?></th>
        <td>
        <input type="checkbox" name="wppc_settings[views_enable_on][post]" value="1" <?php echo ( isset($wppc_settings['views_enable_on']['post']) ? 'checked': '' ); ?> /> <?php echo esc_html__( 'Post', 'wp-post-controller' ) ?>
        <input type="checkbox" name="wppc_settings[views_enable_on][page]" value="1" <?php echo ( isset($wppc_settings['views_enable_on']['page']) ? 'checked': '' ); ?> /> <?php echo esc_html__( 'Page', 'wp-post-controller' ) ?>  
        </td>
        </tr>                
    </table>

    <h3><?php echo esc_html__( 'Frontend Settings', 'wp-post-controller' ) ?></h3>

    <table class="form-table">        
        
        <tr valign="top">
        <th scope="row"><?php echo esc_html__( 'Position', 'wp-post-controller' ) ?></th>
        <td>

        <select name="wppc_settings[views_position]">
            <option value="before_the_content" <?php echo (isset($wppc_settings['views_position']) && $wppc_settings['views_position'] == 'before_the_content' ? 'selected' : '' ); ?> ><?php echo esc_html__( 'Before The Content', 'wp-post-controller' ) ?></option>
            <option value="after_the_content" <?php echo (isset($wppc_settings['views_position']) && $wppc_settings['views_position'] == 'after_the_content' ? 'selected' : '' ); ?> ><?php echo esc_html__( 'After The Content', 'wp-post-controller' ) ?></option>
        </select>
  
        </td>
        </tr>

    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>