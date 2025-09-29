<?php

use app\helpers\MainHelper;
use app\modules\events\models\Events;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* если есть заявка на модерацию в статусе Новая */
$to_moderation = !empty($original->currentModeration);

$this->registerJsFile('/js/lk-events.js', ['depends' => [app\assets\AppAsset::class]]);
?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <div class="lk_block">
                    <header class="lk_block_header">
                        <h1 class="lk_block_title-big"><?= $original->status == Events::STATUS_NEW ? 'Добавить мероприятие' : 'Редактирование мероприятия'; ?></h1>
                        <div class="lk_block_subtitle"><?= $model->content; ?></div>
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
                        <div class="lk-event-reg-step-name">О мероприятии</div>
                    </a>
                    <a href="<?= Url::toRoute([$model->getUrlPath(), 'step' => 4, 'id' => $original->id]); ?>"
                       class="lk-event-reg-step active">
                        <div class="lk-event-reg-step-num"><img src="/img/i_check1-white.svg" alt=""></div>
                        <div class="lk-event-reg-step-name">Регистрация</div>
                    </a>
                    <div class="lk-event-reg-step active">
                        <div class="lk-event-reg-step-num">5</div>
                        <div class="lk-event-reg-step-name">Публикация</div>
                    </div>
                </div>

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
                <div class="lk_block lk_block-contacts-step5">
                    <div class="lk_content">
                        <h4 class="lk_step_title font20">Контакты для связи</h4>
                        <p>Рекомендуем указать контакт для оперативной связи. Данные будут указаны в билете.</p>
                        <div class="ip_cell w100">
                            <?= $form->field($event_model, 'contact_email')->input('email', ['placeholder' => "E-mail"]); ?>
                            <?= $form->field($event_model, 'contact_phone')->input('tel', ['placeholder' => "+7 (000) 000-00-00"]); ?>
                            <?= $form->field($event_model, 'contact_wa')->input('tel', ['placeholder' => "+7 (000) 000-00-00"]); ?>
                            <?= $form->field($event_model, 'contact_telegram')->textInput(['placeholder' => "https://t.me/account_name"]); ?>
                        </div>
                    </div>
                </div>
                <div class="lk_block">
                    <div class="lk_content">
                        <h4 class="lk_step_title font20">Кому отображать</h4>
                        <?= $form->field($event_model, 'vis_for', ['template' => '{input}{error}{hint}'])->radioList(
                                $event_model->getVisList(),
                                [
                                        'item' => function ($index, $label, $name, $checked, $value) {
                                            return '<div class="ip_cell w100"><input type="radio" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked' : '') . ' class="ch" data-kind="kind_' . $value . '" tabindex="3"><label class="notmark">' . $label . '</label></div>';
                                        }
                                ]
                        )->label(false); ?>
                    </div>
                </div>
                <div class="lk_block">
                    <div class="lk_content">
                        <h4 class="lk_step_title font20">Время публикации</h4>
                        <?= $form->field($event_model, 'start_publish_late', ['template' => '{input}{error}{hint}', 'options' => ['class' => '']])->radioList(
                                [0 => 'Опубликовать немедленно', 1 => 'Запланировать публикацию'],
                                ['item' => function ($index, $label, $name, $checked, $value) {
                                    return '<div class="ip_cell w100"><input type="radio" ' . ($checked ? 'checked="checked" ' : '') . 'name="' . $name . '" value="' . $value . '" class="ch ' . ($value == 1 ? 'need-delay-ch' : 'need-send-ch') . '"><label>' . $label . '</label></div>';
                                }
                                ]
                        ); ?>

                        <div class="ip_cell ip_cell-event-date w100 mb0 need-delay-ch-wrap" style="display: none;">
                            <?= $form->field($event_model, 'start_publish_date', ['options' => ['class' => 'ip_cell datarange_ipc mr20']])->textInput(['placeholder' => "Дата начала", 'class' => 'input_text datepicker']); ?>
                            <?= $form->field($event_model, 'start_publish_time', ['options' => ['class' => 'ip_cell datarange_ipc mr20']])->input('time', ['placeholder' => "__:__"]); ?>
                            <?= $form->field($event_model, 'start_publish_timezone', ['options' => ['class' => 'ip_cell datarange_ipc w20']])->dropDownList(MainHelper::getTimeZoneList(), ['class' => "pretty_select"]); ?>
                        </div>
                    </div>
                </div>

                <div class="lk_block need-send-ch-wrap">
                    <div class="lk_content">
                        <div class="ip_cell w100 flex justify-between buttons-wrapper mb0">
                            <a href="<?= Url::toRoute([$model->getUrlPath(), 'step' => 4, 'id' => $event_model->id]); ?>"
                               class="button-o gray medium">Вернуться</a>
                            <button type="submit"
                                    class="button blue medium lk"><?= $to_moderation ? 'Отправить на модерацию' : 'Отправить'; ?></button>
                        </div>
                    </div>
                </div>

                <div class="lk_block need-delay-ch-wrap" style="display: none;">
                    <div class="lk_content">
                        <div class="ip_cell w100 flex justify-between buttons-wrapper mb0">
                            <a href="<?= Url::toRoute([$model->getUrlPath(), 'step' => 4, 'id' => $event_model->id]); ?>"
                               class="button-o gray medium">Вернуться</a>
                            <button type="submit"
                                    class="button blue medium lk"><?= $to_moderation ? 'Отправить на модерацию' : 'Запланировать'; ?></button>
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
                <div class="modal_title">Ошибка редактирования мероприятия</div>
                <p>При сохранении данных возникли ошибки. <?= \app\helpers\MainHelper::getHelpText() ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
    <div class="modal-side_banner" id="moderate_event_modal_fancy">
        <div class="modal-review-content">
            <div class="modal-side_banner-info">
                <div class="success_box">
                    <div class="modal_title">Модерация</div>
                    <p>Мероприятие отправлено на модерацию</p>
                    <div class="ip_cell buttons w100 mt20 mb0">
                        <a href="#" class="button blue small">Ок</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
