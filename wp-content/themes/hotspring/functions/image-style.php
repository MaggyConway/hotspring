<?php

class ImageRatio {

  private $ratio;

  function __construct($ratioW = 4, $ratioH = 3) {
    $this->ratio = array($ratioW, $ratioH);
  }

  function getLargestSize($imgW, $imgH) {
    $inverse = false;
    // let's try to keep width and calculate new height
    $newSize = round(($this->ratio[1] * $imgW) / $this->ratio[0]);
    if ($newSize > $imgH) {
      $inverse = true;
      // if the calculated height is bigger than actual size
      // let's keep current height and calculate new width
      $newSize = round(($this->ratio[0] * $imgH) / $this->ratio[1]);
    }

    return $inverse ? array( $newSize, $imgH ) : array( $imgW, $newSize );
  }

  function getCalcResize($w) {
      return [$w,round($w * $this->ratio[1]/$this->ratio[0])];
  }
}

add_filter( 'intermediate_image_sizes_advanced', function( $sizes, $metadata ) {


if (! empty( $metadata['width'] ) && ! empty( $metadata['height'] ) ) {

    $image_sizes = [ '4-3', '2-1', '1-1', '16-9', '4-5' ];

    foreach($image_sizes as $image_size) {

        $num = explode( '-', $image_size );

        $ratio = new ImageRatio( $num[0], $num[1] );
        list($width, $height) = $ratio->getLargestSize(
            $metadata['width'],
            $metadata['height']
        );
        // let's add our custom size
        $sizes['xxl-' . $image_size] = array(
            'width'  => $width,
            'height' => $height,
            'crop'   => true
        );

        list($width, $height) = $ratio->getCalcResize(1200);
        $sizes['xl-' . $image_size] = array(
            'width'  => $width,
            'height' => $height,
            'crop'   => true
        );
        list($width, $height) = $ratio->getCalcResize(992);
        $sizes['lg-' . $image_size] = array(
            'width'  => $width,
            'height' => $height,
            'crop'   => true
        );
        list($width, $height) = $ratio->getCalcResize(768);
        $sizes['md-' . $image_size] = array(
            'width'  => $width,
            'height' => $height,
            'crop'   => true
        );
        list($width, $height) = $ratio->getCalcResize(576);
        $sizes['sm-' . $image_size] = array(
            'width'  => $width,
            'height' => $height,
            'crop'   => true
        );

    }

    /*$ratio = new ImageRatio( 4, 3 );
    list($width, $height) = $ratio->getLargestSize(
        $metadata['width'],
        $metadata['height']
    );
    // let's add our custom size
    $sizes['xxl-4-3'] = array(
        'width'  => $width,
        'height' => $height,
        'crop'   => true
    );

    list($width, $height) = $ratio->getCalcResize(1200);
    $sizes['xl-4-3'] = array(
        'width'  => $width,
        'height' => $height,
        'crop'   => true
    );
    list($width, $height) = $ratio->getCalcResize(992);
    $sizes['lg-4-3'] = array(
        'width'  => $width,
        'height' => $height,
        'crop'   => true
    );
    list($width, $height) = $ratio->getCalcResize(768);
    $sizes['md-4-3'] = array(
        'width'  => $width,
        'height' => $height,
        'crop'   => true
    );
    list($width, $height) = $ratio->getCalcResize(576);
    $sizes['sm-4-3'] = array(
        'width'  => $width,
        'height' => $height,
        'crop'   => true
    );


   // calculate the max width and height for the ratio
   $ratio = new ImageRatio( 2, 1 );
   list($width, $height) = $ratio->getLargestSize(
      $metadata['width'],
      $metadata['height']
   );
   // let's add our custom size
   $sizes['xxl-2-1'] = array(
     'width'  => $width,
     'height' => $height,
     'crop'   => true
   );

   list($width, $height) = $ratio->getCalcResize(1200);
   $sizes['xl-2-1'] = array(
       'width'  => $width,
       'height' => $height,
       'crop'   => true
   );
   list($width, $height) = $ratio->getCalcResize(992);
   $sizes['lg-2-1'] = array(
       'width'  => $width,
       'height' => $height,
       'crop'   => true
   );
   list($width, $height) = $ratio->getCalcResize(768);
   $sizes['md-2-1'] = array(
       'width'  => $width,
       'height' => $height,
       'crop'   => true
   );
   list($width, $height) = $ratio->getCalcResize(576);
   $sizes['sm-2-1'] = array(
       'width'  => $width,
       'height' => $height,
       'crop'   => true
   );

   // calculate the max width and height for the ratio
   $ratio = new ImageRatio( 1, 1 );
   list($width, $height) = $ratio->getLargestSize(
      $metadata['width'],
      $metadata['height']
   );
   // let's add our custom size
   $sizes['xxl-1-1'] = array(
     'width'  => $width,
     'height' => $height,
     'crop'   => true
   );

   list($width, $height) = $ratio->getCalcResize(1200);
   $sizes['xl-1-1'] = array(
       'width'  => $width,
       'height' => $height,
       'crop'   => true
   );
   list($width, $height) = $ratio->getCalcResize(992);
   $sizes['lg-1-1'] = array(
       'width'  => $width,
       'height' => $height,
       'crop'   => true
   );
   list($width, $height) = $ratio->getCalcResize(768);
   $sizes['md-1-1'] = array(
       'width'  => $width,
       'height' => $height,
       'crop'   => true
   );
   list($width, $height) = $ratio->getCalcResize(576);
   $sizes['sm-1-1'] = array(
       'width'  => $width,
       'height' => $height,
       'crop'   => true
   );

   // calculate the max width and height for the ratio
   $ratio = new ImageRatio( 16, 9 );
   list($width, $height) = $ratio->getLargestSize(
      $metadata['width'],
      $metadata['height']
   );
   // let's add our custom size
   $sizes['xxl-16-9'] = array(
     'width'  => $width,
     'height' => $height,
     'crop'   => true
   );

   list($width, $height) = $ratio->getCalcResize(1200);
   $sizes['xl-16-9'] = array(
       'width'  => $width,
       'height' => $height,
       'crop'   => true
   );
   list($width, $height) = $ratio->getCalcResize(992);
   $sizes['lg-16-9'] = array(
       'width'  => $width,
       'height' => $height,
       'crop'   => true
   );
   list($width, $height) = $ratio->getCalcResize(768);
   $sizes['md-16-9'] = array(
       'width'  => $width,
       'height' => $height,
       'crop'   => true
   );
   list($width, $height) = $ratio->getCalcResize(576);
   $sizes['sm-16-9'] = array(
       'width'  => $width,
       'height' => $height,
       'crop'   => true
   );

   // calculate the max width and height for the ratio
   $ratio = new ImageRatio( 4, 5 );
   list($width, $height) = $ratio->getLargestSize(
      $metadata['width'],
      $metadata['height']
   );
   // let's add our custom size
   $sizes['xxl-4-5'] = array(
     'width'  => $width,
     'height' => $height,
     'crop'   => true
   );

   list($width, $height) = $ratio->getCalcResize(1200);
   $sizes['xl-4-5'] = array(
       'width'  => $width,
       'height' => $height,
       'crop'   => true
   );
   list($width, $height) = $ratio->getCalcResize(992);
   $sizes['lg-4-5'] = array(
       'width'  => $width,
       'height' => $height,
       'crop'   => true
   );
   list($width, $height) = $ratio->getCalcResize(768);
   $sizes['md-4-5'] = array(
       'width'  => $width,
       'height' => $height,
       'crop'   => true
   );
   list($width, $height) = $ratio->getCalcResize(576);
   $sizes['sm-4-5'] = array(
       'width'  => $width,
       'height' => $height,
       'crop'   => true
   );*/

}
return $sizes;

}, 10, 2 );

add_action( 'after_setup_theme', 'wpdocs_theme_setup' );

function wpdocs_theme_setup() {

    $image_sizes  = [ '16-9', '4-5', '4-3', '2-1', '1-1' ];
    $screen_sizes = [ 'xxl-', 'xl-', 'lg-', 'md-', 'sm-' ];

    foreach( $image_sizes as $image_size ) {
        foreach ( $screen_sizes as $screen_size ) {
            add_image_size( $screen_size . $image_size );
        }
    }

// add_image_size( 'homepage-thumb', 220, 180, true ); // (cropped)
}