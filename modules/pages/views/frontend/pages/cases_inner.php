<?php

use app\helpers\MainHelper;
use app\modules\cases\models\Cases;
use app\modules\users\models\UserAR;
use yii\helpers\Html;

$blocks = MainHelper::cleanInvisibleMultifield($model->blocks);
$cards = MainHelper::cleanInvisibleMultifield($model->cards);
$organizers = MainHelper::cleanInvisibleMultifield($model->organizers);
$teams = MainHelper::cleanInvisibleMultifield($model->teams);
$partners = MainHelper::cleanInvisibleMultifield($model->partners);
$infoPartners = MainHelper::cleanInvisibleMultifield($model->info_partners);
$banners = MainHelper::cleanInvisibleMultifield($model->banners);
$materials = $model->materialsObjects;
?>
    <section class="sec section-project">
        <div class="container wide">
            <div class="section-project-bg"
                 style="background: linear-gradient(90deg, rgba(51, 51, 51, 0.8) 1.21%, rgba(51, 51, 51, 0.2) 100%), url(<?= $model->getThumb('back_image', 'main'); ?>) center/cover no-repeat;"></div>
            <div class="section-project-info">
                <?= $this->render('_social_box_cases', ['model' => $model]); ?>
                <div class="section-project-date"><?= $model->getProjectDateForView(); ?></div>
                <h1 class="section-project-title"><?= $model->name; ?></h1>
                <h2 class="section-project-subtitle"><?= $model->description; ?></h2>
                <?php if ((!empty($model->project_button_link) && !empty($model->project_button_name)) || (!empty($model->project_button2_link) && !empty($model->project_button2_name))) { ?>
                    <div class="section-project-buttons">
                        <?php if (!empty($model->project_button_link) && !empty($model->project_button_name)) {
                            echo Html::a($model->project_button_name, $model->project_button_link, ['class' => 'button yellow']);
                        } ?>
                        <?php if (!empty($model->project_button2_link) && !empty($model->project_button2_name)) {
                            echo Html::a($model->project_button2_name, $model->project_button2_link, ['class' => 'button white']);
                        } ?>
                    </div>
                <?php } ?>
                <?= $this->render('_social_box_cases_mobile', ['model' => $model]); ?>
            </div>
        </div>
    </section>

    <section class="sec section-page pb0">
        <div class="container wide">
            <div class="index_video-area ver-3">
                <div class="index_video-right">
                    <?php if (!empty($model->video_link)) { ?>
                        <div class="youtube_preview mb0">
                            <a href="<?= $model->video_link; ?>" data-fancybox class="youtube_link">
                                <?= Html::img($model->getThumb('video_image', 'main'), ['alt' => $model->video_title, 'loading' => 'lazy']); ?>
                            </a>
                        </div>
                    <?php } elseif (!empty($model->video_image)) { ?>
                        <?= Html::img($model->getThumb('video_image', 'main'), ['alt' => $model->video_title, 'loading' => 'lazy']); ?>
                    <?php } ?>
                </div>
                <article class="index_video-left">
                    <div class="text-content">
                        <?php if (!empty($model->video_title)) { ?>
                            <h2><?= $model->video_title; ?></h2>
                        <?php } ?>
                        <?php if (!empty($model->video_descr)) { ?>
                            <p><?= $model->video_descr; ?></p>
                        <?php } ?>
                    </div>
                </article>
            </div>

        </div>
    </section>

