<?php

use app\modules\pages\models\OrdersListMks;
use app\modules\payment\models\Payment;
use yii\helpers\Url;

$order_list = OrdersListMks::find()->where(['model' => OrdersListMks::class, 'visible' => 1])->one();
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
                            <div>
                                <h4>Клиент:</h4>
                                <p><?= $order->user->profile->halfname; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lk_block">
                    <main class="lk_content lk_content-request_basic">
                        <p>Услуга:</p>
                        <h4><?= $order->service_name; ?></h4>
                        <div class="buttons">
                            <a href="#" class="button lk red reflect_status" data-action="close"
                               data-order="<?= $order->id; ?>">Закрыть заказ</a>
                            <a href="#" class="button lk darkgreen reflect_status" data-action="offer"
                               data-order="<?= $order->id; ?>">Предложить альтернативу</a>
                        </div>
                    </main>
                </div>
                <?= \app\modules\message\widgets\message\MessageWidget::widget(['order' => $order]); ?>
            </div>
            <?= \app\modules\users\widgets\profile\MksmenuWidget::widget(); ?>
        </div>
    </main>
    <div class="modal" id="success_order_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Изменение заказа</div>
                <p>При изменении заказа возникла ошибка.</p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>

        </div>
        <div class="modal_overlay"></div>
    </div>
<?php
$url_order = Url::toRoute(['/pages/orders/confirmorder']);
$js = <<<JS
    $('body').on('click','.reflect_status', function(e){
        e.preventDefault();
        let order = $(this).data('order');
        let action = $(this).data('action');
        let param = yii.getCsrfParam();
        let token = yii.getCsrfToken();
        $.ajax({
            type: 'POST',
            url: '{$url_order}',
            processData: true,
            dataType: 'json',
            data: {order:order,action:action,param:token},
            success: function(data) {
                if (data.status == 'success') {
                    // в случае успеха показать модалку и обновить данные на странице подключим pjax попозже
                    // $('#success_order_modal .success_box p').html(data.message);
                    // modalPos('#success_order_modal');
                    window.location.href = window.location.href;
                } else {
                    // в случае ошибки вывести сообщение
                    $('#success_order_modal .success_box p').html(data.message);
                    modalPos('#success_order_modal');
                }
            }
        });
    });
JS;

$this->registerJs($js);
?>