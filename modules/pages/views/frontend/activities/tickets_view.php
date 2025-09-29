<?php

use app\modules\events\models\Events;
use app\modules\pages\models\LKEventsTickets;

$parent_page = LKEventsTickets::find()->where(['visible' => 1, 'model' => LKEventsTickets::class])->one();

$location_icons = [Events::TYPE_ONLINE => 'location-online', Events::TYPE_OFFLINE => 'location-offline', Events::TYPE_HYBRID => 'location-gibrid'];

$list_type = [
        Events::TYPE_ONLINE => 'Онлайн',
        Events::TYPE_OFFLINE => 'Офлайн',
        Events::TYPE_HYBRID => 'Гибридное',
];

$price_text = '';
if ($event->need_tariff) {
    if (!empty($event->eventsForms)) {
        $price_text = [];
        foreach ($event->eventsForms as $event_form) {
            $price_text[] = ($event_form->payregister ? 'от ' . number_format($event_form->minTariffPrice, 0, '', ' ') . ' ₽' : 'Бесплатно');
        }
        $price_text = array_unique($price_text);
        $price_text = implode('/', $price_text);
    }
} else {
    $price_text = 'Регистрация не требуется';
}

?>

<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">
        <div class="lk_maincol">
            <?php if ($parent_page) { ?>
                <div class="ip_cell w100">
                    <a href="<?= $parent_page->getUrlPath(); ?>" class="button-o back">Билеты</a>
                </div>
            <?php } ?>
            <div class="lk_order_item">
                <h4 class="lk-order-title"><?= $event->name; ?></h4>
                <div class="lk-event-info-wrapper">
                    <div class="lk-event-info price"><?= $price_text; ?></div>
                    <div class="lk-event-info <?= $location_icons[$event->type]; ?>"><?= (!empty($event->prettyPlace) ? $event->prettyPlace : $list_type[$event->type]); ?></div>
                    <?php if ($event->format) { ?>
                        <div class="lk-event-info learn"><?= $event->format->name; ?></div>
                    <?php } else { ?>
                        <div class="lk-event-info"></div>
                    <?php } ?>
                    <div class="lk-event-info date"><?= app\helpers\MainHelper::printDateRange($event); ?></div>
                </div>
                <div class="lk-event-buttons">
                    <?php if ($event->status == 'published') { ?>
                        <a href="<?= $event->getUrlPath(); ?>" class="button-o small site_open gray" target="_blank">Открыть
                            на сайте</a>
                    <?php } ?>
                </div>
            </div>

            <?php if (!empty($tariffs_ids)) {
                foreach ($tariffs_ids as $tariff_id) { ?>
                    <div class="lk_block">
                        <div class="lk_content">
                            <div class="flex align-center tarif-transaction-info">
                                <h4 class="lk_step_title font20 mr30">
                                    Тариф: <?= $tickets_list[$tariff_id][0]->tarifName; ?></h4>
                            </div>
                            <div class="tarif-table-wrapper">
                                <table class="table tarif-table transaction-table">
                                    <tr>
                                        <th class="desktop-visible">Дата покупки</th>
                                        <th class="desktop-visible">№ заказа</th>
                                        <th class="desktop-visible">№ билета</th>
                                        <th class="mobile-visible">Билеты</th>
                                        <th class="centered">Цена</th>
                                        <th style="display: none;"></th>
                                        <th style="display: none;"></th>
                                    </tr>
                                    <?php foreach ($tickets_list[$tariff_id] as $ticket) { ?>
                                        <tr>
                                            <td class="desktop-visible"><?= Yii::$app->formatter->asDatetime($ticket->order->created_at, 'php:d.m.Y'); ?></td>
                                            <td class="desktop-visible"><?= $ticket->order->orderNum; ?></td>
                                            <td class="desktop-visible"><?= $ticket->ticketNum; ?></td>
                                            <td class="mobile-visible">
                                                <?= Yii::$app->formatter->asDatetime($ticket->order->created_at, 'php:d.m.Y'); ?>
                                                <br>
                                                <?= $ticket->order->orderNum; ?> <br>
                                                Цена: <?= number_format($ticket->price, 0, '.', '&nbsp;'); ?> ₽ <br>
                                            </td>
                                            <td class="centered"><?= number_format($ticket->price, 0, '.', '&nbsp;'); ?>
                                                ₽
                                            </td>
                                            <?php if (Yii::$app->params['enable_tickets']) { ?>
                                                <td class="centered"><a style="color: #0086FF;"
                                                                        href="<?= $ticket->ticketUrl; ?>">Скачать</a>
                                                </td><?php } ?>
                                            <th style="display: none;"></th>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>