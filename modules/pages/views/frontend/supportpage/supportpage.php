<?php

?>

    <section class="sec content_sec">
        <div class="container small">
            <h1 id="page-title" class="page-title">Служба поддержки</h1>
            <?php if (!empty($model->content)) { ?>
                <!-- <div class="subheader"> -->
                <?= $model->content; ?>
                <!-- </div> -->
            <?php } ?>
            <?php /*  в content страницы разместят форму битрикс24. пока нашу форму скрываю.
		<?php $form = ActiveForm::begin([
		    'id' => 'support-form',
		    'options' => ['novalidate' => 'novalidate', 'class'	=> 'support-form', 'autocomplete' => 'off'],
		    'enableAjaxValidation' => false,
		    'enableClientValidation' => true,
		    'validateOnSubmit' => true,
		    'validateOnChange' => true,
		    'validateOnType' => false,
		    'validateOnBlur' => true,
		    'fieldConfig' => [
		        'options' => ['class' => 'ip_cell label-on w100'],
		        'template' => '{input}{label}{error}<div class="input-status"></div>{hint}',
		        'inputOptions' => ['class' => 'input_text'],
		        'labelOptions' => ['class' => 'ip_label'],
		    ],
		]); ?>
		<?php if (Yii::$app->user->isGuest) { ?>
			<div class="ip-cells">
				<?= $form->field($modelform, 'name')->textInput(['placeholder' => 'Имя']); ?>
				<?= $form->field($modelform, 'surname')->textInput(['placeholder' => 'Фамилия']); ?>
			</div>
			<div class="ip-cells">
				<?= $form->field($modelform, 'phone')->input('tel', ['placeholder' => 'Мобильный телефон', 'class' => 'input_text phone-mask']); ?>
				<?= $form->field($modelform, 'email')->input('email', ['placeholder' => 'Электронная почта']); ?>
			</div>
		<?php } else { ?>
				<?= $form->field($modelform, 'name', ['options' => ['class' => 'hidden']])->textInput(['type' => 'hidden', 'placeholder' => 'Имя', 'value' => Yii::$app->user->identity->userAR->profile->name]); ?>
				<?= $form->field($modelform, 'surname', ['options' => ['class' => 'hidden']])->textInput(['type' => 'hidden', 'placeholder' => 'Фамилия', 'value' => Yii::$app->user->identity->userAR->profile->surname]); ?>
				<?= $form->field($modelform, 'phone', ['options' => ['class' => 'hidden']])->input('tel', ['type' => 'hidden', 'placeholder' => 'Мобильный телефон', 'class' => 'input_text phone-mask', 'value' => Yii::$app->user->identity->userAR->profile->phone]); ?>
				<?= $form->field($modelform, 'email', ['options' => ['class' => 'hidden']])->input('email', ['type' => 'hidden', 'placeholder' => 'Электронная почта', 'value' => Yii::$app->user->identity->userAR->email]); ?>
		<?php } ?>

			<?= $form->field($modelform, 'theme')->input('text', ['placeholder' => 'Тема обращения']); ?>
			<?= $form->field($modelform, 'message')->textArea(['placeholder' => "Опишите, пожалуйста, проблемму с которой Вы столкнулись", 'maxlength' => 500, 'class' => 'input_text middle limitedSybmbols']); ?>
			
			<h4 class="lk_step_title mb10">Скриншот</h4>
            <p>Добавьте скриншоты, чтобы мы могли лучше понять ситуацию.</p>
			<?= $form->field($modelform, 'image', ['options' => ['class' => ''], 'template' => '{input}{error}{hint}'])->widget('app\widgets\multiimage\MultiimageWidget', []); ?>

			<?php if (Yii::$app->user->isGuest) { ?>
			<div class="ip_cell w100">
				<?= $form->field($modelform, 'agreements', ['options' => ['class' => 'ip_cell label-on w100']])->widget('app\modules\formagree\widgets\formagree\FormagreeWidget'); ?>
			</div>
			<?php } ?>

			<div class="ip_cell w100 mb0">
				<button class="button">Отправить сообщение</button>
			</div>

		<?php ActiveForm::end(); ?>
		*/ ?>
        </div>

    </section>
