<?php

use kitsunefet\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <div class="lk_block">
                    <header class="lk_block_header">
                        <h1 class="lk_block_title-big"><?= $model->getNameForView(); ?></h1>
                        <?= $model->content; ?>
                    </header>
                    <main class="lk_content">
                        <?php $form = ActiveForm::begin([
                                'id' => 'profilefiz-form',
                                'action' => '/site/ajaxValidate/',
                                'options' => ['class' => 'register_form marked'],
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
                        <?= $form->field($modelform, 'about_myself')->textArea(['placeholder' => "Например, специалист по личностному развитию, доктор экономических наук"]); ?>
                        <?= $form->field($modelform, 'history')->widget(CKEditor::class, [
                                'editorOptions' => ElFinder::ckeditorOptions(
                                        'elfinderuser',
                                        array_merge(['editorplaceholder' => 'Моя история'], \app\helpers\ckeditor\CKConfig::DEFAULT)
                                ),
                                'containerOptions' => [
                                        'class' => 'editor_container',
                                ],
                        ]); ?>
                        <?= $form->field($modelform, 'video')->input('url', ['placeholder' => "Ссылка на видеопрезентацию"]); ?>
                        <div class="ip_cell w100">
                            <button class="button-o lk_button_submit" type="submit">Сохранить</button>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </main>
                </div>

            </div>
            <?= \app\modules\users\widgets\profile\ExpertmenuWidget::widget(); ?>
        </div>
    </main>
    <div class="modal" id="fail_profile_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Ошибка сохранения данных</div>
                <p><?= \app\helpers\MainHelper::getHelpText() ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
    <div class="modal" id="success_profile_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Изменение профиля</div>
                <p>Профиль успешно изменён</p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
<?php
$url = Url::toRoute(['/pages/profile/savehistory']);
$js = <<<JS
    $('#profilefiz-form').on('beforeSubmit', function(event){
        var formData = new FormData($('#profilefiz-form')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    $('#success_profile_modal .success_box p').html(data.message);
                    modalPos('#success_profile_modal');
                    setTimeout(function () {
                        closeModal('#success_profile_modal'); // закрываем модалку
                    }, 2000);
                } else {
                    // показать модалку с ошибкой
                    $('#fail_profile_modal .success_box p').html(data.message);
                    modalPos('#fail_profile_modal');
                }
            }
        });
        return false;
    });
    $('#profilefiz-form').on('submit', function(e){
        e.preventDefault();
        return false;
    });
JS;
$this->registerJs($js);
?>