<?php
/**
 * Social class
 * 
 * @package   WP_Simple_SEO
 * @author    WP Simple SEO
 * @version   1.0.0
 * @copyright WP Simple SEO
 */
class WP_Simple_SEO_Social {

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
        if ( ! WP_Simple_SEO_Settings::get_instance()->get_setting( 'social', 'general[enabled]' ) ) {
            return;
        }

        // Filter the meta tags generated in includes/global/meta.php, appending
        // social meta tags where applicable.
        add_filter( 'wp_simple_seo_output_meta_tags_home',      array( $this, 'output_meta_tags_home' ), 10, 2 );
        add_filter( 'wp_simple_seo_output_meta_tags_archive',   array( $this, 'output_meta_tags_archive' ), 10, 2 );
        add_filter( 'wp_simple_seo_output_meta_tags_single',    array( $this, 'output_meta_tags_single' ), 10, 2 );
        add_filter( 'wp_simple_seo_output_meta_tags_taxonomy',  array( $this, 'output_meta_tags_taxonomy' ), 10, 2 );
        add_filter( 'wp_simple_seo_output_meta_tags_date',      array( $this, 'output_meta_tags_date' ), 10, 2 );
        add_filter( 'wp_simple_seo_output_meta_tags_author',    array( $this, 'output_meta_tags_author' ), 10, 2 );
        add_filter( 'wp_simple_seo_output_meta_tags_search',    array( $this, 'output_meta_tags_search' ), 10, 2 );

    }

    /**
     * Returns an array of meta tags for social sharing
     *
     * @since 1.0.0
     *
     * @param   string  $title          Title
     * @param   string  $description    Description
     * @param   string  $type           Type
     * @param   string  $url            Page URL
     * @param   string  $image_url      Image URL
     * @return  array                   Meta Tags
     */
    private function get_social_tags( $title, $description, $type, $url = '', $image_url = '' ) {

        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Get the default image URL, if no image URL was specified
        if ( empty( $image_url ) ) {
            $default_image_id = $instance->get_setting( 'social', 'open_graph[default_image]' );
            if ( ! empty( $default_image_id ) ) {
                $default_image = wp_get_attachment_image_src( $default_image_id, 'full' );
                if ( $default_image !== false ) {
                    $image_url = $default_image[0];
                }
            }
        }

        // Define social meta tags
        $social_tags = array(
            // OpenGraph (Facebook, Pinterest)
            // See: http://ogp.me
            'og_title' => array(
                'tag'   => 'property',
                'key'   => 'og:title',
                'value' => $title,
            ),
            'og_type'  => array(
                'tag'   => 'property',
                'key'   => 'og:type',
                'value' => $type,
            ),
            'og_description' => array(
                'tag'   => 'property',
                'key'   => 'og:description',
                'value' => $description,
            ),
            'og_url' => array(
                'tag'   => 'property',
                'key'   => 'og:url',
                'value' => $url,
            ),
            'og_image' => array(
                'tag'   => 'property',
                'key'   => 'og:image',
                'value' => $image_url,
            ),
            'og:locale' => array(
                'tag'   => 'property',
                'key'   => 'og:locale',
                'value' => get_locale(),
            ),
            'og_site_name' => array(
                'tag'   => 'property',
                'key'   => 'og:site_name',
                'value' => get_bloginfo( 'name' ),
            ),

            // Twitter
            'twitter_card' => array(
                'tag'   => 'property',
                'key'   => 'twitter:card',
                'value' => $instance->get_setting( 'social', 'twitter[card_type]' ),
            ),
            'twitter_site' => array(
                'tag'   => 'property',
                'key'   => 'twitter:site',
                'value' => $instance->get_setting( 'social', 'twitter[username]' ),
            ),
            'twitter_title' => array(
                'tag'   => 'property',
                'key'   => 'twitter:title',
                'value' => $title,
            ),
            'twitter_description' => array(
                'tag'   => 'property',
                'key'   => 'twitter:description',
                'value' => $description,
            ),
            'twitter_image' => array(
                'tag'   => 'property',
                'key'   => 'twitter:image',
                'value' => $image_url,
            ),
            'twitter_image_alt' => array(
                'tag'   => 'property',
                'key'   => 'twitter:image:alt',
                'value' => '',
            ),
        );

        // Allow devs to filter the social tags
        $social_tags = apply_filters( 'wp_simple_seo_social_get_social_tags', $social_tags, $title, $description, $type, $url, $image_url );

        return $social_tags;

    }

    /**
     * Outputs meta tags for the Home Page
     *
     * @since   1.0.0
     *
     * @param   array   $meta_tags  Existing Meta Tags
     * @param   object  $object     Object (WP_Post)
     * @return  array               Meta Tags
     */
    public function output_meta_tags_home( $meta_tags, $object ) {

        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Do nothing if social metadata is not enabled
        if ( ! $instance->get_setting( 'social', 'general[enabled]' ) ) {
            return $meta_tags;
        }

        // Get image, if the WP_Post is populated
        if ( ! is_null( $object ) ) {
            $image_url = wp_get_attachment_url( get_post_thumbnail_id( $object->ID ) ); 
        } else {
            $image_url = '';
        }

        // Get social tags
        $social_tags = $this->get_social_tags( 
            $instance->get_setting( 'meta', 'home[title]' ), 
            $instance->get_setting( 'meta', 'home[description]' ), 
            'website',
            get_bloginfo( 'url' ),
            $image_url
        );

        // Allow devs to filter the social tags
        $social_tags = apply_filters( 'wp_simple_seo_social_output_meta_tags_home', $social_tags, $meta_tags, $object );
        
        // Merge tags
        $meta_tags = array_merge( $meta_tags, $social_tags );

        // Return
        return $meta_tags;

    }

    /**
     * Outputs meta tags for Post Type Archives
     *
     * @since   1.0.0
     *
     * @param   array       $meta_tags          Existing Meta Tags
     * @param   WP_PostType $post_type_object   Post Type
     * @return  array                           Meta Tags
     */
    public function output_meta_tags_archive( $meta_tags, $post_type_object ) {

        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Do nothing if social metadata is not enabled
        if ( ! $instance->get_setting( 'social', 'general[enabled]' ) ) {
            return $meta_tags;
        }

        // Get social tags
        $social_tags = $this->get_social_tags(
            $instance->get_setting( 'meta', 'post_types[' . $post_type_object->name . '][archive][title]' ),
            $instance->get_setting( 'meta', 'post_types[' . $post_type_object->name . '][archive][description]' ),
            'website',
            get_post_type_archive_link( $post_type_object->name )
        );

        // Allow devs to filter the social tags
        $social_tags = apply_filters( 'wp_simple_seo_social_output_meta_tags_archive', $social_tags, $meta_tags, $post_type_object );
        
        // Merge tags
        $meta_tags = array_merge( $meta_tags, $social_tags );

        // Return
        return $meta_tags;

    }

    /**
     * Outputs meta tags for Singular Post Types
     *
     * @since   1.0.0
     *
     * @param   array   $meta_tags  Existing Meta Tags
     * @param   WP_Post $post       WordPress Post
     * @return  array               Meta Tags
     */
    public function output_meta_tags_single( $meta_tags, $post ) {

        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Do nothing if social metadata is not enabled
        if ( ! $instance->get_setting( 'social', 'general[enabled]' ) ) {
            return $meta_tags;
        }

        // Get social tags
        $social_tags = $this->get_social_tags(
            $instance->get_setting( 'meta', 'post_types[' . $post->post_type . '][single][title]', $post->ID ),
            $instance->get_setting( 'meta', 'post_types[' . $post->post_type . '][single][description]', $post->ID ),
            'article',
            get_permalink( $post ),
            wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) )
        );

        // Add some additional Open Graph metadata for Articles
        $social_tags['article_published_time'] = array(
            'tag'   => 'property',
            'key'   => 'article:published_time',
            'value' => date( 'Y-m-dTH:i:s', strtotime( $post->post_date ) ),
        );
        $social_tags['article_modified_time'] = array(
            'tag'   => 'property',
            'key'   => 'article:modified_time',
            'value' => date( 'Y-m-dTH:i:s', strtotime( $post->post_modified ) ),
        );
        $social_tags['article_author'] = array(
            'tag'   => 'property',
            'key'   => 'article:section',
            'value' => get_author_posts_url( $post->post_author ),
        ); 
        $social_tags['article_section'] = array(
            'tag'   => 'property',
            'key'   => 'article:section',
            'value' => '',
        );
        $social_tags['article_tag'] = array(
            'tag'   => 'property',
            'key'   => 'article:tag',
            'value' => '',
        );

        // Publisher
        $social_tags['article_publisher'] = array(
            'tag'   => 'property',
            'key'   => 'article:publisher',
            'value' => $instance->get_setting( 'social', 'facebook[url]' ),
        );
        
        // Allow devs to filter the social tags
        $social_tags = apply_filters( 'wp_simple_seo_social_output_meta_tags_single', $social_tags, $meta_tags, $post );
        
        // Merge tags
        $meta_tags = array_merge( $meta_tags, $social_tags );

        // Return
        return $meta_tags;

    }

    /**
     * Outputs meta tags for Taxonomy Terms
     *
     * @since   1.0.0
     *
     * @param   array   $meta_tags  Existing Meta Tags
     * @param   WP_Term $term       Taxonomy Term
     * @return  array               Meta Tags
     */
    public function output_meta_tags_taxonomy( $meta_tags, $term ) {

        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Do nothing if social metadata is not enabled
        if ( ! $instance->get_setting( 'social', 'general[enabled]' ) ) {
            return $meta_tags;
        }

        // Get social tags
        $social_tags = $this->get_social_tags( 
            $instance->get_setting( 'meta', 'taxonomies[' . $term->taxonomy . '][title]', $term->term_id ),
            $instance->get_setting( 'meta', 'taxonomies[' . $term->taxonomy . '][description]', $term->term_id ),
            'website',
            get_term_link( $term )
        );

        // Allow devs to filter the social tags
        $social_tags = apply_filters( 'wp_simple_seo_social_output_meta_tags_taxonomy', $social_tags, $meta_tags, $term );
        
        // Merge tags
        $meta_tags = array_merge( $meta_tags, $social_tags );

        // Return
        return $meta_tags;

    }

    /**
     * Outputs meta tags for Date based Archives
     *
     * @since   1.0.0
     *
     * @param   array   $meta_tags  Existing Meta Tags
     * @param   array   $query      WP_Query query
     * @return  array               Meta Tags
     */
    public function output_meta_tags_date( $meta_tags, $query ) {

        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Do nothing if social metadata is not enabled
        if ( ! $instance->get_setting( 'social', 'general[enabled]' ) ) {
            return $meta_tags;
        }

        // Get social tags
        $social_tags = $this->get_social_tags( 
            $instance->get_setting( 'meta', 'archives[dates][title]' ),
            $instance->get_setting( 'meta', 'archives[dates][description]' ),
            'website'
        );

        // Allow devs to filter the social tags
        $social_tags = apply_filters( 'wp_simple_seo_social_output_meta_tags_date', $social_tags, $meta_tags, $query );
        
        // Merge tags
        $meta_tags = array_merge( $meta_tags, $social_tags );

        // Return
        return $meta_tags;

    }

    /**
     * Outputs meta tags for Author Archives
     *
     * @since   1.0.0
     *
     * @param   array   $meta_tags  Existing Meta Tags
     * @param   WP_User $author     Author
     * @return  array               Meta Tags
     */
    public function output_meta_tags_author( $meta_tags, $author ) {

        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Do nothing if social metadata is not enabled
        if ( ! $instance->get_setting( 'social', 'general[enabled]' ) ) {
            return $meta_tags;
        }

        // Get social tags
        $social_tags = $this->get_social_tags( 
            $instance->get_setting( 'meta', 'archives[authors][title]' ),
            $instance->get_setting( 'meta', 'archives[authors][description]' ),
            'profile',
            get_author_posts_url( $author->ID )
        );

        // Add some additional Open Graph metadata for Articles
        $social_tags['profile_first_name'] = array(
            'tag'   => 'property',
            'key'   => 'profile:first_name',
            'value' => get_user_meta( $author->ID, 'first_name', true ),
        );
        $social_tags['profile_last_name'] = array(
            'tag'   => 'property',
            'key'   => 'profile:last_name',
            'value' => get_user_meta( $author->ID, 'last_name', true ),
        );
        $social_tags['profile_gender'] = array(
            'tag'   => 'property',
            'key'   => 'profile:gender',
            'value' => '',
        );
        $social_tags['profile_username'] = array(
            'tag'   => 'property',
            'key'   => 'profile:username',
            'value' => $author->display_name,
        );

        // Allow devs to filter the social tags
        $social_tags = apply_filters( 'wp_simple_seo_social_output_meta_tags_author', $social_tags, $meta_tags, $author );
        
        // Merge tags
        $meta_tags = array_merge( $meta_tags, $social_tags );

        // Return
        return $meta_tags;

    }

    /**
     * Outputs meta tags for Search Results Archives
     *
     * @since   1.0.0
     *
     * @param   array   $meta_tags  Existing Meta Tags
     * @param   string  $search     Search Terms
     * @return  array               Meta Tags
     */
    public function output_meta_tags_search( $meta_tags, $search_terms ) {

        // Get settings instance
        $instance = WP_Simple_SEO_Settings::get_instance();

        // Do nothing if social metadata is not enabled
        if ( ! $instance->get_setting( 'social', 'general[enabled]' ) ) {
            return $meta_tags;
        }

        // Get social tags
        $social_tags = $this->get_social_tags( 
            $instance->get_setting( 'meta', 'search[title]' ),
            $instance->get_setting( 'meta', 'search[description]' ),
            'website',
            get_search_link( $search_terms )
        );

        // Allow devs to filter the social tags
        $social_tags = apply_filters( 'wp_simple_seo_social_output_meta_tags_search', $social_tags, $meta_tags, $search_terms );
        
        // Merge tags
        $meta_tags = array_merge( $meta_tags, $social_tags );

        // Return
        return $meta_tags;

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