$url = Url::toRoute(['/pages/activities/saveevent', 'step' => 5]);
$url_tariff = Url::toRoute(['/pages/activities/savetariff']);
$url_tariffinfo = Url::toRoute(['/pages/activities/gettariff']);
$js = <<<JS
    $('#event-form').on('beforeSubmit', function(event) {
        let formData = new FormData($('#event-form')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    // переадресация на следующий шаг
                    if (data.show_message == 'show') {
                        $('#moderate_event_modal_fancy .ip_cell.buttons a').attr('href',data.redirect_to);
                        $('#moderate_event_modal_fancy .success_box p').html(data.message);
                        $.fancybox.open({
                            src: '#moderate_event_modal_fancy',
                            type: 'inline',
                            modal: true,
                            beforeClose: function() {
                                return false;
                            }
                        });
                    } else {
                        window.location.href = data.redirect_to;
                    }
                } else {
                    // показать модалку с ошибкой
                    $('#fail_service_modal .modal_title').html('Ошибка редактирования мероприятия');
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

    let inputs_array = [];
    let btn_send = $('#event-form .need-send-ch-wrap .button').text();
    let btn_delay = $('#event-form .need-delay-ch-wrap .button').text();
    $('.lk_block-contacts-step5 .input_text').each(function(i, e) {
        inputs_array.push($(this).val());
    });
    $('.lk_block-contacts-step5 .input_text').on('keyup change', function(e){
        let cnt = 0;
        $('.lk_block-contacts-step5 .input_text').each(function(i, e) {
            if((inputs_array[i] != $(this).val()) && ($(this).val() != '')) {
                cnt++;
            }
        });
        $('.lk_block-contacts-step5 .input_text').promise().done(function() {
            if(cnt > 0) {
                $('#event-form .need-send-ch-wrap .button, #event-form .need-delay-ch-wrap .button').text('Отправить на модерацию');
            } else {
                $('#event-form .need-send-ch-wrap .button').text(btn_send);
                $('#event-form .need-delay-ch-wrap .button').text(btn_delay);
            }
        });
    });

JS;
$this->registerJs($js);
?>