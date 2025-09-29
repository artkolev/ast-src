<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <div class="lk_block">
                    <header class="lk_block_header">
                        <h1 class="lk_block_title-big"><?= $model->getNameForView(); ?></h1>
                        <?= $model->content; ?>
                        <?= $model->urusr_content; ?>
                    </header>
                    <main class="lk_content">
                        <?php $form = ActiveForm::begin([
                                'id' => 'profileavatar-form',
                                'action' => '/site/ajaxValidate/',
                                'options' => ['class' => 'avatar_form'],
                                'enableAjaxValidation' => true,
                                'enableClientValidation' => true,
                                'validateOnSubmit' => true,
                                'validateOnChange' => true,
                                'validateOnType' => false,
                                'validateOnBlur' => true,
                                'fieldConfig' => [
                                        'options' => ['class' => 'ip_cell w100'],
                                        'template' => '{label}{input}{error}{hint}',
                                        'inputOptions' => ['class' => 'input_text'],
                                        'labelOptions' => ['class' => 'ip_label'],
                                ],
                        ]); ?>
                        <div class="round_avatar lk_avatar_upload_box uploadContainer added need-crop <?= (!empty($is_moderated)) ? 'moderated' : ''; ?>"
                             data-aspectRatioX="1" data-aspectRatioY="1">
                            <a href="#" class="remove-photo"><i class="fa fa-remove"></i></a>
                            <div class="lk_avatar_upload_img dropzone_local simulateAvatarUpload">
                                <?php if (!empty($is_moderated)) { ?>
                                    <img src="<?= $is_moderated->getThumb('image', 'profile'); ?>" class="preview-photo"
                                         alt="">
                                <?php } else { ?>
                                    <img src="<?= Yii::$app->user->identity->userAR->profile->getThumb('image', 'profile'); ?>"
                                         class="preview-photo" alt="">
                                <?php } ?>
                                <button type="button" class="button blue small crop_button js_upload_avatar_moderation"
                                        style="display: none;">Отправить на модерацию
                                </button>
                            </div>
                            <div class="lk_avatar_upload">
                                <a href="#" class="button-o small upload_button simulateAvatarUpload">Загрузить
                                    аватар</a>
                                <?= $form->field($avatarform, 'image', ['template' => '{input}{error}{hint}'])->fileInput(['class' => "image_upload", 'accept' => '.jpg, .jpeg, .png']); ?>
                                <p>
                                    Формат: jpg, png. <br>
                                    Максимальный вес: 1 Mb <br>
                                    Рекомендованный размер: 500х500 px
                                </p>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>
                        <?php $form = ActiveForm::begin([
                                'id' => 'profilefiz-form',
                                'action' => '/site/ajaxValidate/',
                                'options' => ['class' => 'register_form marked'],
                                'enableAjaxValidation' => true,
                                'enableClientValidation' => true,
                                'validateOnSubmit' => true,
                                'validateOnChange' => true,
                                'validateOnType' => false,
                                'validateOnBlur' => true,
                                'fieldConfig' => [
                                        'options' => ['class' => 'ip_cell w100'],
                                        'template' => '{label}{input}{error}{hint}',
                                        'inputOptions' => ['class' => 'input_text'],
                                        'labelOptions' => ['class' => 'ip_label'],
                                ],
                        ]); ?>

                        <?= $form->field($modelform, 'organization_name', ['options' => ['class' => 'ip_cell w100 lock-on'], 'template' => '{label}<div class="flex">{input}' . $modelform->getQuestion('organization_name') . '<div class="pencil-field field-off"></div></div>{error}{hint}'])->textInput(['placeholder' => "Наименование организации*"]); ?>
                        <?= $form->field($modelform, 'inn', ['options' => ['class' => 'ip_cell w100 lock-on'], 'template' => '{label}<div class="flex">{input}' . $modelform->getQuestion('organization_name') . '<div class="pencil-field field-off"></div></div>{error}{hint}'])->textInput(['placeholder' => "ИНН*", 'maxlength' => 12]); ?>
                        <?= $form->field($modelform, 'office', ['options' => ['class' => 'ip_cell w100 lock-on'], 'template' => '{label}{input}{error}<div class="pencil-field field-off"></div>{hint}'])->textInput(['placeholder' => "Должность*"]); ?>
                        <?= $form->field($modelform, 'surname', ['options' => ['class' => 'ip_cell w100 lock-on'], 'template' => '{label}{input}{error}<div class="pencil-field field-off"></div>{hint}'])->textInput(['placeholder' => "Фамилия*"]); ?>
                        <?= $form->field($modelform, 'name', ['options' => ['class' => 'ip_cell w100 lock-on'], 'template' => '{label}{input}{error}<div class="pencil-field field-off"></div>{hint}'])->textInput(['placeholder' => "Имя*"]); ?>
                        <?= $form->field($modelform, 'patronymic', ['options' => ['class' => 'ip_cell w100 lock-on'], 'template' => '{label}{input}{error}<div class="pencil-field field-off"></div>{hint}'])->textInput(['placeholder' => "Отчество"]); ?>
                        <?= $form->field($modelform, 'phone', ['options' => ['class' => 'ip_cell w100 lock-on'], 'template' => '{label}{input}{error}<div class="pencil-field field-off"></div>{hint}'])->input('tel', ['placeholder' => "+7 (000) 000-00-00", 'class' => 'input_text phone-mask']); ?>
                        <?php if (!empty($modelform->getQuestion('email'))) {
                            echo $form->field($modelform, 'email', ['options' => ['class' => 'ip_cell w100 lock-on'], 'template' => '{label}<div class="flex">{input}' . $modelform->getQuestion('email') . '<div class="pencil-field field-off"></div></div>{error}{hint}'])->input('email', ['placeholder' => "my@mail.ru"]);
                        } else {
                            echo $form->field($modelform, 'email', ['options' => ['class' => 'ip_cell w100 lock-on'], 'template' => '{label}{input}{error}<div class="pencil-field field-off"></div>{hint}'])->input('email', ['placeholder' => "my@mail.ru"]);
                        } ?>
                        <?= $form->field($modelform, 'city_id')->dropDownList($modelform->getCityList(), ['class' => "pretty_select"]); ?>

                        <div class="ip_cell w100">
                            <button class="button-o lk_button_submit" type="submit">Изменить</button>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </main>
                </div>
            </div>
            <?= \app\modules\users\widgets\profile\UrmenuWidget::widget(); ?>
        </div>
    </main>
    <div class="modal" id="fail_profile_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Ошибка сохранения данных</div>
                <p><?= \app\helpers\MainHelper::getHelpText() ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
    <div class="modal" id="success_profile_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Изменение профиля</div>
                <p>Профиль успешно изменён</p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
