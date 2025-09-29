<?php

use app\modules\pages\models\Regfizusr;
use app\modules\pages\models\Resetpass;
use app\modules\pages\models\SupportPage;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>

<section class="sec reg-section">
    <div class="container wide">
        <div class="reg-block">
            <h1 class="auth-title">Восстановление пароля</h1>
            <?php $form = ActiveForm::begin([
                    'id' => 'setnewpass-form',
                    'action' => '/site/ajaxValidate/',
                    'options' => ['class' => 'reg-form js-validation'],
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => false,
                    'validateOnSubmit' => true,
                    'validateOnChange' => true,
                    'validateOnType' => false,
                    'validateOnBlur' => true,
                    'fieldConfig' => [
                            'options' => ['class' => 'ip_cell label-on w100'],
                            'template' => '{input}{label}<div class="input-status"></div>{error}{hint}<a href="#" class="show_password"></a>',
                            'inputOptions' => ['class' => 'input_text'],
                            'labelOptions' => ['class' => 'ip_label'],
                    ],
            ]); ?>
            <?= Html::hiddenInput('step', 'step3'); ?>
            <?= $form->field($modelform, 'email', ['template' => '{input}<div class="input-status"></div>{error}{hint}'])->hiddenInput(); ?>
            <?= $form->field($modelform, 'code', ['template' => '{input}<div class="input-status"></div>{error}{hint}'])->hiddenInput(); ?>
            <?= $form->field($modelform, 'password')->passwordInput(['placeholder' => 'Пароль', 'required' => '']); ?>
            <?= $form->field($modelform, 'passwordConfirm')->passwordInput(['placeholder' => 'Потвердить пароль', 'required' => '']); ?>
            <div class="ip_cell w100 mb0">
                <button class="button">Войти</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <?php if ($regfizusrPage = Regfizusr::find()->where(['model' => Regfizusr::class, 'visible' => 1])->one()) { ?>
            <div class="first-visit">
                Если вы впервые на сайте, <a href="<?= $regfizusrPage->getUrlPath(); ?>">зарегистрируйтесь</a>
            </div>
        <?php } ?>
    </div>
</section>
<div class="modal-side_banner" id="problem_auth">
    <div class="modal-review-content">
        <div class="modal-side_banner-info need_auth-popup-info">
            <h3><b>Не удаётся войти?</b></h3>
            <p>
                Проверьте корректность вводимых данных – без тире и пробела, язык ввода, клавишу «Caps Lock» <br><br>
                Воспользуйтесь функцией <a
                        href="<?= Url::toRoute(Resetpass::find()->where(['model' => Resetpass::class, 'visible' => 1])->one()->getUrlPath()); ?>"
                        class="reset_password">восстановления пароля</a> <br><br>
                Если указанные способы не помогают, <a
                        href="<?= Url::toRoute(SupportPage::find()->where(['model' => SupportPage::class, 'visible' => 1])->one()->getUrlPath()); ?>">обратитесь
                    в службу поддержки</a>
            </p>
        </div>
    </div>
</div>
<?php
$url = Url::toRoute(['/pages/register/resetpass']);
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
				}
		    }
		});
	    return false;
    });
    $('#login-form').on('submit', function(e){
    	e.preventDefault();
    	return false;
    });
JS;
$this->registerJs($js);
?>

