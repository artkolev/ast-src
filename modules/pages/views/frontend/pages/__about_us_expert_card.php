<?php

use app\modules\pages\models\AboutUs;
use app\modules\users\models\UserAR;
use yii\helpers\Html;

/**
 * @var string $title
 * @var string $anchor
 * @var string $text
 * @var string $type
 * @var UserAR[] $list
 */

?>
<?php if (!empty($list)) { ?>
    <?php if ($type === AboutUs::EXPERT_CARD_TYPE_STATIC) { ?>
        <section id="<?= $anchor; ?>" class="sec section-page section-experts-academy gray-bg">
            <div class="container wide">
                <h3 class="section-page-title"><?= $title; ?></h3>
                <?php if (!empty($text)) { ?>
                    <div class="subheader"><?= $text; ?></div>
                <?php } ?>
                <div class="experts-academy-list experts-academy-list-v2 owl-carousel owl-theme" data-loop="true"
                     data-autoplay="true" data-timeout="5000">
                    <?php foreach ($list as $user) { ?>
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
                    <?php } ?>
                </div>
            </div>
        </section>
    <?php } else { ?>
        <section id="academy-experts" class="sec section-page section-experts-academy gray-bg">
            <div class="container wide">
                <h3 class="section-page-title"><?= $title; ?></h3>
                <?php if (!empty($text)) { ?>
                    <div class="subheader"><?= $text; ?></div>
                <?php } ?>
                <div class="experts-academy-list default-slider-4 owl-carousel owl-theme" data-loop="true"
                     data-autoplay="true" data-timeout="5000">
                    <?php foreach ($list as $user) { ?>
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
                    <?php } ?>
                </div>
            </div>
        </section>
    <?php } ?>
<?php } ?>
