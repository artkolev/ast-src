<?php

use yii\helpers\Html;

?>
<?php if (!empty($items)) { ?>
    <?php foreach ($items as $key => $item) {
        $badge = $item->getLowestPrice();
        ?>
        <div onclick="window.location.href='<?= $item->getUrlPath(); ?>'"
             class="all-events-card <?= in_array($item->id, $promo_items_ids) ? 'focused' : ''; ?> <?= ($badge['form_id']) ? '' : 'no-prices'; ?>">
            <?= $this->render('_shields', ['items' => $item->shieldsVisible]); ?>
            <?= Html::a(Html::img($item->getThumb('image', 'main'), ['alt' => $item->name, 'loading' => 'lazy']), $item->getUrlPath(), ['class' => 'all-events-card-img']); ?>
            <div class="all-events-card-info">
                <?php
                if ($badge['form_id']) { ?>
                    <a href="<?= $item->getUrlPath() . '#tickets_box_' . $badge['form_id']; ?>"
                       class="all-events-card-price blue <?= $item->hit_price ? 'superprice' : ''; ?>">
                        <?= ($badge['low_price'] == 0) ? 'Бесплатно' : 'от ' . number_format($badge['low_price'], 0, '.', ' ') . ' ₽'; ?>
                    </a>
                <?php } ?>
                <div class="all-events-card-dates">
                    <?= $item->getEduprogDateForView(); ?>
                </div>
                <?php if ($item->category) { ?>
                    <div class="all-events-card-type"><?= $item->category->name; ?> (<?= $item->hours; ?> ч)</div>
                <?php } ?>
                <a href="<?= $item->getUrlPath(); ?>" class="all-events-card-title"><?= $item->name; ?></a>
                <div class="all-events-card-author-wrapper">
                    <a href="<?= $item->author->getUrlPath(); ?>" class="all-events-card-author">
                        <div class="all-events-card-author-img">
                            <img src="<?= $item->author->profile->getThumb('image', 'main'); ?>"
                                 alt="<?= $item->author->profile->halfname; ?>">
                        </div>
                        <div class="all-events-card-author-name"><?= $item->author->profile->halfname; ?></div>
                    </a>
                    <span class="all-events-card-tag"><?= $item->formatName; ?></span>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } else { ?>
    <p>К сожалению, программы не найдены</p>
<?php } ?>
