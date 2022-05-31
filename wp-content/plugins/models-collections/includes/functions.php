<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

// Return array of model menu data.
function getMenuModelDataById( $id ) {

    $model_image_src = get_field( 'model_info_image', $id );
    $model_image = wp_get_attachment_image_url( $model_image_src['id'], 'md-4-3' );
    $model_total_seats = get_field( 'model_total_seats', $id );
    $model_style = get_field( 'model_style', $id );
    $model_price = get_field( 'model_price', $id );
    $model_dimensions = get_field( 'model_dimensions', $id );
    $model_voltage = get_field( 'model_voltage', $id );
    $model_water_care = get_field( 'model_water_care', $id );

    $bazaarvoice_data  = get_field( 'bazaarvoice_data', $id );
	$bazaarvoice_data  = json_decode( $bazaarvoice_data, true );
    return [
        'id' => $id,
        'link' => get_permalink($id),
        'image' => !empty($model_image) ? $model_image : 'https://via.placeholder.com/336x250?text=4x3',
        'model_total_seats' => ($model_total_seats) ? $model_total_seats . '  Seats' : 'X Seats',
        'style' => ($model_style) ? $model_style : 'Default style',
        'price' => ($model_price) ? $model_price : '$$$$',
        'dimensions' => ($model_dimensions) ? $model_dimensions : 'L” x W” x D',
        'voltage' => ($model_voltage) ? $model_voltage : '115V or 230V',
        'water_care' => ($model_water_care) ? $model_water_care : 'Freshwater Salt System Ready',
        'total_reviews' => $bazaarvoice_data['totalReviews'],
        'average_rating' => $bazaarvoice_data['averageRating'],
    ];
}

// Return array of collection menu data.
function getMenuCollectionDataById( $id ) {
    $collection_image_src = get_field( 'collection_info_image', $id );
    $collection_image = wp_get_attachment_image_url( $collection_image_src['id'], 'md-4-3' );
    $collection_title = get_field( 'collection_info_title', $id );
    $collection_text = get_field( 'collection_info_text', $id );
    return [
        'link' => get_permalink($id),
        'image' => ($collection_image) ? $collection_image : 'https://via.placeholder.com/336x250?text=4x3',
        'title' => ($collection_title) ? $collection_title : 'Prism H4 headline plus italics Collection Tagline min-max for place holder text in presentation.',
        'text' => ($collection_text) ? $collection_text : '
        <ul>
            <li>Lorem ipsum dolor sit amet</li>
            <li>Aenean commodo ligula eget dolor</li>
            <li>Cum sociis natoque penatibus</li>
            <li>Donec quam felis, ultricies nec, pelle</li>
        </ul>
        ',
    ];
}