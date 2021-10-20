<?php
/**
 * Post class
 * 
 * @package   WP_Simple_SEO
 * @author    WP Simple SEO
 * @version   1.0.0
 * @copyright WP Simple SEO
 */
class WP_Simple_SEO_Post {

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Holds the base class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public $base;

    /**
     * Constructor
     *
     * @since 1.0.0
     */
    public function __construct() {

        // Scripts
        add_action( 'wp_simple_seo_admin_scripts_js_post', array( $this, 'enqueue_scripts' ) );
        add_filter( 'tiny_mce_before_init', array( $this, 'add_tinymce_js_events' ) );
        
        // Metaboxes
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_settings' ), 10, 2 );

        // Permalinks
        add_filter( 'get_sample_permalink', array( $this, 'remove_stop_words_from_proposed_permalink' ), 10, 4 );
        add_filter( 'name_save_pre', array( $this, 'remove_stopwords_from_permalink' ), 0 );

        // Cache
        add_action( 'wp_loaded', array( $this, 'register_delete_cache' ) );
        add_action( 'before_delete_post', array( $this, 'delete_cache' ) );
        add_action( 'save_post', array( $this, 'delete_cache' ) );

    }

    /**
     * Enqueues scripts for the Post Edit screen.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts() {

        global $post;

        // Bail if no Post object exists
        if ( ! $post || empty( $post ) ) {
            return;
        }

        // Enqueue
        wp_enqueue_script( $this->base->plugin->name . '-preview', $this->base->plugin->url . 'assets/js/preview.js', array( 'jquery' ), $this->base->plugin->version, true );
        wp_localize_script( $this->base->plugin->name . '-preview', 'wp_simple_seo_preview', array(
            'ajax'                          => admin_url( 'admin-ajax.php' ),
            'get_snippet_preview_nonce'     => wp_create_nonce( $this->base->plugin->name . '-get-snippet-preview' ),
            'id'                            => $post->ID,
            'max_meta_title_length'         => WP_Simple_SEO_Common::get_instance()->get_max_meta_title_length(),
            'max_meta_description_length'   => WP_Simple_SEO_Common::get_instance()->get_max_meta_description_length(),
        ) );

    }

    /**
     * Registers event listeners to update the Snippet Preview
     *
     * @since   1.0.0
     *
     * @param   array   $init   Init
     * @return  array           Init
     */
    public function add_tinymce_js_events( $init ) {

        $init['setup'] = "function( editor ) { 
            editor.on( 'keyup', function( editor ) { 
                wp_simple_seo_update_preview( 'post', jQuery( 'form#post' ) );
            } ); 
            editor.on( 'Change', function( editor ) { 
                wp_simple_seo_update_preview( 'post', jQuery( 'form#post' ) );
            } ); 
        }";

