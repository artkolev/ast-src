<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_block">
                <header class="lk_block_header">
                    <h1 class="lk_block_title-big"><?= $model->getNameForView(); ?></h1>
                </header>
                <main class="lk_content">
                    <?= $model->content; ?>
                    <?php $form = ActiveForm::begin([
                            'id' => 'signcontract-form',
                            'action' => '/site/ajaxValidate/',
                            'options' => ['class' => ''],
                            'enableAjaxValidation' => true,
                            'enableClientValidation' => false,
                            'validateOnSubmit' => true,
                            'validateOnChange' => false,
                            'validateOnType' => false,
                            'validateOnBlur' => false,
                            'fieldConfig' => [
                                    'options' => ['class' => 'ip_cell w100'],
                                    'template' => '{label}{input}{error}{hint}',
                                    'inputOptions' => ['class' => 'input_text'],
                                    'labelOptions' => ['class' => 'ip_label'],
                            ],
                    ]); ?>
                    <?php
                    $contracts = ArrayHelper::map($modelform->getContractList(), 'id', 'text_label');
                    if (!empty($contracts)) {
                        echo $form->field($modelform, 'contracts[]', ['template' => '{input}{hint}{error}', 'options' => ['class' => '']])->checkboxList($contracts, ['tag' => 'div', 'item' => function ($index, $label, $name, $checked, $value) {
                            $return = '<input class="ch small ch_politics" type="checkbox" name="' . $name . '" value="' . $value . '" /><label class="notmark">' . $label . '</label><br><br>';
                            return $return;
                        }
                        ]); ?>
                        <div class="ip_cell w100 flex flex-end">
                            <button type="submit" class="button-o lk_button">Принять условия и подписать договор(ы)</a>
                        </div>
                    <?php } ?>
                    <?php ActiveForm::end(); ?>
                </main>
            </div>
            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
        </div>
    </main>


    <div class="modal" id="fail_dogovor_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Подписание договора</div>
                <p>Подписание договора невозможно, обртитесь к администратору</p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
<?php
$url = Url::toRoute(['/pages/isle/signnextcontract/']);
$js = <<<JS
    $('#signcontract-form').on('beforeSubmit', function(event){
        var formData = new FormData($('#signcontract-form')[0]);
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
                    modalPos('#fail_dogovor_modal');
                }
            }
        });
        return false;
    });
    $('#signcontract-form').on('submit', function(e){
        e.preventDefault();
        return false;
    });
JS;
$this->registerJs($js);
?>