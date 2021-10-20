<?php
/**
 * Settings class
 * 
 * @package   WP_Simple_SEO
 * @author    WP Simple SEO
 * @version   1.0.0
 * @copyright WP Simple SEO
 */
class WP_Simple_SEO_Settings {

    /**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Retrieves a setting from the options table.
     *
     * If a Post ID is specified, attempts to retrieve the setting from the Post Meta table first,
     * falling back to the options table if no Post meta setting exists.
     *
     * If the key doesn't exist, the default value will be returned as defined in get_settings_defaults()
     *
     * @since   1.0.0
     *
     * @param   string  $type       Setting Type
     * @param   string  $key        Setting key value to retrieve
     * @param   int     $id         Post or Term ID
     * @return  string              Post / Term Meta Value, Plugin Option Value or Default Value
     */
    public function get_setting( $type, $key, $id = '' ) {

        // By default, assume we can't get the setting's value
        $result = new WP_Error( 'setting_missing', __( 'Setting does not exist', 'wp-simple-seo' ) );

        // If an ID has been specified, attempt to get the setting from the Post or Term
        if ( ! empty( $id ) ) {
            if ( strpos( $key, 'taxonomies[' ) !== false ) {
                // Term
                $result = $this->get_term_setting( $type, $key, $id );
            } else {
                // Post
                $result = $this->get_post_setting( $type, $key, $id );
            }
        }

        // If the result isn't an error, return the value
        if ( ! is_wp_error( $result ) ) {
            // If we're returning a string (i.e. the actual value), run data validation on it
            if ( ! is_array( $result ) ) {
                $result = $this->escape_string( $result );
            }

            return $result;
        }

        // Get plugin setting (will fallback to default if plugin setting doesn't exist)
        $result = $this->get_plugin_setting( $type, $key );
        
        // If the result isn't an error, return the value
        if ( ! is_wp_error( $result ) ) {
            // If we're returning a string (i.e. the actual value), run data validation on it
            if ( ! is_array( $result ) ) {
                $result = $this->escape_string( $result );
            }

            return $result;
        }
        
        // If an error occured, return a blank string
        return '';

    }

    /**
     * Returns a setting from the WordPress Post Meta table for the given Type,
     * falling back to the Plugin Settings, and finally Default Settings, if
     * no value is found.
     *
     * @since 1.0.0
     *
     * @param   string  $type       Setting Type
     * @param   string  $key        Setting key value to retrieve
     * @param   int     $id         Post ID
     * @return  string              Post Meta Value, Option Value or Default Value
     */
    private function get_post_setting( $type, $key, $id ) {

        // Get settings from Post
        $settings = $this->get_post_settings( $type, $id );

        // Return
        return $this->extract_setting( $key, $settings );

    }

    /**
     * Returns a setting from the WordPress Term Meta table for the given Type,
     * falling back to the Plugin Settings, and finally Default Settings, if
     * no value is found.
     *
     * @since 1.0.0
     *
     * @param   string  $type       Setting Type
     * @param   string  $key        Setting key value to retrieve
     * @param   int     $id         Term ID
     * @return  string              Term Meta Value, Option Value or Default Value
     */
    private function get_term_setting( $type, $key, $id ) {

        // Get settings from Post
        $settings = $this->get_term_settings( $type, $id );

        // Return
        return $this->extract_setting( $key, $settings );

    }

    /**
     * Returns a setting from the WordPress Options table for the given Type
     *
     * Returns a default setting if no setting is found.
     *
     * @since 1.0.0
     *
     * @param   string  $type       Setting Type
     * @param   string  $key        Setting key value to retrieve
     * @return  array               Settings
     */
    private function get_plugin_setting( $type, $key ) {

        // Get settings from Post
        $settings = $this->get_plugin_settings( $type );

        // Return
        return $this->extract_setting( $key, $settings );

    }

    /**
     * Returns settings from the WordPress Post Meta table for the given Type
     *
     * Individual key/value meta settings are stored in a structured array that
     * matches the structure of the Plugin / Options table array.
     *
     * @since   1.0.0
     *
     * @param   string  $type   Type    (e.g. meta)
     * @param   int     $id     Post ID
     * @return  array           Settings
     */
    public function get_post_settings( $type, $id ) {

        // Get Post Type
        $post_type = get_post_type( $id );

        // Get Post Meta Settings
        $settings = get_post_meta( $id, '_wp_simple_seo_' . $type, true );
        
        // Allow devs to filter before returning
        $settings = apply_filters( 'wp_simple_seo_settings_get_post_settings', $settings, $type, $id );

        // Return result
        return $settings;

    }

