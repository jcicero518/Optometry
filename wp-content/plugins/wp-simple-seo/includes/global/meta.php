<?php
/**
 * Meta class
 * 
 * @package   WP_Simple_SEO
 * @author    WP Simple SEO
 * @version   1.0.0
 * @copyright WP Simple SEO
 */
class WP_Simple_SEO_Meta {

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

        // Remove some WordPress actions, as we'll set our own
        remove_action( 'wp_head', 'noindex', 1 );

        // Add our meta actions
        add_action( 'wp_head', array( $this, 'maybe_output_meta_tags' ), 1 );

    }

    /**
     * Outputs meta tags in the <head> of the WordPress site, if required,
     * depending on whether a single post type, archive, taxonomy term
     * or date based archive is being displayed.
     *
     * @since 1.0.0
     */
    public function maybe_output_meta_tags() {

        // Don't do anything if we're on certain sections of the site
        if ( is_feed() ) {
            return;
        }

        // Home: Latest Posts
        if ( is_front_page() && is_home() ) {
            $this->output_meta_tags_home();
            return;
        }

        // Home: Static Page
        if ( is_front_page() && ! is_home() ) {
            $this->output_meta_tags_home();
            return;
        }

        // Static Blog Page
        if ( ! is_front_page() && is_home() ) {
            $this->output_meta_tags_archive();
            return;
        }

        // Post Type Archive
        if ( is_post_type_archive() ) {
            $this->output_meta_tags_archive();
            return;
        }

        // Single Post Type
        if ( is_singular() ) {
            $this->output_meta_tags_single();
            return;
        }

        // Taxonomy Term Archive
        if ( is_category() || is_tag() || is_tax() ) {
            $this->output_meta_tags_taxonomy();
            return;
        }

        // Date-based Archive
        if ( is_day() || is_month() || is_year() || is_time() ) {
            $this->output_meta_tags_date();
            return;
        }

        // Author
        if ( is_author() ) {
            $this->output_meta_tags_author();
            return;
        }

        // Search
        if ( is_search() ) {
            $this->output_meta_tags_search();
            return;
        }

        // 404
        if ( is_404() ) {
            $this->output_meta_tags_404();
            return;
        }

    }

    /**
     * Outputs meta tags for the Home Page
     *
     * @since 1.0.0
     */
    private function output_meta_tags_home() {

        // Get queried object
        $object = get_queried_object();
        
        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Get meta tags
        $meta_tags = array(
            'description'   => array(
                'tag'   => 'name',
                'key'   => 'description',
                'value' => $instance->get_setting( 'meta', 'home[description]' ),
            ),
            'noindex'   => array(
                'tag'   => 'name',
                'key'   => 'robots',
                'value' => ( ( $instance->get_setting( 'meta', 'home[noindex]' ) || get_option( 'blog_public' ) == '0' ) ? 'noindex' : '' ),
            ),

            // For the Home Page, also output some other meta tags
            'bing'   => array(
                'tag'   => 'name',
                'key'   => 'msvalidate.01',
                'value' => $instance->get_setting( 'general', 'webmaster_tools[bing_verification]' ),
            ),
            'google'   => array(
                'tag'   => 'name',
                'key'   => 'google-site-verification',
                'value' => $instance->get_setting( 'general', 'webmaster_tools[google_verification]' ),
            ),

            // No ODP/DMOZ/Ydir
            'noodp'   => array(
                'tag'   => 'name',
                'key'   => 'robots',
                'value' => ( $instance->get_setting( 'meta', 'general[noodp]' ) ? 'noodp' : '' ),
            ),
            'noydir'   => array(
                'tag'   => 'name',
                'key'   => 'robots',
                'value' => ( $instance->get_setting( 'meta', 'general[noydir]' ) ? 'noydir' : '' ),
            ),
        );

        // Bing: If the entire meta tag has been pasted in as the value, just extract the content
        if ( strpos( $meta_tags['bing']['value'], 'content=' ) !== false ) {
            $meta_tags['bing']['value'] = substr( $meta_tags['bing']['value'], strpos( $meta_tags['bing']['value'], 'content="' ) + 9 );
            $meta_tags['bing']['value'] = str_replace( '"', '', $meta_tags['bing']['value'] );
            $meta_tags['bing']['value'] = str_replace( '/', '', $meta_tags['bing']['value'] );
            $meta_tags['bing']['value'] = str_replace( '>', '', $meta_tags['bing']['value'] );
            $meta_tags['bing']['value'] = trim( $meta_tags['bing']['value'] );
        }

        // Google Sitelinks Search Box
        if ( ! $instance->get_setting( 'general', 'sitelinks_searchbox[enabled]' ) ) {
            $meta_tags['google_sitelinks'] = array(
                'tag'   => 'name',
                'key'   => 'google',
                'value' => 'nositelinkssearchbox',
            );
        }
        
        // Allow devs to filter the meta tags
        $meta_tags = apply_filters( 'wp_simple_seo_output_meta_tags_home', $meta_tags, $object );

        // Output the meta tags
        $this->output_meta_tags( $meta_tags, 'home', $object );

    }

    /**
     * Outputs meta tags for Post Type Archives
     *
     * @since 1.0.0
     */
    private function output_meta_tags_archive() {

        // Get the post type archive we're viewing
        $object = get_queried_object();
        $post_type_object = WP_Simple_SEO_Common::get_instance()->get_post_type_archive_post_type();
        
        // If we couldn't determine which post type we are viewing, bail
        if ( empty( $post_type_object ) ) {
            return;
        }

        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Get meta tags
        $meta_tags = array(
            'description'   => array(
                'tag'   => 'name',
                'key'   => 'description',
                'value' => $instance->get_setting( 'meta', 'post_types[' . $post_type_object->name . '][archive][description]' ),
            ),
            'noindex'   => array(
                'tag'   => 'name',
                'key'   => 'robots',
                'value' => ( ( $instance->get_setting( 'meta', 'post_types[' . $post_type_object->name . '][archive][noindex]' ) || get_option( 'blog_public' ) == '0' ) ? 'noindex' : '' ),
            ),
        );
        
        // Allow devs to filter the meta tags
        $meta_tags = apply_filters( 'wp_simple_seo_output_meta_tags_archive', $meta_tags, $post_type_object );

        // Output the meta tags
        $this->output_meta_tags( $meta_tags, 'post_type_archive', $post_type_object );

    }

    /**
     * Outputs meta tags for Singular Post Types
     *
     * @since 1.0.0
     */
    private function output_meta_tags_single() {

        // Get the post type archive we're viewing
        $post = get_queried_object();
        $post_type = $post->post_type;

        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Get meta tags
        $meta_tags = array(
            'description'   => array(
                'tag'   => 'name',
                'key'   => 'description',
                'value' => $instance->get_setting( 'meta', 'post_types[' . $post_type . '][single][description]', $post->ID ),
            ),
            'noindex'   => array(
                'tag'   => 'name',
                'key'   => 'robots',
                'value' => ( ( $instance->get_setting( 'meta', 'post_types[' . $post_type . '][single][noindex]', $post->ID ) || get_option( 'blog_public' ) == '0' ) ? 'noindex' : '' ),
            ),
            'nofollow'   => array(
                'tag'   => 'name',
                'key'   => 'robots',
                'value' => ( $instance->get_setting( 'meta', 'post_types[' . $post_type . '][single][nofollow]', $post->ID ) ? 'nofollow' : '' ),
            ),
            'noimageindex'   => array(
                'tag'   => 'name',
                'key'   => 'robots',
                'value' => ( $instance->get_setting( 'meta', 'post_types[' . $post_type . '][single][noimageindex]', $post->ID ) ? 'noimageindex' : '' ),
            ),  
        );
        
        // Allow devs to filter the meta tags
        $meta_tags = apply_filters( 'wp_simple_seo_output_meta_tags_single', $meta_tags, $post );

        // Output the meta tags
        $this->output_meta_tags( $meta_tags, 'post_single', $post );

    }

    /**
     * Outputs meta tags for Taxonomy Terms
     *
     * @since 1.0.0
     */
    private function output_meta_tags_taxonomy() {

        // Get the taxonomy term we're viewing
        $term = get_queried_object();

        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Get meta tags
        $meta_tags = array(
            'description'   => array(
                'tag'   => 'name',
                'key'   => 'description',
                'value' => $instance->get_setting( 'meta', 'taxonomies[' . $term->taxonomy . '][description]', $term->term_id ),
            ),
            'noindex'   => array(
                'tag'   => 'name',
                'key'   => 'robots',
                'value' => ( ( $instance->get_setting( 'meta', 'taxonomies[' . $term->taxonomy . '][noindex]', $term->term_id ) || get_option( 'blog_public' ) == '0' ) ? 'noindex' : '' ),
            ),
        );

        // Allow devs to filter the meta tags
        $meta_tags = apply_filters( 'wp_simple_seo_output_meta_tags_taxonomy', $meta_tags, $term );

        // Output the meta tags
        $this->output_meta_tags( $meta_tags, 'taxonomy', $term );

    }

    /**
     * Outputs meta tags for Date based Archives
     *
     * @since 1.0.0
     */
    private function output_meta_tags_date() {

        global $wp_query;

        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Get meta tags
        $meta_tags = array(
            'description'   => array(
                'tag'   => 'name',
                'key'   => 'description',
                'value' => $instance->get_setting( 'meta', 'archives[date][description]' ),
            ),
            'noindex'   => array(
                'tag'   => 'name',
                'key'   => 'robots',
                'value' => ( ( $instance->get_setting( 'meta', 'archives[date][noindex]' ) || get_option( 'blog_public' ) == '0' ) ? 'noindex' : '' ),
            ),
        );

        // If this site only has one author with published Posts,
        // always noindex the date based archive.
        if ( ! $instance->site_has_multiple_authors() ) {
            $meta_tags['noindex']['value'] = 'noindex';
        }

        // Allow devs to filter the meta tags
        $meta_tags = apply_filters( 'wp_simple_seo_output_meta_tags_date', $meta_tags, $wp_query->query );

        // Output the meta tags
        $this->output_meta_tags( $meta_tags, 'date', $wp_query->query );

    }

    /**
     * Outputs meta tags for Author Archives
     *
     * @since 1.0.0
     */
    private function output_meta_tags_author() {

        // Get the author we're viewing
        $author = get_queried_object();

        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Get meta tags
        $meta_tags = array(
            'description'   => array(
                'tag'   => 'name',
                'key'   => 'description',
                'value' => $instance->get_setting( 'meta', 'archives[author][description]' ),
            ),
            'noindex'   => array(
                'tag'   => 'name',
                'key'   => 'robots',
                'value' => ( ( $instance->get_setting( 'meta', 'archives[author][noindex]' ) || get_option( 'blog_public' ) == '0' ) ? 'noindex' : '' ),
            ),
        );

        // If this site only has one author with published Posts,
        // always noindex the author archive.
        if ( ! $instance->site_has_multiple_authors() ) {
            $meta_tags['noindex']['value'] = 'noindex';
        }

        // Allow devs to filter the meta tags
        $meta_tags = apply_filters( 'wp_simple_seo_output_meta_tags_author', $meta_tags, $author );

        // Output the meta tags
        $this->output_meta_tags( $meta_tags, 'author', $author );

    }

    /**
     * Outputs meta tags for Search Results Archives
     *
     * @since 1.0.0
     */
    private function output_meta_tags_search() {

        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Get meta tags
        $meta_tags = array(
            'description'   => array(
                'tag'   => 'name',
                'key'   => 'description',
                'value' => $instance->get_setting( 'meta', 'search[description]' ),
            ),
            'noindex'   => array(
                'tag'   => 'name',
                'key'   => 'robots',
                'value' => ( ( $instance->get_setting( 'meta', 'search[noindex]' ) || get_option( 'blog_public' ) == '0' ) ? 'noindex' : '' ),
            ),
        );

        // Allow devs to filter the meta tags
        $meta_tags = apply_filters( 'wp_simple_seo_output_meta_tags_search', $meta_tags, sanitize_text_field( $_REQUEST['s'] ) );

        // Output the meta tags
        $this->output_meta_tags( $meta_tags, 'search', sanitize_text_field( $_REQUEST['s'] ) );

    }

    /**
     * Outputs meta tags for 404 Not Found screen
     *
     * @since 1.0.0
     */
    private function output_meta_tags_404() {

        // Get meta tags
        // Always noindex 404 pages
        $meta_tags = array(
            'noindex'   => array(
                'tag'   => 'name',
                'key'   => 'robots',
                'value' => 'noindex',
            ),
        );

        // Allow devs to filter the meta tags
        $meta_tags = apply_filters( 'wp_simple_seo_output_meta_tags_404', $meta_tags );

        // Output the meta tags
        $this->output_meta_tags( $meta_tags, '404' );

    }

    /**
     * Outputs meta tags based on the given key/value array pairs.
     *
     * @since 1.0.0
     *
     * @param   array   $meta_tags  Meta Tags
     * @param   string  $screen     Screen (home | post_type_archive | post_single | taxonomy | author | date | search | 404)
     * @param   mixed   $object     Object (WP_Post | WP_PostType | WP_Term | WP_User | array | false )
     */
    private function output_meta_tags( $meta_tags, $screen = 'home', $object = false ) {

        // Don't do anything if the tags aren't an array
        if ( ! is_array( $meta_tags ) ) {
            return;
        }

        // Iterate through meta tags, concatenating by key
        // This ensures that multiple meta robot tag directives are concatenated
        $meta = array();
        foreach ( $meta_tags as $meta_tag ) {
            // If the meta tag's value is blank, skip
            if ( empty( $meta_tag['value'] ) ) {
                continue;
            }

            // Parse value, which might contain {template_tags}
            if ( $screen != false ) {
                $meta_tag['value'] = WP_Simple_SEO_Parser::get_instance()->parse_tags( $meta_tag['value'], $screen, $object );
            }

            // Add this meta tag to the array

            if ( ! isset( $meta[ $meta_tag[ 'key' ] ] ) ) {
                $meta[ $meta_tag[ 'key' ] ] = array(
                    'tag'   => $meta_tag['tag'],
                    'key'   => $meta_tag['key'],
                    'value' => array(
                        $meta_tag['value'], 
                    ),
                );
            } else {
                // Array for this meta tag is already setup; just add the value
                $meta[ $meta_tag[ 'key' ] ]['value'][] = $meta_tag['value'];  
            }
        }

        // Output meta tags
        foreach ( $meta as $meta_tag ) {
            echo '<meta ' . $meta_tag['tag'] . '="' . $meta_tag['key'] . '" content="' . stripslashes( strip_tags( implode( ',', $meta_tag['value'] ) ) ) . '" />';
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