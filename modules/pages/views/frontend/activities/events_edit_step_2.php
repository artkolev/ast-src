<?php

use app\helpers\MainHelper;
use app\modules\events\models\Events;
use app\modules\pages\models\LKEventsChangeDate;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerJsFile('/js/lk-events.js', ['depends' => [app\assets\AppAsset::class]]);

/* страница переноса даты мероприятия */
$changedate_page = LKEventsChangeDate::find()->where(['model' => LKEventsChangeDate::class, 'visible' => 1])->one();
$changedate_url = (!empty($changedate_page) ? Url::toRoute([$changedate_page->getUrlPath(), 'id' => $original->id]) : false);

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
                    <div class="lk-event-reg-step active">
                        <div class="lk-event-reg-step-num">2</div>
                        <div class="lk-event-reg-step-name">Условия</div>
                    </div>
                    <a <?= ($original->status == Events::STATUS_NEW ? '' : 'href="' . Url::toRoute([$model->getUrlPath(), 'step' => 3, 'id' => $original->id]) . '" '); ?>class="lk-event-reg-step">
                        <div class="lk-event-reg-step-num">3</div>
                        <div class="lk-event-reg-step-name">О мероприятии</div>
                    </a>
                    <a <?= ($original->status == Events::STATUS_NEW ? '' : 'href="' . Url::toRoute([$model->getUrlPath(), 'step' => 4, 'id' => $original->id]) . '" '); ?>class="lk-event-reg-step">
                        <div class="lk-event-reg-step-num">4</div>
                        <div class="lk-event-reg-step-name">Регистрация</div>
                    </a>
                    <a <?= ($original->status == Events::STATUS_NEW ? '' : 'href="' . Url::toRoute([$model->getUrlPath(), 'step' => 5, 'id' => $original->id]) . '" '); ?>class="lk-event-reg-step">
                        <div class="lk-event-reg-step-num">5</div>
                        <div class="lk-event-reg-step-name">Публикация</div>
                    </a>
                </div>
                <div class="lk_block">
                    <main class="lk_content">
                        <?php $form = ActiveForm::begin([
                                'id' => 'event-form',
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
                                        'template' => '{label}{input}{error}{hint}',
                                        'inputOptions' => ['class' => 'input_text'],
                                        'labelOptions' => ['class' => 'ip_label', 'encode' => false],
                                ],
                        ]); ?>
                        <?= $form->field($event_model, 'id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
                        <?= $form->field($event_model, 'step', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
                        <h4 class="lk_step_title font20 mb10">Дата*</h4>
                        <?php if (empty($original->ordersAll)) {
                            /* если на мероприятие еще никто не зарегистрировался - дату можно менять */ ?>
                            <p class="mb20">Пожалуйста, указывайте точное время.</p>
                            <div class="ip_cell ip_cell-event-date w100 mb0">
                                <?= $form->field($event_model, 'event_date', ['options' => ['class' => 'ip_cell datarange_ipc mr20']])->textInput(['placeholder' => "Дата начала", 'class' => 'input_text datepicker keypress']); ?>
                                <?= $form->field($event_model, 'event_time_start', ['options' => ['class' => 'ip_cell datarange_ipc mr20']])->input('time', ['placeholder' => "__:__"]); ?>
                                <?= $form->field($event_model, 'event_timezone', ['options' => ['class' => 'ip_cell datarange_ipc w20']])->dropDownList(MainHelper::getTimeZoneList(), ['class' => "pretty_select"]); ?>
                            </div>
                            <div class="ip_cell ip_cell-event-date w100 mb40">
                                <?= $form->field($event_model, 'event_date_end', ['options' => ['class' => 'ip_cell datarange_ipc mr20']])->textInput(['placeholder' => "Дата окончания", 'class' => 'input_text datepicker keypress']); ?>
                                <?= $form->field($event_model, 'event_time_end', ['options' => ['class' => 'ip_cell datarange_ipc']])->input('time', ['placeholder' => "__:__"]); ?>
                            </div>
                        <?php } else {
                            /* иначе просто hidden fields */ ?>
                            <div class="ip_cell ip_cell-event-date w100">
                                <p class="mb20">Дата проведения мероприятия: <br>
                                    <b><?= app\helpers\MainHelper::printDateRange($original); ?> <br>
                                        <?= $original->event_time_start; ?> - <?= $original->event_time_end; ?></b>
                                </p>
                                <?php if ($changedate_url) { ?>
                                    <a href="<?= $changedate_url; ?>" class="button-o small blue">Изменить дату</a>
                                <?php } ?>
                                <?= $form->field($event_model, 'event_date', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
                                <?= $form->field($event_model, 'event_time_start', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
                                <?= $form->field($event_model, 'event_timezone', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
                                <?= $form->field($event_model, 'event_date_end', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
                                <?= $form->field($event_model, 'event_time_end', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
                            </div>
                        <?php } ?>
                        <div class="need-show-ch-wrap">
                            <h4 class="lk_step_title font20">Формат мероприятия*</h4>

                            <?= $form->field($event_model, 'type', ['template' => '{input}{error}{hint}', 'options' => ['class' => 'ip_cell ip_cell-format w100']])->radioList(
                                    $original->getTypeList(),
                                    ['item' => function ($index, $label, $name, $checked, $value) {
                                        switch ($value) {
                                            case Events::TYPE_HYBRID:
                                                $return = '<div class="ip_cell mr50"><div class="flex"><input type="radio" ' . ($checked ? 'checked="checked" ' : '') . 'name="' . $name . '" value="' . $value . '" data-value="1" class="ch ' . $value . '-ch"><label>' . $label . '</label><div class="question_box"><a href="javascript:void(0)" class="question_icon">?</a><div class="question_text">Мероприятие проходит в онлайне и офлайне</div></div></div></div>';
                                                break;
                                            default:
                                                $return = '<div class="ip_cell' . (($index < 2) ? ' mr50' : '') . '"><input type="radio" ' . ($checked ? 'checked="checked" ' : '') . 'name="' . $name . '" value="' . $value . '" data-value="1" class="ch ' . $value . '-ch"><label>' . $label . '</label></div>';
                                        }
                                        return $return;
                                    }
                                    ]
                            ); ?>
                            <h4 class="lk_step_title font20">Место проведения</h4>

                            <?= $form->field($event_model, 'city_id', ['options' => ['class' => 'ip_cell w100 hybrid-need-show offline-need-show', 'style' => 'display: none;']])->dropDownList($event_model->cityList, ['class' => "pretty_select", "style" => "width:100%"]); ?>
                            <?= $form->field($event_model, 'street', ['options' => ['class' => 'ip_cell w100 hybrid-need-show offline-need-show', 'style' => 'display: none;'], 'template' => '<div class="symbols_counter_box">{label}{input}<span class="symbols_counter"></span></div>{error}{hint}'])->textInput(['placeholder' => "Например, Международная ул., 16", 'class' => 'input_text limitedSybmbols', 'maxlength' => 90]); ?>

                            <?= $form->field($event_model, 'place', ['options' => ['class' => 'ip_cell w100 hybrid-need-show offline-need-show', 'style' => 'display: none;'], 'template' => '{label}<div class="flex no-flex-wrap">{input}' . $event_model->getQuestion('place') . '</div>{error}{hint}'])->textInput(['placeholder' => "Например, Крокус Экспо", 'maxlength' => 30]); ?>

                            <?= $form->field($event_model, 'online_place', ['options' => ['class' => 'ip_cell w100 hybrid-need-show online-need-show', 'style' => 'display: none;'], 'template' => '{label}<div class="flex no-flex-wrap">{input}' . $event_model->getQuestion('online_place') . '</div>{error}{hint}'])->textInput(['placeholder' => "Например, Яндекс.Телемост"]); ?>

                            <?= $form->field($event_model, 'translation', ['options' => ['class' => 'ip_cell w100 mb50 hybrid-need-show online-need-show'], 'labelOptions' => ['label' => 'Ссылка для подключения на мероприятие. <span style="color:red;">Заполните это поле не позднее 2 суток до начала мероприятия!</span><br><small>Ссылка будет добавлена в письмо-напоминание, которое будет направлено участникам за 1 сутки до начала мероприятия. Если вы не заполнили поле, направьте ссылку доступа участникам за 1 сутки до мероприятия.</small>']])->textInput(['placeholder' => "url"]); ?>
                        </div>
                        <div class="ip_cell w100 flex justify-between buttons-wrapper mb0">
                            <a href="<?= Url::toRoute([$model->getUrlPath(), 'step' => 1, 'id' => $event_model->id]); ?>"
                               class="button-o gray medium">Вернуться</a>
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
                <div class="modal_title">Ошибка редактирования мероприятия</div>
                <p>При сохранении данных возникли ошибки. <?= \app\helpers\MainHelper::getHelpText(); ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
<?php
$url = Url::toRoute(['/pages/activities/saveevent', 'step' => 2]);
$js = <<<JS
    $('#lkevent-event_date').on('blur',function(){
        $('#event-form').yiiActiveForm('validateAttribute', 'lkevent-event_date_end');
    });
    $('#lkevent-event_date_end').on('blur',function(){
        $('#event-form').yiiActiveForm('validateAttribute', 'lkevent-event_date');
    });
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
JS;
$this->registerJs($js);
?>