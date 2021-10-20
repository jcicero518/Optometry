<?php
/**
 * Tags class
 * 
 * @package   WP_Simple_SEO
 * @author    WP Simple SEO
 * @version   1.0.0
 * @copyright WP Simple SEO
 */
class WP_Simple_SEO_Tags {

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Returns template tags for the Home Page
     *
     * @since 1.0.0
     *
     * @return  array   Tags
     */
    public function get_home_tags() {

        // Define tag groups
        $tags = array(
            'General'   => $this->_get_general_tags(),
            'Pagination'=> $this->_get_pagination_tags(),
        );

        // Return filtered results
        return apply_filters( 'wp_simple_seo_tags_get_home_tags', $tags );

    }

    /**
     * Returns template tags for a Single Post
     *
     * @since 1.0.0
     *
     * @return  array   Tags
     */
    public function get_post_tags( $post_type ) {

        // Define tag groups
        $tags = array(
            'General'   => $this->_get_general_tags(),
            'Pagination'=> $this->_get_pagination_tags(),
            'Post'      => $this->_get_post_tags(),
            'Author'    => $this->_get_author_tags(),
        );

        // For each taxonomy assigned to the Post Type, add taxonomy tags
        $taxonomies = WP_Simple_SEO_Common::get_instance()->get_taxonomies( $post_type );
        if ( count( $taxonomies ) > 0 ) {
            $tags['Taxonomy'] = array();
            foreach ( $taxonomies as $taxonomy ) {
                $tags['Taxonomy'] = array_merge( $tags['Taxonomy'], $this->_get_post_taxonomy_tags( $taxonomy ) );
            }
        }

        // Return filtered results
        return apply_filters( 'wp_simple_seo_tags_get_post_tags', $tags, $post_type, $taxonomies );

    }

    /**
     * Returns template tags for a Post Type Archive
     *
     * @since 1.0.0
     *
     * @return  array   Tags
     */
    public function get_post_archive_tags( $post_type_object ) {

        // Define tag groups
        $tags = array(
            'General'   => $this->_get_general_tags(),
            'Pagination'=> $this->_get_pagination_tags(),
            'Post Type' => $this->_get_post_type_tags( $post_type_object ),
        );

        // Return filtered results
        return apply_filters( 'wp_simple_seo_tags_get_post_archive_tags', $tags, $post_type_object );

    }

    /**
     * Returns template tags for a Taxonomy Archive
     *
     * @since 1.0.0
     *
     * @return  array   Tags
     */
    public function get_taxonomy_tags( $taxonomy ) {

        $tags = array(
            'General'   => $this->_get_general_tags(),
            'Pagination'=> $this->_get_pagination_tags(),
            'Term'      => $this->_get_taxonomy_term_tags( $taxonomy ),
        );

        // Return filtered results
        return apply_filters( 'wp_simple_seo_tags_get_taxonomy_tags', $tags, $taxonomy );

    }

    /**
     * Returns template tags for an Author Archive
     *
     * @since 1.0.0
     *
     * @return  array   Tags
     */
    public function get_author_tags() {

        $tags = array(
            'General'   => $this->_get_general_tags(),
            'Pagination'=> $this->_get_pagination_tags(),
            'Author'    => $this->_get_author_tags(),
        );

        // Return filtered results
        return apply_filters( 'wp_simple_seo_tags_get_author_tags', $tags );

    }

    /**
     * Returns template tags for a Date based Archive
     *
     * @since 1.0.0
     *
     * @return  array   Tags
     */
    public function get_date_tags() {

        $tags = array(
            'General'   => $this->_get_general_tags(),
            'Pagination'=> $this->_get_pagination_tags(),
            'Date'      => $this->_get_date_tags(),
        );

        // Return filtered results
        return apply_filters( 'wp_simple_seo_tags_get_date_tags', $tags );

    }

    /**
     * Returns template tags for the Search Results screen
     *
     * @since 1.0.0
     *
     * @return  array   Tags
     */
    public function get_search_tags() {

        $tags = array(
            'General'   => $this->_get_general_tags(),
            'Pagination'=> $this->_get_pagination_tags(),
            'Search'    => $this->_get_search_tags(),
        );

        // Return filtered results
        return apply_filters( 'wp_simple_seo_tags_get_search_tags', $tags );

    }

    /**
     * Returns template tags for the 404 screen
     *
     * @since 1.0.0
     *
     * @return  array   Tags
     */
    public function get_404_tags() {

        $tags = array(
            'General'   => $this->_get_general_tags(),
        );

        // Return filtered results
        return apply_filters( 'wp_simple_seo_tags_get_404_tags', $tags );

    }

