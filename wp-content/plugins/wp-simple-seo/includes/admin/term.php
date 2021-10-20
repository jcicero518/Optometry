<?php
/**
 * Term class
 * 
 * @package   WP_Simple_SEO
 * @author    WP Simple SEO
 * @version   1.0.0
 * @copyright WP Simple SEO
 */
class WP_Simple_SEO_Term {

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
        add_action( 'wp_simple_seo_admin_scripts_js_term', array( $this, 'enqueue_scripts' ) );
        
        // Metabox
        add_action( 'admin_init', array( $this, 'add_meta_box' ) );

    }

    /**
     * Enqueues scripts for the Term Edit screen.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts() {

        // Get base plugin instance
        $this->base = WP_Simple_SEO::get_instance();

        // Get Term
        if ( ! isset( $_GET['taxonomy'] ) ) {
            return;
        }
        if ( ! isset( $_GET['tag_ID'] ) ) {
            return;
        }

        $term = get_term( (int) $_GET['tag_ID'], sanitize_text_field( $_GET['taxonomy'] ) );

        // Bail if no Term object exists
        if ( ! $term || empty( $term ) ) {
            return;
        }

        // Enqueue
        wp_enqueue_script( $this->base->plugin->name . '-preview', $this->base->plugin->url . 'assets/js/preview.js', array( 'jquery' ), $this->base->plugin->version, true );
        wp_localize_script( $this->base->plugin->name . '-preview', 'wp_simple_seo_preview', array(
            'ajax'                          => admin_url( 'admin-ajax.php' ),
            'get_snippet_preview_nonce'     => wp_create_nonce( $this->base->plugin->name . '-get-snippet-preview' ),
            'id'                            => $term->term_id,
            'max_meta_title_length'         => WP_Simple_SEO_Common::get_instance()->get_max_meta_title_length(),
            'max_meta_description_length'   => WP_Simple_SEO_Common::get_instance()->get_max_meta_description_length(),
        ) );

    }

    /**
     * Adds the meta editor box to the Taxonomy Term Editor, if it's enabled
     * for the Taxonomy Type the user is creating / editing.
     *
     * @since 1.0.0
     */
    public function add_meta_box() {

        // Get settings instance
        $settings = WP_Simple_SEO_Settings::get_instance();

        // Get meta taxonomy settings, to determine whether to output 'meta boxes' (fields) on taxonomy term add/edit screens.
        $meta_taxonomies_settings = $settings->get_setting( 'meta', 'taxonomies' );
        if ( empty( $meta_taxonomies_settings ) ) {
            return;
        }

        // Set a flag to enqueue CSS and JS
        $enqueued_css_js = false;

        // Iterate through the taxonomies, registering field output and saving if the taxonomy has the meta box enabled.
        foreach ( $meta_taxonomies_settings as $taxonomy => $taxonomy_settings ) {
            if ( $settings->get_setting( 'meta', 'taxonomies[' . $taxonomy . '][meta_box]' ) ) {
                // Register fields for this taxonomy
                add_action( $taxonomy . '_edit_form_fields', array( $this, 'output_meta_box' ), 9999 );
                add_action( 'edited_' . $taxonomy, array( $this, 'save_settings' ) );

                // Register cache actions
                add_action( 'edited_' . $taxonomy, array( $this, 'delete_cache' ) );
                add_action( 'delete_' . $taxonomy, array( $this, 'delete_cache' ) );

                // Enqueue CSS and JS
                if ( ! $enqueued_css_js ) {
                    add_action( 'admin_enqueue_scripts', array( $this, 'scripts_css' ) );
                    $enqueued_css_js = true;
                }
            }
        }

    }

    /**
     * Enqueues JS and CSS if we're on a plugin screen or the welcome screen.
     *
     * @since 1.0.0
     */
    public function scripts_css() {

        WP_Simple_SEO_Admin::get_instance()->enqueue_scripts_css( 'term', get_current_screen() );

    }

    /**
     * Outputs the meta box for the Term the user is editing
     *
     * @since 1.0.0
     *
     * @param   WP_Term     $term   The WordPress Term that's being edited
     */
    public function output_meta_box( $term ) {

        // Get base instance
        $this->base = WP_Simple_SEO::get_instance();

        // Get the tabs for the post screen
        $tabs = $this->get_screen_tabs();

        // Get the current tab
        // If no tab specified, get the first tab
        $tab = $this->get_current_screen_tab( $tabs );

        // Get taxonomy and tags
        $taxonomy = get_taxonomy( $term->taxonomy );
        $tags = WP_Simple_SEO_Tags::get_instance()->get_taxonomy_tags( $taxonomy );

        // Get Snippet Preview, by parsing the Title and Description
        $parser = WP_Simple_SEO_Parser::get_instance();
        $preview = array(
            'title'         => $parser->parse_tags( $this->get_setting( 'meta', 'taxonomies[' . $taxonomy->name . '][title]', $term->term_id ), 'taxonomy', $term ),
            'url'           => get_term_link( $term ),
            'description'   => $parser->parse_tags( $this->get_setting( 'meta', 'taxonomies[' . $taxonomy->name . '][description]', $term->term_id ), 'taxonomy', $term ),
        );

        // Get maximum lengths for the meta title and description
        $max_meta_title_length = WP_Simple_SEO_Common::get_instance()->get_max_meta_title_length();
        $max_meta_description_length = WP_Simple_SEO_Common::get_instance()->get_max_meta_description_length();
        
        // Load view
        include_once( $this->base->plugin->folder . '/views/admin/term.php' );

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
                'documentation' => 'https://wpsimpleseo.com/documentation/term-settings/',
            ),
        );

        // Allow addons to define tabs on existing screens
        $tabs = apply_filters( 'wp_simple_seo_term_get_screen_tabs', $tabs );

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
     * @param   int       $term_id  Term ID (optional)
     * @return  mixed               Value
     */
    private function get_setting( $screen, $key, $term_id = '' ) {

        return WP_Simple_SEO_Settings::get_instance()->get_setting( $screen, $key, $term_id );

    }

    /**
     * Helper method to save settings for the given screen
     *
     * @since 1.0
     *
     * @param int     $term_id     Term ID
     */
    public function save_settings( $term_id ) {

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
        if ( ! wp_verify_nonce( $_POST[ $this->base->plugin->name . '_nonce' ], 'wp-simple-seo_term' ) ) {
            return false;
        }

        // Save settings
        $term_settings = $_POST[ $this->base->plugin->name ];
        WP_Simple_SEO_Settings::get_instance()->update_term_settings( $term_id, 'meta', $term_settings );
        
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