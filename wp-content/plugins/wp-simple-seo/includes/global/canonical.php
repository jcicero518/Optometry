<?php
/**
 * Canonical class
 * 
 * @package   WP_Simple_SEO
 * @author    WP Simple SEO
 * @version   1.0.0
 * @copyright WP Simple SEO
 */
class WP_Simple_SEO_Canonical {

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

        add_action( 'wp_head', array( $this, 'maybe_output_canonical_links' ), 1 );

    }

    /**
     * Outputs Canonical Links
     *
     * @since   1.0.0
     */
    public function maybe_output_canonical_links() {

        // Don't do anything if we're on certain sections of the site
        if ( is_feed() ) {
            return;
        }

        // Home
        if ( is_front_page() && is_home() ) {
            return $this->output_canonical_links_home();
        }

        // Static Home Page
        if ( is_front_page() ) {
            return $this->output_canonical_links_single();
        }

        // Static Blog Page
        if ( is_home() ) {
            return $this->output_canonical_links_archive();
        }

        // Post Type Archive
        if ( is_post_type_archive() ) {
            return $this->output_canonical_links_archive();
        }

        // Single Post Type
        if ( is_singular() ) {
            $this->output_canonical_links_single();
            return;
        }

        // Taxonomy Term Archive
        if ( is_category() || is_tag() || is_tax() ) {
            $this->output_canonical_links_taxonomy();
            return;
        }

        // Date-based Archive
        if ( is_day() || is_month() || is_year() || is_time() ) {
            $this->output_canonical_links_date();
            return;
        }

        // Author
        if ( is_author() ) {
            $this->output_canonical_links_author();
            return;
        }

        // Search
        if ( is_search() ) {
            $this->output_canonical_links_search();
            return;
        }

    }

    /**
     * Outputs canonical links for the Home Page
     *
     * @since 1.0.0
     */
    private function output_canonical_links_home() {

        // Get canonical links
        $canonical_links = array(
            'canonical' => get_bloginfo( 'url' ),
        );
        
        // Allow devs to filter the canonical links
        $links = apply_filters( 'wp_simple_seo_canonical_output_canonical_links_home', $canonical_links );

        // Output the canonical links
        $this->output_canonical_links( $canonical_links, 'home' );

    }

    /**
     * Outputs canonical links for Post Type Archives
     *
     * @TODO
     *
     * @since 1.0.0
     */
    private function output_canonical_links_archive() {

        // Get the post type archive we're viewing
        $object = get_queried_object();

        // Depending on the object we've received, determine the post type we're on
        $post_type_object = '';
        if ( is_a( $object, 'WP_Post' ) ) {
            // Post Object
            // If we're viewing a Page, it might be the Posts Page
            if ( $object->post_type == 'page' && get_option( 'page_for_posts' ) == $object->ID ) {
                $post_type_object = get_post_type_object( 'post' );
            } else {
                $post_type_object = get_post_type_object( $object->post_type );
            }
        } else {
            // Post Type Object
            $post_type_object = $object;
        }

        // If we couldn't determine which post type we are viewing, bail
        if ( empty( $post_type_object ) ) {
            return;
        }

        // Get canonical links
        $canonical_links = array(
            'canonical' => get_post_type_archive_link( $post_type_object->name ),
        );
        
        // Allow devs to filter the canonical links
        $canonical_links = apply_filters( 'wp_simple_seo_canonical_output_canonical_links_archive', $canonical_links, $post_type_object );

        // Output the canonical links
        $this->output_canonical_links( $canonical_links, 'post_type_archive', $post_type_object );

    }

    /**
     * Outputs canonical links for Singular Post Types
     *
     * @since 1.0.0
     */
    private function output_canonical_links_single() {

        global $wp_query;

        // Get the post type archive we're viewing
        $post = get_queried_object();

        // Build the base permalink
        $base_url = get_permalink( $post->ID );

        // Determine if the Post content is split using nextpage
        $pagination_data = WP_Simple_SEO_Common::get_instance()->get_single_post_pagination_data( $post->post_content );

        // Generate canonical, prev and next links
        $canonical_links = $this->generate_canonical_links( $base_url, $pagination_data['total'], $pagination_data['current'] );

        // If the Post's settings specify a canonical link, this should be used instead
        $canonical_override = WP_Simple_SEO_Settings::get_instance()->get_setting( 'meta', 'post_types[' . $post->post_type . '][single][canonical]', $post->ID );
        if ( ! empty( $canonical_override ) ) {
            $canonical_links['canonical'] = $canonical_override;
        }

        // Allow devs to filter the canonical links
        $canonical_links = apply_filters( 'wp_simple_seo_canonical_output_canonical_links_single', $canonical_links, $post );

        // Output the canonical links
        $this->output_canonical_links( $canonical_links, 'post_single', $post );

    }

    /**
     * Outputs canonical links for Taxonomy Terms
     *
     * @since 1.0.0
     */
    private function output_canonical_links_taxonomy() {

        global $wp_query;

        // Get the taxonomy term we're viewing
        $term = get_queried_object();

        // Build the base permalink
        $base_url = get_term_link( $term );

        // Get number of pages for this taxonomy term archive, and the current page
        $number_of_pages = $wp_query->max_num_pages;
        $current_page = ( get_query_var( 'paged' ) > 0 ? get_query_var( 'paged' ) : 1 );

        // Generate canonical, prev and next links
        $canonical_links = $this->generate_canonical_links( $base_url, $number_of_pages, $current_page );

        // Allow devs to filter the canonical links
        $canonical_links = apply_filters( 'wp_simple_seo_canonical_output_canonical_links_taxonomy', $canonical_links, $term );

        // Output the canonical links
        $this->output_canonical_links( $canonical_links, 'taxonomy', $term );

    }

    /**
     * Outputs canonical links for Date based Archives
     *
     * @since 1.0.0
     */
    private function output_canonical_links_date() {

        global $wp_query;

        // Get the year, month and day for the date archive we're viewing
        // Some of these won't be populated e.g. if we're viewing a year based archive,
        // month and date will be empty.
        $year   = get_query_var( 'year' );
        $month  = get_query_var( 'month' );
        $day    = get_query_var( 'day' );

        // Build the base permalink depending on which of the above are present
        if ( ! empty( $day ) ) {
            // Day Link e.g. 2016/12/01
            $base_url = get_day_link( $year, $month, $day );
        } else if ( ! empty( $month ) ) {
            // Month Link e.g. 2016/12
            $base_url = get_month_link( $year, $month );
        } else {
            // Year Link e.g. 2016
            $base_url = get_year_link( $year );
        }
        
        // Get number of pages for this date based archive, and the current page
        $number_of_pages = $wp_query->max_num_pages;
        $current_page = ( get_query_var( 'paged' ) > 0 ? get_query_var( 'paged' ) : 1 );

        // Generate canonical, prev and next links
        $canonical_links = $this->generate_canonical_links( $base_url, $number_of_pages, $current_page );

        // Allow devs to filter the canonical links
        $canonical_links = apply_filters( 'wp_simple_seo_canonical_output_canonical_links_date', $canonical_links, $wp_query->query );

        // Output the canonical links
        $this->output_canonical_links( $canonical_links, 'date', $wp_query->query );

    }

    /**
     * Outputs canonical links for Author Archives
     *
     * @since 1.0.0
     */
    private function output_canonical_links_author() {

        global $wp_query;

        // Get the author we're viewing
        $author = get_queried_object();

        // Build the base permalink
        $base_url = get_author_posts_url( $author->ID );

        // Get number of pages for this author archive, and the current page
        $number_of_pages = $wp_query->max_num_pages;
        $current_page = ( get_query_var( 'paged' ) > 0 ? get_query_var( 'paged' ) : 1 );

        // Generate canonical, prev and next links
        $canonical_links = $this->generate_canonical_links( $base_url, $number_of_pages, $current_page );

        // Allow devs to filter the canonical links
        $canonical_links = apply_filters( 'wp_simple_seo_canonical_output_canonical_links_author', $canonical_links, $author );

        // Output the canonical links
        $this->output_canonical_links( $canonical_links, 'author', $author );

    }

    /**
     * Outputs canonical links for Search Results Archives
     *
     * @since 1.0.0
     */
    private function output_canonical_links_search() {

        global $wp_query;

        // Get queried object
        $search_term = sanitize_text_field( $_REQUEST['s'] );

        // Build the base permalink
        $base_url = get_bloginfo( 'url' );

        // Get number of pages for this author archive, and the current page
        $number_of_pages = $wp_query->max_num_pages;
        $current_page = ( get_query_var( 'paged' ) > 0 ? get_query_var( 'paged' ) : 1 );

        // Generate canonical, prev and next links
        $canonical_links = $this->generate_canonical_links( $base_url, $number_of_pages, $current_page );

        // Allow devs to filter the canonical links
        $canonical_links = apply_filters( 'wp_simple_seo_canonical_output_canonical_links_search', $canonical_links, $search_term );

        // Output the canonical links
        $this->output_canonical_links( $canonical_links, 'search', $search_term );

    }

    /**
     * Generates canonical links for canonical, next and prev, depending on the supplied
     * number of pages and current page the visitor is on.
     *
     * @since   1.0.0
     *
     * @param   string  $base_url           Base URL (pagination will be appended to this if necessary)
     * @param   int     $number_of_pages    Number of Pages
     * @param   int     $current_page       Current Page
     * @return  array                       Canonical Links
     */
    private function generate_canonical_links( $base_url, $number_of_pages, $current_page ) {

        // Setup the array
        $canonical_links = array();

        // If there's more than one page, output prev/next canonical links
        if ( $number_of_pages > 1 ) {
            // Depending on the page we're viewing, build the necessary canonical links
            if ( $current_page == 1 ) {
                // Only output 'next' link if we're on the first page
                $canonical_links['canonical']   = trailingslashit( $base_url );
                $canonical_links['next']        = trailingslashit( $base_url . '/page/' . ( $current_page + 1 ) );
            } else if ( $current_page == $number_of_pages ) {
                // Only output 'prev' link if we're on the final page
                $canonical_links['canonical']   = trailingslashit( $base_url . '/page/' . ( $current_page ) );
                $canonical_links['prev']        = trailingslashit( $base_url . '/page/' . ( $current_page - 1 ) );  
            } else {
                // Output both 'next' and 'prev' links, as we're on a middle page
                $canonical_links['canonical']   = trailingslashit( $base_url . '/page/' . ( $current_page ) );
                $canonical_links['next']        = trailingslashit( $base_url . '/page/' . ( $current_page + 1 ) );
                $canonical_links['prev']        = trailingslashit( $base_url . '/page/' . ( $current_page - 1 ) );  
            }
        } else {
            // Just build the date based archive URL
            $canonical_links['canonical']       = trailingslashit( $base_url );
        }

        // For each canonical link, append the search term if it's been supplied
        if ( isset( $_REQUEST['s'] ) ) {
            foreach ( $canonical_links as $key => $value ) {
                $canonical_links[ $key ] = add_query_arg( array(
                    's' => sanitize_text_field( $_REQUEST['s'] ),
                ), $value );
            }
        }

        // Allow devs to filter the canonical links
        $canonical_links = apply_filters( 'wp_simple_seo_canonical_generate_canonical_links', $canonical_links, $number_of_pages, $current_page );

        // Return
        return $canonical_links;

    }

    /**
     * Outputs canonical links based on the given key/value array pairs.
     *
     * @since   1.0.0
     *
     * @param   array   $canonical_links    Canonical Links
     * @param   string  $screen             Screen (home | post_type_archive | post_single | taxonomy | author | date | search | 404)
     * @param   mixed   $object             Object (WP_Post | WP_PostType | WP_Term | WP_User | array | false )
     */
    private function output_canonical_links( $canonical_links, $screen = 'home', $object = false ) {

        // Allow devs to filter the final array of canonical links before they're output
        $canonical_links = apply_filters( 'wp_simple_seo_canonical_output_canonical_links', $canonical_links, $screen, $object );

        // Don't do anything if the tags aren't an array
        if ( ! is_array( $canonical_links ) ) {
            return;
        }
        if ( count( $canonical_links ) == 0 ) {
            return;
        }

        // Build output
        foreach ( $canonical_links as $rel => $href ) {
            // If the link has no value, skip it
            if ( empty( $href ) ) {
                continue;
            }

            // Output the canonical tag
            echo '<link rel="' . $rel . '" href="' . $href . '" />';
            echo "\n";
        }

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