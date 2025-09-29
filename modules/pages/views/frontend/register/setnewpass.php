<?php

use app\modules\settings\models\Settings;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>

    <section class="sec content_sec">
        <div class="container">
            <section class="register_form_success">
                <div class="ib">
                    <img src="/img/envelope_big.svg" alt=""/>
                    <h1><?= $model->getNameForView(); ?></h1>
                    <?php if ($message) { ?>
                        <p><?= $message; ?></p>
                        <a href="/" class="button small">ОК, вернуться на главную</a>
                    <?php } else { ?>
                        <?= $model->content; ?>
                        <?php $form = ActiveForm::begin([
                                'id' => 'setnewpass-form',
                                'action' => '/site/ajaxValidate/',
                                'options' => ['class' => ''],
                                'enableAjaxValidation' => true,
                                'enableClientValidation' => false,
                                'validateOnSubmit' => true,
                                'validateOnChange' => true,
                                'validateOnType' => false,
                                'validateOnBlur' => true,
                                'fieldConfig' => [
                                        'options' => ['class' => 'ip_cell'],
                                        'template' => '{input}{error}{hint}',
                                        'inputOptions' => ['class' => 'input_text'],
                                        'labelOptions' => ['class' => 'ip_label'],
                                ],
                        ]); ?>
                        <?= $form->field($modelform, 'key', ['options' => ['class' => '']])->hiddenInput(); ?>
                        <?= $form->field($modelform, 'hash', ['options' => ['class' => '']])->hiddenInput(); ?>
                        <?= $form->field($modelform, 'password')->passwordInput(['placeholder' => "Пароль*"]); ?>
                        <?= $form->field($modelform, 'passwordConfirm')->passwordInput(['placeholder' => "Подтверждение пароля*"]); ?>
                        <button type="submit" class="button small">Изменить пароль</button>
                        <?php ActiveForm::end(); ?>
                    <?php } ?>
                </div>
            </section>
        </div>
    </section>

    <div class="modal" id="fail_reset_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Ошибка сброса пароля</div>
                <p><?= Settings::getInfo('resetpass_error'); ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>

<?php
$url = Url::toRoute(['/pages/register/changepass']);
$js = <<<JS
    $('#setnewpass-form').on('beforeSubmit', function(event){
		var formData = new FormData($('#setnewpass-form')[0]);
		$.ajax({
			type: 'POST',
			url: '{$url}',
			contentType: false,
			processData: false,
			dataType: 'json',
			data: formData,
			success: function(data) {
				if (data.status == 'success') {
					// переадресация на страницу успешной регистрации Эксперта
					window.location.href = data.redirect_to;
				} else {
					// показать модалку с ошибкой
					modalPos('#fail_reset_modal');
				}
		    }
		});
	    return false;
    });
    $('#setnewpass-form').on('submit', function(e){
    	e.preventDefault();
    	return false;
    });
JS;
$this->registerJs($js);
?>