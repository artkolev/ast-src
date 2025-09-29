<?php

use app\modules\service\models\Service;
use yii\helpers\Html;

/**
 * @var Service $items
 */
?>

<?php if (!empty($items)) { ?>
    <?php foreach ($items as $item) { ?>
        <?php $eventHolder = $item->eventCardStatusText(); ?>
        <div class="expert-page-event <?= $eventHolder->holder ? 'soldout' : false; ?>">
            <a href="<?= $item->getUrlPath(); ?>" class="expert-page-event-img">
                <?= Html::img($item->getThumb('image', 'main'), ['alt' => $item->name, 'loading' => 'lazy']); ?>
                <?php if ($eventHolder->holder) { ?>
                    <!-- если soldout - выводить лейбл, если билетов нет, добавить класс closed -->
                    <div class="soldout-label closed"><?= $eventHolder->holder_text; ?></div>
                <?php } ?>
            </a>
            <div class="expert-page-event-info">
                <div class="all-events-card-dates"><?= $item->getEventDateForView(); ?></div>
                <div class="all-events-card-type"><?= $item->format ? $item->format->name : ''; ?></div>
                <a href="<?= $item->getUrlPath(); ?>" class="expert-page-event-title"><?= $item->name; ?></a>
                <?php if (!$eventHolder->holder) { ?>
                    <?php [$price, $form_id] = $item->priceBadge; ?>
                    <?php if ($price) { ?>
                        <a href="<?= $item->getUrlPath() . '#tickets_box_' . $form_id; ?>"
                           class="all-events-card-price"><?= $price; ?></a>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
<?php } ?>