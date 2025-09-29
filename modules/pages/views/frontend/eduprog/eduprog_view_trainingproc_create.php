<?php
/*
    @descr Страница Создания, редактирования, просмотра, копирования порядок обучения для программы ДПО в ЛК
    @var $model Class app\modules\pages\models\LKEduprogViewTrainingprocCreate; текущая страница
    @var $trainingproc_form Class app\models\LKEduprogTrainingproc; модель формы
    @var $eduprog_model Class app\modules\eduprog\models\Eduprog; модель программы ДПО, для которой создаётся/редактируется порядок обучения
    @var $original Class app\modules\eduprog\models\EduprogTrainingproc; текущий порядок обучения
    @var $training_message_page Class app\modules\pages\models\LKEduprogViewTrainingproc; страница Порядок обучения

    @action pages/eduprog/eduprog-view-trainingproc-create
*/

use kitsunefet\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <div class="ip_cell w100">
                    <?php if ($training_message_page) {
                        echo Html::a('Вернуться', Url::toRoute([$training_message_page->getUrlPath(), 'id' => $eduprog_model->id]), ['class' => 'button-o back']); ?>
                    <?php } ?>
                </div>
                <div class="lk-block-header-no-bg">
                    <h1 class="lk_block_title-big"><?= ($trainingproc_form->id ? 'Редактировать порядок обучения' : 'Создать порядок обучения'); ?></h1>
                    <div class="lk_block_subtitle"><?= $model->content; ?></div>
                </div>
                <?php $form = ActiveForm::begin([
                        'id' => 'eduprog-trainingproc-form',
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
                <?= $form->field($trainingproc_form, 'id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
                <?= $form->field($trainingproc_form, 'eduprog_id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb15">Заголовок</h4>
                        <?= $form->field($trainingproc_form, 'name', ['template' => '<div class="symbols_counter_box">{input}<span class="symbols_counter"></span></div>{error}{hint}'])->textInput(['autocomplete' => 'off', 'placeholder' => "Заголовок", 'class' => 'input_text limitedSybmbols', 'maxlength' => 90]); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20">Текст</h4>
                        <?= $form->field($trainingproc_form, 'content', ['template' => '{input}{error}{hint}'])->widget(CKEditor::class, [
                                'editorOptions' => ElFinder::ckeditorOptions(
                                        'elfinderuser',
                                        array_merge(['editorplaceholder' => "Введите текст новости"], \app\helpers\ckeditor\CKConfig::add_charlimit(\app\helpers\ckeditor\CKConfig::DEFAULT, 2000))
                                ),
                                'containerOptions' => [
                                        'class' => 'editor_container',
                                ],
                        ]); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <div class="lk_content">
                        <?= $form->field($trainingproc_form, 'start_publish_late', ['options' => ['class' => 'ip_cell ip_cell-format w100 mb10'], 'template' => '{input}{error}{hint}'])->radioList(
                                ['Опубликовать сейчас', 'Запланировать публикацию'],
                                ['item' => function ($index, $label, $name, $checked, $value) {
                                    $return = '<div class="ip_cell w100"><input type="radio" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked="checked"' : '') . ' class="ch ' . ($value ? 'need-delay-ch' : 'need-send-ch') . '"><label class="notmark">' . $label . '</label></div>';
                                    return $return;
                                }
                                ]
                        ); ?>
                        <div class="ip_cell ip_cell-event-date w100 mb0 need-delay-ch-wrap" style="display: none;">
                            <?= $form->field($trainingproc_form, 'start_publish_date', ['template' => '{label}{input}{error}{hint}', 'options' => ['class' => 'ip_cell datarange_ipc mr20']])->textInput(['placeholder' => "Дата начала", 'class' => 'input_text datepicker']); ?>

                            <?= $form->field($trainingproc_form, 'start_publish_time', ['template' => '{label}{input}{error}{hint}', 'options' => ['class' => 'ip_cell datarange_ipc mr20']])->input('time', ['placeholder' => "__:__", 'class' => 'input_text']); ?>
                        </div>
                    </div>
                </div>

                <div class="lk_block need-send-ch-wrap">
                    <div class="lk_content">
                        <div class="ip_cell w100 flex align-center justify-between buttons-wrapper mb0">
                            <button type="submit" class="button blue medium lk">Опубликовать</button>
                        </div>
                    </div>
                </div>
                <div class="lk_block need-delay-ch-wrap" style="display: none;">
                    <div class="lk_content">
                        <div class="ip_cell w100 flex align-center justify-between buttons-wrapper mb0">
                            <button type="submit" class="button blue medium lk">Запланировать</button>
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
                <div class="modal_title">Ошибка создания порядка обучения</div>
                <p>При сохранении данных возникли ошибки. <?= \app\helpers\MainHelper::getHelpText() ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>

<?php
$url = Url::toRoute(['/pages/eduprog/save-trainingproc/']);
$js = <<<JS
    $('#eduprog-trainingproc-form').on('beforeSubmit', function(event){
        var formData = new FormData($('#eduprog-trainingproc-form')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    // переадресация на список порядка обучения
                    window.location.href = data.redirect_to;
                } else {
                    // показать модалку с ошибкой
                    $('#fail_service_modal .modal_title').html('Ошибка сохранения порядка обучения');
                    $('#fail_service_modal p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
        return false;
    });
    $('#eduprog-trainingproc-form').on('submit', function(e){
        e.preventDefault();
        return false;
    });
JS;
$this->registerJs($js);
?>