<?php
namespace ComponentsKit\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Section_Media_Carousel
 *
 * Elementor widget for Section_Media_Carousel.
 *
 * @since 1.0.0
 */
class Section_Hotspot_Model extends Widget_Base {

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
		return 'section-hotspot-model';
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
		return __( 'Section HotSpot model', 'hotspring-lang' );
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

		$this->add_control(
			'description',
			[
				'label' => __( 'Description', 'hotspring-lang' ),
				'type' => Controls_Manager::TEXTAREA,
			]
		);

		$repeater = new Repeater();
        
        $repeater->add_control(
            'group',
            [
                'label' => esc_html__( 'Group ID', 'hotspring-lang' ),
                'type' => Controls_Manager::NUMBER,
                'default' => esc_html__( '1', 'hotspring-lang' ),
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'heading',
            [
                'label' => esc_html__( 'Title', 'hotspring-lang' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $repeater->add_control(
			'description',
			[
				'label' => __( 'Description slide', 'elementor' ),
				'type' => Controls_Manager::WYSIWYG,
				'show_label' => false,
			]
		);

        $repeater->add_control(
            'main-image',
            [
                'label' => __( 'Main image', 'hotspring-lang' ),
                'type' => Controls_Manager::MEDIA,
            ]
        );

        $repeater->add_control(
            'polygon-image',
            [
                'label' => __( 'Image polygon', 'hotspring-lang' ),
                'type' => Controls_Manager::MEDIA,
            ]
        );

        $repeater->add_control(
            'massage-image',
            [
                'label' => __( 'Default image massage points', 'hotspring-lang' ),
                'type' => Controls_Manager::MEDIA,
            ]
        );

        $repeater->add_control(
            'info-text',
            [
                'label' => esc_html__( 'Information', 'hotspring-lang' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Information text', 'hotspring-lang' ),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'groups',
            [
                'label' => __( 'Grops', 'hotspring-lang' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'heading' => __( 'Slide title 1', 'hotspring-lang' ),
                        'description' => 'Description slide',
                    ],
                    [
                        'heading' => __( 'Slide title 2', 'hotspring-lang' ),
                        'description' => 'Description slide',
                    ],
                ],
                'title_field' => '{{{ group }}}: {{{ heading }}}',
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'group',
            [
                'label' => esc_html__( 'Group ID', 'hotspring-lang' ),
                'type' => Controls_Manager::NUMBER,
                'default' => esc_html__( '1', 'hotspring-lang' ),
                'label_block' => true,
            ]
        );

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
			'tab-content',
			[
				'label' => __( 'Content', 'elementor' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => __( 'Content', 'elementor' ),
				'show_label' => false,
			]
		);

		$repeater->add_control(
            'massage-image',
            [
                'label' => __( 'Image massage points', 'hotspring-lang' ),
                'type' => Controls_Manager::MEDIA,
            ]
        );

        $repeater->add_control(
            'tab-icon',
            [
                'label' => __( 'Icon tab', 'hotspring-lang' ),
                'type' => Controls_Manager::MEDIA,
            ]
        );

        $repeater->add_control(
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
					'{{WRAPPER}} {{CURRENT_ITEM}} .elementor-divider-separator' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);
        
		$repeater->add_control(
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
					'{{WRAPPER}} {{CURRENT_ITEM}} .elementor-divider-separator' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
            'points',
            [
                'label' => __( 'Points/Tabs', 'hotspring-lang' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'heading' => __( 'Tab 1', 'hotspring-lang' ),
                        'tab-content' => 'Cookie muffin gummies oat cake candy canes chupa chups. Croissant cookie carrot cake biscuit icing sweet roll sesame snaps pie bear claw. Sugar plum icing jelly carrot cake biscuit.',
                    ],
                    [
                        'heading' => __( 'Tab 2', 'hotspring-lang' ),
                        'tab-content' => 'Brownie cake chocolate gingerbread oat cake brownie cheesecake. Fruitcake pie jelly beans shortbread jujubes. Ice cream jelly beans tart marzipan shortbread.',
                    ],
                ],
                'title_field' => '{{{ group }}}: {{{ heading }}}',
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

        $output .= '
			<div class="title">
				<h2 class="text-center">' . $settings['title'] . '</h2>
			</div>
		';
        
        if ( isset( $settings['description'] ) && !empty( $settings['description'] )) {
			$output .= '
					<div class="description">
						<p class="text-center">' . $settings['description'] . '</p>
					</div>
			';
		}

		$output .= '
            <div class="swiper-container swiper section-hotspot-model">
                <div class="swiper-wrapper">
		';

		$output .= $this->getSlide();

		$output .= '
				</div>
				<div class="swiper-button-next"></div>
				<div class="swiper-button-prev"></div>
				<div class="swiper-pagination"></div>
			</div>
		';

		print $output;
		
	}

	protected function getSlide() {
		
		$output = '';
		$settings = $this->get_settings_for_display();
        $svg_plus = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="icon-jet-tool-plus-sign-desktop-tablet" x="0px" y="0px" viewBox="0 0 48 48" style="enable-background:new 0 0 48 48;" xml:space="preserve"><style type="text/css">.st0{opacity:0.5;} .st1{fill:#199EB2;} .st2{fill:#FFFFFF;}</style><g id="Group_2034" class="st0"><path id="Path_3285" class="st1" d="M48,24c0,13.3-10.7,24-24,24C10.7,48,0,37.3,0,24S10.7,0,24,0C37.3,0,48,10.7,48,24"/></g><path id="plus-solid" class="st2" d="M32.5,24.5c0,0.7-0.6,1.2-1.2,1.2h-5.5v5.5c0,0.7-0.6,1.2-1.2,1.2c-0.7,0-1.2-0.6-1.2-1.2l0,0  v-5.5h-5.5c-0.7,0-1.2-0.6-1.2-1.2c0-0.7,0.6-1.2,1.2-1.2h5.5v-5.5c0-0.7,0.6-1.2,1.2-1.2c0.7,0,1.2,0.6,1.2,1.2v5.5h5.5  C31.9,23.3,32.5,23.8,32.5,24.5C32.5,24.5,32.5,24.5,32.5,24.5z"/></svg>';

        foreach ( $settings['groups'] as $group ) {

            $group_id = $group['group'];
            $main_image = wp_get_attachment_image( $group['main-image']['id'], '', '', array( 'class' => 'swiper-slide__main-image', 'alt'=>$heading ));
            $polygon_image = wp_get_attachment_image( $group['polygon-image']['id'], '', '', array( 'class' => 'swiper-slide__polygon-image', 'style' => 'margin-top:-100%;', 'alt'=>$heading ));
            $massage_image = wp_get_attachment_image( $group['massage-image']['id'], '', '', array( 'class' => 'swiper-slide__massage-image', 'alt'=>$heading ));

            $output .= '
                <div class="swiper-slide">
                    <div class="row">
                        <div class="col-8 col-md-5">
							<div class="swiper-slide__hotspot" style="position:relative">
            ';

            $output .= $main_image . $polygon_image;

            $i = 0;

            foreach ( $settings['points'] as $tab ) {
                $i++;
                if ( $group_id == $tab['group'] ) {
                    $output .= '<div class="point-plus tab-' . $tab['group'] . '-' . $i . '" data-tab-index=".tab-' . $tab['group'] . '-' . $i . '" style="min-width:20px;position:absolute;top:' . $tab['top']['size'] . $tab['top']['unit'] . ';left:' . $tab['left']['size'] . $tab['left']['unit'] . ';">' . $svg_plus . '</div>';
                }
            }
            
            $output .= '	</div>';

			if ( isset( $group['info-text'] ) && !empty( $group['info-text'] ) ) {
                $output .= '
							<div class="swiper-slide-info">
								<i aria-hidden="true" class="fas fa-info"></i>
								<span class="swiper-slide-info__text">' . $group['info-text'] . '</span>
							</div>
            	';
            }

			$output .= '
                        </div>
                        <div class="col-4 col-md-3 massage-image">
            ';

            $output .= $massage_image;

			$i = 0;

			foreach ( $settings['points'] as $tab ) {
				$i++;
				$data_tab_index = 'tab-' . $tab['group'] . '-' . $i;
				$output .= wp_get_attachment_image( $tab['massage-image']['id'], '', '', array( 'class' => 'swiper-slide__massage-image_tab ' . $data_tab_index, 'style' => 'display:none;', 'alt'=>$heading ));
			}

            $output .= '        
                        </div>
                        <div class="col-12 col-md-4">
                            <h3 class="slide-heading">' . $group['heading'] . '</h3>
                            <p class="slide-description">' . $group['description'] . '</p>
                            <div class="tab-container">
            ';

            $output .= $this->getTab( $group_id );

            $output .= '
                            </div>
                        </div>
                    </div>
                </div>
            ';

        }

		return $output;

	}

	protected function getTab( $id ) {

		$output = '';
		$settings = $this->get_settings_for_display();
		$count = count($settings['points']);
        $i = 0;

        foreach ( $settings['points'] as $tab ) {
            $i++;
            if ( $id == $tab['group'] ) {
				$tab_icon = wp_get_attachment_image( $tab['tab-icon']['id'], '', '', array( 'class' => 'tab-head__icon', 'alt'=>$heading ));
                $output .= '
                    <div class="tab tab-item  tab-' . $tab['group'] . '-' . $i . '" data-index="' . $i . '">
                        <div class="tab-head" data-tab-index=".tab-' . $tab['group'] . '-' . $i . '">
                            ' . $tab_icon . $tab['heading'] . '
                        </div>
                        <div class="tab-content" >
                            ' . $tab['tab-content'] . '
							<div class="tab-footer">';

							if ( $count >= $i && $i !== 1 ) {
								$pi = $i - 1;
								$output .= '<a href="" class="tab-btn-nav tab-prev"  data-tab-index=".tab-' . $tab['group'] . '-' . $pi . '">Previous Jet</a>';
							}
							
							if ( $i <= $count - 1 ) {
								$ni = $i + 1;
								$output .= '<a href="" class="tab-btn-nav tab-next" data-tab-index=".tab-' . $tab['group'] . '-' . $ni . '">Next Jet</a>';
							}
								
				$output .= '
							</div>
                        </div>
                    </div>
                ';
            }
        }
		
		return $output;

	}

}
