<div class="collection-block">
    <div class="collection-block__image">
        <img src="<?php print $collection_data['image']; ?>" alt="<?php print $collection_data['title']; ?>">
    </div>
    <div class="collection-block__content">
        <h4 class="collection-block__title"><?php print $collection_data['title']; ?></h4>
        <div class="collection-block__text"><?php print $collection_data['text']; ?></div>
        <a href="<?php print $collection_data['link']; ?>" class="collection-block__link">View Collection</a>
    </div>
</div>