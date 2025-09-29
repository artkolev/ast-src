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
                    </header>
                    <main class="lk_content">
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
                        <?= $form->field($modelform, 'url_to_site', ['template' => '{label}<div class="flex">{input}' . $modelform->getQuestion('url_to_site') . '</div>{error}{hint}'])->input('text', ['placeholder' => "Адрес сайта"]); ?>
                        <?= $form->field($modelform, 'url_to_vk', ['options' => ['class' => 'ip_cell social_url'], 'template' => '<span class="in_circle"><i class="fa fa-vk"></i></span><div class="flex">{input}' . $modelform->getQuestion('url_to_vk') . '</div>{error}{hint}'])->input('text', ['placeholder' => "Ссылка на страницу в социальной сети"]); ?>
                        <?= ''; // $form->field($modelform, 'url_to_fb',['options'=>['class'=>'ip_cell social_url'], 'template' => '<span class="in_circle"><i class="fa fa-facebook"></i></span><div class="flex">{input}'.$modelform->getQuestion('url_to_fb').'</div>{error}{hint}'])->input(['placeholder' => "Ссылка на страницу в социальной сети"]); ?>
                        <?= ''; // $form->field($modelform, 'url_to_insta',['options'=>['class'=>'ip_cell social_url'], 'template' => '<span class="in_circle"><i class="fa fa-instagram"></i></span><div class="flex">{input}'.$modelform->getQuestion('url_to_insta').'</div>{error}{hint}'])->input(['placeholder' => "Ссылка на страницу в социальной сети"]); ?>
                        <?= $form->field($modelform, 'url_to_dzen', ['options' => ['class' => 'ip_cell social_url'], 'template' => '<span class="in_circle"><i class="fa fa-zen"></i></span><div class="flex">{input}' . $modelform->getQuestion('url_to_dzen') . '</div>{error}{hint}'])->input('text', ['placeholder' => "Ссылка на страницу в социальной сети"]); ?>
                        <?= $form->field($modelform, 'url_to_twitter', ['options' => ['class' => 'ip_cell social_url'], 'template' => '<span class="in_circle"><i class="fa fa-twitter"></i></span><div class="flex">{input}' . $modelform->getQuestion('url_to_twitter') . '</div>{error}{hint}'])->input('text', ['placeholder' => "Ссылка на страницу в социальной сети"]); ?>
                        <?= $form->field($modelform, 'url_to_youtube', ['options' => ['class' => 'ip_cell social_url'], 'template' => '<span class="in_circle"><i class="fa fa-youtube-play"></i></span><div class="flex">{input}' . $modelform->getQuestion('url_to_youtube') . '</div>{error}{hint}'])->input('text', ['placeholder' => "Ссылка на страницу в социальной сети"]); ?>
                        <?= $form->field($modelform, 'url_to_telegram', ['options' => ['class' => 'ip_cell social_url'], 'template' => '<span class="in_circle"><i class="fa fa-send"></i></span><div class="flex">{input}' . $modelform->getQuestion('url_to_telegram') . '</div>{error}{hint}'])->input('text', ['placeholder' => "Ссылка на страницу в социальной сети"]); ?>


                        <?= $form->field($modelform, 'photos_link', ['template' => '{label}<div class="flex">{input}' . $modelform->getQuestion('photos_link') . '</div>{error}{hint}'])->input('url', ['placeholder' => "Ссылка на фотографии организации"]); ?>
                        <?= $form->field($modelform, 'docs_link', ['template' => '{label}<div class="flex">{input}' . $modelform->getQuestion('docs_link') . '</div>{error}{hint}'])->input('url', ['placeholder' => "Ссылка на документы организации"]); ?>

                        <?= $form->field($modelform, 'video_links', ['template' => '{label}<div class="flex">{input}' . $modelform->getQuestion('video_links') . '</div>{error}{hint}'])->dropDownList($modelform->video_links, ['class' => "pretty_tags", 'multiple' => 'multiple', 'data-placeholder' => 'Ссылки на видео организации']); ?>
                        <?= $form->field($modelform, 'publications', ['template' => '{label}<div class="flex">{input}' . $modelform->getQuestion('publications') . '</div>{error}{hint}'])->dropDownList($modelform->publications, ['class' => "pretty_tags", 'multiple' => 'multiple', 'data-placeholder' => 'Ссылки на публикации организации']); ?>

                        <div class="ip_cell w100">
                            <button class="button-o lk_button_submit" type="submit">Сохранить</button>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </main>
                </div>

            </div>
            <?= \app\modules\users\widgets\profile\ExporgmenuWidget::widget(); ?>
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
$url = Url::toRoute(['/pages/profile/savecontacts']);
$js = <<<JS
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
                    setTimeout(function () {
                        closeModal('#success_profile_modal'); // закрываем модалку
                    }, 2000);
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
JS;
$this->registerJs($js);
?>