<?php

use yii\helpers\Url;

?>

<script async src="https://yastatic.net/share2/share.js"></script>
<div class="share-block desktop-visible">
    <div class="ya-share2"
         data-curtain
         data-shape="round"
         data-color-scheme="blackwhite"
         data-services="vkontakte,telegram,whatsapp"
         data-url="<?= Url::to([$model->getUrlPath()], true); ?>"
         data-direction="vertical"
    ></div>
    <span class="share-soc copyURL" data-copied="Ссылка скопирована">
        <img src="/img/socs/share.png" alt="">
        <img src="/img/socs/share-hover.png" class="share-soc-hover" alt="">
    </span>
</div>