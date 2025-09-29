<?php

use app\models\OrderRejectForm;
use app\modules\pages\models\OrdersList;
use app\modules\payment\models\Payment;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$order_list = OrdersList::find()->where(['model' => OrdersList::class, 'visible' => 1])->one();
$orders_url = (!empty($order_list)) ? $order_list->getUrlPath() : false;
$has_offered = (!empty($order->offered_datestart) or !empty($order->offered_dateend) or !empty($order->offered_special));
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
                                <p class="yellow"><?= $order->statusName; ?></p>
                            </div>
                            <div>
                                <h4>Эксперт:</h4>
                                <p><?= $order->executor->profile->halfname; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ($has_offered) { ?>
                    <div class="lk_block">
                        <main class="lk_content lk_content-request_basic">
                            <p>Изменение условий:</p>
                            <h4>Эксперт отправил на согласование условия.</h4>

                            <?php if (!empty($order->offered_datestart)) { ?>
                                <p>Начало выполнения: <b
                                            class="big"><?= Yii::$app->formatter->asDatetime($order->offered_datestart, 'd.MM.y'); ?></b>
                                </p>
                            <?php } ?>
                            <?php if (!empty($order->offered_dateend)) { ?>
                                <p>Завершение выполнения: <b
                                            class="big"><?= Yii::$app->formatter->asDatetime($order->offered_dateend, 'd.MM.y'); ?></b>
                                </p>
                            <?php } ?>
                            <?php if (!empty($order->offered_special)) { ?>
                                <p>Особые условия: <b class="big"><?= $order->offered_special; ?></b></p>
                            <?php } ?>
                            <br>
                            <div class="buttons">
                                <a href="#" class="button w180px gray lk change_deadline" data-action="decline"
                                   data-order="<?= $order->id; ?>">Отклонить условия</a>
                                <a href="#" class="button w180px lk change_deadline" data-action="accept"
                                   data-order="<?= $order->id; ?>">Принять условия</a>

                            </div>
                        </main>
                    </div>
                <?php } ?>
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
                            <a href="#"
                               class="button lk gray open_slidebox <?= (strtotime($order->execute_start) < time() + 48 * 60 * 60) ? 'disabled' : ''; ?>"
                               data-slide_box="reason">Отменить заказ</a>
                        </div>
                    </main>
                </div>

                <?php
                if (strtotime($order->execute_start) >= time() + 48 * 60 * 60) {
                    $modelform = new OrderRejectForm();
                    $modelform->order_id = $order->id; ?>
                    <div id="reason" class="lk_block slide_box hidden">
                        <main class="lk_content">
                            <?php $form = ActiveForm::begin([
                                    'id' => 'reason-form',
                                    'action' => '/site/ajaxValidate/',
                                    'options' => ['class' => ''],
                                    'enableAjaxValidation' => true,
                                    'enableClientValidation' => true,
                                    'validateOnSubmit' => true,
                                    'validateOnChange' => true,
                                    'validateOnType' => false,
                                    'validateOnBlur' => true,
                                    'fieldConfig' => [
                                            'options' => ['class' => 'ip_cell w100'],
                                            'template' => '{input}{error}{hint}',
                                            'inputOptions' => ['class' => 'input_text'],
                                    ],
                            ]); ?>
                            <h1 class="lk_block_title">Вы уверены, что хотите отменить заказ?</h1>
                            <p>Укажите причину отмены заказа. Эксперт получит сообщение и причину отказа.</p>
                            <?= $form->field($modelform, 'order_id')->hiddenInput(); ?>
                            <?= $form->field($modelform, 'reason_id')->checkBoxList(
                                    $modelform->getReasonList(),
                                    ['item' => function ($index, $label, $name, $checked, $value) {
                                        $return = '<div class="ip_cell w100"><input type="checkbox" name="' . $name . '" value="' . $value . '" class="ch"><label>' . $label . '</label></div>';
                                        return $return;
                                    }
                                    ]
                            ); ?>
                            <div class="lk_podpis left">Другое</div>
                            <?= $form->field($modelform, 'reason_text')->textArea(['placeholder' => 'Укажите причину']); ?>
                            <div class="ip_cell w100">
                                <button type="submit" class="button-o medium lk">Отправить ответ и отменить</button>
                                <a href="#" class="button-o blue medium lk close_slidebox" data-slide_box="reason">Не
                                    отменять</a>
                            </div>
                            <?php ActiveForm::end(); ?>
                        </main>
                    </div>
                <?php } ?>

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
$url_order = Url::toRoute(['/pages/orders/changedeadline']);
$url_order_reject = Url::toRoute(['/pages/orders/rejectorder']);
$js = <<<JS
    $('body').on('click','.change_deadline', function(e) {
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

    $('#reason-form').on('beforeSubmit', function(event) {
        var formData = new FormData($('#reason-form')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url_order_reject}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
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
        return false;
    });
    $('#reason-form').on('submit', function(e){
        e.preventDefault();
        return false;
    });
JS;

$this->registerJs($js);
?>