<?php

use app\models\OrderPriceForm;
use app\models\OrderRejectForm;
use app\modules\pages\models\OrdersListIncom;
use app\modules\payment\models\Payment;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$order_list = OrdersListIncom::find()->where(['model' => OrdersListIncom::class, 'visible' => 1])->one();
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
                                <h4>Клиент:</h4>
                                <p><?= $order->user->profile->halfname; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ($has_offered) { ?>
                    <div class="lk_block">
                        <main class="lk_content lk_content-request_basic">
                            <p>Изменение условий:</p>
                            <h4>Вы отправили на согласование новые условия исполнения заказа. Дождитесь ответа.</h4>

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
                                <a href="#" class="button-o medium lk change_deadline" data-action="decline"
                                   data-order="<?= $order->id; ?>">Отменить изменение условий</a>
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
                            <a href="#" class="button lk gray open_slidebox" data-slide_box="reason">Отменить заказ</a>
                            <a href="#" class="button lk <?= (!$has_offered ? 'open_slidebox' : 'disabled'); ?>"
                               data-slide_box="dead_line">Уточнить условия</a>
                            <a href="#"
                               class="button lk <?= ((empty($order->execute_start) && empty($order->execute_before)) ? 'disabled' : 'open_slidebox'); ?>"
                               data-slide_box="order_done">Заказ выполнен</a>
                        </div>
                    </main>
                </div>
                <?php if (!$has_offered) { ?>
                    <?php $modelform = new OrderPriceForm();
                    $modelform->order_id = $order->id;
                    if (!empty($order->offered_datestart)) {
                        $modelform->date_start = Yii::$app->formatter->asDatetime($order->offered_datestart, 'd.MM.y');
                    } elseif (!empty($order->execute_start)) {
                        $modelform->date_start = Yii::$app->formatter->asDatetime($order->execute_start, 'd.MM.y');
                    }
                    if (!empty($order->offered_dateend)) {
                        $modelform->date_end = Yii::$app->formatter->asDatetime($order->offered_dateend, 'd.MM.y');
                    } elseif (!empty($order->execute_before)) {
                        $modelform->date_end = Yii::$app->formatter->asDatetime($order->execute_before, 'd.MM.y');
                    }
                    $modelform->special = (!empty($order->offered_special) ? $order->offered_special : $order->special);
                    ?>
                    <div id="dead_line" class="lk_block lk_block-change-service_basic slide_box hidden">
                        <?php $form = ActiveForm::begin([
                                'id' => 'price-form',
                                'action' => '/site/ajaxValidate/',
                                'options' => ['class' => 'marked'],
                                'enableAjaxValidation' => true,
                                'enableClientValidation' => true,
                                'validateOnSubmit' => true,
                                'validateOnChange' => true,
                                'validateOnType' => false,
                                'validateOnBlur' => true,
                                'fieldConfig' => [
                                        'options' => ['class' => 'ip_cell lined w100'],
                                        'template' => '{label}{input}{error}{hint}',
                                        'inputOptions' => ['class' => 'input_text'],
                                        'labelOptions' => ['class' => 'ip_label'],
                                ],
                        ]); ?>
                        <span style="display:none;">
                            <?= $form->field($modelform, 'order_id')->hiddenInput(); ?>
                        </span>
                        <header class="lk_block_header">
                            <h4>Условия </h4>
                            <p>Укажите срок выполнения заказа и особые условия.</p>
                            <span class="dateRange">
                                <?= $form->field($modelform, 'date_start')->textInput(['class' => 'input_text ip_medium datepicker keypress date-mask start lk_current_date_default w206px input_change-service_blue']); ?>
                                <?= $form->field($modelform, 'date_end')->textInput(['class' => 'input_text ip_medium datepicker keypress date-mask end lk_current_date_default w206px input_change-service_blue']); ?>
                            </span>
                        </header>
                        <main class="lk_content lk_content-change-service_basic">
                            <?= $form->field($modelform, 'special', ['options' => ['class' => 'ip_cell w100'], 'template' => '{label}<div class="symbols_counter_box">{input}<span class="symbols_counter"></span></div>{error}{hint}'])->textArea(['class' => 'input_text limitedSybmbols', 'maxlength' => '300']); ?>
                            <div class="buttons">
                                <a href="#" class="button-o blue lk w180px close_slidebox" data-slide_box="dead_line">Отмена</a>
                                <button type="submit" class="button lk w180px mb0">Применить</button>
                            </div>
                        </main>
                        <?php ActiveForm::end(); ?>
                    </div>
                <?php } ?>

                <div id="order_done" class="lk_block slide_box hidden">
                    <main class="lk_content lk_content-request_basic">
                        <p>Завершение заказа:</p>
                        <h4>Вы завершаете выполнение заказа. Клиент должен будет подтвердить, что услуга оказана
                            качественно, в срок и у него нет претензий.</h4>
                        <br>
                        <div class="buttons">
                            <a href="#" class="button-o blue lk close_slidebox" data-slide_box="order_done">Отмена</a>
                            <a href="#"
                               class="button lk  <?= ((empty($order->execute_start) && empty($order->execute_before)) ? 'disabled' : 'finalize_order'); ?>"
                               data-order="<?= $order->id; ?>">Подтверждаю, заказ выполнен</a>
                        </div>
                    </main>
                </div>

                <?php $modelform = new OrderRejectForm();
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
                        <p>Укажите причину отмены заказа. Клиент получит сообщение и причину отказа.</p>
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
$url_order = Url::toRoute(['/pages/orders/newdeadline']);
$url_order_exec = Url::toRoute(['/pages/orders/execorder']);
$url_order_reject = Url::toRoute(['/pages/orders/rejectorder']);
$url_order_decline_deadline = Url::toRoute(['/pages/orders/changedeadline']);
$js = <<<JS
    $('body').on('click','.change_deadline', function(e){
        e.preventDefault();
        let order = $(this).data('order');
        let action = $(this).data('action');
        let param = yii.getCsrfParam();
        let token = yii.getCsrfToken();
        $.ajax({
            type: 'POST',
            url: '{$url_order_decline_deadline}',
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
    
    $('#price-form').on('beforeSubmit', function(event) {
        var formData = new FormData($('#price-form')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url_order}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    // в случае успеха показать модалку и обновить данные на странице подключим pjax попозже
                        // $('#success_queries_modal .success_box p').html(data.message);
                        // modalPos('#success_queries_modal');

                    window.location.href = window.location.href;
                } else {
                    // в случае ошибки вывести сообщение
                    $('#success_queries_modal .success_box p').html(data.message);
                    modalPos('#success_queries_modal');
                }
            }
        });
        return false;
    });
    $('#price-form').on('submit', function(e){
        e.preventDefault();
        return false;
    });



    $('body').on('click','.finalize_order', function(e){
        e.preventDefault();
        let order = $(this).data('order');
        let param = yii.getCsrfParam();
        let token = yii.getCsrfToken();
        $.ajax({
            type: 'POST',
            url: '{$url_order_exec}',
            processData: true,
            dataType: 'json',
            data: {order:order,param:token},
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