<?php

use app\helpers\MainHelper;
use yii\helpers\Html;

$cards = MainHelper::cleanInvisibleMultifield($model->card_list);
$faq = MainHelper::cleanInvisibleMultifield($model->faq_list);
$anchors = MainHelper::cleanInvisibleMultifield($model->anchors);
?>
    <div class="sec services-promo-banner">
        <!--<div class="services-banner-img visible-over650" style="background: linear-gradient(0deg, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url(<?= $model->getThumb('image', 'main'); ?>) center no-repeat; background-size: cover;"></div>-->
        <!--<div class="services-banner-img visible-less650" style="background: linear-gradient(0deg, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url(<?= $model->getThumb('image_mobile', 'main'); ?>) center no-repeat; background-size: cover;"></div>-->
        <div class="container wide">
            <div class="services-promo-info">
                <?php if ($model->block1_title) { ?><h1><?= $model->block1_title; ?></h1><?php } ?>
                <div class="buttons">
                    <?php if ($model->block1_left_button_title && $model->block1_left_button_url) { ?><a
                        href="<?= $model->block1_left_button_url; ?>"
                        class="button"><?= $model->block1_left_button_title; ?></a><?php } ?>
                    <?php if ($model->block1_right_button_title && $model->block1_right_button_url) { ?><a
                        href="<?= $model->block1_right_button_url; ?>"
                        class="button"><?= $model->block1_right_button_title; ?></a><?php } ?>
                </div>
            </div>
            <div class="services-promo-animation">
                <?php for ($col_id = 0; $col_id < $cols; $col_id++) {
                    if (!empty($random_experts) && sizeof($random_experts) >= $rows * $col_id) { ?>
                        <div class="services-promo-animation-column">
                            <?php if (!($col_id % 2)) {
                                foreach (array_slice($random_experts, $rows * $col_id, $cols) as $random_expert) { ?>
                                    <a href="<?= $random_expert->getUrlPath(); ?>"><img
                                                src="<?= $random_expert->profile->getThumb('image', 'prev'); ?>" alt=""></a>
                                <?php }
                            } ?>
                            <?php foreach (array_slice($random_experts, $rows * $col_id, $rows) as $random_expert) { ?>
                                <a href="<?= $random_expert->getUrlPath(); ?>"><img
                                            src="<?= $random_expert->profile->getThumb('image', 'prev'); ?>" alt=""></a>
                            <?php } ?>
                            <?php if ($col_id % 2) {
                                foreach (array_slice($random_experts, $rows * $col_id, $cols) as $random_expert) { ?>
                                    <a href="<?= $random_expert->getUrlPath(); ?>"><img
                                                src="<?= $random_expert->profile->getThumb('image', 'prev'); ?>" alt=""></a>
                                <?php }
                            } ?>
                        </div>
                    <?php }
                }
                ?>
            </div>
            <?php if (!empty($anchors)) { ?>
                <div class="services-promo-anchors">
                    <?php foreach ($anchors as $anchor) { ?>
                        <?php if ($anchor['link'] && $anchor['name']) { ?><a href="<?= $anchor['link']; ?>"
                                                                             class="anchor"><?= $anchor['name']; ?></a><?php } ?>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>

    <main class="sec content_sec">
        <?php if (!empty($model->problems)) { ?>
            <div id="services-problem" class="sec services-problem-block">
                <div class="container wide">
                    <?php if ($model->block2_title) { ?><h2
                            class="services-promo-title"><?= $model->block2_title; ?></h2><?php } ?>
                    <?php if ($model->block2_text) { ?><p
                            class="services-promo-text"><?= $model->block2_text; ?></p><?php } ?>
                    <div class="services-problem-slider owl-carousel owl-theme">
                        <?php foreach ($model->problems as $item) { ?>
                            <div class="services-problem-slide"
                                 style="background: #<?= $item->color ? $item->color : 'FFEED4'; ?>;">
                                <?php if (!empty($item->getThumb('image', 'main'))) { ?>
                                    <div class="services-problem-slide-icon">
                                        <img src="<?= $item->getThumb('image', 'main'); ?>" alt="">
                                    </div>
                                <?php } ?>
                                <div class="services-problem-slide-name"><?= $item->name; ?></div>
                                <div class="services-problem-list">
                                    <?php foreach ($item->questions as $question) { ?>
                                        <div class="services-problem-element"><span><?= $question; ?></span></div>
                                    <?php } ?>
                                </div>
                                <a href="<?= $service_type_page->getUrlPath() . '?problem_id=' . $item->id . '&' . preg_replace('/\?/', '', $item->page_query); ?>"
                                   class="button-o"><?= $item->url_title; ?></a>
                            </div>
                        <?php } ?>
                        <div class="services-problem-slide-fake"></div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php if (!empty($faq)) { ?>
            <div id="services-support" class="sec services-support-block">
                <div class="container wide">
                    <?php if ($model->block3_title) { ?><h2
                            class="services-promo-title"><?= $model->block3_title; ?></h2><?php } ?>
                    <div class="services-support-list">
                        <?php foreach ($faq as $item) { ?>
                            <a href="<?= $item['link']; ?>" class="services-support-element">
                                <div class="services-support-element-icon">
                                    <img src="<?= $model->getThumb('faqimage', 'main', false, $item['image'][0]); ?>"
                                         alt="<?= Html::encode($item['name']); ?>">
                                </div>
                                <div class="services-support-element-name"><?= $item['name']; ?></div>
                                <div class="services-support-element-text"><?= $item['description']; ?></div>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if (!empty($pop)) { ?>
            <div id="services-type" class="sec services-type-promo-block">
                <div class="container wide">
                    <h2 class="services-promo-title with_button"><?= $model->block4_title ? $model->block4_title : 'Популярные виды услуг'; ?> </h2>
                    <div class="services-type-promo-slider owl-carousel owl-theme">
                        <?php foreach ($pop as $item) { ?>
                            <a href="<?= $service_type_page->getUrlPath() . '?service_types[]=' . $item->id; ?>"
                               class="services-type-promo-slide">
                                <div class="services-type-promo-slide-name"><?= $item->name; ?></div>
                                <div class="services-type-promo-slide-text"><?= $item->subtitle; ?></div>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?= $this->render('_index_slider', compact('model')); ?>

        <?php if (!empty($model->block5_title) || !empty($model->block5_text)) { ?>
            <div class="sec services-promo-banner2-block">
                <div class="container wide">
                    <div class="services-promo-banner2">
                        <?php if (!empty($model->block5_title)) { ?>
                            <div class="services-promo2-title"><?= $model->block5_title; ?></div><?php } ?>
                        <?php if (!empty($model->block5_text)) { ?>
                            <div class="services-promo2-text"><?= $model->block5_text; ?></div><?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>


        <?php if (!empty($cards)) { ?>
            <div id="services-promo-cards" class="sec services-promo-cards-block">
                <div class="container wide">
                    <div class="services-promo-cards">
                        <?php foreach ($cards as $item) { ?>
                            <div class="services-promo-card">
                                <img src="<?= $model->getThumb('cardsimage', 'main', false, $item['image'][0]); ?>"
                                     class="services-promo-card-icon1" alt="<?= Html::encode($item['name']); ?>">
                                <div class="services-promo-card-title"><?= $item['name']; ?></div>
                                <a href="<?= $item['link']; ?>" class="button"><?= $item['link_title']; ?></a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </main>

<?php
$js = <<<JS
$(document).ready(function(e) {
	var slider_square = $('.services-square-slider');
    if(slider_square.length){
        slider_square.owlCarousel({
            center: false,
            items: 4.7,
            loop: true,
            nav: true,
            dots: false,
            margin: 20,
            mouseDrag: true,
            touchDrag: true,
            navSpeed: 1300,
            responsive: {
                0: {
                    items: 2.2,
                    margin: 5,
                    nav: false
                },
                750: {
                    items: 3
                },
                1100: {
                    items: 4
                },
                1370: {
                    items: 4.5
                },
                1600: {
                    items: 4.7
                }
            }
        });
    }

    var slider_triple = $('.services-triple-slider');
    if(slider_triple.length){
        slider_triple.owlCarousel({
            center: false,
            items: 2.37,
            loop: true,
            nav: true,
            dots: false,
            margin: 20,
            mouseDrag: true,
            touchDrag: true,
            navSpeed: 1300,
            responsive: {
                0: {
                    items: 1.5,
                    margin: 5,
                    nav: false
                },
                750: {
                    items: 1.5
                },
                1100: {
                    items: 2.25
                },
                1450: {
                    items: 2.37
                }
            }
        });
    }

    if (window.innerWidth >= 1385) {
        var left = $('.container.wide').offset().left
        if(slider_triple.length){
            slider_triple.parent().css('padding-left',left);
            slider_triple.trigger('refresh.owl.carousel');
        }
        if(slider_square.length){
            slider_square.parent().css('padding-left',left);
            slider_square.trigger('refresh.owl.carousel');
        }
    }

    $(window).resize(function() {
        if (window.innerWidth >= 1385) {
            var left = $('.container.wide').offset().left
            if(slider_triple.length){
                slider_triple.parent().css('padding-left',left);
                slider_triple.trigger('refresh.owl.carousel');
            }
            if(slider_square.length){
                slider_square.parent().css('padding-left',left);
                slider_square.trigger('refresh.owl.carousel');
            }
        }
    });

    var slider_type = $('.services-type-promo-slider');
    if(slider_type.length){
        slider_type.owlCarousel({
            center: false,
            items: 3,
            loop: false,
            nav: true,
            dots: false,
            margin: 40,
            mouseDrag: true,
            touchDrag: true,
            navSpeed: 1300,
            responsive: {
                0: {
                    items: 1,
                    margin: 10
                },
                750: {
                    items: 2
                },
                1100: {
                    items: 3
                }
            }
        });
    }

    if (window.innerWidth <= 1100) {
        var slider_problem = $('.services-problem-slider');
        if(slider_problem.length){
            slider_problem.owlCarousel({
                center: false,
                items: 2,
                loop: false,
                nav: true,
                dots: false,
                margin: 10,
                mouseDrag: true,
                touchDrag: true,
                navSpeed: 1300,
                responsive: {
                    0: {
                        items: 1
                    },
                    768: {
                        items: 2
                    }
                }
            });
        }
    }
    
})
JS;

$this->registerJs($js);
?>