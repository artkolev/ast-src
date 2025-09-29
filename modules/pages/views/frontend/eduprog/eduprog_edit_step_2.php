<?php
/*
    @descr Первый шар создания/редактирования программы ДПО
    @var $model Class app\modules\pages\models\LKEduprogEdit; текущая страница
    @action pages/eduprog/eduprogedit
*/

use app\modules\eduprog\models\Eduprog;
use app\modules\pages\models\LKEduprogChangeDate;
use kitsunefet\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerJsFile('/js/lk-events.js', ['depends' => [app\assets\AppAsset::class]]);

/* страница переноса даты программы */
$changedate_page = LKEduprogChangeDate::find()->where(['model' => LKEduprogChangeDate::class, 'visible' => 1])->one();
$changedate_url = (!empty($changedate_page) ? Url::toRoute([$changedate_page->getUrlPath(), 'id' => $original->id]) : false);

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
                    <div class="lk-event-reg-step active">
                        <div class="lk-event-reg-step-num">2</div>
                        <div class="lk-event-reg-step-name">Условия</div>
                    </div>
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
                        <h4 class="lk_step_title font20 mb15">Даты начала и окончания обучения</h4>

                        <?php if (empty($original->ordersAll)) { ?>
                            <div class="ip_cell ip_cell-event-date w100 mb0">
                                <?= $form->field($eduprog_model, 'date_start', ['options' => ['class' => 'ip_cell datarange_ipc mr20']])->textInput(['autocomplete' => 'off', 'placeholder' => "Дата начала", 'class' => 'input_text datepicker keypress']); ?>
                                <?= $form->field($eduprog_model, 'date_stop', ['options' => ['class' => 'ip_cell datarange_ipc mr20']])->textInput(['autocomplete' => 'off', 'placeholder' => "Дата окончания", 'class' => 'input_text datepicker keypress']); ?>
                            </div>
                        <?php } else {
                            /* иначе просто hidden fields */ ?>
                            <div class="ip_cell ip_cell-event-date w100">
                                <p class="mb20">
                                    <b>с <?= Yii::$app->formatter->asDatetime($original->date_start, 'd MMMM y'); ?>
                                        по <?= Yii::$app->formatter->asDatetime($original->date_stop, 'd MMMM y'); ?> </b>
                                </p>
                                <?php if ($changedate_url) { ?>
                                    <a href="<?= $changedate_url; ?>" class="button-o small blue">Изменить дату</a>
                                <?php } ?>
                                <?= $form->field($eduprog_model, 'date_start', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
                                <?= $form->field($eduprog_model, 'date_stop', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
                            </div>
                        <?php } ?>

                        <h4 class="lk_step_title font20 mb15">График обучения</h4>
                        <?= $form->field($eduprog_model, 'shedule_text', ['template' => '<div class="symbols_counter_box">{input}<span class="symbols_counter"></span></div>{error}{hint}'])->textInput(['autocomplete' => 'off', 'placeholder' => "Например, 4 модуля по 2 выходных дня", 'class' => 'input_text limitedSybmbols', 'maxlength' => 30]); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb15">Дата закрытия программы и выдачи документов</h4>
                        <p class="mb20">Документы будут направлены слушателям программы в указанную дату в период с
                            00:00 до 06:00 по часовому поясу Москвы.</p>
                        <div class="ip_cell ip_cell-event-date w100 mb0">
                            <?= $form->field($eduprog_model, 'date_close', ['options' => ['class' => 'ip_cell datarange_ipc mr20']])->textInput(['autocomplete' => 'off', 'placeholder' => "Дата закрытия", 'class' => 'input_text datepicker keypress']); ?>
                        </div>
                        <h4 class="lk_step_title font20 mb15">Дата завершения набора слушателей</h4>
                        <p class="mb20">После этой даты оформить заказ на программу будет невозможно</p>
                        <div class="ip_cell ip_cell-event-date w100 mb0">
                            <?= $form->field($eduprog_model, 'date_stop_sale', ['options' => ['class' => 'ip_cell datarange_ipc mr20']])->textInput(['autocomplete' => 'off', 'placeholder' => "Дата окончания набора", 'class' => 'input_text datepicker keypress']); ?>
                        </div>
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb15">Формат</h4>
                        <div class="need-show-ch-wrap">
                            <?= $form->field($eduprog_model, 'format', ['options' => ['class' => 'ip_cell ip_cell-format w100 mb10'], 'template' => '{input}{error}{hint}'])->radioList(
                                    $original->getFormatList(),
                                    ['item' => function ($index, $label, $name, $checked, $value) {
                                        $return = '<div class="ip_cell mr50"><input type="radio" ' . ($checked ? 'checked="checked" ' : '') . 'name="' . $name . '" value="' . $value . '" data-value="1" class="ch ' . $value . '-ch"><label class="notmark">' . $label . '</label></div>';
                                        return $return;
                                    }
                                    ]
                            ); ?>
                            <?= $form->field($eduprog_model, 'city_id', ['options' => ['class' => 'ip_cell w100 hybrid-need-show', 'style' => 'display: none;']])->dropDownList($original->getCityList(), ['prompt' => "Не задан", 'class' => 'pretty_select', 'style' => 'width:100%']); ?>
                            <?= $form->field($eduprog_model, 'place', ['options' => ['class' => 'ip_cell w100 hybrid-need-show', 'style' => 'display: none;']])->textInput(['placeholder' => "Например, Крокус Экспо"]); ?>
                            <?= $form->field($eduprog_model, 'address', ['template' => '{label}<div class="symbols_counter_box">{input}<span class="symbols_counter"></span></div>{error}{hint}', 'options' => ['class' => 'ip_cell w100 hybrid-need-show', 'style' => 'display: none;']])->textInput(['placeholder' => "Например, Международная ул., 16", 'maxlength' => 90, 'class' => 'input_text limitedSybmbols']); ?>
                        </div>
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb15">Правила проведения и условия участия</h4>
                        <p>Укажите:
                        <ol>
                            <li>Список документов, которые должен предоставить слушатель до начала обучения.</li>
                            <li>Требования к уровню профессиональной подготовки слушателей.</li>
                            <li>Необходимое количество часов и заданий, которые слушатель обязан пройти для завершения
                                программы.
                            </li>
                        </ol>
                        </p>
                        <?= $form->field($eduprog_model, 'rules', ['template' => '{input}{error}{hint}'])->widget(CKEditor::class, [
                                'editorOptions' => ElFinder::ckeditorOptions(
                                        'elfinderuser',
                                        array_merge(['editorplaceholder' => ""], \app\helpers\ckeditor\CKConfig::add_charlimit(\app\helpers\ckeditor\CKConfig::DEFAULT, 2000))
                                ),
                                'containerOptions' => [
                                        'class' => 'editor_container',
                                ],
                        ]); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb15">Контакты организатора для слушателей</h4>
                        <?= $form->field($eduprog_model, 'contact_email')->textInput(['placeholder' => "email@mail.ru"]); ?>
                        <?= $form->field($eduprog_model, 'contact_phone')->input('tel', ['placeholder' => "+7 (000) 000-00-00"]); ?>
                        <?= $form->field($eduprog_model, 'contact_wa')->input('tel', ['placeholder' => "+7 (000) 000-00-00"]); ?>
                        <?= $form->field($eduprog_model, 'contact_telegram')->textInput(['placeholder' => "https://t.me/account_name"]); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <div class="lk_content">
                        <div class="ip_cell w100 flex justify-between buttons-wrapper mb0">
                            <a href="<?= Url::toRoute([$model->getUrlPath(), 'id' => $original->id]); ?>"
                               class="button-o gray medium">Вернуться</a>
                            <button class="button blue medium lk">Продолжить</button>
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
$url = Url::toRoute(['/pages/eduprog/saveeduprog/', 'step' => 2]);
$js = <<<JS
    $('#lkeduprog-date_stop').on('blur',function(){
        $('#eduprog-form').yiiActiveForm('validateAttribute', 'lkeduprog-date_start');
        $('#eduprog-form').yiiActiveForm('validateAttribute', 'lkeduprog-date_close');
    });
    $('#lkeduprog-date_start').on('blur',function(){
        $('#eduprog-form').yiiActiveForm('validateAttribute', 'lkeduprog-date_stop');
    });
    $('#lkeduprog-date_close').on('blur',function(){
        $('#eduprog-form').yiiActiveForm('validateAttribute', 'lkeduprog-date_stop');
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