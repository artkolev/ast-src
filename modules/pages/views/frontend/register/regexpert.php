<?php

use app\modules\pages\models\SupportPage;
use app\modules\settings\models\Settings;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
    <section class="sec reg-section">
        <div class="container wide">
            <div class="reg-block">
                <h1 class="page-title"><?= $model->getNameForView(); ?></h1>
                <?= $model->content; ?>
                <?php $form = ActiveForm::begin([
                        'id' => 'regexpert-form',
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
                <?= $form->field($modelform, 'phone')->input('text', ['placeholder' => "+7 000 000-00-00", 'class' => 'input_text phone-mask', 'required' => '']); ?>
                <!-- <div class="input-reg-notice">Если вы вводите телефонный номер другой страны, код подтверждения придет по E-mail</div> -->
                <?= $form->field($modelform, 'email')->input('text', ['placeholder' => "email@mail.ru", 'required' => '']); ?>
                <?= $form->field($modelform, 'surname')->textInput(['placeholder' => "Фамилия", 'required' => '']); ?>
                <?= $form->field($modelform, 'name')->textInput(['placeholder' => "Имя", 'required' => '']); ?>
                <?= $form->field($modelform, 'patronymic')->textInput(['placeholder' => "Отчество"]); ?>
                <?= $form->field($modelform, 'about_myself')->textArea(['placeholder' => "Например, специалист по личностному развитию, доктор экономических наук", 'maxlength' => 255, 'class' => 'input_text small limitedSybmbols', 'required' => '']); ?>
                <?= $form->field($modelform, 'password', ['template' => '{input}{label}<div class="input-status"></div>{error}{hint}<a href="#" class="show_password"></a>'])->passwordInput(['placeholder' => "Пароль", 'required' => '']); ?>
                <?= ''; // $form->field($modelform, 'passwordConfirm',['template' => '{input}{label}<div class="input-status"></div>{error}{hint}<a href="#" class="show_password"></a>'])->passwordInput(['placeholder' => "Подтверждение пароля",'required'=>'']); ?>
                <?= $form->field($modelform, 'agreements', ['template' => '{input}<div class="input-status"></div>{error}{hint}', 'options' => ['class' => 'ip_cell w100']])->widget('app\modules\formagree\widgets\formagree\FormagreeWidget'); ?>
                <div class="ip_cell w100 mb0">
                    <button class="button js-submit-button">Получить код</button>
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
    <div class="modal" id="fail_register_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Ошибка регистрации</div>
                <p><?= Settings::getInfo('register_error'); ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>

        </div>
        <div class="modal_overlay"></div>
    </div>

<?php /* maintenance */
if (Settings::getInfo('maintenance_registration')) { ?>
    <div id="maintenance_box" class="maintenance_box" style="margin:0px; text-align: center;">
        <h3>
            <br>По техническим причинам регистрация временно приостановлена.
            <br>Приносим извинения.
            <br>Пожалуйста, повторите попытку через 1 час.
            <br><a href="/" class="button mt20">Перейти на главную</a>
        </h3>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $.fancybox.open({
                src: '#maintenance_box',
                type: 'inline',
                opts: {
                    modal: true,
                    showCloseButton: false,
                    hideOnOverlayClick: false,
                }
            });
        });
    </script>
<?php } ?>

<?php
$yandex_id = Yii::$app->params['yandex_metric_id'];
$yandex_code = '';
if (Yii::$app->params['is_original_server']) {
    $yandex_code = 'ym(' . $yandex_id . ',"reachGoal",data.send_target);';
}
$url = Url::toRoute(['/pages/register/saveexpert']);
$js = <<<JS
    $('#regexpert-form').on('beforeSubmit', function(event){
        $('#regexpert-form .js-submit-button').prop('disabled', true);
            var formData = new FormData($('#regexpert-form')[0]);
            ajaxStart = false;
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
                            {$yandex_code};
                        }
                        // переадресация на страницу успешной регистрации Эксперта
                        window.location.href = data.redirect_to;
                    } else {
                        // показать модалку с ошибкой
                        modalPos('#fail_register_modal');
                    $('#regexpert-form .js-submit-button').prop('disabled', false);
                    }
                },
                error: function() {
				$('#regexpert-form .js-submit-button').prop('disabled', false);
				}
            });
	    return false;
    });
    $('#regexpert-form').on('submit', function(e){
    	e.preventDefault();
    	return false;
    });

    $('#regexpert-form').on('beforeValidate', function(event){
        $('#regexpert-form .js-submit-button').prop('disabled', true);
    });

    $('#regexpert-form').on('afterValidate', function(event, messages, errorAttributes){
        if (errorAttributes.length > 0) {
            $('#regexpert-form .js-submit-button').prop('disabled', false);
        }
    });
JS;
$this->registerJs($js);
?>