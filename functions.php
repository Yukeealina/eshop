<?php
/**
 * Eshop functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Eshop
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

if ( ! function_exists( 'eshop_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function eshop_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Eshop, use a find and replace
		 * to change 'eshop' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'eshop', get_template_directory() . '/languages' );

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
		register_nav_menus(
			array(
				'menu-1' => esc_html__( 'Primary', 'eshop' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		// Set up the WordPress core custom background feature.
		add_theme_support(
			'custom-background',
			apply_filters(
				'eshop_custom_background_args',
				array(
					'default-color' => 'ffffff',
					'default-image' => '',
				)
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				'width'       => 250,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);
	}
endif;
add_action( 'after_setup_theme', 'eshop_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function eshop_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'eshop_content_width', 640 );
}
add_action( 'after_setup_theme', 'eshop_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function eshop_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'eshop' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'eshop' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'eshop_widgets_init' );

class macho_bootstrap_walker extends Walker_Nav_Menu
{

/**
 * @see Walker::start_lvl()
 * @since 3.0.0
 *
 * @param string $output Passed by reference. Used to append additional content.
 * @param int $depth Depth of page. Used for padding.
 */
public function start_lvl(&$output, $depth = 0, $args = array())
{
    $indent = str_repeat("\t", $depth);
    $output .= "\n$indent<ul role=\"menu\" class=\"dropdown-menu\">\n";
}

/**
 * @see Walker::start_el()
 * @since 3.0.0
 *
 * @param string $output Passed by reference. Used to append additional content.
 * @param object $item Menu item data object.
 * @param int $depth Depth of menu item. Used for padding.
 * @param int $current_page Menu item ID.
 * @param object $args
 */
public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
{
    $indent = ($depth) ? str_repeat("\t", $depth) : '';

    /**
     * Dividers, Headers or Disabled
     * =============================
     * Determine whether the item is a Divider, Header, Disabled or regular
     * menu item. To prevent errors we use the strcasecmp() function to so a
     * comparison that is not case sensitive. The strcasecmp() function returns
     * a 0 if the strings are equal.
     */
    if (strcasecmp($item->attr_title, 'divider') == 0 && $depth === 1) {
        $output .= $indent . '<li role="presentation" class="divider">';
    } else if (strcasecmp($item->title, 'divider') == 0 && $depth === 1) {
        $output .= $indent . '<li role="presentation" class="divider">';
    } else if (strcasecmp($item->attr_title, 'dropdown-header') == 0 && $depth === 1) {
        $output .= $indent . '<li role="presentation" class="dropdown-header">' . esc_attr($item->title);
    } else if (strcasecmp($item->attr_title, 'disabled') == 0) {
        $output .= $indent . '<li role="presentation" class="disabled"><a href="#">' . esc_attr($item->title) . '</a>';
    } else {

        $class_names = $value = '';

        $classes = empty($item->classes) ? array() : (array)$item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));

        if ($args->has_children)
            $class_names .= ' submenu dropdown';

        if (in_array('current-menu-item', $classes))
            $class_names .= '';

        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';

        $output .= $indent . '<li' . $id . $value . $class_names . '>';

        $atts = array();
        $atts['title'] = !empty($item->title) ? $item->title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '';
        $atts['rel'] = !empty($item->xfn) ? $item->xfn : '';


        $atts['custom'] = !empty($item->custom) ? $item->custom : '';

        // If item has_children add atts to a.
        if ($args->has_children && $depth === 0) {
            $atts['href'] = $item->title;
            $atts['data-toggle'] = 'dropdown';
            $atts['class'] = 'dropdown-toggle';
            $atts['aria-haspopup'] = 'true';
        } else {
            $atts['href'] = !empty($item->url) ? $item->url : '';
        }

        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args);


        $attributes = '';
        foreach ($atts as $attr => $value) {


            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $item_output = $args->before;

        /*
         * Glyphicons
         * ===========
         * Since the the menu item is NOT a Divider or Header we check the see
         * if there is a value in the attr_title property. If the attr_title
         * property is NOT null we apply it as the class name for the glyphicon.
         */
        if (!empty($item->attr_title))
            $item_output .= '<a' . $attributes . '><span class="glyphicon ' . esc_attr($item->attr_title) . '"></span>&nbsp;';
        else
            $item_output .= '<a' . $attributes . '>';


        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= ($args->has_children && 0 === $depth) ? ' </a>' : '</a>';
        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}

/**
 * Traverse elements to create list from elements.
 *
 * Display one element if the element doesn't have any children otherwise,
 * display the element and its children. Will only traverse up to the max
 * depth and no ignore elements under that depth.
 *
 * This method shouldn't be called directly, use the walk() method instead.
 *
 * @see Walker::start_el()
 * @since 2.5.0
 *
 * @param object $element Data object
 * @param array $children_elements List of elements to continue traversing.
 * @param int $max_depth Max depth to traverse.
 * @param int $depth Depth of current element.
 * @param array $args
 * @param string $output Passed by reference. Used to append additional content.
 * @return null Null on failure with no changes to parameters.
 */
public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output)
{
    if (!$element)
        return;

    $id_field = $this->db_fields['id'];

    // Display this element.
    if (is_object($args[0]))
        $args[0]->has_children = !empty($children_elements[$element->$id_field]);

    parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
}

/**
 * Menu Fallback
 * =============
 * If this function is assigned to the wp_nav_menu's fallback_cb variable
 * and a manu has not been assigned to the theme location in the WordPress
 * menu manager the function with display nothing to a non-logged in user,
 * and will add a link to the WordPress menu manager if logged in as an admin.
 *
 * @param array $args passed from the wp_nav_menu function.
 *
 */