    /**
     * Returns an array of AIOSEO to WP Simple SEO tag mappings
     *
     * @since   1.0.0
     *
     * @return  array   Tag Mappings (aioseo => wp-simple-seo)
     */
    public function get_aiseo_tag_mappings() {

        $tags = array(
            // General
            '%blog_title%'             => '{site_name}',
            '%blog_description%'       => '{site_description}',

            // Pagination
            '%page%'                    => '{pagination_page} / {pagination_total}',

            // Post
            '%page_title%'              => '{post_title}',
            '%post_title%'              => '{post_title}',

            // Post Type
            '%archive_title%'           => '{posttype_singular_name}',

            // Taxonomy: Category
            '%category%'                => '{taxonomy_category_name}',
            '%category_title%'          => '{taxonomy_category_name}',
            '%category_description%'    => '{taxonomy_category_description}',
            
            // Taxonomy: Tag
            '%tag%'                     => '{taxonomy_post_tag_name}',
            '%tag_title%'               => '{taxonomy_post_tag_name}',
            '%tag_description%'         => '{taxonomy_post_tag_description}',
            
            // Author
            '%author'                   => '{author_display_name}',
            '%page_author_login%'       => '{author_user_login}',
            '%page_author_nicename%'    => '{author_user_nicename}',
            '%page_author_firstname%'   => '{author_user_firstname}',
            '%page_author_lastname%'    => '{author_user_lastname}',


            // Search
            '%search%'                  => '{search_terms}',

            // 404
            '%request_words%'           => '',
        );

        // Return filtered results
        return apply_filters( 'wp_simple_seo_tags_get_yoast_tag_mappings', $tags );

    }

    /**
     * Returns an array of Yoast to WP Simple SEO tag mappings
     *
     * @since   1.0.0
     *
     * @return  array   Tag Mappings (yoast => wp-simple-seo)
     */
    public function get_yoast_tag_mappings() {

        $tags = array(
            // General
            '%%sitename%%'              => '{site_name}',
            '%%sitedesc%%'              => '{site_description}',
            '%%sep%%'                   => '{title_separator}',

            // Pagination
            '%%page%%'                  => '{pagination_page} / {pagination_total}',
            '%%pagenumber%%'            => '{pagination_page}',
            '%%pagetotal%%'             => '{pagination_total}',

            // Post
            '%%title%%'                 => '{post_title}',
            '%%excerpt%%'               => '{post_excerpt}',
            '%%excerpt_only%%'          => '{post_excerpt}',
            '%%pt_single%%'             => '{post_type}',
            '%%pt_plural%%'             => '{post_type_plural}',
            '%%cf_!%%'                  => '{post_custom_field_!}',

            // Post Type
            '%%pt_single%%'             => '{posttype_singular_name}',
            '%%pt_plural%%'             => '{posttype_name}',

            // Taxonomy: Category
            '%%category%%'              => '{taxonomy_category_name}',
            '%%category_description%%'  => '{taxonomy_category_description}',

            // Taxonomy: Tag
            '%%tag%%'                   => '{taxonomy_post_tag_name}',
            '%%tag_description%%'       => '{taxonomy_post_tag_description}',

            // Term
            '%%term_title%%'            => '{term_name}',
            '%%term_description%%'      => '{term_description}',

            // Author
            '%%name%%'                  => '{author_display_name}',

            // Search
            '%%searchphrase%%'          => '{search_terms}',
        );

        // Return filtered results
        return apply_filters( 'wp_simple_seo_tags_get_yoast_tag_mappings', $tags );

    }

    /**
     * Helper method to retrieve general tags, that are always available.
     *
     * @since 1.0.0
     *
     * @return  array  Tags
     */
    private function _get_general_tags() {

        $tags = array(
            '{site_name}'           => __( 'Site Name', 'wp-simple-seo' ),
            '{site_description}'    => __( 'Site Description', 'wp-simple-seo' ), 
            '{title_separator}'     => __( 'Title Separator Symbol', 'wp-simple-seo' ), 
        );

        // Return filtered results
        return apply_filters( 'wp_simple_seo_tags__get_general_tags', $tags );

    }

    /**
     * Helper method to retrieve pagination tags.
     *
     * @since   1.0.0
     *
     * @return  array  Tags
     */
    private function _get_pagination_tags() {

        $tags = array(
            '{pagination_page_total}'   => __( 'Page Number and Total', 'wp-simple-seo' ),
            '{pagination_page}'         => __( 'Page Number', 'wp-simple-seo' ),
            '{pagination_total}'        => __( 'Page Total', 'wp-simple-seo' ), 
        );

        // Return filtered results
        return apply_filters( 'wp_simple_seo_tags__get_pagination_tags', $tags );

    }

