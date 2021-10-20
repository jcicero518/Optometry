<?php
/**
 * Admin Bar class
 * 
 * @package   WP_Simple_SEO
 * @author    WP Simple SEO
 * @version   1.0.7
 * @copyright WP Simple SEO
 */
class WP_Simple_SEO_Admin_Bar {

    /**
     * Holds the class object.
     *
     * @since   1.0.7
     *
     * @var     object
     */
    public static $instance;

    /**
     * Holds the base class object.
     *
     * @since   1.0.7
     *
     * @var     object
     */
    public $base;

    /**
     * Constructor
     *
     * @since 1.0.0
     */
    public function __construct() {

        // Actions
        add_action( 'wp_enqueue_scripts', array( $this, 'css' ) );
        add_action( 'admin_bar_menu', array( $this, 'register_admin_bar_menus' ), 81 );
        
    }

    /**
     * Enqueues CSS for the Admin Bar
     *
     * @since   1.0.7
     */
    public function css() {

        // Allow Addons to define Menus now
        $groups = apply_filters( 'wp_simple_seo_admin_bar_register_admin_bar_menus', array() );

        // Bail if no groups
        if ( empty( $groups ) ) {
            return;
        }

        // Get base instance
        $this->base = WP_Simple_SEO::get_instance();

        // Enqueue CSS
        wp_enqueue_style( 'dashicons' );
        wp_enqueue_style( $this->base->plugin->name . '-admin-bar', $this->base->plugin->url . 'assets/css/admin-bar.css' );

    }

    /**
     * Registers Menus in the WordPress Admin Bar
     *
     * @since   1.0.7
     *
     * @param   WP_Admin_Bar    $wp_admin_bar   WordPress Admin Bar Object
     */
    public function register_admin_bar_menus( $wp_admin_bar ) {

        // Allow Addons to define Menus now
        $groups = apply_filters( 'wp_simple_seo_admin_bar_register_admin_bar_menus', array() );

        // Bail if no groups
        if ( empty( $groups ) ) {
            return;
        }

        // Register groups
        foreach ( $groups as $group_id => $group ) {
            // Define title depending on whether Content Analysis passes or fails
            $wp_admin_bar->add_menu( array(
                'id'    => $group_id,
                'title' => '<span class="dashicons dashicons-' . ( ( $group['class'] == 'green' ) ? 'yes' : 'no' ) . '"></span>' .  $group['title'],
                'href'  => $group['href'],
                'meta'  => array(
                    'class' => $group['class'],
                ),
            ) );

            // Add Menu Items
            foreach ( $group['items'] as $menu_item_id => $menu_item ) {
                $wp_admin_bar->add_menu( array(
                    'id'    => $menu_item_id,
                    'parent'=> $group_id,
                    'title' => '<span class="dashicons dashicons-' . ( ( $menu_item['class'] == 'green' ) ? 'yes' : 'no' ) . '"></span>' .  $menu_item['title'],
                    'href'  => $menu_item['href'],
                    'meta'  => array(
                        'class' => $menu_item['class'],
                    ),
                ) );
            }
        }

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since   1.0.7
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