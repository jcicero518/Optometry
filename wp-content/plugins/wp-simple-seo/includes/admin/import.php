<?php
/**
 * Import class
 *
 * This class handles importing data from third party sources
 * e.g. Yoast SEO
 * 
 * @package   WP_Simple_SEO
 * @author    WP Simple SEO
 * @version   1.0.0
 * @copyright WP Simple SEO
 */
class WP_Simple_SEO_Import {

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

        // Importers
        add_action( 'wp_simple_seo_import', array( $this, 'import' ), 10, 2 );
        add_filter( 'wp_simple_seo_import_yoast', array( $this, 'import_yoast' ) );
        add_filter( 'wp_simple_seo_import_aioseo', array( $this, 'import_aioseo' ) );
        
    }

    /**
     * Helper method to retrieve an array of import sources that this plugin
     * can import SEO data from.
     *
     * These will typically be other WordPress Plugins that have data stored
     * in this WordPress installation e.g. Yoast, AIOSEO
     *
     * @since 1.0.0
     *
     * @return  array   Import Sources
     */
    public function get_import_sources() {

        // Determine which SEO Plugins the user might be able to import data from
        $import_sources = array();
        
        $yoast = get_option( 'wpseo' );
        if ( is_array( $yoast ) && ! empty( $yoast ) ) {
            $import_sources['yoast'] = array(
                'name'          => 'yoast',
                'label'         => __( 'Yoast SEO', 'wp-simple-seo' ),
                'documentation' => 'https://wpsimpleseo.com/documentation/import-yoast-seo/',
            );
        }

        $aioseo = get_option( 'aioseop_options' );
        if ( is_array( $aioseo ) && ! empty( $aioseo ) ) {
            $import_sources['aioseo'] = array(
                'name'          => 'aioseo',
                'label'         => __( 'All in One SEO', 'wp-simple-seo' ),
                'documentation' => 'https://wpsimpleseo.com/documentation/import-aioseo-pack/',
            );
        }

        // Allow devs to filter import sources
        $import_sources = apply_filters( 'wp_simple_seo_import_get_import_sources', $import_sources );

        // Return
        return $import_sources;
        
    }


    /**
     * Import data from WP Simple SEO
     *
     * @since   1.0.0
     *
     * @param   bool    $success    Success
     * @param   array   $data       Array
     * @return  mixed               WP_Error | bool
     */
    public function import( $success, $data ) {

        // Check data is an array
        if ( ! is_array( $data ) ) {
            return new WP_Error( 'wp_simple_seo_import_error', __( 'Supplied file is not a valid JSON settings file, or has become corrupt.', 'wp-simple-seo' ) );
        }

        // Get settings instance
        $settings_instance = WP_Simple_SEO_Settings::get_instance();
        
        // Iterate through settings screens ($data), saving the settings
        foreach ( $data as $type => $value ) {
            $settings_instance->update_plugin_settings( $type, $value );
        }

        // Done
        return true;
        
    }

    /**
     * Import data from All in One SEO
     *
     * @since   1.0.0
     *
     * @param   bool    Success
     * @return  mixed   WP_Error | bool
     */
    public function import_aioseo( $success = false ) {

        // Get instances
        $settings_instance = WP_Simple_SEO_Settings::get_instance();
        $parser = WP_Simple_SEO_Parser::get_instance();

        // Get AIOSEO values
        $import     = get_option( 'aioseop_options' );
        
        /**
         * 1. General
         */
        // Get WP Simple SEO and Yoast SEO values
        $settings   = $settings_instance->get_plugin_settings( 'general' );

        // Update Plugin Settings based on AIOSEO Settings
        if ( isset( $import['aiosp_google_verify'] ) ) {
            $settings['webmaster_tools']['google_verification'] = $import['aiosp_google_verify'];
        }
        if ( isset( $import['aiosp_bing_verify'] ) ) {
            $settings['webmaster_tools']['bing_verification'] = $import['aiosp_bing_verify'];
        }
        if ( isset( $import['aiosp_google_sitelinks_search'] ) ) {
            if ( $import['aiosp_google_sitelinks_search'] == 'on' ) {
                $settings['sitelinks_searchbox']['enabled'] = 1;
            } else {
                $settings['sitelinks_searchbox']['enabled'] = 0;
            }
        }
        if ( isset( $import['aiosp_google_set_site_name'] ) && $import['aiosp_google_set_site_name'] == 'on' ) {
            if ( isset( $import['aiosp_google_specify_site_name'] ) ) {
                $settings['knowledge_graph']['alternate_name'] = $parser->parse_aioseo_tags( $import['aiosp_google_specify_site_name'] );
            }
        }

        // Save Settings
        $settings_instance->update_plugin_settings( 'general', $settings );

        /**
         * 2. Meta
         */
        // Get WP Simple SEO values
        $settings   = $settings_instance->get_plugin_settings( 'meta' );

        // Update Plugin Settings based on AIOSEO Settings
        
        // Meta: General
        // N/A

        // Meta: Home Page
        if ( isset( $import['aiosp_home_title'] ) ) {
            $settings['home']['title'] = $parser->parse_aioseo_tags( $import['aiosp_home_title'] );
        } else if ( isset( $import['aiosp_home_page_title_format'] ) ) {
            $settings['home']['title'] = $parser->parse_aioseo_tags( $import['aiosp_home_page_title_format'] );
        }
        if ( isset( $import['aiosp_home_description'] ) ) {
            $settings['home']['description'] = $parser->parse_aioseo_tags( $import['aiosp_home_description'] );
        }

        // Meta: Post Types
        $post_types = WP_Simple_SEO_Common::get_instance()->get_post_types();
        foreach ( $post_types as $post_type ) {
            // Single
            if ( isset( $import[ 'aiosp_' . $post_type->name . '_title_format' ] ) ) {
                $settings['post_types'][ $post_type->name ]['single']['title'] = $parser->parse_aioseo_tags( $import[ 'aiosp_' . $post_type->name . '_title_format'] );
            }
            if ( isset( $import['aiosp_description_format'] ) ) {
                $settings['post_types'][ $post_type->name ]['single']['description'] = $parser->parse_aioseo_tags( $import[ 'aiosp_description_format' ] );
            }
            if ( isset( $import[ 'aiosp_cpostnoindex' ] ) && is_array( $import[ 'aiosp_cpostnoindex' ] ) && isset( $import[ 'aiosp_cpostnoindex' ][ $post_type->name ] ) ) {
                $settings['post_types'][ $post_type->name ]['single']['noindex'] = 1;
            }

            // Archive
            if ( $settings_instance->post_type_has_archive( $post_type ) ) {
                // Use Post Type, or failing that fallback to default
                if ( isset( $import[ 'aiosp_' . $post_type->name . '_title_format' ] ) ) {
                    $settings['post_types'][ $post_type->name ]['archive']['title'] = $parser->parse_aioseo_tags( $import[ 'aiosp_' . $post_type->name . '_title_format' ] );
                } elseif ( isset( $import[ 'aiosp_archive_title_format' ] ) ) {
                    $settings['post_types'][ $post_type->name ]['archive']['title'] = $parser->parse_aioseo_tags( $import[ 'aiosp_archive_title_format' ] );
                }
                if ( isset( $import[ 'aiosp_description_format' ] ) ) {
                    $settings['post_types'][ $post_type->name ]['archive']['description'] = $import[ 'aiosp_description_format' ];
                }
                if ( isset( $import[ 'aiosp_cpostnoindex' ] ) && is_array( $import[ 'aiosp_cpostnoindex' ] ) && isset( $import[ 'aiosp_cpostnoindex' ][ $post_type->name ] ) ) {
                    $settings['post_types'][ $post_type->name ]['archive']['noindex'] = 1;
                }
            }
        }

        // Meta: Taxonomies
        // @TODO
        $taxonomies = WP_Simple_SEO_Common::get_instance()->get_taxonomies();
        foreach ( $taxonomies as $taxonomy ) {
            if ( isset( $import[ 'aiosp_' . $taxonomy->name . '_title_format' ] ) ) {
                $settings['taxonomies'][ $taxonomy->name ]['title'] = $import[ 'aiosp_' . $taxonomy->name . '_title_format' ];
            }
            if ( isset( $import[ 'aiosp_description_format' ] ) ) {
                $settings['taxonomies'][ $taxonomy->name ]['description'] = $import[ 'aiosp_description_format' ];
            }
            if ( isset( $import[ 'aiosp_' . $taxonomy->name . '_noindex' ] ) && $import[ 'aiosp_' . $taxonomy->name . '_noindex' ] == 'on' ) {
                $settings['taxonomies'][ $taxonomy->name ]['noindex'] = 1;
            }
        }

        // Meta: Archives: Author
        if ( isset( $import['aiosp_author_title_format'] ) ) {
            $settings['archives']['author']['title'] = $parser->parse_aioseo_tags( $import['aiosp_author_title_format'] );
        }
        if ( isset( $import['aiosp_description_format'] ) ) {
            $settings['archives']['author']['description'] = $parser->parse_aioseo_tags( $import['aiosp_description_format'] );
        }

        // Meta: Archives: Date
        if ( isset( $import['aiosp_date_title_format'] ) ) {
            $settings['archives']['date']['title'] = $parser->parse_aioseo_tags( $import['aiosp_date_title_format'] );
        }
        if ( isset( $import['aiosp_description_format'] ) ) {
            $settings['archives']['date']['title'] = $parser->parse_aioseo_tags( $import['aiosp_description_format'] );
        }

        // Meta: Search Results
        if ( isset( $import['aiosp_search_title_format'] ) ) {
            $settings['search']['title'] = $parser->parse_aioseo_tags( $import['aiosp_search_title_format'] );
        }
        if ( isset( $import['aiosp_description_format'] ) ) {
            $settings['search']['description'] = $parser->parse_aioseo_tags( $import['aiosp_description_format'] );
        }

        // Meta: 404
        if ( isset( $import['aiosp_404_title_format'] ) ) {
            $settings['four04']['title'] = $parser->parse_aioseo_tags( $import['aiosp_404_title_format'] );
        }
        
        // Save Settings
        $settings_instance->update_plugin_settings( 'meta', $settings );

        /**
         * 3. Social
         */
        // N/A

        /**
         * 4. Sitemaps
         */
        // N/A

        /**
         * 5. Posts
         * - Import metadata where it's specified on any Post/Page/CPT,
         * as these are overrides that we need to honor.
         */
        $query = new WP_Query( array(
            'post_type'         => 'any',
            'post_status'       => 'publish',
            'posts_per_page'    => -1,
            'meta_query'        => array(
                'relation'      => 'OR',
                array(
                    'key'       => '_aioseop_title',
                    'compare'   => '!=',
                    'value'     => '',
                ),
                array(
                    'key'       => '_aioseop_description',
                    'compare'   => '!=',
                    'value'     => '',
                ),
                array(
                    'key'       => '_aioseop_noindex',
                    'compare'   => '!=',
                    'value'     => '',
                ),
                array(
                    'key'       => '_aioseop_nofollow',
                    'compare'   => '!=',
                    'value'     => '',
                ),
            ),
            'fields'            => 'ids',
        ) );

        if ( count( $query->posts ) > 0 ) {
            foreach ( $query->posts as $post_id ) {
                // Build settings array
                $post_type = get_post_type( $post_id );
                $settings = array(
                    'post_types' => array(
                        $post_type => array(
                            'single' => array(
                            ),
                        ),
                    ),
                );

                $title = get_post_meta( $post_id, '_aioseop_title', true );
                $description = get_post_meta( $post_id, '_aioseop_description', true );
                $noindex = get_post_meta( $post_id, '_aioseop_noindex', true );
                $nofollow = get_post_meta( $post_id, '_aioseop_nofollow', true );
                if ( ! empty( $title ) ) {
                    $settings['post_types'][ $post_type ]['single']['title'] = $parser->parse_aioseo_tags( $title );
                }
                if ( ! empty( $description ) ) {
                    $settings['post_types'][ $post_type ]['single']['description'] = $parser->parse_aioseo_tags( $description );
                }
                if ( ! empty( $noindex ) ) {
                    $settings['post_types'][ $post_type ]['single']['noindex'] = ( $noindex == 'on' ? 1 : 0 );
                }
                if ( ! empty( $nofollow ) ) {
                    $settings['post_types'][ $post_type ]['single']['nofollow'] = ( $nofollow == 'on' ? 1 : 0 );
                }

                $settings_instance->update_post_settings( $post_id, 'meta', $settings );
            }
        }

        /**
         * 6. Terms
         */
        // N/A

        // Done
        return true;

    }

    /**
     * Import data from Yoast SEO
     *
     * @since   1.0.0
     *
     * @param   bool    Success
     * @return  mixed   WP_Error | bool
     */
    public function import_yoast( $success = false ) {

        // Get instances
        $settings_instance = WP_Simple_SEO_Settings::get_instance();
        $parser = WP_Simple_SEO_Parser::get_instance();

        /**
         * 1. General
         */
        // Get WP Simple SEO and Yoast SEO values
        $settings   = $settings_instance->get_plugin_settings( 'general' );
        $import     = get_option( 'wpseo' );

        // Webmaster Tools
        if ( isset( $import['googleverify'] ) ) {
            $settings['webmaster_tools']['google_verification'] = $import['googleverify'];
        }
        if ( isset( $import['msverify'] ) ) {
            $settings['webmaster_tools']['bing_verification'] = $import['msverify'];
        }

        // Knowledge Graph
        if ( isset( $import['company_or_person'] ) ) {
            $settings['knowledge_graph']['type'] = $import['company_or_person'];
        
            if ( $import['company_or_person'] == 'company' && isset( $import['company_name'] ) ) {
                $settings['knowledge_graph']['name'] = $import['company_name'];
            }
            if ( $import['company_or_person'] == 'person' && isset( $import['person_name'] ) ) {
                $settings['knowledge_graph']['name'] = $import['person_name'];
            }
        }
        if ( isset( $import['alternative_website_name'] ) ) {
            $settings['knowledge_graph']['alternate_name'] = $import['alternative_website_name'];
        }
        if ( isset( $import['company_logo'] ) ) {
            $settings['knowledge_graph']['logo'] = $import['company_logo'];
        }

        // Save Settings
        $settings_instance->update_plugin_settings( 'general', $settings );

        /**
         * 2. Meta
         */
        // Get WP Simple SEO and Yoast SEO values
        $settings   = $settings_instance->get_plugin_settings( 'meta' );
        $import     = get_option( 'wpseo_titles' );

        // Update Plugin Settings based on Yoast SEO Settings
        
        // Meta: Home Page
        if ( isset( $import['title-home-wpseo'] ) ) {
            $settings['home']['title'] = $parser->parse_yoast_tags( $import['title-home-wpseo'] );
        }
        if ( isset( $import['metadesc-home-wpseo'] ) ) {
            $settings['home']['description'] = $parser->parse_yoast_tags( $import['metadesc-home-wpseo'] );
        }

        // Meta: Post Types
        $post_types = WP_Simple_SEO_Common::get_instance()->get_post_types();
        foreach ( $post_types as $post_type ) {
            // Single
            if ( isset( $import[ 'title-' . $post_type->name ] ) ) {
                $settings['post_types'][ $post_type->name ]['single']['title'] = $parser->parse_yoast_tags( $import[ 'title-' . $post_type->name ] );
            }
            if ( isset( $import[ 'metadesc-' . $post_type->name ] ) ) {
                $settings['post_types'][ $post_type->name ]['single']['description'] = $parser->parse_yoast_tags( $import[ 'metadesc-' . $post_type->name ] );
            }
            if ( isset( $import[ 'noindex-' . $post_type->name ] ) ) {
                $settings['post_types'][ $post_type->name ]['single']['noindex'] = $import[ 'noindex-' . $post_type->name ];
            }
            if ( isset( $import[ 'hideeditbox-' . $post_type->name ] ) ) {
                $settings['post_types'][ $post_type->name ]['single']['meta_box'] = ( ( $import[ 'hideeditbox-' . $post_type->name ] == '1' ) ? 0 : 1 );
            }

            // Archive
            if ( $settings_instance->post_type_has_archive( $post_type ) ) {
                if ( isset( $import[ 'title-ptarchive-' . $post_type->name ] ) ) {
                    $settings['post_types'][ $post_type->name ]['archive']['title'] = $parser->parse_yoast_tags( $import[ 'title-ptarchive-' . $post_type->name ] );
                }
                if ( isset( $import[ 'metadesc-ptarchive-' . $post_type->name ] ) ) {
                    $settings['post_types'][ $post_type->name ]['archive']['description'] = $parser->parse_yoast_tags( $import[ 'metadesc-ptarchive-' . $post_type->name ] );
                }
                if ( isset( $import[ 'noindex-ptarchive-' . $post_type->name ] ) ) {
                    $settings['post_types'][ $post_type->name ]['single']['noindex'] = $import[ 'noindex-ptarchive-' . $post_type->name ];
                }
            }
        }

        // Meta: Taxonomies
        $taxonomies = WP_Simple_SEO_Common::get_instance()->get_taxonomies();
        foreach ( $taxonomies as $taxonomy ) {
            if ( isset( $import[ 'title-tax-' . $taxonomy->name ] ) ) {
                $settings['taxonomies'][ $taxonomy->name ]['title'] = $parser->parse_yoast_tags( $import[ 'title-tax-' . $taxonomy->name ] );
            }
            if ( isset( $import[ 'metadesc-tax-' . $taxonomy->name ] ) ) {
                $settings['taxonomies'][ $taxonomy->name ]['description'] = $parser->parse_yoast_tags( $import[ 'metadesc-tax-' . $taxonomy->name ] );
            }
            if ( isset( $import[ 'noindex-tax-' . $taxonomy->name ] ) ) {
                $settings['taxonomies'][ $taxonomy->name ]['noindex'] = $import[ 'noindex-tax-' . $taxonomy->name ];
            }
            if ( isset( $import[ 'hideeditbox-tax-' . $taxonomy->name ] ) ) {
                $settings['taxonomies'][ $taxonomy->name ]['meta_box'] = ( ( $import[ 'hideeditbox-tax-' . $taxonomy->name ] == '1' ) ? 0 : 1 );
            }
        }

        // Meta: Archives: Author
        if ( isset( $import['title-author-wpseo'] ) ) {
            $settings['archives']['author']['title'] = $parser->parse_yoast_tags( $import['title-author-wpseo'] );
        }
        if ( isset( $import['metadesc-author-wpseo'] ) ) {
            $settings['archives']['author']['description'] = $parser->parse_yoast_tags( $import['metadesc-author-wpseo'] );
        }
        if ( isset( $import['noindex-author-wpseo'] ) && $import['noindex-author-wpseo'] ) {
            $settings['archives']['author']['noindex'] = true;
        }

        // Meta: Archives: Date
        if ( isset( $import['title-archive-wpseo'] ) ) {
            $settings['archives']['date']['title'] = $parser->parse_yoast_tags( $import['title-archive-wpseo'] );
        }
        if ( isset( $import['metadesc-archive-wpseo'] ) ) {
            $settings['archives']['date']['description'] = $parser->parse_yoast_tags( $import['metadesc-archive-wpseo'] );
        }
        if ( isset( $import['noindex-archive-wpseo'] ) && $import['noindex-archive-wpseo'] ) {
            $settings['archives']['date']['noindex'] = true;
        }

        // Meta: Search Results
        if ( isset( $import['title-search-wpseo'] ) ) {
            $settings['search']['title'] = $parser->parse_yoast_tags( $import['title-search-wpseo'] );
        }
        if ( isset( $import['metadesc-search-wpseo'] ) ) {
            $settings['search']['description'] = $parser->parse_yoast_tags( $import['metadesc-search-wpseo'] );
        }

        // Meta: 404
        if ( isset( $import['title-404-wpseo'] ) ) {
            $settings['four04']['title'] = $parser->parse_yoast_tags( $import['title-404-wpseo'] );
        }

        // Save Settings
        $settings_instance->update_plugin_settings( 'meta', $settings );

        /**
         * 3. Social
         */
        // Get WP Simple SEO and Yoast SEO values
        $settings   = $settings_instance->get_plugin_settings( 'social' );
        $import     = get_option( 'wpseo_social' );

        // Update Plugin Settings based on Yoast SEO Settings
        
        // Facebook
        if ( isset( $import['og_default_image'] ) ) {
            $settings['facebook']['default_image'] = $import['og_default_image'];
        }
        if ( isset( $import['facebook_site'] ) ) {
            $settings['facebook']['url'] = $import['facebook_site'];
        }

        // Twitter
        if ( isset( $import['twitter_card_type'] ) ) {
            $settings['twitter']['card_type'] = $import['twitter_card_type'];
        }
        if ( isset( $import['twitter_site'] ) ) {
            $settings['twitter']['username'] = $import['twitter_site'];
            $settings['twitter']['url'] = 'http://twitter.com/' . $import['twitter_site'];
        }

        // Other Social Networks
        if ( isset( $import['google_plus_url'] ) ) {
            $settings['google']['url'] = $import['google_plus_url'];
        }
        if ( isset( $import['instagram_url'] ) ) {
            $settings['instagram']['url'] = $import['instagram_url'];
        }
        if ( isset( $import['youtube_url'] ) ) {
            $settings['youtube']['url'] = $import['youtube_url'];
        }
        if ( isset( $import['linkedin_url'] ) ) {
            $settings['linkedin']['url'] = $import['linkedin_url'];
        }
        if ( isset( $import['myspace_url'] ) ) {
            $settings['myspace']['url'] = $import['myspace_url'];
        }
        if ( isset( $import['pinterest_url'] ) ) {
            $settings['pinterest']['url'] = $import['pinterest_url'];
        }

        // Save Settings
        //$settings_instance->update_plugin_settings( 'social', $settings );

        /**
         * 4. Sitemaps
         */
        // Get WP Simple SEO and Yoast SEO values
        $settings   = $settings_instance->get_plugin_settings( 'sitemap' );
        $import     = get_option( 'wpseo_xml' );

        // Update Plugin Settings based on Yoast SEO Settings
        
        // Enabled
        if ( isset( $import['enablexmlsitemap'] ) ) {
            $settings['general']['enabled'] = $import['enablexmlsitemap'];
        }

        // Save Settings
        $settings_instance->update_plugin_settings( 'sitemap', $settings );

        /**
         * 5. Posts
         * - Import metadata where it's specified on any Post/Page/CPT,
         * as these are overrides that we need to honor.
         */
        $query = new WP_Query( array(
            'post_type'         => 'any',
            'post_status'       => 'publish',
            'posts_per_page'    => -1,
            'meta_query'        => array(
                'relation'      => 'OR',
                array(
                    'key'       => '_yoast_wpseo_title',
                    'compare'   => '!=',
                    'value'     => '',
                ),
                array(
                    'key'       => '_yoast_wpseo_metadesc',
                    'compare'   => '!=',
                    'value'     => '',
                ),
                array(
                    'key'       => '_yoast_wpseo_meta-robots-noindex',
                    'compare'   => '!=',
                    'value'     => '',
                ),
                array(
                    'key'       => '_yoast_wpseo_meta-robots-nofollow',
                    'compare'   => '!=',
                    'value'     => '',
                ),
            ),
            'fields'            => 'ids',
        ) );

        if ( count( $query->posts ) > 0 ) {
            foreach ( $query->posts as $post_id ) {
                // Build settings array
                $post_type = get_post_type( $post_id );
                $settings = array(
                    'post_types' => array(
                        $post_type => array(
                            'single' => array(
                            ),
                        ),
                    ),
                );

                $title = get_post_meta( $post_id, '_yoast_wpseo_title', true );
                $description = get_post_meta( $post_id, '_yoast_wpseo_metadesc', true );
                $noindex = get_post_meta( $post_id, '_yoast_wpseo_meta-robots-noindex', true );
                $nofollow = get_post_meta( $post_id, '_yoast_wpseo_meta-robots-nofollow', true );
                if ( ! empty( $title ) ) {
                    $settings['post_types'][ $post_type ]['single']['title'] = $parser->parse_yoast_tags( $title );
                }
                if ( ! empty( $description ) ) {
                    $settings['post_types'][ $post_type ]['single']['description'] = $parser->parse_yoast_tags( $description );
                }
                if ( ! empty( $noindex ) ) {
                    $settings['post_types'][ $post_type ]['single']['noindex'] = (int) $noindex;
                }
                if ( ! empty( $nofollow ) ) {
                    $settings['post_types'][ $post_type ]['single']['nofollow'] = (int) $nofollow;
                }

                $settings_instance->update_post_settings( $post_id, 'meta', $settings );
            }
        }

        /**
         * 6. Terms
         * - Import metadata where it's specified on any Term,
         * as these are overrides that we need to honor.
         * - Yoast SEO stores term meta in wp_options, not wp_termmeta
         */
        $import = get_option( 'wpseo_taxonomy_meta' );
        if ( is_array( $import ) && count( $import ) > 0 ) {
            foreach ( $import as $taxonomy => $terms ) {
                foreach ( $terms as $term_id => $meta ) {
                    // Build settings array
                    $settings = array(
                        'taxonomies' => array(
                            $taxonomy => array(
                            ),
                        ),
                    );

                    if ( isset( $meta['wpseo_title'] ) && ! empty( $meta['wpseo_title'] ) ) {
                        $settings['taxonomies'][ $taxonomy ]['title'] = $parser->parse_yoast_tags( $meta['wpseo_title'] );
                    }
                    if ( isset( $meta['wpseo_desc'] ) && ! empty( $meta['wpseo_desc'] ) ) {
                        $settings['taxonomies'][ $taxonomy ]['description'] = $parser->parse_yoast_tags( $meta['wpseo_desc'] );
                    }
                    if ( isset( $meta['wpseo_noindex'] ) && ! empty( $meta['wpseo_noindex'] ) ) {
                        $settings['taxonomies'][ $taxonomy ]['noindex'] = ( $meta['wpseo_noindex'] == 'noindex' ? 1 : 0 );
                    }

                    // Store term settings
                    $settings_instance->update_term_settings( $term_id, 'meta', $settings );
                }
            }
        }
        
        // Done
        return true;

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