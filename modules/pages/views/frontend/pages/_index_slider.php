<?php

/**
 * Мультиполя модели должны быть slider_image и slider_mobile_image, миниатюры main.
 * @see \app\modules\pages\models\TargetAudiencePage::getFields() slider_image
 * @see \app\modules\pages\models\TargetAudiencePage::behaviors() sliderimage
 */

use app\helpers\MainHelper;
use app\modules\admin\components\DeepModel;
use yii\web\View;

/**
 * @var View $this
 * @var DeepModel $model
 */

if (isset($model->slider) && !empty($model->slider)) {
    $items = MainHelper::cleanInvisibleMultifield($model->slider);
    if (empty($items)) {
        return '';
    }
    ?>
    <section class="sec index-slider-section">
        <div class="container wide">
            <?php /* <div class="close-index-slider"></div><?php */ ?>
            <div class="index-slider with-img owl-carousel owl-theme" data-autoplay="true" data-timeout="10000">
                <?php
                foreach ($items as $key => $item) {
                    $item = (object)$item;
                    ?>
                    <div class="index-slide">
                        <?php if ($item->url) { ?>
                        <a <?= ($item->use_fancybox && MainHelper::validateYoutubeLink($item->url)) ? 'data-fancybox' : ''; ?>
                                href="<?= $item->url; ?>" rel="nofollow"><?php } ?>
                            <img class="visible-over650"
                                 src="<?= $model->getThumb('sliderimage', 'main', false, (int)($item->slider_image)[0]); ?>"
                                 alt="<?= $item->name; ?>">
                            <img class="visible-less650"
                                 src="<?= $model->getThumb('slidermobileimage', 'main', false, (int)($item->slider_mobile_image)[0]); ?>"
                                 alt="<?= $item->name; ?>">
                            <?php if ($item->url) { ?></a><?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>