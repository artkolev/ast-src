<?php

use app\modules\pages\models\SupportPage;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
    <section class="sec reg-section">
        <div class="container wide">
            <div class="reg-block">
                <h1 class="page-title">Введите код</h1>
                <!-- Все комменты ниже для вариации с смс -->
                <div class="reg-block-text">
                    Мы отправили письмо с кодом на email
                    <!-- Мы отправили SMS c кодом на номер -->
                </div>
                <div class="reset-info">
                    <div class="reset-name"><?= $modelform->email; ?></div>
                </div>
                <!-- <div class="reset-info">
                    <div class="reset-name">+7 903 097-14-42</div>
                    <a href="reg-new-reset1.html" class="change_reset_email">Изменить номер</a>
                </div> -->
                <?php $form = ActiveForm::begin([
                        'id' => 'confirmcode-form',
                        'action' => '/site/ajaxValidate/',
                        'options' => ['class' => 'reg-form reset-pwd-form js-validation'],
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => false,
                        'validateOnSubmit' => true,
                        'validateOnChange' => false,
                        'validateOnType' => false,
                        'validateOnBlur' => false,
                        'fieldConfig' => [
                                'options' => ['class' => 'ip_cell label-on'],
                                'template' => '{input}',
                                'inputOptions' => ['class' => 'input_text pincode'],
                        ],
                ]); ?>
                <?= $form->field($modelform, 'email', ['template' => '{input}{error}', 'options' => ['class' => 'ip_cell label-on w100']])->hiddenInput()->error(); ?>
                <div class="pincode-label">
                    Введите код из письма
                    <!-- Код из SMS -->
                </div>
                <div class="call-wrapper-v3">
                    <?= $form->field($modelform, 'code', ['template' => '{input}{error}{hint}', 'options' => ['class' => 'ip_cell label-on']])->textInput(['class' => 'input_text pincode-v3', 'pattern' => "/^-?\d+\.?\d*$/", 'onKeyPress' => 'if(this.value.length==6) return false;', 'required' => '']); ?>
                </div>
                <?= $form->field($modelform, 'codeFull', ['template' => '{input}{error}', 'options' => ['class' => 'ip_cell label-on w100']])->hiddenInput()->error(); ?>
                <div class="ip_cell w100 mb0">
                    <button class="button ">Отправить</button>
                </div>
                <div class="reset-pwd-info">
                    <span class="clock-text" style="display: none;">Получить новый код можно через <span
                                class="clock countdown"></span></span>
                    <a href="" class="reset_resend" style="display: none;">Получить новый код</a>
                </div>
                <?php if ($supportpagePage = SupportPage::find()->where(['model' => SupportPage::class, 'visible' => 1])->one()) { ?>
                    <div class="reg-support-link-wrapper">
                        <a href="<?= Url::toRoute($supportpagePage->getUrlPath()); ?>" class="reg-support-link"
                           target="_blank">Написать в техподдержку</a>
                    </div>
                <?php } ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </section>
<?php
$url = Url::toRoute(['/pages/register/checkconfirmcode']);
$yandex_id = Yii::$app->params['yandex_metric_id'];
$renewCodeUrl = Url::toRoute(['/pages/register/resendcode']);
$js = <<<JS
    // глобальная переменная для счётчика
    // передавать значение в секундах
	// если переменная resetTime пустая, то на странице будет отображаться кнопка "Получить новый код"
    let resetTime = {$reset_time};
    let resendTimer = {$resend_timer};
    // функция таймера
    function reSendCode(date) {
        $('.clock-text').show();
        $('.reset_resend').hide();
        let finalDate = new Date();
        finalDate = finalDate.setTime(finalDate.getTime() + (date*1000));
        $('.clock').parent().show();
        $('.clock').countdown(finalDate)
            .on('update.countdown', function(event) {
                $(this).html(event.strftime('%M:%S'));
            })
            .on('finish.countdown', function(event) {
                $(this).parent().hide();
                $('.reset_resend').show();
            });
    }
    // если переменная resetTime пустая, то на странице будет отображаться кнопка "Получить новый код"
    // иначе выполнится функция
    if(resetTime) {
        reSendCode(resetTime);
    } else {
        $('.reset_resend').show();
    }
    // по клику производим необходимые манипуляции для обновления счетчика
    $('body').on('click', '.reset_resend', function(e) {
        e.preventDefault();
        let param = yii.getCsrfParam();
        let token = yii.getCsrfToken();
        let data = {};
        data[param] = token;
        data['email'] = '{$modelform->email}';
        $.ajax({
			type: 'POST',
			url: '{$renewCodeUrl}',
			data: data,
			success: function () {
                reSendCode(resendTimer);
			}
		});
    });
    
    $('#confirmcode-form').on('beforeSubmit', function(event){
		var formData = new FormData($('#confirmcode-form')[0]);
		$.ajax({
			type: 'POST',
			url: '{$url}',
			contentType: false,
			processData: false,
			dataType: 'json',
			data: formData,
			success: function(data) {
				if (data.status == 'success') {
                    if (data.send_target) {
                        // ym({$yandex_id},'reachGoal',data.send_target);
                    }
					if (data.redirect_to) {
    					window.location.href = data.redirect_to;
                        return;
                    }
                    if (data.success_html) {
                        $('.reg-section .container.wide').html(data.success_html);
                        return;
                    }
				} else if (data.status == 'fail') {
					if (data.error_html) {
                        $('.reg-section .container.wide').html(data.error_html);
					}
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