<?php

use app\modules\events\models\Events;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerJsFile('/js/lk-events.js', ['depends' => [app\assets\AppAsset::class]]);
?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <div class="lk_block">
                    <header class="lk_block_header">
                        <h1 class="lk_block_title-big"><?= (empty($original) or ($original->status == Events::STATUS_NEW)) ? 'Добавить мероприятие' : 'Редактирование мероприятия'; ?></h1>
                        <div class="lk_block_subtitle"><?= $model->content; ?></div>
                    </header>
                </div>
                <div class="lk-event-reg-steps">
                    <div class="lk-event-reg-step active">
                        <div class="lk-event-reg-step-num">1</div>
                        <div class="lk-event-reg-step-name">Описание</div>
                    </div>
                    <a <?= ((empty($original) or ($original->status == Events::STATUS_NEW)) ? '' : 'href="' . Url::toRoute([$model->getUrlPath(), 'step' => 2, 'id' => $original->id]) . '" '); ?>class="lk-event-reg-step">
                        <div class="lk-event-reg-step-num">2</div>
                        <div class="lk-event-reg-step-name">Условия</div>
                    </a>
                    <a <?= ((empty($original) or ($original->status == Events::STATUS_NEW)) ? '' : 'href="' . Url::toRoute([$model->getUrlPath(), 'step' => 3, 'id' => $original->id]) . '" '); ?>class="lk-event-reg-step">
                        <div class="lk-event-reg-step-num">3</div>
                        <div class="lk-event-reg-step-name">О мероприятии</div>
                    </a>
                    <a <?= ((empty($original) or ($original->status == Events::STATUS_NEW)) ? '' : 'href="' . Url::toRoute([$model->getUrlPath(), 'step' => 4, 'id' => $original->id]) . '" '); ?>class="lk-event-reg-step">
                        <div class="lk-event-reg-step-num">4</div>
                        <div class="lk-event-reg-step-name">Регистрация</div>
                    </a>
                    <a <?= ((empty($original) or ($original->status == Events::STATUS_NEW)) ? '' : 'href="' . Url::toRoute([$model->getUrlPath(), 'step' => 5, 'id' => $original->id]) . '" '); ?>class="lk-event-reg-step">
                        <div class="lk-event-reg-step-num">5</div>
                        <div class="lk-event-reg-step-name">Публикация</div>
                    </a>
                </div>
                <div class="lk_block">
                    <main class="lk_content">
                        <?php $form = ActiveForm::begin([
                                'id' => 'event-form',
                                'action' => '/site/ajaxValidate/',
                                'options' => ['class' => 'marked'],
                                'enableAjaxValidation' => true,
                                'enableClientValidation' => true,
                                'validateOnSubmit' => true,
                                'validateOnChange' => true,
                                'validateOnType' => false,
                                'validateOnBlur' => true,
                                'fieldConfig' => [
                                        'options' => ['class' => 'ip_cell w100'],
                                        'template' => '{label}{input}{error}{hint}',
                                        'inputOptions' => ['class' => 'input_text'],
                                        'labelOptions' => ['class' => 'ip_label'],
                                ],
                        ]); ?>
                        <?= $form->field($event_model, 'id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
                        <?= $form->field($event_model, 'step', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
                        <?= $form->field($event_model, 'name', ['template' => '<div class="symbols_counter_box">{label}{input}<span class="symbols_counter"></span></div>{error}{hint}'])->textInput(['autocomplete' => 'off', 'placeholder' => "Название пишется без кавычек", 'class' => 'input_text limitedSybmbols', 'maxlength' => 90]); ?>

                        <?= $form->field($event_model, 'anons', ['template' => '<div class="symbols_counter_box">{label}{input}<span class="symbols_counter"></span></div>{error}{hint}'])->textArea(['autocomplete' => 'off', 'placeholder' => "Например: III Всероссийская конференция", 'class' => 'input_text limitedSybmbols', 'maxlength' => 150]); ?>

                        <?= $form->field($event_model, 'format_id')->dropDownList($event_model->formatList, ['class' => "pretty_select", 'prompt' => 'Выберите вид мероприятия',]); ?>
                        <?= $form->field($event_model, 'age_id')->dropDownList($event_model->ageList, ['class' => "pretty_select"]); ?>
                        <?= $form->field($event_model, 'licensed', ['template' => '{input}{label}{hint}{error}'])->checkbox(['class' => 'ch normal'], false); ?>
                        <div class="ip_cell w100 flex flex-end mb0">
                            <button type="submit" class="button blue medium lk">Продолжить</button>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </main>
                </div>
            </div>
            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
        </div>
    </main>
    <div class="modal" id="fail_service_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Ошибка создания мероприятия</div>
                <p>При сохранении данных возникли ошибки. <?= \app\helpers\MainHelper::getHelpText() ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
<?php
$url = Url::toRoute(['/pages/activities/saveevent', 'step' => 1]);
$js = <<<JS
    $('#event-form').on('beforeSubmit', function(event){
        var formData = new FormData($('#event-form')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    // переадресация на второй шаг
                    window.location.href = data.redirect_to;
                } else {
                    // показать модалку с ошибкой
                    $('#fail_service_modal .modal_title').html('Ошибка создания мероприятия');
                    $('#fail_service_modal p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
        return false;
    });
    $('#event-form').on('submit', function(e){
        e.preventDefault();
        return false;
    });
JS;
$this->registerJs($js);
?>