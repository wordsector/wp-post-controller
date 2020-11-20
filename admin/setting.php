<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class WPPC_Admin_Setting {

    private static $instance;   

    public static function get_instance() {
            
        if ( null == self::$instance ) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct() {

        add_action( 'admin_menu', array($this, 'setting_menu') );                            
        add_action( 'admin_init', array($this, 'register_setting') );
        add_action( 'admin_enqueue_scripts', array($this,'enqueue_scripts') );
        add_action( 'wp_ajax_wppc_send_query', array($this, 'send_query') );
        add_action( 'admin_footer', array($this, 'footer_content') );
                             
    }
    public function enqueue_scripts($hook){

        $local = array(     
            'current_url'                  => wppc_current_url(),             
            'ajax_url'                     => admin_url( 'admin-ajax.php' ),            
            'wppc_nonce'                   => wp_create_nonce('wppc_check_nonce'),                          
            'page_now'                     => $hook            
        );

        $local = apply_filters('wppc_filter_enqueue_data',$local,'wppc_local');

        wp_register_script( 'wppc-setting-js', WPPC_PLUGIN_URL . 'public/js/backend/setting.js', array('jquery'), WPPC_VERSION , true );                        
        wp_localize_script( 'wppc-setting-js', 'wppc_local', $local );        
        wp_enqueue_script( 'wppc-setting-js' );

        wp_enqueue_style( 'wppc-setting-style', WPPC_PLUGIN_URL . 'public/css/backend/setting.css', false , WPPC_VERSION );

    }

    public function setting_menu(){

        add_menu_page(
            wppc_escape_html('WP Post Controller Settings'), 
            wppc_escape_html('WP Post Controller'), 
            'manage_options',      
            'wp_post_controller_setting',
            array($this, 'setting_interface') , 
             WPPC_PLUGIN_URL.'public/images/post-controller.png' 
        );

    }

    public function register_setting(){

        register_setting( 'wppc-settings-group', 'wppc_settings' );

        add_settings_section('wppc_post_views_section', __return_false(), '__return_false', 'wppc_post_views_section');

        add_settings_field(
            'post_views_settings',								
            '',
            array($this, 'post_views_tab_callback'),					
            'wppc_post_views_section',						
            'wppc_post_views_section',
            array('class' => 'wppc-tab-first-tr')						
        );

        add_settings_section('wppc_support_section', __return_false(), '__return_false', 'wppc_support_section');

        add_settings_field(
            'support_settings',
            '',		
            array($this, 'support_tab_callback'),
            'wppc_support_section',
            'wppc_support_section',
            array('class' => 'wppc-tab-first-tr')						
        );

    }

    public function send_query(){
        
         if ( ! isset( $_POST['wppc_nonce'] ) ){
            return; 
         }
        
         if ( !wp_verify_nonce( $_POST['wppc_nonce'], 'wppc_check_nonce' ) ){
            return;  
         }            
         
         $message        = sanitize_textarea_field($_POST['message']); 
         $email          = sanitize_textarea_field($_POST['email']);          
         $user           = wp_get_current_user();
                           
         $message = '<p>'.$message.'</p><br><br>'                 
                 . '<br><br>'.'Query From Plugin Dashboard';
         
         if($user){
             
             $user_data  = $user->data;        
             $user_email = $user_data->user_email;     
             
             if($email){
                 $user_email = $email;
             }            
             
             $sendto    = 'wordsector@gmail.com';
             $subject   = "WP Post Controller Customer Query";
             
             $headers[] = 'Content-Type: text/html; charset=UTF-8';
             $headers[] = 'From: '. esc_attr($user_email);            
             $headers[] = 'Reply-To: ' . esc_attr($user_email);
                                  
             $sent = wp_mail($sendto, $subject, $message, $headers); 
 
             if($sent){
 
                echo json_encode(array('status'=>'success'));  
 
             }else{
 
                echo json_encode(array('status'=>'error'));            
 
             }
             
         }
                         
         wp_die();    

    }

    public function setting_interface(){
	            
        if ( ! current_user_can( 'manage_options' ) ) {
                return;
        }
            
        if ( isset( $_GET['settings-updated'] ) ) {							                                                 
            settings_errors();               
        }
                   
        $setting_tab = wppc_selected_tab('post_views', array('post_views', 'support'));            
    
        ?>
        <div class="wppc-setting-container">
        <div class="wrap">	
            <h1 class="wp-heading-inline"> <?php echo wppc_escape_html( 'WP Post Controller' ); ?></h1><br>		
        <div>
        <h2 class="nav-tab-wrapper wppc-tabs">                    
            <?php			    
                        echo '<a href="' . esc_url(wppc_selected_tab_url('post_views')) . '" class="nav-tab ' . esc_attr( $setting_tab == 'post_views' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . wppc_escape_html('Post Views') . '</a>';                                                           
                        echo '<a href="' . esc_url(wppc_selected_tab_url('support')) . '" class="nav-tab ' . esc_attr( $setting_tab == 'support' ? 'nav-tab-active' : '') . '"><span class=""></span> ' . wppc_escape_html('Support') . '</a>';                                                                                                                                                                                                                                                                  
            ?>                    
        </h2>                                                            
            </div>
                
            <form action="<?php echo admin_url("options.php") ?>" method="post" enctype="multipart/form-data" class="wppc-setting-form">		
            <div class="form-wrap wppc-setting-form-wrap">
                <?php
            
                    settings_fields( 'wppc-settings-group' );										
            
                    echo "<div class='wppc-post_views' ".( $setting_tab != 'post_views' ? 'style="display:none;"' : '').">";                                                            
                    
                        do_settings_sections( 'wppc_post_views_section' );	
                    echo "</div>";
                                                                                
                    echo "<div class='wppc-support' ".( $setting_tab != 'support' ? 'style="display:none;"' : '').">";
                                
                        do_settings_sections( 'wppc_support_section' );	
                    echo "</div>";
                                                                        
                ?>
            </div>
            <div class="button-wrapper">
                <?php                
                    submit_button( wppc_escape_html('Save Settings') );
                ?>
            </div>                  
        </form>
    </div>    
    </div>    
    <?php
    }    

    public function support_tab_callback(){

        ?>
            <div class="wppc_support_tab_content">                   
                <ul>
                    <li>
                       <input type="text" id="wppc_query_email" name="wppc_query_email" placeholder="<?php echo wppc_escape_html('Enter a valid email'); ?>">
                    </li>
                    <li>                    
                        <div><textarea rows="8" cols="80" id="wppc_query_message" name="wppc_query_message" placeholder="<?php echo wppc_escape_html('Write your query, suggestion or requested features.'); ?>"></textarea></div>
                        <span class="wppc-query-success wppc_hide_element"><?php echo wppc_escape_html('Thank you for contacting us, Please wait we will get back to you shortly'); ?></span>
                        <span class="wppc-query-error wppc_hide_element"><?php echo wppc_escape_html('Something went wrong. Please contact via email at wordsector@gmail.com'); ?></span>
                    </li>                
                    <li>
                        <button class="button wppc-send-support-request"><?php echo wppc_escape_html('Send'); ?></button>
                    </li>
                </ul> 

                <strong><?php echo wppc_escape_html('Any query, suggestion or requested features are welcome. You can contact us via email at') ?> <a href="mailto:wordsector@gmail.com">wordsector@gmail.com</a></strong>                              
            </div>
        <?php

    }
    

    public function post_views_tab_callback(){

        global $wppc_settings;  
        $wppc_settings = get_option('wppc_settings');

        ?>

        <div class="wrap">    
        <h3><?php echo wppc_escape_html('Backend Setting'); ?></h3>

        <table class="form-table">                
            <tr valign="top">
            <th scope="row"><?php echo wppc_escape_html('Enable On'); ?></th>
            <td>

                <?php
                
                $post_types = wppc_get_custom_post_types();

                if($post_types){

                    foreach ($post_types as $key => $value) {
                        
                        echo '  <input class="wppc_pv_post_type" type="checkbox" name="wppc_settings[views_enable_on]['.esc_attr($key).']" value="1" '.(isset($wppc_settings["views_enable_on"][$key]) ? "checked": "").' /> ' . ucwords(wppc_escape_html($value));

                    }

                }

                ?>                
                <p class="wppc-description"> <?php echo wppc_escape_html('Select the post type whereever you want to enable a post view counter'); ?> </p>
            </td>
            </tr>                

            <tr valign="top">
            <th scope="row"><?php echo wppc_escape_html('Views Column'); ?></th>
            <td>
                <input type="radio" id="enable_views_column" name="wppc_settings[views_column]" value="enable" <?php echo (isset($wppc_settings['views_column']) && $wppc_settings['views_column'] == 'enable' ? 'checked' : '') ?> > <?php echo wppc_escape_html('Enable'); ?>
                <input type="radio" id="disable_views_column" name="wppc_settings[views_column]" value="disable" <?php echo (isset($wppc_settings['views_column']) && $wppc_settings['views_column'] == 'disable' ? 'checked' : '') ?> > <?php echo wppc_escape_html('Disable'); ?>
                <p class="wppc-description"><?php echo wppc_escape_html('Enables a post view count column in above selected post type List'); ?></p>
            </td>
            </tr>
        </table>

        <h3><?php echo wppc_escape_html('Frontend Setting'); ?></h3>

        <table class="form-table">                
            <tr valign="top">
            <th scope="row"><?php echo wppc_escape_html('Position'); ?></th>
            <td>
                <select name="wppc_settings[views_position]">
                    <option value=""><?php echo wppc_escape_html('Select Position'); ?></option>
                    <option value="before_the_content" <?php echo (isset($wppc_settings['views_position']) && $wppc_settings['views_position'] == 'before_the_content' ? 'selected' : '' ); ?> ><?php echo wppc_escape_html('Before The Content'); ?></option>
                    <option value="after_the_content" <?php echo (isset($wppc_settings['views_position']) && $wppc_settings['views_position'] == 'after_the_content' ? 'selected' : '' ); ?> ><?php echo wppc_escape_html('After The Content'); ?></option>
                </select>
                <p class="wppc-description"><?php echo wppc_escape_html('Show particular post views to your users. If You do not wish to show to users, Don\'t select position'); ?></p>
            </td>
            </tr>
        </table>     
        </div>

        <?php        
    }

    public function footer_content(){
    
        $screen = get_current_screen();
        if($screen->id == 'toplevel_page_wp_post_controller_setting'){
            echo '<div class="wppc-footer-message"><p class="wppc-description">'.wppc_escape_html('Thank you for using WP Post Controller. Please Rate Us.').' <a target="_blank" href="https://wordpress.org/plugins/wp-post-controller/#reviews"><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span></a> '.wppc_escape_html('It will help us to server you better').'</p> </div>';
        }        
    
    }

}

if(class_exists('WPPC_Admin_Setting')){
    WPPC_Admin_Setting::get_instance();
}