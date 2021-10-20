<?php
/**
 * Common class
 * 
 * @package   WP_Simple_SEO
 * @author    WP Simple SEO
 * @version   1.0.0
 * @copyright WP Simple SEO
 */
class WP_Simple_SEO_Common {

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Helper method to better get a Post Type Archive's Post Type
     * based on what the visitor is viewing.
     *
     * When a Posts Page is used, this is returned as the Posts' Post Type Archive
     *
     * @since   1.0.7
     *
     * @return  mixed   empty string | WP_Post_Type
     */
    public function get_post_type_archive_post_type() {

        // Get the post type archive we're viewing
        $object = get_queried_object();

        // Depending on the object we've received, determine the post type we're on
        $post_type_object = '';
        if ( is_a( $object, 'WP_Post' ) ) {
            // Post Object
            // If we're viewing a Page, it might be the Posts Page
            $page_for_posts_id = get_option( 'page_for_posts' );
            if ( $object->post_type == 'page' && $page_for_posts_id == $object->ID ) {
                $post_type_object = get_post_type_object( 'post' );
            } else {
                $post_type_object = get_post_type_object( $object->post_type );
            }
        } else {
            // Post Type Object
            $post_type_object = $object;
        }

        return $post_type_object;

    }

    /**
     * Helper method to retrieve public Post Types
     *
     * @since 1.0
     *
     * @return array Public Post Types
     */
    public function get_post_types() {

        // Get public Post Types
        $types = get_post_types( array(
            'public' => true,
        ), 'objects' );

        // Filter out excluded post types
        $excluded_types = $this->get_excluded_post_types();
        if ( is_array( $excluded_types ) ) {
            foreach ( $excluded_types as $excluded_type ) {
                unset( $types[ $excluded_type ] );
            }
        }

        // Return filtered results
        return apply_filters( 'wp_simple_seo_common_get_post_types', $types );

    }

    /**
     * Helper method to retrieve excluded Post Types
     *
     * @since 1.0.0
     *
     * @return array Excluded Post Types
     */
    public function get_excluded_post_types() {

        // Define any excluded post types
        $types = array( 'attachment' );

        // Return filtered results
        return apply_filters( 'wp_simple_seo_common_get_excluded_post_types', $types );

    }

    /**
     * Helper method to retrieve Taxonomies, grouped by Post Type
     *
     * @since   1.0.7
     *
     * @return  array   Taxonomies, grouped by Post Type
     */
    public function get_post_types_taxonomies() {

        // Get public Post Types
        $post_types = $this->get_post_types();

        // Bail if no Post Types were found
        if ( empty( $post_types ) ) {
            return;
        }

        // Get taxonomies for each Post Type
        $post_type_taxonomies = array();
        foreach ( $post_types as $post_type ) {
            $post_type_taxonomies[ $post_type->name ] = $this->get_taxonomies( $post_type->name );

            // If the result is empty, remove the key/value from the array
            if ( empty( $post_type_taxonomies[ $post_type->name ] ) ) {
                unset( $post_type_taxonomies[ $post_type->name ] );
            }
        }

        // Return filtered results
        return apply_filters( 'wp_simple_seo_common_get_post_types_taxonomies', $post_type_taxonomies, $post_types );

    }

    /**
     * Helper method to retrieve Taxonomies
     *
     * @since 1.0.0
     *
     * @param   string  $post_type  Post Type (if defined, only returns Taxonomies assigned to the given Post Type)
     * @param   bool    $names      Return taxonomy names only (default: false = return objects)
     * @return  array               Taxonomies
     */
    public function get_taxonomies( $post_type = '', $names = false ) {

        // Build args
        $args = array(
            'public' => true,
        );

        // If a post type is specified, limit taxonomies to that post type
        if ( ! empty( $post_type ) ) {
            $args['object_type'] = array( $post_type );
        }

        // Get Taxonomies
        $taxonomies = get_taxonomies( $args, ( $names ? 'names' : 'objects' ) );
        
        // Filter out excluded post types
        $excluded_taxonomies = self::get_excluded_taxonomies();
        if ( is_array( $excluded_taxonomies ) ) {
            foreach ( $excluded_taxonomies as $excluded_taxonomy ) {
                unset( $taxonomies[ $excluded_taxonomy ] );
            }
        }

        // Return filtered results
        return apply_filters( 'wp_simple_seo_common_get_taxonomies', $taxonomies );

    }

