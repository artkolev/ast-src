<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
<?php
$user = Yii::$app->user->identity->userAR;
$login_page = \app\modules\pages\models\Login::find()->where(['model' => \app\modules\pages\models\Login::class, 'visible' => 1])->one();
$login_url = $login_page ? $login_page->getUrlPath() : false;
$register_page = \app\modules\pages\models\Regfizusr::find()->where(['model' => \app\modules\pages\models\Regfizusr::class, 'visible' => 1])->one();
$register_url = $register_page ? $register_page->getUrlPath() : false;
$fieldOptions = ['options' => ['class' => 'ip_cell label-on w100'], 'template' => '{input}{label}<div class="input-status"></div>{error}{hint}', 'inputOptions' => ['class' => 'input_text'], 'labelOptions' => ['class' => 'ip_label']];
?>
<div class="modal" id="orders_modal">
    <div class="modal_content">
        <a href="#" class="modal_close">x</a>
        <div class="modal_title">Создание заказа</div>
        <div id="service_article" class="ip_cell">
            <div class="service_article-title"></div>
            <div class="service_article-text"></div>
        </div>
        <?php $form = ActiveForm::begin([
                'id' => 'orders-form',
                'action' => '/site/ajaxValidate/',
                'options' => ['class' => 'contact_form'],
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
        <span style="display:none;">
            <?= $form->field($model, 'service_id')->hiddenInput(); ?>
        </span>
        <div class="ip_cell w100">
            <div class="buy-ticket-detail">
                Укажите ваши контактные данные для связи
            </div>
            <div id="fiz" class="register_form request_form tab-form">
                <div class="ip-cells">
                    <?= $form->field($model, 'customer_name', $fieldOptions)->textInput(['required' => 'required', 'class' => 'input_text']); ?>
                    <?= $form->field($model, 'customer_surname', $fieldOptions)->textInput(['required' => 'required', 'class' => 'input_text']); ?>
                    <?= $form->field($model, 'customer_phone', $fieldOptions)->input('tel', ['required' => 'required', 'placeholder' => '+7 (000) 000-00-00', 'class' => 'input_text phone-mask']); ?>
                </div>
            </div>
        </div>
        <div class="ip_cell w100 contact_buttons">
            <button type="submit" class="button-o blue big js-submit-button">Продолжить</button>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="modal_overlay"></div>
</div>
<div class="modal" id="fail_order_modal">
    <div class="modal_content">
        <a href="#" class="modal_close">x</a>
        <div class="success_box">
            <div class="modal_title">Создание заказа</div>
            <p>При создании заказа возникла ошибка.</p>
            <div class="modal_buttons">
                <a href="#" class="button small close_modal">ОК</a>
            </div>
        </div>

    </div>
    <div class="modal_overlay"></div>
</div>
<?php
$url_order = Url::toRoute(['/pages/orders/create']);
$js = <<<JS
	$('body').on('click','.orderCreate', function(e){
		e.preventDefault();
		let orderCreateButton = $(this);
        
		orderCreateButton.prop('disabled', true);
		orderCreateButton.addClass('disabled');

		var service = $(this).data('service');
		var token = yii.getCsrfToken();
		$.ajax({
			type: 'POST',
			url: '{$url_order}',
			processData: true,
			dataType: 'json',
			data: {service:service,param:token},
			success: function(data){
				if (data.status == 'success') {
					// в случае успеха редирект на страницу оплаты заказа
					if (data.redirect_to) {
						window.location.href = data.redirect_to;
					} else {
						$('#fail_order_modal .success_box p').html(data.message);
						modalPos('#fail_order_modal');
					}
                    orderCreateButton.prop('disabled', false);
                    orderCreateButton.removeClass('disabled');
				} else if (data.status == 'need_register') {
					$.fancybox.open($('#need_auth'));
                    orderCreateButton.prop('disabled', false);
                    orderCreateButton.removeClass('disabled');
				} else {
					// в случае ошибки вывести сообщение
					$('#fail_order_modal .success_box p').html(data.message);
					modalPos('#fail_order_modal');
                    
                    orderCreateButton.prop('disabled', true);
                    orderCreateButton.addClass('disabled');
				}
			}
		});
	});
JS;

$jsModal = <<<JS
	$('body').on('click','.orderCreate', function(e){
		e.preventDefault();
		var service = $(this).data('service');
        
        $('#orders-service_id').val(service);
        modalPos('#orders_modal');
	});

    $('#orders-form').on('beforeSubmit', function(event){
		let orderCreateButton = $('#orders-form .js-submit-button');
		orderCreateButton.prop('disabled', true);
		orderCreateButton.addClass('disabled');
		var formData = new FormData($('#orders-form')[0]);
		$.ajax({
			type: 'POST',
			url: '{$url_order}',
			contentType: false,
			processData: false,
			dataType: 'json',
			data: formData,
			success: function(data) {
				closeModal('#orders_modal');
				if (data.status == 'success') {
					// в случае успеха редирект на страницу оплаты заказа
					if (data.redirect_to) {
						window.location.href = data.redirect_to;
					} else {
						$('#fail_order_modal .success_box p').html(data.message);
						modalPos('#fail_order_modal');
					}
                    orderCreateButton.prop('disabled', false);
                    orderCreateButton.removeClass('disabled');
				} else if (data.status == 'need_register') {
					$.fancybox.open($('#need_auth'));
                    orderCreateButton.prop('disabled', false);
                    orderCreateButton.removeClass('disabled');
				} else {
					// в случае ошибки вывести сообщение
					$('#fail_order_modal .success_box p').html(data.message);
					modalPos('#fail_order_modal');
                    
                    orderCreateButton.prop('disabled', true);
                    orderCreateButton.addClass('disabled');
				}
			},
            error: function() {
            	orderCreateButton.prop('disabled', false);
            	orderCreateButton.removeClass('disabled');
			}
		});
		
	    return false;
    });

    $('#orders-form').on('submit', function(e){
    	e.preventDefault();
    	return false;
    });

    $('#orders-form').on('beforeValidate', function(event){
        $('#orders-form .js-submit-button').prop('disabled', true);
        $('#orders-form .js-submit-button').addClass('disabled');
    });

    $('#orders-form').on('afterValidate', function(event, messages, errorAttributes){
        if (errorAttributes.length > 0) {
            $('#orders-form .js-submit-button').prop('disabled', false);
            $('#orders-form .js-submit-button').removeClass('disabled');
        }
    });
JS;


if (!Yii::$app->user->isGuest && (empty($user->profile->name) || empty($user->profile->surname) || empty($user->profile->phone))) {
    $this->registerJs($jsModal);

} else {
    $this->registerJs($js);
}
?>