    /**
     * Returns settings from the WordPress Term Meta table for the given Type.
     *
     * Individual key/value meta settings are stored in a structured array that
     * matches the structure of the Plugin / Options table array.
     *
     * @since 1.0.0
     *
     * @param   string  $type       Type    (e.g. meta)
     * @param   int     $term_id    Term ID
     * @return  array               Settings
     */
    private function get_term_settings( $type, $term_id ) {

        // Get Term
        $term = get_term( $term_id );

        // Define structured array that follows Plugin settings
        $settings = array(
            'taxonomies' => array(
                $term->taxonomy => array(
                ),
            ),
        );

        // Get Term Meta Settings
        $title          = get_term_meta( $term_id , '_wp_simple_seo_' . $type . '_title', true );
        $description    = get_term_meta( $term_id , '_wp_simple_seo_' . $type . '_description', true );
        $noindex        = get_term_meta( $term_id , '_wp_simple_seo_' . $type . '_noindex', true );

        // If a Term Meta Setting isn't blank, assign it to the array
        if ( ! empty( $title ) ) {
            $settings['taxonomies'][ $term->taxonomy ]['title'] = $title;   
        }
        if ( ! empty( $description ) ) {
            $settings['taxonomies'][ $term->taxonomy ]['description'] = $description;   
        }
        if ( ! empty( $noindex ) ) {
            $settings['taxonomies'][ $term->taxonomy ]['noindex'] = $noindex;   
        }

        // Allow devs to filter before returning
        $settings = apply_filters( 'wp_simple_seo_settings_get_term_settings', $settings, $type, $term_id );

        // Return result
        return $settings;

    }

    /**
     * Returns settings from the WordPress Options table for the given Type
     *
     * @since 1.0.0
     *
     * @param   string $type    Type
     * @return  array           Settings
     */
    public function get_plugin_settings( $type ) {

        // Get current settings
        $settings = get_option( 'wp-simple-seo-' . $type );

        // Allow devs to filter before returning
        $settings = apply_filters( 'wp_simple_seo_settings_get_plugin_settings', $settings, $type );
        
        // If settings are blank, return defaults
        if ( ! $settings || empty( $settings ) ) {
            $settings = $this->get_default_settings( $type );
        }

        // @TODO Recursively compare $settings with $defaults, using value of $defaults where the equivalent in
        // $settings does not have a key.
        
        // Return result
        return $settings;

    }

