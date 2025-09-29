<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
<main class="sec content_sec section-moderator gray_bg">
    <div class="container wide lk-container">
        <div class="lk_maincol">
            <h1 class="page-title"><?= $model->getNameForView(); ?></h1>
            <?php $form = ActiveForm::begin([
                    'id' => 'billexport-form',
                    'action' => '/site/ajaxValidate/',
                    'options' => ['class' => 'marked'],
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => true,
                    'validateOnSubmit' => true,
                    'validateOnChange' => false,
                    'validateOnType' => false,
                    'validateOnBlur' => false,
                    'fieldConfig' => [
                            'template' => '{label}{input}{error}{hint}',
                            'inputOptions' => ['class' => 'input_text'],
                            'labelOptions' => ['class' => 'ip_label'],
                            'options' => ['class' => 'ip_cell default-input mr20'],
                    ],
            ]); ?>
            <div class="finance-manager-wrapper">
                <div class="ip_cell w100 two-inputs">
                    <?= $form->field($modelform, 'date_start')->textInput(['class' => 'input_text tleft date-range1']); ?>
                    <?= $form->field($modelform, 'date_stop')->textInput(['class' => 'input_text tleft date-range2']); ?>
                    <button class="button">Выгрузить</button>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
            <div id="loader" style="display:none;">Идет формирование списка счетов</div>
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>

<?php
$url_bill_export = Url::toRoute(['/pages/finman/export/']);

$js = <<<JS
let date = new Date();
let todaysDate = date.toLocaleDateString('ru-ru');
$('.two-inputs').dateRangePicker({
    singleMonth: false,
    startOfWeek: 'monday',
    endDate: todaysDate,
    format: 'DD.MM.YYYY',
    language: 'ru',
    separator : ' по ',
    autoClose: true,
    getValue: function()
    {
        if ($(this).find('.date-range1').val() && $(this).find('.date-range2').val() )
            return $(this).find('.date-range1').val() + ' по ' + $(this).find('.date-range2').val();
        else
            return '';
    },
    setValue: function(s,s1,s2)
    {
        $(this).find('.date-range1').val(s1);
        $(this).find('.date-range2').val(s2);
    }
});

$('#billexport-form').on('beforeSubmit', function(event){
    var formData = new FormData($('#billexport-form')[0]);
    $('#loader').css('display','block');
    $.ajax({
        type: 'POST',
        url: '{$url_bill_export}',
        contentType: false,
        processData: false,
        xhrFields: {
			'responseType': 'blob'
		},
        data: formData,
        success: function(data, status, xhr) {
			var blob = new Blob([data], {type: xhr.getResponseHeader('Content-Type')});
			var link = document.createElement('a');
			link.href = window.URL.createObjectURL(blob);
			link.download = 'export.xlsx';
			link.click();
			$('#loader').css('display','none');
		}
    });
    return false;
});
$('#billexport-form').on('submit', function(e){
    e.preventDefault();
    return false;
});

JS;
$this->registerJs($js);
$this->registerCssFile('/css/style-moderator.css', ['depends' => [app\assets\AppAsset::class]]);
?>
