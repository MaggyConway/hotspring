<?php
// namespace ComponentsKit;

use Elementor\Core\Files\File_Types\Svg;

add_action( 'wp_enqueue_scripts', 'scripts_lib_tippy' );

function _getCardImage( $image, $link, $icon = '' ) {
    if ( !empty($icon['library']) ) {
        if($icon['library'] == 'svg'){
            $result_icon =  '
                <div class="elementor-icon">
                '.Svg::get_inline_svg( $icon['value']['id'] ).'
                </div>';
        }
        if($icon['library'] != 'svg'){
        $result_icon = '
            <div class="elementor-icon">
                <i aria-hidden="true" class="' . $icon['value'] . '"></i>
            </div>';
        }
        /*if ( !empty( $link ) ) {
            return '<div class="elementor-icon-wrapper">
            <a href="' . $link . '">
                ' . $result_icon . '
            </a>
            </div>
            ';
        }*/
        /*return '<div class="elementor-icon-wrapper">
            ' . $result_icon . '
        </div>
        ';*/
    }
    $main_image = $image . '<div class="elementor-icon-wrapper">' . $result_icon . '</div>';
	if ( !empty( $link ) ) {
		return '<a href="' . $link . '" class="card-link">' . $main_image . '</a>';
	}
	return $main_image;
}

function _getCardLink( $button_text, $link ) {
	if ( !empty( $link ) ) {
		return '<a href="' . $link . '" class="card-link">' . globalSuperscript($button_text) . '</a>';
	}
	return;
}

function _getCardHeadingLink( $heading, $link ) {
    if ( !empty( $link ) ) {
        return '<a href="' . $link . '" class="card-heading-link">' . globalSuperscript($heading) . '</a>';
    }
    return $heading;
}

function getBaseCard( $size, $type, $image_id, $heading, $description, $button_text, $link, $icon = '', $col_number = '' ){
	$image_size = 'xl-4-3';
    if( $size == 'xsmall' ) {
        $image_size = [200, 200]; //@todo fix image size xs-1-1
    }

    $image = wp_get_attachment_image( $image_id, $image_size, '', array( 'class' => 'card-img-top', 'alt'=>$heading ));

	if ( $type == 'vertical' ) {
		return '
		<div class="card ' . $size . ' ' . $type . ' ' . $col_number . '">
			' . _getCardImage( $image, $link, $icon ) . '
			<div class="card-body">
				<h4 class="card-title">' . _getCardHeadingLink( $heading, $link ) . '</h4>
				<p class="card-text">' . globalSuperscript($description) . '</p>
				' . _getCardLink( $button_text, $link ) . '
			</div>
		</div>';
	} else {
		return '
		<div class="card ' . $size . ' ' . $type . ' ' . $col_number . '">
			<div class="row g-0">
				<div class="col-12 col-lg-6">
				' . _getCardImage( $image, $link ) . '
				</div>
				<div class="col-12 col-lg-6">
					<div class="card-body">
						<h4 class="card-title">' . _getCardHeadingLink( $heading, $link ) . '</h4>
						<p class="card-text">' . globalSuperscript($description) . '</p>
						' . _getCardLink( $button_text, $link ) . '
					</div>
				</div>
			</div>
		</div>';
	}
}

function getCost( $cost ) {
    $format = '<span class="cost-widget"><span class="cost active-cost-part">%s</span><span class="cost passive-cost-part">%s</span> <i class="fa fa-info-circle" aria-hidden="true"></i></span>';
    switch ( $cost ) {
        case 1:
            return sprintf( $format, '$', '$$$$' );
            break;
        case 2:
            return sprintf( $format, '$$', '$$$' );
            break;
        case 3:
            return sprintf( $format, '$$$', '$$' );
            break;
        case 4:
            return sprintf( $format, '$$$$', '$' );
            break;
        case 5:
            return sprintf( $format, '$$$$$', '' );
            break;
        default:
            return sprintf( $format, '', '$$$$$' );
            break;
    }
}
function bazaarvoice_model_reviews_summary($id) {
    $bazaarvoice_id = get_field( 'bazaarvoice_id', $id );
    $bazaarvoice_data = get_field( 'bazaarvoice_data', $id );
    if (!empty($bazaarvoice_data)) {
        $bazaarvoice_data = json_decode( $bazaarvoice_data, true );
    }

    $url = get_permalink($id);
    $style = 'yellow';
    $link = $url . '#section--reviews';
    $no_scroll_link = FALSE;
    $total_reviews = $bazaarvoice_data['totalReviews'];
    if ($total_reviews == 0) {
        $no_scroll_link = TRUE;
        $link_text = 'Write a Review';
    } else {
        $link_text = '(' . $total_reviews . ')';
    }
    $additional_class = $no_scroll_link ? 'write-review' : 'smoothScroll';
    $star = star_rating( $bazaarvoice_data['averageRating'], $style, $link, $additional_class);

    // wp_enqueue_script( 'bazaarvoice-stars');
    // wp_localize_script( 'bazaarvoice-stars', 'bazaarvoice_js',array('product_id' => $bazaarvoice_id));
    // wp_enqueue_style( 'bazaarvoice-stars-css');

    $output = '
            <div class="bv-stars short">
                ' . $star . '
                <a href="' . $link . '" class="bv-stars--link link ' . $additional_class . '" >' . $link_text . '</a>
            </div>';
    return $output;
}

