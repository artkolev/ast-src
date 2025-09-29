<?php
/*
    @descr Заказы слушателя ДПО в ЛК
    @var $model Class app\modules\pages\models\LKEduprogViewMemberOrders; текущая страница
    @action pages/eduprog/eduprog-view-member-orders
*/

use app\modules\eduprog\models\Tariff;
use yii\helpers\Url;

?>

<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">
        <div class="lk_maincol">
            <?php if (!empty($parent_page)) { ?>
                <div class="ip_cell w100">
                    <a href="<?= Url::toRoute([$parent_page->getUrlPath(), 'id' => $member->eduprog_id]); ?>"
                       class="button-o back">Программа</a>
                </div>
            <?php } ?>

            <?= $this->render('_expert_member_card', ['member' => $member]); ?>
            <?= $this->render('_change_status_member_engine', ['member_type' => 'member_page']); ?>
            <?= $this->render('_expert_member_submenu', ['member' => $member, 'model' => $model]); ?>

            <?php if (!empty($orders)) {
                foreach ($orders as $order) { ?>
                    <div class="lk_order_item lk_order_item-dpo">
                        <div class="lk-event-info-wrapper">
                            <div class="lk-event-info"><span>Заказ №:</span> <?= $order->orderNum; ?></div>
                            <div class="lk-event-info"><span>Тариф:</span>
                                <?php
                                $tariff_groups = $order->items_group;
                                if (!empty($tariff_groups)) {
                                    foreach ($tariff_groups as $tariff_id => $count) {
                                        $tariff = Tariff::findOne($tariff_id);
                                        $tariff_name = (!empty($tariff) ? $tariff->name : 'Тариф удалён');
                                        echo $tariff_name . ' X ' . $count . '<br>';
                                    }
                                }
                                ?>
                            </div>
                            <div class="lk-event-info">
                                <span><?= $order->payment?->getLKTitlePaymentDate() ?? 'Оплачен:' ?></span><?= Yii::$app->formatter->asDatetime($order->payment->payment_date, 'php:d.m.Y'); ?>
                            </div>
                            <div class="lk-event-info">
                                <span>Стоимость:</span><?= number_format($order->price, 0, '.', ' '); ?> ₽
                            </div>
                            <div class="lk-event-info"><span>Слушатель:</span><?= $order->user->profile->fullname ?>
                            </div>
                            <div class="lk-event-info"><span>E-mail слушателя:</span><?= $order->user->email ?></div>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p>Данный слушатель не является плательщиком</p>
            <?php } ?>
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>