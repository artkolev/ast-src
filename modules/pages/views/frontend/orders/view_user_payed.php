<?php

use app\modules\pages\models\OrdersList;
use app\modules\payment\models\Payment;

$order_list = OrdersList::find()->where(['model' => OrdersList::class, 'visible' => 1])->one();
$orders_url = (!empty($order_list)) ? $order_list->getUrlPath() : false;
?>
<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">
        <div class="lk_maincol">
            <?php if ($orders_url) { ?>
                <a href="<?= $orders_url; ?>" class="button-o back">Вернуться к заказам</a>
            <?php } ?>

            <div class="lk_order_item">
                <div class="lk_order_item_info-basic">
                    <div class="grid-template-a">
                        <div>
                            <h4>Заказ №:</h4>
                            <p><?= $order->orderNum; ?></p>
                        </div>
                        <?php if ($order->payment && ($order->payment->status == Payment::STATUS_ACCEPTED)) { ?>
                            <div>
                                <h4><?= $order->payment?->getLKTitlePaymentDate() ?? 'Оплачен:' ?></h4>
                                <p><?= Yii::$app->formatter->asDatetime($order->payment->payment_date, 'd.MM.y'); ?></p>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="grid-template-b">
                        <div>
                            <h4>Статус:</h4>
                            <p class="blue"><?= $order->statusName; ?></p>
                        </div>
                        <div>
                            <h4>Эксперт:</h4>
                            <p><?= $order->executor->profile->halfname; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lk_block">
                <main class="lk_content lk_content-request_basic">
                    <p>Услуга:</p>
                    <h4><?= $order->service_name; ?></h4>
                </main>
            </div>
            <?= \app\modules\message\widgets\message\MessageWidget::widget(['order' => $order]); ?>
        </div>

        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>