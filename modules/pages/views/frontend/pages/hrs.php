<?php

use app\modules\pages\models\AcademyCatalog;
use yii\helpers\Html;

$experts_page = AcademyCatalog::find()->where(['visible' => 1, 'model' => AcademyCatalog::class])->one();
$experts_url = false;
if ($experts_page) {
    $experts_url = trim(\app\helpers\MainHelper::get_template_base_url(), '/') . $experts_page->getUrlPath();
}
?>

    <div class="sec section-page-banner">
        <div class="section-page-banner-bg"
             style="background: url(<?= $model->getThumb('image', 'main'); ?>) center/cover no-repeat;"></div>
        <div class="container wide">
            <div class="section-page-banner-info">
                <?php if ($model->block1_title) { ?><h1 class="section-page-banner-title"><span
                        style="color: #0086FF;"><?= $model->block1_span; ?></span> <?= $model->block1_title; ?>
                    </h1><?php } ?>
                <div class="section-page-banner-text"><?= $model->content; ?></div>
                <div class="buttons">
                    <?php if ($model->block1_left_button_title && $model->block1_left_button_url) { ?><a
                        href="<?= $model->block1_left_button_url; ?>"
                        class="button"><?= $model->block1_left_button_title; ?></a><?php } ?>
                    <?php if ($model->block1_right_button_title && $model->block1_right_button_url) { ?><a
                        href="<?= $model->block1_right_button_url; ?>"
                        class="button"><?= $model->block1_right_button_title; ?></a><?php } ?>
                </div>
            </div>
        </div>
        <img src="<?= $model->getThumb('image_mobile', 'main'); ?>" class="section-page-banner-bg-mob">
    </div>

    <main class="sec content_sec">
        <?php if (!empty($model->features)) { ?>
            <div class="sec section-page section-accent gray-bg">
                <div class="container wide">
                    <div class="accent-list default-slider-3 owl-carousel owl-theme" data-loop="true"
                         data-autoplay="true" data-timeout="5000">
                        <?php foreach ($model->features as $item) { ?>
                            <div class="accent-element">
                                <?php if (!empty($item->getThumb('image', 'main'))) { ?>
                                    <div class="accent-element-img">
                                        <img src="<?= $item->getThumb('image', 'main'); ?>" alt="">
                                    </div>
                                <?php } ?>
                                <div class="accent-element-title"><?= $item->name; ?></div>
                                <div class="accent-element-text"><?= $item->description; ?></div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if (!empty($model->problematic)) { ?>
            <div class="sec section-page section-experts-for-org">
                <div class="container wide">
                    <?php if ($model->block_problematic_title) { ?><h2
                            class="section-page-title"><?= $model->block_problematic_title; ?></h2><?php } ?>
                    <?php if ($model->block_problematic_text) { ?>
                        <div class="section-page-text"><?= $model->block_problematic_text; ?></div><?php } ?>
                    <div class="experts-for-org-list">
                        <?php foreach ($model->problematic as $item) { ?>
                            <div class="experts-for-org-element">
                                <div class="experts-for-org-element-title"><?= $item['title']; ?></div>
                                <div class="experts-for-org-element-text"><?= $item['text']; ?></div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if (!empty($model->proposal)) { ?>
            <div class="sec section-page section-corporate-offer">
                <div class="container wide">
                    <?php if ($model->block_proposal_title) { ?><h2
                            class="section-page-title"><?= $model->block_proposal_title; ?></h2><?php } ?>
                    <div class="corporate-offer-list default-slider-4 owl-carousel owl-theme" data-loop="true"
                         data-autoplay="true" data-timeout="5000">
                        <?php foreach ($model->proposal as $key => $item) { ?>
                            <div class="corporate-offer-element">
                                <div class="corporate-offer-step"><?= str_pad($key, 2, '0', STR_PAD_LEFT); ?></div>
                                <div class="corporate-offer-title"><?= $item['title']; ?></div>
                                <div class="corporate-offer-text"><?= $item['text']; ?><?php if (!empty($item['url'])) { ?>
                                        <br><a href="<?= $item['url']; ?>">Заполнить форму></a><?php } ?></div>
                            </div>
                        <?php } ?>
                        <?php if (!empty($model->block_proposal_url)) { ?>
                            <div class="corporate-offer-element-banner">
                                <div class="corporate-offer-banner-title">Смотрите все услуги, которые оказывают
                                    Эксперты
                                </div>
                                <a href="<?= $model->block_proposal_url; ?>" class="button white">Перейти к каталогу</a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if (!empty($model->block_join_title) || !empty($model->block_join_text)) { ?>
            <div class="sec">
                <div class="container wide section-page section-text-banner">
                    <?php if ($model->block_join_title) { ?><h2
                            class="section-page-title"><?= $model->block_join_title; ?></h2><?php } ?>
                    <?php if ($model->block_join_text) { ?>
                        <div class="section-page-text"><?= $model->block_join_text; ?></div><?php } ?>
                </div>
            </div>
        <?php } ?>

        <?php if (!empty($model->getExperts())) { ?>
            <div class="sec section-page section-experts-academy">
                <div class="container wide">
                    <?php if ($model->block_experts_title) { ?><h3
                            class="section-page-title"><?= $model->block_experts_title; ?></h3><?php } ?>
                    <div class="experts-academy-list default-slider-4 owl-carousel owl-theme" data-loop="true"
                         data-autoplay="true" data-timeout="5000">
                        <?php foreach ($model->getExperts() as $item) { ?>
                            <div class="experts-academy-element">
                                <div class="expert_item">
                                    <div class="expert_item-img_box">
                                        <?= Html::a(Html::img($item->profile->getThumb('image', 'prev'), ['alt' => $item->profile->fullname]), $item->getUrlPath(), ['class' => 'expert_item-img', 'style' => 'width: 100%;']); ?>
                                        <?= app\widgets\shield\ShieldWidget::widget(['user' => $item]); ?>
                                    </div>
                                    <div class="expert_item-info">
                                        <h4><a href="<?= $item->getUrlPath(); ?>"><?= $item->profile->fullname; ?></a>
                                        </h4>
                                        <div class="expert_item-desc">
                                            <a href="<?= $item->getUrlPath(); ?>"><?= $item->profile->about_myself; ?></a>
                                        </div>
                                        <?php if (!is_null($item->directionM)) { ?>
                                            <div class="expert_item-caf">
                                                <a href="<?= $item->getUrlPath(); ?>"><?= $item->directionM->name; ?></a>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="experts-academy-element-banner">
                            <div class="experts-academy-banner-title">Подобрать эксперта самостоятельно</div>
                            <a href="<?= $experts_url ?>" class="button white">Перейти к каталогу</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if (!empty($model->smi)) { ?>
            <div class="sec section-page section-academy-smi gray-bg">
                <div class="container wide">
                    <?php if ($model->block_smi_title) { ?><h2
                            class="section-page-title with_button"><?= $model->block_smi_title; ?><?php } ?> <?php if ($model->block_smi_url) { ?>
                        <a href="<?= $model->block_smi_url; ?>" class="button see_all">Смотреть все</a></h2><?php } ?>
                    <div class="academy-smi-list default-slider-3 owl-carousel owl-theme" data-loop="true"
                         data-autoplay="true" data-timeout="5000">
                        <?php foreach ($model->smi as $item) { ?>
                            <a href="<?= $item->link; ?>" class="academy-smi-element" target="_blank">
                                <?php if (!empty($item->image)) { ?>
                                    <div class="academy-smi-logo">
                                        <img src="<?= $item->image->src; ?>" alt="">
                                    </div>
                                <?php } ?>
                                <div class="academy-smi-title"><?= $item->name; ?></div>
                                <span class="academy-smi-date"><?= $item->date; ?></span>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if (!empty($model->getProjects())) { ?>
            <div class="sec section-page section-from-blog">
                <div class="container wide">
                    <?php if ($model->block_projects_title) { ?><h2
                            class="section-page-title with_button"><?= $model->block_projects_title; ?><?php } ?> <?php if ($model->block_projects_url) { ?>
                        <a href="<?= $model->block_projects_url; ?>" class="button see_all">Смотреть все</a>
                    </h2><?php } ?>
                    <div class="blog-page-4card-slider owl-carousel owl-theme" data-loop="true" data-autoplay="true"
                         data-timeout="5000">
                        <?php foreach ($model->getProjects() as $item) { ?>
                            <a href="<?= $item->getUrlPath(); ?>" class="blog-page-4card-slide">
                                <div class="blog-page-4card-slide-img">
                                    <img src="<?= $item->getThumb('image', 'main'); ?>" alt="">
                                </div>
                                <div class="blog-page-4card-slide-info">
                                    <div class="blog-page-4card-slide-title"><?= $item->name; ?></div>
                                    <div class="blog-page-4card-slide-text-wrapper">
                                        <div class="blog-page-4card-slide-date"><?= \Yii::$app->formatter->asDate($item->published, 'php:j.m.Y'); ?></div>
                                        <!--<div class="blog-page-4card-slide-viewed">453</div>-->
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if (!empty($model->faq)) { ?>
            <div class="sec section-page section-faq-block gray-bg">
                <div class="container wide">
                    <?php if ($model->block_faq_title) { ?><h2
                            class="section-page-title"><?= $model->block_faq_title; ?></h2><?php } ?>
                    <div class="accordion_box faq_box">
                        <?php foreach ($model->faq as $item) { ?>
                            <div class="accordion_item">
                                <h5 class="accordion_title"><?= $item['title']; ?></h5>
                                <div class="accordion_desc"><p><?= $item['text']; ?></p></div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if (!empty($model->block_personal_title) || !empty($model->block_personal_text)) { ?>
            <div class="sec section-page section-personal-consultation">
                <div class="container wide">
                    <div class="join_us_box">
                        <span class="join_us_bg" data-parallax></span>
                        <?php if ($model->block_personal_title) { ?>
                            <div class="join_us_box-title"><?= $model->block_personal_title; ?></div><?php } ?>
                        <?php if ($model->block_personal_text) { ?><p><?= $model->block_personal_text; ?></p><?php } ?>
                        <?php if (!empty($model->block_personal_url)) { ?>
                            <div class="join_us_button">
                                <a href="<?= $model->block_personal_url; ?>" class="button white">Получить персональное
                                    предложение</a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>

    </main>

<?php
$this->registerCssFile('/css/style-blog.css', ['depends' => [app\assets\AppAsset::class]]);
$this->registerJsFile('/js/main-blog.js', ['depends' => [app\assets\AppAsset::class]]);
$js = <<<JS

    

JS;

$this->registerJs($js);
?>