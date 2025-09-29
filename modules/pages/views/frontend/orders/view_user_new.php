<?php

use app\modules\pages\models\OrdersList;
use app\modules\pages\models\SelectPayment;
use app\modules\payment_system\models\PaymentSystem;
use yii\helpers\Url;

$payment_page = SelectPayment::find()->where(['model' => SelectPayment::class, 'visible' => 1])->one();
$order_list = OrdersList::find()->where(['model' => OrdersList::class, 'visible' => 1])->one();
$payment_url = (!empty($payment_page)) ? Url::toRoute([$payment_page->getUrlPath(), 'category' => PaymentSystem::USEDIN_SERVICES, 'id' => $order->id]) : false;
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
                    </div>
                    <div class="grid-template-b">
                        <div>
                            <h4>Статус:</h4>
                            <p class="lightGray"><?= $order->statusName; ?></p>
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
                    <div class="buttons">
                        <?php if ($payment_url) { ?>
                            <a href="<?= $payment_url; ?>" class="button lk w180px">Оплатить</a>
                        <?php } ?>
                    </div>
                </main>
            </div>
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>