function star_rating( $rating, $style = 'orange', $link = null, $additional_class = '' ) {
    $rating_procent = ($rating / 5) * 100 ;

    $output ='
        <div class="back-stars">
          <i class="fa fa-star-o" aria-hidden="true"></i>
          <i class="fa fa-star-o" aria-hidden="true"></i>
          <i class="fa fa-star-o" aria-hidden="true"></i>
          <i class="fa fa-star-o" aria-hidden="true"></i>
          <i class="fa fa-star-o" aria-hidden="true"></i>
          <div class="front-stars" style="width: '.$rating_procent.'%">
            <i class="fa fa-star" aria-hidden="true"></i>
            <i class="fa fa-star" aria-hidden="true"></i>
            <i class="fa fa-star" aria-hidden="true"></i>
            <i class="fa fa-star" aria-hidden="true"></i>
            <i class="fa fa-star" aria-hidden="true"></i>
          </div>
        </div>';
    if ( empty($link) ) {
        return '<div class="bv-star-rating ' . esc_attr($style) . ' ">' . $output . '</div>';
    } else {
        return '<div class="bv-star-rating ' . esc_attr($style) . ' "><a href="' . $link . '" class="bv-stars--star-link ' . $additional_class . '">' . $output . '</a></div>';
    }
}

function inLineRating( $averageRating, $totalReviews, $price, $link ){

  $stars = star_rating( $averageRating, 'yellow', null, '' );


 return '<div class="in-line-rating">'.$stars .' <a href="'.$link.'#section--reviews'.'" class="reviews">('.$totalReviews.' of Reviews)</a><spna class="separator">|</span> '.getCost( strlen($price) ).'</div>' ;
}

function getSwiperNext( $type = 'default' ) {
    return '<div class="swiper-button-next"></div>';
}
function getSwiperPrev( $type = 'default' ) {
    return '<div class="swiper-button-prev"></div>';
}
function getSwiperPagination( $type = 'default' ) {
    return '<div class="swiper-pagination"></div>';
}


function getBaseModelCard($id, $clases) {
    $class_array = [];
    $model_name = get_field('model_title_with_trademark', $id);
    if (empty($model_name)) {
        $model_name = get_the_title($id);
    }
    $_model_name = get_the_title($id);

    $product_image = get_the_post_thumbnail_url($id);
    $model_three_quarter_image = get_field( 'model_three_quarter_image' , $id);
    $price_range = strlen( get_field( 'model_price', $id ) );
    $seating_capacity = get_field( 'model_total_seats' , $id);
    $field_model_collection = get_field( 'field_model_collection' , $id);
    $model_series = $field_model_collection->post_title;
    $cost = getCost( $price_range );

    //capacity
    switch ($seating_capacity) {
        case $seating_capacity >= 2 && $seating_capacity <= 3:
            $class_array[] = 'seats-2-3';
            break;
        case $seating_capacity >= 4 && $seating_capacity <= 5:
            $class_array[] = 'seats-4-5';
            break;
        case $seating_capacity >= 6 && $seating_capacity <= 8:
            $class_array[] = 'seats-6-8';
            break;
    }

    //price
    switch ($price_range) {
        case 1:
        case 2:
            $class_array[] = 'value-entry';
            break;
        case 3:
            $class = 'premium';
            if( $_model_name == 'Ravello' ) {
                $class = 'luxury';
            }
            $class_array[] = $class;
            break;
        case 4:
            $class_array[] = 'luxury';
            break;
    }

    //lounge
    $lounge = get_field( 'model_style' , $id);
    if( isset( $lounge ) && $lounge == 'Lounge' ) {
        $class_array[] = 'lounge';
    }

    //salt
    $water_care_systems = get_field( 'model_water_care' , $id );
    if( $water_care_systems == "FreshWater® Salt System Ready" ) {
        $class_array[] = 'salt-water';
    }


    $three_quarter_image = !empty( $model_three_quarter_image['id'] ) ? wp_get_attachment_image($model_three_quarter_image['id'],[276, 276],'',array('class' => 'hover model-image','alt' => $model_name) ) : '' ;

    $post_thumbnail_id = get_post_thumbnail_id( $id );
    $product_image = !empty( $post_thumbnail_id ) ? wp_get_attachment_image( $post_thumbnail_id, [276, 276], '', array( 'class' => 'model-image', 'alt'=>$model_name )) : '' ;


    $rating = bazaarvoice_model_reviews_summary( $id );
    $url = get_permalink($id);
    $output = '
    <div class="' . $clases . '  ' . implode($class_array,' ') . ' card text-center model-card">
      <a href="' . $url . '">
        ' . $product_image . '
        ' . $three_quarter_image . '
      </a>
      <div class="card-body">
        <h3><a href="' . $url . '" class="card-title">' . globalSuperscript($model_name) . '</a></h3>
        <h4 class="card-series">' . globalSuperscript($model_series) . ' Collection</h4>
        <div class="card-rating">' . $rating . '</div>
        <div class="card-capacity-cost">'.$seating_capacity.' Seats<span class="delimeter">|</span>'.$cost.'</div>
      </div>
    </div>
';
    return $output;
}

function globalSuperscript($text){
    $text = preg_replace('/(®|&reg;|™|&trade;)(?!<\/sup>)/','<sup>$1</sup>', $text);
    return $text;
}

function scripts_lib_tippy() {
  wp_enqueue_script('tippy', 'https://unpkg.com/@popperjs/core@2/dist/umd/popper.min.js', array('jquery'), null, true);
  wp_enqueue_script('tippy-bundle', 'https://unpkg.com/tippy.js@6/dist/tippy-bundle.umd.js', array('jquery'), null, true);
  wp_enqueue_style('tippy-style', 'https://unpkg.com/tippy.js@6/themes/light.css', [], '', 'all');
}