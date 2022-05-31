<?php
namespace ComponentsKit\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Sections_Reviews
 *
 * Elementor widget for Sections_Reviews.
 *
 * @since 1.0.0
 */
class Sections_Reviews extends Widget_Base {

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
		return 'sections-reviews';
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
		return __( 'Sections: Reviews', 'hotspring-lang' );
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
            'name',
            [
                'label' => __( 'Name', 'hotspring-lang' ),
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
            'number_stars',
            [
                'label' => __( 'Number of stars', 'hotspring-lang' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 5,
                'step' => 0.5,
                'default' => 5,
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

        $this->add_control(
            'title_review',
            [
                'label' => __( 'Title review', 'hotspring-lang' ),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'title_description',
            [
                'label' => __( 'Title description', 'hotspring-lang' ),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );

        $this->add_control(
            'create_review_btn',
            [
                'label' => esc_html__( 'Create review button', 'elementor-pro' ),
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
		$settings = $this->get_settings_for_display();

        $open_tag_wrap = '';
        $close_tag_wrap = '';
        if( $settings['use_wrap'] == 'yes' ) {
            $open_tag_wrap = '<div class="col-md-12"><div class="row">';
            $close_tag_wrap = '</div></div>';
        }
		?>
		<div class="title">
            <div class="container">
                <div class="row text-center text-xl-start">

                    <?php echo $open_tag_wrap ?>

                        <div class="col-xl-4 col-sm-12">
                            <h2><?php echo globalSuperscript($settings['title_review']); ?></h2>
                            <div class="title-description">
                                <?php echo globalSuperscript($settings['title_description']); ?>
                            </div>

                            <div class="row flex-column flex-sm-row">
                                <div class="col">
                                    <div class="link-btn">
                                        <a href="#" class="btn btn-primary">See All Reviews</a>
                                    </div>
                                </div>

                                <?php
                                if( $settings['create_review_btn'] == 'yes' ) {
                                    ?>
                                    <div class="col">
                                        <div class="link-btn">
                                            <a href="#" class="btn btn-secondary">Create a Review</a>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>

                        <div class="col-xl-7 col-sm-12 offset-xl-1 review">
                            <div class="review__stars">
                                <?php echo star_rating( $settings['number_stars'], 'yellow', null, '' ); ?>
                            </div>
                            <div class="review__text">
                                <?php echo globalSuperscript($settings['description']); ?>
                            </div>
                            <div class="review__author">
                                <?php echo globalSuperscript($settings['name']); ?>
                            </div>
						</div>
                    <?php echo $close_tag_wrap ?>
                </div>
            </div>
        </div>
        <?php
	}
}
