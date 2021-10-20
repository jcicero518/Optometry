<?php
/**
 * Export class
 * 
 * @package   WP_Simple_SEO
 * @author    WP Simple SEO
 * @version   1.0.0
 * @copyright WP Simple SEO
 */
class WP_Simple_SEO_Export {

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Constructor
     *
     * @since 1.0.0
     */
    public function __construct() {

        
    }

    /**
     * Export data, forcing a browser download
     *
     * @since 1.0.0
     *
     * @return  array           Data
     */
    public function export() {

        // Load instances
        $settings_instance = WP_Simple_SEO_Settings::get_instance();
        
        // Get plugin settings
        $settings = array(
            'welcome'   => $settings_instance->get_plugin_settings( 'welcome' ),
            'general'   => $settings_instance->get_plugin_settings( 'general' ),
            'meta'      => $settings_instance->get_plugin_settings( 'meta' ),
            'social'    => $settings_instance->get_plugin_settings( 'social' ),
            'sitemap'   => $settings_instance->get_plugin_settings( 'sitemap' ),
        );

        // Allow addons to add their own settings to the export file now
        $settings = apply_filters( 'wp_simple_seo_export', $settings );

        // Build JSON
        return json_encode( $settings );
        
    }

    /**
     * Force a browser download comprising of the given JSON data
     *
     * @since   1.0.0
     *
     * @param   string  $json   JSON Data for file
     */
    public function force_file_download( $json ) {

        // Output JSON, prompting the browser to auto download as a JSON file now
        header( "Content-type: application/x-msdownload" );
        header( "Content-Disposition: attachment; filename=export.json" );
        header( "Pragma: no-cache" );
        header( "Expires: 0" );
        echo $json;
        exit();

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