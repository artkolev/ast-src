<?php

use yii\helpers\Html;

/**
 * @var array $promo_items_ids
 * @var \app\modules\events\models\Events[] $items
 */

$promo_items_ids = $promo_items_ids ?? [];

/* используется только для страниц каталога. Если надо в другом месте - см. фильтры по специализациям. */
?>
<?php if (!empty($items)) { ?>
    <?php foreach ($items as $key => $item) { ?>
        <?php
        [$price, $form_id] = $item->priceBadge;
        $eventHolder = $item->eventCardStatusText();
        $itemClassList = [
                ($item->author_id == 0 || $item->author->profile->is_academy) ? 'academy' : false,
                in_array($item->id, $promo_items_ids) ? 'focused' : false,
                $eventHolder->holder ? 'soldout no-prices' : false,
                $price ? false : ($eventHolder->holder ? false : 'no-prices')
        ];
        $itemClassListStr = trim(implode(' ', $itemClassList));
        ?>
        <div onclick="window.location.href='<?= $item->getUrlPath(); ?>'"
             class="all-events-card <?= $itemClassListStr; ?>">
            <a href="<?= $item->getUrlPath(); ?>" class="all-events-card-img">
                <?= Html::img($item->getThumb('image', 'main'), ['alt' => $item->name, 'loading' => 'lazy']); ?>
                <?php if ($eventHolder->holder) { ?>
                    <!-- если soldout - выводить лейбл, если билетов нет, добавить класс closed -->
                    <div class="soldout-label closed"><?= $eventHolder->holder_text; ?></div>
                <?php } ?>
            </a>
            <div class="all-events-card-info">
                <?php if (!$eventHolder->holder) { ?>
                    <?php if ($price) { ?>
                        <a href="<?= $item->getUrlPath() . '#tickets_box_' . $form_id; ?>"
                           class="all-events-card-price"><?= $price; ?></a>
                    <?php } ?>
                <?php } ?>
                <div class="all-events-card-dates"><?= $item->getEventDateForView(); ?></div>
                <div class="all-events-card-type"><?= $item->format ? $item->format->name : ''; ?></div>
                <a href="<?= $item->getUrlPath(); ?>" class="all-events-card-title"><?= $item->name; ?></a>
                <div class="all-events-card-author-wrapper">
                    <?php $author = $item->getAuthorForView(); ?>
                    <a href="<?= $author['link']; ?>" class="all-events-card-author">
                        <div class="all-events-card-author-img">
                            <img src="<?= $author['image']; ?>" alt="<?= $author['name']; ?>">
                        </div>
                        <div class="all-events-card-author-name"><?= $author['name']; ?></div>
                    </a>
                    <span class="all-events-card-tag"><?= $item->typeName; ?></span>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } else { ?>
    <p>К сожалению, мероприятия не найдены</p>
<?php } ?>
