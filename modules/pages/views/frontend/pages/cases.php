<?php

use yii\helpers\Html;

?>
<main class="sec content_sec">
    <div class="container wide">
        <h1><?= $model->getNameForView(); ?></h1>
        <div class="subheader"><?= $model->content; ?></div>
        <?php if ($cases) { ?>
            <div class="directions_box">
                <?php foreach ($cases as $item) { ?>
                    <div class="direction_item">
                        <div class="relative">
                            <div>
                                <h3><?= Html::a($item->name, $item->getUrlPath()); ?></h3>
                                <div class="direction_item-desc">
                                    <div><?= $item->anons; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

            </div>
        <?php } ?>
    </div>
</main>
