<?php
/**
 * Administration class
 * 
 * @package   WP_Simple_SEO
 * @author    WP Simple SEO
 * @version   1.0.0
 * @copyright WP Simple SEO
 */
class WP_Simple_SEO_Admin {

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
     * Success, Warning and Error Notices
     *
     * @since 1.0.0
     *
     * @var array
     */
    public $notices = array(
        'success'   => array(),
        'warning'   => array(),
        'error'     => array(),
    );

    /**
     * Constructor
     *
     * @since 1.0.0
     */
    public function __construct() {

        // Actions
        add_action( 'wp_loaded', array( $this, 'save_third_party_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'scripts_css' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );

        // WP_List_Table Columns and Views
        add_action( 'pre_get_posts', array( $this, 'views_filter' ) );
        add_action( 'init', array( $this, 'register_admin_columns_and_views' ) ); 

        // Import & Export
        add_action( 'plugins_loaded', array( $this, 'maybe_export' ) );

        // Support
        add_action( 'plugins_loaded', array( $this, 'maybe_redirect_to_support' ) );

        // Screens
        add_filter( 'wp_simple_seo_admin_get_current_screen_general', array( $this, 'get_current_screen_general' ), 10, 2 );
        add_filter( 'wp_simple_seo_admin_get_current_screen_sitemap', array( $this, 'get_current_screen_sitemap' ), 10, 2 );
        add_filter( 'wp_simple_seo_admin_get_current_screen_import-export', array( $this, 'get_current_screen_import_export' ), 10, 2 );

        // Actions: Screen-specific
        add_action( 'wp_simple_seo_admin_scripts_js_social', array( $this, 'social_scripts_css' ), 10, 2 );
        
    }

    /**
     * Runs a WordPress action based on the wp_simple_seo_view parameter in the URL
     *
     * @since   1.0.7
     */
    public function views_filter( $query ) {

        // Bail if there's no wp_simple_seo_view defined
        if ( ! isset( $_REQUEST['wp_simple_seo_view'] ) ) {
            return;
        }

        // Bail if not the main query
        if ( ! $query->is_main_query() ) {
            return;
        }

        // Run action
        do_action( 'wp_simple_seo_admin_views_filter_' . sanitize_text_field( $_REQUEST['wp_simple_seo_view'] ), $query );

    }

    /**
     * Registers columns and views in WP_List_Table for public Post Types, displaying the Content Analysis status
     *
     * @since   1.0.7
     */
    public function register_admin_columns_and_views() {

        // Get all public Post Types
        $post_types = WP_Simple_SEO_Common::get_instance()->get_post_types();

        // Bail if no public Post Types
        if ( ! is_array( $post_types ) ) {
            return;
        }
        if ( count( $post_types ) == 0 ) {
            return;
        }

        // Register columns and views
        foreach ( $post_types as $post_type ) {
            add_filter( 'views_edit-' . $post_type->name, array( $this, 'register_views' ) );
            add_filter( 'manage_edit-' . $post_type->name . '_columns', array( $this, 'register_columns' ) );
            add_action( 'manage_' . $post_type->name . '_posts_custom_column', array( $this, 'output_columns_data' ), 10, 2 );
        }
        
    }

    /**
     * Registers links for the views section of WP_List_Tables
     *
     * @since   1.0.7
     *
     * @param   array   $views  Views
     * @return  array           Views
     */
    public function register_views( $views ) {

        // Get post type from current screen
        $screen = get_current_screen();
        $post_type = ( isset( $screen->post_type ) ? $screen->post_type : false );

        // Bail if no post type found
        if ( ! $post_type ) {
            return $views;
        }

        // Get current view
        $active_view = ( isset( $_REQUEST['wp_simple_seo_view'] ) ? sanitize_text_field( $_REQUEST['wp_simple_seo_view'] ) : '' );

        // Allow devs / addons to define view links now
        $views = apply_filters( 'wp_simple_seo_admin_register_views_' . $post_type, $views, $active_view );
        $views = apply_filters( 'wp_simple_seo_admin_register_views', $views, $active_view, $post_type );

        return $views;

    }

    /**
     * Adds columns to all public Post Types WP_List_Tables within the WordPress Administration
     * 
     * @since   1.0.7
     *
     * @param   array   $columns    Default Columns
     * @return  array               New Columns
     */
    public function register_columns( $columns ) {

        // Get post type from current screen
        $screen = get_current_screen();
        $post_type = ( isset( $screen->post_type ) ? $screen->post_type : false );

        // Bail if no post type found
        if ( ! $post_type ) {
            return $columns;
        }
       
        // Allow devs / addons to define columns
        $columns = apply_filters( 'wp_simple_seo_admin_register_columns_' . $post_type, $columns );
        $columns = apply_filters( 'wp_simple_seo_admin_register_columns', $columns, $post_type, $screen );

        return $columns;

    }

    /**
     * Manages the data to be displayed within a column on public Post Types
     * 
     * @since   1.0.7
     *
     * @param   string    $column_name    Column Name
     * @param   int       $post_id        Post ID
     */
    public function output_columns_data( $column_name, $post_id ) {

        // Get Post Type
        $post_type = get_post_type( $post_id );

        // Allow devs / addons to output now
        do_action( 'wp_simple_seo_admin_output_columns_data_' . $post_type, $column_name, $post_id );
        do_action( 'wp_simple_seo_admin_output_columns_data', $column_name, $post_type, $post_id );

    }

    /**
     * If the Export button was clicked, generate a JSON file and prompt its download now
     *
     * @since   1.0.0
     */
    public function maybe_export() {

        // Get current screen
        $screen = $this->get_current_screen();
        if ( ! $screen || is_wp_error( $screen ) ) {
            return;
        }

        // Check we're on the Import / Export screen
        if ( $screen['name'] != 'import-export' ) {
            return;
        }

        // Check we requested the export action
        if ( ! isset( $_GET['export'] ) ) {
            return;
        }
        if ( $_GET['export'] != 1 ) {
            return;
        }

        // Get export data
        $exporter = WP_Simple_SEO_Export::get_instance();
        $settings = $exporter->export();
        $exporter->force_file_download( $settings ); // This ends the PHP operation

    }

    /**
     * If the Support menu item was clicked, redirect
     *
     * @since   1.0.0
     */
    public function maybe_redirect_to_support() {

        // Get current screen
        $screen = $this->get_current_screen();
        if ( ! $screen || is_wp_error( $screen ) ) {
            return;
        }

        // Check we're on the Support screen
        if ( $screen['name'] != 'support' ) {
            return;
        }

        // Redirect
        wp_redirect( WP_Simple_SEO::get_instance()->plugin->support_url );
        die();

    }

    /**
     * Enqueues JS and CSS if we're on a plugin screen or the welcome screen.
     *
     * @since 1.0.0
     */
    public function scripts_css() {

        // Bail if we can't get the current admin screen, or we're not viewing a screen
        // belonging to this plugin.
        if ( ! function_exists( 'get_current_screen' ) ) {
            return;
        }

        // Get current screen and registered plugin screens
        $screen = get_current_screen();
        $screens = $this->get_screens();

        // If we're on the top level screen, enqueue
        if ( $screen->base == 'toplevel_page_' . $this->base->plugin->name ) {
            $this->enqueue_scripts_css( 'general', $screen, $screens );
            return;
        }

        // Iterate through the registered screens, to see if we're viewing that screen
        foreach ( $screens as $registered_screen ) {
            if ( $screen->id == 'wp-simple-seo_page_wp-simple-seo-' . $registered_screen['name'] ) {
                // We're on a plugin screen
                $this->enqueue_scripts_css( $registered_screen['name'], $screen, $screens );
                return;
            }
        }

    }

    /**
     * Enqueues scripts and CSS
     *
     * @since   1.0.0
     *
     * @param   string      $plugin_screen_name     The plugin screen name (e.g. welcome, general, social)
     * @param   WP_Screen   $screen                 Current WordPress Screen object
     * @param   array       $screens                Registered Plugin Screens (optional)
     */
    public function enqueue_scripts_css( $plugin_screen_name, $screen, $screens = '' ) {

        global $post;

        // Enqueue JS
        // These scripts are registered in _modules/licensing/lum.php
        wp_enqueue_script( 'lum-admin-clipboard' );
        wp_enqueue_script( 'lum-admin-conditional' );
        wp_enqueue_media();
        wp_enqueue_script( 'lum-admin-media-library' );
        wp_enqueue_script( 'lum-admin-tabs' );
        wp_enqueue_script( 'lum-admin-tags' );
        wp_enqueue_script( 'lum-admin' );

        // These scripts are in the main plugin
        wp_enqueue_script( $this->base->plugin->name . '-admin', $this->base->plugin->url . 'assets/js/admin.js', array( 'jquery' ), $this->base->plugin->version, true );
        wp_localize_script( $this->base->plugin->name . '-admin', 'wp_simple_seo_admin', array(
            'bulk_actions'  => apply_filters( 'wp_simple_seo_admin_enqueue_scripts_css_admin_js_bulk_actions', array() ),
            'messages'      => array(
                'cancel'            => __( 'Cancel', 'wp-simple-seo' ),
                'cancelling'        => __( 'Cancelling...', 'wp-simple-seo' ),
                'finished'          => __( 'Finished', 'wp-simple-seo' ),
                'no_posts_selected' => __( 'No Posts were selected to apply the Bulk Action on.', 'wp-simple-seo' ),
            ),
        ) );

        // Enqueue CSS
        wp_enqueue_style( 'lum-admin' );
        wp_enqueue_style( $this->base->plugin->name . '-admin', $this->base->plugin->url . 'assets/css/admin.css' );

        // Allow devs to load their JS / CSS now
        do_action( 'wp_simple_seo_admin_scripts_js', $screen, $screens );
        do_action( 'wp_simple_seo_admin_scripts_js_' . $plugin_screen_name, $screen, $screens );

        do_action( 'wp_simple_seo_admin_scripts_css', $screen, $screens );
        do_action( 'wp_simple_seo_admin_scripts_css_' . $plugin_screen_name, $screen, $screens );

    }

    /**
     * Enqueues JS and CSS for the Social Settings screen
     *
     * @since 1.0.0
     */
    public function social_scripts_css() {

        wp_enqueue_media();

    }
    
    /**
     * Adds menu and sub menu items to the WordPress Administration
     *
     * @since 1.0.0
     */
    public function admin_menu() {

        // Get the registered screens
        $screens = $this->get_screens();

        // Get base instance
        $this->base = WP_Simple_SEO::get_instance();

        // Create the top level screen
        add_menu_page( $this->base->plugin->displayName, $this->base->plugin->displayName, 'manage_options', $this->base->plugin->name, array( $this, 'admin_screen' ), 'dashicons-admin-site' );
       
        // If we only have one registered screen, don't do anything else
        if ( count( $screens ) == 1 ) {
            return;
        }

        // Iterate through screens, adding as submenu items
        foreach ( (array) $screens as $screen ) {
            // The general screen doesn't need to append the page slug
            $slug = ( ( $screen['name'] == 'general' || $screen['name'] == 'welcome' ) ? $this->base->plugin->name : $this->base->plugin->name . '-' . $screen['name'] );

            // Add submenu page
            add_submenu_page( $this->base->plugin->name, $screen['label'], $screen['label'], 'manage_options', $slug, array( $this, 'admin_screen' ) );
        }

        // Allow Licensing (Addons) submodule to add its menu link now
        do_action( str_replace( '-', '_', $this->base->plugin->name ) . '_admin_menu' );

    }

    /**
     * Redirects the user to the welcome screen, if it's the first time
     * they've interacted with the plugin.
     *
     * @since 1.0.0
     */
    public function maybe_redirect_to_welcome_screen() {

        // Get the current screen to see if we're about to load a plugin screen.
        $screen = $this->get_current_screen();
        if ( ! $screen || is_wp_error( $screen ) ) {
            return;
        }

        // Check if we've already displayed the welcome screen
        $instance = WP_Simple_SEO_Settings::get_instance();
        if ( $instance->get_setting( 'welcome', 'displayed' ) ) {
            return;
        }

        // If here, display the welcome screen once.
        wp_safe_redirect( admin_url( 'index.php?page=' . $this->base->plugin->name . '-welcome' ) );
        exit;

    }

    /**
     * Returns an array of screens for the plugin's admin
     *
     * @since 1.0
     *
     * @return array Sections
     */
    private function get_screens() {

        // Get base instance
        $this->base = WP_Simple_SEO::get_instance();

        // Get settings instance
        $settings = WP_Simple_SEO_Settings::get_instance();

        // If the user hasn't seen the welcome screen, just return that as the available screen.
        // This ensures the user must go through the Welcome screen, and no other sub menu items
        // are added to WordPress.
        if ( ! $settings->get_setting( 'welcome', 'displayed' ) ) {
            // Build screen data
            $screens = array(
                // Visibility, Tagline and SEO Config
                'welcome'   => array(
                    'name'          => 'welcome',
                    'label'         => __( 'Welcome', 'wp-simple-seo' ),
                    'description'   => __( 'Thanks for choosing WP Simple SEO. Your site is now configured for SEO, but there are a few things you might want to do.', 'wp-simple-seo' ),
                    'view'          => $this->base->plugin->folder . 'views/admin/welcome.php',
                    'columns'       => 1,
                    'data'          => array(
                        'import_sources'        => WP_Simple_SEO_Import::get_instance()->get_import_sources(),
                        'is_production_site'    => WP_Simple_SEO_Common::get_instance()->is_production_site(),
                        'is_public'             => get_option( 'blog_public' ),
                        'default_tagline'       => ( ( get_option( 'blogdescription' ) == __( 'Just another WordPress site' ) ) ? true : false ),
                        'google'                => $this->get_google_status(),
                        'no_options_displayed'  => false,
                    ),
                ),
            );

            // Set a flag to determine whether any options will be displayed on this screen
            if ( count( $screens['welcome']['data']['import_sources'] ) == 0 && 
                ! $screens['welcome']['data']['is_production_site'] && 
                ! $screens['welcome']['data']['default_tagline'] ) {
                $screens['welcome']['data']['no_options_displayed'] = true;
            }

            // Return screens now, as we only want the Welcome screen to be available
            return $screens;
        }

        // If here, the user has seen and completed the welcome screen.
        // Define the available settings screens
        $screens = array(
            'general'   => array(
                'name'          => 'general',
                'label'         => __( 'General', 'wp-simple-seo' ),
                'description'   => __( 'This section verifies site ownership with Google and Bing, submits your XML sitemap to Google and allows you to provide additional information and options for Google\'s Knowledge Graph and Sitelinks Search Box. <br />You only usually need to set this up once, but you can always come back if you need to occasionally tweak something.', 'wp-simple-seo' ),
                'view'          => $this->base->plugin->folder . 'views/admin/settings-general.php',
                'columns'       => 2,
                'data'          => array(),
                'documentation' => 'https://wpsimpleseo.com/documentation/general-settings',
            ),

            'meta'      => array(
                'name'          => 'meta',
                'label'         => __( 'Meta', 'wp-simple-seo' ),
                'description'   => 
                    __( 'The defaults set here determine the Title and Description that will appear on search engine results.
                        <br />
                        You can choose not to index certain parts of your web site (noindex). 
                        <br />
                        To exclude a specific Post, Page or Taxonomy Term from the search engines, edit your Post and choose the noindex option there.', 'wp-simple-seo' ),
                'view'          => $this->base->plugin->folder . 'views/admin/settings-meta.php',
                'columns'       => 1,
                'data'          => array(),
                'documentation' => 'https://wpsimpleseo.com/documentation/meta-settings',
            ),

            'social'   => array(
                'name'          => 'social',
                'label'         => __( 'Social', 'wp-simple-seo' ),
                'description'   => 
                    __( 'The settings here define your social media profile links for Google\'s Knowledge Graph, and fallback options when visitors share your Pages and Posts on some social networks.', 'wp-simple-seo' ),
                'view'          => $this->base->plugin->folder . 'views/admin/settings-social.php',
                'columns'       => 1,
                'data'          => array(),
                'documentation' => 'https://wpsimpleseo.com/documentation/social-settings',
            ),

            'sitemap'   => array(
                'name'          => 'sitemap',
                'label'         => __( 'Sitemap', 'wp-simple-seo' ),
                'description'   => 
                    __( 'If enabled, an XML sitemap is generated, which can be submitted to search engines so they can attempt to index your web site content.
                        <br />
                        We honor the indexing settings in the Meta section of the plugin; if you choose not to index something (noindex), it won\'t be included in the sitemap.
                        <br />
                        Author sitemaps are only generated if your site has two or more Authors with published Posts.  This prevents potentially submitting duplicate URLs.', 'wp-simple-seo' ),
                'view'          => $this->base->plugin->folder . 'views/admin/settings-sitemap.php',
                'columns'       => 1,
                'data'          => array(),
                'documentation' => 'https://wpsimpleseo.com/documentation/sitemap-settings',
            ),
        );

        // Allow addons to specify additional screens
        $screens = apply_filters( 'wp_simple_seo_admin_get_screens', $screens );

        // Add Import & Export + Support screens to the end of the array
        $screens['import-export'] = array(
            'name'          => 'import-export',
            'label'         => __( 'Import &amp; Export', 'wp-simple-seo' ),
            'description'   => 
                __( 'Import SEO configuration data from another WP Simple SEO installation, or a third party plugin that has been
                    previously used on this site.
                    <br />
                    Export WP Simple SEO configuration data to a JSON file.', 'wp-simple-seo' ),
            'view'          => $this->base->plugin->folder . 'views/admin/settings-import-export.php',
            'columns'       => 1,
            'data'          => array(),
            'documentation' => 'https://wpsimpleseo.com/documentation/import-export',
        );
        $screens['support'] = array(
            'name'          => 'support',
            'label'         => __( 'Support', 'wp-simple-seo' ),
        );

        // Return
        return $screens;

    }

    /**
     * Gets the current admin screen the user is on
     *
     * @since 1.0.0
     *
     * @return array    Screen name and label
     */
    private function get_current_screen() {

        // Bail if no page given
        if ( ! isset( $_GET['page'] ) ) {
            return;
        }

        // Get current screen name
        $screen = sanitize_text_field( $_GET['page'] );

        // Get registered screens
        $screens = $this->get_screens();

        // If screen name matches plugin, we're on either the general screen or welcome screen
        if ( $screen == $this->base->plugin->name ) {
            if ( isset( $screens['general'] ) ) {
                return apply_filters( 'wp_simple_seo_admin_get_current_screen_general', $screens['general'], $screen );
            } else {
                return apply_filters( 'wp_simple_seo_admin_get_current_screen_welcome', $screens['welcome'], $screen );
            }
        }

        // Remove the plugin name from the screen
        $screen = str_replace( $this->base->plugin->name . '-', '', $screen );

        // Check if the screen exists
        if ( ! isset( $screens[ $screen ] ) ) {
            return new WP_Error( 'screen_missing', __( 'The requested administration screen does not exist', 'wp-simple-seo' ) );
        }

        // Filter the result, to allow third parties to inject any data they want to access in their screen view now
        $screens[ $screen ] = apply_filters( 'wp_simple_seo_admin_get_current_screen_' . $screen, $screens[ $screen ], $screen );

        // Return the screen
        return $screens[ $screen ];

    }

    /**
     * Injects Google Status information into the General screen data, for use by the view.
     *
     * @since   1.0.0
     *
     * @param   array   $screen         Screen
     * @param   string  $screen_name    Screen Name
     * @return  array                   Screen
     */
    public function get_current_screen_general( $screen, $screen_name ) {

        $screen['data'] = array(
            'is_production_site'    => WP_Simple_SEO_Common::get_instance()->is_production_site(),
            'google'                => $this->get_google_status(),
        );

        return $screen;

    }

    /**
     * Injects Google Status information into the Sitemap screen data, for use by the view.
     *
     * @since   1.0.0
     *
     * @param   array   $screen         Screen
     * @param   string  $screen_name    Screen Name
     * @return  array                   Screen
     */
    public function get_current_screen_sitemap( $screen, $screen_name ) {

        $screen['data'] = array(
            'google' => $this->get_google_status(),
        );

        return $screen;

    }

    /**
     * Injects Import Sources information into the Import / Export screen data, for use by the view.
     *
     * @since   1.0.0
     *
     * @param   array   $screen         Screen
     * @param   string  $screen_name    Screen Name
     * @return  array                   Screen
     */
    public function get_current_screen_import_export( $screen, $screen_name ) {

        $screen['data'] = array(
            'import_sources' => WP_Simple_SEO_Import::get_instance()->get_import_sources(),
        );

        return $screen;

    }

    /**
     * Gets the current admin screen name the user is on
     *
     * @since   1.0.0
     *
     * @return  mixed  false | Screen Name
     */
    private function get_current_screen_name() {

        // If no page name was given, we're not on a plugin screen.
        if ( ! isset( $_GET['page'] ) ) {
            return false;
        }

        // Get screen name
        $screen = sanitize_text_field( $_GET['page'] );

        // If screen name matches plugin, we might be on the welcome screen
        if ( $screen == $this->base->plugin->name && $this->get_setting( 'welcome', 'displayed' ) != 1 ) {
            return 'welcome';
        }

        return $screen;

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
     * Returns an array of tabs for each plugin section
     *
     * @since 1.0.0
     *
     * @param   string  $screen     Screen
     * @return  array               Tabs
     */
    private function get_screen_tabs( $screen ) {

        // Define tabs array
        $tabs = array();

        // Define the tabs depending on which screen is specified
        switch ( $screen ) {

            /**
            * General
            */
            case 'general':
                $tabs = array(
                    'google' => array(
                        'name'          => 'google',
                        'label'         => __( 'Google', 'wp-simple-seo' ),
                    ),
                    'bing' => array(
                        'name'          => 'bing',
                        'label'         => __( 'Bing', 'wp-simple-seo' ),
                    ),
                );
                break;

            /**
            * Meta
            */
            case 'meta':
                // Default tabs
                $tabs = array(
                    'general' => array(
                        'name'  => 'general',
                        'label' => __( 'General', 'wp-simple-seo' ),
                    ),
                    'home' => array(
                        'name'  => 'home',
                        'label' => __( 'Home Page', 'wp-simple-seo' ),
                    ),
                    'post_types' => array(
                        'name'  => 'post_types',
                        'label' => __( 'Post Types', 'wp-simple-seo' ),
                    ),
                    'taxonomies' => array(
                        'name'  => 'taxonomies',
                        'label' => __( 'Taxonomies', 'wp-simple-seo' ),
                    ),
                    'archives' => array(
                        'name'  => 'archives',
                        'label' => __( 'Archives', 'wp-simple-seo' ),
                    ),
                    'search' => array(
                        'name'  => 'search',
                        'label' => __( 'Search Results', 'wp-simple-seo' ),
                    ),
                    '404' => array(
                        'name'  => '404',
                        'label' => __( '404', 'wp-simple-seo' ),
                    ),
                );
                break;

            /**
            * Social
            */
            case 'social':
                // General Tab
                $tabs = array(
                    'general' => array(
                        'name' => 'general',
                        'label' => __( 'General', 'wp-simple-seo' ),
                    ),
                    'profiles' => array(
                        'name' => 'profiles',
                        'label' => __( 'Profiles', 'wp-simple-seo' ),
                    ),
                    'open-graph' => array(
                        'name' => 'open-graph',
                        'label' => __( 'Open Graph', 'wp-simple-seo' ),
                    ),
                    'twitter' => array(
                        'name' => 'twitter',
                        'label' => __( 'Twitter', 'wp-simple-seo' ),
                    ),
                );
                break;

            /**
             * Sitemap
             */
            case 'sitemap':
                // Default tabs
                $tabs = array(
                    'general' => array(
                        'name' => 'general',
                        'label' => __( 'General', 'wp-simple-seo' ),
                    ),
                );
                break;

            /**
             * Import & Export
             */
            case 'import-export':
                // Default tabs
                $tabs = array(
                    'import' => array(
                        'name'          => 'import',
                        'label'         => __( 'Import from WP Simple SEO', 'wp-simple-seo' ),
                        'documentation' => 'https://wpsimpleseo.com/documentation/import-wp-simple-seo/',
                    ),
                );

                // Depending on whether any third party SEO plugin data is present in this install,
                // add additional tabs.
                $import_sources = WP_Simple_SEO_Import::get_instance()->get_import_sources();
                if ( count( $import_sources ) > 0 ) {
                    foreach ( $import_sources as $import_source ) {
                        $tabs[ 'import-' . $import_source['name'] ] = array(
                            'name'          => 'import-' . $import_source['name'],
                            'label'         => sprintf( __( 'Import from %s', 'wp-simple-seo' ), $import_source['label'] ),
                            'documentation' => $import_source['documentation'],
                        );
                    }
                }

                // Finally, add the export tab
                $tabs['export'] = array(
                    'name'          => 'export',
                    'label'         => __( 'Export', 'wp-simple-seo' ),
                    'documentation' => 'https://wpsimpleseo.com/documentation/export-configuration/',
                );

                break;

            /**
             * Addons
             * - Allow addons to specify their screen tabs now
             */
            default:
                $tabs = apply_filters( 'wp_simple_seo_admin_get_screen_tabs_' . $screen, $tabs );
                break;

        }

        // Allow addons to define tabs on existing screens
        $tabs = apply_filters( 'wp_simple_seo_admin_get_screen_tabs', $tabs, $screen );

        // Return
        return $tabs;

    }

    /**
     * Output the Settings screen
     * Save POSTed data from the Administration Panel into a WordPress option
     *
     * @since 1.0.0
     */
    public function admin_screen() {

        // Get the current screen
        $screen = $this->get_current_screen();
        if ( ! $screen || is_wp_error( $screen ) ) {
            return;
        }

        // Maybe run actions
        $this->run_actions( $screen['name'] );

        // Maybe save settings
        $this->save_settings( $screen['name'] );

        // Hacky; get the current screen again, so its data is refreshed post save and actions
        // @TODO optimize this
        $screen = $this->get_current_screen();
        if ( ! $screen || is_wp_error( $screen ) ) {
            return;
        }
        
        // Get the tabs for the given screen
        $tabs = $this->get_screen_tabs( $screen['name'] );

        // Get the current tab
        // If no tab specified, get the first tab
        $tab = $this->get_current_screen_tab( $tabs );

        // Define a string of conditional tabs
        // The tabs are only displayed if the General > Enabled option is checked
        $conditional_tabs = '';
        foreach ( $tabs as $tab_key => $data ) {
            if ( $tab_key == 'general' ) {
                continue;
            }

            $conditional_tabs .= $tab_key . ',';
        }
        $conditional_tabs = trim( $conditional_tabs, ',' );

        // Load View

        // 1. Start Setup
        if ( $screen['name'] == 'welcome' ) {
            // Setup needed
            // Check for any oAuth messages that might have been returned
            if ( isset( $_GET['wp-simple-seo-error'] ) ) {
                $this->notices['error'][] = urldecode( stripslashes( $_GET['wp-simple-seo-error'] ) );
            }

            // If this site isn't a production site, add a notice to let the user know that some welcome
            // options aren't available, but will be when they go live.
            if ( ! WP_Simple_SEO_Common::get_instance()->is_production_site() ) {
                $this->notices['warning'][] = __( 'Some options are not displayed on this screen (such as submitting your XML sitemap), because we\'ve detected this is a local or development web site.  You\'ll see the additional options once you load WP Simple SEO on your live / production site.', 'wp-simple-seo' );
            }
            
            include_once( $this->base->plugin->folder . '/views/admin/welcome.php' );   
            return;
        }

        // 2. Finished Setup
        $finished_setup = ( isset( $_GET['finished_setup'] ) ? (int) $_GET['finished_setup'] : 0 );
        if ( $finished_setup ) {
            include_once( $this->base->plugin->folder . '/views/admin/welcome-finished.php' ); 
            return;
        }

        // 3. Default Plugin Screen
        include_once( $this->base->plugin->folder . '/views/admin/settings.php' );

        // Request Review
        if ( $this->get_setting( 'welcome', 'displayed' ) ) { 
            $this->base->licensing->request_review();
        }
    
    }

    /**
     * Returns an array of information queried from the Google Search Console, covering:
     * - Google oAuth authorized
     * - Google oAuth URL (used to start the oAuth process)
     * - Site Registered in Google Search Console
     * - Site Verified in Google Search Console
     * - Sitemap Submitted to Google
     *
     * @since   1.0.0
     *
     * @return  array   Data
     */
    public function get_google_status() {

        // Get instance
        $google = WP_Simple_SEO_Google::get_instance();

        // Assume nothing has been done
        $results = array(
            'oauth_authorized'   => false,
            'oauth_url'          => false,
            'site_registered'    => false,
            'site_verified'      => false,
            'sitemap_submitted'  => false,
        );

        // Get the oAuth URL, in case it's needed
        $oauth_url = $google->get_authorize_url();
        if ( is_wp_error( $oauth_url ) ) {
            // Add error message to array of errors, so it's output.
            $this->notices['errors'][] = $oauth_url->get_error_message();

            // Return the default array of Google results
            return $results;
        }

        // Get oAuth URL and whether the user is authorized with Google 
        $results['oauth_url'] = $oauth_url;
        $results['oauth_authorized'] = $google->is_authorized();

        // At this stage, if the user has not authorized WP Simple SEO, return
        if ( is_wp_error( $results['oauth_authorized'] ) ) {
            // Return the default array of Google results
            $results['oauth_authorized'] = false;
            return $results;
        }

        // Determine whether the site has been registered, verified, and the sitemap submitted
        $results['site_registered'] = $google->site_registered( get_bloginfo( 'url' ) );
        $results['site_verified']   = $google->site_verified( get_bloginfo( 'url' ) );
        $results['sitemap_submitted'] = $google->sitemap_exists( get_bloginfo( 'url' ), 'sitemap_index.xml' );

        // Filter results
        $results = apply_filters( 'wp_simple_seo_admin_get_google_status', $results, $google );

        // Return
        return $results;

    }

    /**
     * Saves settings that have come from a third party i.e. an oAuth process
     *
     * This is done on wp_loaded(), so wp_redirect() can be safely used without the 'headers already sent'
     * error.
     *
     * @since   1.0.0
     */
    public function save_third_party_settings() {

        /**
         * If a third party service returns values in the URL, we need to sanitize and store them.
         * This happens for Google oAuth
         */
        if ( isset( $_REQUEST['wp-simple-seo-google-webmaster-access-token'] ) ) {
            $settings = WP_Simple_SEO_Settings::get_instance()->get_plugin_settings( 'general' );
            $settings['webmaster_tools']['google_access_token'] = stripslashes( $_REQUEST['wp-simple-seo-google-webmaster-access-token'] );
            WP_Simple_SEO_Settings::get_instance()->update_plugin_settings( 'general', $settings );

            /**
             * Depending on the screen we're on, define the redirect URL
             */
            $screen = $this->get_current_screen();
            if ( ! $screen || is_wp_error( $screen ) ) {
                return;
            }

            switch ( $screen['name'] ) {
                /**
                 * Welcome
                 */
                case 'welcome':
                    $url = 'admin.php?page=' . $_GET['page'] . '&action=google_search_console_submit';
                    break;

                default:
                    $url = 'admin.php?page=' . $_GET['page'];
                    break;
            }

            wp_redirect( $url );
            die();
        }

    }

    /**
     * Runs action(s) depending on the URL request and screen the user is on.
     *
     * @since   1.0.0
     *
     * @param   string  $screen     Screen
     */
    private function run_actions( $screen = 'general' ) {

        // If no action is specified, bail
        if ( ! isset( $_REQUEST['action'] ) ) {
            return;
        }

        // Get action
        $action = sanitize_text_field( $_REQUEST['action'] );

        // Depending on the screen we're on, run the action
        switch ( $screen ) {
            /**
             * Welcome
             */
            case 'welcome':
                switch ( $action ) {
                    /**
                     * Register site, verify ownership and submit sitemap
                     */
                    case 'google_search_console_submit':
                        // Get existing settings
                        $settings = WP_Simple_SEO_Settings::get_instance()->get_plugin_settings( 'general' );

                        // 1. Check if this site exists in the user's Google Search Console. If not, add it now
                        $google = WP_Simple_SEO_Google::get_instance();
                        if ( ! $google->site_registered( get_bloginfo( 'url' ) ) ) {
                            // Add site to Google
                            $result = $google->site_add( get_bloginfo( 'url' ) );
                            
                            // Bail if something went wrong when adding the site to Google
                            if ( is_wp_error( $result ) ) {
                                $this->notices['error'][] = $result->get_error_message();
                                return;
                            }
                        }

                        // 2. Fetch the meta tag value from Google, which is used to verify ownership of the site.
                        // Store the verification code in the plugin settings
                        $result = $google->site_verify_get_meta_tag_value( get_bloginfo( 'url' ) );
                        if ( is_wp_error( $result ) ) {
                            $this->notices['error'][] = $result->get_error_message();
                            return;
                        }
                        $settings['webmaster_tools']['google_verification'] = $result;
                        WP_Simple_SEO_Settings::get_instance()->update_plugin_settings( 'general', $settings );

                        // 3. Request that Google now check the site to confirm the meta tag verification is active and valid.
                        $result = $google->site_verify( get_bloginfo( 'url' ) );
                        if ( is_wp_error( $result ) ) {
                            $this->notices['error'][] = $result->get_error_message();
                            return;
                        }
                       
                        // 4. Check if this site's XML sitemap exists in the user's Google Search Console. If not, add it now
                        if ( ! $google->sitemap_exists( get_bloginfo( 'url' ), 'sitemap_index.xml' ) ) {
                            // Add sitemap to Google
                            $result = $google->sitemap_add( get_bloginfo( 'url' ), 'sitemap_index.xml' );

                            // Bail if something went wrong when adding the sitemap to Google
                            if ( is_wp_error( $result ) ) {
                                $this->notices['error'][] = $result->get_error_message();
                                return;
                            }
                        }

                        // Done!
                        $this->notices['success'][] = __( 'Thanks - your site and sitemap have been submitted to Google successfully!', 'wp-simple-seo' );
                        return;
                        break;

                } // Action
                break;

            /**
             * General
             */
            case 'general':
                switch ( $action ) {
                    /**
                     * Google: Register Site
                     */
                    case 'google_site_register':
                        // Get class instance
                        $google = WP_Simple_SEO_Google::get_instance();

                        // Confirm the site does not exist in the user's Google Search Console.
                        if ( $google->site_registered( get_bloginfo( 'url' ) ) ) {
                            $this->notices['success'][] = __( 'This web site is already registered with Google.', 'wp-simple-seo' );
                            return;
                        }

                        // Add site to Google
                        $result = $google->site_add( get_bloginfo( 'url' ) );
                        if ( is_wp_error( $result ) ) {
                            $this->notices['error'][] = $result->get_error_message();
                            return;
                        }

                        // OK
                        $this->notices['success'][] = __( 'Site registered with Google successfully.', 'wp-simple-seo' );
                        return;
                        break;

                    /**
                     * Google: Verify Site Ownership
                     */
                    case 'google_site_verify':
                        // Get class instance
                        $google = WP_Simple_SEO_Google::get_instance();

                        // Get General Settings
                        $settings = WP_Simple_SEO_Settings::get_instance()->get_plugin_settings( 'general' );

                        // Fetch the meta tag value from Google, which is used to verify ownership of the site.
                        // Store the verification code in the plugin settings
                        $result = $google->site_verify_get_meta_tag_value( get_bloginfo( 'url' ) );
                        if ( is_wp_error( $result ) ) {
                            $this->notices['error'][] = $result->get_error_message();
                            return;
                        }
                        $settings['webmaster_tools']['google_verification'] = $result;
                        WP_Simple_SEO_Settings::get_instance()->update_plugin_settings( 'general', $settings );

                        // Request that Google now check the site to confirm the meta tag verification is active and valid.
                        $result = $google->site_verify( get_bloginfo( 'url' ) );
                        if ( is_wp_error( $result ) ) {
                            $this->notices['error'][] = $result->get_error_message();
                            return;
                        }

                        // OK
                        $this->notices['success'][] = __( 'Site ownership verified with Google successfully.', 'wp-simple-seo' );
                        return;
                        break;

                    /**
                     * Google: Submit Sitemap
                     */
                    case 'google_sitemap_submit':
                        // Get class instance
                        $google = WP_Simple_SEO_Google::get_instance();

                        // Check if this site's XML sitemap already exists in the user's Google Search Console.
                        if ( $google->sitemap_exists( get_bloginfo( 'url' ), 'sitemap_index.xml' ) ) {
                            // Sitemap 
                            $this->notices['success'][] = __( 'This web site\'s sitemap has already been submitted to Google.', 'wp-simple-seo' );
                            return;
                        }

                        // Add sitemap to Google
                        $result = $google->sitemap_add( get_bloginfo( 'url' ), 'sitemap_index.xml' );
                        if ( is_wp_error( $result ) ) {
                            $this->notices['error'][] = $result->get_error_message();
                            return;
                        }

                        // OK
                        $this->notices['success'][] = __( 'Sitemap submitted to Google successfully.', 'wp-simple-seo' );
                        return;
                        break;
                }
                break;

            /**
             * Meta
             */
            case 'meta':
                break;

            /**
             * Social
             */
            case 'social':
                break;

            /**
             * Sitemaps
             */
            case 'sitemap':
                switch ( $action ) {
                    /**
                     * Google: Submit Sitemap
                     */
                    case 'google_sitemap_submit':
                        // @TODO
                        break;
                }
                break;

            /**
             * Addons
             */
            default:
                // Allow addons to save settings now
                $result = apply_filters( 'wp_simple_seo_admin_run_action_' . $screen, false, $action );
                if ( is_wp_error( $result ) ) {
                    $this->notices['error'][] = $result->get_error_message();
                    return;
                }

                // OK
                if ( ! empty( $result ) ) {
                    $this->notices['success'][] = $result;
                    return;
                }
                break;

        }

    }

    /**
     * Save settings for the given screen
     *
     * @since 1.0
     *
     * @param string     $screen     Screen
     */
    public function save_settings( $screen = 'general' ) {

        // Check that some data was submitted in the request
        if ( ! isset( $_REQUEST[ $this->base->plugin->name . '_nonce' ] ) ) { 
            return;
        }

        // Invalid nonce
        if ( ! wp_verify_nonce( $_REQUEST[ $this->base->plugin->name . '_nonce' ], 'wp-simple-seo_' . $screen ) ) {
            $this->notices['error'][] = __( 'Invalid nonce specified. Settings NOT saved.', 'wp-simple-seo' );
            return false;
        }

        // Depending on the screen we're on, save the data and perform some actions
        switch ( $screen ) {
            /**
             * Welcome
             */
            case 'welcome':
                // Site Visibility
                if ( isset( $_POST['visibility_checkbox'] ) ) {
                    update_option( 'blog_public', 1 );
                }

                // Tagline
                if ( isset( $_POST['tagline_checkbox'] ) ) {
                    update_option( 'blogdescription', sanitize_text_field( $_POST['tagline'] ) );
                }

                // Import SEO Configuration
                if ( isset( $_POST['import_checkbox'] ) ) {
                    $import_source = sanitize_text_field( $_POST['import_source'] );
                    
                    // See includes/admin/import.php for build in Importers; developers can add their own to hook here too
                    $result = apply_filters( 'wp_simple_seo_import_' . $import_source, false );
                }

                // Mark the welcome screen as done, and exit this function
                return WP_Simple_SEO_Settings::get_instance()->update_plugin_settings( $screen, array(
                    'displayed' => 1,
                ) );
                break;

            /**
             * General
             */
            case 'general':
                // Save settings
                $result = WP_Simple_SEO_Settings::get_instance()->update_plugin_settings( $screen, $_POST );
                break;

            /**
             * Meta
             */
            case 'meta':
                // Save settings
                $result = WP_Simple_SEO_Settings::get_instance()->update_plugin_settings( $screen, $_POST );
                break;

            /**
             * Social
             */
            case 'social':
                // Save settings
                $result = WP_Simple_SEO_Settings::get_instance()->update_plugin_settings( $screen, $_POST );
                break;

            /**
             * Sitemaps
             */
            case 'sitemap':
                // Save settings
                $result = WP_Simple_SEO_Settings::get_instance()->update_plugin_settings( $screen, $_POST );

                // Flush rewrite rules, so the XML sitemap doesn't 404
                flush_rewrite_rules( true );
                break;

            /**
             * Import
             */
            case 'import-export':
                // Determine which plugin we're importing settings from
                $import_sources = WP_Simple_SEO_Import::get_instance()->get_import_sources();
                if ( is_array( $import_sources ) && count( $import_sources ) > 0 ) {
                    foreach ( $import_sources as $import_source => $label ) {
                        // If a POST variable is set, import from this SEO Plugin
                        if ( isset( $_POST['import_' . $import_source ] ) ) {
                            // See includes/admin/import.php for build in Importers; developers can add their own to hook here too
                            $result = apply_filters( 'wp_simple_seo_import_' . $import_source, false );
                            break;
                        }
                    }
                }

                // If here, we might be importing a JSON file
                // Check if a file was uploaded
                if ( ! is_array( $_FILES ) ) {
                    $result = new WP_Error( __( 'No JSON file uploaded.', 'wp-simple-seo' ) );
                    break;
                }

                // Check if the uploaded file encountered any errors
                if ( $_FILES['import']['error'] != 0 ) {
                    $result = new WP_Error( __( 'Error when attempting to upload JSON file for import.', 'wp-simple-seo' ) );
                    break;
                }

                // Read file
                $handle = fopen( $_FILES['import']['tmp_name'], 'r' );
                $json = fread( $handle, $_FILES['import']['size'] );
                fclose( $handle );
                $data = json_decode( $json, true );

                // Import data
                $result = apply_filters( 'wp_simple_seo_import', false, $data );
                break;

            /**
             * Addons
             */
            default:
                // Allow addons to save settings now
                $result = apply_filters( 'wp_simple_seo_admin_save_settings_' . $screen, false, $_POST );
                break;

        }

        // Check the result
        if ( isset( $result ) && is_wp_error( $result ) ) {
            $this->notices['error'][] = $result->get_error_message();
            return;
        }

        // OK
        $this->notices['success'][] = __( 'Settings saved.', 'wp-simple-seo' );
        return true;

    }

    /**
     * Helper method to get the setting value from the Plugin settings
     *
     * @since 1.0.0
     *
     * @param   string    $screen   Screen
     * @param   string    $keys     Setting Key(s)
     * @return  mixed               Value
     */
    public function get_setting( $screen = '', $key = '' ) {

        return WP_Simple_SEO_Settings::get_instance()->get_setting( $screen, $key );

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