public static function fallback($args)
{
    if (current_user_can('manage_options')) {

        extract($args);

        $fb_output = null;

        if ($container) {
            $fb_output = '<' . $container;

            if ($container_id)
                $fb_output .= ' id="' . $container_id . '"';

            if ($container_class)
                $fb_output .= ' class="' . $container_class . '"';

            $fb_output .= '>';
        }

        $fb_output .= '<ul';

        if ($menu_id)
            $fb_output .= ' id="' . $menu_id . '"';

        if ($menu_class)
            $fb_output .= ' class="' . $menu_class . '"';

        $fb_output .= '>';
        $fb_output .= '<li><a href="' . admin_url('nav-menus.php') . '">Add a menu</a></li>';
        $fb_output .= '</ul>';

        if ($container)
            $fb_output .= '</' . $container . '>';

        echo $fb_output;
    }
}
}

if( function_exists('acf_add_options_page') ) {
	
	acf_add_options_page(array(
		'page_title' 	=> 'Eshop General Settings',
		'menu_title'	=> 'Eshop Settings',
		'menu_slug' 	=> 'eshop-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
	
	acf_add_options_sub_page(array(
		'page_title' 	=> 'Eshop Header Settings',
		'menu_title'	=> 'Header',
		'parent_slug'	=> 'eshop-general-settings',
	));
	
	acf_add_options_sub_page(array(
		'page_title' 	=> 'Eshop Footer Settings',
		'menu_title'	=> 'Footer',
		'parent_slug'	=> 'eshop-general-settings',
	));
	
}

/**
 * Enqueue scripts and styles.
 */
function eshop_scripts() {
	wp_enqueue_style( 'eshop-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'eshop-style', 'rtl', 'replace' );

	wp_enqueue_style( 'eshop-style0', get_template_directory_uri() . '/css/animate.css');
	wp_enqueue_style( 'eshop-style1', get_template_directory_uri() . '/css/bootstrap.css');
	wp_enqueue_style( 'eshop-style2', get_template_directory_uri() . '/css/flex-slider.min.css');
	wp_enqueue_style( 'eshop-style3', get_template_directory_uri() . '/css/font-awesome.css');
	wp_enqueue_style( 'eshop-style4', get_template_directory_uri() . '/css/jquery.fancybox.min.css');
	wp_enqueue_style( 'eshop-style5', get_template_directory_uri() . '/css/jquery-ui.css');
	wp_enqueue_style( 'eshop-style6', get_template_directory_uri() . '/css/magnific-popup.css');
	wp_enqueue_style( 'eshop-style7', get_template_directory_uri() . '/css/magnific-popup.min.css');
	wp_enqueue_style( 'eshop-style8', get_template_directory_uri() . '/css/niceselect.css');
	wp_enqueue_style( 'eshop-style9', get_template_directory_uri() . '/css/nice-select.css');
	wp_enqueue_style( 'eshop-style10', get_template_directory_uri() . '/css/owl-carousel.css');
	wp_enqueue_style( 'eshop-style11', get_template_directory_uri() . '/css/reset.css');
	wp_enqueue_style( 'eshop-style12', get_template_directory_uri() . '/css/responsive.css');
	wp_enqueue_style( 'eshop-style13', get_template_directory_uri() . '/css/slicknav.min.css');
	wp_enqueue_style( 'eshop-style14', get_template_directory_uri() . '/css/themify-icons.css');
	wp_enqueue_style( 'eshop-style15', get_template_directory_uri() . '/css/style.css');

	wp_deregister_script('jquery');
	wp_register_script('jquery', get_template_directory_uri() . '/js/jquery.min.js');
	wp_enqueue_script('jquery');

	wp_enqueue_script( 'eshop-navigation1', get_template_directory_uri() . '/js/active.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'eshop-navigation2', get_template_directory_uri() . '/js/bootstrap.min.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'eshop-navigation3', get_template_directory_uri() . '/js/easing.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'eshop-navigation4', get_template_directory_uri() . '/js/facnybox.min.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'eshop-navigation5', get_template_directory_uri() . '/js/finalcountdown.min.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'eshop-navigation6', get_template_directory_uri() . '/js/flex-slider.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'eshop-navigation7', get_template_directory_uri() . '/js/gmap.min.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'eshop-navigation8', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'eshop-navigation9', get_template_directory_uri() . '/js/jquery-migrate-3.0.0.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'eshop-navigation10', get_template_directory_uri() . '/js/jquery-ui.min.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'eshop-navigation11', get_template_directory_uri() . '/js/magnific-popup.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'eshop-navigation12', get_template_directory_uri() . '/js/map-script.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'eshop-navigation13', get_template_directory_uri() . '/js/nicesellect.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'eshop-navigation14', get_template_directory_uri() . '/js/onepage-nav.min.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'eshop-navigation15', get_template_directory_uri() . '/js/owl-carousel.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'eshop-navigation16', get_template_directory_uri() . '/js/popper.min.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'eshop-navigation17', get_template_directory_uri() . '/js/scrollup.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'eshop-navigation18', get_template_directory_uri() . '/js/slicknav.min.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'eshop-navigation19', get_template_directory_uri() . '/js/waypoints.min.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'eshop-navigation20', get_template_directory_uri() . '/js/ytplayer.min.js', array(), _S_VERSION, true );
	wp_enqueue_script( 'eshop-navigation21', get_template_directory_uri() . '/js/customizer.js', array(), _S_VERSION, true );


	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'eshop_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

