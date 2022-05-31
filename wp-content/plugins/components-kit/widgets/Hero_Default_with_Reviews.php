<?php
namespace ComponentsKit\Widgets;

use Elementor\Utils;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Files\File_Types\Svg;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Hero Default with Reviews
 *
 * Elementor widget for Hero Default with Reviews.
 *
 * @since 1.0.0
 */
class Hero_Default_with_Reviews extends Widget_Base {

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
        return 'hero-default-with-reviews';
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
        return __( 'Hero Default with Reviews', 'hotspring-lang' );
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
            'small_image',
            [
                'label' => __( 'Choose Small Image', 'hotspring-lang' ),
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
            'small_image_width',
            [
                'label' => __( 'Width small image', 'hotspring-lang' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 430
                    ],
                ],
                'default' => [
                    'size' => 430,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .small_image' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
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
        $this->add_responsive_control(
			'icon_top',
			[
				'label' => __( 'Icon top', 'elementor' ),
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
					'{{WRAPPER}} .elementor-icon' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);
        
		$this->add_responsive_control(
			'icon_left',
			[
				'label' => __( 'Icon left', 'elementor' ),
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
					'{{WRAPPER}} .elementor-icon' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
            'left',
            [
                'label' => __( 'Small Image left', 'elementor' ),
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
                    '{{WRAPPER}} .small_image' => 'left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'bottom',
            [
                'label' => __( 'Small Image bottom', 'elementor' ),
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
                    '{{WRAPPER}} .small_image' => 'position: absolute; bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => __( 'Title', 'hotspring-lang' ),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_responsive_control(
            'm_top',
            [
                'label' => __( 'Indent title from image', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'range' => [
                    'px' => [
                        'min' => 0,
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
                    '{{WRAPPER}} .hero .hero__title' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'description',
            [
                'label' => __( 'Description', 'hotspring-lang' ),
                'type' => Controls_Manager::TEXTAREA,
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
        global $post;
        $settings = $this->get_settings_for_display();

        $image = wp_get_attachment_image( $settings['image']['id'], 'xl-4-3', '', array( 'class' => 'card-img-top', 'alt' => $settings['image']['alt'] ));
        $small_image = wp_get_attachment_image( $settings['small_image']['id'], '', '', array( 'class' => 'small_image', 'alt' => $settings['small_image']['alt'] ));

        $result_icon = !empty($settings['icon']['value']['id']) ? '<div class="elementor-icon">'. Svg::get_inline_svg( $settings['icon']['value']['id'] ).'</div>' : '';
        ?>

        <div class="hero">
            <div class="row g-0 align-items-end align-items-xl-center flex-column-reverse flex-xl-row">

                <div class="col"></div>
                <div class="col-xl-4 col-sm-12 g-xl-0">
                    <h1 class="hero__title"><?php echo $settings['title']; ?></h2>
                    <div class="hero__description"><?php echo $settings['description']; ?></div>
                    <?php
                    $bazaarvoice_data = get_field( 'bazaarvoice_data', $post->ID );
                    if ( isset($bazaarvoice_data) && !empty($bazaarvoice_data)) {
                        $bazaarvoice_data = json_decode( $bazaarvoice_data, true );
                        $averageRating = star_rating($bazaarvoice_data['averageRating'], 'yellow', null, '');
                        echo '<div class="hero__reviews">' . $averageRating . '<a href="#section--reviews"> (' . $bazaarvoice_data['totalReviews'] . ' of Reviews)</a></div>';
                    }
                    ?>
                </div>
                <div class="col"></div>
                <div class="col-xl-6 col-10 col-sm-10 position-relative">
                    <?php echo $image ?>
                    <?php echo $small_image ?>
                    <?php echo $result_icon; ?>
                </div>
            </div>
        </div>
        <?php
    }
}
