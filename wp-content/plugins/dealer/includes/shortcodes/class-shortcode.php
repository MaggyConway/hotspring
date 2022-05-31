<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;  // if direct access
}

class class_dealer_shortcode {

	public function __construct() {
		add_shortcode( 'dealer-json-ld-store', [ $this, 'dealer_json_ld_store' ] );
		add_shortcode( 'dealer-json-ld-breadcrumb', [ $this, 'dealer_json_ld_breadcrumb' ] );
		add_shortcode( 'dealer-json-ld-local-business', [ $this, 'dealer_json_ld_local_business'] );
	}

	public function dealer_json_ld_store( $attributes, $content = null ) {
		$id = isset( $attributes['id'] ) ? $attributes['id'] : get_the_ID();

		$postalCode      = get_field( 'dealership_zip', $id );
		$addressLocality = get_field( 'dealership_city', $id );
		$streetAddress   = get_field( 'dealership_address_1', $id );
		$telephone       = get_field( 'dealer_phone', $id );
		$name            = get_field( 'dealership_name', $id );
		$country         = get_field( 'dealership_country', $id );
		$location        = get_field( 'dealership_coordinates', $id );
		$latitude        = $location[ 'lat' ];
		$longitude       = $location[ 'lng' ];
		$url             = get_field( 'dealer_website', $id );
		$email           = get_field( 'dealer_email', $id );

		$store_url = get_permalink( $id );

		$address = (object) array(
			'@type' => 'PostalAddress',
		);

		if ( ! empty( $postalCode ) ) {
			$address->postalCode = $postalCode;
		}
		if ( ! empty( $addressLocality ) ) {
			$address->addressLocality = $addressLocality;
		}
		if ( ! empty( $streetAddress ) ) {
			$address->streetAddress = $streetAddress;
		}

		$custom_logo_id = get_theme_mod( 'custom_logo' );
		$logo           = wp_get_attachment_image_src( $custom_logo_id, 'full' );

		$dealer = (object) array(
			'@context'        => 'http://schema.org',
			'@type'           => 'Store',
			'@id'             => $store_url,
			'name'            => $name,
			'url'             => $store_url,
			'address'         => $address,
			'paymentAccepted' => 'Cash, Credit Card',
			'priceRange'      => '$$$',
			'image'           => esc_url( $logo[0] ),
		);

		if ( ! empty( $url ) ) {
			$dealer->url = $url;
		} else {
			$dealer->url = get_permalink( $id );
		}
		if ( ! empty( $email ) ) {
			$dealer->email = $email;
		}
		if ( ! empty( $telephone ) ) {
			$dealer->telephone = $telephone;
		}

		if ( ! empty( $name ) ) {
			$dealer->name = $name;
		}

		// openingHours
		if ( ! empty( get_field( 'edl_dealer_hours', $id ) ) ) {
			$dealer->openingHours = get_field( 'edl_dealer_hours', $id );
		}

		// geo
		if ( ! empty( $latitude ) && ! empty( $longitude ) ) {
			$dealer->geo = (object) array(
				'@type'     => 'GeoCoordinates',
				'latitude'  => $latitude,
				'longitude' => $longitude,
			);
		}
		return '<script type="application/ld+json">' . json_encode( $dealer ) . '</script>';
	}

	public function dealer_json_ld_local_business( $attributes ) {
		$id = isset( $attributes['id'] ) ? $attributes['id'] : get_the_ID();

		$postalCode      = get_field( 'dealership_zip', $id );
		$addressLocality = get_field( 'dealership_city', $id );
		$streetAddress   = get_field( 'dealership_address_1', $id );
		$telephone       = get_field( 'dealer_phone', $id );
		$name            = get_field( 'dealership_name', $id );
		$country         = get_field( 'dealership_country', $id );
		$location        = get_field( 'dealership_coordinates', $id );
		$latitude        = $location[ 'lat' ];
		$longitude       = $location[ 'lng' ];
		$url             = get_field( 'dealer_website', $id );
		$email           = get_field( 'dealer_email', $id );


		$output_later = true;
		$address      = (object) array(
			'@type' => 'PostalAddress',
		);

		if ( ! empty( $postalCode ) ) {
			$address->postalCode = $postalCode;
		}
		if ( ! empty( $addressLocality ) ) {
			$address->addressLocality = $addressLocality;
		}

		if ( ! empty( $streetAddress ) ) {
			$address->streetAddress = $streetAddress;
		}
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		$logo           = wp_get_attachment_image_src( $custom_logo_id, 'full' );

		$organization = (object) array(
			'@context'        => 'http://schema.org',
			'@type'           => 'LocalBusiness',
			'address'         => $address,
			"paymentAccepted" => 'Cash, Credit Card',
			"priceRange"      => '$$$',
			"image"           => esc_url( $logo[0] ),
		);

		if ( ! empty( $url ) ) {
			$organization->url = $url;
		}
		if ( ! empty( $email ) ) {
			$organization->email = $email;
		}
		if ( ! empty( $telephone ) ) {
			$organization->telephone = $telephone;
		}

		if ( ! empty( $name ) ) {
			$organization->name = $name;
		}

		//membership_and_awards
		if ( ! empty( get_field( 'membership_and_awards', $id ) ) ) {
			$organization->award = strip_tags( get_field( 'membership_and_awards', $id ) );
		}
		// openingHours
		if ( ! empty( get_field( 'edl_dealer_hours', $id ) ) ) {
			$organization->openingHours = strip_tags( get_field( 'edl_dealer_hours', $id ) );
		}
		//areas_we_serve
		if ( ! empty( get_field( 'areas_we_serve', $id ) ) ) {
			$organization->areaServed = strip_tags( get_field( 'areas_we_serve', $id ) );
		}
		//about_description
		if ( ! empty( get_field( 'about_description', $id ) ) ) {
			$organization->knowsAbout = strip_tags( get_field( 'about_description', $id ) );
		}
		//"paymentAccepted":"Cash, Credit Card",
		if ( ! empty( $latitude ) && ! empty( $longitude ) ) {
			$organization->geo = (object) array(
				"@type"     => "GeoCoordinates",
				"latitude"  => $latitude,
				"longitude" => $longitude
			);
		}

		return '<script type="application/ld+json">' . json_encode( $organization ) . '</script>';
	}


	public function dealer_json_ld_breadcrumb( $attributes, $content = null ) {
		$url   = isset( $attributes['url'] ) ? $attributes['url'] : null;
		$title = isset( $attributes['title'] ) ? $attributes['title'] : null;

		$itemList[] = (object) array(
			'@type'    => 'ListItem',
			'position' => 1,
			'item'     => (object) array(
				'@id'  => home_url(),
				'name' => 'Home',
			),
		);
		$itemList[] = (object) array(
			'@type'    => 'ListItem',
			'position' => 2,
			'item'     => (object) array(
				'@id'  => home_url( '/find-a-dealer' ),
				'name' => 'Dealers',
			),
		);
		$breadcrumb = (object) array(
			'@context'        => 'http://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $itemList,
		);
		return '<script type="application/ld+json">' . json_encode( $breadcrumb ) . '</script>';
	}

}
new class_dealer_shortcode();