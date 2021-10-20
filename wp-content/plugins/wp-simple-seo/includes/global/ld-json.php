<?php
/**
 * LD JSON class, covering:
 * - Google Knowledge Graph (including Social Links)
 * - Google Sitelinks Search Box
 * 
 * @package   WP_Simple_SEO
 * @author    WP Simple SEO
 * @version   1.0.0
 * @copyright WP Simple SEO
 */
class WP_Simple_SEO_LD_JSON {

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

        add_filter( 'wp_simple_seo_ld_json_output', array( $this, 'google_knowledge_graph_schema' ), 1 );
        add_action( 'wp_head', array( $this, 'output_json' ), 1 );

    }

    /**
     * Returns LD-JSON for Google Knowledge Graph Schema:
     * - Organization / Company / Person
     * - Social Links
     * - Sitelink Searchbox
     *
     * @since   1.0.0
     *
     * @param   array   $ld_json    JSON array of LD-JSON entities to output
     * @return  array               JSON array of LD-JSON entities to output
     */
    public function google_knowledge_graph_schema( $ld_json ) {

        // No need to output anything if we're not on the front page
        if ( ! is_front_page() ) {
            return $ld_json;
        }

        // Get settings instance
        $settings = WP_Simple_SEO_Settings::get_instance();

        // Get Entity Type
        $type = $settings->get_setting( 'general', 'knowledge_graph[type]' );

        // Don't do anything if neither Company or Person are set
        if ( empty( $type ) ) {
            return $ld_json;
        }

        // Setup JSON array
        $json = array(
            '@context'  => 'http://schema.org',
            'url'       => get_bloginfo( 'url' ),
            'sameAs'    => array(),
        );

        /**
         * Type
         */
        switch ( $type ) {
            /**
             * Company
             */
            case 'company':
                $json['@type'] = 'Organization';
                $json['@id']   = '#organization';
                break;

            /**
             * Person
             */
            case 'person':
                $json['@type'] = 'Person';
                $json['@id']   = '#person';
                break;

            /**
             * WebSite
             */
            case 'website':
                $json['@type'] = 'WebSite';
                break;
        }

        // https://developers.google.com/search/docs/data-types/sitename
        $json['name']           = $settings->get_setting( 'general', 'knowledge_graph[name]' );
        $json['alternateName']  = $settings->get_setting( 'general', 'knowledge_graph[alternate_name]' );

        // https://developers.google.com/search/docs/data-types/logo
        $logo_id  = $settings->get_setting( 'general', 'knowledge_graph[logo]' );
        if ( ! empty( $logo_id ) ) {
            $logo = wp_get_attachment_image_src( $logo_id, 'full' );
            if ( $logo != false ) {
                $json['logo'] = $logo[0];
            }
        }

        /**
         * SiteLinks Search
         * - If disabled, see includes/global/meta.php, where we output <meta name="google" content="nositelinkssearchbox" />
         */
        if ( $settings->get_setting( 'general', 'sitelinks_searchbox[enabled]' ) ) {
            $json['potentialAction'] = array(
                '@type'         => 'SearchAction',
                'target'        => get_bloginfo( 'url' ) . '?q={search_term_string}',
                'query-input'   => 'required name="search_term_string"',
            );
        }

        /**
         * Social Profile Links, if Social Metadata is enabled
         * https://developers.google.com/search/docs/data-types/social-profile-links
         */
        if ( $settings->get_setting( 'social', 'general[enabled]' ) ) {
            $profiles = WP_Simple_SEO_Common::get_instance()->get_social_networks();
            foreach ( $profiles as $profile ) {
                // See if a URL exists in Social Settings
                $url = $settings->get_setting( 'social', $profile['name'] . '[url]' );
                if ( empty( $url ) ) {
                    continue;
                }

                // For Twitter, we need to prepend http://twitter.com
                if ( $profile['name'] == 'twitter' ) {
                    $url = 'https://twitter.com/' . $url;
                }

                // URL exists, so add it to the JSON data
                $json['sameAs'][] = $url;
            }
        }
        
        // Allow devs to filter the JSON data
        $json = apply_filters( 'wp_simple_seo_ld_json_output_google_knowledge_graph_schema', $json );

        // Add JSON to the array and return
        $ld_json[] = $json;

        return $ld_json;

    }

    /**
     * Outputs LD-JSON
     *
     * @since   1.0.0
     */
    public function output_json() {

        // Allow Plugin and Addons to build LD-JSON
        $json = apply_filters( 'wp_simple_seo_ld_json_output', array() );

        // Don't do anything if the JSON isn't an array
        if ( ! is_array( $json ) ) {
            return;
        }

        // Output
        echo '<script type="application/ld+json">';
        foreach ( $json as $ld_json ) {
            echo wp_json_encode( $ld_json ) . "\n";
        }
        echo '</script>';

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