<?php
namespace ComponentsKit\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Section_Color_Visualizer
 *
 * Elementor widget for Section_Color_Visualizer.
 *
 * @since 1.0.0
 */
class Section_Color_Visualizer extends Widget_Base {

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
		return 'section-color-visualizer';
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
		return __( 'Section Color Visualizer', 'hotspring-lang' );
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
		return [ 'components-kit-elementor', 'jquery-swatch' ];
	}

    /**
     * Retrieve the list of styles the widget depended on.
     *
     * Used to set styles dependencies required to run the widget.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return array Widget styles dependencies.
     */
    public function get_style_depends() {
        return [ 'components-kit-elementor-style' ];
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

		echo '<div class="title">';
		    echo '<h2>'.$settings['title'].'</h2>';
		echo '</div>';

        $id = get_the_ID();
        $products_colors = get_field( 'model_shell_and_cabin_color', $id );
        $post            = get_post( $id );

        $output          = '
      <div class="container">
        <div class="row">
          
          <div class="col-lg-7 col-sm-12">
            <div class="color-selector-spa" data-model="' . $post->post_name . '">
              <div class="shells">
                ' . $this->getShellImages( $products_colors ) . '
              </div>
              <div class="cabinets">
                ' . $this->getCabinetImages( $products_colors ) . '
              </div>
              <!--<p class="text-center color-selector-disclaimer"><i><b>(Actual colors and product may differ from on screen representation. Please see your local dealer to verify.)</b></i></p>-->
            </div>
          </div>
          <div class="col-lg-5 col-sm-12">
            <div class="row color-selector-picker">
              <div class="cabinet color-selector-column">
                <h3>Cabinet</h3>
                ' . $this->getCabinetColorImages( $products_colors ) . '
                <h4 class="text-center"></h4>
              </div>
              <div class="shell color-selector-column">
                <h3>Shell</h3>
                ' . $this->getShellColorImages( $products_colors ) . '
                <h4 class="text-center"></h4>
              </div>
            </div>
          </div>
        </div>
      </div>';
        echo $output;
	}

    public function getShellImages( $data ) {
        $output = '';
        $shell  = [];
        foreach ( $data as $cabinet ) {
            foreach ( $cabinet['shell_colors'] as $color ) {
                $shell[ $color['shell_color_name'] ] = $color['shell_image'];
            }
        }
        foreach ( $shell as $key => $value ) {
            $output .= '<img data-no-lazy="1" src="' . $value . '" class="img-responsive" data-shell="' . $key . '" style="display: none;" />';
        }
        return $output;
    }

    public function getCabinetImages( $data ) {
        $output = '';
        foreach ( $data as $key => $value ) {
            $output .= '<img data-no-lazy="1" src="' . $value['cabinet_image'] . '" class="img-responsive" data-cabinet="' . $value['cabinet_color_name'] . '" style="display: none;" />';
        }
        return $output;
    }

    public function getCabinetColorImages( $data ) {
        $output  = '';
        $shell   = [];
        $output .= '<ul class="list-inline d-flex flex-column">';
        foreach ( $data as $cabinet ) {
            $output .= '<li data-cabinet="' . $cabinet['cabinet_color_name'] . '" class="swatch selected list-inline-item mb-1"><img data-no-lazy="1" src="' . $cabinet['cabinet_color_image'] . '" class="rounded-square"/>' . $cabinet['cabinet_color_name'] . '</li>';
        }
        $output .= '</ul>';
        return $output;
    }

    public function getShellColorImages( $data ) {
        $output = '';
        $shell  = [];
        foreach ( $data as $cabinet ) {
            $output .= '<ul class="list-inline shell-colors" data-shellfor="' . $cabinet['cabinet_color_name'] . '">';
            foreach ( $cabinet['shell_colors'] as $color ) {
                $output .= '<li data-shell="' . $color['shell_color_name'] . '" class="swatch selected list-inline-item mb-1"><img data-no-lazy="1" src="' . $color['shell_color_image'] . '" class="rounded-square" />' . $color['shell_color_name'] . '</li>';
            }
            $output .= '</ul>';
        }
        return $output;
    }

}
