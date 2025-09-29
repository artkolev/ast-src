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
                        'id' => 'blog-form',
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
                                <?= $form->field($news_model, 'id', ['template' => '{input}{error}{hint}'])->hiddenInput(); ?>
                            </span>
                        <?= $form->field($news_model, 'name', ['template' => '{label}{input}<span class="symbols_counter">90 символов</span>{error}{hint}', 'options' => ['class' => 'ip_cell w100 symbols_counter_box']])->textInput(['placeholder' => 'Укажите название', 'class' => "input_text limitedSybmbols", 'maxlength' => 90]); ?>
                        <?= ''; // =$form->field($news_model, 'url')->textInput(['placeholder' => "Url-адрес новости"]); ?>
                        <?= $form->field($news_model, 'city_id')->dropDownList($news_model->cityList, ['class' => "pretty_select"]); ?>

                        <?= $form->field($news_model, 'content', ['template' => '{input}{error}{hint}'])->widget(CKEditor::class, [
                                'editorOptions' => ElFinder::ckeditorOptions(
                                        'elfinderuser',
                                        \app\helpers\ckeditor\CKConfig::add_charlimit(\app\helpers\ckeditor\CKConfig::DEFAULT, 30000)
                                ),
                                'containerOptions' => [
                                        'class' => 'editor_container',
                                ],
                        ]); ?>

                        <?= $form->field($news_model, 'direction')->dropDownList($news_model->getDirectionList(), ['class' => "pretty_select_max3", 'multiple' => 'multiple']); ?>
                        <?= $form->field($news_model, 'start_publish', ['template' => '{label}{input}{error}{hint}'])->textInput(['class' => "input_text ip_medium datepicker keypress date-mask"]); ?>
                        <?= $form->field($news_model, 'end_publish', ['template' => '{label}{input}{error}{hint}'])->textInput(['class' => "input_text ip_medium datepicker keypress date-mask"]); ?>

                        <?= $form->field($news_model, 'image', ['options' => ['class' => ''], 'template' => '{input}{error}{hint}'])->widget('app\widgets\singleimage\SingleimageWidget', ['preloaded' => $original->image]); ?>

                        <h4 class="lk_step_title mt20">Добавить видео в начало публикации</h4>
                        <?= $form->field($news_model, 'video1_name')->textInput(['placeholder' => "Введите название видео"]); ?>
                        <?= $form->field($news_model, 'video1_link')->textInput(['placeholder' => "Укажите ссылку на видео"]); ?>

                        <?= $form->field($news_model, 'video1', ['options' => ['class' => ''], 'template' => '{input}{error}{hint}'])->widget('app\widgets\singleimage\SingleimageWidget', ['preloaded' => $original->video1]); ?>

                        <h4 class="lk_step_title mt20">Добавить видео в конце публикации</h4>
                        <?= $form->field($news_model, 'video2_name')->textInput(['placeholder' => "Введите название видео"]); ?>
                        <?= $form->field($news_model, 'video2_link')->textInput(['placeholder' => "Укажите ссылку на видео"]); ?>

                        <?= $form->field($news_model, 'video2', ['options' => ['class' => ''], 'template' => '{input}{error}{hint}'])->widget('app\widgets\singleimage\SingleimageWidget', ['preloaded' => $original->video2]); ?>
                        <h4 class="lk_step_title mt20">Статус публикации</h4>
                        <div class="ip_cell w100">
                            <?= $form->field($news_model, 'visible', ['template' => '{input}<label class="notmark">Публиковать</label>{hint}{error}'])->checkbox(['class' => 'ip_cell w100 ch small ch_politics'], false); ?>
                        </div>
                        <h4 class="lk_step_title">Кому отображать</h4>
                        <?= $form->field($news_model, 'vis_for', ['template' => '{input}{error}{hint}'])->radioList(
                                $news_model->getVisList(),
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
                        <?= $form->field($news_model, 'keywords', ['template' => '{input}{error}{hint}'])->widget(KeywordWidget::class)->label(''); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb15">Укажите теги</h4>
                        <p class="mt0 mb20">Выберите из предложенных и при необходимости добавьте свои.</p>
                        <?= $form->field($news_model, 'tags')->dropDownList($news_model->getTagsList(), ['class' => "pretty_tags_ns_max5", 'multiple' => 'multiple']); ?>
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
                <div class="modal_title">Ошибка создания новости</div>
                <p>При сохранении данных возникли ошибки. <?= \app\helpers\MainHelper::getHelpText() ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>

        </div>
        <div class="modal_overlay"></div>
    </div>
<?php
$url = Url::toRoute(['/pages/activities/savenews']);
$js = <<<JS
    $('#blog-form').on('beforeSubmit', function(event){
        var formData = new FormData($('#blog-form')[0]);
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
                    $('#fail_service_modal .modal_title').html('Создание новости');
                    $('#fail_service_modal p').html(data.message);
                    modalPos('#fail_service_modal');

                    // переадресация на страницу списка новостей
                    window.redirect_url = data.redirect_to;
                    setTimeout('window.location.href = window.redirect_url',5000);
                } else {
                    // показать модалку с ошибкой
                    $('#fail_service_modal .modal_title').html('Ошибка создания новости');
                    $('#fail_service_modal p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
        return false;
    });
    $('#blog-form').on('submit', function(e){
        e.preventDefault();
        return false;
    });
JS;
$this->registerJs($js);
?>