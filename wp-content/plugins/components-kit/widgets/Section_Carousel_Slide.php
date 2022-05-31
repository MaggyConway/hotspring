<?php
namespace ComponentsKit\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Section_Carousel_Slide
 *
 * Elementor widget for Section_Carousel_Slide.
 *
 * @since 1.0.0
 */
class Section_Carousel_Slide extends Widget_Base {

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
		return 'section-carousel-slide';
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
		return __( 'Section: Carousel Slide', 'hotspring-lang' );
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
		return [ 'elementor-frontend', 'components-kit-elementor'];
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
		// $this->add_control(
		// 	'col_number',
		// 	[
		// 		'label' => esc_html__( 'Number of Сolumns', 'elementor-pro' ),
		// 		'type' => Controls_Manager::SELECT,
		// 		'default' => '2-col',
		// 		'options' => [
		// 			'2-col' => esc_html__( '2 Col', 'elementor-pro' ),
		// 			'3-col' => esc_html__( '3 Col', 'elementor-pro' ),
		// 		],
		// 		'frontend_available' => true,
		// 	]
		// );
		// $this->add_control(
		// 	'card_size',
		// 	[
		// 		'label' => esc_html__( 'Card Size', 'elementor-pro' ),
		// 		'type' => Controls_Manager::SELECT,
		// 		'default' => 'xsmall',
		// 		'options' => [
		// 			'xsmall' => esc_html__( 'XSmall', 'elementor-pro' ),
		// 			'small' => esc_html__( 'Small', 'elementor-pro' ),
		// 			'large' => esc_html__( 'Large', 'elementor-pro' ),
		// 			'xlarge' => esc_html__( 'XLarge', 'elementor-pro' ),
		// 		],
		// 		'frontend_available' => true,
		// 	]
		// );

		// $this->add_control(
		// 	'card_type',
		// 	[
		// 		'label' => esc_html__( 'Card Type', 'elementor-pro' ),
		// 		'type' => Controls_Manager::SELECT,
		// 		'default' => 'vertical',
		// 		'options' => [
		// 			'horizontal' => esc_html__( 'Horizontal', 'elementor-pro' ),
		// 			'vertical' => esc_html__( 'Vertical', 'elementor-pro' ),
		// 		],
		// 		'frontend_available' => true,
		// 	]
		// );

		$repeater = new Repeater();
		$repeater->add_control(
			'image',
			[
				'label' => __( 'Image XL', 'elementor-custom' ),
				'type' => Controls_Manager::MEDIA,
			]
		);
		$repeater->add_control(
			'image_md',
			[
				'label' => __( 'Image MD', 'elementor-custom' ),
				'type' => Controls_Manager::MEDIA,
			]
		);
		$repeater->add_control(
			'image_sm',
			[
				'label' => __( 'Image SM', 'elementor-custom' ),
				'type' => Controls_Manager::MEDIA,
			]
		);
		$repeater->add_control(
			'heading',
			[
				'label' => esc_html__( 'Title & Description', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Heading', 'elementor-pro' ),
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'description',
			[
				'label' => esc_html__( 'Description', 'elementor-pro' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'elementor-pro' ),
				'show_label' => false,
			]
		);
		$repeater->add_control(
			'button_text',
			[
				'label' => esc_html__( 'Button Text', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Learn More', 'elementor-pro' ),
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => esc_html__( 'Link', 'elementor-pro' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'elementor-pro' ),
			]
		);

		$repeater->add_control(
			'label',
			[
				'label' => __( 'Label', 'elementor-custom' ),
				'type' => Controls_Manager::MEDIA,
			]
		);

		$this->add_control(
			'slides',
			[
				'label' => __( 'Slides', 'elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'heading' => __( 'Slide 1', 'elementor' ),
						'description' => 'Cookie muffin gummies oat cake candy canes chupa chups. Croissant cookie carrot cake biscuit icing sweet roll sesame snaps pie bear claw. Sugar plum icing jelly carrot cake biscuit.',
						'button_text' => 'Learn More',
					],
					[
						'heading' => __( 'Slide 2', 'elementor' ),
						'description' => 'Brownie cake chocolate gingerbread oat cake brownie cheesecake. Fruitcake pie jelly beans shortbread jujubes. Ice cream jelly beans tart marzipan shortbread.',
						'button_text' => 'Learn More',
					],
				],
				'title_field' => '{{{ heading }}}',
			]
		);

		$this->add_responsive_control(
			'slides_height',
			[
				'label' => esc_html__( 'Height', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 400,
				],
				'size_units' => [ 'px', 'vh', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .swiper-slide' => 'height: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'slides_width',
			[
				'label' => esc_html__( 'Width label', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 81,
				],
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .swiper-slide__label' => 'width: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

        $this->add_responsive_control(
            'right',
            [
                'label' => __( 'Right for label', 'hotspring-lang' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => -200,
                        'max' => 800
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100
                    ],
                ],
                'default' => [
                    'size' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .swiper-slide__label' => 'position: absolute; right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

		$this->add_responsive_control(
            'top',
            [
                'label' => __( 'Top for label', 'hotspring-lang' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => -200,
                        'max' => 800
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100
                    ],
                ],
                'default' => [
                    'size' => 0,
                ],
                'selectors' => [
                    '{{WRAPPER}} .swiper-slide__label' => 'position: absolute; top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

		$this->add_control(
			'navigation_style',
			[
				'label' => esc_html__( 'Navigation Style', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__( 'Default Arrow', 'elementor-pro' ),
					'stacked' => esc_html__( 'Stacked', 'elementor-pro' ),
					'filled' => esc_html__( 'Filled', 'elementor-pro' ),
				],
				'frontend_available' => true,
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
		// d($settings);
		print '
            <div class="swiper-object">
                <div class="swiper section-carousel-slider swiper-container">
                    <div class="swiper-wrapper">
                        ';

                        foreach ( $settings['slides'] as $slide ) {
                            print $this->getSlide( $slide );
                        }
                print '
                    </div>
                </div>
                ' . getSwiperNext() . '
                ' . getSwiperPrev() . '
                ' . getSwiperPagination() . '
            </div>';
	}

	protected function getSlide( $slide ) {

		$image = wp_get_attachment_image( $slide['image']['id'], array('1366', '547'), '', array( 'class' => 'swiper-slide__image image_xl col-10 col-md-5 col-xl-6 offset-1 offset-md-0', 'alt'=>$heading ));
		$image_md = wp_get_attachment_image( $slide['image_md']['id'], '', '', array( 'class' => 'swiper-slide__image image_md col-10 col-md-5 col-xl-6 offset-1 offset-md-0', 'alt'=>$heading ));
		$image_sm = wp_get_attachment_image( $slide['image_sm']['id'], '', '', array( 'class' => 'swiper-slide__image image_sm col-10 col-md-5 col-xl-6 offset-1 offset-md-0', 'alt'=>$heading ));
		$label_image = wp_get_attachment_image( $slide['label']['id'], 'xs-1-1', '', array( 'class' => 'swiper-slide__label', 'alt'=>$heading ));
		return '
			<div class="swiper-slide container-fluid">
				<div class="row align-items-xl-center">
					<div class="swiper-slide__info col-12 col-md-5 col-xl-3 offset-md-1">
						<h2 class="swiper-slide__info__heading">' . globalSuperscript($slide['heading']) . '</h2>
						<div class="swiper-slide__info__description">' . globalSuperscript($slide['description']) . '</div>
						<a class="btn btn-primary">Shop Now</a>
					</div>
					' . $image . $image_md . $image_sm . $label_image . '
				</div>
			</div>
		';
	}
}
