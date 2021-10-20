<?php
/**
 * Parser class
 * 
 * @package   WP_Simple_SEO
 * @author    WP Simple SEO
 * @version   1.0.0
 * @copyright WP Simple SEO
 */
class WP_Simple_SEO_Parser {

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
     * Replaces template tags in the given content with actual values from the given object, such as a 
     * WordPress Post, Taxonomy Term or User.
     *
     * @since   1.0.0
     *
     * @param   string  $content        Content
     * @param   string  $screen         Screen (home | post_type_archive | post_single | taxonomy | author | date | search | 404)
     * @param   mixed   $object         Object (WP_Post | WP_PostType | WP_Term | WP_User | array | false )
     */
    public function parse_tags( $content, $screen = 'home', $object = false ) {

        global $wp_query;

        // Extract all tags from the content we're parsing
        preg_match_all( '/{(.*?)}/', $content, $matches );

        // If no tags were found, just return the content
        if ( empty( $matches ) || empty( $matches[0] ) ) {
            return $content;
        }

        // Get common instance
        $common = WP_Simple_SEO_Common::get_instance();
        $settings = WP_Simple_SEO_Settings::get_instance();

        // Iterate through the tags that were found in the content, replacing them with real content
        foreach ( $matches[1] as $key => $content_tag ) {
            // Always assume there is no value to replace the tag with
            $tag_value = '';

            // Determine the start of the tag, as this tell us which tag type it is (site, post, taxonomy, author etc)
            $tag_parts = explode( '_', $content_tag );

            switch ( $tag_parts[0] ) {

                /**
                 * General
                 */
                case 'site':
                    $tag_value = get_bloginfo( $tag_parts[1] );
                    break;

                case 'title':
                     $tag_value = WP_Simple_SEO_Settings::get_instance()->get_setting( 'meta', 'general[title_separator]' );
                     break;

                /**
                 * Pagination
                 */
                case 'pagination':
                    // Assume no pagination needs to be output
                    $pagination_page = '';
                    $pagination_total = '';

                    // Pagination can be in an archive (post type, taxonomy, date, author),
                    // or within a single Page/Post/CPT
                    switch ( $screen ) {
                        /**
                         * Single Post
                         */
                        case 'post_single':
                            // Determine if the Post content is split using nextpage
                            $pagination_data = WP_Simple_SEO_Common::get_instance()->get_single_post_pagination_data( $object->post_content );

                            // If there's only one page, no pagination is needed in the metadata
                            if ( $pagination_data['total'] == 1 && $pagination_data['current'] == 1 ) {
                                break;
                            }

                            // Define the current and total pagination values
                            $pagination_page = $pagination_data['current'];
                            $pagination_total = $pagination_data['total'];
                            break;

                        /**
                         * Archive
                         */
                        default:
                            // Determine the current and total pagination values, to see if
                            // pagination exists for this particular section of the site, depending
                            // on whether we're in the admin interface or not
                            if ( is_admin() ) {
                                $pagination_page     = 1; // For the preview, just use 1
                                $pagination_total    = floor( $object->count / get_option( 'posts_per_page' ) );
                            } else {
                                $pagination_page = get_query_var( 'paged' );
                                $pagination_total = $wp_query->max_num_pages;
                            }

                            // If there's only one page, no pagination is needed in the metadata
                            if ( $pagination_total == 0 ) {
                                break;
                            }

                            // Pagination page is zero based; fix this
                            if ( $pagination_page == 0 ) {
                                $pagination_page = 1;
                            }

                            break;
                    }

                    // Define the tag value
                    switch ( $content_tag ) {
                        case 'pagination_page_total':
                            $pagination_separator = $settings->get_setting( 'meta', 'general[pagination_separator]', '/' );
                            $tag_value = $pagination_page . ' ' . $pagination_separator . ' ' . $pagination_total;
                            break;

                        case 'pagination_page':
                            $tag_value = $pagination_page;
                            break;

                        case 'pagination_total':
                            $tag_value = $pagination_total;
                            break;
                    }

                    break;

                /**
                 * Post
                 */
                case 'post':
                    if ( $tag_parts[1] == 'custom' ) {
                        // Meta Field
                        $field = str_replace( 'post_custom_field_', '', $content_tag );

                        // Fetch value from Post Object
                        $tag_value = get_post_meta( $object->ID, $field, true );
                    } else {
                        // Post Field

                        // Fetch value from Post Object
                        switch ( $content_tag ) {
                            /**
                             * Excerpt
                             * - Generate excerpt if one doesn't exist
                             */
                            case 'post_excerpt':
                                // Generate a post excerpt if one isn't specified
                                if ( empty( $object->{ $content_tag } ) ) {
                                    $object->{ $content_tag } = wp_trim_words( $object->post_content );
                                }
                                break;

                            /**
                             * Post Type Plural
                             * - Fetch the plural post type label
                             */
                            case 'post_type_plural':
                                $post_type_object = get_post_type_object( $object->post_type );
                                $object->{ $content_tag } = $post_type_object->labels->name;
                                break;

                            /**
                             * Post URL
                             */
                            case 'post_url':
                                $object->{ $content_tag } = get_permalink( $object->ID );
                                break;

                        }

                        // Replace tag with value
                        $tag_value = strip_tags( strip_shortcodes( $object->{ $content_tag } ) );   
                    }
                    break;

                /**
                 * Post Type
                 */
                case 'posttype':
                    $field = str_replace( 'posttype_', '', $content_tag );
                    $tag_value = $object->labels->{ $field };
                    break;

                /**
                 * Author
                 */
                case 'author':
                    // Get author, depending on the screen we're viewing
                    if ( $screen == 'post_single' ) {
                        $author = get_user_by( 'id', $object->post_author );
                    }
                    if ( $screen == 'author' ) {
                        $author = $object;
                    }

                    // Convert tag to WP_User field key
                    $field = str_replace( 'author_', '', $content_tag );

                    // Get value
                    $tag_value = $author->{ $field };
                    break;

                /**
                 * Taxonomy Term
                 */
                case 'term':
                    $field = str_replace( 'term_', '', $content_tag );
                    $tag_value = $object->{ $field };
                    break;

                /**
                 * Taxonomy
                 */
                case 'taxonomy':
                    // Assume there is no tag value
                    $tag_value = '';

                    // Get Taxonomy Name
                    $taxonomy = $tag_parts;
                    unset( $taxonomy[0], $taxonomy[ count( $tag_parts ) - 1 ] );
                    $taxonomy = implode( '_', $taxonomy );

                    // Get Taxonomy Terms
                    $terms = wp_get_post_terms( $object->ID, $taxonomy );
                    if ( empty( $terms ) ) {
                        break;
                    }

                    if ( is_array( $terms ) ) {
                        $field = str_replace( 'taxonomy_' . $taxonomy . '_', '', $content_tag );
                        $tag_value = $terms[0]->{ $field };
                    }
                    break;

                /**
                 * Date
                 */
                case 'date':
                    $field = str_replace( 'date_', '', $content_tag );
                    $tag_value = ( isset( $object[ $field ] ) ? $object[ $field ] : '' );
                    break;

                /**
                 * Search
                 */
                case 'search':
                    $tag_value = $object;
                    break;
            }
            
            // Replace the tag with the content
            $content = str_replace( $matches[0][ $key ], $tag_value, $content );

        }

        // Remove leading and trailing whitespace
        $content = trim( $content );

        // Remove newlines
        $content = str_replace( "\n", ' ',  $content );
        $content = str_replace( "\r", ' ',  $content );

        // Remove duplicate spaces
        $content = preg_replace( '/\s+/', ' ', $content );

        // Return filtered content
        return apply_filters( 'wp_simple_seo_parser_parse_tags', $content, $screen, $object, $matches );

    }

