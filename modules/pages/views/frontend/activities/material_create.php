<?php

use app\modules\keywords\widgets\KeywordWidget;
use kitsunefet\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <?php $form = ActiveForm::begin([
                        'id' => 'material-form',
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
                <div class="lk_block">
                    <header class="lk_block_header">
                        <h1 class="lk_block_title-big"><?= $model->getNameForView(); ?></h1>
                        <?= $model->content; ?>
                    </header>
                    <main class="lk_content">
                    <span style="display: none">
                        <?= $form->field($material_model, 'id', ['template' => '{input}{error}{hint}'])->hiddenInput(); ?>
                    </span>
                        <?= $form->field($material_model, 'name', ['template' => '{label}{input}<span class="symbols_counter">90 символов</span>{error}{hint}', 'options' => ['class' => 'ip_cell w100 symbols_counter_box']])->textInput(['placeholder' => 'Укажите название', 'class' => "input_text limitedSybmbols", 'maxlength' => 90]); ?>

                        <?= $form->field($material_model, 'description')->textArea(['placeholder' => "Краткое описание"]); ?>

                        <?= $form->field($material_model, 'content', ['template' => '{input}{error}{hint}'])->widget(CKEditor::class, [
                                'editorOptions' => ElFinder::ckeditorOptions(
                                        'elfinderuser',
                                        \app\helpers\ckeditor\CKConfig::add_charlimit(\app\helpers\ckeditor\CKConfig::DEFAULT, 30000)
                                ),
                                'containerOptions' => [
                                        'class' => 'editor_container',
                                ],
                        ]); ?>

                        <?= $form->field($material_model, 'direction')->dropDownList($material_model->getDirectionList(), ['class' => "pretty_select_max3", 'multiple' => 'multiple']); ?>
                        <?= $form->field($material_model, 'start_publish', ['template' => '{label}{input}{error}{hint}'])->textInput(['class' => "input_text ip_medium datepicker keypress date-mask"]); ?>
                        <?= $form->field($material_model, 'end_publish', ['template' => '{label}{input}{error}{hint}'])->textInput(['class' => "input_text ip_medium datepicker keypress date-mask"]); ?>
                        <h4 class="lk_step_title mt20">Обложка</h4>
                        <p>Пожалуйста, не используйте изображение с нанесенным на него текстом.</p>
                        <?= $form->field($material_model, 'image', ['options' => ['class' => ''], 'template' => '{input}{error}{hint}'])->widget('app\widgets\singleimage\SingleimageWidget', ['preloaded' => $original->image]); ?>

                        <h4 class="lk_step_title mt20">Добавить видео</h4>
                        <?= $form->field($material_model, 'video_name')->textInput(['placeholder' => "Введите название видео"]); ?>
                        <?= $form->field($material_model, 'video_link')->textInput(['placeholder' => "Укажите ссылку на видео"]); ?>
                        <?= $form->field($material_model, 'videoimage', ['options' => ['class' => ''], 'template' => '{input}{error}{hint}'])->widget('app\widgets\singleimage\SingleimageWidget', ['preloaded' => $original->videoimage]); ?>

                        <h4 class="lk_step_title mt20">Статус публикации</h4>
                        <div class="ip_cell w100">
                            <?= $form->field($material_model, 'visible', ['template' => '{input}<label class="notmark">Публиковать</label>{hint}{error}'])->checkbox(['class' => 'ip_cell w100 ch small ch_politics'], false); ?>
                        </div>
                        <h4 class="lk_step_title">Кому отображать</h4>
                        <?= $form->field($material_model, 'vis_for', ['template' => '{input}{error}{hint}'])->radioList(
                                $material_model->getVisList(),
                                [
                                        'item' => function ($index, $label, $name, $checked, $value) {
                                            return '<div class="ip_cell w100"><input type="radio" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked' : '') . ' class="ch" data-kind="kind_' . $value . '" tabindex="3"><label class="notmark">' . $label . '</label></div>';
                                        }
                                ]
                        )->label(false); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb15">Выберите ключевые слова</h4>
                        <p class="mt0 mb20">Выберите 5 ключевых слов из предложенных.<br>Это нужно для более быстрого и
                            удобного поиска программы на сайте.</p>
                        <?= $form->field($material_model, 'keywords', ['template' => '{input}{error}{hint}'])->widget(KeywordWidget::class)->label(''); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb15">Укажите теги</h4>
                        <p class="mt0 mb20">Выберите из предложенных и при необходимости добавьте свои.</p>
                        <?= $form->field($material_model, 'tags')->dropDownList($material_model->getTagsList(), ['class' => "pretty_tags_ns_max10", 'multiple' => 'multiple']); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content">
                        <div class="ip_cell w100">
                            <button type="submit" class="button-o lk_button_submit">Сохранить</button>
                        </div>
                    </main>
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
                <div class="modal_title">Ошибка создания материала</div>
                <p>При сохранении данных возникли ошибки. <?= \app\helpers\MainHelper::getHelpText() ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>

        </div>
        <div class="modal_overlay"></div>
    </div>
<?php
$url = Url::toRoute(['/pages/activities/savematerial']);
$js = <<<JS
    $('#material-form').on('beforeSubmit', function(event){
        var formData = new FormData($('#material-form')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    // показать модалку об успешном действии, через 5 секунд редирект
                    $('#fail_service_modal .modal_title').html('Создание материала');
                    $('#fail_service_modal p').html(data.message);
                    modalPos('#fail_service_modal');

                    // переадресация на страницу списка новостей
                    window.redirect_url = data.redirect_to;
                    setTimeout('window.location.href = window.redirect_url',5000);
                } else {
                    // показать модалку с ошибкой
                    $('#fail_service_modal .modal_title').html('Ошибка создания материала');
                    $('#fail_service_modal p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
        return false;
    });
    $('#material-form').on('submit', function(e){
        e.preventDefault();
        return false;
    });
JS;
$this->registerJs($js);
?>