    /**
     * Returns the default settings for the given Type
     *
     * These settings are used as the fallbacks if none are defined in the Post Meta,
     * Taxonomy Term or Options tables.
     *
     * @since   1.0.0
     *
     * @param   string $type    Type
     * @return  mixed           Default Settings | empty string
     */
    private function get_default_settings( $type ) {

        // Define defaults
        $defaults = array(
            'welcome' => array(
                'displayed' => 0,
            ),
            'general' => array(
                'webmaster_tools' => array(
                    'google_verification' => '',
                    'google_access_token' => '',
                    'bing_verification'   => '',
                ),
                'knowledge_graph' => array(
                    'type'          => 'company',
                    'name'          => get_bloginfo( 'name' ),
                    'alternate_name'=> '',
                    'logo'          => '',
                ),
                'sitelinks_searchbox' => array(
                    'enabled'       => 1,
                ),
            ),
            'meta' => array(
                'general' => array(
                    'title_separator'       => '&mdash;',
                    'pagination_separator'  => '/',
                    'noodp'                 => 1,
                    'noydir'                => 1,
                ),
                'home' => array(
                    'title'         => '{site_name}',
                    'description'   => '{site_description}', 
                ),
                'post_types' => array(
                    // Will be populated later in this function
                ),
                'taxonomies' => array(
                    // Will be populated later in this function
                ),
                'archives' => array(
                    'author' => array(  
                        'title'         => '{author_display_name} {pagination_page_total} {title_separator} {site_name}',
                        'description'   => '{author_description}', 
                        'noindex'       => ( WP_Simple_SEO_Settings::get_instance()->site_has_multiple_authors() ? 0 : 1 ),
                    ),
                    'date' => array(  
                        'title'         => '{date_year} {date_month} {date_day} {pagination_page_total} {title_separator} {site_name}',
                        'description'   => '{date_year} {date_month} {date_day}', 
                        'noindex'       => ( WP_Simple_SEO_Settings::get_instance()->site_has_multiple_authors() ? 0 : 1 ),
                    ),
                ),
                'search' => array(
                    'title'         => __( 'Search Results for ', 'wp-simple-seo' ) . '{search_terms} {title_separator} {site_name}',
                    'description'   => __( 'Search Results for ', 'wp-simple-seo' ) . '{search_terms}', 
                ),
                'four04' => array(
                    'title'         => __( '404 Not Found', 'wp-simple-seo' ) . ' {title_separator} {site_name}',
                ),
            ),
            'social' => array(
                'general' => array(
                    'enabled' => 1,
                ),
                'facebook' => array(
                    'default_image' => '',
                    'url'           => '',
                ),
                'twitter' => array(
                    'card_type' => 'summary',
                    'username'  => '',
                    'url'       => '',
                ),
                'google'    => array(
                    'url'   => '',
                ),
                'instagram' => array(
                    'url'   => '',
                ),
                'youtube'   => array(
                    'url'   => '',
                ),
                'linkedin'  => array(
                    'url'   => '',
                ),
                'myspace' => array(
                    'url'   => '',
                ),
                'pinterest' => array(
                    'url'   => '',
                ),
                'soundcloud' => array(
                    'url'   => '',
                ),
                'tumblr' => array(
                    'url'   => '',
                ),
            ),
            'sitemap' => array(
                'general' => array(
                    'enabled' => 1,
                ),
            ),
        );

        // Depending on the registered Post Types, add some more defaults
        $post_types = WP_Simple_SEO_Common::get_instance()->get_post_types();
        foreach ( $post_types as $post_type ) {
            // Setup array
            $defaults['meta']['post_types'][ $post_type->name ] = array();

            // Single
            $defaults['meta']['post_types'][ $post_type->name ]['single'] = array(
                'title'         => '{post_title} {title_separator} {site_name}',
                'description'   => '{post_excerpt}',
                'noindex'       => 0,
                'nofollow'      => 0,
                'noimageindex'  => 0,
                'meta_box'      => 1,
                'canonical'     => '',
            );

            // If this post type has_archive support, add archive defaults
            if ( $this->post_type_has_archive( $post_type->name ) ) {
                // Archive
                $defaults['meta']['post_types'][ $post_type->name ]['archive'] = array(
                    'title'         => '{posttype_name} Archives {title_separator} {site_name}',
                    'description'   => '{posttype_name} Archives', 
                    'noindex'       => 0,
                );
            }
        }

        // Depending on the registered Taxonomies, add some more defaults
        $taxonomies = WP_Simple_SEO_Common::get_instance()->get_taxonomies();
        foreach ( $taxonomies as $taxonomy ) {
            // Setup array
            $defaults['meta']['taxonomies'][ $taxonomy->name ] = array(
                'title'         => '{term_name} {pagination_page_total} {title_separator} {site_name}',
                'description'   => '{term_description}', 
                'noindex'       => 0,
                'meta_box'      => 1,
            );
        }

        // Allow devs to filter defaults
        $defaults = apply_filters( 'wp_simple_seo_settings_get_plugin_defaults', $defaults, $type );

        // Return result
        return ( isset( $defaults[ $type ] ) ? $defaults[ $type ] : '' );

    }

    /**
     * Attempts to extract the given key value from the settings array.
     *
     * If no setting value can be found, returns a WP_Error.
     *
     * @since 1.0.0
     *
     * @param   string  $key        Key
     * @param   array   $settings   Settings
     * @return  mixed               WP_Error | string | array
     */
    private function extract_setting( $key, $settings ) {

        // If settings are empty, return an error
        if ( ! $settings ) {
            return new WP_Error( 'setting_missing', __( 'Setting does not exist', 'wp-simple-seo' ) );
        }
        
        // Convert string to array keys
        $keys = explode( '[', $key );
        
        // If it's just a string, return the setting if it exists
        if ( ! is_array( $keys ) ) {
            if ( ! isset( $settings[ $key ] ) ) {
                return new WP_Error( 'setting_missing', __( 'Setting does not exist', 'wp-simple-seo' ) );
            }

            return $settings[ $key ];
        }
        
        // Iterate through array keys
        foreach ( $keys as $count => $sub_key ) {
            // Cleanup key
            $sub_key = trim( $sub_key, '[]' );

            // Check if key exists
            if ( ! isset( $settings[ $sub_key ] ) ) {
                // It doesn't exist, so return the option value using the original key
                return new WP_Error( 'setting_missing', __( 'Setting does not exist', 'wp-simple-seo' ) );
            }

            // Key exists - make settings the value (which could be an array or the final value)
            // of this key
            $settings = $settings[ $sub_key ];
        }

        // If here, setting exists
        return $settings; // This will be a non-array value

    }

