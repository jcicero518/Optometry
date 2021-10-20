<?php
/**
 * title class
 * 
 * @package   WP_Simple_SEO
 * @author    WP Simple SEO
 * @version   1.0.0
 * @copyright WP Simple SEO
 */
class WP_Simple_SEO_Title {

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

        // Filter the title, whether the theme uses 4.4.+ methods or not.
        add_filter( 'pre_get_document_title', array( $this, 'get_title' ), 10 );
        add_filter( 'wp_title', array( $this, 'get_title' ), 10, 3 );

    }

    /**
     * Returns the <title> tag in the <head> of the WordPress site, 
     * depending on whether a single post type, archive, taxonomy term
     * or date based archive is being displayed.
     *
     * @since 1.0.0
     *
     * @param   string  $title  Title
     * @return  string          Title
     */
    public function get_title( $title ) {

        // Don't do anything if we're on certain sections of the site
        if ( is_feed() ) {
            return $title;
        }

        // Home
        if ( is_front_page() && is_home() ) {
            return $this->get_title_home();
        }

        // Static Home Page
        if ( is_front_page() ) {
            return $this->get_title_home();
        }

        // Static Blog Page
        if ( is_home() ) {
            return $this->get_title_archive();
        }

        // Post Type Archive
        if ( is_post_type_archive() ) {
            return $this->get_title_archive();
        }

        // Single Post Type
        if ( is_singular() ) {
            return $this->get_title_single();
        }

        // Taxonomy Term Archive
        if ( is_category() || is_tag() || is_tax() ) {
            return $this->get_title_tax();
        }

        // Date-based Archive
        if ( is_day() || is_month() || is_year() || is_time() ) {
            return $this->get_title_date();
        }

        // Author
        if ( is_author() ) {
            return $this->get_title_author();
        }

        // Search
        if ( is_search() ) {
            return $this->get_title_search();
        }

        // 404
        if ( is_404() ) {
            return $this->get_title_404();
        }

        // If we're here, we didn't meet any conditions.
        // Just return the title
        return $title;

    }

    /**
     * Outputs the title for the Home Page
     *
     * @since 1.0.0
     *
     * @return string Title
     */
    private function get_title_home() {

        // Get the post type archive we're viewing
        $object = get_queried_object();

        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Allow devs to filter the title
        $title = apply_filters( 'wp_simple_seo_get_title_home', $instance->get_setting( 'meta', 'home[title]' ) );

        // Return the title
        return $this->parse_title( $title, 'home', $object );

    }

    /**
     * Outputs the title for Post Type Archives
     *
     * @since 1.0.0
     *
     * @return string Title
     */
    private function get_title_archive() {
        
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

        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Allow devs to filter the title
        $title = apply_filters( 'wp_simple_seo_get_title_archive', $instance->get_setting( 'meta', 'post_types[' . $post_type_object->name . '][archive][title]' ) );

        // Return the title
        return $this->parse_title( $title, 'post_type_archive', $post_type_object );

    }

    /**
     * Outputs the title for Singular Post Types
     *
     * @since 1.0.0
     *
     * @return string Title
     */
    private function get_title_single() {

        // Get the post type archive we're viewing
        $post = get_queried_object();
        $post_type = $post->post_type;

        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Allow devs to filter the title
        $title = apply_filters( 'wp_simple_seo_get_title_single', $instance->get_setting( 'meta', 'post_types[' . $post_type . '][single][title]', $post->ID ) );

        // Return the title
        return $this->parse_title( $title, 'post_single', $post );

    }

    /**
     * Outputs the title for Taxonomy Terms
     *
     * @since 1.0.0
     *
     * @return string Title
     */
    private function get_title_tax() {

        // Get the taxonomy term we're viewing
        $term = get_queried_object();
        $taxonomy = $term->taxonomy;

        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Allow devs to filter the title
        $title = apply_filters( 'wp_simple_seo_get_title_tax', $instance->get_setting( 'meta', 'taxonomies[' . $taxonomy . '][title]', $term->term_id ) );

        // Return the title
        return $this->parse_title( $title, 'taxonomy', $term );

    }

    /**
     * Outputs the title for Date based Archives
     *
     * @since 1.0.0
     *
     * @return string Title
     */
    private function get_title_date() {

        global $wp_query;

        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Allow devs to filter the title
        $title = apply_filters( 'wp_simple_seo_get_title_date', $instance->get_setting( 'meta', 'archives[date][title]' ) );

        // Return the title
        return $this->parse_title( $title, 'date', $wp_query->query );

    }

    /**
     * Outputs the title for Author Archives
     *
     * @since 1.0.0
     *
     * @return string Title
     */
    private function get_title_author() {

        // Get the author we're viewing
        $author = get_queried_object();

        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Allow devs to filter the title
        $title = apply_filters( 'wp_simple_seo_get_title_date', $instance->get_setting( 'meta', 'archives[author][title]' ) );

        // Return the title
        return $this->parse_title( $title, 'author', $author );

    }

    /**
     * Outputs the title for Search Results Archives
     *
     * @since 1.0.0
     *
     * @return string Title
     */
    private function get_title_search() {

        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Allow devs to filter the title
        $title = apply_filters( 'wp_simple_seo_get_title_search', $instance->get_setting( 'meta', 'search[title]' ) );

        // Return the title
        return $this->parse_title( $title, 'search', sanitize_text_field( $_REQUEST['s'] ) );

    }

    /**
     * Outputs meta tags for 404 Not Found screen
     *
     * @since 1.0.0
     *
     * @return string Title
     */
    private function get_title_404() {

        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Allow devs to filter the title
        $title = apply_filters( 'wp_simple_seo_get_title_404', $instance->get_setting( 'meta', 'four04[title]' ) );

        // Return the title
        return $this->parse_title( $title, '404' );

    }

    /**
     * Returns the title tag, parsed if necessary.
     *
     * @since 1.0.0
     *
     * @param   string  $title      Title
     * @param   string  $screen     Screen (home | post_type_archive | post_single | taxonomy | author | date | search | 404)
     * @param   mixed   $object     Object (WP_Post | WP_PostType | WP_Term | WP_User | array | false )
     */
    private function parse_title( $title, $screen = 'home', $object = false ) {

        // Parse title
        $title = WP_Simple_SEO_Parser::get_instance()->parse_tags( $title, $screen, $object );

        // Strip HTML and slashes
        $title = stripslashes( strip_tags( $title ) );
        
        // Return filtered result
        return apply_filters( 'wp_simple_seo_get_title', $title, $object );

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