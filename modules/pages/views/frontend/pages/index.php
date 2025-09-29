<?php
$this->registerCssFile('/css/style-blog.css', ['depends' => [app\assets\AppAsset::class]]);
$this->registerJsFile('/js/main-blog.js', ['depends' => [app\assets\AppAsset::class]]);
?>
<div class="sec section-page-banner section-mainpage-banner">
    <div class="container wide">
        <div class="section-page-banner-info">
            <h1 class="section-page-banner-title"><?= $model->getNameForView(); ?></h1>
            <div class="section-page-banner-text"><?= $model->content; ?></div>
            <div class="buttons">
                <?php if (!empty($model->button1_text) && !empty($model->button1_link)) { ?>
                    <a href="<?= $model->button1_link; ?>" class="button"><?= $model->button1_text; ?></a>
                <?php } ?>
                <?php if (!empty($model->button2_text) && !empty($model->button2_link)) { ?>
                    <a href="<?= $model->button2_link; ?>" class="button gray"><?= $model->button2_text; ?></a>
                <?php } ?>
            </div>
        </div>
        <?= \app\modules\users\widgets\userslist\UsersListWidget::widget(['limit' => 4 * 4, 'items' => $model->expertsClickable, 'view' => 'string_slider']); ?>
    </div>
</div>
<main class="sec content_sec mainpage-content_sec">
    <?= $this->render('_index_slider', compact('model')); ?>
    <?php if ($model->help_show) { ?>
        <div class="sec section-page services-support-block">
            <div class="container wide">
                <div class="section-page-title"><?= $model->help_title; ?></div>
                <div class="services-support-list">
                    <?php if (!empty($model->expertshelp)) {
                        foreach ($model->expertshelp as $item) { ?>
                            <a href="<?= $item->link; ?>" class="services-support-element">
                                <div class="services-support-element-icon">
                                    <?php if ($item->image) { ?>
                                        <img src="<?= $item->getThumb('image', 'main'); ?>"
                                             alt="<?= str_replace('"', '&quot;', $item->name); ?>">
                                    <?php } ?>
                                </div>
                                <div class="services-support-element-name"><?= $item->name; ?></div>
                                <div class="services-support-element-text"><?= $item->description; ?></div>
                            </a>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if (!empty($model->homesocial)) { ?>
        <div class="sec section-page section-mainpage-categories gray-bg">
            <div class="container wide">
                <?php foreach ($model->homesocial as $socblock) { ?>
                    <div class="mainpage-category">
                        <div class="mainpage-category-info">
                            <h3 class="mainpage-category-title"><?= $socblock->name; ?></h3>
                            <div class="mainpage-category-text"><?= $socblock->content; ?></div>
                            <?php if (!empty($socblock->button_link)) { ?>
                                <div class="buttons">
                                    <a href="<?= $socblock->button_link; ?>"
                                       class="button"><?= $socblock->button_text ? $socblock->button_text : 'Подробнее'; ?></a>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="mainpage-category-img">
                            <?php if (!empty($socblock->image)) { ?>
                                <img src="<?= $socblock->image->src; ?>" alt="<?= $socblock->name; ?>">
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
    <?php if ($model->show_events) { ?>
        <?= \app\modules\events\widgets\mainpage\MainpageWidget::widget(['loop' => false, 'autoplay' => true, 'autoplayTimeout' => 5000]); ?>
    <?php } ?>
    <?php if ($model->plus_show) { ?>
        <div class="sec section-page section-join-us gray-bg">
            <div class="container wide">
                <div class="join-us-category">
                    <div class="join-us-category-img">
                        <?php if ($model->plus_image) { ?>
                            <img src="<?= $model->getThumb('plus_image', 'main'); ?>"
                                 alt="<?= str_replace('"', '&quot;', $model->plus_title); ?>">
                        <?php } ?>
                    </div>
                    <div class="join-us-category-info">
                        <div class="join-us-category-title"><?= $model->plus_title; ?></div>
                        <div class="join-us-category-text"><?= $model->plus_text; ?></div>
                        <?php if (!empty($model->pluses)) {
                            $pluses = $model->pluses;
                            usort($pluses, function ($a, $b) {
                                return $a['order'] <=> $b['order'];
                            });
                            ?>
                            <div class="check-list">
                                <?php foreach ($pluses as $plus) { ?>
                                    <div class="check-element"><?= $plus['name']; ?></div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <div class="buttons">
                            <?php if (!empty($model->plus_button_link) && !empty($model->plus_button_text)) { ?>
                                <a href="<?= $model->plus_button_link; ?>"
                                   class="button"><?= $model->plus_button_text; ?></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="sec section-page section-experts-academy">
        <div class="container wide">
            <h3 class="section-page-title">Уже работают с Академией</h3>
            <?= \app\modules\users\widgets\userslist\UsersListWidget::widget(['limit' => 12, 'items' => $model->expertsWorking, 'loop' => true, 'autoplay' => true, 'autoplayTimeout' => 5000]); ?>
        </div>
    </div>
    <div class="sec section-page section-experts-academy">
        <div class="container wide">
            <div class="services-promo-cards">
                <?php if (!empty($model->startwork_banner1_title)) { ?>
                    <div class="services-promo-card">
                        <?php if (!empty($model->startwork_banner1_image)) { ?>
                            <img src="<?= $model->getThumb('startwork_banner1_image', 'main'); ?>"
                                 class="services-promo-card-icon"
                                 alt="<?= str_replace('"', '&quot;', $model->startwork_banner1_title); ?>">
                        <?php } ?>
                        <div class="services-promo-card-title"><?= $model->startwork_banner1_title; ?></div>
                        <?php if (!empty($model->startwork_banner1_button_text) && !empty($model->startwork_banner1_button_link)) { ?>
                            <a href="<?= $model->startwork_banner1_button_link; ?>"
                               class="button"><?= $model->startwork_banner1_button_text; ?></a>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if (!empty($model->startwork_banner2_title)) { ?>
                    <div class="services-promo-card">
                        <?php if (!empty($model->startwork_banner2_image)) { ?>
                            <img src="<?= $model->getThumb('startwork_banner2_image', 'main'); ?>"
                                 class="services-promo-card-icon"
                                 alt="<?= str_replace('"', '&quot;', $model->startwork_banner2_title); ?>">
                        <?php } ?>
                        <div class="services-promo-card-title"><?= $model->startwork_banner2_title; ?></div>
                        <?php if (!empty($model->startwork_banner2_button_text) && !empty($model->startwork_banner2_button_link)) { ?>
                            <a href="<?= $model->startwork_banner2_button_link; ?>"
                               class="button"><?= $model->startwork_banner2_button_text; ?></a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?= \app\modules\service\widgets\mainpage\MainpageWidget::widget(['loop' => true, 'autoplay' => true, 'autoplayTimeout' => 5000, 'services_subtitle' => $model->services_subtitle]); ?>
    <?php if ($model->startwork_show) { ?>
        <div class="sec section-page section-corporate-offer section-corporate-offer-mainpage">
            <div class="container wide">
                <div class="section-page-title"><?= $model->startwork_title; ?></div>
                <?php if (!empty($model->startwork_steps)) {
                    $steps = $model->startwork_steps;
                    usort($steps, function ($a, $b) {
                        return $a['order'] <=> $b['order'];
                    }); ?>
                    <div class="corporate-offer-list default-slider-4 owl-carousel owl-theme" data-loop="true"
                         data-autoplay="true" data-timeout="5000">
                        <?php foreach ($steps as $key => $item) { ?>
                            <div class="corporate-offer-element">
                                <div class="corporate-offer-step"><?= str_pad(($key + 1), 2, '0', STR_PAD_LEFT); ?></div>
                                <div class="corporate-offer-title"><?= $item['name']; ?></div>
                                <div class="corporate-offer-text"><?= $item['descr']; ?></div>
                            </div>
                        <?php } ?>
                        <?php if (!empty($model->startwork_steps_last_title)) { ?>
                            <div class="corporate-offer-element-banner">
                                <div class="corporate-offer-banner-title"><?= $model->startwork_steps_last_title; ?></div>
                                <?php if (!empty($model->startwork_steps_last_button_link) && !empty($model->startwork_steps_last_button_text)) { ?>
                                    <a href="<?= $model->startwork_steps_last_button_link; ?>"
                                       class="button white"><?= $model->startwork_steps_last_button_text; ?></a>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>

    <?php /*<div class="sec section-page section-academy-smi gray-bg">
            <div class="container wide">
                <h2 class="section-page-title with_button">Академия в СМИ <a href="" class="button see_all">Смотреть все</a></h2>
                <div class="academy-smi-list default-slider-3 owl-carousel owl-theme" data-loop="true" data-autoplay="true" data-timeout="5000">
                    <a href="" class="academy-smi-element" target="_blank">
                        <div class="academy-smi-logo">
                            <img src="/img/smi/rbk.png" alt="">
                        </div>
                        <div class="academy-smi-title">Название статьи в публикации на сайте партнера</div>
                        <span class="academy-smi-date">12.08.2000</span>
                    </a>
                    <a href="" class="academy-smi-element" target="_blank">
                        <div class="academy-smi-logo">
                            <img src="/img/smi/rbk.png" alt="">
                        </div>
                        <div class="academy-smi-title">Название статьи в публикации на сайте партнера</div>
                        <span class="academy-smi-date">12.08.2000</span>
                    </a>
                    <a href="" class="academy-smi-element" target="_blank">
                        <div class="academy-smi-logo">
                            <img src="/img/smi/rbk.png" alt="">
                        </div>
                        <div class="academy-smi-title">Название статьи в публикации на сайте партнера</div>
                        <span class="academy-smi-date">12.08.2000</span>
                    </a>
                    <a href="" class="academy-smi-element" target="_blank">
                        <div class="academy-smi-logo">
                            <img src="/img/smi/rbk.png" alt="">
                        </div>
                        <div class="academy-smi-title">Название статьи в публикации на сайте партнера</div>
                        <span class="academy-smi-date">12.08.2000</span>
                    </a>
                    <div class="academy-smi-element-banner">
                        <div class="see-all-smi-title">Смотрите все статьи об Академии в СМИ</div>
                        <a href="" class="button white">Смотреть все</a>
                    </div>
                </div>
            </div>
    </div> */ ?>

    <?php if ($model->show_blog) { ?>
        <?= \app\modules\lenta\widgets\blog\mainpage\MainpageWidget::widget(['view' => 'mainpage', 'limit' => 7]); ?>
    <?php } ?>

    <?php if ($model->join_show) { ?>
        <div class="sec section-page section-personal-consultation">
            <div class="container wide">
                <div class="join_us_box">
                    <span class="join_us_bg" data-parallax></span>
                    <div class="join_us_box-title"><?= $model->join_title; ?></div>
                    <p><?= $model->join_text; ?></p>
                    <?php if (!empty($model->join_button_text) && !empty($model->join_button_link)) { ?>
                        <div class="join_us_button">
                            <a href="<?= $model->join_button_link; ?>"
                               class="button white"><?= $model->join_button_text; ?></a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
</main>