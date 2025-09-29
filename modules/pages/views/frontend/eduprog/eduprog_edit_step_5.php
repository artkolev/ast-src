<?php
/*
    @descr Пятый шаг создания/редактирования программы ДПО
    @var $model Class app\modules\pages\models\LKEduprogEdit; текущая страница
    @action pages/eduprog/eduprogedit
*/

use app\modules\eduprog\models\Eduprog;
use kitsunefet\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerJsFile('/js/jquery-ui.min.js', ['depends' => [app\assets\AppAsset::class]]);
$this->registerJsFile('/js/jquery.ui.touch-punch.min.js', ['depends' => [app\assets\AppAsset::class]]);
$this->registerJsFile('/js/lk-events.js', ['depends' => [app\assets\AppAsset::class]]);
?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <div class="lk_block">
                    <header class="lk_block_header">
                        <h1 class="lk_block_title-big"><?= (empty($original) or ($original->status == Eduprog::STATUS_NEW)) ? 'Добавить программу ДПО' : 'Редактирование программы ДПО'; ?></h1>
                        <div class="lk_block_subtitle">
                            <?= $model->content; ?>
                            <br>
                            <b><?= $eduprog_model->name ?></b>
                        </div>
                    </header>
                </div>

                <div class="lk-event-reg-steps">
                    <a href="<?= Url::toRoute([$model->getUrlPath(), 'id' => $original->id]); ?>"
                       class="lk-event-reg-step active">
                        <div class="lk-event-reg-step-num"><img src="/img/i_check1-white.svg" alt=""></div>
                        <div class="lk-event-reg-step-name">Описание</div>
                    </a>
                    <a href="<?= Url::toRoute([$model->getUrlPath(), 'step' => 2, 'id' => $original->id]); ?>"
                       class="lk-event-reg-step active">
                        <div class="lk-event-reg-step-num"><img src="/img/i_check1-white.svg" alt=""></div>
                        <div class="lk-event-reg-step-name">Условия</div>
                    </a>
                    <a href="<?= Url::toRoute([$model->getUrlPath(), 'step' => 3, 'id' => $original->id]); ?>"
                       class="lk-event-reg-step active">
                        <div class="lk-event-reg-step-num"><img src="/img/i_check1-white.svg" alt=""></div>
                        <div class="lk-event-reg-step-name">О программе</div>
                    </a>
                    <a href="<?= Url::toRoute([$model->getUrlPath(), 'step' => 4, 'id' => $original->id]); ?>"
                       class="lk-event-reg-step active">
                        <div class="lk-event-reg-step-num"><img src="/img/i_check1-white.svg" alt=""></div>
                        <div class="lk-event-reg-step-name">Регистрация<br> и тарифы</div>
                    </a>
                    <div class="lk-event-reg-step active">
                        <div class="lk-event-reg-step-num">5</div>
                        <div class="lk-event-reg-step-name">Письмо</div>
                    </div>
                    <a <?= ((empty($original) or ($original->status == Eduprog::STATUS_NEW)) ? '' : 'href="' . Url::toRoute([$model->getUrlPath(), 'step' => 6, 'id' => $original->id]) . '" '); ?>class="lk-event-reg-step">
                        <div class="lk-event-reg-step-num">6</div>
                        <div class="lk-event-reg-step-name">Оферта<br> и публикация</div>
                    </a>
                </div>

                <?php $form = ActiveForm::begin([
                        'id' => 'eduprog-form',
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
                <?= $form->field($eduprog_model, 'id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
                <?= $form->field($eduprog_model, 'step', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>


                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb10">Текст, который увидит пользователь после успешной
                            регистрации</h4>
                        <p class="mt0 mb20">Автоматически будет написано название программы и даты проведения. Вы можете
                            добавить дополнительную информацию по организации программы.</p>
                        <?= $form->field($eduprog_model, 'success_text', ['template' => '{input}{error}{hint}'])->widget(CKEditor::class, [
                                'editorOptions' => ElFinder::ckeditorOptions(
                                        'elfinderuser',
                                        array_merge(['editorplaceholder' => "Текст"], \app\helpers\ckeditor\CKConfig::add_charlimit(\app\helpers\ckeditor\CKConfig::DEFAULT, 2000))
                                ),
                                'containerOptions' => [
                                        'class' => 'editor_container',
                                ],
                        ]); ?>
                    </main>
                </div>
                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb10">Текст, который пользователь получит по email</h4>
                        <p class="mt0 mb20">Внесите в поле ниже список документов, который должен предоставить слушатель
                            для зачисления (обратите, пожалуйста, внимание: вы должны будете проверить предоставленные
                            документы в течение 3 рабочих дней. Если документы соответствуют требованиям, вам нужно
                            изменить статус слушателя на «Обучается».).<br>
                            При необходимости добавьте любую организационную информацию, касающуюся процесса зачисления
                            (например, если потенциальный слушатель должен пройти собеседование или другие вступительные
                            испытания)<br><br>
                            Этот текст будет добавлен в автописьмо, которое клиент получает после оплаты обучения.<br>
                            Автописьмо содержит основную информацию о заказе: название программы, период обучения,
                            данные об оплате. </p>
                        <?= $form->field($eduprog_model, 'success_letter', ['template' => '{input}{error}{hint}'])->widget(CKEditor::class, [
                                'editorOptions' => ElFinder::ckeditorOptions(
                                        'elfinderuser',
                                        array_merge(['editorplaceholder' => "Текст"], \app\helpers\ckeditor\CKConfig::add_charlimit(\app\helpers\ckeditor\CKConfig::DEFAULT, 2000))
                                ),
                                'containerOptions' => [
                                        'class' => 'editor_container',
                                ],
                        ]); ?>
                    </main>
                </div>
                <div class="lk_block">
                    <div class="lk_content">
                        <div class="ip_cell w100 flex justify-between buttons-wrapper mb0">
                            <a href="<?= Url::toRoute([$model->getUrlPath(), 'step' => 4, 'id' => $original->id]); ?>"
                               class="button-o gray medium">Вернуться</a>
                            <button type="submit" class="button blue medium lk">Продолжить</button>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
        </div>
    </main>

    <div class="modal" id="fail_service_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Ошибка создания программы</div>
                <p>При сохранении данных возникли ошибки. <?= \app\helpers\MainHelper::getHelpText() ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>

<?php
$url = Url::toRoute(['/pages/eduprog/saveeduprog/', 'step' => 5]);
$js = <<<JS
    $('#eduprog-form').on('beforeSubmit', function(event){
        var formData = new FormData($('#eduprog-form')[0]);
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
                    $('#fail_service_modal .modal_title').html('Ошибка создания программы');
                    $('#fail_service_modal p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
        return false;
    });
    $('#eduprog-form').on('submit', function(e){
        e.preventDefault();
        return false;
    });

JS;
$this->registerJs($js);
?>