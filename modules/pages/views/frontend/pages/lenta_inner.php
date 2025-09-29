<?php

use app\helpers\MainHelper;
use app\modules\lenta\models\LentaInnerSlider;
use app\modules\pages\models\Eventspage;
use app\modules\service\models\Service;

?>
    <main class="sec content_sec section-blog-page">
        <div class="container wide">
            <?= $this->render('_social_box_lenta', ['model' => $model]); ?>
            <div class="blog-page-content">
                <div class="blog-page-info-top-mobile">
                    <a href="<?= $return_url; ?>" class="blog-back-button"><?= $return_title; ?></a>
                </div>
                <div class="blog-page-info-top">
                    <a href="<?= $return_url; ?>" class="blog-back-button"><?= $return_title; ?></a>
                    <!-- <a href="#!" class="blog-page-main-tag">Исследования</a> -->
                    <div class="blog-page-date"><?= \Yii::$app->formatter->asDate($model->published, 'php:d.m.Y'); ?></div>
                    <!--<div class="blog-viewed"><?= $model->views; ?></div>-->
                </div>
                <h1 class="page-title"><?= $model->getNameForView(); ?></h1>

                <p class="subheader" style="display: none;"><?= $model->subtitle; ?></p>

                <div class="blog-page-poster-wrapper">
                    <?php if (!empty($model->image) && !$model->hide_image_inner) { ?>
                        <div class="blog-page-poster">
                            <img src="<?= $model->getThumb('image', 'page_inner'); ?>"
                                 alt="<?= str_replace('"', '&quot;', $model->name); ?>" loading="lazy">
                        </div>
                    <?php } ?>

                    <?php if (!empty($side_users_with_tags)) { ?>
                        <div class="blog-right-column desktop-visible">
                            <?= $side_users_with_tags; ?>
                        </div>
                    <?php } ?>

                </div>

                <?php if (!empty($model->video_link)) { ?>
                    <?php if (!empty($model->videoimage)) { ?>
                        <div class="youtube_preview">
                            <?= $model->video_name ? '<div class="podpis">' . $model->video_name . '</div>' : ''; ?>
                            <?= MainHelper::getMultiEmbededAddress($model->video_link, image_url: $model->videoimage ? $model->getThumb('videoimage', 'main') : '', image_name: $model->video_name ?? ''); ?>
                        </div>
                    <?php } else { ?>
                        <div class="youtube_preview">
                            <?= ($model->video_name ? '<div class="podpis">' . $model->video_name . '</div>' : ''); ?>
                            <?= MainHelper::getMultiEmbededAddress($model->video_link); ?>
                        </div>
                        <h4><?= $model->video_name; ?></h4>
                    <?php } ?>
                <?php } ?>

                <?php if (!empty($model->video1_link)) { ?>
                    <?php if (!empty($model->video1)) { ?>
                        <div class="youtube_preview">
                            <?= $model->video1_name ? '<div class="podpis">' . $model->video1_name . '</div>' : ''; ?>
                            <?= MainHelper::getMultiEmbededAddress($model->video1_link, image_url: $model->video1 ? $model->getThumb('video1', 'main') : '', image_name: $model->video1_name ?? ''); ?>
                        </div>
                    <?php } else { ?>
                        <div class="youtube_preview">
                            <?= ($model->video1_name ? '<div class="podpis">' . $model->video1_name . '</div>' : ''); ?>
                            <?= MainHelper::getMultiEmbededAddress($model->video1_link); ?>
                        </div>
                        <h4><?= $model->video1_name; ?></h4>
                    <?php } ?>
                <?php } ?>

                <?= $content; ?>

                <?php if (!empty($model->video2_link)) { ?>
                    <?php if (!empty($model->video2)) { ?>
                        <div class="youtube_preview">
                            <?= $model->video2_name ? '<div class="podpis">' . $model->video2_name . '</div>' : ''; ?>
                            <?= MainHelper::getMultiEmbededAddress($model->video2_link, image_url: $model->video2 ? $model->getThumb('video2', 'main') : '', image_name: $model->video2_name ?? ''); ?>
                        </div>
                    <?php } else { ?>
                        <div class="youtube_preview">
                            <?= ($model->video2_name ? '<div class="podpis">' . $model->video2_name . '</div>' : ''); ?>
                            <?= MainHelper::getMultiEmbededAddress($model->video2_link); ?>
                        </div>
                        <h4><?= $model->video2_name; ?></h4>
                    <?php } ?>
                <?php } ?>

                <?php if (!empty($model->report)) { ?>
                    <div class="blog-page-text-wrapper">
                        <?php if (!empty($model->report_title)) { ?>
                            <h2><?= $model->report_title; ?></h2>
                        <?php } ?>
                        <div class="gallery_box">
                            <?php foreach ($model->report as $key => $image) { ?>
                                <a href="<?= $image->src; ?>" data-fancybox="gallery" class="gallery_item"><img
                                            src="<?= $model->getThumb('report', 'main', $key); ?>"
                                            alt="<?= str_replace('"', '&quot;', $image->name); ?>" loading="lazy"/></a>
                            <?php } ?>
                        </div>
                        <?php if (!empty($model->report_sub)) { ?>
                            <div class="podpis"><?= $model->report_sub; ?></div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php $slider_items_counter = 0; ?>
                <?php if (!empty($lentaslider)) { ?>
                    <div class="blog-page-text-wrapper">
                        <div class="blog-page-banner-slider owl-carousel owl-theme" data-autoplay="true"
                             data-timeout="5000">
                            <?php foreach ($lentaslider as $slide) {
                                $item = $slide->currentElement;
                                if (!empty($item)) {
                                    $slider_items_counter++;
                                    if ($slide->type == LentaInnerSlider::SLIDER_TYPE_EVENTS) { ?>
                                        <div class="blog-page-banner-slide">
                                            <div class="blog-page-banner-slide-info">
                                                <div class="blog-page-banner-slide-title"><?= $item->name; ?></div>
                                                <div class="blog-page-banner-slide-text"><?= \Yii::$app->formatter->asDate($item->event_date, 'php:j F'); ?>
                                                    - <?= \Yii::$app->formatter->asDate($item->event_date_end, 'php:j F'); ?></div>
                                                <a href="<?= $item->getUrlPath(); ?>" class="button" rel="nofollow">Посмотреть</a>
                                            </div>
                                            <div class="blog-page-banner-slide-img">
                                                <img src="<?= $item->getThumb('image', 'main'); ?>"
                                                     alt="<?= str_replace('"', '&quot;', $item->name); ?>">
                                            </div>
                                        </div>
                                    <?php } elseif ($slide->type == LentaInnerSlider::SLIDER_TYPE_SERVICE) { ?>
                                        <div class="services-expert services-expert3">
                                            <div class="services-expert-wrapper">
                                                <div class="services-expert-text">
                                                    <div class="services-expert-img-wrapper">
                                                        <a href="<?= $item->user->getUrlPath(); ?>"
                                                           class="services-expert-img" rel="nofollow">
                                                            <img src="<?= $item->user->profile->getThumb('image', 'main'); ?>"
                                                                 alt="<?= $item->user->profile->halfname; ?>"
                                                                 loading="lazy">
                                                        </a>
                                                        <a href="<?= $item->user->getUrlPath(); ?>"
                                                           class="services-expert-name"
                                                           rel="nofollow"><?= $item->user->profile->halfname; ?></a>
                                                    </div>
                                                    <div class="services-expert-directs"><?= $item->direction->name; ?></div>
                                                </div>
                                            </div>
                                            <div class="services-expert-info">
                                                <a href="<?= $item->getUrlPath(); ?>" class="services-expert-service"
                                                   rel="nofollow"><?= $item->name; ?></a>
                                                <div class="services-expert-price-wrapper">
                                                    <?php if ($item->type == Service::TYPE_TYPICAL) { ?>
                                                        <div class="services-expert-price <?php if ($item->old_price && $item->old_price > $item->price) { ?>have-old-price<?php } ?>"><?= number_format($item->price, 0, '', '&nbsp;'); ?>
                                                            ₽ <?php if ($item->old_price && $item->old_price > $item->price) { ?>
                                                                <span class="services-expert-old-price"><?= number_format($item->old_price, 0, '', '&nbsp;'); ?>
                                                                ₽</span><?php } ?></div>
                                                    <?php } else { ?>
                                                        <div class="services-expert-price arrangement">По запросу</div>
                                                    <?php } ?>
                                                    <a href="<?= $item->getUrlPath(); ?>" class="button-o small small2"
                                                       rel="nofollow">Подробнее</a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }
                                }
                            } ?>
                            <?php if ($slider_items_counter == 0) {
                                $events_catalog = Eventspage::find()->where(['model' => Eventspage::class, 'visible' => 1])->one();
                                ?>
                                <div class="blog-page-banner-slide">
                                    <div class="blog-page-banner-slide-info">
                                        <div class="blog-page-banner-slide-title">Смотреть каталог <br>мероприятий</div>
                                        <div class="blog-page-banner-slide-text"></div>
                                        <a href="<?= $events_catalog->getUrlPath(); ?>" class="button" rel="nofollow">Посмотреть
                                            все</a>
                                    </div>
                                    <div class="blog-page-banner-slide-img">
                                        <img src="/img/events-default.png" alt="">
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <div class="blog-page-info-bottom">
                    <?= $this->render('_social_box_lenta_mobile', ['model' => $model, 'text' => 'Поделиться', 'title' => $model->getNameForView()]); ?>

                    <?php if (!empty($side_users_with_tags)) { ?>
                        <div class="blog-right-column mobile-visible">
                            <?= $side_users_with_tags; ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </main>

<?php if (!empty($same_type)) { ?>
    <section class="sec content_sec section-blog-page2">
        <div class="container wide container-slider">
            <div class="section-blog-page-title2 with_button"><?= $same_type_title; ?> <?php if (!empty($same_type_url)) { ?>
                    <a href="<?= $same_type_url; ?>" class="button see_all">Смотреть все</a><?php } ?></div>
            <div class="blog-page-4card-slider owl-carousel owl-theme" data-loop="true" data-autoplay="true"
                 data-timeout="5000">
                <?php foreach ($same_type as $item) { ?>
                    <a href="<?= $item->getUrlPath(); ?>" class="blog-page-4card-slide" rel="nofollow">
                        <div class="blog-page-4card-slide-img">
                            <img src="<?= $item->getThumb('image', 'main'); ?>"
                                 alt="<?= str_replace('"', '&quot;', $item->name); ?>">
                        </div>
                        <div class="blog-page-4card-slide-info">
                            <div class="blog-page-4card-slide-title"><?= $item->name; ?></div>
                            <div class="blog-page-4card-slide-text-wrapper">
                                <div class="blog-page-4card-slide-date"><?= \Yii::$app->formatter->asDate($item->published, 'php:d.m.Y'); ?></div>
                                <!--<div class="blog-page-4card-slide-viewed"><?= $item->views; ?></div>-->
                            </div>
                        </div>
                    </a>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>

<?php if (!empty($more_items)) { ?>
    <section class="sec content_sec section-blog-page2">
        <div class="container wide container-slider">
            <div class="section-blog-page-title2 with_button">Еще в Ленте <a href="<?= $lenta_url; ?>"
                                                                             class="button see_all">Смотреть все</a>
            </div>
            <div class="blog-page-4card-slider2 owl-carousel owl-theme" data-loop="true" data-autoplay="true"
                 data-timeout="5000">
                <?php foreach ($more_items as $item) { ?>
                    <a href="<?= $item->getUrlPath(); ?>" class="blog-page-4card-slide2 blog-page-4card-slide2-with-bg"
                       rel="nofollow">
                        <div class="blog-page-4card-slide2-bg"
                             style="background: url(<?= $item->getThumb('image', 'main'); ?>) center/cover no-repeat;"></div>
                        <?php if ($item->author) { ?>
                            <div class="blog-page-4card-slide2-author-wrapper">
                                <div class="blog-page-4card-slide2-author-img">
                                    <img src="<?= $item->author->profile->getThumb('image', 'main'); ?>"
                                         alt="<?= $item->author->profile->halfname; ?>">
                                </div>
                                <div class="blog-page-4card-slide2-author"><?= $item->author->profile->halfname; ?></div>
                            </div>
                        <?php } else { ?>
                            <div class="blog-page-4card-slide2-author-wrapper">
                                <div class="blog-page-4card-slide2-author-img">
                                    <img src="/img/blog-page/academy.jpg"
                                         alt="<?= str_replace('"', '&quot;', $item->name); ?>">
                                </div>
                                <div class="blog-page-4card-slide2-author">Академия</div>
                            </div>
                        <?php } ?>
                        <div class="blog-page-4card-slide2-info">
                            <div class="blog-page-4card-slide2-title"><?= $item->name; ?></div>
                            <div class="blog-page-4card-slide2-text-wrapper">
                                <div class="blog-page-4card-slide2-date"><?= \Yii::$app->formatter->asDate($item->published, 'php:d.m.Y'); ?></div>
                                <!--<div class="blog-page-4card-slide2-viewed"><?= $item->views; ?></div>-->
                            </div>
                        </div>
                    </a>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>

<?php if (!empty($category_tags)) { ?>
    <section class="sec content_sec section-blog-page2">
        <div class="container wide">
            <div class="services-category-list">
                <?php foreach ($category_tags as $i => $item) { ?>
                    <a href="<?= $return_url . '?tag=' . urlencode($item->name); ?>"
                       class="services-category"><?= $item->name; ?></a>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>

<?php if (!empty($ads)) { ?>
    <section class="sec content_sec section-blog-page2">
        <div class="container wide">
            <div class="blog-page-long-banner">
                <?php foreach ($ads as $item) { ?>
                    <?php if (!empty($item->link)) { ?><a href="<?= $item->link; ?>" target="_blank" rel="nofollow"><?php } ?>
                    <img src="<?= $item->getThumb('image', 'main'); ?>" alt="" class="visible-over650" loading="lazy">
                    <img src="<?= $item->getThumb('image_mobile', 'main'); ?>" alt="" class="visible-less650"
                         loading="lazy">
                    <?php if (!empty($item->link)) { ?></a><?php } ?>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>
<?php
$this->registerJsFile('/js/main-blog.js', ['depends' => [app\assets\AppAsset::class]]);
$this->registerCssFile('/css/style-blog.css', ['depends' => [app\assets\AppAsset::class]]);
?>