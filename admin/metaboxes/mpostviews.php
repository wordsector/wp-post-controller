<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register a meta box using a class.
 */
class WPPC_Post_Views_Meta_Box {

    private $query = null;
 
    /**
     * Constructor.
     */
    public function __construct() {
        if ( is_admin() ) {

            if($this->query == null){
                require_once WPPC_PLUGIN_DIR_PATH .'admin/dbquery.php';
                $this->query = new WPPC_Db_Query();
            }

            add_action( 'load-post.php',     array( $this, 'init_metabox' ) );
            add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
        }
 
    }
 
    /**
     * Meta box initialization.
     */
    public function init_metabox() {
        add_action( 'add_meta_boxes', array( $this, 'add_metabox'  )        );
        add_action( 'save_post',      array( $this, 'save_metabox' ), 10, 2 );
    }
 
    /**
     * Adds the meta box.
     */
    public function add_metabox() {
        add_meta_box(
            'wppc-post-views-meta-box',
            wppc_escape_html('Post Views'),
            array( $this, 'render_metabox' ),
            'post',
            'side',
            'low'
        );
 
    }
 
    /**
     * Renders the meta box.
     */
    public function render_metabox( $post ) {
        // Add nonce for security and authentication.
        wp_nonce_field( 'wppc_nonce_action', 'wppc_nonce' );
        
        $count    = $this->query->get_total_count_by_post($post->ID);

        ?> 
        
        <div>
            <div class="wppc-rpv-metabox">
                <span class="wppc-rpv-c-span"><?php echo wppc_escape_html('View :') ?> </span> <span class="wppc-rpv-right-span"><?php echo esc_html($count); ?></span> 
            </div>
            <div class="wppc-rpv-metabox">
                <label for="wppc_reset_post_view"> <span class="wppc-rpv-c-span"><?php echo wppc_escape_html('Reset :') ?></span><input class="wppc-rpv-right-span" id="wppc_reset_post_view" type="checkbox" name="wppc_reset_post_view" value="1" /></label>
            </div>
        </div>

        <?php

    }
 
    /**
     * Handles saving the meta box.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @return null
     */
    public function save_metabox( $post_id, $post ) {
        // Add nonce for security and authentication.
        $nonce_name   = isset( $_POST['wppc_nonce'] ) ? $_POST['wppc_nonce'] : '';
        $nonce_action = 'wppc_nonce_action';
 
        // Check if nonce is valid.
        if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
            return;
        }
 
        // Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
 
        // Check if not an autosave.
        if ( wp_is_post_autosave( $post_id ) ) {
            return;
        }
 
        // Check if not a revision.
        if ( wp_is_post_revision( $post_id ) ) {
            return;
        }

        if(isset($_POST['wppc_reset_post_view'])){
            $result = $this->query->delete_post_views_by_post_id($post_id);
        }

    }
}
 
new WPPC_Post_Views_Meta_Box();