    /**
     * Helper method to retrieve excluded Taxonomies
     *
     * @since 1.0
     *
     * @return array Excluded Taxonomies
     */
    public function get_excluded_taxonomies() {

        // Get excluded Taxonomies
        $taxonomies = array( 'post_format' );

        // Return filtered results
        return apply_filters( 'wp_simple_seo_common_get_excluded_taxonomies', $taxonomies );

    }

    /**
     * Helper method to retrieve Knowledge Graph entity types
     *
     * @since   1.0.0
     *
     * @return  array   Knowledge Graph Entity Types
     */
    public function get_entity_types() {

        // Define entity types
        $types = array(
            'company' => array(
                'name'  => 'company',
                'label' => __( 'Company', 'wp-simple-seo' ),
            ),
            'person' => array(
                'name'  => 'person',
                'label' => __( 'Person', 'wp-simple-seo' ),
            ),
        );

        // Filter to add/remove types
        $types = apply_filters( 'wp_simple_seo_common_get_entity_types', $types );

        // Return
        return $types;

    }

    /**
     * Helper method to retrieve social networks
     *
     * See: https://developers.google.com/search/docs/data-types/social-profile-links
     *
     * @since 1.0.0
     *
     * @return array Social Networks
     */
    public function get_social_networks() {

        // Define networks
        $social_networks = array(
            'facebook' => array(
                'name'  => 'facebook',
                'label' => __( 'Facebook', 'wp-simple-seo' ),
            ),
            'twitter' => array(
                'name'  => 'twitter',
                'label' => __( 'Twitter', 'wp-simple-seo' ),
            ),
            'google' => array(
                'name'  => 'google',
                'label' => __( 'Google+', 'wp-simple-seo' ),
            ),
            'instagram' => array(
                'name'  => 'instagram',
                'label' => __( 'Instagram', 'wp-simple-seo' ),
            ),
            'youtube' => array(
                'name'  => 'youtube',
                'label' => __( 'YouTube', 'wp-simple-seo' ),
            ), 
            'linkedin' => array(
                'name'  => 'linkedin',
                'label' => __( 'LinkedIn', 'wp-simple-seo' ),
            ),
            'myspace' => array(
                'name'  => 'myspace',
                'label' => __( 'MySpace', 'wp-simple-seo' ),
            ), 
            'pinterest' => array(
                'name'  => 'pinterest',
                'label' => __( 'Pinterest', 'wp-simple-seo' ),
            ),
            'soundcloud' => array(
                'name'  => 'soundcloud',
                'label' => __( 'SoundCloud', 'wp-simple-seo' ),
            ),
            'tumblr' => array(
                'name'  => 'tumblr',
                'label' => __( 'Tumblr', 'wp-simple-seo' ),
            ),
        );

        // Filter to add/remove social networks
        $social_networks = apply_filters( 'wp_simple_seo_common_get_social_networks', $social_networks );

        // Return
        return $social_networks;

    }

    /**
     * Helper method to retrieve Twitter card types
     *
     * @since 1.0.0
     *
     * @return  array   Twitter Card Types
     */
    public function get_twitter_card_types() {

        // Define card types
        $card_types = array(
            'summary' => array(
                'name'  => 'summary',
                'label' => __( 'Summary Card', 'wp-simple-seo' ),
            ),
            'twitter' => array(
                'name'  => 'summary_large_image',
                'label' => __( 'Summary Card with Large Image', 'wp-simple-seo' ),
            ),
        );

        // Filter to add/remove card types
        $card_types = apply_filters( 'wp_simple_seo_common_get_twitter_card_types', $card_types );

        // Return
        return $card_types;

    }

    /**
     * Returns an array of WP_User objects for all Users that have at least
     * one published Post
     *
     * @since 1.0.0
     *
     * @return mixed    false | array of WP_User objects
     */
    public function get_users_with_published_posts() {

        $query = new WP_User_Query( array(
            'has_published_posts' => true,
        ) );
        $results = $query->get_results();

        // If no results found, bail
        if ( empty( $results ) ) {
            return false;
        }

        // Return Users
        return $results;

    }

