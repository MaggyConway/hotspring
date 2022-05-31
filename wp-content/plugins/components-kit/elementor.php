<?php
namespace ComponentsKit;

// use ComponentsKit\PageSettings\Page_Settings;

/**
 * Class Plugin
 *
 * Main Plugin class
 * @since 0.0.1
 */
class ComponentsKitElementor {

	/**
	 * Instance
	 *
	 * @since 0.0.1
	 * @access private
	 * @static
	 *
	 * @var Plugin The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * widget_scripts
	 *
	 * Load required plugin core files.
	 *
	 * @since 0.0.1
	 * @access public
	 */
	public function widget_scripts() {
		wp_register_script( 'components-kit-elementor', plugins_url( '/assets/js/components-kit-elementor.js', __FILE__ ), [ 'jquery', 'elementor-frontend', ], false, true );
		wp_register_script( 'jquery-swatch', plugins_url( '/assets/js/jquery.swatch.js', __FILE__ ), [ 'jquery', 'elementor-frontend', ], false, true );
	}

    /**
     * widget_styles
     *
     * Load required plugin core files.
     *
     * @since 0.0.1
     * @access public
     */
    public function widget_styles() {
        wp_register_style( 'components-kit-elementor-style', plugins_url( '/assets/css/components-kit-elementor-style.css', __FILE__ ) );
    }

	/**
	 * Register Widgets
	 *
	 * Register new Elementor widgets.
	 *
	 * @since 0.0.1
	 * @access public
	 *
	 * @param Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register_widgets( $widgets_manager ) {
		// Its is now safe to include Widgets files
		require_once( __DIR__ . '/widgets/Hero_Default.php' );
		require_once( __DIR__ . '/widgets/Hero_Default_Gallery.php' );
		require_once( __DIR__ . '/widgets/Hero_Default_with_Reviews.php' );
		require_once( __DIR__ . '/widgets/Hero_Compare_Collections.php' );
		require_once( __DIR__ . '/widgets/Hero_Blog_Home.php' );
		require_once( __DIR__ . '/widgets/Hero_Blog_Article.php' );
		require_once( __DIR__ . '/widgets/Hero_Search_Results.php' );
		require_once( __DIR__ . '/widgets/Hero_Model.php' );
		require_once( __DIR__ . '/widgets/Three_Cards.php' );
		require_once( __DIR__ . '/widgets/Three_Cards_Carousel.php' );
		require_once( __DIR__ . '/widgets/Cards.php' );
		require_once( __DIR__ . '/widgets/Cards_Carousel.php' );
		require_once( __DIR__ . '/widgets/Section_Video.php' );
		require_once( __DIR__ . '/widgets/Section_Content.php' );
		require_once( __DIR__ . '/widgets/Section_Copy.php' );
		require_once( __DIR__ . '/widgets/Section_Color_Visualizer.php' );
		require_once( __DIR__ . '/widgets/Section_Shop_by_Model.php' );
		require_once( __DIR__ . '/widgets/Section_Carousel_Slide.php' );
        require_once( __DIR__ . '/widgets/Section_Full_Width_Gallery.php' );
		require_once( __DIR__ . '/widgets/Sections_Reviews.php' );
		require_once( __DIR__ . '/widgets/Section_Model_Cards.php' );
		require_once( __DIR__ . '/widgets/Section_Media_Carousel.php' );
		require_once( __DIR__ . '/widgets/Section_Hotspot_Model.php' );
    	require_once( __DIR__ . '/widgets/Section_Cta_Text.php' );
		require_once( __DIR__ . '/widgets/Section_Cta_Head.php' );
		require_once( __DIR__ . '/widgets/Section_CTA_Button.php' );


		// Register Widgets
		$widgets_manager->register( new Widgets\Hero_Default() );
		$widgets_manager->register( new Widgets\Hero_Default_Gallery() );
		$widgets_manager->register( new Widgets\Hero_Default_with_Reviews() );
		$widgets_manager->register( new Widgets\Hero_Compare_Collections() );
		$widgets_manager->register( new Widgets\Hero_Blog_Home() );
		$widgets_manager->register( new Widgets\Hero_Blog_Article() );
		$widgets_manager->register( new Widgets\Hero_Search_Results() );
		$widgets_manager->register( new Widgets\Hero_Model() );
		$widgets_manager->register( new Widgets\Three_Cards() );
		$widgets_manager->register( new Widgets\Three_Cards_Carousel() );
		$widgets_manager->register( new Widgets\Cards() );
		$widgets_manager->register( new Widgets\Cards_Carousel() );
		$widgets_manager->register( new Widgets\Section_Video() );
		$widgets_manager->register( new Widgets\Section_Content() );
		$widgets_manager->register( new Widgets\Section_Copy() );
		$widgets_manager->register( new Widgets\Section_Color_Visualizer() );
		$widgets_manager->register( new Widgets\Section_Shop_by_Model() );
		$widgets_manager->register( new Widgets\Section_Carousel_Slide() );
        $widgets_manager->register( new Widgets\Section_Full_Width_Gallery() );
		$widgets_manager->register( new Widgets\Sections_Reviews() );
		$widgets_manager->register( new Widgets\Section_Model_Cards() );
		$widgets_manager->register( new Widgets\Section_Media_Carousel() );
		$widgets_manager->register( new Widgets\Section_Hotspot_Model() );
		$widgets_manager->register( new Widgets\Section_Cta_Text() );
		$widgets_manager->register( new Widgets\Section_Cta_Head() );
		$widgets_manager->register( new Widgets\Section_CTA_Button() );

		//$widgets_manager->register( new Widgets\Inline_Editing() );
	}

	/**
	 *  Plugin class constructor
	 *
	 * Register plugin action hooks and filters
	 *
	 * @since 0.0.1
	 * @access public
	 */
	public function __construct() {

		// Register widget scripts
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'widget_scripts' ] );

		// Register widget styles
        add_action( 'elementor/frontend/after_register_styles', [ $this, 'widget_styles' ] );

		// Register widgets
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );

	}
}

// Instantiate Plugin Class
ComponentsKitElementor::instance();
