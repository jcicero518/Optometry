<?php
/**
 * Generic class for interacting with token based JSON APIs
 * conforming to JSend.
 *
 * Provides an API endpoint option, with post() and get() functions
 * which wrap wp_remote_post() / wp_remote_get()
 * 
 * @package   WP_Simple_SEO
 * @author    WP Simple SEO
 * @version   1.0.0
 * @copyright WP Simple SEO
 */
class WP_Simple_SEO_API {

    /**
     * The API Endpoint
     *
     * @since   1.0.0
     *
     * @var     string
     */
    private $api_endpoint = '';

    /**
     * The License Key
     *
     * @since   1.0.0
     *
     * @var     string
     */
    private $license_key = '';

    /**
     * Constructor.  Sets the API endpoint.
     *
     * @since   1.0.0
     *
     * @param   string  $api_endpoint   API Endpoint
     * @param   string  $license_key    License Key
     */
    public function __construct( $api_endpoint, $license_key ) {

        $this->api_endpoint     = $api_endpoint;
        $this->license_key      = $license_key;

    }

    /**
     * Gets the HTML from the given URL
     *
     * @since   1.0.0
     *
     * @param   string  $url    URL
     * @return  mixed           WP_Error | string
     */
    public function get_html_from_url( $url ) {

        // Perform GET request
        $response = wp_remote_get( $url, array(
            'timeout'   => 20,
        ) );

        // Bail if an error
        if ( is_wp_error( $response ) ) {
            return $response;
        }

        // Return body
        return wp_remote_retrieve_body( $response );

    }

    /**
     * Performs a POST request
     *
     * @since   1.0.0
     *
     * @param   string  $url        URL
     * @param   array   $arguments  Arguments
     */
    public function post( $url, $arguments ) {

        // Perform POST request
        $response = wp_remote_post( $this->api_endpoint . '/?api=1&license_key=' . $this->license_key, array(
            'timeout'   => 20,
            'headers'   => array(
                'Accept'        => 'application/json',
            ),
            'body'      => array(
                'endpoint'  => $url,
                'params'    => $arguments,
            ),
        ) );

        // Bail if an error
        if ( is_wp_error( $response ) ) {
            return $response;
        }

        // Get body
        $body = wp_remote_retrieve_body( $response );

        // Decode JSON
        $results = json_decode( $body, TRUE );

        // If the success flag is false, bail
        if ( ! $results['success'] ) {
            return new WP_Error( 'wp_simple_seo_post_error', implode( "\n", $results['data']['errors'] ) );
        }

        // If there is no data, something went wrong
        if ( ! isset( $results['data'] ) ) {
            return new WP_Error( 'wp_simple_seo_post_error', __( 'No data was found in the response', 'wp-simple-seo' ) );
        }

        // Return
        return $results['data'];

    }

}