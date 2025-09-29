<?php

use app\models\ProfileAnketa;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$anketaform = new ProfileAnketa();
?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <?php $form = ActiveForm::begin([
                    'id' => 'anketa-form',
                    'action' => '/site/ajaxValidate/',
                    'options' => ['class' => 'marked', 'autocomplete' => 'off'],
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
            <div class="lk_maincol">
                <div class="lk_block">
                    <header class="lk_block_header">
                        <h1 class="lk_block_title-big"><?= $model->getNameForView(); ?></h1>
                        <?= $model->content; ?>
                    </header>
                    <main class="lk_content">
                        <div class="existing_career">
                            <?php foreach ($user->careerFront as $career) {
                                echo $this->render('_career_element', ['career' => $career, 'can_edit' => true]);
                            } ?>
                        </div>
                        <div class="ip_cell w100">
                            <button class="button blue medium lk add_career">Добавить место работы</button>
                        </div>
                        <?= $form->field($anketaform, 'career_check', ['template' => '{input}{error}{hint}'])->textInput(['style' => 'display:none']); ?>
                    </main>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
            <?= \app\modules\users\widgets\profile\ExpertmenuWidget::widget(); ?>
        </div>
    </main>
    <div class="modal" id="career_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <?php
            $career_form = new \app\models\ProfileCareer();
            $form = ActiveForm::begin([
                    'id' => 'career-form',
                    'action' => '/site/ajaxValidate/',
                    'options' => ['class' => 'career_form'],
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => false,
                    'validateOnSubmit' => true,
                    'validateOnChange' => true,
                    'validateOnType' => false,
                    'validateOnBlur' => true,
                    'fieldConfig' => [
                            'template' => '{input}{error}{hint}',
                            'inputOptions' => ['class' => 'input_text'],
                    ],
            ]); ?>
            <div class="modal_title">Место работы</div>
            <input type="hidden" id="careerId" name="id">
            <div class="ip_cell w100">
                <label class="ip_label">Название организации*</label>
                <?= $form->field($career_form, 'name')->textInput(['placeholder' => "Название организации*"]); ?>
            </div>
            <div class="ip_cell w100">
                <label class="ip_label">Должность*</label>
                <?= $form->field($career_form, 'office')->textInput(['placeholder' => "Должность*"]); ?>
            </div>
            <div class="ip_cell w100">
                <label class="ip_label">Период работы</label>
                <div class="time_box years">
                    <?= $form->field($career_form, 'work_from', ['options' => ['class' => 'ip_cell ipс_short'], 'template' => '{input}{error}{hint}'])->textInput(['placeholder' => "1990", 'class' => 'input_text ip_short maskYear datepicker-yyyy keypress']); ?>
                    <span>&mdash;</span>
                    <?= $form->field($career_form, 'work_to', ['options' => ['class' => 'ip_cell ipс_short'], 'template' => '{input}{error}{hint}'])->textInput(['placeholder' => "1994", 'class' => 'input_text ip_short maskYear datepicker-yyyy keypress']); ?>
                </div>
            </div>
            <div class="ip_cell w100">
                <?= $form->field($career_form, 'by_realtime', ['template' => '{input}<label>По настоящее время</label>{hint}{error}', 'options' => ['class' => 'ip_cell w49']])->checkbox(['class' => 'ch'], false); ?>
            </div>
            <div class="ip_cell w100">
                <label class="ip_label">Достижения</label>
                <?= $form->field($career_form, 'achiev')->textInput(['placeholder' => "Достижения"]); ?>
            </div>
            <div class="ip_cell w100">
                <button class="button-o lk_button_submit" type="submit">Сохранить</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="modal_overlay"></div>
    </div>
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
                <div class="modal_title">Изменение внесены</div>
                <p>Данные успешно изменены</p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
<?php
$url_get_career = Url::toRoute(['/pages/profile/getcareer']);
$url_save_career = Url::toRoute(['/pages/profile/savecareer']);
$url_remove_career = Url::toRoute(['/pages/profile/removecareer']);

$js = <<<JS
    //место работы
    $('body').on('click','.add_career', function(e){
		e.preventDefault();
		modalPos('#career_modal');
		$("#career-form")[0].reset();
        $('#careerId').val('')
	});
    $('#career-form').on('submit', function(e){
    	e.preventDefault();
    	return false;
    });
    $('#career-form').on('beforeSubmit', function(event){
        let formData = new FormData($('#career-form')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url_save_career}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    closeModal('#career_modal');
                    $("#career-form")[0].reset();
                    
                    if ($('[data-career="' + data.data.id + '"]').length > 0) {
                        $('[data-career="' + data.data.id + '"]').replaceWith(data.new_career_html);
                    } else {
                    $('.existing_career').append(data.new_career_html);
                }
                } else if (data.status == 'fail') {
                    $('#fail_save_anketa .success_box p').html(data.message);
                    modalPos('#fail_save_anketa');
            }
                $('#anketa-form').yiiActiveForm('validateAttribute', 'profileanketa-career_check');
            },
            error: function(data) {
                $('#fail_save_anketa .success_box p').html('Произошла ошибка при отправке данных');
                modalPos('#fail_save_anketa');
            }
        });
        return false;
    });
    $('body').on('click','.edit_career', function(e) {
        let id = $(e.currentTarget).parent().data('career');
        $.ajax({
            type: 'GET',
            url: '{$url_get_career}',
            dataType: 'json',
            data: {id: id},
            success: function(data) {
                if (data.status == 'success') {
                    let modal = $('#career_modal');

                    for (var attr in data.data) {
                        if (attr == 'by_realtime') {
                            if (data.data[attr] == '1') {
                                $('#profilecareer-' + attr, modal).prop('checked', true);
            }
                } else {
                            $('#profilecareer-' + attr, modal).val(data.data[attr])
                        }
                    }
                    $('#careerId', modal).val(id)

		            modalPos('#career_modal');
                } else if (data.status == 'fail') {
                    $('#fail_save_anketa .success_box p').html(data.message);
                    modalPos('#fail_save_anketa');
                }
            },
            error: function(data) {
                $('#fail_save_anketa .success_box p').html('Произошла ошибка при отправке данных');
                modalPos('#fail_save_anketa');
            }
        });
    });
    $('body').on('click','.remove_career', function(e) {
        let id = $(e.currentTarget).parent().data('career');
        $.ajax({
            type: 'POST',
            url: '{$url_remove_career}',
            dataType: 'json',
            data: {id: id},
            success: function(data) {
                if (data.status == 'success') {
                    $('[data-career="' + id + '"]').remove();
                } else if (data.status == 'fail') {
                    $('#fail_save_anketa .success_box p').html(data.message);
                    modalPos('#fail_save_anketa');
                }
                $('#anketa-form').yiiActiveForm('validateAttribute', 'profileanketa-career_check');
            },
            error: function(data) {
                $('#fail_save_anketa .success_box p').html('Произошла ошибка при отправке данных');
                modalPos('#fail_save_anketa');
            }
        });
    });
JS;
$this->registerJs($js);
?>