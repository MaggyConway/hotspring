<?php
namespace ComponentsKit\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Cards_Carousel
 *
 * Elementor widget for Cards_Carousel.
 *
 * @since 1.0.0
 */
class Cards_Carousel extends Widget_Base {

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
		return 'cards-сarousel';
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
		return __( 'Cards Carousel', 'hotspring-lang' );
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
					'1' => esc_html__( '1 Col', 'elementor-pro' ),
					'2' => esc_html__( '2 Col', 'elementor-pro' ),
					'3' => esc_html__( '3 Col', 'elementor-pro' ),
					'4' => esc_html__( '4 Col', 'elementor-pro' ),
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

		$repeater = new Repeater();
		$repeater->add_control(
			'image',
			[
				'label' => __( 'Image', 'elementor-custom' ),
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

		/*$col_class = 'col-md-6';
        if ( $settings['col_number'] == '1-col' ) {
            $col_class = 'col-md-12';
        } elseif ($settings['col_number'] == '2-col') {
            $col_class = 'col-md-6';
        } elseif ($settings['col_number'] == '3-col') {
            $col_class = 'col-md-4';
        } elseif ($settings['col_number'] == '4-col') {
            $col_class = 'col-md-3';
        }*/

		// print '
		// <div class="container">
		// 	<div class="row">';
		// 	foreach ( $settings['cards'] as $card ) {
				// print '
				// <div class="' . $col_class . ' mb-3">' .
				// 	getBaseCard($settings['card_size'], $settings['card_type'], $card['image']['id'], $card['heading'], $card['description'], $card['button_text'] , $card['link']['url'] )
				// 	. '
				// </div>';
		// 	}
		// print '
		// 	</div>
		// </div>
		// ';

        ?>
        <div class="container">
            <div class="swiper-object">
                <div class="swiper swiper-container swiper-product cards-сarousel">
                    <div class="swiper-wrapper align-items-stretch" data-slide="<?php echo $settings['col_number'] ?>">
                        <?php
                        foreach ( $settings['cards'] as $card ) {
                            print $this->getSlide( $settings, $card );
                        }
                        ?>
                    </div>
                </div>
                <?php
                print getSwiperNext();
                print getSwiperPrev();
                print getSwiperPagination();
                ?>
            </div>
        </div>

        <?php
	}

	protected function getSlide( $settings, $card ) {

		return '
		<div class="swiper-slide">'
		    . getBaseCard($settings['card_size'], $settings['card_type'], $card['image']['id'], $card['heading'], $card['description'], $card['button_text'] , $card['link']['url'] )
		. '</div>';
	}

}
