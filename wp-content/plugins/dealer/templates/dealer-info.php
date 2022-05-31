<h2>Contact Info</h2>
<p class="dealer-address">
<?php
  if ( get_field( 'dealership_address_1' ) ) {
    echo '<span>' . get_field( 'dealership_address_1' ) . '</span>,<br/>';
  }
  if ( get_field( 'dealership_city' ) ) {
    echo '<span>' . get_field( 'dealership_city' ) . '</span>, ';
  }
  if ( get_field( 'dealership_state_code' ) ) {
    echo '<span>' . get_field( 'dealership_state_code' ) . '</span> ';
  }
  if ( get_field( 'dealership_zip' ) ) {
    echo '<span>' . get_field( 'dealership_zip' ) . '</span>';
  }
?>
</p>

<ul class="dealer-contact-info">
  <?php
  if ( get_field( 'dealership_phone' ) ) { ?>
    <li>
      <a href="tel:<?php the_field( 'dealership_phone' ); ?>">
        <i class="fa fa-phone" aria-hidden="true"></i> <?php the_field( 'dealership_phone' ); ?>
      </a>
    </li>
  <?php }
  if ( get_field( 'dealership_email' ) ): ?>
    <li>
      <a class="dealer-website-email-link" href="mailto:<?php the_field( 'dealership_email' ); ?>">
        <i class="fa fa-envelope" aria-hidden="true"></i> <?php the_field( 'dealership_email' ); ?>
      </a>
    </li>
  <?php endif;
  if ( get_field( 'dealership_website' ) ): ?>
    <li>
      <a class="dealer-website-external-link" target="_blank" href="http://<?php the_field( 'dealership_website' ); ?>">
        <?php the_field( 'dealership_website' ); ?>
      </a>
    </li>
  <?php endif; ?>
</ul>
