<?php

use yii\helpers\Html;

?>
<div class="experts_box">
    <?php if (!empty($items)) { ?>
        <?php foreach ($items as $key => $item) { ?>
            <div class="material-item">
                <div class="material-item-wrapper">
                    <?php if ($item->getThumb('image', 'main')) { ?>
                        <div class="material-img">
                            <img src="<?= $item->getThumb('image', 'main'); ?>" alt="">
                        </div>
                    <?php } ?>
                    <div class="material-item-info">
                        <div class="material-item-autor visible-over650">
                            <?php if ($item->author) { ?>
                                <a href="<?= $item->author->getUrlPath(); ?>" class="material-item-autor-img">
                                    <img src="<?= $item->author->profile->getThumb('image', 'main'); ?>" alt="">
                                </a>
                                <a href="<?= $item->author->getUrlPath(); ?>"
                                   class="material-item-autor-name"><?= $item->author->profile->name; ?> <?= $item->author->profile->surname; ?></a>
                            <?php } ?>
                            <div class="material-item-autor-date"><?= \Yii::$app->formatter->asDate($item->published, 'php:d.m.Y'); ?></div>
                        </div>
                        <div class="material-item-text"><?= Html::a($item->name, $item->getUrlPath()); ?></div>
                        <div class="material_item-tags">
                            <?php foreach ($item->tags as $k => $tag) {
                                echo Html::a('<b class="tag-hovered">' . $tag->name . '</b><span>' . $tag->name . '</span></a>', $model->getUrlPath() . '?tag=' . urlencode($tag->name), ['class' => 'tag', 'data-tagid' => $tag->id, 'data-tagname' => $tag->name]);
                            } ?>
                            <a href="#" class="tag more"><span>Ещё +<u><!-- js --></u></span></a>
                        </div>
                    </div>
                </div>
                <div class="material-item-autor visible-less650">
                    <?php if ($item->author) { ?>
                        <a href="<?= $item->author->getUrlPath(); ?>" class="material-item-autor-img">
                            <img src="<?= $item->author->profile->getThumb('image', 'main'); ?>" alt="">
                        </a>
                        <a href="<?= $item->author->getUrlPath(); ?>"
                           class="material-item-autor-name"><?= $item->author->profile->name; ?> <?= $item->author->profile->surname; ?></a>
                    <?php } ?>
                    <div class="material-item-autor-date"><?= \Yii::$app->formatter->asDate($item->published, 'php:d.m.Y'); ?></div>
                </div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <h3 class="pb20"><i>К сожалению, по вашему запросу ничего не найдено</i></h3>
    <?php } ?>
</div>