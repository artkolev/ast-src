<?php

use app\modules\pages\models\Regfizusr;
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
                            'template' => '{input}{label}<div class="input-status"></div>{error}{hint}',
                            'inputOptions' => ['class' => 'input_text'],
                            'labelOptions' => ['class' => 'ip_label'],
                    ],
            ]); ?>
            <?= Html::hiddenInput('step', 'step1'); ?>
            <?= $form->field($modelform, 'email')->textInput(['placeholder' => 'email@mail.ru', 'required' => '']); ?>
            <div class="ip_cell w100 mb0">
                <button class="button">Получить код</button>
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
