<section class="dealer-info dealer-info--confirmation dz">
    <h2 class="hidden-sm hidden-xs text-center"><?php print $dealer_param['dealerName']; ?></h2>
    <div class="row">
      <div class="col-md-6">
        <?php if( ! empty($location) ):?>
          <div class="acf-map" style="height: 300px!important;">
          <div class="marker" data-lat="<?php echo $location['lat']; ?>" data-lng="<?php echo $location['lng'];?>" data-title="<?php print $dealer_param['dealerName']; ?>"></div>
          </div>
        <?php endif; ?>
      </div>
      <div class="col-md-6">
        <div class="row">
          <div class="col-md-4">
            <b>Address:</b>
          </div>
          <div class="col-md-8">
            <?php
              $param = NULL;
              if ( $dealer_param['dealerAddress1'] ) {
                $param .= $dealer_param['dealerAddress1'];
                echo '<span>' . $dealer_param['dealerAddress1'] . '</span></br>';
              }
              if ( $dealer_param['dealerCity'] ) {
                $param .= ", " . $dealer_param['dealerCity'];
                echo '<span>' . $dealer_param['dealerCity'] . '</span>';
              }
              if ( $dealer_param['dealerState'] ) {
                echo ', <span>' . $dealer_param['dealerState'] . '</span>';
              }
              if ( $dealer_param['dealerPostcode'] ) {
                $param .= " " . $dealer_param['dealerPostcode'];
                echo ' <span>' . $dealer_param['dealerPostcode'] . '</span></br>';
              }
              if ( $param ) {
                echo '<a href="http://maps.google.com/maps?daddr=' . urlencode($param) . '" class="track-confirmation-get-directions">Get Directions</a>';
              }
            ?>
          </div>
        </div>

        <?php if($dealer_param['dealerHours']): ?>
          <div class="row">
            <div class="col-md-4">
              <b>Store Hours:</b>
            </div>
            <div class="col-md-8">
              <?php echo $dealer_param['dealerHours']; ?>
            </div>
          </div>
        <?php endif;?>

        <?php if($dealer_param['dealerPhoneNumber']): ?>
          <div class="row">
            <div class="col-md-4">
              <b>Phone:</b>
            </div>
            <div class="col-md-8">
              <a class="track-confirmation-phone" href="tel:<?php echo $dealer_param['dealerPhoneNumber']; ?>"><?php echo $dealer_param['dealerPhoneNumber']; ?></a>
            </div>
          </div>
        <?php endif;?>

        <?php if($dealer_param['dealerEmail']): ?>
          <div class="row">
            <div class="col-md-4">
              <b>Email:</b>
            </div>
            <div class="col-md-8">
              <a class="track-confirmation-email" href="mailto:<?php echo $dealer_param['dealerEmail']; ?>"><?php echo $dealer_param['dealerEmail']; ?></a>
            </div>
          </div>
        <?php endif;?>

        <?php if($dealer_param['dealerWebsite']): ?>
          <div class="row">
            <div class="col-md-4">
              <b>Website:</b>
            </div>
            <div class="col-md-8">
            <a href="<?php echo $dealer_param['dealerWebsite']; ?>" class="track-confirmation-website"><?php echo $dealer_param['dealerName'];?></a>
            </div>
          </div>
        <?php endif;?>
      </div>
    </div>
</section>
