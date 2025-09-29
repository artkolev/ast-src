<?php

use app\modules\service\models\Service;
use yii\helpers\Html;

/**
 * @var Service $items
 */
?>

<?php if (!empty($items)) { ?>
    <?php foreach ($items as $item) { ?>
        <div class="expert-page-event <?= ($item->statusFull == 'archive' || $item->registration_open == false) ? 'soldout' : ''; ?>">
            <a href="<?= $item->getUrlPath(); ?>" class="expert-page-event-img">
                <?= Html::img($item->getThumb('image', 'main'), ['alt' => $item->name, 'loading' => 'lazy']); ?>
                <!-- если soldout - выводить лейбл -->
                <div class="soldout-label <?= $item->registration_open == false ? 'closed' : ''; ?>">
                    <?= $item->registration_open == false ? 'Продажа закрыта' : 'Программа завершена'; ?>
                </div>
            </a>
            <div class="expert-page-event-info">
                <div class="all-events-card-dates"><?= $item->getEduprogDateForView(); ?></div>
                <div class="all-events-card-type"><?= $item->format ? $item->format->name : ''; ?></div>
                <a href="<?= $item->getUrlPath(); ?>" class="expert-page-event-title"><?= $item->name; ?></a>
                <?php $badge = $item->getLowestPrice(); ?>
                <?php if ($badge['form_id']) { ?>
                    <a href="<?= $item->getUrlPath() . '#tickets_box_' . $badge['form_id']; ?>"
                       class="all-events-card-price <?= $item->hit_price ? 'superprice' : ''; ?>">
                        <?= ($badge['low_price'] == 0) ? 'Бесплатно' : 'от ' . number_format($badge['low_price'], 0, '.', ' ') . ' ₽'; ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
<?php } ?>