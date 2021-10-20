<?php
/**
 * Google class.
 *
 * Acts as a wrapper for the Google Search Console and Site Verification classes at vendor/includes/google
 *
 * @package   WP_Simple_SEO
 * @author    WP Simple SEO
 * @version   1.0.0
 * @copyright WP Simple SEO
 */
class WP_Simple_SEO_Google {

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public $base;

    /**
     * Holds the Google client object.
     *
     * @since   1.0.0
     *
     * @var object
     */
    private $client;

    /**
     * The Google Client ID
     *
     * @since   1.0.0
     *
     * @var     string
     */
    private $client_id = '361445029462-ecr3tkikv5nti49m7ro5ihs027lugn6t.apps.googleusercontent.com';

    /**
     * The User's Access Token
     *
     * @since   1.0.0
     *
     * @var     string
     */
    private $access_token;

    /**
     * The oAuth Gateway Endpoint to use. This should be a site that has
     * the WP Simple SEO oAuth Gateway Plugin installed.
     *
     * @since   1.0.0
     *
     * @var     string
     */
    private $oauth_gateway_endpoint = 'https://wpsimpleseo.com/';

    /**
     * Returns whether WP Simple SEO is authorized to access the user's
     * Google account.
     *
     * @since   1.0.0
     *
     * @return  mixed    WP_Error | true
     */
    public function is_authorized() {

        // Store the access token
        $this->access_token = WP_Simple_SEO_Settings::get_instance()->get_setting( 'general', 'webmaster_tools[google_access_token]' );

        if ( empty( $this->access_token ) ) {
            return new WP_Error( 'wp_simple_seo_google_is_authorized', __( 'Google: No access token exists.', 'wp-simple-seo' ) );
        }

        // Setup the Google client
        if ( empty( $this->client ) ) {
            $this->client = $this->setup_client();
        }

        // If setting up the client failed, return WP_Error now
        if ( is_wp_error( $this->client ) ) {
            return $this->client;
        }

        // OK
        return true;

    }

    /**
     * Returns the Google oAuth URL used to get a code
     *
     * @since 1.0.0
     *
     * @return  mixed  WP_Error | string URL
     */
    public function get_authorize_url() {

        // Setup the Google client
        if ( empty( $this->client ) ) {
            $this->client = $this->setup_client();
        }

        // If setting up the client failed, return WP_Error now
        if ( is_wp_error( $this->client ) ) {
            return $this->client;
        }

        // Force prompt
        $this->client->setApprovalPrompt( 'force' );

        // Return the auth URL
        return $this->client->createAuthUrl();
        
    }


    /**
     * Checks if the given site URL is registered in the Google Search Console
     * for the oAuth user.
     *
     * @since   1.0.0
     *
     * @param   string  $site_url   Site URL
     * @return  bool                Site Regsitered and Verified in Search Console
     */
    public function site_registered( $site_url ) {

        // Setup the Google client
        if ( empty( $this->client ) ) {
            $this->client = $this->setup_client();
        }

        // If setting up the client failed, return WP_Error now
        if ( is_wp_error( $this->client ) ) {
            return $this->client;
        }

        // Setup the Webmaster object
        $webmaster = new Google_Service_Webmasters( $this->client );

        // Search for the site in Google Search Console
        try {
            $site = $webmaster->sites->get( $site_url );
        } catch( Google_Exception $e ) {
            // Site is not a verified Search Console site in this account
            return false;
        } catch( Google_Service_Exception $e ) {
            // Technical error
            return new WP_Error( 'wp_simple_seo_google_site_exists', $e->getMessage() );
        }

        // Site exists
        return true;

    }

    /**
     * Adds the given site URL to the Google Search Console
     * for the oAuth user.
     *
     * @since   1.0.0
     *
     * @param   string  $site_url   Site URL
     */
    public function site_add( $site_url ) {

        // Setup the Google client
        if ( empty( $this->client ) ) {
            $this->client = $this->setup_client();
        }

        // If setting up the client failed, return WP_Error now
        if ( is_wp_error( $this->client ) ) {
            return $this->client;
        }

        // Setup the Webmaster object
        $webmaster = new Google_Service_Webmasters( $this->client );

        // Add site to Google Search Console
        try {
            $webmaster->sites->add( $site_url );
        } catch( Google_Exception $e ) {
            return new WP_Error( 'wp_simple_seo_google_site_add', $e->getMessage() );
        } catch( Google_Service_Exception $e ) {
            return new WP_Error( 'wp_simple_seo_google_site_add', $e->getMessage() );
        }

        return true;

    }

