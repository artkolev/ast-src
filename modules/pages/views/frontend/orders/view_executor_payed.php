<?php

use app\models\OrderPriceForm;
use app\modules\pages\models\OrdersListIncom;
use app\modules\payment\models\Payment;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$order_list = OrdersListIncom::find()->where(['model' => OrdersListIncom::class, 'visible' => 1])->one();
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
                            <a href="#" class="button lk open_slidebox" data-slide_box="dead_line">Уточнить условия</a>
                        </div>
                    </main>
                </div>

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
                            <a href="#" class="button-o blue lk w180px close_slidebox"
                               data-slide_box="dead_line">Отмена</a>
                            <button type="submit" class="button lk w180px mb0">Применить</button>
                        </div>
                    </main>
                    <?php ActiveForm::end(); ?>
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
$js = <<<JS
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
JS;

$this->registerJs($js);
?>