    /**
     * Stores the given settings for the given Type into the WordPress Post Meta table
     *
     * @since 1.0.0
     *
     * @param   id      $post_id    Post ID
     * @param   string  $type       Type
     * @param   array   $settings   Settings
     * @return  bool                Success
     */
    public function update_post_settings( $post_id, $type, $settings ) {

        // Get Post Type
        $post_type = get_post_type( $post_id );

        // Get plugin settings, or defaults if no plugin settings exist.
        $plugin_settings = $this->get_plugin_settings( $type );

        // Perform array_diff on the multidimensional settings array
        // This will return just the settings that are different from the plugin / defaults
        $settings = $this->array_diff_assoc_recursive( $settings, $plugin_settings );

        // Allow devs to filter before saving
        $settings = apply_filters( 'wp_simple_seo_settings_update_post_settings', $settings, $post_id, $type );

        // Clear cache
        WP_Simple_SEO_Cache::get_instance()->delete_all();

        // If empty, delete
        if ( empty( $settings ) ) {
            return $this->delete_post_settings( $post_id, $type );
        }

        // Store
        update_post_meta( $post_id, '_wp_simple_seo_' . $type, $settings );

        // Store post settings as individual key/value pairs, so they can easily be set / accessed / removed by third party plugins
        // and WP_Query calls

        // Title
        if ( isset( $settings['post_types'][ $post_type ]['single']['title'] ) ) {
            update_post_meta( $post_id, '_wp_simple_seo_' . $type . '_title', $settings['post_types'][ $post_type ]['single']['title'] );
        } else {
            delete_post_meta( $post_id, '_wp_simple_seo_' . $type . '_title' );
        }

        // Description
        if ( isset( $settings['post_types'][ $post_type ]['single']['description'] ) ) {
            update_post_meta( $post_id, '_wp_simple_seo_' . $type . '_description', $settings['post_types'][ $post_type ]['single']['description'] );
        } else {
            delete_post_meta( $post_id, '_wp_simple_seo_' . $type . '_description' );
        }

        // noindex
        if ( isset( $settings['post_types'][ $post_type ]['single']['noindex'] ) ) {
            update_post_meta( $post_id, '_wp_simple_seo_' . $type . '_noindex', $settings['post_types'][ $post_type ]['single']['noindex'] );
        } else {
            delete_post_meta( $post_id, '_wp_simple_seo_' . $type . '_noindex' );
        }

        // nofollow
        if ( isset( $settings['post_types'][ $post_type ]['single']['nofollow'] ) ) {
            update_post_meta( $post_id, '_wp_simple_seo_' . $type . '_nofollow', $settings['post_types'][ $post_type ]['single']['nofollow'] );
        } else {
            delete_post_meta( $post_id, '_wp_simple_seo_' . $type . '_nofollow' );
        }

        // Canonical
        if ( isset( $settings['post_types'][ $post_type ]['single']['canonical'] ) ) {
            update_post_meta( $post_id, '_wp_simple_seo_' . $type . '_canonical', $settings['post_types'][ $post_type ]['single']['canonical'] );
        } else {
            delete_post_meta( $post_id, '_wp_simple_seo_' . $type . '_canonical' );
        }

        // Allow devs to store post settings as individual key/value pairs now
        do_action( 'wp_simple_seo_settings_update_post_settings_individual', $post_id, $type, $settings );

        return true;

    }

