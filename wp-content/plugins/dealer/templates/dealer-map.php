<div class="dealer-map">
  <?php if ( ! empty( $location ) ): ?>
    <div class="acf-map">
      <div class="marker" data-lat="<?php echo $location['lat']; ?>"
           data-lng="<?php echo $location['lng']; ?>"
           data-title="<?php the_field( 'dealership_name' ); ?>">
        <p><?php the_field( 'dealership_name' ); ?></p>
        <p><?php the_field( 'dealership_address_1' ); ?></p>
      </div>
    </div>
  <?php endif; ?>
</div><!-- dealer-map -->
