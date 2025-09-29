<?php

?>
<div class="container wide">
    <div class="advent-page-title" title="Онлайн-адвент">
        <?php if (!empty($model->getThumb('title_image', 'main'))) { ?>
            <img src="<?= $model->getThumb('title_image', 'main'); ?>" alt="" loading="lazy">
        <?php } else { ?>
            <img src="img/advent/advent-title.svg" alt="" loading="lazy">
        <?php } ?>
    </div>
    <?php if (!empty($model->title)) { ?>
        <div class="advent-page-text"><?= $model->title; ?></div>
    <?php } ?>
    <div class="advent-calendar">
        <?php foreach ($model->cards as $card) { ?>
            <div class="advent-date-container advent-date-flipper-animation">
                <div class="advent-date-flipper">
                    <div class="advent-date-flipper-front">
                        <img src="<?= $model->getThumb('card_promo', 'main', false, ($card['image_promo'])[0] ?? null); ?>"
                             alt="" loading="lazy">
                    </div>
                    <?php if (!empty($card['url'])) { ?>
                        <a href="<?= $card["url"]; ?>" target="_blank" class="advent-date-flipper-back">
                            <img src="<?= $model->getThumb('card_banner', 'main', false, ($card['image_banner'])[0] ?? null); ?>"
                                 alt="" loading="lazy">
                            <?php if (!empty($card['button_text'])) { ?>
                                <span class="btn-advent"><?= $card['button_text']; ?></span>
                            <?php } ?>
                        </a>
                    <?php } else { ?>
                        <div class="advent-date-flipper-back">
                            <img src="<?= $model->getThumb('card_banner', 'main', false, ($card['image_banner'])[0] ?? null); ?>"
                                 alt="" loading="lazy">
                            <?php if (!empty($card['button_text'])) { ?>
                                <span class="btn-advent"><?= $card['button_text']; ?></span>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        <div class="advent-date-new-year">
            <?php if (!empty($model->getThumb('banner_image', 'main'))) { ?>
                <img src="<?= $model->getThumb('banner_image', 'main'); ?>"
                     alt=""
                     class="desktop-visible <?= !empty($model->getThumb('modal_image', 'main')) ? 'advent-banner' : ''; ?>"
                     loading="lazy">
            <?php } ?>
            <?php if (!empty($model->getThumb('banner_image_mobile', 'main'))) { ?>
                <img src="<?= $model->getThumb('banner_image_mobile', 'main'); ?>"
                     alt=""
                     class="mobile-visible <?= !empty($model->getThumb('modal_image', 'main')) ? 'advent-banner' : ''; ?>"
                     loading="lazy">
            <?php } ?>
        </div>

        <?php if (!empty($model->getThumb('modal_image', 'main'))) { ?>
            <div id="advent-modal" style="display: none; padding: 0">
                <img src="<?= $model->getThumb('modal_image', 'main'); ?>" loading="lazy">
            </div>
        <?php } ?>
    </div>
</div>
<?php
$js = <<<JS
    $('body').on('click', '.advent-date-flipper-animation:not(.blocked)', function() {
        $(this).toggleClass('active');
    });

    $('body').on('click', '.advent-banner', function() {
        $.fancybox.open({
            src: '#advent-modal',
            type: 'inline',
        });
    });
JS;
$this->registerJs($js);
?>

