<?php

use app\models\QueriesRejectForm;
use app\modules\pages\models\QueriesList;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$queries_list = QueriesList::find()->where(['model' => QueriesList::class, 'visible' => 1])->one();
$queries_url = (!empty($queries_list)) ? $queries_list->getUrlPath() : false;
?>
<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">
        <div class="lk_maincol">
            <?php if ($queries_url) { ?>
                <a href="<?= $queries_url; ?>" class="button-o back">Вернуться к запросам</a>
            <?php } ?>
            <!-- BLOCK-->
            <div class="lk_order_item">
                <div class="lk_order_item_info-basic">
                    <div class="grid-template-a">
                        <div><h4>Запрос №:</h4>
                            <p><?= $query->queryNum; ?></p></div>
                        <div><h4>Отправлен:</h4>
                            <p><?= Yii::$app->formatter->asDatetime($query->created_at, 'd.MM.y'); ?></p></div>
                    </div>
                    <div class="grid-template-b">
                        <div><h4>Статус:</h4>
                            <p class="blue"><?= $query->statusName; ?></p></div>
                        <div><h4>Эксперт:</h4>
                            <p><?= $query->executor->profile->halfname; ?></p></div>
                    </div>
                </div>
            </div>
            <div class="lk_block">
                <main class="lk_content lk_content-basic">
                    <p>Услуга:</p>
                    <h4><?= $query->service_name; ?></h4>
                    <?= !empty($query->service_descr) ? '<h4>' . $query->service_descr . '</h4>' : ''; ?>
                    <?php if (!empty($query->user_comment)) { ?>
                        <p>Комментарий:</p>
                        <h5><?= $query->user_comment; ?></h5>
                    <?php } ?>
                </main>
            </div>
            <div class="lk_block">
                <main class="lk_content lk_content-request_basic">
                    <h4>Вы оформили запрос. Дождитесь, когда эксперт ответит.</h4>
                    <a href="" class="button lk gray open_slidebox" data-slide_box="reason">Отменить запрос</a>
                </main>
            </div>

            <?php $modelform = new QueriesRejectForm();
            $modelform->query_id = $query->id; ?>
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
                    <h1 class="lk_block_title">Вы уверены, что хотите отменить запрос?</h1>
                    <p>Укажите причину отмены запроса. Исполнитель получит сообщение и причину отказа.</p>
                    <?= $form->field($modelform, 'query_id')->hiddenInput(); ?>
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
            <?= \app\modules\message\widgets\message\MessageWidget::widget(['query' => $query]); ?>
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>

<div class="modal" id="success_queries_modal">
    <div class="modal_content">
        <a href="#" class="modal_close">x</a>
        <div class="success_box">
            <div class="modal_title">Изменение запроса</div>
            <p>При изменении запроса возникла ошибка.</p>
            <div class="modal_buttons">
                <a href="#" class="button small close_modal">ОК</a>
            </div>
        </div>

    </div>
    <div class="modal_overlay"></div>
</div>
<?php
$url_queries_reject = Url::toRoute(['/pages/queries/rejectquery']);
$js = <<<JS
    $('#reason-form').on('beforeSubmit', function(event) {
        var formData = new FormData($('#reason-form')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url_queries_reject}',
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
    $('#reason-form').on('submit', function(e){
        e.preventDefault();
        return false;
    });


JS;

$this->registerJs($js);
?>
