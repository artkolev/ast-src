<?php

use app\helpers\MainHelper;
use app\modules\pages\models\AllCatalog;
use app\modules\pages\models\ExpertsPage;
use app\widgets\shield\ShieldWidget;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var ExpertsPage $model
 */

$all_catalog = AllCatalog::find()->where(['model' => AllCatalog::class])->one();
?>

<div class="sec section-page-banner">
    <div class="section-page-banner-bg"
         style="background: url(<?= $model->getThumb('image', 'main'); ?>) center/cover no-repeat;"></div>
    <div class="container wide">
        <div class="section-page-banner-info">
            <?= $model->block1_title ? "<h1 class=\"section-page-banner-title\"><span style=\"color: #0086FF;\">{$model->block1_span}</span> {$model->block1_title}</h1>" : ''; ?>
            <div class="section-page-banner-text"><?= $model->content; ?></div>
            <div class="buttons">
                <?= ($model->block1_left_button_title && $model->block1_left_button_url) ? "<a href=\"{$model->block1_left_button_url}\" class=\"button\">{$model->block1_left_button_title}</a>" : ''; ?>
                <?= ($model->block1_right_button_title && $model->block1_right_button_url) ? "<a href=\"{$model->block1_right_button_url}\" class=\"button\">{$model->block1_right_button_title}</a>" : ''; ?>
            </div>
        </div>
    </div>
    <img src="<?= $model->getThumb('image_mobile', 'main'); ?>" alt="<?= Html::encode($model->block1_title); ?>"
         class="section-page-banner-bg-mob">
</div>