    /**
     * Replaces template tags in the given content with actual values from the given WordPress Admin form data 
     * (i.e. a single Post or Taxonomy Term).
     *
     * Typically used in the Administration interface via AJAX to return a live update of the parsed title or
     * meta description, as the user changes data on a Post or Taxonomy Term.
     *
     * @since   1.0.0
     *
     * @param   string  $content        Content
     * @param   string  $screen         Screen (post_single | taxonomy)
     * @param   mixed   $object         Object (WP_Post | WP_PostType | WP_Term | WP_User | array | false )
     * @param   array   $preview_data   Form Data
     */
    public function parse_tags_using_form_data( $content, $screen, $object, $preview_data ) {

        global $wp_query;

        // Extract all tags from the content we're parsing
        preg_match_all( '/{(.*?)}/', $content, $matches );

        // If no tags were found, just return the content
        if ( empty( $matches ) || empty( $matches[0] ) ) {
            return $content;
        }

        // Get common instance
        $common = WP_Simple_SEO_Common::get_instance();



        // Iterate through the tags that were found in the content, replacing them with real content
        foreach ( $matches[1] as $key => $content_tag ) {
            // Always assume there is no value to replace the tag with
            $tag_value = '';

            // Determine the start of the tag, as this tell us which tag type it is (site, post, taxonomy, author etc)
            $tag_parts = explode( '_', $content_tag );

            switch ( $tag_parts[0] ) {

                /**
                 * General
                 */
                case 'site':
                    $tag_value = get_bloginfo( $tag_parts[1] );
                    break;

                case 'title':
                     $tag_value = WP_Simple_SEO_Settings::get_instance()->get_setting( 'meta', 'general[title_separator]' );
                     break;

                /**
                 * Pagination
                 */
                case 'pagination':
                    // Assume no pagination needs to be output
                    $pagination_page = '';
                    $pagination_total = '';

                    // Pagination can be in an archive (post type, taxonomy, date, author),
                    // or within a single Page/Post/CPT
                    switch ( $screen ) {
                        /**
                         * Single Post
                         */
                        case 'post_single':
                            // Determine if the Post content is split using nextpage
                            $pagination_data = WP_Simple_SEO_Common::get_instance()->get_single_post_pagination_data( $preview_data['post_content'] );

                            // If there's only one page, no pagination is needed in the metadata
                            if ( $pagination_data['total'] == 1 && $pagination_data['current'] == 1 ) {
                                break;
                            }

                            // Define the current and total pagination values
                            $pagination_page = $pagination_data['current'];
                            $pagination_total = $pagination_data['total'];
                            break;

                        /**
                         * Archive
                         */
                        default:
                            // Determine the current and total pagination values, to see if
                            // pagination exists for this particular section of the site, depending
                            // on whether we're in the admin interface or not
                            if ( is_admin() ) {
                                $pagination_page     = 1; // For the preview, just use 1
                                $pagination_total    = floor( $object->count / get_option( 'posts_per_page' ) );
                            } else {
                                $pagination_page = get_query_var( 'paged' );
                                $pagination_total = $wp_query->max_num_pages;
                            }

                            // If there's only one page, no pagination is needed in the metadata
                            if ( $pagination_total == 0 ) {
                                break;
                            }

                            // Pagination page is zero based; fix this
                            if ( $pagination_page == 0 ) {
                                $pagination_page = 1;
                            }
                            break;
                    }

                    // Define the tag value
                    switch ( $content_tag ) {
                        case 'pagination_page':
                            $tag_value = $pagination_page;
                            break;

                        case 'pagination_total':
                            $tag_value = $pagination_total;
                            break;
                    }

                    break;

                /**
                 * Post
                 */
                case 'post':
                    if ( $tag_parts[1] == 'custom' ) {
                        // Meta Field
                        $field = str_replace( 'post_custom_field_', '', $content_tag );

                        // Fetch value from Preview Data, if it exists
                        if ( ! isset( $preview_data['meta'] ) ) {
                            break;
                        }
                        if ( empty( $preview_data['meta'] ) ) {
                            break;
                        }

                        // Iterate through Preview Data, to find the field
                        foreach ( $preview_data['meta'] as $id => $meta ) {
                            if ( $meta['key'] != $field ) {
                                continue;
                            }

                            // Match found
                            $tag_value = $meta['value'];
                            break;
                        }
                    } else {
                        // Post Field

                        // Fetch value from Preview Data
                        switch ( $content_tag ) {
                            /**
                             * Excerpt
                             * - Generate excerpt if one doesn't exist
                             */
                            case 'post_excerpt':
                                // Generate a post excerpt if one isn't specified
                                if ( empty( $preview_data['post_excerpt'] ) ) {
                                    $preview_data[ $content_tag ] = wp_trim_words( $preview_data['post_content'] );
                                }
                                break;

                            /**
                             * Post Type Plural
                             * - Fetch the plural post type label
                             */
                            case 'post_type_plural':
                                $post_type_object = get_post_type_object( $preview_data['post_type'] );
                                $preview_data[ $content_tag ] = $post_type_object->labels->name;
                                break;

                            /**
                             * Post URL
                             */
                            case 'post_url':
                                // If no URL has yet been given, a sample permalink hasn't been generated
                                // in the Admin UI by WordPress.
                                // Therefore fall back to the preview URL for now
                                if ( ! isset( $preview_data[ $content_tag ] ) || empty( $preview_data[ $content_tag ] ) ) {
                                    $preview_data[ $content_tag ] = get_permalink( $object->ID );
                                }
                                break;

                            /**
                             * Post Date
                             */
                            case 'post_date':
                                // Combine date and time fields from the Publish panel in the Admin UI
                                $preview_data[ $content_tag ] = 
                                        $preview_data['aa'] . '-' . $preview_data['mm'] . '-' . $preview_data['jj'] . ' ' . 
                                        $preview_data['hh'] . ':' . $preview_data['mn'] . ':' . $preview_data['ss'];
                                break;
                        }

                        // Replace tag with value
                        $tag_value = strip_tags( strip_shortcodes( $preview_data[ $content_tag ] ) );

                    }
                    break;

                /**
                 * Author
                 */
                case 'author':
                    // Get author, depending on the screen we're viewing
                    if ( $screen == 'post_single' ) {
                        // If post_author_override is set, the logged in User is assigning the Post to another Author
                        // We need to use that Author's data
                        if ( isset( $preview_data['post_author_override'] ) ) {
                            $user_id = (int) $preview_data['post_author_override'];
                        } else {
                            $user_id = (int) $preview_data['post_author'];
                        }
                        
                        $author = get_user_by( 'id', $user_id );
                    }
                    if ( $screen == 'author' ) {
                        $author = $object;
                    }

                    // Convert tag to WP_User field key
                    $field = str_replace( 'author_', '', $content_tag );

                    // Get value
                    $tag_value = $author->{ $field };
                    break;

                /**
                 * Taxonomy
                 */
                case 'taxonomy':
                    // Assume there is no tag value
                    $tag_value = '';

                    // Get Taxonomy Name
                    $taxonomy = $tag_parts;
                    unset( $taxonomy[0], $taxonomy[ count( $tag_parts ) - 1 ] );
                    $taxonomy = implode( '_', $taxonomy );

                    // Get Taxonomy Terms
                    if ( ! isset( $preview_data['post_' . $taxonomy ] ) ) {
                        break;
                    }
                    if ( ! isset( $preview_data['post_' . $taxonomy ] ) ) {
                        break;
                    }

                    // Iterate through
                    $terms = array();
                    foreach ( $preview_data['post_' . $taxonomy ] as $term_id ) {
                        $term_id = (int) $term_id;

                        // Skip zero
                        if ( $term_id == 0 ) {
                            continue;
                        }

                        // Add term to array
                        $terms[] = get_term( $term_id, $taxonomy );
                    }

                    // Bail if no terms found
                    if ( empty( $terms ) ) {
                        break;
                    }

                    // Replace tag with value
                    if ( is_array( $terms ) ) {
                        $field = str_replace( 'taxonomy_' . $taxonomy . '_', '', $content_tag );
                        $tag_value = $terms[0]->{ $field };
                    }

                    break;

                /**
                 * Taxonomy Term
                 */
                case 'term':
                    $field = str_replace( 'term_', '', $content_tag );
                    $tag_value = $preview_data[ $field ];
                    break;

            }
            
            // Replace the tag with the content
            $content = str_replace( $matches[0][ $key ], $tag_value, $content );

        }

        // Remove leading and trailing whitespace
        $content = trim( $content );

        // Return filtered content
        return apply_filters( 'wp_simple_seo_parser_parse_tags_using_form_data', $content, $screen, $object, $preview_data, $matches );

    }