<?php /*
<div class="modal" id="fail_modal">
	<div class="modal_content">
		<a href="#" class="modal_close">x</a>
		<div class="success_box">
			<div class="modal_title">Ошибка сохранения данных</div>
			<p><?=\app\helpers\MainHelper::getHelpText()?></p>
			<div class="modal_buttons">
				<a href="#" class="button small close_modal">ОК</a>
			</div>
		</div>
	</div>
	<div class="modal_overlay"></div>
</div>

<?php
$this->registerJsFile('/js/jquery.validate.min.js', ['depends' => [app\assets\AppAsset::class]]);

$url = Url::toRoute(['/pages/supportpage/saveform']);
$regexp_email = '/[^@ \t\r\n]+@[^@ \t\r\n]+\.[^@ \t\r\n]+/';
$regexp_phone = '/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,9}$/';
$js = <<<JS

    function checkValidate() {
        var form = $('form');

		var rules = {
			'SupportForm[name]': {
				required: true 
			},
			'SupportForm[surname]': {
				required: true 
			},
			'SupportForm[email]': {
				required: true,
				email: true 
			},
			'SupportForm[phone]': {
				required: true,
				phone: true 
			},
			'SupportForm[theme]': {
				required: true 
			},
			'SupportForm[message]': {
				required: true 
			},
        };
		//add checkboxes
		let arr=[];
		$('.field-supportform-agreements input[type="checkbox"]').each( (counter,e) => {
			if( m = $(e).prop('id').match(/agreements_(\d+)/i) ) {
				arr.push(m[1]);
			}
		});
		arr.map((id) => {
			rules["SupportForm[agreements]["+id+"]"] = {required: true };
		});

        $.each(form, function () {
            $(this).validate({
                ignore: [],
                errorClass: 'error',
                validClass: 'success',
                errorElement : 'span',
                rules: rules,
                errorPlacement: function(error, element) {
                    var placement = $(element).data('error');
                    if (placement) {
                        $(placement).append(error);
                    } else {
                        error.insertBefore(element);
                    }
                },
                messages: {
                    phone: 'Некорректный номер',
                    email: 'Некорректный e-mail'
                } 
            });
        });
        jQuery.validator.addMethod('email', function (value, element) {
		    return this.optional(element) || {$regexp_email}.test(value);
	  	});
	  	jQuery.validator.addMethod('phone', function (value, element) {
	    	return this.optional(element) || {$regexp_phone}.test(value);
	  	});
    }
    checkValidate();

    $('.label-on .input_text').on('keyup change', function(){
    	if($(this).val() != '') {
    		$(this).addClass('active');
    		$(this).parents('.ip_cell').removeClass('has-error');
    	} else {
    		$(this).removeClass('active');
    	}

    	setTimeout(function(){
    		if($(this).hasClass('success') && !$(this).hasClass('error') || !$(this).is('[required]')) {
	    		$(this).parents('.ip_cell').removeClass('has-error');
	    	} else {
	    		$(this).parents('.ip_cell').addClass('has-error');
	    	}
    	}, 100);
    });

    $('.label-on .ch').on('change', function(){
    	if($(this).is(':checked') || !$(this).is('[required]')) {
    		$(this).parents('.field-supportform-agreements').removeClass('has-error');
    	} else {
    		$(this).parents('.field-supportform-agreements').addClass('has-error');
    	}
    });

	$('#support-form').on('submit', function(e){
    	e.preventDefault();
    	return false;
    });

    $('#support-form').on('beforeSubmit', function(event) {
		let support_form = $('#support-form');
		$(this).valid();
    	$('.label-on .input_text').each(function(){
    		if($(this).hasClass('success') || !$(this).is('[required]')) {
	    		$(this).parents('.ip_cell').removeClass('has-error');
	    	} else {
	    		$(this).parents('.ip_cell').addClass('has-error');
	    	}
    	});
		let agreements_error = false;
    	$('.label-on .ch').each(function(){
    		if($(this).is(':checked')) {
	    		$(this).parents('.field-supportform-agreements').removeClass('has-error');
	    	} else {
				agreements_error = true;
	    	}
    	}).promise().done( function(){ 
			if(agreements_error) {
				$('.field-supportform-agreements').addClass('has-error');
				$('.field-supportform-agreements').find('.help-block').text('Примите условия, чтобы продолжить');
			}

			if(support_form.valid()) {
				$('#support-form button').prop('disabled', true);
				var formData = new FormData($('#support-form')[0]);
				$.ajax({
					type: 'POST',
					url: '{$url}',
					contentType: false,
					processData: false,
					dataType: 'json',
					data: formData,
					success: function(data) {
						if (data.status == 'success') {
							$('#support-form button').prop('disabled', false);
							$('#support-form').hide();
							$('#page-title').text('Ваше сообщение успешно отправлено');
							$('.subheader').text('Ваше обращение принято. В ближайшее время наш специалист ответит вам.');
							ym(67214377,'reachGoal','support');
						} else {
							// показать модалку с ошибкой
							$('#fail_modal .success_box p').html(data.message);
							modalPos('#fail_modal');
							$('#support-form button').removeAttr('disabled');
						}
						$('#support-form button').removeAttr('disabled');
					},
					error: function() {
						$('#support-form button').removeAttr('disabled');
					}
				});
				return false;
			}
		} );
    });

JS;
$this->registerJs($js);

*/
?>