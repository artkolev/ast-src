<?php
/*
    @descr Заказы по программе ДПО в ЛК клиента
    @var $model Class app\modules\pages\models\LKEduprogClientOrder; текущая страница
    @action pages/eduprog/eduprog-client-order
*/

use app\modules\eduprog\models\Tariff;
use yii\widgets\Pjax;

?>
<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">
        <?php Pjax::begin(['id' => 'content_page', 'options' => ['class' => 'lk_maincol']]); ?>
        <?= $this->render('_client_eduprog_card', ['eduprog' => $eduprog]); ?>
        <?= $this->render('_client_submenu', ['eduprog' => $eduprog, 'model' => $model]); ?>
        <?php if (!empty($orders)) {
            foreach ($orders as $order) { ?>
                <div class="lk_order_item lk_order_item-dpo">
                    <div class="lk-event-info-wrapper">
                        <div class="lk-event-info"><span>Заказ №:</span> <?= $order->orderNum; ?></div>
                        <div class="lk-event-info"><span>Тариф:</span>
                            <?php $tariff_groups = $order->items_group;
                            if (!empty($tariff_groups)) {
                                foreach ($tariff_groups as $tariff_id => $count) {
                                    $tariff = Tariff::findOne($tariff_id);
                                    $tariff_name = (!empty($tariff) ? $tariff->name : 'Тариф удалён');
                                    echo $tariff_name . ' X ' . $count . '<br>';
                                }
                            } ?>
                        </div>
                        <div class="lk-event-info">
                            <span><?= $order->payment?->getLKTitlePaymentDate() ?? 'Оплачен:' ?></span><?= Yii::$app->formatter->asDatetime($order->payment->payment_date, 'php:d.m.Y'); ?>
                        </div>
                        <div class="lk-event-info">
                            <span>Стоимость:</span><?= number_format($order->price, 0, '.', ' '); ?> ₽
                        </div>
                        <div class="members-block_rows">
                            <h4 class="members-block-title">Слушатели:</h4>
                            <div class="members-list">
                                <div class="members-list-row members-list-row-head">
                                    <div class="members-list-col"><strong class="desktop-visible">ФИО:</strong></div>
                                    <div class="members-list-col"><strong class="desktop-visible">E-mail:</strong></div>
                                </div>
                                <?php foreach ($order->items as $item) { ?>
                                    <div class="members-list-row">
                                        <div class="members-list-col"><strong
                                                    class="mobile-visible">ФИО: </strong><?= $item->user->profile->fullname ?>
                                        </div>
                                        <div class="members-list-col"><strong
                                                    class="mobile-visible">E-mail: </strong><?= $item->user->email ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }
        } ?>
        <?php Pjax::end(); ?>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>