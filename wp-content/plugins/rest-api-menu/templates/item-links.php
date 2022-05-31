<ul class="collection__models">
    <?php
    foreach ($links as $key => $item) {
        if( empty( $item['link'] ) ) {
            $item['link'] = $item['model_data']['link'];
          }
        $item_class = 'collection__model';
        if ( !empty( $item['class'] ) ) {
            $item_class .= ' ' . $item['class'];
        }
        if( isset($item['title']) ) {
            print '
            <li class="' . $item_class . '">
                <a href="'.$item['link'].'" data-hover-load="'.$key.'">'.$item['title'].' <span>'.$item['model_data']['seating_capacity'].'</span></a>
            </li>';
        } else {
            print '
            <li class="' . $item_class . '">
                <a href="' . $item['link'] . '">' . $key . '</a>
            </li>
            ';
        }

    }
    ?>
</ul>
<div class="collection__panel"></div>