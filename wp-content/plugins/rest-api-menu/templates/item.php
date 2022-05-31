<?php
$this_class = 'menu__item';
if( !empty($links) ) {
  $this_class .= ' has-submenu';
}
if(!empty($class)){
  $this_class .= ' ' . $class;
}
?>
<li class="<?php print $this_class;?>">
        <a href="<?php print $link;?>" data-text="<?php print $title;?>"><?php print $title;?></a>
        <?php if( !empty($links) ) { ?>
        <div class="main-menu__panel">
          <div class="header__container main-menu__container">
            <ul class="submenu submenu_left">
              <?php foreach ($links as $key => $item) {
                $item_class = 'menu__item';
                if ( !empty( $item['class'] ) ) {
                  $item_class .= ' ' . $item['class'];
                }
                if (isset($item['collection_id'])) {
                  $item_class .= ' has-submenu';
                  if( empty( $item['link'] ) ) {
                    $item['link'] = $item['collection_data']['link'];
                  }
                  $item_collection = wpram_get_template('item-collection.php', $item);
                  print '
                            <li class="' . $item_class . '">
                            <a href="' . $item['link'] . '" data-load-item="' . $title . '" data-hover-load="' . $key . '">' . $item['title'] . '</a>
                            ' . $item_collection . '
                            </li>';
                }
                if ( !isset($item['collection_id']) && isset( $item['links'] ) ) {
                  $item_class .= ' has-submenu links';
                  if( empty( $item['link'] ) ) {
                    $item['link'] = $item['collection_data']['link'];
                  }
                  $item_collection = wpram_get_template('item-links.php', $item);
                  print '
                            <li class="' . $item_class . '">
                            <a href="' . $item['link'] . '" data-load-item="' . $title . '" data-hover-load="' . $key . '">' . $item['title'] . '</a>
                            ' . $item_collection . '
                            </li>';
                }
                if ( !isset( $item['title'] ) ) {
                  print '<li class="' . $item_class . '"><a href="' . $item  . '" >' . $key  . '</a></li>';
                }


              }?>

            </ul>
          </div>
        </div>
        <?php } ?>
</li>