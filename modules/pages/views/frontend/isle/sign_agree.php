<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
    <section class="sec content_sec">
        <div class="container">
            <section class="register_form_success">
                <div class="ib">
                    <img src="/img/envelope_big.svg" alt=""/>
                    <h1><?= $model->getNameForView(); ?></h1>
                    <div style="text-align:left;"><?= $model->content; ?></div>
                    <?php if ($user->greeting) { ?>
                        <div style="text-align:left;"><?= $model->expert_text; ?></div>
                    <?php } ?>
                    <?php if (!empty($modelform->getAgrees())) {
                        $form = ActiveForm::begin([
                                'id' => 'sign-form',
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
                        <div class="mb20" style="text-align:left;">
                            <?= $form->field($modelform, 'agreements', ['options' => ['class' => 'ip_cell w100']])->widget('app\modules\formagree\widgets\formagree\FormagreeWidget'); ?>
                        </div>
                        <button type="submit" class="button small">Подтвердить</button>
                        <?php ActiveForm::end();
                    } else { ?>
                        <a href="<?= Url::toRoute([$profile_page->getUrlPath(), 'greeting' => 'OK']); ?>"
                           class="button lk">ОК</a>
                    <?php } ?>
                </div>
            </section>
        </div>
    </section>

    <div class="modal" id="fail_reset_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Ошибка сохранения данных</div>
                <p>Возникли технические проблемы. <?= \app\helpers\MainHelper::getHelpText() ?> или повторите попытку
                    позже</p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>

<?php
$js = <<<JS
    $('#sign-form').on('beforeSubmit', function(event){
        var formData = new FormData($('#sign-form')[0]);
        $.ajax({
            type: 'POST',
            url: '/site/ajaxSave/',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    // переадресация
                    window.location.reload();
                } else {
                    // показать модалку с ошибкой
                    modalPos('#fail_reset_modal');
                }
            }
        });
        return false;
    });
    $('#sign-form').on('submit', function(e){
        e.preventDefault();
        return false;
    });
JS;
$this->registerJs($js);
?>