<?php

use app\modules\settings\models\Settings;
use app\modules\users\models\UserAR;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var UserAR $user
 */
$has_sertificates = false;
?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <div class="lk_block">
                    <header class="lk_block_header">
                        <h1 class="lk_block_title-big"><?= $model->getNameForView(); ?></h1>
                        <?= $model->content; ?>
                    </header>
                    <main class="lk_content">
                        <?php
                        if (!empty($servicefosform->getFormsresultList($user->id))) {
                            $has_sertificates = true;
                            $form = ActiveForm::begin([
                                    'id' => 'sertificatefos-form',
                                    'action' => '/site/ajaxValidate/',
                                    'options' => ['class' => 'speaker_form'],
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

                            <?= $form->field($servicefosform, 'sert_type', ['template' => '<div class="flex"><div id="sert_type" class="custom_dropdown_box"><a href="#" class="custom_dropdown-link" data-placeholder="Тип сертификата"></a><div class="custom_dropdown-list checkboxes_js mScrollbarCustom simplebar">{input}</div></div></div>{hint}{error}'])->checkboxList($servicefosform->getFormsresultList($user->id), ['item' => function ($index, $label, $name, $checked, $value) {
                                $return = '<div class="custom_dropdown-row"><input class="rd custom_dropdown-choice" type="radio" name="' . $name . '" value="' . $value . '" /><label>' . $label . '</label></div>';
                                return $return;
                            }
                            ]); ?>
                            <?= $form->field($servicefosform, 'secret_word')->input('secret_word', ['id' => 'sert_secret_word', 'class' => 'input_text hidden', 'placeholder' => 'Секретное слово']); ?>
                            <div class="register_step_buttons" style="padding-top: 8pt;">
                                <button type="submit" class="button">Получить</button>
                            </div>
                            <?php ActiveForm::end();
                        } ?>
                        <div class="modal" id="success_fosform_modal">
                            <div class="modal_content">
                                <a href="#" class="modal_close">x</a>
                                <div class="success_box">
                                    <div class="modal_title"></div>
                                    <div class="message"></div>
                                    <div class="modal_buttons">
                                        <a href="#" class="button small close_modal">ОК</a>
                                    </div>
                                </div>

                            </div>
                            <div class="modal_overlay"></div>
                        </div>

                        <?php
                        if (!empty($user->profile->sertificate)) {
                            $has_sertificates = true;
                            foreach ($user->profile->sertificate as $key => $file) { ?>
                                <a href="<?= $user->profile->getFile('sertificate', $key); ?>"
                                   class="link_file"><?= $file->name; ?></a> <br>
                            <?php }
                        }
                        if (!empty($user->getSertificate()->andWhere(['IS NOT', 'sertificate_num', null])->all())) {
                            $has_sertificates = true;
                            foreach ($user->getSertificate()->andWhere(['IS NOT', 'sertificate_num', null])->all() as $sertificate) { ?>
                                <a href="<?= $sertificate->getSertLink(); ?>" class="link_file">Сертификат
                                    №<?= $sertificate->sertificate_num; ?></a> <br>
                            <?php }
                        }
                        if (!empty($user->sertificatefos)) {
                            $has_sertificates = true;
                            foreach ($user->sertificatefos as $sert) { ?>
                                <a href="<?= $sert->getPdfLink(); ?>"
                                   class="link_file"><?= $sert->form->getPublicName(); ?></a> <br>
                            <?php }
                        }
                        if (!empty($tickets_with_sert)) {
                            $has_sertificates = true;
                            foreach ($tickets_with_sert as $ticket) { ?>
                                <a href="<?= $ticket->getSertificateUrl(); ?>" target="_blank"
                                   class="link_file"><?= $ticket->eventName; ?>. <?= $ticket->tarifName; ?>
                                    . <?= $ticket->fio; ?>. <?= $ticket->sertificate_num; ?></a> <br>
                            <?php }
                        } ?>

                        <?php
                        // Нет сертификатов
                        if (!$has_sertificates) {
                            if (Settings::getInfo('lk_no_serts')) {
                                echo Settings::getInfo('lk_no_serts');
                            } else {
                                echo 'На данный момент у вас нет доступных сертификатов';
                            }
                        }
                        ?>

                    </main>
                </div>
            </div>
            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
        </div>
    </main>
<?php
$url = Url::toRoute(['/pages/documents/sertificatefos']);
$url_check_secret = Url::toRoute(['/pages/documents/checksecretword']);
$title =
$js = <<<JS
    $('input[name="SertificateFos[sert_type][]"]').on('click', function(event) {
        var formData = new FormData($('#sertificatefos-form')[0]);
        $.ajax({
			type: 'POST',
			url: '{$url_check_secret}',
			contentType: false,
			processData: false,
			dataType: 'json',
			data: formData,
			success: function(data) {
				if (data.status == 'success') {
					if(data.result=='true'){
                        $('#sert_secret_word').show();
                    }
                    else{
                        $('#sert_secret_word').hide();
                    }
				}
			}
		});
    });

    $('#sertificatefos-form').on('beforeSubmit', function(event) {
		var formData = new FormData($('#sertificatefos-form')[0]);
		$.ajax({
			type: 'POST',
			url: '{$url}',
			contentType: false,
			processData: false,
			dataType: 'json',
			data: formData,
			success: function(data) {
				if (data.status == 'success') {
					location.reload();
				} else {
					// показать ошибку
					$('#success_fosform_modal .success_box .message').html(data.message);
                    $('#success_fosform_modal .success_box .modal_title').html('Запрос на выдачу сертификата');
					modalPos('#success_fosform_modal');
				}
			}
		});
	    return false;
    });
    $('#sertificatefos-form').on('submit', function(e){
    	e.preventDefault();
    	return false;
    });
JS;
$this->registerJs($js);
?>