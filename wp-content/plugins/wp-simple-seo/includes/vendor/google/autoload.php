<?php
/**
* Autoloader, modified for Envira
*
* @since 1.0.0
*/
if ( ! function_exists( 'WP_Simple_SEO_Google_Autoload' ) ) {
    function WP_Simple_SEO_Google_Autoload( $className ) {

        $classPath = explode( '_', $className );
        if ( $classPath[0] != 'Google' ) {
          return;
        }
        
        // Drop 'Google', and maximum class file path depth in this project is 3.
        $classPath = array_slice( $classPath, 1, 2 );

        $filePath = dirname( __FILE__ ) . '/' . implode( '/', $classPath ) . '.php';
        if ( file_exists( $filePath ) ) {
          require_once( $filePath );
        }

    }

    // Register the autoloader
    spl_autoload_register( 'WP_Simple_SEO_Google_Autoload' );
}