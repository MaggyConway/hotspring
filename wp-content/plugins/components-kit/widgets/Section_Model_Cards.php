<?php
namespace ComponentsKit\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Section_Shop_by_Model
 *
 * Elementor widget for Section_Shop_by_Model.
 *
 * @since 1.0.0
 */
class Section_Model_Cards extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'section-model-cards';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Section model cards', 'hotspring-lang' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-posts-ticker';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'general' ];
	}

	/**
	 * Retrieve the list of scripts the widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [ 'elementor-frontend', 'components-kit-elementor' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$args = array(
			'post_type' => 'collections',
			'posts_per_page' => -1,
			'orderby' => 'id',
			'order' => 'DESC'
		);

		$posts = get_posts( $args );
		$models['0000'] = 'Default';

		foreach ( $posts as $post ) {
			$models[$post->ID] = get_the_title( $post->ID );
		}

		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'hotspring-lang' ),
			]
		);

		$this->add_control(
			'model_collection',
			[
				'label' => __( 'Collection', 'hotspring-lang' ),
				'type' => Controls_Manager::SELECT,
                'default' => '0000',
				'options' => $models
			]
		);

		$this->add_control(
			'swiper_mobile',
			[
				'label' => esc_html__( 'Swiper on mobile', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'no',
				'options' => [
					'yes' => esc_html__( 'Yes', 'elementor-pro' ),
					'no' => esc_html__( 'No', 'elementor-pro' ),
				],
				'frontend_available' => true,
			]
		);

        $this->add_control(
            'use_wrap',
            [
                'label' => esc_html__( 'Use wrapper', 'elementor-pro' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'no',
                'options' => [
                    'yes' => esc_html__( 'Yes', 'elementor-pro' ),
                    'no' => esc_html__( 'No', 'elementor-pro' ),
                ]
            ]
        );

		$this->end_controls_section();

	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$clases = 'model-card-slide col-12 col-md-6 col-lg-4';
		$settings = $this->get_settings_for_display();
		$setting_option = $settings['model_collection'];
		$open_tag_mobile = '';
		$close_tag_mobile = '';

		if ($setting_option === '0000') {
			$setting_option = '';
		}
        if( $settings['swiper_mobile'] == 'yes' ) {
            $open_tag_mobile = '<div class="swiper_mobile_models">';
            $close_tag_mobile = '</div>';
        }

        $posts  = get_posts(
            array(
                'numberposts' => -1,
                'post_type'   => 'model',
                'orderby'     => 'menu_order',
                'order'       => 'ASC',
                'meta_key'    => 'model_collection',
                'meta_value'  => $setting_option
            )
        );

        $open_tag_wrap = '';
        $close_tag_wrap = '';
        if( $settings['use_wrap'] == 'yes' ) {
            $open_tag_wrap = '<div class="col-lg-10 offset-lg-1"><div class="row">';
            $close_tag_wrap = '</div></div>';
        }

        $output = '';
        $output .= $open_tag_mobile . '
            <div class="container swiper-wrap">
                <div class="row">
                '.$open_tag_wrap;
					foreach ( $posts as $key => $post ) {
						$output .= getBaseModelCard($post->ID, $clases);
					}
                $output .= $close_tag_wrap . '
                </div>
            </div>' . $close_tag_mobile;

        echo $output;
	}

}
