<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
<div class="card p-3">
    <div class="card-body">
        <h1>Авторизация</h1>
        <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'options' => ['class' => ''],
                'action' => '/admin/login/validate/',
                'enableAjaxValidation' => true,
                'enableClientValidation' => false,
                'validateOnSubmit' => true,
                'validateOnChange' => false,
                'validateOnType' => false,
                'validateOnBlur' => false,
                'fieldConfig' => [
                        'options' => ['class' => 'input-group mb-3']
                ],
        ]); ?>
        <?= $form->field($model, 'action', ['template' => '<div style="display:none;">{input}{error}{hint}</div>'])->hiddenInput(); ?>
        <?= $form->field($model, 'username', ['template' => '<div class="input-group-prepend"><span class="input-group-text"><svg class="c-icon"><use xlink:href="' . Yii::$app->assetManager->getPublishedUrl('@admin/web') . '/svg/free.svg#cil-user"></use></svg></span></div>{input}<div class="w100">{error}{hint}</div>', 'inputOptions' => ['class' => 'form-control', 'placeholder' => 'E-mail или логин', 'autocomplete' => 'off']])->textInput(); ?>
        <?= $form->field($model, 'password', ['template' => '<div class="input-group-prepend"><span class="input-group-text"><svg class="c-icon"><use xlink:href="' . Yii::$app->assetManager->getPublishedUrl('@admin/web') . '/svg/free.svg#cil-lock-locked"></use></svg></span></div>{input}<div class="w100">{error}{hint}</div>', 'inputOptions' => ['class' => 'form-control', 'placeholder' => 'Пароль', 'autocomplete' => 'off']])->passwordInput(); ?>
        <div class="showCode">
            <span class="code_message">Код был отправлен на указанный email</span>
            <?= $form->field($model, 'code', ['template' => '<div class="input-group-prepend"><span class="input-group-text"><svg class="c-icon"><use xlink:href="' . Yii::$app->assetManager->getPublishedUrl('@admin/web') . '/svg/free.svg#cil-applications"></use></svg></span></div>{input}<div class="w100">{error}{hint}</div>', 'inputOptions' => ['class' => 'form-control', 'placeholder' => 'Введите код', 'autocomplete' => 'off']])->textInput(); ?>
        </div>
        <div class="row">
            <div class="col-12 text-right hideCode">
                <button class="btn btn-primary px-4" type="submit">Получить код</button>
                <div class="showCode">
                    <button class="btn btn-primary px-4" type="submit">Вход</button>
                </div>
            </div>
            <div class="col-6 showCode">
                <button class="btn btn-link px-0 resendCode" type="button">Отправить код повторно</button>
            </div>
            <div class="col-6 text-right showCode">
                <button class="btn btn-primary px-4" type="submit">Вход</button>
            </div>
            <!-- <div class="col-6 text-right"> -->
            <!-- <button class="btn btn-link px-0" type="button">Забыли пароль?</button> -->
            <!-- </div> -->
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php

$this->registerCss('.showCode{display:none;} .code_message{display:none;color: green;font-weight: bold;margin: 0 0 10px 0;}');

$url_save = Url::toRoute(['/admin/login/index']);
$js = <<<JS
	$('.resendCode').click(function(){
		$('#loginform-action').val('default');
		$('#loginform-code').val('');
		$('#login-form').submit();
	});
    $('#login-form').on('beforeSubmit', function(event) {
		var formData = new FormData($('#login-form')[0]);
		$.ajax({
			type: 'POST',
			url: '{$url_save}',
			contentType: false,
			processData: false,
			dataType: 'json',
			data: formData,
			success: function(data) {
				if (data.status == 'success') {
					if (data.action == 'showCode') {
						// показать поле для ввода кода
						$('.showCode').css('display','block');
						$('.hideCode').css('display','none');
						// сменить экшен
						$('#loginform-action').val('code');
						// показать сообщение о коде
						$('.code_message').html(data.message);
						$('.code_message').css('display','block');
						setTimeout("$('.code_message').css('display','none');",3000);
					}
					if (data.action == 'login') {
						window.location.href = data.url;
					}
				} else {
                    alert(data.message);
				}
		    }
		});
	    return false;
    });
JS;
$this->registerJs($js);
?>
