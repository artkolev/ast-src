<?php

use app\modules\service\models\Service;
use yii\helpers\Html;

/**
 * @var Service $items
 */
?>

<?php if (!empty($items)) { ?>
    <?php foreach ($items as $item) { ?>
        <a href="<?= $item->getUrlPath(); ?>" class="expert-page-event expert-page-simple">
            <div class="expert-page-event-img">
                <?= Html::img($item->getThumb('image', 'main'), ['alt' => $item->name]); ?>
            </div>
            <div class="expert-page-event-info">
                <div class="blog-page-4card-slide-title"><?= $item->name; ?></div>
                <div class="blog-page-4card-slide-text-wrapper">
                    <div class="blog-page-4card-slide-date"><?= \Yii::$app->formatter->asDate($item->published, 'php:d.m.Y'); ?></div>
                    <!-- <div class="blog-page-4card-slide-viewed">453</div> -->
                </div>
            </div>
        </a>
    <?php } ?>
<?php } ?>