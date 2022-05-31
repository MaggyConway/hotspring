<li class="menu__item dropdown"><a href="<?php print $link;?>"><?php print $title;?></a>
    <div class="dropdown-menu-wrapper">
        <div class="container">
            <ul class="dropdown-menu-level-1 list">
                <?php foreach ($links as $key => $item) {
                    if ( isset($item['title']) ) {
                       print '<li class="menu__item-level-1"><a href="' . $item['link'] . '" data-hover-load="' . $key . '">' . $item['title'] . '</a></li>';
                    } else {
                        print '<li class="menu__item-level-1"><a href="' . $item  . '" >' . $key  . '</a></li>';
                    }
                }?>
            </ul>
        </div>
    </div>
</li>