    /**
     * Returns the meta tag needed to verify ownership of a web site with Google.
     *
     * @since   1.0.0
     *
     * @param   string  $site_url   Site URL to verify ownership of
     */
    public function site_verify_get_meta_tag_value( $site_url ) {

        // Setup the Google client
        if ( empty( $this->client ) ) {
            $this->client = $this->setup_client();
        }

        // If setting up the client failed, return WP_Error now
        if ( is_wp_error( $this->client ) ) {
            return $this->client;
        }

        try {
            // Setup Request Site
            $site = new Google_Service_SiteVerification_SiteVerificationWebResourceGettokenRequestSite();
            $site->setIdentifier( $site_url );
            $site->setType( 'SITE' );

            // Setup Request
            $request = new Google_Service_SiteVerification_SiteVerificationWebResourceGettokenRequest();
            $request->setSite( $site );
            $request->setVerificationMethod( 'META' );

            // Setup Site Verification
            $service = new Google_Service_SiteVerification( $this->client );
            $web_resource = $service->webResource;
            $result = $web_resource->getToken( $request );
        } catch( Google_Exception $e ) {
            return new WP_Error( 'wp_simple_seo_google_site_verify_get_meta_tag_value', $e->getMessage() );
        } catch( Google_Service_Exception $e ) {
            return new WP_Error( 'wp_simple_seo_google_site_verify_get_meta_tag_value', $e->getMessage() );
        }

        // Extract the value from the meta tag
        $start_tag = 'content="';
        $end_tag = '" />';
        $start_pos = strpos( $result->token, $start_tag );
        $end_pos = strpos( $result->token, $end_tag );
        $meta_tag = substr( $result->token, $start_pos + strlen( $start_tag ), ( $end_pos - ( $start_pos + strlen( $start_tag ) ) ) );

        // If the meta tag is blank, something went wrong
        if ( empty( $meta_tag ) ) {
            return new WP_Error( 'wp_simple_seo_google_site_verify_meta_tag_empty', __( 'The site verification meta tag from Google is blank, so we are unable to verify your web site ownership.', 'wp-simple-seo' ) );
        }

        // Return the token
        return $meta_tag;

    }

    /**
     * Submits the given site URL to Google to verify ownership of the site.
     * This should be called once the meta tag from site_get_verification_code() has been
     * stored and output on the site.
     *
     * @since   1.0.0
     *
     * @param   string  $site_url   Site URL
     * @return  mixed               WP_Error | true
     */
    public function site_verify( $site_url ) {

        // Setup the Google client
        if ( empty( $this->client ) ) {
            $this->client = $this->setup_client();
        }

        // If setting up the client failed, return WP_Error now
        if ( is_wp_error( $this->client ) ) {
            return $this->client;
        }

        try {
            // Setup Request Site
            $site = new Google_Service_SiteVerification_SiteVerificationWebResourceResourceSite();
            $site->setIdentifier( $site_url );
            $site->setType( 'SITE' );
             
            // Setup Request
            $request = new Google_Service_SiteVerification_SiteVerificationWebResourceResource();
            $request->setSite( $site );
             
            $service = new Google_Service_SiteVerification( $this->client );
            $web_resource = $service->webResource;
            $result = $web_resource->insert( 'META', $request );
        } catch( Google_Exception $e ) {
            // Meta token not found on site
            return new WP_Error( 'wp_simple_seo_google_site_verify', $e->getMessage() );
        } catch( Google_Service_Exception $e ) {
            // Technical error
            return new WP_Error( 'wp_simple_seo_google_site_verify', $e->getMessage() );
        }

        // Done
        return true;

    }

    /**
     * Determines whether the given site URL has had its ownership verified with Google.
     *
     * @since   1.0.0
     *
     * @param   string  $site_url   Site URL
     * @return  bool                Verified
     */
    public function site_verified( $site_url ) {

        // Setup the Google client
        if ( empty( $this->client ) ) {
            $this->client = $this->setup_client();
        }

        // If setting up the client failed, return WP_Error now
        if ( is_wp_error( $this->client ) ) {
            return $this->client;
        }

        // Setup the Site Verification object
        $site_verification = new Google_Service_SiteVerification( $this->client );

        try {
            // Get the site
            // If not found i.e. not verified, an exception will be thrown
            $result = $site_verification->webResource->get( $site_url );
        } catch( Google_Exception $e ) {
            // Site is not verified
            return false;
        } catch( Google_Service_Exception $e ) {
            // Technical error
            return new WP_Error( 'wp_simple_seo_google_site_verified', $e->getMessage() );
        }

        // If no exception was thrown, the site is verified.
        return true;

    }

