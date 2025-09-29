<?php
/*
    @descr Шестой шаг создания/редактирования программы ДПО
    @var $model Class app\modules\pages\models\LKEduprogEdit; текущая страница
    @action pages/eduprog/eduprogedit
*/

use app\helpers\MainHelper;
use app\modules\eduprog\models\Eduprog;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerJsFile('/js/jquery-ui.min.js', ['depends' => [app\assets\AppAsset::class]]);
$this->registerJsFile('/js/jquery.ui.touch-punch.min.js', ['depends' => [app\assets\AppAsset::class]]);
$this->registerJsFile('/js/lk-events.js', ['depends' => [app\assets\AppAsset::class]]);

$oferta = false;
$oferta_link = false;
if ($original && $original->currentModeration && $original->currentModeration->oferta) {
    $oferta = $original->currentModeration->oferta;
    $oferta_link = $original->currentModeration->getFile('oferta');
} elseif ($original && $original->oferta) {
    $oferta = $original->oferta;
    $oferta_link = $original->getFile('oferta');
}

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
                    <a href="<?= Url::toRoute([$model->getUrlPath(), 'step' => 5, 'id' => $original->id]); ?>"
                       class="lk-event-reg-step active">
                        <div class="lk-event-reg-step-num"><img src="/img/i_check1-white.svg" alt=""></div>
                        <div class="lk-event-reg-step-name">Письмо</div>
                    </a>
                    <div class="lk-event-reg-step active">
                        <div class="lk-event-reg-step-num">6</div>
                        <div class="lk-event-reg-step-name">Оферта<br> и публикация</div>
                    </div>
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
                        <h4 class="lk_step_title font20 mb10">Договор оферты с обучающимся</h4>
                        <?php if ($original && $original->currentModeration && ($original->currentModeration->first_moderation == 1)) { ?>
                            <p class="mt0 mb20">Прикрепите договор оферты на оказание образовательных услуг по
                                публикуемой программе.
                                ВНИМАНИЕ! Проверьте, пожалуйста, внимательно загружаемый документ. После публикации
                                программы вы не сможете изменить его самостоятельно.
                                <br> В случае возникновения вопросов обратитесь, пожалуйста, к сотруднику Академии по
                                почте <a href="mailto:dpo@ast-academy.ru">dpo@ast-academy.ru</a>
                            </p>
                            <div class="upload_pdf_list upload-document" data-maxfiles="1">
                                <?php if (!empty($oferta)) { // файл был загружен ранее?>
                                    <?= $form->field($eduprog_model, 'oferta', ['template' => '<a href="#" class="upload_pdf_link">Загрузить документ Word <br>Формат .doc, .docx, до 5 Мб</a><span class="upload_pdf_result"><b>' . $oferta->name . '</b><span>' . MainHelper::prettyFileSize($oferta->size) . '</span></span><a href="#" class="file_pdf_remove">x</a>{input}{error}{hint}', 'options' => ['class' => 'upload_pdf_box added']])->fileInput(['data-file_id' => $oferta->id, 'class' => 'upload_pdf-input', 'accept' => '.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document']); ?>
                                <?php } else { ?>
                                    <?= $form->field($eduprog_model, 'oferta', ['template' => '<a href="#" class="upload_pdf_link">Загрузить документ Word <br>Формат .doc, .docx, до 5 Мб</a><span class="upload_pdf_result"></span><a href="#" class="file_pdf_remove">x</a>{input}{error}{hint}', 'options' => ['class' => 'upload_pdf_box']])->fileInput(['data-file_id' => '', 'class' => 'upload_pdf-input', 'accept' => '.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document']); ?>
                                <?php } ?>
                                <div class="upload_pdf_js"></div>
                            </div>
                        <?php } else { ?>
                            <p class="mt0 mb20">Договор оферты на оказание образовательных услуг по публикуемой
                                программе. Эти условия будет принимать пользователь, приобретая обучение на сайте.</p>
                            <?php if (!empty($oferta)) { ?>
                                <p><a href="<?= $oferta_link; ?>">Скачать договор</a></p>
                            <?php } ?>
                        <?php } ?>
                    </main>
                </div>

                <div class="lk_block">
                    <div class="lk_content">
                        <h4 class="lk_step_title font20">Время публикации</h4>
                        <?= $form->field($eduprog_model, 'start_publish_late', ['template' => '{input}{error}{hint}', 'options' => ['class' => '']])->radioList(
                                [0 => 'Опубликовать немедленно', 1 => 'Запланировать публикацию'],
                                ['item' => function ($index, $label, $name, $checked, $value) {
                                    return '<div class="ip_cell w100"><input type="radio" ' . ($checked ? 'checked="checked" ' : '') . 'name="' . $name . '" value="' . $value . '" class="ch ' . ($value == 1 ? 'need-delay-ch' : 'need-send-ch') . '"><label>' . $label . '</label></div>';
                                }
                                ]
                        ); ?>
                        <div class="ip_cell ip_cell-event-date w100 mb0 need-delay-ch-wrap" style="display: none;">
                            <?= $form->field($eduprog_model, 'start_publish_date', ['options' => ['class' => 'ip_cell datarange_ipc mr20']])->textInput(['placeholder' => "Дата начала", 'class' => 'input_text datepicker']); ?>
                            <?php // =$form->field($eduprog_model, 'start_publish_time',['options'=>['class'=>'ip_cell datarange_ipc mr20']])->input('time',['placeholder' => "__:__"]);?>
                        </div>
                    </div>
                </div>


                <div class="lk_block">
                    <div class="lk_content">
                        <div class="ip_cell w100 flex justify-between buttons-wrapper mb0">
                            <a href="<?= Url::toRoute([$model->getUrlPath(), 'step' => 5, 'id' => $original->id]); ?>"
                               class="button-o gray medium">Вернуться</a>
                            <a href="<?= Url::toRoute(['preview/eduprog/', 'id' => $original->id]); ?>" target="_blank"
                               class="button-o gray medium">Предпросмотр</a>
                            <input id="moder_type" type="hidden" name="moderate_mode" value="0">
                            <?php if ($original && $original->currentModeration) { ?>
                                <button type="submit" data-moder="0" class="js-typemoder button-o gray medium"
                                        style="margin-right: auto;">Сохранить в черновик
                                </button>
                            <?php } ?>
                            <button type="submit" data-moder="1"
                                    class="js-typemoder button blue medium lk need-send-ch-wrap"><?= (!empty($original->currentModeration) ? 'Отправить на модерацию' : 'Отправить') ?></button>
                            <button type="submit" data-moder="1"
                                    class="js-typemoder button blue medium lk need-delay-ch-wrap"><?= (!empty($original->currentModeration) ? 'Отправить на модерацию' : 'Запланировать') ?></button>
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
    <div class="modal-side_banner" id="moderate_eduprog_modal_fancy">
        <div class="modal-review-content">
            <div class="modal-side_banner-info">
                <div class="success_box">
                    <div class="modal_title">Модерация</div>
                    <p>Программа отправлена на модерацию</p>
                    <div class="ip_cell buttons w100 mt20 mb0">
                        <a href="#" class="button blue small">Ок</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
