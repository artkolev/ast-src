<?php

use app\modules\events\models\Events;
use app\modules\pages\models\LKEventsTicketsView;
use yii\helpers\Url;

$view_page = LKEventsTicketsView::find()->where(['model' => LKEventsTicketsView::class, 'visible' => 1])->one();
$view_url = (!empty($view_page) ? $view_page->getUrlPath() : false);

$location_icons = [Events::TYPE_ONLINE => 'location-online', Events::TYPE_OFFLINE => 'location-offline', Events::TYPE_HYBRID => 'location-gibrid'];

$list_type = [
        Events::TYPE_ONLINE => 'Онлайн',
        Events::TYPE_OFFLINE => 'Офлайн',
        Events::TYPE_HYBRID => 'Гибридное',
];
?>
<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">

        <div class="lk_maincol">
            <div class="lk_block">
                <header class="lk_block_header">
                    <h1 class="lk_block_title-big mb0"><?= $model->getNameForView(); ?></h1>
                    <?php if (!empty($model->content)) { ?>
                        <div class="mt20"><?= $model->content; ?></div>
                    <?php } ?>
                </header>
            </div>
            <?php
            if (!empty($items)) {
                foreach ($items as $item) {
                    $price_text = '';
                    if ($item->need_tariff) {
                        if (!empty($item->eventsForms)) {
                            $price_text = [];
                            foreach ($item->eventsForms as $event_form) {
                                $price_text[] = ($event_form->payregister ? 'от ' . number_format($event_form->minTariffPrice, 0, '', ' ') . ' ₽' : 'Бесплатно');
                            }
                            $price_text = array_unique($price_text);
                            $price_text = implode('/', $price_text);
                        }
                    } else {
                        $price_text = 'Регистрация не требуется';
                    } ?>
                    <div class="lk_order_item">
                        <a href="<?= ($view_url ? Url::toRoute([$view_url, 'id' => $item->id]) : ''); ?>"
                           class="blue lk_order_more lk_order_more-basic"><img src="/img/nav_right-white.svg"
                                                                               alt=""/></a>
                        <h4 class="lk-order-title"><?= $item->name; ?></h4>
                        <div class="lk-event-info-wrapper">
                            <div class="lk-event-info price"><?= $price_text; ?></div>
                            <div class="lk-event-info <?= $location_icons[$item->type]; ?>"><?= (!empty($item->prettyPlace) ? $item->prettyPlace : $list_type[$item->type]); ?></div>
                            <?php if ($item->format) { ?>
                                <div class="lk-event-info learn"><?= $item->format->name; ?></div>
                            <?php } else { ?>
                                <div class="lk-event-info"></div>
                            <?php } ?>
                            <div class="lk-event-info date"><?= app\helpers\MainHelper::printDateRange($item); ?></div>
                        </div>

                        <div class="lk-event-buttons">
                            <?php if (in_array($item->statusFull, ['published', 'cancelled'])) { ?>
                                <a href="<?= $item->getUrlPath(); ?>" class="site_open button-o small gray"
                                   target="_blank">Открыть на сайте</a>
                            <?php } ?>
                            <a href="<?= ($view_url ? Url::toRoute([$view_url, 'id' => $item->id]) : ''); ?>"
                               class="button-o small gray">Просмотр</a>
                        </div>
                        <a href="<?= ($view_url ? Url::toRoute([$view_url, 'id' => $item->id]) : ''); ?>"
                           class="realblue lk_order_more-basic_mobile">Перейти<img src="/img/nav_right-white.svg"
                                                                                   alt=""/></a>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="lk_block">
                    <div class="lk_content">
                        Вы еще не приобретали билеты на мероприятия
                    </div>
                </div>
            <?php } ?>
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>