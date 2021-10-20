<?php
/**
 * AJAX class
 * 
 * @package   WP_Simple_SEO
 * @author    WP Simple SEO
 * @version   1.0.0
 * @copyright WP Simple SEO
 */
class WP_Simple_SEO_AJAX {

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

        // Actions
        add_action( 'wp_ajax_wp_simple_seo_get_snippet_preview', array( $this, 'get_snippet_preview' ) );

    }

    /**
     * Returns the Snippet Preview comprising of the Title and Description,
     * based on the supplied POSTed data.
     *
     * Used for Post and Term Meta Previews
     *
     * @since 1.0.0
     */
    public function get_snippet_preview() {

        // Run a security check first.
        check_ajax_referer( 'wp-simple-seo-get-snippet-preview', 'nonce' );

        // Get vars
        $id             = absint( $_POST['id'] );
        $type           = sanitize_text_field( $_POST['type'] );
        $title          = sanitize_text_field( $_POST['title'] );
        $description    = sanitize_text_field( $_POST['description'] );
        $form_data      = array();
        parse_str( $_POST['form_data'], $form_data );

        // If we have form data, make some adjustments to the key names so they match the equivalent object
        if ( isset( $form_data['excerpt'] ) ) {
            $form_data['post_excerpt'] = $form_data['excerpt'];
            unset( $form_data['excerpt'] );
        }

        // Get parser instance, and parse the title and description
        // depending on the type (post | term)
        $parser = WP_Simple_SEO_Parser::get_instance();
        switch ( $type ) {
            /**
             * Post
             */
            case 'post':
                // Get Post by ID, parse Title and Description
                $post        = get_post( $id );
                $title       = $parser->parse_tags_using_form_data( $title, 'post_single', $post, $form_data );
                $description = $parser->parse_tags_using_form_data( $description, 'post_single', $post, $form_data );
                break;

            /**
             * Taxonomy Term
             */
            case 'term':
                // Get Term by ID, parse Title and Description
                $term        = get_term( $id );
                $title       = $parser->parse_tags_using_form_data( $title, 'taxonomy', $term, $form_data );
                $description = $parser->parse_tags_using_form_data( $description, 'taxonomy', $term, $form_data );
                break;

        }

        // Build data array
        $data = array(
            'post'          => $form_data, // Debug
            'title'         => $title,
            'description'   => $description,
        );

        // Filter
        $data = apply_filters( 'wp_simple_seo_ajax_get_snippet_preview', $data );

        // Return
        wp_send_json_success( $data );

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