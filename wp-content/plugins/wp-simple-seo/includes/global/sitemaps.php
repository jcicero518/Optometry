<?php
/**
 * Sitemap class
 * 
 * @package   WP_Simple_SEO
 * @author    WP Simple SEO
 * @version   1.0.0
 * @copyright WP Simple SEO
 */
class WP_Simple_SEO_Sitemaps {

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

        // If sitemaps aren't enabled, don't do anything
        if ( ! WP_Simple_SEO_Settings::get_instance()->get_setting( 'sitemap', 'general[enabled]' ) ) {
            return;
        }
        
        add_action( 'init', array( $this, 'register_rewrite_rules' ), 2 );
        add_action( 'wp', array( $this, 'output_xml_sitemap' ), 3 );

    }

    /**
     * Helper method to retrieve the maximum number of enties per XML sitemap
     *
     * @since   1.0.0
     *
     * @return  int     Maximum number of entries per XML sitemap
     */
    public function get_max_entries_per_sitemap() {

        return apply_filters( 'wp_simple_seo_sitemaps_get_max_entries_per_sitemap', 1000 );

    }

    /**
     * Registers a rewrite rule to catch all sitemap_*.xml requests, routing
     * them to a rewrite tag we then use
     *
     * @since 1.0.0
     */
    public function register_rewrite_rules() {

        // Register the rewrite tag, so we can use get_query_var()
        add_rewrite_tag( '%wp_simple_seo_sitemap%', '([^&]+)' );
        add_rewrite_tag( '%wp_simple_seo_sitemap_type%', '([^&]+)' ); // post_types, taxonomies, archives
        add_rewrite_tag( '%wp_simple_seo_sitemap_name%', '([^&]+)' ); // post type, taxonomy, date, author
        add_rewrite_tag( '%wp_simple_seo_sitemap_page%', '([0-9]+)' ); // page, for pagination

        // Register the rewrite rules
        add_rewrite_rule( 
            '^sitemap_index.xml$',
            'index.php?wp_simple_seo_sitemap=1&wp_simple_seo_sitemap_type=index',
            'top'
        );
        add_rewrite_rule( 
            '^sitemap_post_types_([^/]*)_([0-9]{1,}).xml$',
            'index.php?wp_simple_seo_sitemap=1&wp_simple_seo_sitemap_type=post_types&wp_simple_seo_sitemap_name=$matches[1]&wp_simple_seo_sitemap_page=$matches[2]',
            'top'
        );
        add_rewrite_rule( 
            '^sitemap_taxonomies_([^/]*)_([0-9]{1,}).xml$',
            'index.php?wp_simple_seo_sitemap=1&wp_simple_seo_sitemap_type=taxonomies&wp_simple_seo_sitemap_name=$matches[1]&wp_simple_seo_sitemap_page=$matches[2]',
            'top'
        );
        add_rewrite_rule( 
            '^sitemap_archives_([^/]*)_([0-9]{1,}).xml$',
            'index.php?wp_simple_seo_sitemap=1&wp_simple_seo_sitemap_type=archives&wp_simple_seo_sitemap_name=$matches[1]&wp_simple_seo_sitemap_page=$matches[2]',
            'top'
        );
        
    }

    /**
     * If the query contains the wp_simple_seo_sitemap_name rewrite tag,
     * output an XML based sitemap.
     *
     * @since 1.0.0
     */
    public function output_xml_sitemap() {

        // Get query vars
        $sitemap      = absint( get_query_var( 'wp_simple_seo_sitemap' ) );
        $sitemap_type = sanitize_text_field( get_query_var( 'wp_simple_seo_sitemap_type' ) );
        $sitemap_name = sanitize_text_field( get_query_var( 'wp_simple_seo_sitemap_name' ) );
        $sitemap_page = absint( get_query_var( 'wp_simple_seo_sitemap_page' ) );

        // Bail if we're not trying to output a sitemap
        if ( ! $sitemap ) {
            return;
        }

        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Determine whether the sitemap we're requesting is enabled by checking the meta noindex value
        $noindex_enabled = $instance->get_setting( 'meta', $sitemap_type . '[' . $sitemap_name . '][noindex]', false );
        $sitemap_enabled = ( $noindex_enabled ? false : true );

        // Allow addons to filter this before we check the sitemap type is enabled
        $sitemap_enabled = apply_filters( 'wp_simple_seo_sitemaps_output_xml_sitemap_enabled', $sitemap_enabled, $sitemap_type, $sitemap_name, $noindex_enabled );

        // Bail if we're not outputting this type of sitemap
        if ( $sitemap_type != 'index' && ! $sitemap_enabled ) {
            return;
        }

        // Build the sitemap entries
        switch ( $sitemap_type ) {
            case 'index':
                $entries = $this->build_index_sitemap();
                break;

            case 'post_types':
                $entries = $this->build_post_type_sitemap( $sitemap_name, $sitemap_page );
                break;

            case 'taxonomies':
                $entries = $this->build_taxonomy_sitemap( $sitemap_name, $sitemap_page );
                break;

            case 'archives':
                $entries = $this->build_author_sitemap();
                break;

            default:
                // Allow addons to output their sitemap
                $entries = apply_filters( 'wp_simple_seo_sitemaps_output_xml_sitemap', $sitemap_type, $sitemap_name, $sitemap_page );
                break;
        }

        // Output the sitemap entries
        if ( $sitemap_type == 'index' ) {
            $this->output_sitemap_index( $entries );
        } else {
            $this->output_sitemap( $entries );
        }
        
    }

    /**
     * Returns entries for the sitemap index, comprising of
     * the URLs where sitemaps can be accessed.
     *
     * @since 1.0.0
     *
     * @return  array   Sitemap Entries
     */
    public function build_index_sitemap() {

        // If caching is enabled, and we have data in our transient, return the data
        if ( false !== ( $entries = WP_Simple_SEO_Cache::get_instance()->get( 'sitemap_index' ) ) ) {
            return $entries;
        }

        // Get settings instance and root URL
        $instance = WP_Simple_SEO_Settings::get_instance();
        $url = get_bloginfo( 'url' );

        // Define the types of sitemap we can have
        $sitemap_types = array( 'post_types', 'taxonomies', 'archives' );

        // Allow addons to add their sitemap types
        $sitemap_types = apply_filters( 'wp_simple_seo_sitemaps_build_index_sitemap_types', $sitemap_types );

        // Iterate through each sitemap type
        $entries = array();
        foreach ( $sitemap_types as $sitemap_type ) {
            // Get the meta settings for this type (e.g. post_type, taxonomies)
            $settings = $instance->get_setting( 'meta', $sitemap_type, array() );

            foreach ( $settings as $type => $type_settings ) {
                // We'll never generate an Archives > Dates sitemap, as it's pointless
                if ( $sitemap_type == 'archives' && $type == 'date' ) {
                    continue;
                }

                // If we're on the Archives > Authors sitemap, check we're not on a single author blog, as
                // there won't be a sitemap if that's the case.
                if ( $sitemap_type == 'archives' && $type == 'author' ) {
                    $has_multiple_authors = WP_Simple_SEO_Settings::get_instance()->site_has_multiple_authors();
                    if ( ! $has_multiple_authors ) {
                        continue;
                    }
                }

                // If there's a noindex flag within these meta settings, we don't want to generate a sitemap
                // for this type.
                $single_noindex = $instance->get_setting( 'meta', $sitemap_type . '[' . $type . '][single][noindex]', false );
                $noindex        = $instance->get_setting( 'meta', $sitemap_type . '[' . $type . '][noindex]', false );
                if ( $single_noindex || $noindex ) {
                    continue;
                } 

                // Determine how many Posts or Terms exist
                switch ( $sitemap_type ) {
                    /**
                     * Post Types
                     */
                    case 'post_types':
                        // Get all published Posts IDs where noindex doesn't exist
                        // or equals 0
                        $total_entries = count( $this->get_post_type_ids( $type , 0, -1 ) );
                        break;

                    /**
                     * Taxonomy Terms
                     */
                    case 'taxonomies':
                        $total_entries = count( $this->get_taxonomy_term_ids( $type , 0, -1 ) );
                        break;

                    /**
                     * Authors
                     */
                    case 'archives':
                        $total_entries = count( $this->get_author_ids( 0, -1 ) );
                        break;

                    /**
                     * Default
                     */
                    default:
                        // Allow addons to build their own entries
                        $total_entries = apply_filters( 'wp_simple_seo_sitemaps_build_index_sitemap_total_items', 0, $sitemap_type, $type );
                        break;
                }

                // Define the total number of XML sitemaps needed.
                $total_pages = ceil( $total_entries / $this->get_max_entries_per_sitemap() );

                // If no total pages exist, we don't need a sitemap
                if ( $total_pages == 0 ) {
                    continue;
                }

                // Iterate through the total pages, building the entries array
                for ( $i = 1; $i <= $total_pages; $i++ ) {
                    $entries[] = array(
                        'loc'       => $url . '/sitemap_' . $sitemap_type . '_' . $type . '_' . $i . '.xml',
                        'lastmod'   => date( 'Y-m-d' ),
                    ); 
                }
            }
        }

        // Filter entries
        $entries = apply_filters( 'wp_simple_seo_sitemaps_build_index_sitemap_entries', $entries, $sitemap_types, $url );

        // Cache entries for future use, if caching is enabled
        WP_Simple_SEO_Cache::get_instance()->set( 'sitemap_index', $entries );
        
        // Return
        return $entries;

    }

    /**
     * Returns entries for a Post Type sitemap, comprising of all Posts
     * for the given Post Type.
     *
     * @since 1.0.0
     *
     * @param   string  $post_type  Post Type
     * @param   int     $page       Page Number
     * @return  array               Sitemap Entries (Posts)
     */
    public function build_post_type_sitemap( $post_type, $page = 0 ) {

        // If there's a noindex flag within the Post Type's meta settings, don't generate a sitemap.
        $single_noindex = WP_Simple_SEO_Settings::get_instance()->get_setting( 'meta', 'post_types[' . $post_type . '][single][noindex]', false );
        if ( $single_noindex ) {
            return false;
        }  

        // If caching is enabled, and we have data in our transient, return the data
        if ( false !== ( $entries = WP_Simple_SEO_Cache::get_instance()->get( 'sitemap_post_types_' . $post_type . '_' . $page ) ) ) {
            return $entries;
        }

        // Get Post IDs
        $post_ids = $this->get_post_type_ids( $post_type, $page, $this->get_max_entries_per_sitemap() );

        // Bail if no Posts exist
        if ( empty( $post_ids ) ) {
            return;
        }

        // For each Post, build the sitemap
        $entries = array();
        foreach ( $post_ids as $post_id ) {
            $entries[] = array(
                'loc'       => get_permalink( $post_id ),
                'lastmod'   => get_post_modified_time( 'Y-m-d', false, $post_id, false ),
                'changefreq'=> 'daily',
                'priority'  => 1,
            );
        }

        // Filter entries
        $entries = apply_filters( 'wp_simple_seo_sitemaps_build_post_type_sitemap_entries', $entries, $post_ids, $post_type, $page );

        // Cache entries for future use, if caching is enabled
        WP_Simple_SEO_Cache::get_instance()->set( 'sitemap_post_types_' . $post_type . '_' . $page, $entries );

        return $entries;

    }

    /**
     * Returns an array of published Post IDs for the given Post Type, that haven't individually
     * been set to noindex.
     *
     * If $page and $per_page parameters are set, returns the subset.
     *
     * @since   1.0.0
     *
     * @param   string  $post_type  Post Type
     * @param   int     $page       Pagination Page
     * @param   int     $per_page   Posts per Page
     * @return  array               Post IDs
     */
    private function get_post_type_ids( $post_type, $page, $per_page ) {

        // Get maximum number of entries for a single XML sitemap
        $max_entries_per_sitemap = $this->get_max_entries_per_sitemap();

        // Convert $page to a zero based index, so that offset is correctly
        $page = ( $page - 1 );

        // Setup arguments for WP_Query
        $args = array(
            'post_type'         => $post_type,
            'post_status'       => 'publish',
            'posts_per_page'    => (int) $per_page,
            'offset'            => (int) ( $page * $max_entries_per_sitemap ),
            'meta_query'        => array(
                'relation'      => 'OR',

                // Where the meta key doesn't exist, or it = 1
                array(
                    'key'       => '_wp_simple_seo_meta_noindex',
                    'compare'   => 'NOT EXISTS',
                    'value'     => '',
                ),
                array(
                    'key'       => '_wp_simple_seo_meta_noindex',
                    'value'     => 0,
                ),
            ),
            'fields' => 'ids',
        );

        // Filter arguments
        $args = apply_filters( 'wp_simple_seo_sitemaps_get_post_type_ids', $args, $post_type, $page, $per_page, $max_entries_per_sitemap );

        // Run query
        $query = new WP_Query( $args );
        
        // Return Post IDs
        return $query->posts;

    }

    /**
     * Returns entries for a Taxonomy sitemap, comprising of all Terms
     * for the given Taxonomy.
     *
     * @since   1.0.0
     *
     * @param   string  $taxonomy   Taxonomy
     * @return  array               Sitemap Entries (Terms)
     */
    public function build_taxonomy_sitemap( $taxonomy ) {

        // If there's a noindex flag within the Taxonomy's meta settings, don't generate a sitemap.
        $single_noindex = WP_Simple_SEO_Settings::get_instance()->get_setting( 'meta', 'taxonomies[' . $taxonomy . '][noindex]', false );
        if ( $single_noindex ) {
            return false;
        }

        // If caching is enabled, and we have data in our transient, return the data
        if ( false !== ( $entries = WP_Simple_SEO_Cache::get_instance()->get( 'sitemap_taxonomies_' . $taxonomy ) ) ) {
            return $entries;
        }

        // Get all Terms
        $terms = get_terms( array(
            'taxonomy' => $taxonomy,
        ) );

        // Get Taxonomy Object
        $taxonomy_object = get_taxonomy( $taxonomy );

        // Bail if no Terms exist
        if ( is_wp_error( $terms ) || count( $terms ) == 0 ) {
            return;
        }

        // For each Term, build the sitemap
        $entries = array();
        foreach ( $terms as $term ) {
            // Fetch the most recently published or updated Post belonging to this Term, to establish
            // the lastmod date.
            $query = new WP_Query( array(
                'post_type'         => $taxonomy_object->object_type,
                'tax_query'         => array(
                    array(
                        'taxonomy'  => $taxonomy,
                        'terms'     => $term,
                    ),
                ),
                'orderby'           => 'modified',
                'order'             => 'DESC',
                'posts_per_page'    => 1,
            ) );

            $last_modified_date = ( ( count( $query->posts ) == 1 ) ? $query->posts[0]->post_modified_gmt : 0 );
            
            // Add entry
            $entries[] = array(
                'loc'       => get_term_link( $term ),
                'lastmod'   => date( 'Y-m-d', strtotime( $last_modified_date ) ),
                'changefreq'=> 'daily',
                'priority'  => 1,
            );
        }

        // Filter entries
        $entries = apply_filters( 'wp_simple_seo_sitemaps_build_taxonomy_sitemap_entries', $entries, $terms );

        // Cache entries for future use, if caching is enabled
        WP_Simple_SEO_Cache::get_instance()->set( 'sitemap_taxonomies_' . $taxonomy, $entries );

        return $entries;

    }

    /**
     * Returns an array of published Term IDs for the given Taxonomy, that haven't individually
     * been set to noindex.
     *
     * If $page and $per_page parameters are set, returns the subset.
     *
     * @since   1.0.0
     *
     * @param   string  $taxonomy   Taxonomy
     * @param   int     $page       Pagination Page
     * @param   int     $per_page   Posts per Page
     * @return  array               Term IDs
     */
    private function get_taxonomy_term_ids( $taxonomy, $page, $per_page ) {

        // Get maximum number of entries for a single XML sitemap
        $max_entries_per_sitemap = $this->get_max_entries_per_sitemap();

        // Setup arguments for WP_Term_Query
        $args = array(
            'taxonomy'          => $taxonomy,
            'hide_empty'        => true,
            'number'            => (int) $per_page,
            'offset'            => (int) ( $page * $max_entries_per_sitemap ),
            'meta_query'        => array(
                'relation'      => 'OR',

                // Where the meta key doesn't exist, or it = 1
                array(
                    'key'       => '_wp_simple_seo_meta_noindex',
                    'compare'   => 'NOT EXISTS',
                    'value'     => '',
                ),
                array(
                    'key'       => '_wp_simple_seo_meta_noindex',
                    'value'     => 0,
                ),
            ),
            'fields' => 'ids',
        );

        // Filter arguments
        $args = apply_filters( 'wp_simple_seo_sitemaps_get_taxonomy_term_ids', $args, $taxonomy, $page, $per_page, $max_entries_per_sitemap );

        // Run query
        $query = new WP_Term_Query( $args );

        // Return Term IDs
        return $query->terms;

    }

    /**
     * Returns an array of author IDs who have at least one published Post.
     *
     * If $page and $per_page parameters are set, returns the subset.
     *
     * @since   1.0.3
     *
     * @param   int     $page       Pagination Page
     * @param   int     $per_page   Posts per Page
     * @return  array               Author IDs
     */
    private function get_author_ids( $page, $per_page ) {

        // Get maximum number of entries for a single XML sitemap
        $max_entries_per_sitemap = $this->get_max_entries_per_sitemap();

        // Setup arguments for WP_Term_Query
        $args = array(
            'has_published_posts'   => true, // All public post types
            'number'                => (int) $per_page,
            'offset'                => (int) ( $page * $max_entries_per_sitemap ),
            'fields'                => 'ids',
        );

        // Filter arguments
        $args = apply_filters( 'wp_simple_seo_sitemaps_get_author_ids', $args, $page, $per_page, $max_entries_per_sitemap );

        // Run query
        $query = new WP_User_Query( $args );

        // Return User IDs
        return $query->results;

    }

    /**
     * Returns entries for an Authors sitemap, comprising of all Users
     * who have at least one published Post.
     *
     * @since   1.0.0
     *
     * @return  array               Sitemap Entries (Terms)
     */
    public function build_author_sitemap() {

        // If there's only one User with published Posts, don't generate a sitemap, as it'll
        // look like duplicated content.
        $has_multiple_authors = WP_Simple_SEO_Settings::get_instance()->site_has_multiple_authors();
        if ( ! $has_multiple_authors ) {
            return false;
        }

        // If there's a noindex flag within the Author's meta settings, don't generate a sitemap.
        $single_noindex = WP_Simple_SEO_Settings::get_instance()->get_setting( 'meta', 'archives[author][noindex]', false );
        if ( $single_noindex ) {
            return false;
        }

        // Get all Users
        $users = WP_Simple_SEO_Common::get_instance()->get_users_with_published_posts();

        // Bail if no Users exist
        if ( ! $users ) {
            return;
        }

        // For each Author, build the sitemap
        $entries = array();

        foreach ( $users as $user ) {
            // Fetch the most recently published or updated Post belonging to this Author, to establish
            // the lastmod date.
            $query = new WP_Query( array(
                'post_type'         => 'any',
                'author'            => $user->ID,
                'orderby'           => 'modified',
                'order'             => 'DESC',
                'posts_per_page'    => 1,
            ) );

            $last_modified_date = ( ( count( $query->posts ) == 1 ) ? $query->posts[0]->post_modified_gmt : 0 );

            $entries[] = array(
                'loc'       => get_author_posts_url( $user->ID ),
                'lastmod'   => date( 'Y-m-d', strtotime( $last_modified_date ) ),
                'changefreq'=> 'daily',
                'priority'  => 1,
            );
        }

        // Filter entries
        $entries = apply_filters( 'wp_simple_seo_sitemaps_build_author_sitemap_entries', $entries, $users );

        // Cache entries for future use, if caching is enabled
        WP_Simple_SEO_Cache::get_instance()->set( 'sitemap_author_' . $page, $entries );

        return $entries;

    }

    /**
     * Outputs the XML for the sitemap index, which tells search engines
     * the location of all sitemaps.
     *
     * @since 1.0.0
     *
     * @param   array   $entries    Entries
     */
    private function output_sitemap_index( $entries ) {

        // Start output
        $output = '<?xml version="1.0" encoding="UTF-8"?>';
        $output .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // For each entry, build the XML
        if ( $entries != false ) {
            foreach ( $entries as $entry ) {
                $output .= '<sitemap>';
                $output .= '<loc>' . $entry['loc'] . '</loc>';
                $output .= '</sitemap>';
            }
        }

        $output .= '</sitemapindex>';

        // Filter output
        $output = apply_filters( 'wp_simple_seo_sitemaps_output_sitemap_index', $output, $entries );

        // Output
        header( 'Content-type: text/xml' );
        echo $output;
        die();

    }

    /**
     * Outputs the XML for the given sitemap entries
     *
     * @since 1.0.0
     *
     * @param   array   $entries    Entries
     */
    private function output_sitemap( $entries ) {

        // Start output
        $output = '<?xml version="1.0" encoding="UTF-8"?>';
        $output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // For each entry, build the XML
        if ( $entries != false ) {
            foreach ( $entries as $entry ) {
                $output .= '<url>';
                $output .= '<loc>' . $entry['loc'] . '</loc>';
                $output .= '<lastmod>' . $entry['lastmod'] . '</lastmod>';
                $output .= '<changefreq>' . $entry['changefreq'] . '</changefreq>';
                $output .= '<priority>' . $entry['priority'] . '</priority>';
                $output .= '</url>';
            }
        }

        $output .= '</urlset>';

        // Filter output
        $output = apply_filters( 'wp_simple_seo_sitemaps_output_sitemap', $output, $entries );

        // Output
        header( 'Content-type: text/xml' );
        echo $output;
        die();

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