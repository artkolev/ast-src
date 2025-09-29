<?php
/*
    @descr Первый шар создания/редактирования программы ДПО
    @var $model Class app\modules\pages\models\LKEduprogEdit; текущая страница
    @action pages/eduprog/eduprogedit
*/

use app\modules\eduprog\models\Eduprog;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerJsFile('/js/lk-events.js', ['depends' => [app\assets\AppAsset::class]]);
?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <div class="lk_block">
                    <header class="lk_block_header">
                        <h1 class="lk_block_title-big"><?= (empty($original) or ($original->status == Eduprog::STATUS_NEW)) ? 'Добавить программу ДПО' : 'Редактирование программы ДПО'; ?></h1>
                        <div class="lk_block_subtitle"><?= $model->content; ?></div>
                    </header>
                </div>

                <div class="lk-event-reg-steps">
                    <div class="lk-event-reg-step active">
                        <div class="lk-event-reg-step-num">1</div>
                        <div class="lk-event-reg-step-name">Описание</div>
                    </div>
                    <a <?= ((empty($original) or ($original->status == Eduprog::STATUS_NEW)) ? '' : 'href="' . Url::toRoute([$model->getUrlPath(), 'step' => 2, 'id' => $original->id]) . '" '); ?>class="lk-event-reg-step">
                        <div class="lk-event-reg-step-num">2</div>
                        <div class="lk-event-reg-step-name">Условия</div>
                    </a>
                    <a <?= ((empty($original) or ($original->status == Eduprog::STATUS_NEW)) ? '' : 'href="' . Url::toRoute([$model->getUrlPath(), 'step' => 3, 'id' => $original->id]) . '" '); ?>class="lk-event-reg-step">
                        <div class="lk-event-reg-step-num">3</div>
                        <div class="lk-event-reg-step-name">О программе</div>
                    </a>
                    <a <?= ((empty($original) or ($original->status == Eduprog::STATUS_NEW)) ? '' : 'href="' . Url::toRoute([$model->getUrlPath(), 'step' => 4, 'id' => $original->id]) . '" '); ?>class="lk-event-reg-step">
                        <div class="lk-event-reg-step-num">4</div>
                        <div class="lk-event-reg-step-name">Регистрация<br> и тарифы</div>
                    </a>
                    <a <?= ((empty($original) or ($original->status == Eduprog::STATUS_NEW)) ? '' : 'href="' . Url::toRoute([$model->getUrlPath(), 'step' => 5, 'id' => $original->id]) . '" '); ?>class="lk-event-reg-step">
                        <div class="lk-event-reg-step-num">5</div>
                        <div class="lk-event-reg-step-name">Письмо</div>
                    </a>
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
                        <h4 class="lk_step_title font20 mb15">Название программы</h4>
                        <?= $form->field($eduprog_model, 'name', ['template' => '<div class="symbols_counter_box">{input}<span class="symbols_counter"></span></div>{error}{hint}'])->textInput(['autocomplete' => 'off', 'placeholder' => "Новая программа", 'class' => 'input_text limitedSybmbols', 'maxlength' => 90]); ?>
                        <h4 class="lk_step_title font20 mb15">Возрастной ценз</h4>

                        <?= $form->field($eduprog_model, 'age_id', ['options' => ['class' => 'ip_cell w100']])->dropDownList(Eduprog::getAgeList(), ['prompt' => "Не задан", 'class' => 'pretty_select', 'style' => 'width:100%']); ?>

                        <div class="need-show-ch-wrap">
                            <h4 class="lk_step_title font20 mb15">Вид программы</h4>
                            <?php $qualification_list = $eduprog_model->getCategoryQualificationList(); ?>
                            <?= $form->field($eduprog_model, 'category_id', ['options' => ['class' => 'ip_cell ip_cell-format w100 mb10'], 'template' => '{input}{error}{hint}'])->radioList(
                                    Eduprog::getCategoryList(),
                                    ['item' => function ($index, $label, $name, $checked, $value) use ($qualification_list) {
                                        $return = '<div class="ip_cell w100 mb10"><input type="radio" name="' . $name . '" value="' . $value . '" data-value="' . $qualification_list[$value] . '" ' . ($checked ? 'checked="checked"' : '') . ' class="ch need-show-ch"><label class="notmark">' . $label . '</label></div>';
                                        return $return;
                                    }
                                    ]
                            ); ?>
                            <div class="need-show" style="display: none;">
                                <h4 class="lk_step_title font20 mb15">Получаемая квалификация</h4>
                                <p>Укажите название квалификации, которую получит слушатель после прохождения
                                    программы</p>
                                <?= $form->field($eduprog_model, 'qualification', ['options' => ['class' => 'ip_cell w100 mb20'], 'template' => '<div class="symbols_counter_box">{input}<span class="symbols_counter"></span></div>{error}{hint}'])->textInput(['autocomplete' => 'off', 'placeholder' => "", 'class' => 'input_text limitedSybmbols', 'maxlength' => 200]); ?>
                            </div>
                        </div>

                        <h4 class="lk_step_title font20 mb15">Количество часов</h4>
                        <?= $form->field($eduprog_model, 'hours', ['options' => ['class' => 'ip_cell ip_cell-rub flex align-center w100'], 'template' => '<div class="ip_cell mr10 mb0">{input}</div><span class="rub">часов</span>{error}{hint}'])->textInput(['type' => 'number', 'autocomplete' => 'off', 'placeholder' => "0", 'class' => 'input_text limitedSybmbols']); ?>

                        <h4 class="lk_step_title font20 mb15">Максимальное количество слушателей</h4>
                        <?= $form->field($eduprog_model, 'member_total', ['options' => ['class' => 'ip_cell w100 mb20']])->textInput(['type' => 'number', 'autocomplete' => 'off', 'placeholder' => "0", 'class' => 'input_text limitedSybmbols']); ?>
                    </main>
                </div>
                <div class="lk_block">
                    <div class="lk_content">
                        <div class="ip_cell w100 flex flex-end mb0">
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
$url = Url::toRoute(['/pages/eduprog/saveeduprog/', 'step' => 1]);
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