<?php

use app\helpers\MainHelper;
use app\modules\pages\models\LKEventsView;
use kitsunefet\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerJsFile('/js/lk-events.js', ['depends' => [app\assets\AppAsset::class]]);

/* страница просмотра данных о мероприятие */
$view_page = LKEventsView::find()->where(['model' => LKEventsView::class, 'visible' => 1])->one();
$view_url = (!empty($view_page) ? $view_page->getUrlPath() : false);

?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <?php if ($view_url) { ?>
                    <div class="ip_cell w100">
                        <a href="<?= Url::toRoute([$view_url, 'id' => $original->id]); ?>" class="button-o back">Мероприятие</a>
                    </div>
                <?php } ?>
                <div class="lk_block">
                    <header class="lk_block_header">
                        <h1 class="lk_block_title-big"><?= $model->getNameForView(); ?></h1>
                        <div class="lk_block_subtitle"><?= $model->content; ?></div>
                    </header>
                </div>
                <div class="lk_block">
                    <main class="lk_content">
                        <?php $form = ActiveForm::begin([
                                'id' => 'event-changedate',
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
                        <?= $form->field($changedate_model, 'events_id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>

                        <h4 class="lk_step_title font20 mt20">Укажите новую дату проведения мероприятия</h4>

                        <p class="mb20">Пожалуйста, указывайте точное время.</p>
                        <div class="ip_cell ip_cell-event-date w100 mb0">
                            <?= $form->field($changedate_model, 'new_event_date', ['options' => ['class' => 'ip_cell datarange_ipc mr20']])->textInput(['placeholder' => "Дата начала", 'class' => 'input_text datepicker keypress']); ?>
                            <?= $form->field($changedate_model, 'new_event_time_start', ['options' => ['class' => 'ip_cell datarange_ipc mr20']])->input('time', ['placeholder' => "__:__"]); ?>
                            <?= $form->field($changedate_model, 'new_event_timezone', ['options' => ['class' => 'ip_cell datarange_ipc w20']])->dropDownList(MainHelper::getTimeZoneList(), ['class' => "pretty_select"]); ?>
                        </div>
                        <div class="ip_cell ip_cell-event-date w100 mb40">
                            <?= $form->field($changedate_model, 'new_event_date_end', ['options' => ['class' => 'ip_cell datarange_ipc mr20']])->textInput(['placeholder' => "Дата окончания", 'class' => 'input_text datepicker keypress']); ?>
                            <?= $form->field($changedate_model, 'new_event_time_end', ['options' => ['class' => 'ip_cell datarange_ipc']])->input('time', ['placeholder' => "__:__"]); ?>
                        </div>

                        <h4 class="lk_step_title">Ознакомьтесь с текстом письма-уведомления о переносе мероприятия</h4>

                        <?= $form->field($changedate_model, 'letter_text', ['template' => '{input}{error}{hint}'])->widget(CKEditor::class, [
                                'editorOptions' => ElFinder::ckeditorOptions(
                                        'elfinderuser',
                                        ['editorplaceholder' => 'Этот текст будет отправлен всем, купившим билеты на данное мероприятие']
                                ),
                                'containerOptions' => [
                                        'class' => 'editor_container',
                                ],
                        ]); ?>

                        <div class="ip_cell w100 flex justify-between buttons-wrapper mb0">
                            <button type="submit" class="button blue medium lk">Изменить дату и отправить рассылку
                            </button>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </main>
                </div>
            </div>
            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
        </div>
    </main>

    <div class="modal" id="cancel_event_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Перенос мероприятия</div>
                <p>Мероприятие перенесено. Рассылка уведомлений участникам, купившим билеты, будет отправлена в
                    ближайшее время.</p>
                <div class="modal_buttons">
                    <a href="#" class="button small">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>

    <div class="modal" id="fail_event_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Ошибка при переносе мероприятия</div>
                <p>При сохранении данных возникли ошибки. <?= \app\helpers\MainHelper::getHelpText() ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>

<?php
$url = Url::toRoute(['/pages/activities/sendchangedateform']);
$js = <<<JS
    $('#event-changedate').on('beforeSubmit', function(event){
        var formData = new FormData($('#event-changedate')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    $('#cancel_event_modal p').html(data.message);
                    $('#cancel_event_modal .modal_buttons a').attr('href',data.redirect_to);
                    modalPos('#cancel_event_modal');
                } else {
                    // показать модалку с ошибкой
                    $('#fail_event_modal .modal_title').html('Ошибка при переносе мероприятия');
                    $('#fail_event_modal p').html(data.message);
                    modalPos('#fail_event_modal');
                }
            }
        });
        return false;
    });
    $('#event-changedate').on('submit', function(e){
        e.preventDefault();
        return false;
    });
JS;
$this->registerJs($js);
?>