<?php

use app\helpers\Constants;

/**
 * @var string $title
 * @var string $anchor
 * @var string $text
 * @var string $type
 * @var string $link
 * @var string $button
 * @var array $list
 */

?>

<?php if (!empty($list)) { ?>
    <?php if ($type === Constants::CARD_TYPE_STATIC) { ?>
        <div <?php if (!empty($anchor)) { ?>id="<?= $anchor; ?>"<?php } ?>
             class="sec section-page section-corporate-offer gray-bg">
            <div class="container wide">
                <?php if ($title) { ?><h2 class="section-page-title"><?= $title; ?></h2><?php } ?>
                <?php if ($text) { ?>
                    <div class="section-page-text"><?= $text; ?></div><?php } ?>
                <div class="corporate-offer-list">
                    <?php foreach ($list as $key => $item) { ?>
                        <div class="corporate-offer-element">
                            <div class="corporate-offer-step"><?= str_pad($key, 2, '0', STR_PAD_LEFT); ?></div>
                            <div class="corporate-offer-title"><?= $item['title']; ?></div>
                            <div class="corporate-offer-text"><?= $item['text']; ?></div>
                        </div>
                    <?php } ?>
                    <?php if (!empty($link) && !empty($button)) { ?>
                        <div class="corporate-offer-element-banner">
                            <div class="corporate-offer-banner-title">Оставьте запрос <br>или заявку</div>
                            <a href="<?= $link; ?>" class="button white"><?= $button; ?></a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div <?php if (!empty($anchor)) { ?>id="<?= $anchor; ?>"<?php } ?>
             class="sec section-page section-corporate-offer gray-bg">
            <div class="container wide">
                <?php if ($title) { ?><h2 class="section-page-title"><?= $title; ?></h2><?php } ?>
                <?php if ($text) { ?>
                    <div class="section-page-text"><?= $text; ?></div><?php } ?>
                <div class="corporate-offer-list default-slider-4 owl-carousel owl-theme" data-loop="true"
                     data-autoplay="true" data-timeout="5000">
                    <?php foreach ($list as $key => $item) { ?>
                        <div class="corporate-offer-element">
                            <div class="corporate-offer-step"><?= str_pad($key, 2, '0', STR_PAD_LEFT); ?></div>
                            <div class="corporate-offer-title"><?= $item['title']; ?></div>
                            <div class="corporate-offer-text"><?= $item['text']; ?></div>
                        </div>
                    <?php } ?>
                    <?php if (!empty($link) && !empty($button)) { ?>
                        <div class="corporate-offer-element-banner">
                            <div class="corporate-offer-banner-title">Оставьте запрос <br>или заявку</div>
                            <a href="<?= $link; ?>" class="button white"><?= $button; ?></a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } ?>
