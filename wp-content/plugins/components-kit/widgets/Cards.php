<?php
namespace ComponentsKit\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Two Cards
 *
 * Elementor widget for Two Cards.
 *
 * @since 1.0.0
 */
class Cards extends Widget_Base {

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
		return 'cards';
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
		return __( 'Cards', 'hotspring-lang' );
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
			'col_number',
			[
				'label' => esc_html__( 'Number of Сolumns', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => '2-col',
				'options' => [
					'2-col' => esc_html__( '2 Col', 'elementor-pro' ),
					'3-col' => esc_html__( '3 Col', 'elementor-pro' ),
				],
				'frontend_available' => true,
			]
		);
		$this->add_control(
			'card_size',
			[
				'label' => esc_html__( 'Card Size', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'xsmall',
				'options' => [
					'xsmall' => esc_html__( 'XSmall', 'elementor-pro' ),
					'small' => esc_html__( 'Small', 'elementor-pro' ),
					'large' => esc_html__( 'Large', 'elementor-pro' ),
					'xlarge' => esc_html__( 'XLarge', 'elementor-pro' ),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'card_type',
			[
				'label' => esc_html__( 'Card Type', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'vertical',
				'options' => [
					'horizontal' => esc_html__( 'Horizontal', 'elementor-pro' ),
					'vertical' => esc_html__( 'Vertical', 'elementor-pro' ),
				],
				'frontend_available' => true,
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
					'fluid' => esc_html__( 'Fluid', 'elementor-pro' ),
				]
			]
		);


		$repeater = new Repeater();
		$repeater->add_control(
			'image',
			[
				'label' => __( 'Image', 'elementor-custom' ),
				'type' => Controls_Manager::MEDIA,
			]
		);
		$repeater->add_control(
			'icon',
			[
				'label' => esc_html__( 'Icon', 'elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa5compatibility' => 'icon',
				'default' => [
					// 'value' => 'fas fa-star',
					// 'library' => 'fa-solid',
				],
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

		$this->add_control(
			'cards',
			[
				'label' => __( 'Cards', 'elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'heading' => __( 'Card 1', 'elementor' ),
						'description' => 'Cookie muffin gummies oat cake candy canes chupa chups. Croissant cookie carrot cake biscuit icing sweet roll sesame snaps pie bear claw. Sugar plum icing jelly carrot cake biscuit.',
						'button_text' => 'Learn More',
					],
					[
						'heading' => __( 'Card 2', 'elementor' ),
						'description' => 'Brownie cake chocolate gingerbread oat cake brownie cheesecake. Fruitcake pie jelly beans shortbread jujubes. Ice cream jelly beans tart marzipan shortbread.',
						'button_text' => 'Learn More',
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
		$this->add_responsive_control(
			'text_align',
			[
				'label' => esc_html__( 'Alignment', 'elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justified', 'elementor' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					// '{{WRAPPER}} .card-text' => 'text-align: {{VALUE}};',
					// '{{WRAPPER}} .card-title' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .card' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .elementor-icon-wrapper' => 'text-align: {{VALUE}};',
				],
			]
		);

		// $this->add_responsive_control(
		// 	'icon_align',
		// 	[
		// 		'label' => esc_html__( 'Icon Alignment', 'elementor' ),
		// 		'type' => Controls_Manager::CHOOSE,
		// 		'options' => [
		// 			'left' => [
		// 				'title' => esc_html__( 'Left', 'elementor' ),
		// 				'icon' => 'eicon-text-align-left',
		// 			],
		// 			'center' => [
		// 				'title' => esc_html__( 'Center', 'elementor' ),
		// 				'icon' => 'eicon-text-align-center',
		// 			],
		// 			'right' => [
		// 				'title' => esc_html__( 'Right', 'elementor' ),
		// 				'icon' => 'eicon-text-align-right',
		// 			],
		// 			'justify' => [
		// 				'title' => esc_html__( 'Justified', 'elementor' ),
		// 				'icon' => 'eicon-text-align-justify',
		// 			],
		// 		],
		// 		'selectors' => [
		// 			'{{WRAPPER}} .elementor-icon-wrapper' => 'text-align: {{VALUE}};',
		// 		],
		// 	]
		// );

		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'fill: {{VALUE}}; color: {{VALUE}}; border-color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'icon_padding',
			[
				'label' => esc_html__( 'Icon Padding', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .elementor-icon' => 'padding: {{SIZE}}{{UNIT}};',
				],
				'range' => [
					'em' => [
						'min' => 0,
						'max' => 5,
					],
				]
			]
		);
		$this->add_responsive_control(
			'top',
			[
				'label' => __( 'top', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range' => [
					'px' => [
						'max' => 1000,
					],
				],
				'default' => [
					'size' => 0,
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-wrapper' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);
        
		$this->add_responsive_control(
			'left',
			[
				'label' => __( 'left', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range' => [
					'px' => [
						'max' => 1000,
					],
				],
				'default' => [
					'size' => 0,
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-wrapper' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'min_height',
			[
				'label' => esc_html__( 'Min Height', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .card' => 'min-height: {{SIZE}}{{UNIT}};',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 800,
					],
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
		$settings = $this->get_settings_for_display();
		$col_class = 'col-12';
		if ( $settings['col_number'] == '2-col' ) {
			$col_class = 'col-12 col-md-6';
			$col_number = 'two-col';
		} else {
			$col_class = 'col-sm-4';
            $col_number = 'three-col';
		}

        $open_tag_mobile = '';
        $close_tag_mobile = '';
        if( $settings['swiper_mobile'] == 'yes' ) {
            $open_tag_mobile = '<div class="swiper_mobile">';
            $close_tag_mobile = '</div>';
        }

        $open_tag_wrap = '';
        $close_tag_wrap = '';
        if( $settings['use_wrap'] == 'yes' ) {
            $open_tag_wrap = '<div class="row"><div class="col-lg-10 offset-lg-1">';
            $close_tag_wrap = '</div></div>';
        }
		$container = 'container';
		if( $settings['use_wrap'] == 'fluid' ) {
			$container = 'container-fluid';
        }

		print $open_tag_mobile . '
            <div class="' . $container . ' swiper-wrap">
                ' . $open_tag_wrap . '
                <div class="row">';
                    foreach ( $settings['cards'] as $card ) {
                        print '
                            <div class="model-card-slide ' . $col_class . '">' .
                                getBaseCard($settings['card_size'], $settings['card_type'], $card['image']['id'], $card['heading'], $card['description'], $card['button_text'] , $card['link']['url'], $card['icon'], $col_number ) . '
                            </div>';
                    }
                    print ' </div>
                ' . $close_tag_wrap . '
            </div>
            ' . $close_tag_mobile;
	}

}
