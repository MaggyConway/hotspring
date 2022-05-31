<?php
namespace ComponentsKit\Widgets;

use Elementor\Widget_Base;
use Elementor\Plugin;
use Elementor\Embed;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Video;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Image_Size;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Section_Media_Carousel
 *
 * Elementor widget for Section_Media_Carousel.
 *
 * @since 1.0.0
 */
class Section_Media_Carousel extends Widget_Base {

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
		return 'section-media-carousel';
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
		return __( 'Section media carousel', 'hotspring-lang' );
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
				'default' => esc_html__( 'Features', 'hotspring-lang' ),
			]
		);

        $repeater = new Repeater();
		
		$repeater->add_control(
			'label',
			[
				'label' => esc_html__( 'Label groups', 'elementor-pro' ),
				'type' => Controls_Manager::TEXT,
			]
		);

        $repeater->add_control(
			'type',
			[
				'type' => Controls_Manager::CHOOSE,
				'label' => esc_html__( 'Type', 'elementor-pro' ),
				'default' => 'image',
				'options' => [
					'image' => [
						'title' => esc_html__( 'Image', 'elementor-pro' ),
						'icon' => 'eicon-image-bold',
					],
					'video' => [
						'title' => esc_html__( 'Video', 'elementor-pro' ),
						'icon' => 'eicon-video-camera',
					],
				],
				'toggle' => false,
			]
		);

		$repeater->add_control(
			'image',
			[
				'label' => esc_html__( 'Image', 'elementor-pro' ),
				'type' => Controls_Manager::MEDIA,
			]
		);

		$repeater->add_control(
			'video',
			[
				'label' => esc_html__( 'Video Link', 'elementor-pro' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'Enter your video link', 'elementor-pro' ),
				'description' => esc_html__( 'YouTube link', 'elementor-pro' ),
				'options' => false,
				'condition' => [
					'type' => 'video',
				],
			]
		);

		$repeater->add_control(
			'label_image',
			[
				'label' => __( 'Label', 'elementor-custom' ),
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
        

        
        $this->add_control(
			'slide',
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
				'title_field' => '{{{ label }}}: {{{ heading }}}',
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

        foreach ( $settings['slide'] as $slide ) {
            $labels[] = $slide['label'];
        }

        $labels = array_unique($labels);

        echo '
			<div class="title">
				<h2 class="text-center">' . $settings['title'] . '</h2>
			</div>
		';

        $x = 0;
        print '
			<div class="media-tabs">
				<div class="container">
					<ul class="media-tabs__header d-flex justify-content-center flex-column flex-md-row">
		';
        foreach ( $labels as $key => $label ) {
            $x++;
            if ( $label !== '' ) {
                print '
					<li class="media-tabs__tab">
						<a class="media-tabs__link tabs-target" data-tab-id="data-tab-id-' . $x . '">' . $label . '</a>
					</li>
				';
            }
        }
        print '
					</ul>
					<div class="media-tabs__content">
						<div class="swiper-container swiper section-media-carousel">
							<div class="swiper-wrapper">
		';
        $slides = array();
        $x = 0;
        foreach ( $labels as $key => $label ) {
            $slides = [];
            $x++;
            foreach ( $settings['slide'] as $slide ) {
                if ( $label == $slide['label'] ) {
                    $slides[] = $slide;
                }
            }
            foreach ( $slides as $slide ) {
                $this->getSlide( $slide, $x );
            }
        }
        print '
						</div>
						</div>
						<div class="swiper-button-next"></div>
						<div class="swiper-button-prev"></div>
						<div class="swiper-pagination"></div>
					
					</div>
				</div>
			</div>
		';

    }

	protected function getSlide( $slide, $x ) {
		
		$wv = new Widget_Video;
		if ( empty( $slide['image']['id'] ) ) {
			$image_url = Utils::get_placeholder_image_src();
		} else {
			$image_url = wp_get_attachment_url( $slide['image']['id'], 'xl-4-3' );
		}

		$label_image = wp_get_attachment_image( $slide['label_image']['id'], 'xs-1-1', '', array( 'class' => 'swiper-slide__label', 'alt'=>$heading ));
		$lightbox_url = Embed::get_embed_url( $slide['video']['url'] );
		$wv->add_render_attribute( 'video-wrapper', 'class', 'elementor-wrapper' );
		$wv->add_render_attribute( 'video-wrapper', 'class', 'elementor-open-lightbox' );
		
		print '
			<div class="swiper-slide data-tab-id-' . $x . '">
				<div class="row d-flex align-items-center">
					<div class="order-1 order-md-0 col-md-5 ">
						<h3 class="swiper-slide__heading">' . $slide['heading'] . '</h3>
						<p class="swiper-slide__description">' . $slide['description'] . '</p>
					</div>
					<div class="order-0 order-md-1 col-md-6 offset-xl-1">
		'; ?>
						<div <?php $wv->print_render_attribute_string( 'video-wrapper' ); ?>>
							<?php
							$wv->add_render_attribute( 'image-overlay', 'class', ['elementor-custom-embed-image-overlay', 'position-relative'] );
							$lightbox_options = [
								'type' => 'video',
								'videoType' => 'youtube',
								'url' => $lightbox_url,
								'modalOptions' => [
									'id' => 'elementor-lightbox-6s75d36f',
									'entranceAnimation' => '',
									'entranceAnimation_tablet' => '',
									'entranceAnimation_mobile' => '',
									'videoAspectRatio' => 169,
								],
							];
							$wv->add_render_attribute( 'image-overlay', [
								'data-elementor-open-lightbox' => 'yes',
								'data-elementor-lightbox' => wp_json_encode( $lightbox_options ),
								'e-action-hash' => Plugin::instance()->frontend->create_action_hash( 'lightbox', $lightbox_options ),
							] );
				
							$settings['image_overlay']['url'] = $image_url;
				
							?>
						</div>
						<div <?php $wv->print_render_attribute_string( 'image-overlay' ); ?>>
							<?php 
								Group_Control_Image_Size::print_attachment_image_html( $settings, 'image_overlay' );
								echo $label_image;
								if ( !empty( $slide['video']['url'] ) || $slide['type'] !== 'image' ) :
							?>
							<div class="elementor-custom-embed-play" role="button">
								<?php Icons_Manager::render_icon( [ 'library' => 'eicons', 'value' => 'eicon-play', ], [ 'aria-hidden' => 'true' ] ); ?>
								<span class="elementor-screen-only"><?php echo esc_html__( 'Play Video', 'hotspring-lang' ); ?></span>
							</div>
								<?php endif; ?>
						</div>
						<?php
		print '
					</div>
				</div>
			</div>
		';
	}
}
