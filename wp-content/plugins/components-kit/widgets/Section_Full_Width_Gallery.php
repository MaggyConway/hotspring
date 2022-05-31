<?php
namespace ComponentsKit\Widgets;

use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Hero Product
 *
 * Elementor widget for Hero Product.
 *
 * @since 1.0.0
 */
class Section_Full_Width_Gallery extends Widget_Base {

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
		return 'section-full-width-gallery';
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
		return __( 'Section Full Width Gallery', 'hotspring-lang' );
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
		return [ 'components-kit-elementor' ];
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

        $repeater = new Repeater();
        $repeater->add_control(
            'heading',
            [
                'label' => esc_html__( 'Title', 'hotspring-lang' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Heading', 'hotspring-lang' ),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'image',
            [
                'label' => __( 'Image', 'hotspring-lang' ),
                'type' => Controls_Manager::MEDIA,
            ]
        );

        $repeater->add_control(
            'description',
            [
                'label' => esc_html__( 'Description', 'hotspring-lang' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'elementor-pro' ),
                'show_label' => false,
            ]
        );

        $this->add_control(
            'slides',
            [
                'label' => __( 'Slides', 'hotspring-lang' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'heading' => __( 'Slide 1', 'hotspring-lang' ),
                        'description' => 'Cookie muffin gummies oat cake candy canes chupa chups. Croissant cookie carrot cake biscuit icing sweet roll sesame snaps pie bear claw. Sugar plum icing jelly carrot cake biscuit.',
                    ],
                    [
                        'heading' => __( 'Slide 2', 'hotspring-lang' ),
                        'description' => 'Brownie cake chocolate gingerbread oat cake brownie cheesecake. Fruitcake pie jelly beans shortbread jujubes. Ice cream jelly beans tart marzipan shortbread.',
                    ],
                ],
                'title_field' => '{{{ heading }}}',
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
		$settings = $this->get_settings_for_display();

		echo '<div class="title">';
		    echo '<h2>'.$settings['title'].'</h2>';
		echo '</div>';

		echo '<div class="swiper-container-thumb">';

            echo '<div class="swiper mySwiper2">';
                echo '<div class="swiper-wrapper">';

                    foreach ( $settings['slides'] as $slide ) {
                        print '
                            <div class="swiper-slide">
                                <img src="'.$slide['image']['url'].'">
                                <div class="swiper-slide-desc">' . $slide['description'] . '</div> 
                            </div>';
                    }

                echo '</div>';
            echo '</div>';

            echo '<div thumbsSlider="" class="swiper mySwiper">';
                echo '<div class="swiper-wrapper">';

                    foreach ( $settings['slides'] as $slide ) {
                        print '
                            <div class="swiper-slide">
                                <img src="'.$slide['image']['url'].'">
                            </div>';
                    }

                echo '</div>';
                echo '<div class="swiper-button-next"></div>';
                echo '<div class="swiper-button-prev"></div>';
            echo '</div>';

        echo '</div>';

	}

}