    /**
     * Helper method to retrieve Post tags.
     *
     * @since 1.0.0
     *  
     * @return  array Tags
     */
    private function _get_post_tags() {

        // Build tags array
        $tags = array(
            '{post_title}'              => __( 'Title', 'wp-simple-seo' ),
            '{post_excerpt}'            => __( 'Excerpt', 'wp-simple-seo' ),
            '{post_content}'            => __( 'Content', 'wp-simple-seo' ),
            '{post_date}'               => __( 'Date', 'wp-simple-seo' ),
            '{post_type}'               => __( 'Type', 'wp-simple-seo' ),
            '{post_type_plural}'        => __( 'Type (Plural)', 'wp-simple-seo' ),
            '{post_url}'                => __( 'URL', 'wp-simple-seo' ),
            '{post_custom_field_NAME}'  => __( 'Meta Field', 'wp-simple-seo' ),
        );

        // Return filtered results
        return apply_filters( 'wp_simple_seo_tags__get_post_tags', $tags );

    }

    /**
     * Helper method to retrieve Post Type tags.
     *
     * @since 1.0.0
     *  
     * @param   WP_PostType     $post_type_object   WordPress Post Type Object
     * @return  array                               Tags
     */
    private function _get_post_type_tags( $post_type_object ) {

        // Build tags array
        $tags = array(
            '{posttype_singular_name}'  => __( 'Singular Name', 'wp-simple-seo' ),
            '{posttype_name}'           => __( 'Plural Name', 'wp-simple-seo' ), 
        );

        // Return filtered results
        return apply_filters( 'wp_simple_seo_tags__get_post_type_tags', $tags );

    }

    /**
     * Helper method to retrieve taxonomy tags for Posts.
     *
     * @since 1.0.0
     *
     * @param   WP_Taxonomy  $taxonomy      WordPress Taxonomy
     * @return  array                       Tags
     */
    private function _get_post_taxonomy_tags( $taxonomy ) {

        // Build the tags array
        $tags = array(
            '{taxonomy_' . $taxonomy->name . '_name}'          => sprintf( __( '%s Name', 'wp-simple-seo' ),  $taxonomy->labels->singular_name ),
            '{taxonomy_' . $taxonomy->name . '_description}'   => sprintf( __( '%s Description', 'wp-simple-seo' ),  $taxonomy->labels->singular_name ), 
        );

        // Return filtered results
        return apply_filters( 'wp_simple_seo_tags__get_taxonomy_tags', $tags );

    }

    /**
     * Helper method to retrieve taxonomy term tags.
     *
     * @since 1.0.0
     *
     * @param   object  WP_Taxonomy     WordPress Taxonomy
     * @return  array                   Tags
     */
    private function _get_taxonomy_term_tags( $taxonomy ) {

        // Build the tags array
        $tags = array(
            '{term_name}'           => sprintf( __( '%s Name', 'wp-simple-seo' ),  $taxonomy->labels->singular_name ),
            '{term_description}'   => sprintf( __( '%s Description', 'wp-simple-seo' ),  $taxonomy->labels->singular_name ), 
        );

        // Return filtered results
        return apply_filters( 'wp_simple_seo_tags__get_taxonomy_term_tags', $tags );

    }

    /**
     * Helper method to retrieve available tags for Authors (WordPress Users)
     *
     * @since 1.0.0
     *
     * @return  array  Tags
     */
    private function _get_author_tags() {

        $tags = array(
            '{author_user_login}'   => __( 'Author Login', 'wp-simple-seo' ), 
            '{author_user_nicename}'=> __( 'Author Nice Name', 'wp-simple-seo' ), 
            '{author_user_email}'   => __( 'Author Email', 'wp-simple-seo' ), 
            '{author_user_url}'     => __( 'Author URL', 'wp-simple-seo' ), 
            '{author_display_name}' => __( 'Author Display Name', 'wp-simple-seo' ), 
            '{author_description}'  => __( 'Author Biographical Info', 'wp-simple-seo' ), 
            '{author_field_NAME}'   => __( 'Author Meta Field', 'wp-simple-seo' ), 
        );

        // Return filtered results
        return apply_filters( 'wp_simple_seo_tags__get_author_tags', $tags );

    }

    /**
     * Helper method to retrieve available tags for Date based archives
     *
     * @since 1.0.0
     *
     * @return  array  Tags
     */
    private function _get_date_tags() {

        $tags = array(
            '{date_year}'       => __( 'Year', 'wp-simple-seo' ), 
            '{date_monthnum}'   => __( 'Month', 'wp-simple-seo' ), 
            '{date_day}'        => __( 'Day', 'wp-simple-seo' ), 
        );

        // Return filtered results
        return apply_filters( 'wp_simple_seo_tags__get_date_tags', $tags );

    }

    /**
     * Helper method to retrieve available tags for Search Results
     *
     * @since 1.0.0
     *
     * @return  array  Tags
     */
    private function _get_search_tags() {

        $tags = array(
            '{search_terms}' => __( 'Search Terms', 'wp-simple-seo' ),
        );

        // Return filtered results
        return apply_filters( 'wp_simple_seo_tags__get_search_tags', $tags );

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