<?php if (!empty($blocks)) { ?>
    <?php foreach ($blocks as $item) { ?>
        <?php if (!empty($item['color'])) { ?>
            <section class="sec section-page section-banner-color">
                <div class="container wide">
                    <div class="banner-color" style="background-color: #<?= $item['color']; ?>;">
                        <div class="banner-color-text" style="color: #<?= $item['color_text']; ?>;">
                            <?= $item['description']; ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php } else { ?>
            <section class="sec section-page pb0">
                <div class="container wide">
                    <h3 class="section-page-title text-left"><?= $item['name']; ?></h3>
                    <div class="text-content" style="color: #<?= $item['color_text']; ?>;">
                        <p>
                            <?= $item['description']; ?>
                        </p>
                    </div>
                    <div class="list-goals">
                        <?php foreach ($item['bullets'] as $bullet) { ?>
                            <div class="list-goal" style="color: #<?= $item['color_text']; ?>;">
                                <div class="list-goal-bullet"
                                     style="background-color: #<?= $item['color_bullets']; ?>;"></div>
                                <?= $bullet; ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </section>
        <?php } ?>
    <?php } ?>
<?php } ?>

<?php if (!empty($cards)) { ?>
    <section class="sec section-page section-cards pt0 pb0">
        <div class="container wide">
            <div class="cards big-cards">
                <?php foreach ($cards as $item) { ?>
                    <div class="card">
                        <div class="card-img">
                            <?= Html::img($model->getThumb('card_image', 'main', false, $item['image'][0]), ['alt' => $item['name'], 'loading' => 'lazy']); ?>
                        </div>
                        <div class="card-name"><?= $item['name']; ?></div>
                        <div class="card-text"><?= $item['text']; ?></div>
                        <?php if (!empty($item['link']) && !empty($item['link_title'])) { ?>
                            <div class="card-buttons">
                                <a href="<?= $item['link']; ?>" class="button"><?= $item['link_title']; ?></a>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>

<?php if (!empty($banners)) { ?>
    <section class="sec section-page academy_slider2_sec section-academy-slider2">
        <div class="container wide">
            <div class="academy_slider2 owl-carousel owl-theme" data-autoplay="true" data-timeout="3000">
                <?php foreach ($banners

                as $item) { ?>
                <?php
                $item = (object)$item;
                $itemFile = $model->getFile('banners_file', false, ($item->video)[0] ?? null);
                ?>
                <?php if ($item->type === Cases::FILE_TYPE_VIDEO && $itemFile) { ?>
                <?php if (!empty($item->link)) { ?>
                <a href="<?= $item->link; ?>" target="_blank" class="academy_slide2">
                    <?php } else { ?>
                    <div class="academy_slide2">
                        <?php } ?>
                        <div class="academy_slide2-video">
                            <video src="<?= $itemFile; ?>" class="slide-video" poster="" preload="false"
                                   loop="true" playsinline muted></video>
                        </div>
                        <?php if (!empty($item->link)) { ?>
                </a>
                <?php } else { ?>
            </div>
            <?php } ?>
            <?php } ?>
            <?php if ($item->type === Cases::FILE_TYPE_IMAGE) { ?>
            <?php if (!empty($item->link)) { ?>
            <a href="<?= $item->link; ?>" target="_blank" class="academy_slide2">
                <?php } else { ?>
                <div class="academy_slide2">
                    <?php } ?>
                    <div class="academy_slide2-img">
                        <img
                                data-src="<?= $model->getThumb('banners_image', 'main', false, ($item->image)[0] ?? null); ?>"
                                alt="" class="slide-1345 owl-lazy">
                        <img
                                data-src="<?= $model->getThumb('bannerstablet_image', 'main', false, ($item->image_tablet)[0] ?? null); ?>"
                                alt="" class="slide-1024 owl-lazy">
                        <img
                                data-src="<?= $model->getThumb('bannersmobile_image', 'main', false, ($item->image_mobile)[0] ?? null); ?>"
                                alt="" class="slide-630 owl-lazy">
                        <img
                                data-src="<?= $model->getThumb('bannersmobilemin_image', 'main', false, ($item->image_mobile_min)[0] ?? null); ?>"
                                alt="" class="slide-300 owl-lazy">
                    </div>
                    <?php if (!empty($item->link)) { ?>
            </a>
            <?php } else { ?>
        </div>
        <?php } ?>
        <?php } ?>
        <?php } ?>
        </div>
        </div>
    </section>
<?php } ?>


<?php if (!empty($teams)) { ?>
    <section class="sec section-page section-speakers">
        <div class="container wide">
            <h3 class="section-page-title text-left"><?= !empty($model->teams_title) ? $model->teams_title : 'Команда проекта'; ?></h3>
            <div class="text-content">
                <p>
                    <?= $model->teams_descr; ?>
                </p>
            </div>
            <div class="speakers-block">
                <div class="speakers-list">
                    <?php foreach ($teams as $i => $item) { ?>
                        <?php if ((int)$item['user_id'] > 0) { ?>
                            <?php $user = UserAR::getUserById($item['user_id']); ?>
                            <?php if (!empty($user)) { ?>
                                <a href="#speaker_<?= $i; ?>" class="speaker-element" data-fancybox>
                                    <object>
                                        <?= Html::a(Html::img($user->profile->getThumb('image', 'profile'), ['alt' => $user->profile->fullname]), $user->getUrlPath(), ['class' => 'speaker-element-img']); ?>
                                        <?= Html::a($user->profile->getHalfname(), $user->getUrlPath(), ['class' => 'speaker-element-name', 'target' => '_blank']); ?>
                                        <?php if (!empty($item['content'])) { ?>
                                            <div class="speaker-element-text"><?= $item['content']; ?></div>
                                        <?php } else { ?>
                                            <div class="speaker-element-text"><?= $user->profile->about_myself; ?></div>
                                        <?php } ?>
                                    </object>
                                </a>
                            <?php } ?>
                        <?php } else { ?>
                            <a href="#speaker_<?= $i; ?>" class="speaker-element" data-fancybox>
                                <object>
                                    <div class="speaker-element-img">
                                        <?= Html::img($model->getThumb('team_image', 'main', false, $item['image'][0]), ['alt' => $item['fio'], 'loading' => 'lazy']); ?>
                                    </div>
                                    <a href="" class="speaker-element-name"><?= $item['fio']; ?></a>
                                    <div class="speaker-element-text"><?= $item['content']; ?></div>
                                </object>
                            </a>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
    <?php foreach ($teams as $i => $item) { ?>
        <?php if ((int)$item['user_id'] > 0) { ?>
            <?php $user = UserAR::getUserById($item['user_id']); ?>
            <?php if (!empty($user)) { ?>
                <div id="speaker_<?= $i; ?>" class="modal modal-speaker">
                    <div class="modal_content">
                        <a href="#" class="modal_close" data-fancybox-close>x</a>
                        <div class="modal-speaker-wrapper">
                            <div class="modal-speaker-img">
                                <?= Html::a(Html::img($user->profile->getThumb('image', 'profile'), ['alt' => $user->profile->fullname]), $user->getUrlPath(), ['class' => 'expert_item-img']); ?>
                            </div>
                            <div class="modal-speaker-info">
                                <!-- если нет ссылки, то div -->
                                <?= Html::a($user->profile->getHalfname('<br>'), $user->getUrlPath(), ['class' => 'modal-speaker-name', 'target' => '_blank']); ?>
                                <?php if (!is_null($user->directionM) && !$user->directionM->stels_direct) { ?>
                                    <div class="modal-speaker-status"><?= $user->directionM->name; ?></div>
                                <?php } ?>
                            </div>
                        </div>
                        <?php if (!empty($item['content'])) { ?>
                            <div class="modal-speaker-text"><?= $item['content']; ?></div>
                        <?php } else { ?>
                            <div class="modal-speaker-text"><?= $user->profile->about_myself; ?></div>
                        <?php } ?>
                        <a class="modal-speaker-link-main-site" target="_blank" href="<?= $user->getUrlPath(); ?>">Подробнее
                            об эксперте</a>
                    </div>
                    <div class="modal_overlay"></div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div id="speaker_<?= $i; ?>" class="modal modal-speaker">
                <div class="modal_content">
                    <a href="#" class="modal_close" data-fancybox-close>x</a>
                    <div class="modal-speaker-wrapper">
                        <div class="modal-speaker-img">
                            <?= Html::img($model->getThumb('team_image', 'main', false, $item['image'][0]), ['alt' => $item['fio'], 'loading' => 'lazy']); ?>
                        </div>
                        <div class="modal-speaker-info">
                            <div class="modal-speaker-name"><?= $item['fio']; ?></div>
                            <div class="modal-speaker-status"><?= $item['content']; ?></div>
                        </div>
                    </div>
                    <div class="modal-speaker-text"><?= $item['content']; ?></div>
                </div>
                <div class="modal_overlay"></div>
            </div>
        <?php } ?>
    <?php } ?>
<?php } ?>

<?php if (!empty($organizers)) { ?>
    <section class="sec section-page gray-bg">
        <div class="container wide">
            <h3 class="section-page-title text-left"><?= !empty($model->organizers_title) ? $model->organizers_title : 'Соорганизаторы'; ?></h3>
            <div class="text-content">
                <p>
                    <?= $model->organizers_descr; ?>
                </p>
            </div>
            <div class="partners">
                <?php foreach ($organizers as $key => $item) { ?>
                    <a href="#modal_partner_<?= $key; ?>" data-fancybox class="partner">
                        <div class="partner-img">
                            <?= Html::img($model->getThumb('organizer_image', 'main', false, $item['image'][0]), ['alt' => $item['name'], 'loading' => 'lazy']); ?>
                        </div>
                        <div class="partner-info">
                            <div class="partner-title"><?= $item['name']; ?></div>
                            <div class="partner-text"><?= $item['text']; ?></div>
                        </div>
                    </a>
                <?php } ?>
            </div>
        </div>
    </section>
    <?php foreach ($organizers as $key => $item) { ?>
        <div class="modal-side_banner" id="modal_partner_<?= $key; ?>">
            <div class="modal-review-content">
                <div class="modal-side_banner-img">
                    <?= Html::img($model->getThumb('organizer_image', 'main', false, $item['image'][0]), ['alt' => $item['name'], 'loading' => 'lazy']); ?>
                </div>
                <div class="modal-side_banner-info">
                    <h3><?= $item['name']; ?></h3>
                    <p><?= $item['text']; ?></p>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } ?>

<?php if (!empty($partners)) { ?>
    <section class="sec section-page section-partners">
        <div class="container wide">
            <h3 class="section-page-title text-left"><?= !empty($model->partners_title) ? $model->partners_title : 'Партнеры'; ?></h3>
            <div class="partners-slider owl-carousel owl-theme" data-autoplay="true" data-timeout="5000">
                <?php foreach ($partners as $item) { ?>
                    <?php if (!empty($item['link'])) { ?>
                        <a href="<?= $item['link']; ?>" class="partners-slide">
                            <div class="partners-slide-img">
                                <?= Html::img($model->getThumb('partner_image', 'main', false, $item['image'][0]), ['alt' => $item['name'], 'loading' => 'lazy']); ?>
                            </div>
                        </a>
                    <?php } else { ?>
                        <div class="partners-slide">
                            <div class="partners-slide-img">
                                <?= Html::img($model->getThumb('partner_image', 'main', false, $item['image'][0]), ['alt' => $item['name'], 'loading' => 'lazy']); ?>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>

<?php if (!empty($infoPartners)) { ?>
    <section class="sec section-page section-partners gray-bg">
        <div class="container wide">
            <h3 class="section-page-title text-left"><?= !empty($model->info_partners_title) ? $model->info_partners_title : 'Партнеры'; ?></h3>
            <div class="partners-slider owl-carousel owl-theme" data-autoplay="true" data-timeout="5000">
                <?php foreach ($infoPartners as $item) { ?>
                    <?php if (!empty($item['link'])) { ?>
                        <a href="<?= $item['link']; ?>" class="partners-slide">
                            <div class="partners-slide-img">
                                <?= Html::img($model->getThumb('info_partner_image', 'main', false, $item['image'][0]), ['alt' => $item['name'], 'loading' => 'lazy']); ?>
                            </div>
                        </a>
                    <?php } else { ?>
                        <div class="partners-slide">
                            <div class="partners-slide-img">
                                <?= Html::img($model->getThumb('info_partner_image', 'main', false, $item['image'][0]), ['alt' => $item['name'], 'loading' => 'lazy']); ?>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>

<?php if (!empty($materials)) { ?>
    <section class="sec section-page">
        <div class="container wide">
            <h3 class="section-page-title text-left"><?= !empty($model->materials_title) ? $model->materials_title : 'Материалы'; ?></h3>
            <div class="blog-page-4card-slider owl-carousel owl-theme" data-autoplay="true" data-timeout="5000">
                <?php foreach ($materials as $i => $item) { ?>
                    <a href="<?= $item->getUrlPath(); ?>" class="blog-page-4card-slide">
                        <div class="blog-page-4card-slide-img">
                            <img src="<?= $item->getThumb('image', 'main'); ?>"
                                 alt="<?= str_replace('"', '&quot;', $item->name); ?>">
                        </div>
                        <div class="blog-page-4card-slide-info">
                            <div class="blog-page-4card-slide-title"><?= $item->name; ?></div>
                            <div class="blog-page-4card-slide-text-wrapper">
                                <div class="blog-page-4card-slide-date"><?= \Yii::$app->formatter->asDate($item->published, 'php:d.m.Y'); ?></div>
                            </div>
                        </div>
                    </a>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>


<?php if (!empty($model->join_us_title) && !empty($model->join_us_text)) { ?>
    <section class="sec section-page section-personal-consultation">
        <div class="container wide">
            <div class="join_us_box">
                <span class="join_us_bg" data-parallax="" style="background-position: 50% -80px;"></span>
                <div class="join_us_box-title"><?= $model->join_us_title; ?></div>
                <p><?= $model->join_us_text; ?></p>
                <div class="join_us_button">
                    <?php if (!empty($model->join_us_button_link) && !empty($model->join_us_button_name)) {
                        echo Html::a($model->join_us_button_name, $model->join_us_button_link, ['class' => 'button white']);
                    } ?>
                </div>
            </div>
        </div>
    </section>
<?php } ?>

    <section class="sec section-share">
        <div class="container">
            <div class="section-page-title">Расскажите о проекте:</div>
            <div class="share-block-project share-block-project-big">
                <?= $this->render('_social_box_cases', ['model' => $model]); ?>
                <?= $this->render('_social_box_cases_mobile', ['model' => $model]); ?>
            </div>
        </div>
    </section>
<?php
$this->registerJsFile('/js/main-blog.js', ['depends' => [app\assets\AppAsset::class]]);
$this->registerCssFile('/css/style-blog.css', ['depends' => [app\assets\AppAsset::class]]);
?>