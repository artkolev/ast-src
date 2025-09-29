<?php

use yii\helpers\Url;

?>

<script async src="https://yastatic.net/share2/share.js"></script>
<div class="desktop-visible">
    <div class="share-block-project">
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
</div>