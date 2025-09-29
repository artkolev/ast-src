<?php

use app\modules\pages\models\AcademyCatalog;
use yii\helpers\Html;

$experts_page = AcademyCatalog::find()->where(['visible' => 1, 'model' => AcademyCatalog::class])->one();
$experts_url = false;
if ($experts_page) {
    $experts_url = trim(\app\helpers\MainHelper::get_template_base_url(), '/') . $experts_page->getUrlPath();
}
?>

<div class="sec section-page-banner section-page-banner-with-img">
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
        <div class="section-page-banner-img">
            <img src="<?= $model->getThumb('image', 'main'); ?>" alt="">
        </div>
    </div>
</div>

<?= $this->render(
        '__numbered_card',
        [
                'title' => $model->block_teams_title,
                'anchor' => 'block_teams',
                'text' => $model->block_teams_text,
                'type' => $model->teams_card_type,
                'list' => $model->teams,
        ]
); ?>

<?php if (!empty($model->for_orgs)) { ?>
    <div class="sec section-page section-experts-for-org">
        <div class="container wide">
            <?php if ($model->block_for_orgs_title) { ?><h2
                    class="section-page-title"><?= $model->block_for_orgs_title; ?></h2><?php } ?>
            <?php if ($model->block_for_orgs_text) { ?>
                <div class="section-page-text"><?= $model->block_for_orgs_text; ?></div><?php } ?>
            <div class="experts-for-org-list experts-for-org-list-v2">
                <?php foreach ($model->for_orgs as $item) { ?>
                    <div class="experts-for-org-element">
                        <div class="experts-for-org-element-title"><?= $item['title']; ?></div>
                        <div class="experts-for-org-element-text"><p><?= $item['text']; ?></p></div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>

<?= $this->render(
        '__numbered_card',
        [
                'title' => $model->block_boss_org_title,
                'anchor' => 'block_boss',
                'text' => $model->block_boss_org_text,
                'type' => $model->boss_card_type,
                'list' => $model->boss_org,
        ]
); ?>

<?php if (!empty($model->services_buttons)) { ?>
    <div class="sec section-page section-experts-join">
        <div class="container wide">
            <div class="page-banner">
                <div class="section-page-banner-info">
                    <?php if ($model->services_title) { ?><h2
                            class="section-page-title"><?= $model->services_title; ?></h2><?php } ?>
                    <?php if ($model->services_text) { ?>
                        <div class="section-page-banner-text"><?= $model->services_text; ?></div><?php } ?>
                    <div class="buttons">
                        <?php foreach ($model->services_buttons as $button) { ?>
                            <a href="<?= $button['link']; ?>" class="button"><?= $button['name']; ?></a>
                        <?php } ?>
                    </div>
                </div>
                <?php if (!empty($model->getThumb('service_image', 'main'))) { ?>
                    <div class="experts-join-img">
                        <img src="<?= $model->getThumb('service_image', 'main'); ?>" alt="">
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>

<?= $this->render(
        '__numbered_card',
        [
                'title' => $model->block_how_we_work_title,
                'anchor' => 'block_how_we_work',
                'text' => $model->block_how_we_work_text,
                'type' => $model->how_we_work_card_type,
                'link' => $model->how_we_work_link,
                'button' => $model->how_we_work_link_button,
                'list' => $model->how_we_work,
        ]
); ?>

<?php if (!empty($model->for_business)) { ?>
    <div class="sec section-page section-experts-for-org">
        <div class="container wide">
            <?php if ($model->block_for_business_title) { ?><h2
                    class="section-page-title"><?= $model->block_for_business_title; ?></h2><?php } ?>
            <?php if ($model->block_for_business_text) { ?>
                <div class="section-page-text"><?= $model->block_for_business_text; ?></div><?php } ?>
            <div class="experts-for-org-list experts-for-org-list-v2">
                <?php foreach ($model->for_business as $item) { ?>
                    <div class="experts-for-org-element">
                        <div class="experts-for-org-element-title"><?= $item['title']; ?></div>
                        <div class="experts-for-org-element-text"><p><?= $item['text']; ?></p></div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>

<?php if (!empty($model->feedback)) { ?>
    <div class="sec section-page section-corporate-offer gray-bg">
        <div class="container wide">
            <?php if ($model->block_feedback_title) { ?><h2
                    class="section-page-title"><?= $model->block_feedback_title; ?></h2><?php } ?>
            <?php if ($model->block_feedback_text) { ?>
                <div class="section-page-text"><?= $model->block_feedback_text; ?></div><?php } ?>
            <div class="corporate-offer-list default-slider-4 owl-carousel owl-theme">
                <?php foreach ($model->feedback as $item) { ?>
                    <div class="review-slide review-open" href="#review_popup" data-fancybox>
                        <div class="review-info-wrapper">
                            <div class="review-person-img">
                                <img src="img/avatar.png" alt="">
                            </div>
                            <div class="review-person-info">
                                <div class="review-person-name"><?= $item['title']; ?></div>
                                <div class="review-person-status"><?= $item['status']; ?></div>
                            </div>
                        </div>
                        <div class="review-text"><?= $item['text']; ?></div>
                        <div class="review-link">Подробнее</div>
                    </div>
                <?php } ?>
                <?php if (!empty($model->feedback_link) && !empty($model->feedback_link_button)) { ?>
                    <div class="corporate-offer-element-banner">
                        <div class="corporate-offer-banner-title">Оставить <br>отзыв</div>
                        <a href="<?= $model->feedback_link; ?>"
                           class="button white"><?= $model->feedback_link_button; ?></a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div id="review_popup" class="modal-review">
        <div class="modal-review-content">
            <div class="review-info-wrapper">
                <div id="modal_review_img" class="review-person-img">
                    <img src="" alt="">
                </div>
                <div class="review-person-info">
                    <div id="modal_review_name" class="review-person-name"></div>
                    <div id="modal_review_status" class="review-person-status"></div>
                </div>
            </div>
            <div id="modal_review_text" class="review-text"></div>
        </div>
    </div>
<?php } ?>

<?php if (!empty($model->experts_help)) { ?>
    <div class="sec section-page services-support-block">
        <div class="container wide">
            <?php if ($model->block_experts_help_title) { ?><h2
                    class="section-page-title"><?= $model->block_experts_help_title; ?></h2><?php } ?>
            <?php if ($model->block_experts_help_text) { ?>
                <div class="section-page-text"><?= $model->block_experts_help_text; ?></div><?php } ?>
            <div class="services-support-list">
                <?php foreach ($model->experts_help as $item) { ?>
                    <a href="<?= $item['link']; ?>" class="services-support-element not-icon">
                        <div class="services-support-element-name"><?= $item['title']; ?></div>
                        <div class="services-support-element-text"><?= $item['text']; ?></div>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>

<?php if (!empty($model->astExpertsCards)) { ?>
    <div id="academy-experts" class="sec section-page section-experts-academy gray-bg">
        <div class="container wide">
            <h3 class="section-page-title"><?= $model->ast_experts_title; ?></h3>
            <?php if (!empty($model->ast_experts_text)) { ?>
                <div class="subheader"><?= $model->ast_experts_text; ?></div>
            <?php } ?>
            <div class="experts-academy-list default-slider-4 owl-carousel owl-theme" data-loop="true"
                 data-autoplay="true" и data-autoplayTimeout="5000">
                <?php $i = -1; ?>
                <?php foreach ($model->astExpertsCards as $user) {
                    $i++; ?>
                    <div class="experts-academy-element">
                        <div class="expert_item">
                            <div class="expert_item-img_box">
                                <?= Html::a(Html::img($user->profile->getThumb('image', 'prev'), ['alt' => $user->profile->fullname]), $user->getUrlPath(), ['class' => 'expert_item-img']); ?>
                                <?= app\widgets\shield\ShieldWidget::widget(['user' => $user]); ?>
                            </div>
                            <div class="expert_item-info">
                                <h4>
                                    <a href="<?= $user->getUrlPath(); ?>"><?= $user->profile->getHalfname('<br>'); ?></a>
                                </h4>
                                <div class="expert_item-desc">
                                    <a href="<?= $user->getUrlPath(); ?>"><?= $user->profile->about_myself; ?></a>
                                </div>
                                <?php if (!is_null($user->directionM) && !$user->directionM->stels_direct) { ?>
                                    <div class="expert_item-caf">
                                        <a href="<?= $user->directionM->getUrlPath(); ?>"><?= $user->directionM->name; ?></a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php if ($model->experts_banner_title && $model->exterts_button_text && $model->experts_banner_link && $i % 3 == 2) { ?>
                        <div class="experts-academy-element-banner">
                            <?= $i; ?>
                            <div class="experts-academy-banner-title"><?= $model->experts_banner_title; ?></div>
                            <a href="<?= $model->experts_banner_link; ?>"
                               class="button white"><?= $model->exterts_button_text; ?></a>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>

<?php if (!empty($model->leading_experts)) { ?>
    <div class="sec section-page section-experts-for-org">
        <div class="container wide">
            <?php if ($model->block_leading_experts_title) { ?><h2
                    class="section-page-title"><?= $model->block_leading_experts_title; ?></h2><?php } ?>
            <?php if ($model->block_leading_experts_text) { ?>
                <div class="section-page-text"><?= $model->block_leading_experts_text; ?></div><?php } ?>
            <div class="experts-for-org-list experts-for-org-list-v2">
                <?php foreach ($model->leading_experts as $item) { ?>
                    <div class="experts-for-org-element">
                        <div class="experts-for-org-element-title"><?= $item['title']; ?></div>
                        <div class="experts-for-org-element-text"><p><?= $item['text']; ?></p></div>
                    </div>
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
            <?php if ($model->block_faq_text) { ?>
                <div class="section-page-text"><?= $model->block_faq_text; ?></div><?php } ?>
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

<?php if (!empty($model->banner_title)) { ?>
    <div id="academy-consultation" class="sec section-page section-personal-consultation">
        <div class="container wide">
            <div class="join_us_box">
                <span class="join_us_bg" data-parallax=""></span>
                <div class="join_us_box-title"><?= $model->banner_title; ?></div>
                <p><?= $model->banner_text; ?></p>
                <div class="join_us_button">
                    <?php foreach ($model->banner_buttons as $item) { ?>
                        <a href="<?= $item['link']; ?>" class="button white"><?= $item['name']; ?></a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<div class="modal" id="personal_modal">
    <div class="modal_content">
        <a href="#" class="modal_close" data-fancybox-close>x</a>
        <script data-b24-form="inline/7/rop42u" data-skip-moving="true">(function (w, d, u) {
                var s = d.createElement('script');
                s.async = true;
                s.src = u + '?' + (Date.now() / 180000 | 0);
                var h = d.getElementsByTagName('script')[0];
                h.parentNode.insertBefore(s, h);
            })(window, document, 'https://crm.ast-academy.ru/upload/crm/form/loader_7_rop42u.js');</script>
    </div>
    <div class="modal_overlay"></div>
</div>
