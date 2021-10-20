<?php
/**
* Plugin Name: WP Simple SEO
* Plugin URI: https://wpsimpleseo.com
* Version: 1.0.8
* Author: WP Simple SEO
* Author URI: https://wpsimpleseo.com
* Description: Simple, effective SEO for your WordPress web site.
*/

/**
 * WP Simple SEO Class
 * 
 * @package   WP_Simple_SEO
 * @author    WP Simple SEO
 * @version   1.0.0
 * @copyright WP Simple SEO
 */
class WP_Simple_SEO {

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Holds the plugin information object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public $plugin = '';

    /**
     * Holds the licensing class object.
     *
     * @since 1.1.6
     *
     * @var object
     */
    public $licensing = '';

    /**
    * Constructor. Acts as a bootstrap to load the rest of the plugin
    *
    * @since 1.0.0
    */
    public function __construct() {

        // Plugin Details
        $this->plugin = new stdClass;
        $this->plugin->name         = 'wp-simple-seo';
        $this->plugin->displayName  = 'WP Simple SEO';
        $this->plugin->folder       = plugin_dir_path( __FILE__ );
        $this->plugin->url          = plugin_dir_url( __FILE__ );
        $this->plugin->version      = '1.0.8';
        $this->plugin->home_url     = 'https://wpsimpleseo.com';
        $this->plugin->support_url  = 'https://wpsimpleseo.com/documentation/support';
        $this->plugin->purchase_url = 'https://wpsimpleseo.com/pricing';
        $this->plugin->review_notice = sprintf( __( 'Thanks for using %s for your SEO!', $this->plugin->name ), $this->plugin->displayName );

        // Licensing Submodule
        if ( ! class_exists( 'Licensing_Update_Manager' ) ) {
            require_once( $this->plugin->folder . '_modules/licensing/lum.php' );
        }
        $this->licensing = new Licensing_Update_Manager( $this->plugin, 'https://wpsimpleseo.com', $this->plugin->name );

        // Initialize non-static classes that contain WordPress Actions or Filters
        // Admin
        if ( is_admin() ) {
            $wp_simple_seo_ajax         = WP_Simple_SEO_AJAX::get_instance();
            $wp_simple_seo_admin        = WP_Simple_SEO_Admin::get_instance();
            $wp_simple_seo_bulk_actions = WP_Simple_SEO_Bulk_Actions::get_instance();
            $wp_simple_seo_import       = WP_Simple_SEO_Import::get_instance();
            $wp_simple_seo_notices      = WP_Simple_SEO_Notices::get_instance();
            $wp_simple_seo_post         = WP_Simple_SEO_Post::get_instance();
            $wp_simple_seo_term         = WP_Simple_SEO_Term::get_instance();

            add_action( 'init', array( $this, 'upgrade' ) );
        }

        // Global
        $wp_simple_seo_admin_bar  = WP_Simple_SEO_Admin_Bar::get_instance();
        $wp_simple_seo_canonical  = WP_Simple_SEO_Canonical::get_instance();
        $wp_simple_seo_ld_json    = WP_Simple_SEO_LD_JSON::get_instance();
        $wp_simple_seo_meta       = WP_Simple_SEO_Meta::get_instance();
        $wp_simple_seo_robots     = WP_Simple_SEO_Robots::get_instance();
        $wp_simple_seo_tags       = WP_Simple_SEO_Tags::get_instance();
        $wp_simple_seo_title      = WP_Simple_SEO_Title::get_instance();
        $wp_simple_seo_social     = WP_Simple_SEO_Social::get_instance();
        $wp_simple_seo_sitemaps   = WP_Simple_SEO_Sitemaps::get_instance();

    }

    /**
     * Runs the upgrade routine once the plugin has loaded
     *
     * @since 1.0.0
     */
    public function upgrade() {

        // Run upgrade routine
        WP_Simple_SEO_Install::get_instance()->upgrade();

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

/**
 * Define the autoloader for this Plugin
 *
 * @since   1.0.0
 *
 * @param   string  $class_name     The class to load
 */
function WP_Simple_SEO_Autoloader( $class_name ) {

    // Define the required start of the class name
    $class_start_name = 'WP_Simple_SEO';

    // Get the number of parts the class start name has
    $class_parts_count = count( explode( '_', $class_start_name ) );

    // Break the class name into an array
    $class_path = explode( '_', $class_name );

    // Bail if it's not a minimum length (i.e. doesn't potentially have WP_Affiliate_Linker Autolinker)
    if ( count( $class_path ) < $class_parts_count ) {
        return;
    }

    // Build the base class path for this class
    $base_class_path = '';
    for ( $i = 0; $i < $class_parts_count; $i++ ) {
        $base_class_path .= $class_path[ $i ] . '_';
    }
    $base_class_path = trim( $base_class_path, '_' );

    // Bail if the first parts don't match what we expect
    if ( $base_class_path != $class_start_name ) {
        return;
    }

    // Define the file name we need to include
    $file_name = strtolower( implode( '-', array_slice( $class_path, $class_parts_count ) ) ) . '.php';

    // Define the paths with file name we need to include
    $include_paths = array(
        dirname( __FILE__ ) . '/includes/admin/' . $file_name,
        dirname( __FILE__ ) . '/includes/global/' . $file_name,
    );

    // Iterate through the include paths to find the file
    foreach ( $include_paths as $path_file ) {
        if ( file_exists( $path_file ) ) {
            require_once( $path_file );
            return;
        }
    }

    // If here, we couldn't find the file!

}
spl_autoload_register( 'WP_Simple_SEO_Autoloader' );

// Initialise class
$wp_simple_seo = WP_Simple_SEO::get_instance();

// Register activation hooks
register_activation_hook( __FILE__, array( 'WP_Simple_SEO_Install', 'activate' ) );
add_action( 'activate_wpmu_site', array( 'WP_Simple_SEO_Install', 'activate_wpmu_site' ) );