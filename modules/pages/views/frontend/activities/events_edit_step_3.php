<?php

use app\modules\events\models\Events;
use app\modules\keywords\widgets\KeywordWidget;
use kitsunefet\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerJsFile('/js/lk-events.js', ['depends' => [app\assets\AppAsset::class]]);

/* изображение обложки с учетом модерации */
$image = false;
if ($original && $original->currentModeration && $original->currentModeration->image) {
    $image = $original->currentModeration->image;
} elseif ($original && $original->image) {
    $image = $original->image;
}

/* изображение видео_1 с учетом модерации */
$video1 = false;
if ($original && $original->currentModeration && $original->currentModeration->video1) {
    $video1 = $original->currentModeration->video1;
} elseif ($original && $original->video1) {
    $video1 = $original->video1;
}

/* изображение видео_2 с учетом модерации */
$video2 = false;
if ($original && $original->currentModeration && $original->currentModeration->video2) {
    $video2 = $original->currentModeration->video2;
} elseif ($original && $original->video2) {
    $video2 = $original->video2;
}

$preloaded_report = [];
if ($original) {
    $preloaded_report = $original->report;
    if ($original->currentModeration) {
        $preloaded_report = array_merge($preloaded_report, $original->currentModeration->report);
        if (!empty($original->currentModeration->remove_report)) {
            foreach ($preloaded_report as $key => $image_item) {
                if (in_array($image_item->id, $original->currentModeration->remove_report)) {
                    unset($preloaded_report[$key]);
                }
            }
        }
    }
}

