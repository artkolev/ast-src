<?php

use app\modules\pages\models\OrdersList;
use app\modules\payment\models\Payment;
use yii\helpers\Url;

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
                                <p class="darkgreen"><?= $order->statusName; ?></p>
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
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content lk_content-basic">
                        <p>Завершение заказа:</p>
                        <h4>Заказ выполнен Экспертом.<br>Закрыть заказ.</h4>
                        <a href="#" class="button lk reflect_status" data-action="accept"
                           data-order="<?= $order->id; ?>">Подтверждаю, заказ выполнен</a>
                        <p><a class="astext open_slidebox" href="#" data-slide_box="start_discus">Заказ не выполнен</a>
                        </p>
                    </main>
                </div>

                <div id="start_discus" class="lk_block slide_box hidden">
                    <main class="lk_content lk_content-request_basic">
                        <h4>Вы уверены, что хотите отказаться от принятия заказа?</h4>
                        <p>Укажите причину отказа*</p>
                        <div class="ip_cell w100">
                            <textarea id="reason_text" class="input_text"
                                      placeholder="Введите сообщение (до 300 символов)."></textarea>
                        </div>
                        <div class="ip_cell w100">
                            <a href="#" class="button-o medium lk reflect_status" data-action="discus"
                               data-order="<?= $order->id; ?>">Отправить обращение</a>
                            <a href="#" class="button blue w180px lk close_slidebox" data-slide_box="start_discus">Отменить</a>
                        </div>
                    </main>
                </div>
                <?= \app\modules\message\widgets\message\MessageWidget::widget(['order' => $order]); ?>
            </div>

            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
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
        let reason = $('#reason_text').val();
        let param = yii.getCsrfParam();
        let token = yii.getCsrfToken();
        $.ajax({
            type: 'POST',
            url: '{$url_order}',
            processData: true,
            dataType: 'json',
            data: {order:order,action:action,reason:reason,param:token},
            success: function(data){
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