        return $init;

    }


    /**
     * Adds the meta editor box to the Post Editor, if it's enabled
     * for the Post Type the user is creating / editing.
     *
     * @since 1.0.0
     */
    public function add_meta_box() {

        global $post;

        // Check if the meta box is enabled on this Post Type
        $meta_box_enabled = $this->get_setting( 'meta', 'post_types[' . $post->post_type . '][single][meta_box]', $post->ID );

        // Filter the result
        $meta_box_enabled = apply_filters( 'wp_simple_seo_post_add_meta_box_enabled', $meta_box_enabled, $post );

        // Bail if the meta box isn't enabled
        if ( ! $meta_box_enabled ) {
            return;
        }

        // Get base instance
        $this->base = WP_Simple_SEO::get_instance();

        // Add meta box
        add_meta_box( $this->base->plugin->name, $this->base->plugin->displayName, array( $this, 'output_meta_box' ), $post->post_type, 'normal', 'low' );
        
        // Enqueue CSS and JS
        add_action( 'admin_enqueue_scripts', array( $this, 'scripts_css' ) );
        
    }

    /**
     * Enqueues JS and CSS if we're on a plugin screen or the welcome screen.
     *
     * @since 1.0.0
     */
    public function scripts_css() {

        WP_Simple_SEO_Admin::get_instance()->enqueue_scripts_css( 'post', get_current_screen() );

    }

    /**
     * Outputs the meta box for the Post the user is editing
     *
     * @since 1.0.0
     */
    public function output_meta_box( $post ) {

        // Get the tabs for the post screen
        $tabs = $this->get_screen_tabs();

        // Get the current tab
        // If no tab specified, get the first tab
        $tab = $this->get_current_screen_tab( $tabs );

        // Get post type and whether sitemaps are enabled
        $post_type = get_post_type_object( $post->post_type );
        $sitemap_enabled = $this->get_setting( 'sitemap', 'general[enabled]', $post->ID );
        $tags = WP_Simple_SEO_Tags::get_instance()->get_post_tags( $post_type->name );

        // Get Snippet Preview, by parsing the Title and Description
        $parser = WP_Simple_SEO_Parser::get_instance();
        $preview = array(
            'title'         => $parser->parse_tags( $this->get_setting( 'meta', 'post_types[' . $post_type->name . '][single][title]', $post->ID ), 'post_single', $post ),
            'url'           => get_permalink( $post->ID ),
            'description'   => $parser->parse_tags( $this->get_setting( 'meta', 'post_types[' . $post_type->name . '][single][description]', $post->ID ), 'post_single', $post ),
        );

        // Get maximum lengths for the meta title and description
        $max_meta_title_length = WP_Simple_SEO_Common::get_instance()->get_max_meta_title_length();
        $max_meta_description_length = WP_Simple_SEO_Common::get_instance()->get_max_meta_description_length();

        // Load view
        include_once( $this->base->plugin->folder . '/views/admin/post.php' );

    }

    /**
     * Returns an array of tabs for the Post screen
     *
     * @since 1.0.0
     *
     * @return  array               Tabs
     */
    private function get_screen_tabs() {

        // Define tabs array
        $tabs = array(
            'meta' => array(
                'name'          => 'meta',
                'label'         => __( 'WP Simple SEO', 'wp-simple-seo' ),
                'documentation' => 'https://wpsimpleseo.com/documentation/post-settings/',
            ),
        );

        // Allow addons to define tabs on existing screens
        $tabs = apply_filters( 'wp_simple_seo_post_get_screen_tabs', $tabs );

        // Return
        return $tabs;

    }

    /**
     * Gets the current admin screen tab the user is on
     *
     * @since 1.0.0
     *
     * @param   array   $tabs   Screen Tabs
     * @return  array           Tab name and label
     */
    private function get_current_screen_tab( $tabs ) {

        // If the supplied tabs are an empty array, return false
        if ( empty( $tabs ) ) {
            return false;
        }

        // If no tab defined, get the first tab name from the tabs array
        if ( ! isset( $_REQUEST['tab'] ) ) {
            foreach ( $tabs as $tab ) {
                return $tab;
            }
        }

        // Return the requested tab, if it exists
        if ( isset( $tabs[ $_REQUEST['tab'] ] ) ) {
            $tab = $tabs[ $_REQUEST['tab'] ];
            return $tab;
        } else {
            foreach ( $tabs as $tab ) {
                return $tab;
            }
        }

    }

    /**
     * Helper method to get the setting value from the Post setting, falling
     * back to the Plugin setting if no Post setting exists.
     *
     * @since 1.0.0
     *
     * @param   string    $screen   Screen
     * @param   string    $keys     Setting Key(s)
     * @param   int       $post_id  Post ID (optional)
     * @return  mixed               Value
     */
    private function get_setting( $screen, $key, $post_id = '' ) {

        return WP_Simple_SEO_Settings::get_instance()->get_setting( $screen, $key, $post_id );

    }

    /**
     * Helper method to save settings for the given screen
     *
     * @since 1.0
     *
     * @param int     $post_id     Post ID
     */
    public function save_settings( $post_id ) {

        // Get base instance
        $this->base = WP_Simple_SEO::get_instance();

        // Check if any data was submitted for this section
        if ( ! isset( $_POST ) || empty( $_POST ) || ! isset( $_POST[ $this->base->plugin->name ] ) ) {
            return;
        }
        
        // Run security checks
        // Missing nonce 
        if ( ! isset( $_POST[ $this->base->plugin->name . '_nonce' ] ) ) { 
            return false;
        }

        // Invalid nonce
        if ( ! wp_verify_nonce( $_POST[ $this->base->plugin->name . '_nonce' ], 'wp-simple-seo_post' ) ) {
            return false;
        }

        // Get Post Type
        $post_type = get_post_type( $post_id );

        // Save settings
        $post_settings = $_POST[ $this->base->plugin->name ];
        WP_Simple_SEO_Settings::get_instance()->update_post_settings( $post_id, 'meta', $post_settings, $post_type );

        // Allow Addons to save settings now
        do_action( 'wp_simple_seo_post_save_settings', $post_id, $post_type );
        
    }

    /**
     * Removes stop words from the proposed permalink that is generated when a new Page / Post
     * Title has been specified on the editor screen.
     *
     * This request is made via AJAX; remove_stopwords_from_permalink() will fire on saving the Post,
     * which is where we'll truly generate a permalink without stopwords.
     *
     * If the user subsequently changes the permalink to include stop words, we allow this,
     * as this might be intentional.
     *
     * @since   1.0.0
     * 
     * @param   array   $permalink  The permalink generated for this post by WordPress.
     * @param   int     $post_ID    The ID of the post.
     * @param   string  $title      The title for the post that the user used.
     * @param   string  $name       The name for the post that the user used.
     * @return  array               Modified Permalink
     */
    public function remove_stop_words_from_proposed_permalink( $permalink, $post_id, $title, $name ) {

        // If Post Name is empty, but a Title has been specified, this is the first time WordPress will generate a slug.
        // We need to remove stop words this one time.
        if ( empty( $name ) && ! empty( $title ) ) {
            // Remove stop words
            $permalink[1] = $this->remove_stop_words( $permalink[1] );
            
            // Filter
            $permalink[1] = apply_filters( 'wp_simple_seo_post_remove_stop_words_from_proposed_permalink', $permalink[1], $permalink, $post_id, $title, $name );
        }

        return $permalink;

    }

    /**
     * Removes stop words from the permalink when saving a new Post for the first time.
     *
     * If the user subsequently changes the permalink to include stop words, we allow this,
     * as this might be intentional.
     *
     * @since   1.0.0
     *
     * @param   string  $permalink  Permalink
     * @return  string              Modified Permalink
     */
    public function remove_stopwords_from_permalink( $permalink ) {

        // Bail if permalink isn't empty
        if ( ! empty( $permalink ) ) {
            return $permalink;
        }

        // Generate permalink from Post Title
        $permalink = sanitize_title_with_dashes( filter_input( INPUT_POST, 'post_title' ) );

        // Remove stop words from the generated Permalink
        $permalink = $this->remove_stop_words( $permalink );

        // Filter
        $permalink = apply_filters( 'wp_simple_seo_post_remove_stopwords_from_permalink', $permalink );

        return $permalink;

    }

    /**
     * Removes stop words from the given string
     *
     * @since 1.0.0
     *
     * @param   string  $content    String to remove stopwords from
     * @return  string              Removed stopwords
     */
    private function remove_stop_words( $content ) {

        // Get stop words
        $stop_words = WP_Simple_SEO_Common::get_instance()->get_stop_words();

        // Create an array from our content, and only return the values that aren't stop words
        $content_arr = array_diff( explode( '-', $content ), $stop_words );

        // Bail if this is less than three words
        if ( count( $content_arr ) < 3 ) {
            return $content;
        }

        // Convert back into a string
        $content_without_stop_words = join( '-', $content_arr );

        // Filter
        $content_without_stop_words = apply_filters( 'wp_simple_seo_post_remove_stop_words', $content_without_stop_words, $content_arr, $content, $stop_words );

        return $content_without_stop_words;

    }

    /**
     * Fires the delete_cache() call when a Post changes status
     *
     * @since   1.0.1
     */
    public function register_delete_cache() {

        add_action( 'transition_post_status', array( $this, 'delete_cache' ), 10, 3 );
        
    }

    /**
     * Deletes all cache entries when a Post is created, edited, deleted, or has its status changed.
     *
     * @since   1.0.1
     */
    public function delete_cache() {

        WP_Simple_SEO_Cache::get_instance()->delete_all();

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object Class.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {
            self::$instance = new self;
        }

        return self::$instance;

    }

}