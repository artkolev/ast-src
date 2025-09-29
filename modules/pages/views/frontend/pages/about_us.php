<?php

use app\modules\pages\models\AboutUs;

/**
 * @var \yii\web\View $this
 * @var AboutUs $model
 */

?>

    <section class="sec section-page section-mission-academy">
        <div class="container wide">
            <div class="mission-academy-info-block">
                <h1 class="page-title"><?= $model->name; ?></h1>
                <div class="subheader"><?= $model->content; ?></div>
                <?php if (!empty($model->header_missions)) { ?>
                    <div class="mission-academy-goals">
                        <?php foreach ($model->header_missions as $header_mission) { ?>
                            <div class="mission-academy-goal"><?= $header_mission['name']; ?></div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
            <?php if (!empty($model->header_missions_slider)) { ?>
            <div
                    class="mission-academy-slider progress-dots progress-dots-blue progress-dots-center owl-carousel owl-theme"
                    data-autoplay="true" data-timeout="5000">
                <?php foreach ($model->header_missions_slider as $item) { ?>
                <?php
                $item = (object)$item;
                $itemImage = $model->getThumb('missionslider', 'main', false, ($item->person_image)[0] ?? null) ?? null;
                ?>
                <?php if (!empty($itemImage) && (bool)($item->visible)) { ?>
                <div class="mission-academy-slide"
                     style="background: linear-gradient(180deg, rgba(0, 0, 0, 0.00) 39.41%, rgba(0, 0, 0, 0.80) 77.76%), url(<?= $itemImage; ?>) center/cover no-repeat, lightgray 0px -0.043px / 100% 104.927% no-repeat;">
                    <?php if (!empty($item->person_link)) { ?>
                    <a href="<?= $item->person_link; ?>" class="mission-academy-slide-quote">
                        <?php } else { ?>
                        <div class="mission-academy-slide-quote">
                            <?php } ?>
                            <?= $item->person_text; ?>
                            <div class="mission-academy-slide-quote-author">
                                <div class="mission-academy-slide-quote-author-name"><?= $item->person; ?></div>
                                <div class="mission-academy-slide-quote-author-text"><?= $item->person_post; ?></div>
                            </div>
                            <?php if (!empty($item->person_link)) { ?>
                    </a>
                    <?php } else { ?>
                </div>
            <?php } ?>
            </div>
        <?php } ?>
        <?php } ?>
        </div>
        <?php } ?>
        <div class="services-promo-anchors services-promo-anchors-v2">
            <a href="#academy-experts" class="anchor">Почетные эксперты</a>
            <a href="#academy-experts_sovet" class="anchor">Ученый совет</a>
            <a href="#academy-participants" class="anchor">Участники</a>
            <a href="#academy-projects" class="anchor">Проекты</a>
            <a href="#academy-partnership" class="anchor">Стать экспертом</a>
        </div>
        </div>
    </section>

<?php if (!empty($model->key_metric_section)) { ?>
    <section class="sec section-page section-experts-stats gray-bg">
        <div class="container wide">
            <div class="experts-stats-list experts-stats-list-v2">
                <?php foreach ($model->key_metric_section as $item) { ?>
                    <?php if (!empty($item['value']) && !empty($item['name']) && (bool)$item['visible']) { ?>
                        <div class="experts-stats-element">
                            <div class="experts-stats-num"><?= $item['value']; ?></div>
                            <div class="experts-stats-title"><?= $item['name']; ?></div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>

<?php if (!empty($model->after_key_metric_text)) { ?>
    <section class="sec section-page section-academy-text gray-bg">
        <div class="container wide">
            <div class="text-block"><?= $model->after_key_metric_text; ?></div>
        </div>
    </section>
<?php } ?>

<?php if (!empty($model->banner2_title)) { ?>
    <section id="academy-partnership"
             class="sec section-page section-personal-consultation section-personal-consultation-v2 gray-bg">
        <div class="container wide">
            <div class="join_us_box">
                <span class="join_us_bg" data-parallax="" style="background-position: 50% -80px;"></span>
                <div class="join_us_box-title"><?= $model->banner2_title; ?></div>
                <p><?= $model->banner2_text; ?></p>
                <div class="join_us_button">
                    <?php foreach ($model->banner2_buttons as $button) { ?>
                        <?php if ($button['visible']) { ?>
                            <a href="<?= $button['link']; ?>" class="button white"><?= $button['name']; ?></a>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
<?php } ?>

<?php if (!empty($model->ast_today_cards)) { ?>
    <section class="sec section-page services-support-block">
        <div class="container wide">
            <h2 class="section-page-title"><?= $model->ast_today_title; ?></h2>
            <div class="services-support-list">
                <?php foreach ($model->ast_today_cards as $item) { ?>
                    <?php
                    $item = (object)$item;
                    $itemImage = $model->getThumb('asttodaycards', 'main', false, ($item->card_image)[0] ?? null) ?: 'img/f-icons/book.svg';
                    ?>
                    <?php if ((bool)$item->visible && !empty($item->card_name)) { ?>
                        <a href="<?= $item->card_link; ?>" class="services-support-element">
                            <div class="services-support-element-icon">
                                <img src="<?= $itemImage; ?>" alt="">
                            </div>
                            <div class="services-support-element-name"><?= $item->card_name; ?></div>
                            <div class="services-support-element-text"><?= $item->card_text; ?></div>
                        </a>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>

<?= $this->render(
        '__about_us_expert_card',
        [
                'title' => $model->ast_experts_title,
                'anchor' => 'academy-experts',
                'text' => null,
                'type' => $model->ast_experts_card_type,
                'list' => $model->astExpertsCards,
        ]
); ?>

<?= $this->render(
        '__about_us_expert_card',
        [
                'title' => $model->ast_council_title,
                'anchor' => 'academy-experts_sovet',
                'text' => $model->ast_council_text,
                'type' => $model->ast_council_card_type,
                'list' => $model->astCouncilsCards,
        ]
); ?>

<?php if (!empty($model->banner_title)) { ?>
    <section id="academy-partnership"
             class="sec section-page section-personal-consultation section-personal-consultation-v2 gray-bg">
        <div class="container wide">
            <div class="join_us_box">
                <span class="join_us_bg" data-parallax="" style="background-position: 50% -80px;"></span>
                <div class="join_us_box-title"><?= $model->banner_title; ?></div>
                <p><?= $model->banner_text; ?></p>
                <div class="join_us_button">
                    <?php foreach ($model->banner_buttons as $button) { ?>
                        <?php if ($button['visible']) { ?>
                            <a href="<?= $button['link']; ?>" class="button white"><?= $button['name']; ?></a>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
<?php } ?>

<?php if (!empty($model->ast_members_cards)) { ?>
    <section class="sec section-page services-support-block" id="academy-participants">
        <div class="container wide">
            <h2 class="section-page-title"><?= $model->ast_members_title; ?></h2>
            <div class="services-support-list">
                <?php foreach ($model->ast_members_cards as $item) { ?>
                    <?php
                    $item = (object)$item;
                    $itemImage = $model->getThumb('astmemberscards', 'main', false, ($item->card_image)[0] ?? null) ?: 'img/f-icons/book.svg';
                    ?>
                    <?php if ((bool)$item->visible && !empty($item->card_name)) { ?>
                        <a href="<?= $item->card_link; ?>" class="services-support-element">
                            <div class="services-support-element-icon">
                                <img src="<?= $itemImage; ?>" alt="">
                            </div>
                            <div class="services-support-element-name"><?= $item->card_name; ?></div>
                            <div class="services-support-element-text"><?= $item->card_text; ?></div>
                        </a>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>

<?php if (!empty($model->ast_projects_cards)) { ?>
    <section id="academy-projects" class="sec section-page academy_slider2_sec section-academy-slider2 gray-bg">
        <div class="container wide">
            <h2 class="section-page-title"><?= $model->ast_projects_title; ?></h2>
            <div class="academy_slider2 owl-carousel owl-theme" data-autoplay="true" data-timeout="5000">
                <?php foreach ($model->ast_projects_cards as $item) { ?>
                    <?php
                    $item = (object)$item;
                    $itemFile = $model->getFile('astprojectscardsfile', false, ($item->card_video)[0] ?? null);
                    ?>
                    <?php if ((bool)$item->visible) { ?>
                        <?php if ($item->card_type === AboutUs::FILE_TYPE_VIDEO && $itemFile) { ?>
                            <a href="<?= $item->card_link; ?>" target="_blank" class="academy_slide2">
                                <div class="academy_slide2-video">
                                    <video src="<?= $itemFile; ?>" class="slide-video" poster="" preload="false"
                                           loop="true" playsinline muted></video>
                                </div>
                            </a>
                        <?php } ?>
                        <?php if ($item->card_type === AboutUs::FILE_TYPE_IMAGE) { ?>
                            <a href="<?= $item->card_link; ?>" target="_blank" class="academy_slide2">
                                <div class="academy_slide2-img">
                                    <img
                                            data-src="<?= $model->getThumb('astprojectscards', 'main', false, ($item->card_image)[0] ?? null); ?>"
                                            alt="" class="slide-1345 owl-lazy">
                                    <img
                                            data-src="<?= $model->getThumb('astprojectscardstablet', 'main', false, ($item->card_image_tablet)[0] ?? null); ?>"
                                            alt="" class="slide-1024 owl-lazy">
                                    <img
                                            data-src="<?= $model->getThumb('astprojectscardsmobile', 'main', false, ($item->card_image_mobile)[0] ?? null); ?>"
                                            alt="" class="slide-630 owl-lazy">
                                    <img
                                            data-src="<?= $model->getThumb('astprojectscardsmobilemin', 'main', false, ($item->card_image_mobile_min)[0] ?? null); ?>"
                                            alt="" class="slide-300 owl-lazy">
                                </div>
                            </a>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>

<?php if (!empty($model->partners_title)) { ?>
    <section class="sec section-page section-personal-consultation section-partnership">
        <div class="container wide">
            <div class="join_us_box join_us_box-v2">
                <span class="join_us_bg join_us_bg-v2"></span>
                <div class="join_us_box-title"><?= $model->partners_title; ?></div>
                <p><?= $model->partners_text; ?></p>
                <div class="join_us_button">
                    <?php foreach ($model->partners_buttons as $button) { ?>
                        <a href="<?= $button['link']; ?>" class="button"><?= $button['name']; ?></a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
<?php } ?>

<?php if (!empty($model->partners_slider_cards)) { ?>
    <section class="sec section-page section-partners gray-bg">
        <div class="container wide">
            <h2 class="section-page-title"><?= $model->partners_slider_title; ?></h2>
            <div class="partners-slider owl-carousel owl-theme" data-autoplay="true" data-timeout="5000">
                <?php foreach ($model->partners_slider_cards as $item) { ?>
                    <?php
                    $item = (object)$item;
                    $itemImage = $model->getThumb('partnersslidercards', 'main', false, ($item->card_image)[0] ?? null);
                    ?>
                    <?php if ((bool)$item->visible && $itemImage) { ?>
                        <div class="partners-slide">
                            <a <?php if (!empty($item->card_link)) { ?>href="<?= $item->card_link; ?>" target="_blank"
                               <?php } ?>class="partners-slide-img">
                                <img src="<?= $itemImage; ?>" alt="<?= $item->card_name; ?>">
                            </a>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>