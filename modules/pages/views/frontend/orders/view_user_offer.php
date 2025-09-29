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
                            <p class="green"><?= $order->statusName; ?></p>
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

                    <?php if (!empty($order->execute_start)) { ?>
                        <p>Начало выполнения: <b
                                    class="big"><?= Yii::$app->formatter->asDatetime($order->execute_start, 'd.MM.y'); ?></b>
                        </p>
                    <?php } ?>
                    <?php if (!empty($order->execute_before)) { ?>
                        <p>Завершение выполнения: <b
                                    class="big"><?= Yii::$app->formatter->asDatetime($order->execute_before, 'd.MM.y'); ?></b>
                        </p>
                    <?php } ?>
                    <?php if (!empty($order->special)) { ?>
                        <p>Особые условия: <b class="big"><?= $order->special; ?></b></p>
                    <?php } ?>
                    <br>
                    <div class="buttons">
                        <a href="#" class="button lk open_slidebox" data-slide_box="history">История заказа</a>
                    </div>
                </main>
            </div>
            <div id="history" class="lk_block slide_box hidden">
                <main class="lk_content">
                    <h1 class="lk_block_title">История заказа</h1>
                    <section class="timeline_box">
                        <?php foreach ($order->history as $event) { ?>
                            <article class="timeline-row">
                                <div class="timeline-date"><?= Yii::$app->formatter->asDatetime($event->created_at, 'php:d.m.Y H:i'); ?></div>
                                <div class="timeline-info">
                                    <p><?= $event->event; ?></p>
                                </div>
                            </article>
                        <?php } ?>
                    </section>
                </main>
            </div>
            <?= \app\modules\message\widgets\message\MessageWidget::widget(['order' => $order]); ?>
        </div>

        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>