    /**
     * Determines if the supplied content will be output in a paginated fashion, and if
     * so returns the current and total number of 'pages'.
     *
     * @since   1.0.0
     *
     * @param   string  $content    Single Post Content
     * @return  array               Current and Total Pages
     */
    public function get_single_post_pagination_data( $content ) {

        global $wp_query;

        // Assume there is no pagination in the content
        $pagination = array(
            'current'   => 0,
            'total'     => 0,
        );
        
        // Detect whether the content is split up
        if ( false !== strpos( $content, '<!--nextpage-->' ) ) {
            $content = str_replace( "\n<!--nextpage-->\n", '<!--nextpage-->', $content );
            $content = str_replace( "\n<!--nextpage-->", '<!--nextpage-->', $content );
            $content = str_replace( "<!--nextpage-->\n", '<!--nextpage-->', $content );

            // Ignore nextpage at the beginning of the content.
            if ( 0 === strpos( $content, '<!--nextpage-->' ) ) {
                $content = substr( $content, 15 );
            }

            $pages = explode( '<!--nextpage-->', $content );
        } else {
            $pages = array( $content );
        }

        $pagination['total'] = count( $pages );
        $pagination['current'] = ( get_query_var( 'page' ) > 0 ? get_query_var( 'page' ) : 1 );

        return $pagination;

    }

    /**
     * Helper method to retrieve stop words
     *
     * @since 1.0.0
     *
     * @return  array   Stop Words
     */
    public function get_stop_words() {

        // Define stopwords
        $stopwords = explode( ',', __( "a,about,above,after,again,against,all,am,an,and,any,are,as,at,be,because,been,before,being,below,between,both,but,by,could,did,do,does,doing,down,during,each,few,for,from,further,had,has,have,having,he,he'd,he'll,he's,her,here,here's,hers,herself,him,himself,his,how,how's,i,i'd,i'll,i'm,i've,if,in,into,is,it,it's,its,itself,let's,me,more,most,my,myself,nor,of,on,once,only,or,other,ought,our,ours,ourselves,out,over,own,same,she,she'd,she'll,she's,should,so,some,such,than,that,that's,the,their,theirs,them,themselves,then,there,there's,these,they,they'd,they'll,they're,they've,this,those,through,to,too,under,until,up,very,was,we,we'd,we'll,we're,we've,were,what,what's,when,when's,where,where's,which,while,who,who's,whom,why,why's,with,would,you,you'd,you'll,you're,you've,your,yours,yourself,yourselves", 'wp-simple-seo' ) );

        // Return filtered results
        return apply_filters( 'wp_simple_seo_common_get_stop_words', $stopwords );

    }

    /**
     * Helper method to retrieve the maximum permitted / displayed title length in SERPs
     *
     * @since   1.0.0
     *
     * @return  int     Maximum length in characters
     */
    public function get_max_meta_title_length() {

        return apply_filters( 'wp_simple_seo_common_get_max_meta_title_length', 60 );

    }

    /**
     * Helper method to retrieve the maximum permitted / displayed description length in SERPs
     *
     * @since   1.0.0
     *
     * @return  int     Maximum length in characters
     */
    public function get_max_meta_description_length() {

        return apply_filters( 'wp_simple_seo_common_get_max_meta_description_length', 160 );

    }

    /**
     * Determines whether the WordPress web site is a production / live web site.
     *
     * Uses several factors, including the URL, whether password protection is applied etc.
     *
     * @since   1.0.0
     *
     * @return  bool    Is Production Site
     */
    public function is_production_site() {

        // Get URLs which are non-production sites
        $development_urls = $this->get_development_urls();

        // Get site URL
        $url = get_bloginfo( 'url' );

        // If the site address is localhost, 127.0.0.1 or .dev, it's not a production site
        foreach ( $development_urls as $development_url ) {
            if ( strpos( $url, $development_url ) !== false ) {
                // Not a production site
                return false;
            }
        }

        // We're on a production / live site
        return true;

    }

    /**
     * Helper method to return URLs of development sites.
     *
     * Can be partial matches e.g. .dev
     *
     * @since   1.0.0
     *
     * @return  array   Development URLs
     */
    private function get_development_urls() {

        $urls = array(
            '127.0.0.1',
            'localhost',
            '.local',
            '.dev.',
        );

        // Filter URLs
        $urls = apply_filters( 'wp_simple_seo_common_get_development_urls', $urls );

        // Return
        return $urls;

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