    /**
     * Stores the given settings for the given Type into the WordPress Term Meta table
     *
     * @since 1.0.0
     *
     * @param   id      $term_id    Term ID
     * @param   string  $type       Type
     * @param   array   $settings   Settings
     * @return  bool                Success
     */
    public function update_term_settings( $term_id, $type, $settings ) {

        // Get Term
        $term = get_term( $term_id );

        // Get plugin settings, or defaults if no plugin settings exist.
        $plugin_settings = $this->get_plugin_settings( $type );

        // Perform array_diff on the multidimensional settings array
        // This will return just the settings that are different from the plugin / defaults
        $settings = $this->array_diff_assoc_recursive( $settings, $plugin_settings );

        // Allow devs to filter before saving
        $settings = apply_filters( 'wp_simple_seo_settings_update_term_settings', $settings, $term_id, $type );

        // If empty, delete
        if ( empty( $settings ) ) {
            return $this->delete_term_settings( $term_id, $type );
        }

        // Store post settings as individual key/value pairs, so they can easily be set / accessed / removed by third party plugins
        // and WP_Query calls

        // Title
        if ( isset( $settings['taxonomies'][ $term->taxonomy ]['title'] ) ) {
            update_term_meta( $term_id, '_wp_simple_seo_' . $type . '_title', $settings['taxonomies'][ $term->taxonomy ]['title'] );
        } else {
            delete_term_meta( $term_id, '_wp_simple_seo_' . $type . '_title' );
        }

        // Description
        if ( isset( $settings['taxonomies'][ $term->taxonomy ]['description'] ) ) {
            update_term_meta( $term_id, '_wp_simple_seo_' . $type . '_description', $settings['taxonomies'][ $term->taxonomy ]['description'] );
        } else {
            delete_term_meta( $term_id, '_wp_simple_seo_' . $type . '_description' );
        }

        // noindex
        if ( isset( $settings['taxonomies'][ $term->taxonomy ]['noindex'] ) ) {
            update_term_meta( $term_id, '_wp_simple_seo_' . $type . '_noindex', $settings['taxonomies'][ $term->taxonomy ]['noindex'] );
        } else {
            delete_term_meta( $term_id, '_wp_simple_seo_' . $type . '_noindex' );
        }

        return true;

    }

    /**
     * Stores the given settings for the given Type into the WordPress Options table
     *
     * @since 1.0.0
     *
     * @param    string  $type       Type
     * @param    array   $settings   Settings
     * @return   bool                Success
     */
    public function update_plugin_settings( $type, $settings ) {

        // Unset some settings
        unset( $settings['submit'] );
        unset( $settings['wp-simple-seo_nonce'] );
        unset( $settings['_wp_http_referer'] );

        // Get defaults
        $defaults = $this->get_default_settings( $type );

        // Get existing settings
        $existing_settings = $this->get_plugin_settings( $type );

        // Allow devs to filter before saving
        $settings = apply_filters( 'wp_simple_seo_settings_update_plugin_settings', $settings, $type );
        
        // Merge settings with existing settings
        $settings = $this->array_merge_assoc_recursive( $existing_settings, $settings );

        // Merge settings with defaults
        $settings = $this->array_merge_assoc_recursive( $defaults, $settings );

        // update_option will return false if no changes were made, so we can't rely on this
        update_option( 'wp-simple-seo-' . $type, $settings );

        // Clear cache
        WP_Simple_SEO_Cache::get_instance()->delete_all();
        
        return true;

    }

    /**
     * Recursive array_merge function
     *
     * @since   1.0.0
     *
     * @param   array   $array1
     * @param   array   $array2
     * @return  array               Merged Data
     */
    private function array_merge_assoc_recursive( $array1, $array2 ) {

        $merged = $array1;

        foreach ( $array2 as $key => $value ) {
            if ( is_array( $value ) && isset( $merged[ $key ] ) && is_array( $merged[ $key ] ) ) {
                $merged[ $key ] = $this->array_merge_assoc_recursive( $merged[ $key ], $value );
            } else if ( is_numeric( $key ) ) {
                if ( ! in_array( $value, $merged ) ) {
                    $merged[] = $value;
                }
            } else {
                $merged[ $key ] = $value;
            }
        }

        return $merged;

    }

