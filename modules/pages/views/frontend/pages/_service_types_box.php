<?php

use app\modules\service\models\Service;
use yii\helpers\Html;

$service_catalog = app\modules\pages\models\ServiceTypePage::find()->where(['model' => app\modules\pages\models\ServiceTypePage::class, 'visible' => 1])->one();
?>
<div class="experts_box">
    <?php foreach ($items as $service) { ?>
        <div class="material-item material-item-service">
            <div class="material-item-wrapper">
                <div class="material-item-info">
                    <?php if ($service->user) { ?>
                        <div class="material-item-autor">
                            <a href="<?= $service->user->getUrlPath(); ?>" class="material-item-autor-img">
                                <img src="<?= $service->user->profile->getThumb('image', 'main'); ?>" alt="">
                            </a>
                            <div class="material-item-autor-info">
                                <a href="<?= $service->user->getUrlPath(); ?>"
                                   class="material-item-autor-name"><?= $service->user->profile->getHalfname(' '); ?></a>
                                <div class="material-item-autor-status"><?= $service->user->roleName; ?></div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="material-item-service-wrapper">
                        <div class="material-item-service-info">
                            <a href="<?= $service->getUrlPath(); ?>"
                               class="material-item-text"><?= $service->name; ?></a>
                            <div class="material-item-text2"><?= $service->short_description; ?></div>
                            <div class="expert_item-tags-material">
                                <?php if ($service->kind != Service::KIND_ONLINE && $service->city) { ?>
                                    <?= Html::a('<b class="tag-hovered">' . mb_convert_case(mb_strtolower($service->city->name, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') . '</b><span>' . mb_convert_case(mb_strtolower($service->city->name, 'UTF-8'), MB_CASE_TITLE, 'UTF-8') . '</span></a>', $service_catalog->getUrlPath() . '?city[]=' . $service->city->id, ['class' => 'tag', 'data-city_id' => $service->city->id, 'data-city_name' => $service->city->name]); ?>
                                <?php } ?>
                                <?= Html::a('<b class="tag-hovered">' . $service->serviceType->name . '</b><span>' . $service->serviceType->name . '</span></a>', $service_catalog->getUrlPath() . '?service_types[]=' . $service->serviceType->id, ['class' => 'tag', 'data-type_id' => $service->serviceType->id, 'data-type_name' => $service->serviceType->name]); ?>
                            </div>
                        </div>
                        <div class="material-item-service-buttons">
                            <?php if ($service->type == Service::TYPE_TYPICAL) { ?>
                                <div class="services-expert-price"><?= number_format($service->price, 0, '', '&nbsp;'); ?>
                                    ₽ <?php if ($service->old_price && $service->old_price > $service->price) { ?><span
                                            class="services-expert-old-price"><?= number_format($service->old_price, 0, '', '&nbsp;'); ?>
                                        руб.</span><?php } ?></div>
                                <div data-service="<?= $service->id; ?>"
                                     class="button-o blue medium send_order orderCreate">Оплатить
                                </div>
                            <?php } ?>
                            <?php if ($service->type == Service::TYPE_CUSTOM) { ?>
                                <div class="service_item-price no-price">Узнать стоимость</div>
                                <div data-service="<?= $service->id; ?>"
                                     class="button-o blue medium send_order queryCreate">Запросить
                                </div>
                            <?php } ?>

                            <a href="<?= $service->getUrlPath(); ?>" class="button-o blue medium">Подробнее</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>