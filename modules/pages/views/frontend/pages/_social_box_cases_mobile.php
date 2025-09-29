<?php

use yii\helpers\Url;

?>

<div class="mobile-visible">
    <div class="share-block-project">
        <div class="share-block-without-widget" style="display: none;">
            <div class="ya-share2"
                 data-curtain
                 data-shape="round"
                 data-services="vkontakte,telegram,whatsapp"
                 data-url="<?= Url::to([$model->getUrlPath()], true); ?>"
                 data-direction="vertical"
            ></div>
            <span class="share-soc copyURL" data-copied="Ссылка скопирована">
                <img src="img/socs/share-hover.png" alt="">
                <img src="img/socs/share.png" class="share-soc-hover" alt="">
            </span>
        </div>
        <div class="share-block-with-widget" style="display: none;">
            <span class="share-soc blog-share"
                  data-title="<?= (isset($title) && !empty($title)) ? $title : $model->getNameForView(); ?>"
                  data-text="<?= $description; ?>" data-url="<?= Url::to([$model->getUrlPath()], true); ?>">
                <img src="img/socs/share-hover.png" alt="">
                <img src="img/socs/share.png" class="share-soc-hover" alt="">
            </span>
        </div>
    </div>
</div>