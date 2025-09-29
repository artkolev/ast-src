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
                        <div class="existing_educat">
                            <?php foreach ($user->educationFront as $education) {
                                echo $this->render('_education_element', ['education' => $education, 'can_edit' => true]);
                            } ?>
                        </div>
                        <div class="ip_cell w100">
                            <button class="button blue medium lk add_education">Добавить образование</button>
                        </div>
                        <?= $form->field($anketaform, 'education_check', ['template' => '{input}{error}{hint}'])->textInput(['style' => 'display:none']); ?>
                    </main>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
            <?= \app\modules\users\widgets\profile\ExpertmenuWidget::widget(); ?>
        </div>
    </main>
    <div class="modal" id="education_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <?php
            $educationModel = new \app\models\ProfileEducation();
            $form = ActiveForm::begin([
                    'id' => 'education-form',
                    'action' => '/site/ajaxValidate/',
                    'options' => ['class' => 'education_form'],
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
            <div class="modal_title">Образование</div>
            <input type="hidden" id="educationId" name="id">

            <div class="ip_cell w100">
                <label class="ip_label">Учебное заведение*</label>
                <?= $form->field($educationModel, 'name')->textInput(['placeholder' => "Название учебного заведения*"]); ?>
            </div>

            <div class="ip_cell w100">
                <label class="ip_label">Специализация*</label>
                <?= $form->field($educationModel, 'speciality')->textInput(['placeholder' => "Ваша специализация*"]); ?>
            </div>

            <div class="ip_cell w100">
                <label class="ip_label">Уровень*</label>
                <?= $form->field($educationModel, 'stage_id')->dropDownList($educationModel->getStageList(), ['class' => "pretty_select"]); ?>
            </div>

            <div class="ip_cell w100">
                <label class="ip_label">Период обучения</label>
                <div class="time_box years">
                    <?= $form->field($educationModel, 'study_from', ['options' => ['class' => 'ip_cell ipс_short'], 'template' => '{input}{error}{hint}'])->textInput(['placeholder' => "1990", 'class' => 'input_text ip_short maskYear datepicker-yyyy keypress']); ?>
                    <span>&mdash;</span>
                    <?= $form->field($educationModel, 'study_to', ['options' => ['class' => 'ip_cell ipс_short'], 'template' => '{input}{error}{hint}'])->textInput(['placeholder' => "1994", 'class' => 'input_text ip_short maskYear datepicker-yyyy keypress']); ?>
                </div>
            </div>

            <div class="ip_cell w100">
                <?= $form->field($educationModel, 'by_realtime', ['template' => '{input}<label>По настоящее время</label>{hint}{error}', 'options' => ['class' => 'ip_cell w49']])->checkbox(['class' => 'ch'], false); ?>
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
                <div class="modal_title">Изменение образования</div>
                <p>Образование успешно изменено</p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
<?php
$url_get_edu = Url::toRoute(['/pages/profile/geteducation']);
$url_save_edu = Url::toRoute(['/pages/profile/saveeducation']);
$url_remove_edu = Url::toRoute(['/pages/profile/removeeducation']);

$js = <<<JS
    //образование
    $('body').on('click','.add_education', function(e){
		e.preventDefault();
		modalPos('#education_modal');
		$("#education-form")[0].reset();
        $('#educationId').val('')
	});
    $('#education-form').on('submit', function(e){
    	e.preventDefault();
    	return false;
    });
    $('#education-form').on('beforeSubmit', function(event){
        let formData = new FormData($('#education-form')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url_save_edu}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    closeModal('#education_modal');
                    $("#education-form")[0].reset();
                    
                    if ($('[data-education="' + data.data.id + '"]').length > 0) {
                        $('[data-education="' + data.data.id + '"]').replaceWith(data.new_educat_html);
                    } else {
                    $('.existing_educat').append(data.new_educat_html);
                            }
                } else if (data.status == 'fail') {
                    $('#fail_profile_modal .success_box p').html(data.message);
                    modalPos('#fail_profile_modal');
                }
                $('#anketa-form').yiiActiveForm('validateAttribute', 'profileanketa-education_check');
                        },
            error: function(data) {
                $('#fail_profile_modal .success_box p').html('Произошла ошибка при отправке данных');
                modalPos('#fail_profile_modal');
                }
        });
        return false;
    });
    $('body').on('click','.edit_education', function(e) {
        let id = $(e.currentTarget).parent().data('education');
            $.ajax({
            type: 'GET',
            url: '{$url_get_edu}',
                dataType: 'json',
            data: {id: id},
                success: function(data){
                    if (data.status == 'success') {
                    let modal = $('#education_modal');
                    
                    for (var attr in data.data) {
                        if (attr == 'by_realtime') {
                            if (data.data[attr] == '1') {
                                $('#profileeducation-' + attr, modal).prop('checked', true);
                    }
                        } else {
                            $('#profileeducation-' + attr, modal).val(data.data[attr])
                        }
                    }
                    $('#profileeducation-stage_id').trigger('change');
                    $('#educationId').val(id)

		            modalPos('#education_modal');
                } else if (data.status == 'fail') {
                    $('#fail_profile_modal .success_box p').html(data.message);
                    modalPos('#fail_profile_modal');
                }
            },
            error: function(data) {
                $('#fail_profile_modal .success_box p').html('Произошла ошибка при отправке данных');
                modalPos('#fail_profile_modal');
        }
    });
	});
    $('body').on('click','.remove_education', function(e) {
        let id = $(e.currentTarget).parent().data('education');
        $.ajax({
            type: 'POST',
            url: '{$url_remove_edu}',
            dataType: 'json',
            data: {id: id},
            success: function(data){
                if (data.status == 'success') {
                    $('[data-education="' + id + '"]').remove();
                } else if (data.status == 'fail') {
                    $('#fail_profile_modal .success_box p').html(data.message);
                    modalPos('#fail_profile_modal');
                }
                $('#anketa-form').yiiActiveForm('validateAttribute', 'profileanketa-education_check');
            },
            error: function(data) {
                $('#fail_profile_modal .success_box p').html('Произошла ошибка при отправке данных');
                modalPos('#fail_profile_modal');
            }
        });
    });
JS;
$this->registerJs($js);
?>