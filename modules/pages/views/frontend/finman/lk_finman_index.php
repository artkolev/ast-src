<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
<main class="sec content_sec section-moderator gray_bg">
    <div class="container wide lk-container">
        <div class="lk_maincol">
            <h1 class="page-title"><?= $model->getNameForView(); ?></h1>

            <div class="finance-manager-wrapper">
                <form id="search_form" autocomplete="off" class="default-input flex w100">
                    <div class="autocomplete ip_cell w100 mr20">
                        <label class="ip_label">Введите номер счета</label>
                        <select class="input_text mr20" id="autocomplete_bill" name="id">
                            <?php if ($bill) { ?>
                                <option value="<?= $bill->id; ?>" selected><?= $bill->payment_id; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <button class="button">Найти</button>
                </form>
                <?php if (!empty($error_messages)) { ?>
                    <div id="error_block" class="finance-manager-info-block">
                        <?php echo implode('<br>', $error_messages); ?>
                    </div>
                <?php } ?>
                <div id="bill_info_container">
                    <?= $this->render('_bill_info', ['bill' => $bill]); ?>
                </div>


                <!-- модалки -->
                <div class="modal" id="finance-manager-date">
                    <div class="modal_content">
                        <a href="#" class="modal_close" data-fancybox-close>x</a>
                        <div class="modal_title">Дата оплаты</div>
                        <?php $form = ActiveForm::begin([
                                'id' => 'billpay-form',
                                'action' => '/site/ajaxValidate/',
                                'options' => ['class' => 'marked'],
                                'enableAjaxValidation' => true,
                                'enableClientValidation' => true,
                                'validateOnSubmit' => true,
                                'validateOnChange' => true,
                                'validateOnType' => false,
                                'validateOnBlur' => true,
                                'fieldConfig' => [
                                        'options' => ['autocomplete' => 'off'],
                                        'template' => '{label}{input}{error}{hint}',
                                        'inputOptions' => ['class' => 'input_text'],
                                        'labelOptions' => ['class' => 'ip_label'],
                                ],
                        ]); ?>
                        <?= $form->field($pay_model, 'id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
                        <?= $form->field($pay_model, 'date')->textInput(['class' => 'input_text datepicker keypress centered']); ?>
                        <br>
                        <div class="ip_cell w100 mb0">
                            <button type="submit" class="button blue big w100">Изменить статус на оплачен</button>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                    <div class="modal_overlay"></div>
                </div>

                <div class="modal" id="finance-manager-choose_postpay">
                    <div class="modal_content">
                        <a href="#" class="modal_close" data-fancybox-close>x</a>
                        <div class="modal_title">Счету №____ будет присвоен признак ПОСТОПЛАТА</div>
                        <div class="ip_cell w100 mb0">
                            <button type="submit" class="button blue big w100" id="choose_postpay_button">ОК</button>
                        </div>
                    </div>
                    <div class="modal_overlay"></div>
                </div>

                <div class="modal" id="finance-manager-date-change">
                    <div class="modal_content">
                        <a href="#" class="modal_close" data-fancybox-close>x</a>
                        <div class="modal_title">Изменить дату оплаты</div>
                        <?php $form = ActiveForm::begin([
                                'id' => 'billdatechange-form',
                                'action' => '/site/ajaxValidate/',
                                'options' => ['class' => 'marked'],
                                'enableAjaxValidation' => true,
                                'enableClientValidation' => true,
                                'validateOnSubmit' => true,
                                'validateOnChange' => true,
                                'validateOnType' => false,
                                'validateOnBlur' => true,
                                'fieldConfig' => [
                                        'options' => ['autocomplete' => 'off'],
                                        'template' => '{label}{input}{error}{hint}',
                                        'inputOptions' => ['class' => 'input_text'],
                                        'labelOptions' => ['class' => 'ip_label'],
                                ],
                        ]); ?>
                        <?= $form->field($date_model, 'id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
                        <div class="ip_cell w100">
                            <label class="ip_label">Дата оплаты</label>
                            <input id="fmbillchangedate-dateold" type="text"
                                   class="input_text datepicker keypress centered" value="" disabled>
                        </div>
                        <?= $form->field($date_model, 'date')->textInput(['class' => 'input_text datepicker keypress centered']); ?>
                        <br>
                        <div class="ip_cell w100 mb0">
                            <button type="submit" class="button blue big w100">Изменить дату оплаты</button>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                    <div class="modal_overlay"></div>
                </div>

                <div class="modal" id="finance-manager-status">
                    <div class="modal_content">
                        <a href="#" class="modal_close" data-fancybox-close>x</a>
                        <div class="modal_title">Изменить статус счета</div>
                        <?php $form = ActiveForm::begin([
                                'id' => 'billstatuschange-form',
                                'action' => '/site/ajaxValidate/',
                                'options' => ['class' => 'marked'],
                                'enableAjaxValidation' => true,
                                'enableClientValidation' => true,
                                'validateOnSubmit' => true,
                                'validateOnChange' => true,
                                'validateOnType' => false,
                                'validateOnBlur' => true,
                                'fieldConfig' => [
                                        'options' => ['autocomplete' => 'off'],
                                        'template' => '{label}{input}{error}{hint}',
                                        'inputOptions' => ['class' => 'input_text'],
                                        'labelOptions' => ['class' => 'ip_label'],
                                ],
                        ]); ?>
                        <?= $form->field($status_model, 'id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
                        <?= $form->field($status_model, 'status', ['template' => '{input}{error}{hint}'])->radioList(
                                $status_model->getStatusList(),
                                [
                                        'item' => function ($index, $label, $name, $checked, $value) {
                                            return '<input type="radio" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked' : '') . ' class="button-o checkbox"><label class="notmark">' . ucwords($label) . '</label>';
                                        },
                                        'class' => 'ip_cell i-f-column align-center w100',
                                ]
                        )->label(false); ?>

                        <div class="ip_cell w100 mb0">
                            <button type="submit" class="button blue big w100">Изменить статус</button>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                    <div class="modal_overlay"></div>
                </div>

                <div class="modal" id="finance-manager-status-postpay">
                    <div class="modal_content">
                        <a href="#" class="modal_close" data-fancybox-close>x</a>
                        <div class="modal_title">Изменить статус счета</div>
                        <?php $form = ActiveForm::begin([
                                'id' => 'billstatuschange-form-postpay',
                                'action' => '/site/ajaxValidate/',
                                'options' => ['class' => 'marked'],
                                'enableAjaxValidation' => true,
                                'enableClientValidation' => true,
                                'validateOnSubmit' => true,
                                'validateOnChange' => true,
                                'validateOnType' => false,
                                'validateOnBlur' => true,
                                'fieldConfig' => [
                                        'options' => ['autocomplete' => 'off'],
                                        'template' => '{label}{input}{error}{hint}',
                                        'inputOptions' => ['class' => 'input_text'],
                                        'labelOptions' => ['class' => 'ip_label'],
                                ],
                        ]); ?>
                        <?= $form->field($status_model, 'id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
                        <?= $form->field($status_model, 'status', ['template' => '{input}{error}{hint}'])->radioList(
                                $status_model->getStatusPostpayList(),
                                [
                                        'item' => function ($index, $label, $name, $checked, $value) {
                                            return '<input type="radio" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked' : '') . ' class="button-o checkbox"><label class="notmark">' . ucwords($label) . '</label>';
                                        },
                                        'class' => 'ip_cell i-f-column align-center w100',
                                ]
                        )->label(false); ?>

                        <div class="ip_cell w100 mb0">
                            <button type="submit" class="button blue big w100">Изменить статус</button>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                    <div class="modal_overlay"></div>
                </div>
                <div class="modal" id="error_modal">
                    <div class="modal_content">
                        <a href="#" class="modal_close" data-fancybox-close>x</a>
                        <div class="success_box">
                            <div class="modal_title">Ошибка</div>
                            <p></p>
                            <div class="modal_buttons">
                                <a href="#" class="button small close_modal" data-fancybox-close>ОК</a>
                            </div>
                        </div>

                    </div>
                    <div class="modal_overlay"></div>
                </div>
            </div>
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>

<?php
$url_search = Url::toRoute(['/pages/finman/billsearch/']);
$url_bill_info = Url::toRoute([$model->getUrlPath()]);
$url_bill_pay = Url::toRoute(['/pages/finman/billpaydate']);
$url_bill_postpay = Url::toRoute(['/pages/finman/billpostpay']);
$url_bill_change_date = Url::toRoute(['/pages/finman/billchangedate']);
$url_bill_change_status = Url::toRoute(['/pages/finman/billchangestatus']);

$js = <<<JS
// поиск по платежу
$('#autocomplete_bill').select2({
	ajax: {
    	delay: 250,
    	dataType: 'json',
    	minimumInputLength: 2,
    	url: '{$url_search}',
    	data: function (params) {
      		var query = {
        		search: params.term,
    		}
		    return query;
    	}
  	},
	language: {
		noResults: function () {
			return 'Ничего не найдено';
		},
		searching: function () {
			return 'Поиск…';
		}
	},
});
$('#autocomplete_bill').change(function(){
	$(this).closest('form').submit();
});

// отправка формы поиска
$('#search_form').submit(function(e){
	e.preventDefault();
	let formData = new FormData(this);
	$.ajax({
		type: 'POST',
		url: '{$url_bill_info}',
		contentType: false,
		processData: false,
		dataType: 'json',
		data: formData,
		success: function(data) {
			if (data.status == 'success') {
				$("#bill_info_container").html(data.html);
			} else {
				$("#bill_info_container").html('');
				$('#error_modal .modal_title').text(data.title);
				$('#error_modal .success_box p').html(data.message);
				$.fancybox.open($('#error_modal'));
			}
		}
	});
});

// оплата счета
$('body').on('click','#open_bill_pay_form',function(e){
	e.preventDefault();
	$('#billpay-form')[0].reset();
	let bill_id = $(this).data('bill');
	let isPostpay = $(this).data('postpay');
	$('#fmbillpay-id').val(bill_id);
    
    if (isPostpay) {
        $('#finance-manager-date button').text('Изменить статус на "Постоплата проведена"')
    } else {
        $('#finance-manager-date button').text('Изменить статус на оплачен')
       
    }
    
	$.fancybox.open($('#finance-manager-date'));
});

$('#billpay-form').on('beforeSubmit', function(event){
    var formData = new FormData($('#billpay-form')[0]);
    $.ajax({
        type: 'POST',
        url: '{$url_bill_pay}',
        contentType: false,
        processData: false,
        dataType: 'json',
        data: formData,
        success: function(data) {
            if (data.status == 'success') {
				$("#bill_info_container").html(data.html);
				$.fancybox.close();
				$('#billpay-form')[0].reset();
			} else {
				$('#error_modal .modal_title').text(data.title);
				$('#error_modal .success_box p').html(data.message);
				$.fancybox.open($('#error_modal'));
			}
        }
    });
    return false;
});
$('#billpay-form').on('submit', function(e){
    e.preventDefault();
    return false;
});

// Перевод на постоплату
$('body').on('click','#open_bill_postpay_form',function(e){
	e.preventDefault();
	let bill_id = $(this).data('bill');
	let payment_id = $(this).data('payment');
    console.log(payment_id)
	$('#fmbillpay-id').val(bill_id);
    $('#finance-manager-choose_postpay .modal_title').text('Счету №' + payment_id + ' будет присвоен признак ПОСТОПЛАТА');
	$.fancybox.open($('#finance-manager-choose_postpay'));
});

$('body').on('click','#choose_postpay_button',function(e){
    $.ajax({
        type: 'POST',
        url: '{$url_bill_postpay}',
        processData: true,
        dataType: 'json',
        data: {
            payment_id: $('#open_bill_postpay_form').data('bill'),
            is_postpay: 1
        },
        success: function(data) {
            if (data.status == 'success') {
				$("#bill_info_container").html(data.html);
				$.fancybox.close();
				$('#billpostpay-form')[0].reset();
			} else {
				$('#error_modal .modal_title').text(data.title);
				$('#error_modal .success_box p').html(data.message);
				$.fancybox.open($('#error_modal'));
			}
        }
    });
    return false;
});


$('body').on('click','#choose_bill_pay_button',function(e){
    $.ajax({
        type: 'POST',
        url: '{$url_bill_postpay}',
        processData: true,
        dataType: 'json',
        data: {
            payment_id: $('#open_bill_pay_form').data('bill'),
            is_postpay: 0
        },
        success: function(data) {
            if (data.status == 'success') {
				$("#bill_info_container").html(data.html);
				$.fancybox.close();
			} else {
				$('#error_modal .modal_title').text(data.title);
				$('#error_modal .success_box p').html(data.message);
				$.fancybox.open($('#error_modal'));
			}
        }
    });
    return false;
});

// смена даты оплаты

$('body').on('click','#open_bill_date_form',function(e) {
	e.preventDefault();
	$('#billdatechange-form')[0].reset();
	let bill_id = $(this).data('bill');
	let bill_date = $(this).data('paydate');
	$('#fmbillchangedate-id').val(bill_id);
	$('#fmbillchangedate-dateold').val(bill_date);
	$.fancybox.open($('#finance-manager-date-change'));
});

$('#billdatechange-form').on('beforeSubmit', function(event){
    var formData = new FormData($('#billdatechange-form')[0]);
    $.ajax({
        type: 'POST',
        url: '{$url_bill_change_date}',
        contentType: false,
        processData: false,
        dataType: 'json',
        data: formData,
        success: function(data) {
            if (data.status == 'success') {
				$("#bill_info_container").html(data.html);
				$.fancybox.close();
				$('#billdatechange-form')[0].reset();
			} else {
				$('#error_modal .modal_title').text(data.title);
				$('#error_modal .success_box p').html(data.message);
				$.fancybox.open($('#error_modal'));
			}
        }
    });
    return false;
});
$('#billdatechange-form').on('submit', function(e){
    e.preventDefault();
    return false;
});

// смена статуса
$('body').on('click','#open_bill_status_form',function(e) {
	e.preventDefault();
	$('#billstatuschange-form')[0].reset();
	$('#billstatuschange-form-postpay')[0].reset();
	let bill_id = $(this).data('bill');
	let isPostpay = $(this).data('postpay');
	let bill_status = $(this).data('status');
	$('#billstatuschange-form #fmbillchangestatus-id').val(bill_id);
	$('#billstatuschange-form-postpay #fmbillchangestatus-id').val(bill_id);
    if (isPostpay) {
	    $.fancybox.open($('#finance-manager-status-postpay'));
    } else {
	    $.fancybox.open($('#finance-manager-status'));
    }
});

$('#billstatuschange-form').on('beforeSubmit', function(event) {
    var formData = new FormData($('#billstatuschange-form')[0]);
    $.ajax({
        type: 'POST',
        url: '{$url_bill_change_status}',
        contentType: false,
        processData: false,
        dataType: 'json',
        data: formData,
        success: function(data) {
            if (data.status == 'success') {
				$("#bill_info_container").html(data.html);
				$.fancybox.close();
				$('#billstatuschange-form')[0].reset();
			} else {
				$('#error_modal .modal_title').text(data.title);
				$('#error_modal .success_box p').html(data.message);
				$.fancybox.open($('#error_modal'));
			}
        }
    });
    return false;
});
$('#billstatuschange-form').on('submit', function(e){
    e.preventDefault();
    return false;
});

$('#billstatuschange-form-postpay').on('beforeSubmit', function(event) {
    var formData = new FormData($('#billstatuschange-form-postpay')[0]);
    $.ajax({
        type: 'POST',
        url: '{$url_bill_change_status}',
        contentType: false,
        processData: false,
        dataType: 'json',
        data: formData,
        success: function(data) {
            if (data.status == 'success') {
				$("#bill_info_container").html(data.html);
				$.fancybox.close();
				$('#billstatuschange-form-postpay')[0].reset();
			} else {
				$('#error_modal .modal_title').text(data.title);
				$('#error_modal .success_box p').html(data.message);
				$.fancybox.open($('#error_modal'));
			}
        }
    });
    return false;
});
$('#billstatuschange-form-postpay').on('submit', function(e){
    e.preventDefault();
    return false;
});

JS;
$this->registerJs($js);
$this->registerCssFile('/css/style-moderator.css', ['depends' => [app\assets\AppAsset::class]]);
?>
