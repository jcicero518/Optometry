<?php
/**
 * SUNY Optometry functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package SUNY_Optometry
 */

if ( ! function_exists( 'optometry_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function optometry_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on SUNY Optometry, use a find and replace
	 * to change 'optometry' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'optometry', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.

	register_nav_menus( array(
		'menu-1' => esc_html__( 'Primary', 'optometry' ),
		'secondary' => esc_html__( 'Secondary', 'optometry' )
	));

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'optometry_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	add_image_size( 'preloader', 25, 25, false );
	/*add_image_size('cropit', 380, 390, true );*/
	/*add_filter( 'image_size_names_choose', function( $sizes ) {
		return array_merge( $sizes, [
			'cropit' => __('Crop 300x300')
		]);
	});*/


}
endif;
add_action( 'after_setup_theme', 'optometry_setup' );


/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function optometry_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'optometry_content_width', 640 );
}
add_action( 'after_setup_theme', 'optometry_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function optometry_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'optometry' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'optometry' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Community Sidebar', 'optometry' ),
		'id'            => 'community-sidebar',
		'description'   => esc_html__( 'Community sidebar content', 'optometry' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer Address', 'optometry' ),
		'id'            => 'footer-address',
		'description'   => esc_html__( 'Address information', 'optometry' ),
		'before_widget' => '<div id="%1$s" class="widget address_widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h6 class="widget-title">',
		'after_title'   => '</h6>',
	) );
}
add_action( 'widgets_init', 'optometry_widgets_init' );

require __DIR__ . '/vendor/autoload.php';

\Amorphous\Optometry\Hooks\ThemeCleanup::getInstance();
\Amorphous\Optometry\Hooks\PostFilters::getInstance();
\Amorphous\Optometry\API\RestController::getInstance();

add_filter( 'rest_cache_headers', [\Amorphous\Optometry\API\RestCache::class, 'setHeaders'] );


//add_action('save_post', 'change_default_slug');

/*function change_default_slug($post_id) {

	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		return;

	if ( !current_user_can('edit_post', $post_id) )
		return;

	remove_action('save_post', 'change_default_slug');

	wp_update_post(array('ID' => $post_id, 'post_name' => get_post_meta($post_id,'addpermalink',true)));

	add_action('save_post', 'change_default_slug');
}*/



/**
 * Enqueue scripts and styles.
 */
function optometry_scripts() {

	$buildPath = function( $env ) {
		switch ( $env ) {
			case 'production':
				return '/build/dist/';
			break;
			case 'development':
				return '/dist/';
			default:
				return '/dist/';
		}
	};

	$env = 'production';

	wp_enqueue_style( 'optometry-style', get_stylesheet_uri() );

	wp_enqueue_style( 'font-awesome', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(), '', 'all' );

	wp_enqueue_style( 'material-icons', '//fonts.googleapis.com/icon?family=Material+Icons', array(), '', 'all' );

	wp_enqueue_style( 'optometry-css', get_template_directory_uri() . $buildPath( $env ) . 'app.min.css', array( 'font-awesome' ), '', 'all' );

	wp_enqueue_script( 'optometry-navigation', get_template_directory_uri() . '/js/contrib/navigation.js', array(), '20151215', true );

	wp_enqueue_script( 'optometry-skip-link-focus-fix', get_template_directory_uri() . '/js/contrib/skip-link-focus-fix.js', array(), '20151215', true );

    /*wp_enqueue_script( 'optometry-scripts-manifest', get_template_directory_uri() . $buildPath( $env ) . 'manifest.bundle.js', array(), '', true );
    wp_enqueue_script( 'optometry-scripts-loadable-chunk-0', get_template_directory_uri() . $buildPath( $env ) . '0.chunk.js', array(), '', true );
    wp_enqueue_script( 'optometry-scripts-loadable-chunk-1', get_template_directory_uri() . $buildPath( $env ) . '1.chunk.js', array(), '', true );
    wp_enqueue_script( 'optometry-scripts-loadable-chunk-2', get_template_directory_uri() . $buildPath( $env ) . '2.chunk.js', array(), '', true );*/

	wp_enqueue_script( 'optometry-scripts-vendor', get_template_directory_uri() . $buildPath( $env ) . 'vendor.bundle.js', array(), '', true );
	wp_enqueue_script( 'optometry-scripts', get_template_directory_uri() . $buildPath( $env ) . 'app.bundle.js', array(), '', true );
	wp_localize_script( 'optometry-scripts', 'OPTO_REST_API', [
		'theme_dir' => get_template_directory_uri(),
		'wp_rest_url' => rest_url(),
		'search_form' => get_search_form( false ),
		'front_page_id' => get_option( 'page_on_front', 0 )
	]);

	global $post;
	$dynamicSidebar = function() {
		ob_start();
		dynamic_sidebar( 'footer-address' );
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	};

	$fm = new FooterMenus();
	$footer_one_one = $fm->start()->parentPage(9)->generateMenu();
	$footer_one_two = $fm->start()->parentPage(11)->generateMenu();

	wp_localize_script( 'optometry-scripts', 'OPTO_FOOTER_MENU', [
		'menu_col_one' => $footer_one_one,
		'menu_col_two' => $footer_one_two,
		'menu_col2_one' => $fm->start()->parentPage(13)->generateMenu(),
		'menu_col2_two' => $fm->start()->parentPage(15)->generateMenu(),
		'menu_col2_three' => $fm->start()->parentPage(17)->generateMenu(),
		'address_info' => $dynamicSidebar(),
		'site_info' => get_theme_mod( 'footer_info_copy', '' )
	]);

	$localizedSettings = [
		'ID' => $post->ID,
		'queriedObject' => get_queried_object(),
		'endpoint' => get_template_directory_uri()
	];

	if ( is_page() || is_archive() || is_singular() ) {
		$localizedSettings['type'] = get_post_type();
		$localizedSettings['templateSlug'] = get_page_template_slug();
		$localizedSettings['template'] = '';
	} else if ( is_category() || is_tag() || is_tax() ) {
		$localizedSettings['taxonomy'] = get_taxonomy( get_queried_object()->taxonomy );
	} else if ( is_search() ) {
		$localizedSettings['searchQuery'] = get_search_query();
	} else if ( is_404() ) {
		$localizedSettings['type'] = 404;
	}

	/*wp_localize_script( 'optometry-scripts', 'first_load_data', [
		'data' => $initialRender->get_post_data()
	]);*/

	wp_localize_script( 'optometry-scripts', 'SiteSettings', $localizedSettings );

	wp_enqueue_script( 'optometry-gfonts', get_template_directory_uri() . '/js/custom/googleFonts.js', array(), '', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'optometry_scripts' );

/**
 * Implement a WP Menu Walker
 */
require get_template_directory() . '/inc/menu-walker.php';

require get_template_directory() . '/inc/footer-menus.php';

require get_template_directory() . '/inc/side-menus.php';

require get_template_directory() . '/inc/shortcodes.php';
/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
//require get_template_directory() . '/inc/jetpack.php';

