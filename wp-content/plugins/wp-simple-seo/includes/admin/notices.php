<?php
/**
 * Notice class. Persists success and error messages
 * across Admin Screens, primarily for Bulk actions
 * on the Media List View.
 * 
 * @package   WP_Simple_SEO
 * @author    WP Simple SEO
 * @version   1.0.0
 * @copyright WP Simple SEO
 */
class WP_Simple_SEO_Notices {

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Holds success and error notices to be displayed
     *
     * @since   1.0.0
     *
     * @var     array
     */
    public $notices = array(
        'success'   => array(),
        'error'     => array(),
    );

    /**
     * Whether to store notices for displaying on the next page load.
     *
     * Set using enable_persistence() and disable_persistence(),
     * useful for the Media Library's Grid view
     *
     * @since   1.0.0
     *
     * @var     bool
     */
    private $store = false; 

    /**
     * The key prefix to use for stored notices
     *
     * @since   1.0.0
     *
     * @var     string
     */
    private $key_prefix = '_wp_simple_seo_notices';

    /**
     * Constructor
     *
     * @since   1.0.0
     */
    public function __construct() {

        add_action( 'admin_notices', array( $this, 'output_notices' ) );

    }

    /**
     * Enable persistence on notices
     *
     * @since   1.0.0
     */
    public function enable_store() {

        $this->store = true;

    }

    /**
     * Disable persistence on notices
     *
     * @since   1.0.0
     */
    public function disable_store() {

        $this->store = false;

    }

    /**
     * Returns all Success Notices that need to be displayed.
     *
     * @since   1.0.0
     *
     * @return  array   Notices
     */ 
    public function get_success_notices() {

        // Get notices from store, if required
        if ( $this->store ) {
            $this->notices = $this->get_notices();
        }

        // Get success notices
        $success_notices = ( isset( $this->notices['success'] ) ? $this->notices['success'] : array() );

        // Allow devs / addons to filter notice
        $success_notices = apply_filters( 'wp_simple_seo_notices_get_success_notices', $success_notices, $this->notices );

        // Return
        return $success_notices;

    }

    /**
     * Add a single Success Notice
     *
     * @since   1.0.0
     *
     * @param   string  $message    Message
     * @return  bool                Success
     */
    public function add_success_notice( $value ) {

        // Get notices from store, if required
        if ( $this->store ) {
            $this->notices = $this->get_notices();
        }

        // Add success notice
        if ( isset( $this->notices['success'] ) ) {
            $this->notices['success'][] = $value;
        } else {
            $this->notices['success'] = array( $value );
        }

        // Store notices, if required
        if ( $this->store ) {
            $this->save_notices( $this->notices );
        }

    }

    /**
     * Returns all Error Notices that need to be displayed.
     *
     * @since   1.0.0
     *
     * @return  array   Notices
     */ 
    public function get_error_notices() {

        // Get notices from store, if required
        if ( $this->store ) {
            $this->notices = $this->get_notices();
        }

        // Get error notices
        $error_notices = ( isset( $this->notices['error'] ) ? $this->notices['error'] : array() );

        // Allow devs / addons to filter notice
        $error_notices = apply_filters( 'wp_simple_seo_notices_get_error_notices', $error_notices, $this->notices );

        // Return
        return $error_notices;

    }

    /**
     * Add a single Error Notice
     *
     * @since   1.0.0
     *
     * @param   string  $message    Message
     * @return  bool                Success
     */
    public function add_error_notice( $value ) {

        // Get notices from store, if required
        if ( $this->store ) {
            $this->notices = $this->get_notices();
        }

        // Add success notice
        if ( isset( $this->notices['error'] ) ) {
            $this->notices['error'][] = $value;
        } else {
            $this->notices['error'] = array( $value );
        }

        // Store notices, if required
        if ( $this->store ) {
            $this->save_notices( $this->notices );
        }

    }

    /**
     * Returns all Success and Error notices
     *
     * @since   1.0.0
     *
     * @return  array   Notices
     */
    private function get_notices() {

        // Get notices
        $notices = get_option( $this->key_prefix );

        // Allow devs / addons to filter notices
        $notices = apply_filters( 'wp_simple_seo_notices_get_notices', $notices );

        // If not an array, setup
        if ( ! is_array( $notices ) ) {
            $notices = array(
                'success'   => array(),
                'error'     => array(),
            );
        }

        // If some keys aren't set, define them now
        if ( ! isset( $notices['success'] ) ) {
            $notices['success'] = array();
        }
        if ( ! isset( $notices['error'] ) ) {
            $notices['error'] = array();
        }

        // Return
        return $notices;

    }

    /**
     * Saves the given notices array.
     *
     * @since   1.0.0
     *
     * @param    array   $notices   Notices
     * @return   bool               Success
     */
    private function save_notices( $notices ) {

        // Allow devs / addons to filter notices
        $notices = apply_filters( 'wp_simple_seo_notices_save_notices', $notices );

        // Update settings
        update_option( $this->key_prefix, $notices );
        
        return true;

    }

    /**
     * Deletes all notices
     *
     * @since   1.0.0
     */
    public function delete_notices() {

        // Delete notices
        delete_option( $this->key_prefix );

         // Allow devs / addons to run any other actions now
        do_action( 'wp_simple_seo_notices_delete_notices' );

        return true;

    }

    /**
     * Output any success and error notices
     *
     * @since   1.0.0
     */
    public function output_notices() {

        // If no notices exist in the class, check the storage
        if ( count( $this->notices['success'] ) == 0 &&
             count( $this->notices['error'] ) == 0 ) {
            $this->notices = $this->get_notices();
        }

        // Success
        if ( count( $this->notices['success'] ) > 0 ) {
            ?>
            <div class="notice notice-success is-dismissible">
                <p>
                    <?php echo implode( '<br />', $this->notices['success'] ); ?>
                </p>
            </div>
            <?php
        }

        // Error
        if ( count( $this->notices['error'] ) > 0 ) {
            ?>
            <div class="notice notice-error is-dismissible">
                <p>
                    <?php echo implode( '<br />', $this->notices['error'] ); ?>
                </p>
            </div>
            <?php
        }

        // Clear storage if it's not enabled
        // This prevents notices stored in this page request from being immediately destroyed
        if ( ! $this->store ) {
            $this->delete_notices();
        }

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