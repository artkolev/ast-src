<?php

use app\modules\pages\models\Page;
use yii\helpers\Url;

/** @var Page $model */

// social widget from here - https://yandex.ru/dev/share/doc/dg/add.html
?>

<script async src="https://yastatic.net/share2/share.js"></script>

<div class="eventpage_sideblock">
    <h5>Поделиться</h5>
    <div class="socs_box">
        <div class="ya-share2"
             data-curtain
             data-shape="round"
             data-color-scheme="blackwhite"
             data-services="vkontakte"
             data-url="<?= Url::to([$model->getUrlPath()], true); ?>"
             data-direction="vertical"
        ></div>
        <span class="soc_circle_text copyURL" data-copied="Ссылка скопирована"><i class="fa fa-link"></i> Копировать ссылку</span>
    </div>
</div>