?>

    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <div class="lk_block">
                    <header class="lk_block_header">
                        <h1 class="lk_block_title-big"><?= $original->status == Events::STATUS_NEW ? 'Добавить мероприятие' : 'Редактирование мероприятия'; ?></h1>
                        <div class="lk_block_subtitle"><?= $model->content; ?></div>
                    </header>
                </div>
                <div class="lk-event-reg-steps">
                    <a href="<?= Url::toRoute([$model->getUrlPath(), 'id' => $original->id]); ?>"
                       class="lk-event-reg-step active">
                        <div class="lk-event-reg-step-num"><img src="/img/i_check1-white.svg" alt=""></div>
                        <div class="lk-event-reg-step-name">Описание</div>
                    </a>
                    <a href="<?= Url::toRoute([$model->getUrlPath(), 'step' => 2, 'id' => $original->id]); ?>"
                       class="lk-event-reg-step active">
                        <div class="lk-event-reg-step-num"><img src="/img/i_check1-white.svg" alt=""></div>
                        <div class="lk-event-reg-step-name">Условия</div>
                    </a>
                    <div class="lk-event-reg-step active">
                        <div class="lk-event-reg-step-num">3</div>
                        <div class="lk-event-reg-step-name">О мероприятии</div>
                    </div>
                    <a <?= ($original->status == Events::STATUS_NEW ? '' : 'href="' . Url::toRoute([$model->getUrlPath(), 'step' => 4, 'id' => $original->id]) . '" '); ?>class="lk-event-reg-step">
                        <div class="lk-event-reg-step-num">4</div>
                        <div class="lk-event-reg-step-name">Регистрация</div>
                    </a>
                    <a <?= ($original->status == Events::STATUS_NEW ? '' : 'href="' . Url::toRoute([$model->getUrlPath(), 'step' => 5, 'id' => $original->id]) . '" '); ?>class="lk-event-reg-step">
                        <div class="lk-event-reg-step-num">5</div>
                        <div class="lk-event-reg-step-name">Публикация</div>
                    </a>
                </div>
                <?php $form = ActiveForm::begin([
                        'id' => 'event-form',
                        'action' => '/site/ajaxValidate/',
                        'options' => ['class' => 'marked'],
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
                <div class="lk_block">
                    <main class="lk_content">
                        <?= $form->field($event_model, 'id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
                        <?= $form->field($event_model, 'step', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
                        <h4 class="lk_step_title font20 mt20">Обложка</h4>
                        <p>Пожалуйста, загружайте изображения с рекомендованными параметрами.</p>
                        <?= $form->field($event_model, 'image', ['options' => ['class' => ''], 'template' => '{input}{error}{hint}'])->widget('app\widgets\singleimage\SingleimageWidget', ['preloaded' => $image]); ?>


                        <h4 class="lk_step_title">Добавьте подробное описание мероприятия (до 4000 символов)*</h4>
                        <?= $form->field($event_model, 'content', ['template' => '{input}{error}{hint}'])->widget(CKEditor::class, [
                                'editorOptions' => ElFinder::ckeditorOptions(
                                        'elfinderuser',
                                        array_merge(['editorplaceholder' => 'Добавьте подробное описание мероприятия (до 4000 символов).'], \app\helpers\ckeditor\CKConfig::add_charlimit(\app\helpers\ckeditor\CKConfig::DEFAULT, 4000))
                                ),
                                'containerOptions' => [
                                        'class' => 'editor_container',
                                ],
                        ]); ?>
                        <h4 class="lk_step_title">Укажите правила проведения мероприятия (до 3200 символов)*</h4>
                        <p>Поле обязательно к заполнению Экспертом. Является неотъемлемой частью <a
                                    href="/public_oferta/" target="_blank">публичной оферты между ООО “АСТ-ГРУПП” и
                                Клиентом</a> в п. 1.3, 3.1.<br>Ссылка на правила отображается в билете на мероприятия.
                        </p>
                        <?= $form->field($event_model, 'dop_content', ['template' => '{input}{error}{hint}'])->widget(CKEditor::class, [
                                'editorOptions' => ElFinder::ckeditorOptions(
                                        'elfinderuser',
                                        array_merge(['editorplaceholder' => 'Укажите особенности проведения мероприятия (до 3200 символов).'], \app\helpers\ckeditor\CKConfig::add_charlimit(\app\helpers\ckeditor\CKConfig::DEFAULT, 3200))
                                ),
                                'containerOptions' => [
                                        'class' => 'editor_container',
                                ],
                        ]); ?>

                        <h4 class="lk_step_title mt20">Видео над текстом</h4>
                        <?= $form->field($event_model, 'video1', ['options' => ['class' => ''], 'template' => '{input}{error}{hint}'])->widget('app\widgets\singleimage\SingleimageWidget', ['preloaded' => $video1]); ?>

                        <?= $form->field($event_model, 'video1_name', ['options' => ['class' => 'ip_cell w100 mb50'], 'template' => '{label}<div class="flex no-flex-wrap">{input}' . $event_model->getQuestion('video1_name') . '</div>{error}{hint}'])->textInput([]); ?>
                        <?= $form->field($event_model, 'video1_link', ['options' => ['class' => 'ip_cell w100 mb50'], 'template' => '{label}<div class="flex no-flex-wrap">{input}' . $event_model->getQuestion('video1_link') . '</div>{error}{hint}'])->textInput([]); ?>

                        <h4 class="lk_step_title">Видео после текста</h4>
                        <?= $form->field($event_model, 'video2', ['options' => ['class' => ''], 'template' => '{input}{error}{hint}'])->widget('app\widgets\singleimage\SingleimageWidget', ['preloaded' => $video2]); ?>

                        <?= $form->field($event_model, 'video2_name', ['options' => ['class' => 'ip_cell w100 mb50'], 'template' => '{label}<div class="flex no-flex-wrap">{input}' . $event_model->getQuestion('video2_name') . '</div>{error}{hint}'])->textInput([]); ?>
                        <?= $form->field($event_model, 'video2_link', ['options' => ['class' => 'ip_cell w100 mb50'], 'template' => '{label}<div class="flex no-flex-wrap">{input}' . $event_model->getQuestion('video2_link') . '</div>{error}{hint}'])->textInput([]); ?>
                        <h4 class="lk_step_title mt20">Фото</h4>
                        <p>Рекомендуем размещать фото с уже проведенных услуг/мероприятий, документы/сертификаты
                            подтверждающие авторство и уникальность услуги и т.д.</p>
                        <?= $form->field($event_model, 'report', ['options' => ['class' => '']])->widget('app\widgets\multiimage\MultiimageWidget', ['preloaded' => $preloaded_report]); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb15">Выберите ключевые слова</h4>
                        <p class="mt0 mb20">Выберите 5 ключевых слов из предложенных.<br>Это нужно для более быстрого и
                            удобного поиска программы на сайте.</p>
                        <?= $form->field($event_model, 'keywords', ['template' => '{input}{error}{hint}'])->widget(KeywordWidget::class)->label(''); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb15">Укажите теги</h4>
                        <p class="mt0 mb20">Выберите из предложенных и при необходимости добавьте свои.</p>
                        <?= $form->field($event_model, 'tags')->dropDownList($original->getTagsList(), ['class' => "pretty_tags_ns_max10", 'multiple' => 'multiple']); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content">
                        <div class="ip_cell w100 flex justify-between buttons-wrapper mb0">
                            <a href="<?= Url::toRoute([$model->getUrlPath(), 'step' => 2, 'id' => $event_model->id]); ?>"
                               class="button-o gray medium">Вернуться</a>
                            <button type="submit" class="button blue medium lk">Продолжить</button>
                        </div>
                    </main>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
        </div>
    </main>

    <div class="modal" id="fail_service_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Ошибка редактирования мероприятия</div>
                <p>При сохранении данных возникли ошибки. <?= \app\helpers\MainHelper::getHelpText() ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
<?php
$url = Url::toRoute(['/pages/activities/saveevent', 'step' => 3]);
$js = <<<JS
    $('#event-form').on('beforeSubmit', function(event){
        var formData = new FormData($('#event-form')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    // переадресация на второй шаг
                    window.location.href = data.redirect_to;
                } else {
                    // показать модалку с ошибкой
                    $('#fail_service_modal .modal_title').html('Ошибка редактирования мероприятия');
                    $('#fail_service_modal p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
        return false;
    });
    $('#event-form').on('submit', function(e){
        e.preventDefault();
        return false;
    });
JS;
$this->registerJs($js);
?>