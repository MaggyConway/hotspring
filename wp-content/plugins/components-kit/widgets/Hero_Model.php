<?php
namespace ComponentsKit\Widgets;

use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Section_Media_Carousel
 *
 * Elementor widget for Section_Media_Carousel.
 *
 * @since 1.0.0
 */
class Hero_Model extends Widget_Base {

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
		return 'hero-model';
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
		return __( 'Hero model', 'hotspring-lang' );
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
			'post_type' => 'model',
			'posts_per_page' => -1,
			'orderby' => 'id',
			'order' => 'DESC'
		);

		$posts = get_posts( $args );
		$models['0000'] = 'Current model';

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
			'select-model',
			[
				'label' => __( 'Select model', 'hotspring-lang' ),
				'type' => Controls_Manager::SELECT,
                'default' => '0000',
				'options' => $models
			]
		);

		$this->add_control(
			'description',
			[
				'label' => __( 'Description', 'hotspring-lang' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'link-brochure',
			[
				'label' => __( 'Link: Get Brochure', 'hotspring-lang' ),
				'type' => Controls_Manager::URL,
				'default' => [
					'url' => '/get-brochure',
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'link-personal',
			[
				'label' => __( 'Link: Get Personal Quote', 'hotspring-lang' ),
				'type' => Controls_Manager::URL,
				'default' => [
					'url' => '/get-pricing',
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'elementor-pro' ),
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
            'slide-text',
            [
                'label' => esc_html__( 'Text info', 'hotspring-lang' ),
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

		$output .= '
			<div class="container hero-model">
				<div class="row">
					<div class="col-lg-5 col-md-12 col-sm-12">
		';

		$output .= $this->getContent();

		$output .= '		
					</div>
					<div class="col-lg-7 col-md-12 col-sm-12">
		';

		$output .= $this->getSlider();

		$output .= '
					</div>
				</div>
			</div>
		';

		echo $output;
		
	}

	protected function getContent() {

		$output = '';
		$settings = $this->get_settings_for_display();
		$select_model = $settings['select-model'];
		
		if ( $select_model === '0000' ) {
			$model_id = get_the_ID();
		} else {
			$model_id = $settings['select-model'];
		}
		
		$bazaarvoice_data = get_field( 'bazaarvoice_data', $model_id );
		$model_total_seats = get_field( 'model_total_seats', $model_id );
		$model_style = get_field( 'model_style', $model_id );
		$model_voltage = get_field( 'model_voltage', $model_id );
		$model_dimensions = get_field( 'model_dimensions', $model_id );
		$model_water_care = get_field( 'model_water_care', $model_id );
		$price_range = strlen( get_field( 'model_price', $model_id ) );
		$cost = getCost( $price_range );
		$url_brochure = $settings['link-brochure']['url'];
		$url_personal = $settings['link-personal']['url'];

		$output .= '<h2>' . get_the_title( $model_id ) . '</h2>';

		if ( isset( $bazaarvoice_data ) && !empty( $bazaarvoice_data ) ) {
			$bazaarvoice_data = json_decode( $bazaarvoice_data, true );
			$averageRating = star_rating( $bazaarvoice_data['averageRating'], 'yellow', null, '');

			$output .= '
				<div>' . $averageRating . '
					<a href="#section--reviews"> (' . $bazaarvoice_data['totalReviews'] . ' of Reviews)</a>
					<span class="delimeter"> | </span>
					' . $cost . '
				</div>
			';
		}

		$output .= '<div>' . $settings['description'] . '</div>';

		$output .= '
			<div class="container-btn">
				<div class="row">
					<div class="col-12 col-xl-auto">
		';

		if ( isset( $url_brochure ) && !empty( $url_brochure ) ) {
			$output .= '
						<a href="' . $url_brochure . '" class="btn btn-primary">Get Brochure</a>
			';
		}

		if ( isset( $url_personal ) && !empty( $url_personal ) ) {
			$output .= '
						<a href="' . $url_personal . '" class="btn btn-outline-primary">Get Personal Quote</a>
			';
		}

		$output .= '
					</div>
				</div>
			</div>
		';

		$output .= '
			<div class="specifications">
				<div class="row">
		';

		if ( isset( $model_total_seats ) && !empty( $model_total_seats) ) {
			$output .= '
					<div class="col-2 d-flex flex-column">
						<span class="specifications__title">People</span>
						<span class="specifications__value">' . $model_total_seats . ' Seats</span>
					</div>
			';
		}

		if ( isset( $model_style ) && !empty( $model_style )) {
			$output .= '
					<div class="col-2 d-flex flex-column">
						<span class="specifications__title">Seating</span>
						<span class="specifications__value">' . $model_style . '</span>
					</div>
			';
		}

		if ( isset( $model_voltage ) && !empty( $model_voltage )) {
			$output .= '
					<div class="col-2 d-flex flex-column">
						<span class="specifications__title">Voltage</span>
						<span class="specifications__value">' . $model_voltage . '</span>
					</div>
			';
		}

		$output .= '
				</div>
				<div class="row">
		';

		if ( isset( $model_dimensions ) && !empty( $model_dimensions )) {
			$output .= '
					<div class="col-12 col-md-6 col-lg-12 d-flex flex-column">
						<span class="specifications__title">Size</span>
						<span class="specifications__value">' . str_replace( '<br />', ' | ', $model_dimensions ) . '</span>
					</div>
			';
		}

		if ( isset( $model_water_care ) && !empty( $model_water_care )) {
			$output .= '
					<div class="col-12 col-md-6 col-lg-12 d-flex flex-column">
						<span class="specifications__title">Water Care</span>
						<span class="specifications__value">' . $model_water_care . '</span>
					</div>
			';
		}

		$output .= '
				</div>
			</div>
		';

		return $output;

	}

	protected function getSlider() {
		
		$output = '';
		$settings = $this->get_settings_for_display();

		$output .= '
			<div class="swiper-container swiper-container-thumb">
				<div class="swiper swiper-hero-model">
					<div class="swiper-wrapper">
		';

                    foreach ( $settings['slides'] as $slide ) {
                        $output .= '
                            <div class="swiper-slide">
                                <img src="'.$slide['image']['url'].'">
                                <div class="swiper-slide-info"><span class="swiper-slide-info__text">' . $slide['slide-text'] . '</span><i aria-hidden="true" class="fas fa-info"></i></div> 
                            </div>
						';
                    }
		
		$output .= '
					</div>
					<div class="swiper-button-next"></div>
					<div class="swiper-button-prev"></div>
				</div>
				<div thumbsSlider="" class="swiper mySwiper">
					<div class="swiper-wrapper">
		';

                    foreach ( $settings['slides'] as $slide ) {
                        $output .= '
                            <div class="swiper-slide">
                                <img src="'.$slide['image']['url'].'">
                            </div>
						';
                    }

		$output .= '
					</div>
				</div>
			</div>
		';

		return $output;

	}
}