    /**
     * Checks if the given site and sitemap URL is registered in the Google Search Console
     * for the oAuth user.
     *
     * @since   1.0.0
     *
     * @param   string  $site_url           Site URL
     * @param   string  $xml_sitemap_url    Sitemap URL
     */
    public function sitemap_exists( $site_url, $xml_sitemap_url ) {

        // Setup the Google client
        if ( empty( $this->client ) ) {
            $this->client = $this->setup_client();
        }

        // If setting up the client failed, return WP_Error now
        if ( is_wp_error( $this->client ) ) {
            return $this->client;
        }

        // Setup the Webmaster object
        $webmaster = new Google_Service_Webmasters( $this->client );

        // Search for the site in Google Search Console
        try {
            $sitemap = $webmaster->sitemaps->get( $site_url, $site_url . '/' . $xml_sitemap_url );
        } catch( Google_Exception $e ) {
            // Site is not a verified Search Console site in this account
            return false;
        } catch( Google_Service_Exception $e ) {
            // Technical error
            return new WP_Error( 'wp_simple_seo_google_sitemap_exists', $e->getMessage() );
        }

        // Site exists
        return true;

    }

    /**
     * Adds the given sitemap URL to the Google Search Console
     * for the given Site URL and oAuth user.
     *
     * @since   1.0.0
     *
     * @param   string  $site_url           Site URL
     * @param   string  $xml_sitemap_url    Sitemap URL
     */
    public function sitemap_add( $site_url, $xml_sitemap_url ) {

        // Setup the Google client
        if ( empty( $this->client ) ) {
            $this->client = $this->setup_client();
        }

        // If setting up the client failed, return WP_Error now
        if ( is_wp_error( $this->client ) ) {
            return $this->client;
        }

        // Setup the Webmaster object
        $webmaster = new Google_Service_Webmasters( $this->client );

        // Search for the site in Google Search Console
        try {
            $sitemap = $webmaster->sitemaps->submit( $site_url, $site_url . '/' . $xml_sitemap_url );
        } catch( Google_Exception $e ) {
            // Error
            return new WP_Error( 'wp_simple_seo_google_sitemap_add', $e->getMessage() );
        } catch( Google_Service_Exception $e ) {
            // Technical error
            return new WP_Error( 'wp_simple_seo_google_sitemap_add', $e->getMessage() );
        }

        // Site exists
        return true;

    }

    /**
     * Initializes the Google Client object
     *
     * @since 1.0.0
     */
    private function setup_client() {

        // Get base instance
        $this->base = WP_Simple_SEO::get_instance();

        // If the Google_Client class already exists, a Theme or Plugin is loading the Google PHP Library.
        if ( ! class_exists( 'Google_Client' ) ) {
            // Load the Google PHP SDK Autoloader
            require $this->base->plugin->folder . 'includes/vendor/google/autoload.php';
        }

        // Setup the client with the client ID
        $client = new Google_Client();
        $client->setClientID( $this->client_id );

        // Request the permissions we need
        $client->addScope( array(
            Google_Service_Webmasters::WEBMASTERS, 
            Google_Service_SiteVerification::SITEVERIFICATION
        ) );

        // Set the redirect URI to wpsimpleseo.com, which will forward on the access token back to this WP install
        // It has to be a fixed URI, as Google API requires a specific redirect URI
        $client->setRedirectUri( $this->oauth_gateway_endpoint );

        // Request offline access, so we can get a refresh token
        $client->setAccessType( 'offline' );

        // Set this site's URL as the state parameter. This is passed to wpsimpleseo.com, so we can redirect the user
        // back to the admin screen with their auth token
        $client->setState( urlencode( admin_url( 'admin.php?page=wp-simple-seo' ) ) );

        // If an access token exists in settings, attempt to set it in the client now
        $settings = WP_Simple_SEO_Settings::get_instance()->get_plugin_settings( 'general' );
        if ( ! empty( $settings['webmaster_tools']['google_access_token'] ) ) {
            $client->setAccessToken( $settings['webmaster_tools']['google_access_token'] );
            
            // If the access token has expired, get a new one now from wpsimpleseo.com
            if ( $client->isAccessTokenExpired() ) {
                
                // Build URL
                $url = add_query_arg( array(
                    'access_token'  => $settings['webmaster_tools']['google_access_token'],
                    'state'         => get_bloginfo( 'url' ),
                ), $this->oauth_gateway_endpoint );

                // Get JSON response
                $response = wp_remote_get( $url );
                $json = json_decode( $response['body'] );

                // Check whether the request was successful
                if ( ! isset( $json->success ) || ! $json->success ) {
                    // Something went wrong
                    return new WP_Error( 'wp_simple_seo_google_webmaster', ( isset( $json->data ) ? $json->data : __( 'Google: An error occured whilst attempting to refresh your access token. Please try again.', 'wp-simple-seo' ) ) );
                }

                // Fetch access token
                $access_token = stripslashes( $json->data->access_token );

                // Store the new access and refresh tokens in our settings
                $settings['webmaster_tools']['google_access_token'] = $access_token;
                WP_Simple_SEO_Settings::get_instance()->update_plugin_settings( 'general', $settings );
                
                // Apply the new access and refresh tokens to the client
                $client->setAccessToken( $access_token );
            }
        }

        // Return the client instance
        return $client;

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