$url = Url::toRoute(['/pages/eduprog/saveeduprog/', 'step' => 6]);
$url_delete_oferta = Url::toRoute(['/pages/eduprog/remove-oferta/']);
$js = <<<JS
    $('.js-typemoder').click(function(){
        $('#moder_type').val($(this).data('moder'));
    });

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
                    // если было отправлено на модерацию - показать модалку. 
                    if (data.show_message == 'show') {
                        $('#moderate_eduprog_modal_fancy .ip_cell.buttons a').attr('href',data.redirect_to);
                        $('#moderate_eduprog_modal_fancy .success_box p').html(data.message);
                        $.fancybox.open({
                            src: '#moderate_eduprog_modal_fancy',
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


    // удаление загруженного файла
    $('.file_pdf_remove').click(function(){
        let file_id = $('#lkeduprog-oferta').data('file_id');
        if (parseInt(file_id) > 0) {
            // запрос на удаление файла
            let param = yii.getCsrfParam();
            let token = yii.getCsrfToken();
            let data = {};
            data[param] = token;
            data['file_id'] = file_id;
            $.ajax({
                type: 'POST',
                url: '{$url_delete_oferta}',
                data: data,
                success: function (data) {
                    if (data.status == 'success') {
                        // ничего не делаем
                    } else {
                        // показать модалку с ошибкой
                        $('#fail_service_modal .modal_title').html('Ошибка удаления договора');
                        $('#fail_service_modal p').html(data.message);
                        modalPos('#fail_service_modal');
                    }
                }
            });
        }
    });

JS;
$this->registerJs($js);
?>