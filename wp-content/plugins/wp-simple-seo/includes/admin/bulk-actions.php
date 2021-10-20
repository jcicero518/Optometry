<?php
/**
 * Bulk Actions class
 * 
 * @package   WP_Simple_SEO
 * @author    WP Simple SEO
 * @version   1.0.0
 * @copyright WP Simple SEO
 */
class WP_Simple_SEO_Bulk_Actions {

    /**
     * Holds the class object.
     *
     * @since   1.0.0
     *
     * @var     object
     */
    public static $instance;

    /**
     * Holds the base class object.
     *
     * @since   1.0.0
     *
     * @var     object
     */
    public $base;

    /**
     * Constructor
     *
     * @since   1.0.0
     */
    public function __construct() {

        add_action( 'admin_init', array( $this, 'register_bulk_action_filters' ) );
        add_action( 'admin_footer', array( $this, 'output_bulk_action_views' ) );
        
    }

    /**
     * Fires filters for registering Bulk Actions on Public Post Types
     * in the WordPress Administration
     *
     * @since   1.0.0
     */
    public function register_bulk_action_filters() {

        // Get public Post Types
        $post_types = WP_Simple_SEO_Common::get_instance()->get_post_types();

        // Bail if no Post Types
        if ( empty( $post_types ) ) {
            return;
        }

        // For each Post Type, add filters for Bulk Actions
        foreach ( $post_types as $post_type ) {
            add_filter( 'bulk_actions-edit-' . $post_type->name, array( $this, 'register_bulk_actions' ) ); 
            add_filter( 'handle_bulk_actions-edit-' . $post_type->name, array( $this, 'handle_bulk_actions' ), 10, 3 ); 
        }

    }

    /**
     * Adds Bulk Action options to Post Type WP_List_Tables
     *
     * @since   1.0.0
     *
     * @param   array   $actions    Registered Bulk Actions
     * @return  array               Registered Bulk Actions
     */
    public function register_bulk_actions( $actions ) {

        // Allow Addons to define Bulk Actions
        $bulk_actions = apply_filters( 'wp_simple_seo_bulk_actions_register_bulk_actions', array() );

        // Bail if no bulk actions
        if ( empty( $bulk_actions ) ) {
            return $actions;
        }

        // Merge with default Bulk Actions
        $actions = array_merge( $bulk_actions, $actions );

        // Return
        return $actions;

    }

    /**
     * Handles Bulk Actions when one is selected to run
     *
     * @since   1.0.0
     *
     * @param   string  $redirect_to    Redirect URL
     * @param   string  $action         Bulk Action to Run
     * @param   array   $post_ids       Post IDs to apply Action on
     * @return  string                  Redirect URL
     */
    public function handle_bulk_actions( $redirect_to, $action, $post_ids ) {

        // Bail if the action isn't specified
        if ( empty( $action ) ) {
            return $redirect_to;
        }

        // Bail if no Post IDs
        if ( empty( $post_ids ) ) {
            return $redirect_to;
        }

        // Allow Addons to run Bulk Actions now
        do_action( 'wp_simple_seo_bulk_actions_handle_bulk_actions', $action, $post_ids );
        do_action( 'wp_simple_seo_bulk_actions_handle_bulk_actions_' . $action, $post_ids );

        // Return redirect
        return $redirect_to;

    }

    /**
     * Outputs hidden Bulk Action Views for Addons to later reference and display
     * when the user chooses a Bulk Action in a WP_List_Table
     *
     * @since   1.0.0
     */
    public function output_bulk_action_views() {

        // Bail if we're not on a WP_List_Table
        $screen = get_current_screen();
        if ( $screen->base != 'edit' ) {
            return;
        }

        // Get base instance
        $this->base = WP_Simple_SEO::get_instance();

        // Bail if no Addons have defined Bulk Actions
        $bulk_actions = apply_filters( 'wp_simple_seo_bulk_actions_register_bulk_actions', array() );
        if ( empty( $bulk_actions ) ) {
            return;
        }

        // Load View
        include_once( $this->base->plugin->folder . '/views/admin/bulk-actions.php' ); 

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since   1.0.0
     *
     * @return  object Class.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {
            self::$instance = new self;
        }

        return self::$instance;

    }

}