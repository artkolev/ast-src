<?php

use app\models\ProfileCafedra;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var ProfileCafedra $modelform
 * @var bool $can_edit
 */
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
                        <?= $form->field($modelform, 'main_direction', [
                                'template' => '{label}<div class="flex">{input}' . $modelform->getQuestion('main_direction') . '</div>{error}{hint}',
                        ])->dropDownList($modelform->getDirectionList(), ['class' => 'select_simple']); ?>
                        <div class="description_direction hidden"></div>
                        <div class="another_cafedra hidden">
                            <?= $form->field($modelform, 'comment', ['template' => '{label}{input}<span class="symbols_counter">300 символов</span>{error}{hint}', 'options' => ['class' => 'ip_cell w100 symbols_counter_box']])->textArea(['placeholder' => 'Описание кафедры', 'maxlength' => 300, 'class' => 'input_text limitedSybmbols']); ?>
                        </div>
                        <br>
                        <?php if ($can_edit) { ?>
                            <div class="ip_cell w100">
                                <button class="button-o blue" type="submit">Продолжить</button>
                            </div>
                        <?php } ?>
                        <?php ActiveForm::end(); ?>
                    </main>
                </div>

            </div>
            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
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
if (!$can_edit) {
    $js = <<<JS
    $('#profilefiz-form input').attr('disabled','disabled');
    $('#profilefiz-form select').attr('disabled','disabled');
JS;
    $this->registerJs($js);
}
$url = Url::toRoute(['/pages/profile/savecafedra']);
$url_direct = Url::toRoute(['/pages/profile/getdirinfo']);
$js = <<<JS
    $('#profilecafedra-main_direction').change(function(){
        var direction = $(this).val();
        if (direction == -1) {
            // выбрана кафедра Другое
            $('.another_cafedra').val('');
            $('.another_cafedra').removeClass('hidden');
        } else {
            $('.another_cafedra').val('');
            $('.another_cafedra').addClass('hidden');
        }
        $.ajax({
            type: 'GET',
            url: '{$url_direct}',
            processData: true,
            dataType: 'json',
            data: {direction:direction},
            success: function(data){
                if (data.status == 'success') {
                    $('.description_direction').html(data.html);
                    $('.description_direction').removeClass('hidden');
                } else {
                    $('.description_direction').html('');
                    $('.description_direction').addClass('hidden');
                }
            }
        });
    });
    $('#profilecafedra-main_direction').trigger('change');

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
                    // $('#success_profile_modal .success_box p').html(data.message);
                    // modalPos('#success_profile_modal');
                    if (data.redirect_to) {
                        window.location.href = data.redirect_to;
                    }
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