<main class="sec content_sec">

    <?php
    $prof = MainHelper::cleanInvisibleMultifield($model->problematic);
    if (!empty($prof)) {
        ?>
        <div class="sec section-page services-support-block gray-bg">
            <div class="container wide">
                <?= $model->block_problematic_title ? "<h2 class=\"section-page-title\">{$model->block_problematic_title}</h2>" : ''; ?>
                <div class="services-support-list">
                    <?php
                    $prof = array_values($prof);
                    foreach ($prof as $item) {
                        $item = (object)$item;
                        $itemImage = $model->getThumb('problematicimage', 'main', false, (int)($item->problematic_image)[0] ?? null) ?? null;
                        if (!empty($item->link)) { ?>
                            <a href="<?= $item->link; ?>" class="services-support-element">
                                <?php if (!empty($itemImage)) { ?>
                                    <div class="services-support-element-icon">
                                        <img src="<?= $itemImage; ?>" alt="<?= Html::encode($item->name); ?>">
                                    </div>
                                <?php } ?>
                                <div class="services-support-element-name"><?= $item->name; ?></div>
                                <div class="services-support-element-text"><?= $item->description; ?></div>
                            </a>
                        <?php } else { ?>
                            <div class="services-support-element">
                                <?php if (!empty($itemImage)) { ?>
                                    <div class="services-support-element-icon">
                                        <img src="<?= $itemImage; ?>" alt="<?= Html::encode($item->name); ?>">
                                    </div>
                                <?php } ?>
                                <div class="services-support-element-name"><?= $item->name; ?></div>
                                <div class="services-support-element-text"><?= $item->description; ?></div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php
    $experts = $model->getExperts(); // Оптимизация, чтобы второй раз не трогать геттер в последующем цикле
    if (!empty($experts)) { ?>
        <div class="sec section-page section-experts-academy">
            <div class="container wide">
                <?= $model->block_experts_title ? "<h3 class=\"section-page-title\">{$model->block_experts_title}</h3>" : ''; ?>
                <div class="experts-academy-list default-slider-4 owl-carousel owl-theme" data-loop="true"
                     data-autoplay="true" data-timeout="5000">
                    <?php foreach ($experts as $i => $item) { ?>
                        <div class="experts-academy-element">
                            <div class="expert_item">
                                <div class="expert_item-img_box">
                                    <?= Html::a(
                                            Html::img($item->profile->getThumb('image', 'prev'), ['alt' => Html::encode($item->profile->fullname)]),
                                            $item->getUrlPath(),
                                            ['class' => 'expert_item-img', 'style' => 'width: 100%;']
                                    ); ?>
                                    <?= ShieldWidget::widget(['user' => $item]); ?>
                                </div>
                                <div class="expert_item-info">
                                    <h4><a href="<?= $item->getUrlPath(); ?>"><?= $item->profile->fullname; ?></a></h4>
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
                        <?php if ($i % 3 == 2) { ?>
                            <div class="experts-academy-element-banner">
                                <div class="experts-academy-banner-title">Познакомиться с экспертами</div>
                                <a href="<?= Url::toRoute([$all_catalog->getUrlPath()]); ?>" class="button white">Перейти
                                    к каталогу</a>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php $marketplace = MainHelper::cleanInvisibleMultifield($model->problematic2);
    if (!empty($marketplace)) { ?>
        <div class="sec section-page section-experts-for-org gray-bg">
            <div class="container wide">
                <?= $model->block_problematic2_title ? "<h2 class=\"section-page-title\">{$model->block_problematic2_title}</h2>" : ''; ?>
                <?= $model->block_problematic2_text ? "<div class=\"section-page-text\">{$model->block_problematic2_text}</div>" : ''; ?>
                <div class="services-support-list">
                    <?php
                    foreach ($marketplace as $item) {
                        $item = (object)$item;
                        if (!empty($item->link)) { ?>
                            <a href="<?= $item->link; ?>" class="services-support-element not-icon">
                                <div class="services-support-element-name"><?= $item->name; ?></div>
                                <div class="services-support-element-text"><?= $item->description; ?></div>
                            </a>
                        <?php } else { ?>
                            <div class="services-support-element not-icon">
                                <div class="services-support-element-name"><?= $item->name; ?></div>
                                <div class="services-support-element-text"><?= $item->description; ?></div>
                            </div>
                        <?php } ?>
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
                    <?= $model->block_personal_title ? "<div class='join_us_box-title'>{$model->block_personal_title}</div>" : ''; ?>
                    <?= $model->block_personal_text ? "<p>{$model->block_personal_text}</p>" : ''; ?>
                    <div class="join_us_button">
                        <a href="/register/" class="button white">Зарегистрироваться</a>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php
    $clientsearch = MainHelper::cleanInvisibleMultifield($model->problematic3);
    if (!empty($clientsearch)) {
        ?>
        <div class="sec section-page section-experts-for-org gray-bg">
            <div class="container wide">
                <?= $model->block_problematic3_title ? "<h2 class=\"section-page-title\">{$model->block_problematic3_title}</h2>" : ''; ?>
                <?= $model->block_problematic3_text ? "<div class=\"section-page-text\">{$model->block_problematic3_text}</div>" : ''; ?>
                <div class="experts-for-org-list">
                    <?php
                    foreach ($clientsearch as $item) {
                        $item = (object)$item;
                        ?>
                        <div class="experts-for-org-element">
                            <div class="experts-for-org-element-title"><?= $item->title; ?></div>
                            <div class="experts-for-org-element-text"><?= $item->text; ?></div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($model->block_join_title) || !empty($model->block_join_text)) { ?>
        <div class="sec section-page section-experts-join">
            <div class="container wide">
                <div class="section-page-banner-info">
                    <?= $model->block_join_title ? "<h2 class=\"section-page-title\">{$model->block_join_title}</h2>" : ''; ?>
                    <?= $model->block_join_text ? "<div class=\"section-page-banner-text\">{$model->block_join_text}</div>" : ''; ?>
                    <?php if (!empty($model->block_join_button_link) && !empty($model->block_join_button_text)) { ?>
                        <div class="buttons">
                            <a href="<?= $model->block_join_button_link; ?>"
                               class="button"><?= $model->block_join_button_text; ?></a>
                        </div>
                    <?php } ?>
                </div>
                <div class="experts-join-img">
                    <img src="<?= $model->getThumb('join', 'main'); ?>"
                         alt="<?= Html::encode($model->block_join_title); ?>">
                </div>
            </div>
        </div>
    <?php } ?>

    <?php
    $stats = MainHelper::cleanInvisibleMultifield($model->stats);
    if (!empty($stats)) {
        ?>
        <div class="sec section-page section-experts-stats gray-bg">
            <div class="container wide">
                <?= $model->block_stats_title ? "<h2 class=\"section-page-title\">{$model->block_stats_title}</h2>" : ''; ?>
                <div class="experts-stats-list default-slider-3 owl-carousel owl-theme" data-loop="true"
                     data-autoplay="true" data-timeout="5000">
                    <?php foreach ($stats as $item) { ?>
                        <div class="experts-stats-element">
                            <div class="experts-stats-num"><?= $item['num']; ?></div>
                            <div class="experts-stats-title"><?= $item['title']; ?></div>
                            <div class="experts-stats-text"><?= $item['text']; ?></div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php
    $proposal = MainHelper::cleanInvisibleMultifield($model->proposal);
    if (!empty($proposal)) { ?>
        <div id="how_to_expert" class="sec section-page section-corporate-offer">
            <div class="container wide">
                <?= $model->block_proposal_title ? "<h2 class=\"section-page-title\">{$model->block_proposal_title}</h2>" : ''; ?>
                <?= $model->block_proposal_text ? "<div class=\"section-page-banner-text\">{$model->block_proposal_text}</div>" : ''; ?>
                <div class="corporate-offer-list default-slider-4 owl-carousel owl-theme" data-loop="true"
                     data-autoplay="true" data-timeout="5000">
                    <?php
                    $proposal = array_values($proposal);
                    foreach ($proposal as $key => $item) {
                        $item = (object)$item; ?>
                        <div class="corporate-offer-element">
                            <div class="corporate-offer-step"><?= str_pad($key + 1, 2, '0', STR_PAD_LEFT); ?></div>
                            <div class="corporate-offer-title"><?= $item->name; ?></div>
                            <div class="supervisor-and-orgs-text"><?= $item->description; ?></div>
                            <?php
                            $itemImage = $model->getThumb('proposalimage', 'main', false, (int)($item->proposal_image)[0] ?? null) ?? null;
                            if (!empty($itemImage)) { ?>
                                <div class="supervisor-and-orgs-img">
                                    <img src="<?= $itemImage; ?>" alt="<?= Html::encode($item->name); ?>">
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php
    $faq = MainHelper::cleanInvisibleMultifield($model->faq);
    if (!empty($faq)) {
        ?>
        <div class="sec section-page section-faq-block gray-bg">
            <div class="container wide">
                <?= $model->block_faq_title ? "<h2 class=\"section-page-title\">{$model->block_faq_title}</h2>" : ''; ?>
                <div class="accordion_box faq_box">
                    <?php
                    foreach ($faq as $item) {
                        $item = (object)$item;
                        ?>
                        <div class="accordion_item">
                            <h5 class="accordion_title"><?= $item->title; ?></h5>
                            <div class="accordion_desc"><p><?= $item->text; ?></p></div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php
    $smi = MainHelper::cleanInvisibleMultifield($model->smi);
    if (!empty($smi)) { ?>
        <div class="sec section-page section-academy-smi gray-bg">
            <div class="container wide">
                <?= $model->block_smi_title ? "<h2 class=\"section-page-title with_button\">{$model->block_smi_title}" : ''; ?>
                <?= $model->block_smi_url ? "<a href=\"{$model->block_smi_url}\" class=\"button see_all\">Смотреть все</a></h2>" : ''; ?>
                <div class="academy-smi-list default-slider-3 owl-carousel owl-theme" data-loop="true"
                     data-autoplay="true" data-timeout="5000">
                    <?php
                    foreach ($smi as $item) {
                        $item = (object)$item;
                        ?>
                        <a href="<?= $item->url; ?>" class="academy-smi-element" target="_blank">
                            <?php
                            $itemImage = $model->getFile('smiimage', false, (int)($item->smi_image)[0] ?? null) ?? null;
                            if (!empty($itemImage)) {
                                ?>
                                <div class="academy-smi-logo">
                                    <img src="<?= $itemImage; ?>" alt="<?= Html::encode($item->name); ?>">
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

    <?php if (!empty($model->block_personal_title) || !empty($model->block_personal_text)) { ?>
        <div class="sec section-page section-personal-consultation">
            <div class="container wide">
                <div class="join_us_box">
                    <span class="join_us_bg" data-parallax></span>
                    <?= $model->block_personal_title ? "<div class='join_us_box-title'>{$model->block_personal_title}</div>" : ''; ?>
                    <?= $model->block_personal_text ? "<p>{$model->block_personal_text}</p>" : ''; ?>
                    <div class="join_us_button">
                        <a href="/register/" class="button white">Зарегистрироваться</a>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

</main>