    /**
     * Parses AIOSEO style tags, returning the WP Simple SEO tag equivalents
     *
     * @since   1.0.0
     *
     * @param   string  $content    Content
     * @return  string              Content
     */
    public function parse_aioseo_tags( $content ) {

        // Extract all tags from the content we're parsing
        preg_match_all( '/%%(.*?)%%/', $content, $matches );

        // If no tags were found, just return the content
        if ( empty( $matches ) || empty( $matches[0] ) ) {
            return $content;
        }

        // Get mappings
        $mappings = WP_Simple_SEO_Tags::get_instance()->get_aioseo_tag_mappings();

        // Iterate through the tags that were found in the content, replacing them with real content
        foreach ( $matches[0] as $key => $content_tag ) {
            // Assume there is no tag mapping available
            $tag_value = '';

            // Check if we have a mapping for this Yoast tag
            if ( isset( $mappings[ $content_tag ] ) ) {
                $tag_value = $mappings[ $content_tag ];
            }

            // Run the replacement
            $content = str_replace( $content_tag, $tag_value, $content );
 
        }

        // Return filtered results
        return apply_filters( 'wp_simple_seo_parser_parse_aioseo_tags', $content, $matches, $mappings );

    }

    /**
     * Parses Yoast style tags, returning the WP Simple SEO tag equivalents
     *
     * @since   1.0.0
     *
     * @param   string  $content    Content
     * @return  string              Content
     */
    public function parse_yoast_tags( $content ) {

        // Extract all tags from the content we're parsing
        preg_match_all( '/%%(.*?)%%/', $content, $matches );

        // If no tags were found, just return the content
        if ( empty( $matches ) || empty( $matches[0] ) ) {
            return $content;
        }

        // Get mappings
        $mappings = WP_Simple_SEO_Tags::get_instance()->get_yoast_tag_mappings();

        // Iterate through the tags that were found in the content, replacing them with real content
        foreach ( $matches[0] as $key => $content_tag ) {
            // Assume there is no tag mapping available
            $tag_value = '';

            // Check if we have a mapping for this Yoast tag
            if ( isset( $mappings[ $content_tag ] ) ) {
                $tag_value = $mappings[ $content_tag ];
            }

            // Run the replacement
            $content = str_replace( $content_tag, $tag_value, $content );
 
        }

        // Return filtered results
        return apply_filters( 'wp_simple_seo_parser_parse_yoast_tags', $content, $matches, $mappings );

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