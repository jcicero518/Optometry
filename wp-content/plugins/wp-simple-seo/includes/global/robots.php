<?php
/**
 * Robots class, covering:
 * - Rewrite rule for /robots.txt
 * - Output of content for /robots.txt
 * 
 * @package   WP_Simple_SEO
 * @author    WP Simple SEO
 * @version   1.0.0
 * @copyright WP Simple SEO
 */
class WP_Simple_SEO_Robots {

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

        add_action( 'init', array( $this, 'register_rewrite_rules' ), 2 );
        add_action( 'wp', array( $this, 'output_robots_txt' ), 3 );

        // Don't cache robots.txt if we're in development mode.
        // This helps developers who might be testing sitemaps to ensure they're seeing the most up to date content
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            add_filter( 'wp_simple_seo_robots_enable_caching', '__return_false' );
        }
        
    }

    /**
     * Registers a rewrite rule to catch robots.txt requests, routing
     * them to a rewrite tag we then use
     *
     * @since 1.0.0
     */
    public function register_rewrite_rules() {

        // Register the rewrite tag, so we can use get_query_var()
        add_rewrite_tag( '%wp_simple_seo_robots%', '([^&]+)' );

        // Register the rewrite rules
        add_rewrite_rule( 
            '^robots.txt$',
            'index.php?wp_simple_seo_robots=1',
            'top'
        );
        
    }

    /**
     * If the query contains the wp_simple_seo_robots rewrite tag,
     * output robots.txt.
     *
     * @since 1.0.0
     */
    public function output_robots_txt() {

        // Get query vars
        $robots      = absint( get_query_var( 'wp_simple_seo_robots' ) );

        // Bail if we're not trying to output robots.txt
        if ( ! $robots ) {
            return;
        }

        // Start building the robots.txt content
        $entries = array(
            'User-agent: *',
        );

        // Disallow everything if Reading > Search Engine Visibility is set
        if ( ! get_option( 'blog_public' ) ) {
            $entries[] = 'Disallow: /';
        } else {
            // Disallow wp-admin, but allow admin-ajax
            $entries[] = 'Disallow: /wp-admin/';
            $entries[] = 'Allow: /wp-admin/admin-ajax.php';
        }

        // Output
        $this->output_robots( $entries );

    }

    /**
     * Outputs robots.txt, based on the given $entries
     *
     * @since   1.0.0
     *
     * @param   array   $entries    Entries
     */
    private function output_robots( $entries ) {

        // For each entry, build the text file
        $output = implode( "\n", $entries );

        // Filter output
        $output = apply_filters( 'wp_simple_seo_robots_output_robots', $output, $entries );

        // Output
        header( 'Content-type: text' );
        echo $output;
        die();

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