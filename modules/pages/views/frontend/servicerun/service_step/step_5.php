<?php

use app\models\ServiceForm;
use app\modules\service\models\Service;
use yii\widgets\ActiveForm;

/**
 * @var \yii\web\View $this
 * @var ServiceForm $modelform
 * @var Service $original
 * @var ActiveForm $form
 */

$preloaded_image = [];

if ($original) {
    $preloaded_image = $original->image;
    if ($original->currentModeration) {
        $preloaded_image = array_merge($preloaded_image, $original->currentModeration->image);
        if (!empty($original->currentModeration->remove_image)) {
            foreach ($preloaded_image as $key => $image_item) {
                if (in_array($image_item->id, $original->currentModeration->remove_image)) {
                    unset($preloaded_image[$key]);
                }
            }
        }
    }
}

$videoimage = false;
if ($original && $original->currentModeration && $original->currentModeration->videoimage) {
    $videoimage = $original->currentModeration->videoimage;
} elseif ($original && $original->videoimage) {
    $videoimage = $original->videoimage;
}
?>

<?= $form->field($modelform, 'id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
<?= $form->field($modelform, 'step', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>

<div class="lk_block">
    <main class="lk_content">
        <h4 class="lk_step_title mt20">Фотогалерея</h4>

        <!--        <div class="ip_cell label-on w100">
                    <div class="flex">
                        <input type="text" class="input_text" name="form" placeholder="Название фотогалереи">
                        <label class="ip_label">Добавить поле !</label>
                        <div class="question_box">
                            <a href="javascript:void(0)" class="question_icon">?</a>
                            <div class="question_text">Заголовок, который будет над добавленными фотографиями. Если поле оставить пустым, то заголовка не будет.</div>
                        </div>
                        <div class="input-status"></div>
                    </div>
                    <div class="help-block"></div>
                </div>-->

        <p>Рекомендуем размещать фото с уже проведенных услуг/мероприятий, документы/сертификаты подстверждающие
            авторство и уникальность услуги и т.д.</p>
        <p>Изображение в тексте</p>
        <?= $form->field($modelform, 'image', ['options' => ['class' => '']])->widget('app\widgets\multiimage\MultiimageWidget', ['preloaded' => $preloaded_image]); ?>

    </main>
</div>

<div class="lk_block">
    <main class="lk_content">
        <h4 class="lk_step_title mt20">Видеопрезентация услуги</h4>
        <p>Отображается под обложкой услуги над описанием. Рекомендуем размещать демо/промо ролик, видеопрезентацию
            услуги или эксперта.</p>

        <!--<div class="ip_cell w100">
            <label class="ip_label">Название видео</label>
            <input type="text" class="input_text" placeholder="Добавить поле !">
        </div>-->

        <p>Ссылка на видео</p>
        <?= $form->field($modelform, 'video', ['template' => '{input}{error}{hint}'])->textInput(['placeholder' => 'Ссылка на видео Youtube, VK, Rutube',]); ?>

        <h4 class="lk_step_title mt20">Заставка для видео</h4>
        <p>Если заставка не добавлена, то будет использована обложка видео, добавленная в youtube</p>
        <?= $form->field($modelform, 'videoimage', ['options' => ['class' => ''], 'template' => '{input}{error}{hint}'])->widget('app\widgets\singleimage\SingleimageWidget', ['preloaded' => $videoimage]); ?>

    </main>
</div>

<!--<div class="lk_block">
    <main class="lk_content">
        <h4 class="lk_step_title font20 mb10">Видеогалерея - Добавить блок целиком</h4>
        <p class="mt0 mb20">Галерея с видео, которые будут в конце текста описания об услуге.</p>
        <div class="ip_cell label-on w100">
            <div class="flex">
                <input type="text" class="input_text" name="form" placeholder="Название галереи с видео">
                <label class="ip_label">Название галереи с видео</label>
                <div class="question_box">
                    <a href="javascript:void(0)" class="question_icon">?</a>
                    <div class="question_text">Заголовок, который будет над добавленными видео. Если поле оставить пустым, то заголовка не будет.</div>
                </div>
                <div class="input-status"></div>
            </div>
            <div class="help-block"></div>
        </div>
        элементы галереи добавляются в js-videogallery-list
        <div class="drag-list js-videogallery-list js-dragndrop-list ui-sortable">

            <div class="drag-element drag-element-mobile js-dragndrop-element" data-sort="0" style="position: relative; left: 0px; top: 0px;">
                <div class="drag-element-infos">
                    <div class="drag-element-info drag-element-name">Название видео</div>
                </div>
                <div class="drag-burger drag-burger-element ui-sortable-handle"></div>
                <div class="edit-element js-edit-video"></div>
                <div class="remove-element js-remove-video"></div>
            </div><div class="drag-element drag-element-mobile js-dragndrop-element" data-sort="1" style="position: relative; left: 0px; top: 0px;">
                <div class="drag-element-infos">
                    <div class="drag-element-info drag-element-name">Название видео</div>
                </div>
                <div class="drag-burger drag-burger-element ui-sortable-handle"></div>
                <div class="edit-element js-edit-video"></div>
                <div class="remove-element js-remove-video"></div>
            </div>
            <div class="drag-element drag-element-mobile js-dragndrop-element" data-sort="2">        <div class="drag-element-infos">            <div class="drag-element-info drag-element-name">Название видео видео видеовидео</div>        </div>        <div class="drag-burger drag-burger-element ui-sortable-handle"></div>        <div class="edit-element js-edit-video"></div>        <div class="remove-element js-remove-video"></div>    </div></div>
        <div class="ip_cell w100 mb0">
            <button class="button blue medium lk video_in_gallery">Добавить видео</button>
        </div>
    </main>
</div>-->
