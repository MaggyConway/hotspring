<?php
namespace ComponentsKit\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Section_Content
 *
 * Elementor widget for Section_Content.
 *
 * @since 1.0.0
 */
class Section_Content extends Widget_Base {

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
		return 'section_content';
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
		return __( 'Content Section', 'hotspring-lang' );
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
            'image',
            [
                'label' => __( 'Choose Image', 'hotspring-lang' ),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'image_position',
            [
                'label' => esc_html__( 'Image position', 'hotspring-lang' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'left' => esc_html__( 'Left', 'hotspring-lang' ),
                    'right' => esc_html__( 'Right', 'hotspring-lang' ),
                ],
                'default' => 'left',
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'image_icon',
            [
                'label' => __( 'Choose Image Icon', 'hotspring-lang' ),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_responsive_control(
            'width',
            [
                'label' => __( 'width', 'hotspring-lang' ),
                'type' => Controls_Manager::SLIDER,
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
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .image_icon' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'left',
            [
                'label' => __( 'left', 'hotspring-lang' ),
                'type' => Controls_Manager::SLIDER,
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
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .image_icon' => 'position: absolute; left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        /*$this->add_control(
            'left',
            [
                'label' => __( 'left', 'hotspring-lang' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => -500,
                        'max' => 500
                    ],
                ],
                'default' => [
                    'size' => 0,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .image_icon' => 'position: absolute; left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );*/

        $this->add_control(
            'section_type',
            [
                'label' => esc_html__( 'Section type', 'hotspring-lang' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'large' => esc_html__( 'Large', 'hotspring-lang' ),
                    'medium' => esc_html__( 'Medium', 'hotspring-lang' ),
                    'small' => esc_html__( 'Small', 'hotspring-lang' ),
                ],
                'default' => 'large',
                'separator' => 'before'
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
			'description',
			[
				'label' => __( 'Description', 'hotspring-lang' ),
				'type' => Controls_Manager::TEXTAREA,
			]
		);

        $this->add_control(
            'text_link',
            [
                'label' => __( 'Text for link', 'hotspring-lang' ),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'link',
            [
                'label' => __( 'link', 'hotspring-lang' ),
                'type' => Controls_Manager::URL,
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

        $image = wp_get_attachment_image( $settings['image']['id'], 'xxl-4-3', '', array( 'class' => 'card-img-top', 'alt' => $settings['image']['alt'] ));
        $image_icon = wp_get_attachment_image( $settings['image_icon']['id'], '', '', array( 'class' => 'image_icon', 'alt' => $settings['image_icon']['alt'] ));

        if( $settings['section_type'] == 'large' ) {

            $this->getLargeSection( $image, $settings['image_position'], $settings['title'], $settings['description'], $settings['link']['url'], $settings['text_link'], $image_icon );

        } elseif( $settings['section_type'] == 'medium' ) {

            $this->getMediumSection( $image, $settings['image_position'], $settings['title'], $settings['description'], $settings['link']['url'], $settings['text_link'], $image_icon );

        } else {

            $this->getSmallSection( $image, $settings['image_position'], $settings['title'], $settings['description'], $settings['link']['url'], $settings['text_link'], $image_icon );

        }
	}

    protected function getLargeSection( $image, $image_position, $title, $description, $url, $text_link, $image_icon ) {

        $gx = $image_position == 'left' ? 'ps-0' : 'pe-0';
	    $image_block = '
            <div class="col-xl-6 col-sm-12 gx-xl-6 ' . $gx . '">
                ' . $image . '
                ' . $image_icon . '
            </div>
	    ';

	    $content_block = '
	        <div class="col"></div>
            <div class="col-xl-4 col-sm-12 content_wrapper">
                <h2 class="section-content__title">' . $title . '</h2>
                <div class="section-content__description">' . $description . '</div>
                <div class="section-content__link"><a href="' . $url . '">' . $text_link . '</a></div>
            </div>
            <div class="col"></div>
        ';

        if( $image_position == 'left' ) {

            echo '
                <div class="">
                    <div class="row g-0 align-items-center flex-xl-row">
                        ' . $image_block . '
                        ' . $content_block . '
                    </div>
                </div>
            ';

        } else {

            echo '
                <div class="">
                    <div class="row g-0 align-items-center flex-xl-row flex-column-reverse">
                        ' . $content_block . '
                        ' . $image_block . '
                    </div>
                </div>
            ';
        }
    }

    protected function getMediumSection( $image, $image_position, $title, $description, $url, $text_link, $image_icon ) {

        $gx = $image_position == 'left' ? 'ps-0' : 'pe-0';
        $image_block = '
            <div class="col-xl-5 col-sm-12 ' . $gx . '">
                ' . $image . '
                ' . $image_icon . '
            </div>
	    ';

        $content_block = '
	        <div class="col"></div>
            <div class="col-xl-5 col-sm-12">
                <h2 class="section-content__title">' . $title . '</h2>
                <div class="section-content__description">' . $description . '</div>
                <div class="section-content__link"><a href="' . $url . '">' . $text_link . '</a></div>
            </div>
            <div class="col"></div>
        ';

        if( $image_position == 'left' ) {

            echo '
                <div class="container-fluid">
                    <div class="row align-items-center flex-xl-row">
                        ' . $image_block . '
                        ' . $content_block . '
                    </div>
                </div>
            ';

        } else {

            echo '
                <div class="container-fluid">
                    <div class="row align-items-center flex-xl-row flex-column-reverse">
                        ' . $content_block . '
                        ' . $image_block . '
                    </div>
                </div>
            ';
        }
    }

    protected function getSmallSection( $image, $image_position, $title, $description, $url, $text_link, $image_icon ) {

        $gx = $image_position == 'left' ? 'ps-0' : 'pe-0';
        $image_block = '
            <div class="col-xl-4 col-sm-12 ' . $gx . '">
                ' . $image . '
                ' . $image_icon . '
            </div>
	    ';

        $content_block = '
	        <div class="col-xl-5 col-sm-12">
                <h2 class="section-content__title">' . globalSuperscript($title) . '</h2>
                <div class="section-content__description">' . globalSuperscript($description) . '</div>
                <div class="section-content__link"><a href="' . $url . '">' . globalSuperscript($text_link) . '</a></div>
            </div>
        ';

        if( $image_position == 'left' ) {

            echo '
                <div class="container">
                    <div class="row align-items-center flex-xl-row">
                        ' . $image_block . '
                        <div class="col"></div>
                        ' . $content_block . '
                    </div>
                </div>
            ';

        } else {

            echo '
                <div class="container">
                    <div class="row align-items-center flex-xl-row flex-column-reverse">
                        ' . $content_block . '
                        <div class="col"></div>
                        ' . $image_block . '
                    </div>
                </div>
            ';
        }
    }

}
