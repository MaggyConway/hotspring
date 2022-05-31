<?php

if ( ! defined('ABSPATH')) exit;  // if direct access
// $wpdp = $GLOBALS['wpdp'];
// // $result = $wpdp->get->dealers_by_state();
//
// $result = $wpdp->get->dealers_by_csc($city, $state, $country);
//

foreach ($result as $key => $post) {
  $coordinates = get_post_meta( $post->ID, 'dealership_coordinates', true );
}


?>
<style>
.acf-map{
  position: relative;
  overflow: hidden;
  height: 500px;
}
.acf-map .marker{
  display: none;
}
</style>

<div class="dp-map">
  <div class="acf-map">
    <?php
    if(isset($result->post_title)){
      $result = array(0 => $result);
    }
    foreach ($result as $key => $post) {
      $coordinates = get_post_meta( $post->ID, 'dealership_coordinates', true );
      $address = get_post_meta( $post->ID, 'dealership_address_1', true );
      $post->guid = rtrim(get_permalink($post->ID), '/');
      ?>
      <div class="marker" data-lat="<?php echo $coordinates['lat']; ?>" data-lng="<?php echo $coordinates['lng'];?>" data-title="<?php echo $post->post_title; ?>">
        <h4><?php echo $post->post_title; ?><h4><?php
          echo '<p>' . $address . '</p>';
          echo '<a href="'.$post->guid.'">See more</a>';
        ?>
      </div>
      <?php
    }
    ?>
  </div>
</div>
<?php
//  print_r($city);
//  print_r($state);
// print_r($result);
//
// do_action('dp_city_header', $city,$result);
// do_action('dp_city_map', $city,$result);
// do_action('dp_city_content',$result, $city, $state, $country);
?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo get_option('dealer_gma_key_web');?>"></script>
<script type="text/javascript">
(function($) {

/*
*  new_map
*
*  This function will render a Google Map onto the selected jQuery element
*
*  @type	function
*  @date	8/11/2013
*  @since	4.3.0
*
*  @param	$el (jQuery element)
*  @return	n/a
*/

function new_map( $el ) {
	var $markers = $el.find('.marker');

	var args = {
		zoom		: 16,
		center		: new google.maps.LatLng(0, 0),
		mapTypeId	: google.maps.MapTypeId.ROADMAP,
    scrollwheel:  false
	};

	// create map
	var map = new google.maps.Map( $el[0], args);


	// add a markers reference
	map.markers = [];


	// add markers
	$markers.each(function(){
    add_marker( $(this), map );
  });


	// center map
	center_map( map );


	// return
	return map;

}

/*
*  add_marker
*
*  This function will add a marker to the selected Google Map
*
*  @type	function
*  @date	8/11/2013
*  @since	4.3.0
*
*  @param	$marker (jQuery element)
*  @param	map (Google Map object)
*  @return	n/a
*/

function add_marker( $marker, map ) {

	// var
	var latlng = new google.maps.LatLng( $marker.attr('data-lat'), $marker.attr('data-lng') );

	// create marker
	var marker = new google.maps.Marker({
		position: latlng,
		map: map,
		title: $marker.attr('data-title')
	});

	// add to array
	map.markers.push( marker );

	// if marker contains HTML, add it to an infoWindow
	if( $marker.html() )
	{
		// create info window
		var infowindow = new google.maps.InfoWindow({
			content		: $marker.html()
		});

		// show info window when marker is clicked
		google.maps.event.addListener(marker, 'click', function() {

			infowindow.open( map, marker );

		});
	}

}

/*
*  center_map
*
*  This function will center the map, showing all markers attached to this map
*
*  @type	function
*  @date	8/11/2013
*  @since	4.3.0
*
*  @param	map (Google Map object)
*  @return	n/a
*/

function center_map( map ) {

	// vars
	var bounds = new google.maps.LatLngBounds();

	// loop through all markers and create bounds
	$.each( map.markers, function( i, marker ){
		var latlng = new google.maps.LatLng( marker.position.lat(), marker.position.lng() );
		bounds.extend( latlng );
	});

	// only 1 marker?
	if( map.markers.length == 1 ) {
		// set center of map
		map.setCenter( bounds.getCenter() );
		map.setZoom( 17 );
    	map.disableScrollWheelZoom();
	}
	else {
		// fit to bounds
		map.fitBounds( bounds );
	}

}

/*
*  document ready
*
*  This function will render each map when the document is ready (page has loaded)
*
*  @type	function
*  @date	8/11/2013
*  @since	5.0.0
*
*  @param	n/a
*  @return	n/a
*/
// global var
var map = null;

$(document).ready(function(){

	$('.acf-map').each(function(){

		// create map
		map = new_map( $(this) );

	});

});

})(jQuery);
</script>
