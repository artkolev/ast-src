<?php

use yii\helpers\Html;

?>
<?php if (!empty($model->fizlic_title) or !empty($model->fizlic_text)) { ?>
    <section class="sec index_header_sec business_header-background">
        <div class="container wide business_margin_sec">
            <div class="business_header">
                <h1><?= $model->fizlic_title; ?></h1>
                <div class="subheader"><?= $model->fizlic_text; ?></div>
                <div class="business_header-btns">
                    <?php if (!empty($model->fizlic_find_specialist_url)) { ?>
                        <a href="<?= $model->fizlic_find_specialist_url; ?>"
                           class="button"><?= $model->fizlic_find_specialist_text; ?></a>
                    <?php } ?>
                    <?php if (!empty($model->fizlic_send_request_text)) { ?>
                        <a href="#"
                           class="button individual_writeacadem_show"><?= $model->fizlic_send_request_text; ?></a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
<?php } ?>
<?php if (!$model->fizlic_video_checkbox && $model->fizlic_video_url && $model->video_image) { ?>
    <section class="sec index_header_sec bgrealgrey">
        <div class="container wide business_margin_sec">
            <div class="business_header">
                <div class="index_video-area">
                    <div class="index_video">
                        <a href="<?= $model->fizlic_video_url; ?>" data-fancybox data-ratio="1.77"
                           class="index_video-box  index_video-box--btn-blue">
                            <img src="<?= $model->getThumb('video_image', 'main'); ?>" alt=""/>
                        </a>
                    </div>
                    <?php if (!empty($model->fizlic_video_title) or !empty($model->fizlic_video_text)) { ?>
                        <article class="index_video">
                            <?= !empty($model->fizlic_video_title) ? '<h2>' . $model->fizlic_video_title . '</h2>' : ''; ?>
                            <?= $model->fizlic_video_text; ?>
                        </article>
                    <?php } ?>
                </div>

            </div>
        </div>
    </section>
<?php } ?>
<?php if (!empty($model->fizlic_i_find_col1_text) or !empty($model->fizlic_i_find_col2_text) or !empty($model->fizlic_i_find_col3_text)) { ?>
    <section class="sec index_header_sec">
        <div class="container wide business_margin_sec">
            <div class="business_header-circle_items">
                <!-- item -->
                <?php if ($model->fizlic_i_find_col1_url && $model->fizlic_i_find_col1_text) { ?>
                    <div class="business_header-circle_item">
                        <a href="<?= $model->fizlic_i_find_col1_url; ?>"><?= $model->fizlic_i_find_col1_text; ?></a>
                    </div>
                <?php } ?>

                <?php if ($model->fizlic_i_find_col2_url && $model->fizlic_i_find_col2_text) { ?>
                    <div class="business_header-circle_item">
                        <a href="<?= $model->fizlic_i_find_col2_url; ?>"><?= $model->fizlic_i_find_col2_text; ?></a>
                    </div>
                <?php } ?>

                <?php if ($model->fizlic_i_find_col3_url && $model->fizlic_i_find_col3_text) { ?>
                    <div class="business_header-circle_item">
                        <a href="<?= $model->fizlic_i_find_col3_url; ?>"><?= $model->fizlic_i_find_col3_text; ?></a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>
<?php if (!empty($model->fizlic_why_we_col1_text) or !empty($model->fizlic_why_we_col2_text) or !empty($model->fizlic_why_we_col3_text)) { ?>
    <section class="sec index_header_sec bgrealgrey">
        <div class="container wide business_margin_sec">
            <div class="business_header">
                <h1><?= $model->fizlic_why_we_title; ?></h1>
                <div class="business_header_why-me">
                    <?php if ($model->fizlic_why_we_col1_text) { ?>
                        <article class="text_icon">
                            <span class="icon"><i class="fa fa-check"></i></span>
                            <div><?= $model->fizlic_why_we_col1_text; ?></div>
                        </article>
                    <?php } ?>
                    <?php if ($model->fizlic_why_we_col2_text) { ?>
                        <article class="text_icon">
                            <span class="icon"><i class="fa fa-check"></i></span>
                            <div><?= $model->fizlic_why_we_col2_text; ?></div>
                        </article>
                    <?php } ?>
                    <?php if ($model->fizlic_why_we_col3_text) { ?>
                        <article class="text_icon">
                            <span class="icon"><i class="fa fa-check"></i></span>
                            <div><?= $model->fizlic_why_we_col3_text; ?></div>
                        </article>
                    <?php } ?>
                </div>
                <?php if (!empty($model->fizlic_why_we_find_specialist_text) or !empty($model->fizlic_why_we_send_request_text)) { ?>
                    <div class="business_header-btns">
                        <?php if (!empty($model->fizlic_why_we_find_specialist_text) or !empty($model->fizlic_why_we_find_specialist_url)) { ?>
                            <a href="<?= $model->fizlic_why_we_find_specialist_url; ?>"
                               class="button"><?= $model->fizlic_why_we_find_specialist_text; ?></a>
                        <?php } ?>
                        <?php if (!empty($model->fizlic_why_we_send_request_text)) { ?>
                            <a href="#"
                               class="button individual_writeacadem_show"><?= $model->fizlic_why_we_send_request_text; ?></a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>
<?php if (!empty($model->individualthemes)) { ?>
    <section class="sec index_specs_sec">
        <div class="container wide business_margin_sec">
            <?php if (!empty($model->fizlic_actual_theme_title)) { ?>
                <div class="business_header"><h2><?= $model->fizlic_actual_theme_title; ?></h2></div>
            <?php } ?>
            <div class="business_header-tag-btns_pc">
                <?php foreach ($model->individualthemes as $key => $item) { ?>
                    <a href="#" id="button_theme-id_<?= $item->id; ?>" data-theme-id="<?= $item->id; ?>"
                       class="button<?= $key === 0 ? ' active' : ''; ?>"><?= $item->name; ?></a>
                <?php } ?>
            </div>
            <div class="business_header-tag-btns_mobile">
                <?php foreach ($model->individualthemes as $key => $item) { ?>
                    <a href="#" id="button_theme-id_<?= $item->id; ?>_mobile" data-theme-id="<?= $item->id; ?>"
                       class="<?= $key === 0 ? 'active' : ''; ?>"><?= $item->name; ?></a>
                <?php } ?>
            </div>
            <?php foreach ($model->individualthemes as $key => $theme) { ?>
                <div class="index_experts_indicator<?= ($key == 0) ? ' active' : ''; ?>"
                     id="index_experts_box_theme-id_<?= $theme->id; ?>"
                     style="display: <?= ($key == 0) ? 'block' : 'none'; ?>">
                    <div class="index_experts_box">
                        <?php foreach ($theme->academs as $item) {
                            $user = $item->user; ?>
                            <div class="expert_item">
                                <div class="expert_item-img_box">
                                    <?= Html::a(Html::img($user->profile->getThumb('image', 'prev'), ['alt' => $user->profile->halfname]), $user->getUrlPath(), ['class' => 'expert_item-img']); ?>
                                    <?= app\widgets\shield\ShieldWidget::widget(['user' => $user]); ?>
                                </div>
                                <div class="expert_item-info">
                                    <h5>
                                        <a href="<?= $user->getUrlPath(); ?>"><?= $user->profile->getHalfname('<br>'); ?></a>
                                    </h5>
                                    <div class="expert_item-city"><?= $user->profile->city->name; ?></div>
                                    <?php if (!is_null($user->directionM) && !$user->directionM->stels_direct) { ?>
                                        <div class="expert_item-direction"><?= $user->directionM->name; ?></div>
                                    <?php } ?>
                                    <div class="expert_item-desc">
                                        <a href="<?= $user->getUrlPath(); ?>"><?= $user->profile->about_myself; ?></a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <?php if (!empty($theme->button_link) && !empty($theme->button_name)) { ?>
                        <div class="index_sec_buttons">
                            <a href="<?= $theme->button_link; ?>" class="button"><?= $theme->button_name; ?></a>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </section>
<?php } ?>
<?php if (!empty($model->individualreviews)) { ?>
    <section class="sec business_slider_sec bgrealgrey">
        <div class="container wide business_margin_sec">
            <?php if (!empty($model->fizlic_reviews_title)) { ?>
                <div class="business_header"><h2><?= $model->fizlic_reviews_title; ?></h2></div>
            <?php } ?>
            <div class="business_slider owl-carousel owl-theme reviews_class" data-autoplay="false" data-timeout="5000">
                <?php foreach ($model->individualreviews as $item) { ?>
                    <div class="business_slide">
                        <h6 id="review_date_<?= $item->id; ?>"><?= Yii::$app->formatter->asDatetime($item->date, 'php:d.m.Y'); ?></h6>
                        <h5 id="review_name_<?= $item->id; ?>"><?= $item->name; ?></h5>
                        <p id="review_description_<?= $item->id; ?>"><?= $item->description; ?></p>
                        <?php if (strlen($item->description) > 168) { ?>
                            <a href="#review_popup" data-review-id="<?= $item->id; ?>" data-fancybox>Подробнее</a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>
<?php if (!empty($model->fizlic_for_whom_block_fizlic_title) or !empty($model->fizlic_for_whom_block_business_title)) { ?>
    <section class="sec index_specs_sec">
        <div class="container wide business_margin_sec">
            <div class="business_more-details">
                <?php if (!empty($model->fizlic_for_whom_block_fizlic_title)) { ?>
                    <div class="business_more-details_item for_fiz">
                        <div class="business_more-details_item-description">
                            <h1><?= $model->fizlic_for_whom_block_fizlic_title; ?></h1>
                            <?php if (!empty($model->fizlic_for_whom_block_fizlic_text)) { ?>
                                <p><?= $model->fizlic_for_whom_block_fizlic_text; ?></p>
                            <?php } ?>
                            <?php if (!empty($model->fizlic_for_whom_block_fizlic_btn_url)) { ?>
                                <a href="<?= $model->fizlic_for_whom_block_fizlic_btn_url; ?>"
                                   class="button send_order"><?= $model->fizlic_for_whom_block_fizlic_btn_text; ?></a>
                            <?php } ?>
                        </div>
                        <?php if (!empty($model->image2)) { ?>
                            <div class="business_more-details_item-img"><img src="<?= $model->image2->src; ?>" alt=""/>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if (!empty($model->fizlic_for_whom_block_business_title)) { ?>
                    <div class="business_more-details_item for_business">
                        <div class="business_more-details_item-description">
                            <h1><?= $model->fizlic_for_whom_block_business_title; ?></h1>
                            <?php if (!empty($model->fizlic_for_whom_block_business_text)) { ?>
                                <p><?= $model->fizlic_for_whom_block_business_text; ?></p>
                            <?php } ?>
                            <?php if (!empty($model->fizlic_for_whom_block_business_btn_url)) { ?>
                                <a href="<?= $model->fizlic_for_whom_block_business_btn_url; ?>"
                                   class="button send_order"><?= $model->fizlic_for_whom_block_business_btn_text; ?></a>
                            <?php } ?>
                        </div>
                        <?php if (!empty($model->image3)) { ?>
                            <div class="business_more-details_item-img"><img src="<?= $model->image3->src; ?>" alt=""/>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>
<div class="modal_review" id="review_popup">
    <div class="business_slide">
        <h6 id="modal_review_date"></h6>
        <h5 id="modal_review_name"></h5>
        <p id="modal_review_description"></p>
    </div>
</div>
<?= \app\modules\individualwriteacadem\widgets\individualwriteacadem\IndividualwriteacademWidget::widget(); ?>
