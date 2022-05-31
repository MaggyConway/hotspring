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
class Section_Shop_by_Model extends Widget_Base {

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
		return 'section-shop-by-model';
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
		return __( 'Section: Shop by Model', 'hotspring-lang' );
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
	 * Retrieve the list of styles the widget depended on.
	 *
	 * Used to set styles dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget styles dependencies.
	 */
    public function get_style_depends() {
        return [ 'components-kit-elementor-style' ];
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
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'hotspring-lang' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'hotspring-lang' ),
				'type' => Controls_Manager::TEXT,
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

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Style', 'hotspring-lang' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_transform',
			[
				'label' => __( 'Text Transform', 'hotspring-lang' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'None', 'hotspring-lang' ),
					'uppercase' => __( 'UPPERCASE', 'hotspring-lang' ),
					'lowercase' => __( 'lowercase', 'hotspring-lang' ),
					'capitalize' => __( 'Capitalize', 'hotspring-lang' ),
				],
				'selectors' => [
					'{{WRAPPER}} .title' => 'text-transform: {{VALUE}};',
				],
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
        $clases = 'swiper-slide ';
		$settings = $this->get_settings_for_display();
		if(!empty($settings['title'])){
			echo '<div class="title"><h2>'.$settings['title'].'</h2></div>';
		}

        $posts = get_posts(
            array(
                'numberposts' => -1,
                'post_type'   => 'model',
                'orderby'     => 'menu_order',
                'order'       => 'ASC',
            )
        );

        $filters = [
			'seats-6-8' => '6-8 Seats',
            'seats-4-5' => '4-5 Seats',
			'seats-2-3' => '2-3 Seats',
            'lounge' => 'Lounge',
            'salt-water' => 'Salt Water',
        ];

        $open_tag_wrap = '';
        $close_tag_wrap = '';
        if( $settings['use_wrap'] == 'yes' ) {
            $open_tag_wrap = '<div class="row"><div class="col-lg-10 offset-lg-1">';
            $close_tag_wrap = '</div></div>';
        }

        $output = '';
        //@todo to change classes
        $output = '<div class="container">' . $open_tag_wrap . '

    <div class="swiper-container swiper-filter d-none d-md-block">
      <div class="tabs-parent">
        <ul class="filter-wrapper">';
        $output .= '<li class="filter-slide current"><a data-filter="">All Models</a></li>';
        foreach ($filters as $key => $value) {
            $output .= '<li class="filter-slide"><a data-filter="' . $key . '">'.$value.'</a></li>';
        }
        $output .= '
        </ul>
      </div>
    </div>
    <div class="swiper-container-mobile text-center d-block d-md-none">
      <form name="swiperFilter" id="swiperFilter">';
        $output .= '<div class="form-group active"><select class="form-select" name="filter">';
        $output .= '<option value="">Filter: All Models </option>';
        foreach ($filters as $key => $value) {
            $output .= '<option value="' . $key . '">' . $value . '</option>';
        }
        $output .= '</select></div>';
        $output .= '
      </form>
    </div>
    <h4 class="results">Results (<span class="count"></span><span class="number">' . count($posts) . '</span>)</h4>
    <div class="swiper-object">
        <div class="swiper-container swiper-product-shop">
            <div class="swiper-wrapper">';
                foreach ( $posts as $key => $post ) {
                    $output .= getBaseModelCard($post->ID, $clases);
                }
            //@todo remove this test slides
            $output .= '
            </div>
        </div>
        <!-- If we need navigation buttons -->
        <div class="swiper-buttons sbm-btns">
            ' . getSwiperPrev() . '
            ' . getSwiperNext() . '
        </div>

        ' . $close_tag_wrap . '
    </div>';

        echo $output;
	}
}