<?php
$url = Url::toRoute(['/pages/profile/savefizusr']);
$url_avatar = Url::toRoute(['/pages/profile/saveavatar']);
$url_rem = Url::toRoute(['/pages/profile/clearavatar']);
$js = <<<JS
    $('.js_upload_avatar_moderation').click(function(){
        $(this).prop('disabled', true);
        $(this).closest('form').submit();
    })
    $('#profileavatar-form').on('beforeSubmit', function(event){
        var formData = new FormData($('#profileavatar-form')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url_avatar}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    $('.lk_avatar_upload_box').addClass('moderated');
                    $('#success_profile_modal .success_box p').html(data.message);
                    modalPos('#success_profile_modal');
                    setTimeout(function () {
                        closeModal('#success_profile_modal'); // закрываем модалку
                    }, 2000);
                } else {
                    // показать модалку с ошибкой
                    $('#fail_profile_modal .success_box p').html(data.message);
                    modalPos('#fail_profile_modal');
                }
                $('.js_upload_avatar_moderation').prop('disabled', false);
            },
            error: function() {
                $('.js_upload_avatar_moderation').prop('disabled', false);
            }
        });
        return false;
    });
    $('#profileavatar-form').on('submit', function(e){
        e.preventDefault();
        return false;
    });

    $('#profilefiz-form').on('beforeSubmit', function(event){
        var formData = new FormData($('#profilefiz-form')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    $('#success_profile_modal .success_box p').html(data.message);
                    modalPos('#success_profile_modal');
                } else {
                    // показать модалку с ошибкой
                    $('#fail_profile_modal .success_box p').html(data.message);
                    modalPos('#fail_profile_modal');
                }
            }
        });
        return false;
    });
    $('#profilefiz-form').on('submit', function(e){
        e.preventDefault();
        return false;
    });

    $('.remove-photo').click(function(){
        $.ajax({
            type: 'GET',
            url: '{$url_rem}',
            processData: true,
            dataType: 'json',
            data: {action:'remove'},
            success: function(data){
                // do nothing
            }
        });

    });

    $('.pencil-field').click(function(e) {
        $(this).toggleClass('field-off');
        $(this).parents('.ip_cell').toggleClass('lock-on');
    });
JS;
$this->registerJs($js);
?>