<?php
namespace ComponentsKit\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Section_CTA_Button
 *
 * Elementor widget for Section_CTA_Button.
 *
 * @since 1.0.0
 */
class Section_CTA_Button extends Widget_Base {

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
		return 'section-cta-button';
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
		return __( 'CTA button', 'hotspring-lang' );
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
            'style_link',
            [
                'label' => esc_html__( 'Section type', 'hotspring-lang' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'btn-primary' => esc_html__( 'Primary', 'hotspring-lang' ),
                    'btn-secondary' => esc_html__( 'Secondary', 'hotspring-lang' ),
                    'btn-success' => esc_html__( 'Success', 'hotspring-lang' ),
                    'btn-danger' => esc_html__( 'Danger', 'hotspring-lang' ),
                    'btn-warning' => esc_html__( 'Warning', 'hotspring-lang' ),
                    'btn-info' => esc_html__( 'Info', 'hotspring-lang' ),
                    'btn-light' => esc_html__( 'Light', 'hotspring-lang' ),
                    'btn-dark' => esc_html__( 'Dark', 'hotspring-lang' ),
                    'btn-link' => esc_html__( 'Link', 'hotspring-lang' ),
                ],
                'default' => 'btn-primary',
                'separator' => 'before'
            ]
        );
        
        $this->add_control(
            'text_link',
            [
                'label' => __( 'Text for link', 'hotspring-lang' ),
                'type' => Controls_Manager::TEXT,
                'default' => 'Text for link',
            ]
        );

        $this->add_control(
            'link',
            [
                'label' => __( 'link', 'hotspring-lang' ),
                'type' => Controls_Manager::URL,
            ]
        );

        $this->add_responsive_control(
			'alignment',
			[
				'label' => esc_html__( 'Alignment', 'hotspring-lang' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'elementor-align-left' => [
						'title' => esc_html__( 'Left', 'hotspring-lang' ),
						'icon' => 'eicon-text-align-left',
					],
					'elementor-align-center' => [
						'title' => esc_html__( 'Center', 'hotspring-lang' ),
						'icon' => 'eicon-text-align-center',
					],
					'elementor-align-right' => [
						'title' => esc_html__( 'Right', 'hotspring-lang' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'elementor-align-left',
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
					'{{WRAPPER}} .btn' => 'text-transform: {{VALUE}};',
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

		$output = '';
        $settings = $this->get_settings_for_display();
        $bs_class  = $settings['style_link'];
        $alignment = $settings['alignment'];
        $link = $settings['link']['url'];
        $text_link = $settings['text_link'];

        $output .= '
            <div class="row">
                <div class="col ' . $alignment . '">
                    <a href="' . $link . '" class="btn cta-btn ' . $bs_class . '">' . $text_link . '</a>
                </div>
            </div>
        ';

        print $output;
        
	}

}