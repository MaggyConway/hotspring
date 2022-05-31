<?php
if ( ! defined('ABSPATH')) exit;  // if direct access
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

