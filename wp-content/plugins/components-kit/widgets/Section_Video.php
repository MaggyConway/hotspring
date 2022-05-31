<?php
namespace ComponentsKit\Widgets;

use Elementor\Embed;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Utils;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Widget_Video;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Section_Video
 *
 * Elementor widget for Section_Video.
 *
 * @since 1.0.0
 */
class Section_Video extends Widget_Base {

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
		return 'section-video';
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
		return __( 'Video Section', 'hotspring-lang' );
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
            'video_type',
            [
                'label' => esc_html__( 'Source', 'hotspring-lang' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'youtube',
                'options' => [
                    'youtube' => esc_html__( 'YouTube', 'hotspring-lang' ),
                    'vimeo' => esc_html__( 'Vimeo', 'hotspring-lang' ),
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'youtube_url',
            [
                'label' => esc_html__( 'Link', 'hotspring-lang' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                    'categories' => [
                        TagsModule::POST_META_CATEGORY,
                        TagsModule::URL_CATEGORY,
                    ],
                ],
                'placeholder' => esc_html__( 'Enter your URL', 'hotspring-lang' ) . ' (YouTube)',
                'default' => '',
                'label_block' => true,
                'condition' => [
                    'video_type' => 'youtube',
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'vimeo_url',
            [
                'label' => esc_html__( 'Link', 'hotspring-lang' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                    'categories' => [
                        TagsModule::POST_META_CATEGORY,
                        TagsModule::URL_CATEGORY,
                    ],
                ],
                'placeholder' => esc_html__( 'Enter your URL', 'hotspring-lang' ) . ' (Vimeo)',
                'default' => '',
                'label_block' => true,
                'condition' => [
                    'video_type' => 'vimeo',
                ],
            ]
        );

        $this->add_control(
            'image-overlay',
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

        /*$this->add_control(
            'youtube_link',
            [
                'label' => __( 'Youtube link', 'hotspring-lang' ),
                'type' => Controls_Manager::URL,
            ]
        );*/

        $this->add_control(
            'video_options',
            [
                'label' => esc_html__( 'Video Options', 'hotspring-lang' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'mute',
            [
                'label' => esc_html__( 'Mute', 'hotspring-lang' ),
                'type' => Controls_Manager::SWITCHER,
                'frontend_available' => true,
                'condition' => [
                    'video_type' => 'youtube',
                ],
            ]
        );

        $this->add_control(
            'controls',
            [
                'label' => esc_html__( 'Player Controls', 'hotspring-lang' ),
                'type' => Controls_Manager::SWITCHER,
                'label_off' => esc_html__( 'Hide', 'hotspring-lang' ),
                'label_on' => esc_html__( 'Show', 'hotspring-lang' ),
                'default' => 'yes',
                'condition' => [
                    'video_type' => 'youtube',
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'modestbranding',
            [
                'label' => esc_html__( 'Modest Branding', 'hotspring-lang' ),
                'type' => Controls_Manager::SWITCHER,
                'condition' => [
                    'video_type' => [ 'youtube' ],
                    'controls' => 'yes',
                ],
                'frontend_available' => true,

            ]
        );

        // YouTube.
        $this->add_control(
            'privacy',
            [
                'label' => esc_html__( 'Privacy Mode', 'hotspring-lang' ),
                'type' => Controls_Manager::SWITCHER,
                'description' => esc_html__( 'When you turn on privacy mode, YouTube won\'t store information about visitors on your website unless they play the video.', 'hotspring-lang' ),
                'condition' => [
                    'video_type' => 'youtube',
                ],
                'frontend_available' => true,
            ]
        );

        $this->add_control(
            'lazy_load',
            [
                'label' => esc_html__( 'Lazy Load', 'hotspring-lang' ),
                'type' => Controls_Manager::SWITCHER,
                'frontend_available' => true,
                'condition' => [
                    'video_type' => 'youtube',
                ],
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
            'view',
            [
                'label' => esc_html__( 'View', 'hotspring-lang' ),
                'type' => Controls_Manager::HIDDEN,
                'default' => 'youtube',
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Video', 'hotspring-lang' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
            'aspect_ratio',
            [
                'label' => esc_html__( 'Aspect Ratio', 'hotspring-lang' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '169' => '16:9',
                    '219' => '21:9',
                    '43' => '4:3',
                    '32' => '3:2',
                    '11' => '1:1',
                    '916' => '9:16',
                ],
                'default' => '169',
                'prefix_class' => 'elementor-aspect-ratio-',
                'frontend_available' => true,
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

        $video_url = $settings[ $settings['video_type'] . '_url' ];


        $reverse = '';
        if( $settings['image_position'] == 'right' ) {
            $reverse = 'flex-column-reverse';
        }

        $open_tag_wrap = '';
        $close_tag_wrap = '';
        $align_items_center = 'align-items-center flex-xl-row ';
        if( $settings['use_wrap'] == 'yes' ) {
            $open_tag_wrap = '<div class="col-xl-10 offset-xl-1"><div class="row ' . $align_items_center . $reverse . '">';
            $close_tag_wrap = '</div></div>';
            $align_items_center = '';
            $reverse = '';
        }

        echo '<div class="container">';
            echo '<div class="row ' . $align_items_center . $reverse . '">';
            echo $open_tag_wrap;
                //echo showVideoSection( $settings['image-overlay']['id'], $settings['image-overlay']['alt'], $settings['title'], $settings['description'], $settings['link']['url'], $settings['text_link'], $settings['image_position'], $settings['youtube_link'] );

                $image_url = wp_get_attachment_url( $settings['image-overlay']['id'] );

                $block_link = '';
                if( isset( $settings['text_link'] ) && !empty( $settings['text_link'] ) ) {
                    $block_link = '<div><a href="' . $settings['link']['url'] . '">' . $settings['text_link'] . '</a></div>';
                }

                $wv = new Widget_Video;

                $wv->add_render_attribute( 'video-wrapper', 'class', 'elementor-wrapper' );
                $wv->add_render_attribute( 'video-wrapper', 'class', 'elementor-open-lightbox' );

                $title = globalSuperscript($settings['title']);
                $description = globalSuperscript($settings['description']);

                if($settings['video_type'] == 'youtube') {
                    $embed_params['controls'] = $settings['controls'];
                    $embed_params['mute'] = $settings['mute'];
                    $embed_params['modestbranding'] = $settings['modestbranding'];

                    $embed_options['privacy'] = $settings['privacy'];
                    $embed_options['lazy_load'] = $settings['lazy_load'];
                } else {
                    $embed_params['color'] = '';
                    $embed_params['autopause'] = 0;
                    $embed_params['loop'] = 0;
                    $embed_params['muted'] = 0;
                    $embed_params['title'] = 0;
                    $embed_params['portrait'] = 0;
                    $embed_params['byline'] = 0;

                    $embed_options['start'] = '';
                    $embed_options['lazy_load'] = '';
                }

                $lightbox_url = Embed::get_embed_url( $video_url, $embed_params, $embed_options );
                ?>

                <div <?php $wv->print_render_attribute_string( 'video-wrapper' ); ?>>

                    <?php
                    $wv->add_render_attribute( 'image-overlay', 'class', ['elementor-custom-embed-image-overlay', 'position-relative'] );


                    $lightbox_options = [
                        'type' => 'video',
                        'videoType' => $settings['video_type'],
                        'url' => $lightbox_url,
                        'modalOptions' => [
                            'id' => 'elementor-lightbox-' . $this->get_id(),
                            'entranceAnimation' => '',
                            'entranceAnimation_tablet' => '',
                            'entranceAnimation_mobile' => '',
                            'videoAspectRatio' => $settings['aspect_ratio'],
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

                <?php
                if( $settings['image_position'] == 'left' ) {
                    ?>

                    <div class="col-xl-7 col-sm-12">
                        <div <?php $wv->print_render_attribute_string( 'image-overlay' ); ?>>
                            <?php Group_Control_Image_Size::print_attachment_image_html( $settings, 'image_overlay' ); ?>
                            <div class="elementor-custom-embed-play" role="button">
                                <?php
                                Icons_Manager::render_icon( [
                                    'library' => 'eicons',
                                    'value' => 'eicon-play',
                                ], [ 'aria-hidden' => 'true' ] );
                                ?>
                                <span class="elementor-screen-only"><?php echo esc_html__( 'Play Video', 'hotspring-lang' ); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-sm-12 content_wrapper offset-xl-1 g-0">
                        <h2 class="section-video__title"><?php echo $title ?></h2>
                        <div class="section-video__description"><?php echo $description ?></div>
                        <?php echo $block_link ?>
                    </div>

                    <?php
                } else {
                    ?>

                    <div class="col-xl-4 col-sm-12 content_wrapper">
                        <h2 class="section-video__title"><?php echo $title ?></h2>
                        <div class="section-video__description"><?php echo $description ?></div>
                        <?php echo $block_link ?>
                    </div>

                    <div class="col-xl-7 col-sm-12 offset-xl-1 g-0">
                        <div <?php $wv->print_render_attribute_string( 'image-overlay' ); ?>>
                            <?php Group_Control_Image_Size::print_attachment_image_html( $settings, 'image_overlay' ); ?>
                            <div class="elementor-custom-embed-play" role="button">
                                <?php
                                Icons_Manager::render_icon( [
                                    'library' => 'eicons',
                                    'value' => 'eicon-play',
                                ], [ 'aria-hidden' => 'true' ] );
                                ?>
                                <span class="elementor-screen-only"><?php echo esc_html__( 'Play Video', 'hotspring-lang' ); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            echo $close_tag_wrap;
            echo '</div>';
        echo '</div>';
	}
}
