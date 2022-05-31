<div class="menu-product-block">
    <div class="menu-product-block__img">
        <img src="<?php print $model_data['image'];?>" alt="<?php print $title;?>">
    </div>
    <div class="menu-dropdown-block__desc">
        <div class="menu-product-block__head">
            <?php print inLineRating( $model_data['average_rating'], $model_data['total_reviews'], $model_data['price'], $model_data['link'] )?>
        </div>
        <div class="menu-product-block__text">
            <p><?php print $model_data['model_total_seats'];?></p>
            <p><?php print $model_data['style'];?></p>
            <p><?php print $model_data['dimensions'];?></p>
            <p><?php print $model_data['voltage'];?></p>
            <p><?php print $model_data['water_care'];?></p>
        </div>
    </div>
</div>