    /**
     * Recursive array_diff function
     *
     * @since 1.0.0
     *
     * @param   array   $array1
     * @param   array   $array2
     * @return  array               Difference
     */
    private function array_diff_assoc_recursive( $array1, $array2 ) {

        $difference = array();

        foreach( $array1 as $key => $value ) {
            if( is_array( $value ) ) {
                if( ! isset( $array2[ $key ]) || ! is_array( $array2[ $key ]) ) {
                    $difference[ $key ] = $value;
                } else {
                    $new_diff = $this->array_diff_assoc_recursive( $value, $array2[ $key ] );
                    if( ! empty( $new_diff ) ) {
                        $difference[ $key ] = $new_diff;
                    }
                }
            } else if( ! array_key_exists( $key, $array2 ) || $array2[ $key ] != $value ) {
                $difference[ $key ] = $value;
            }
        }

        return $difference;

    }

    /**
     * Escapes the given string value, depending on whether we're in the WordPress Administration
     * interface or not.
     *
     * @since   1.0.0
     *
     * @param   string  $string     String to escape
     * @return  string              Escaped string
     */
    private function escape_string( $string ) {

        if ( is_admin() ) {
            // Escape the data, so it outputs in an input field correctly
            $string = esc_attr( $string );
        }

        // Stripslashes
        $string = stripslashes( $string );

        // Return
        return $string;
        
    }

    /**
     * Deletes settings for the given Type in the WordPress Post Meta table
     *
     * @since 1.0.0
     *
     * @param   int     $post_id    Post ID
     * @param   string  $type       Type
     */
    public function delete_post_settings( $post_id, $type ) {

        // Delete meta
        delete_post_meta( $post_id, '_wp_simple_seo_' . $type . '_title' );
        delete_post_meta( $post_id, '_wp_simple_seo_' . $type . '_description' );
        delete_post_meta( $post_id, '_wp_simple_seo_' . $type . '_noindex' );
        delete_post_meta( $post_id, '_wp_simple_seo_' . $type . '_nofollow' );
        delete_post_meta( $post_id, '_wp_simple_seo_' . $type . '_canonical' );
        
        // Allow devs to run an action now
        do_action( 'wp_simple_seo_settings_delete_post_settings', $post_id, $type );

        return true;

    }

    /**
     * Deletes settings for the given Type in the WordPress Term Meta table
     *
     * @since 1.0.0
     *
     * @param   int     $term_id    Term ID
     * @param   string  $type       Type
     */
    public function delete_term_settings( $term_id, $type ) {

        // Delete meta
        delete_term_meta( $term_id, '_wp_simple_seo_' . $type . '_title' );
        delete_term_meta( $term_id, '_wp_simple_seo_' . $type . '_description' );
        delete_term_meta( $term_id, '_wp_simple_seo_' . $type . '_noindex' );

        // Allow devs to run an action now
        do_action( 'wp_simple_seo_settings_delete_term_settings', $term_id, $type );

    }

    /**
     * Deletes settings for the given Type in the WordPress Options table
     *
     * @since 1.0.0
     *
     * @param   string  $type   Type
     */
    public function delete_plugin_settings( $type ) {

        // Delete option
        delete_option( 'wp-simple-seo-' . $type );

        // Allow devs to run an action now
        do_action( 'wp_simple_seo_settings_delete_plugin_settings', $type );

    }

    /**
     * Determines whether the given Post Type has an archive.
     *
     * For Posts, we determine this in a slightly different way, by checking for:
     * - Settings > Reading > Front page displays = A static page
     *
     * @since 1.0.0
     *
     * @param   mixed   $post_type  Post Type (string | Post Type object )
     * @return  bool                Post Type Has Archives
     */
    public function post_type_has_archive( $post_type ) {

        // Get post type object now if we weren't given one.
        if ( ! is_object( $post_type ) ) {
            $post_type = get_post_type_object( $post_type );
        }

        // Special check for Posts
        $page_for_posts = get_option( 'page_for_posts' );
        $show_on_front = get_option( 'show_on_front' );
        if ( $post_type->name == 'post' && $show_on_front == 'page' && ! empty( $page_for_posts ) ) {
            return true;
        }

        // Fallback to the post type's has_archive property
        return (bool) $post_type->has_archive;

    }

    /**
     * Determines whether the site has two or more Authors (Users with published posts).
     *
     * @since 1.0.0
     *
     * @return bool
     */
    public function site_has_multiple_authors() {

        $query = new WP_User_Query( array(
            'has_published_posts' => true,
        ) );
        $count = $query->get_total();

        // If less than two authors, return false
        if ( $count < 2 ) {
            return false;
        }

        return true;

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