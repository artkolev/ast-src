<?php
/*
    @descr Третий шаг создания/редактирования программы ДПО
    @var $model Class app\modules\pages\models\LKEduprogEdit; текущая страница
    @action pages/eduprog/eduprogedit
*/

use app\modules\eduprog\models\Eduprog;
use app\modules\keywords\widgets\KeywordWidget;
use app\modules\users\models\UserAR;
use kitsunefet\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerJsFile('/js/jquery-ui.min.js', ['depends' => [app\assets\AppAsset::class]]);
$this->registerJsFile('/js/jquery.ui.touch-punch.min.js', ['depends' => [app\assets\AppAsset::class]]);
$this->registerJsFile('/js/lk-events.js', ['depends' => [app\assets\AppAsset::class]]);
$this->registerCssFile('/css/style-blog.css', ['depends' => [app\assets\AppAsset::class]]);

/* изображение обложки с учетом модерации */
$image = false;
if ($original && $original->currentModeration && $original->currentModeration->image) {
    $image = $original->currentModeration->image;
} elseif ($original && $original->image) {
    $image = $original->image;
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
                        <h1 class="lk_block_title-big"><?= (empty($original) or ($original->status == Eduprog::STATUS_NEW)) ? 'Добавить программу ДПО' : 'Редактирование программы ДПО'; ?></h1>
                        <div class="lk_block_subtitle">
                            <?= $model->content; ?>
                            <br>
                            <b><?= $eduprog_model->name ?></b>
                        </div>
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
                        <div class="lk-event-reg-step-name">О программе</div>
                    </div>
                    <a <?= ((empty($original) or ($original->status == Eduprog::STATUS_NEW)) ? '' : 'href="' . Url::toRoute([$model->getUrlPath(), 'step' => 4, 'id' => $original->id]) . '" '); ?>class="lk-event-reg-step">
                        <div class="lk-event-reg-step-num">4</div>
                        <div class="lk-event-reg-step-name">Регистрация<br> и тарифы</div>
                    </a>
                    <a <?= ((empty($original) or ($original->status == Eduprog::STATUS_NEW)) ? '' : 'href="' . Url::toRoute([$model->getUrlPath(), 'step' => 5, 'id' => $original->id]) . '" '); ?>class="lk-event-reg-step">
                        <div class="lk-event-reg-step-num">5</div>
                        <div class="lk-event-reg-step-name">Письмо</div>
                    </a>
                    <a <?= ((empty($original) or ($original->status == Eduprog::STATUS_NEW)) ? '' : 'href="' . Url::toRoute([$model->getUrlPath(), 'step' => 6, 'id' => $original->id]) . '" '); ?>class="lk-event-reg-step">
                        <div class="lk-event-reg-step-num">6</div>
                        <div class="lk-event-reg-step-name">Оферта<br> и публикация</div>
                    </a>
                </div>

                <?php $form = ActiveForm::begin([
                        'id' => 'eduprog-form',
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
                <?= $form->field($eduprog_model, 'id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
                <?= $form->field($eduprog_model, 'step', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mt20">Обложка</h4>
                        <p>Пожалуйста, загружайте изображения с рекомендованными параметрами.</p>
                        <?= $form->field($eduprog_model, 'image', ['options' => ['class' => ''], 'template' => '{input}{error}{hint}'])->widget('app\widgets\singleimage\SingleimageWidget', ['preloaded' => $image]); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20">О программе</h4>
                        <?= $form->field($eduprog_model, 'content', ['template' => '{input}{error}{hint}'])->widget(CKEditor::class, [
                                'editorOptions' => ElFinder::ckeditorOptions(
                                        'elfinderuser',
                                        array_merge(['editorplaceholder' => "Подробнее о предмете обучения; почему данная тематика актуальна в данное время."], \app\helpers\ckeditor\CKConfig::add_charlimit(\app\helpers\ckeditor\CKConfig::DEFAULT, 2000))
                                ),
                                'containerOptions' => [
                                        'class' => 'editor_container',
                                ],
                        ]); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb10">Видеогалерея</h4>
                        <p class="mt0 mb20">Галерея с видео, которые будут в конце текста описания о программе.</p>
                        <?= $form->field($eduprog_model, 'video_title', ['options' => ['class' => 'ip_cell label-on w100'], 'template' => '<div class="flex">{input}{label}' . $eduprog_model->getQuestion('video_title') . '<div class="input-status"></div></div>{error}{hint}'])->textInput(['placeholder' => "Название галереи с видео"]); ?>

                        <div id="video_list" class="drag-list js-videogallery-list js-dragndrop-list">
                            <?php
                            $key_max_video = 0;
                            if (!empty($eduprog_model->video)) {
                                foreach ($eduprog_model->video as $key_last => $videorow) {
                                    if ($key_max_video < $key_last) {
                                        $key_max_video = $key_last;
                                    }
                                    // если есть картинка в модерации - отображаем её, если нет - из оригинальной программы.
                                    // еще нужно учесть список к удалению х_х
                                    $videoimage = false;
                                    if ($original && $original->currentModeration) {
                                        // пробуем достать иображение из модерации
                                        if (!empty($videorow['image'])) {
                                            $videoimage = $original->currentModeration->getThumb('videoimage', 'main', false, $videorow['image'][0]);
                                        }
                                    }
                                    if (empty($videoimage)) {
                                        if (!empty($videorow['image'])) {
                                            $videoimage = $original->getThumb('videoimage', 'main', false, $videorow['image'][0]);
                                        }
                                    } ?>

                                    <div id="video_<?= $key_last; ?>"
                                         class="drag-element drag-element-mobile js-dragndrop-element"
                                         data-sort="<?= $key_last; ?>">
                                        <?php
                                        // системные поля, присутствуют всегда
                                        echo $form->field($eduprog_model, 'video[' . $key_last . '][order]', ['template' => '{input}{error}{hint}', 'options' => ['style' => 'display:none;']])->hiddenInput(['class' => 'order_field']);
                                        echo $form->field($eduprog_model, 'video[' . $key_last . '][visible]', ['template' => '{input}{error}{hint}', 'options' => ['style' => 'display:none;']])->hiddenInput(['class' => 'visible_field']);
                                        // холдер для загруженных изображений
                                        // нужно скрытое поле, способное хранить массив данных
                                        $data_ids = array_combine($eduprog_model->video[$key_last]['image'], $eduprog_model->video[$key_last]['image']);
                                        echo $form->field($eduprog_model, 'video[' . $key_last . '][image]', ['template' => '{input}{error}{hint}', 'options' => ['style' => 'display:none;']])->dropDownList($data_ids, ['multiple' => 'multiple']);

                                        ?>
                                        <div class="drag-element-infos">
                                            <div id="video_name_text_<?= $key_last; ?>"
                                                 class="drag-element-info drag-element-name"><?= $eduprog_model->video[$key_last]['name']; ?></div>
                                            <?php echo $form->field($eduprog_model, 'videoimage_loader[' . $key_last . '][]', ['template' => '{error}{hint}'])->hiddenInput(); ?>
                                        </div>
                                        <div class="drag-burger drag-burger-element"></div>
                                        <div class="edit-element js-edit-video"
                                             data-fancyelement="#video_modal_<?= $key_last; ?>"></div>
                                        <div class="remove-element js-remove-video"></div>
                                        <div style="display:none;">
                                            <!-- модалка видео -->
                                            <div class="modal" id="video_modal_<?= $key_last; ?>">
                                                <div class="modal_content">
                                                    <a href="#" class="modal_close" data-fancybox-close>x</a>
                                                    <div class="modal_title">Видео</div>
                                                    <?php echo $form->field($eduprog_model, 'video[' . $key_last . '][name]', ['template' => '<label class="ip_label">Название видео</label> {input}{error}{hint}', 'options' => ['class' => 'ip_cell w100']])->textInput(['class' => 'transname input_text', 'data-nametext' => '#video_name_text_' . $key_last]); ?>
                                                    <?php echo $form->field($eduprog_model, 'video[' . $key_last . '][link]', ['template' => '<label class="ip_label">Ссылка на видео (Youtube, VK, Rutube)</label> {input}{error}{hint}', 'options' => ['class' => 'ip_cell w100']])->textInput(); ?>
                                                    <h4 class="lk_step_title mb10">Заставка для видео</h4>
                                                    <p class="modal_text mt0 mb20">Если заставка не добавлена, то будет
                                                        использована обложка видео, добавленная в youtube</p>
                                                    <div class="file_upload_box horizontal <?= (!empty($videoimage)) ? 'added' : ''; ?> uploadContainer need-crop"
                                                         data-aspectRatioX="3" data-aspectRatioY="2"
                                                         data-minCropBoxWidth="876" data-minWidth="876">
                                                        <div class="dropzone_local simulateAvatarUpload">
                                                            <img src="<?= $videoimage; ?>" alt="" class="preview-photo">
                                                            <button type="button" class="button blue small crop_button"
                                                                    style="display: none;">Применить
                                                            </button>
                                                            <a href="#"
                                                               data-target="lkeduprog-video-<?= $key_last; ?>-image"
                                                               class="remove-photo"><i class="fa fa-remove"></i></a>
                                                        </div>
                                                        <?php echo $form->field($eduprog_model, 'videoimage_loader[' . $key_last . '][]', ['template' => '<a href="#" class="button-o gray upload_button simulateAvatarUpload">Загрузить обложку 876х584 px (3:2)</a> <p>Формат: jpg, png, jpeg. <br>Максимальный вес: 2Мб <br>Рекомендованный размер: 876х584 px</p> {input}{error}{hint}', 'options' => ['class' => 'file_upload_info']])->fileInput(['class' => 'image_upload']); ?>
                                                    </div>
                                                    <div class="ip_cell w100 mb0">
                                                        <button class="button blue big w100" data-fancybox-close>
                                                            Добавить видео
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="modal_overlay"></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                        <div class="ip_cell w100 mb0">
                            <button class="button blue medium lk video_in_gallery" data-field="video"
                                    data-cur_key="<?= $key_max_video; ?>">Добавить видео
                            </button>
                        </div>
                    </main>
                </div>
                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb10">Чему вы научитесь</h4>
                        <?= $form->field($eduprog_model, 'learn', ['template' => '{input}{error}{hint}'])->widget(CKEditor::class, [
                                'editorOptions' => ElFinder::ckeditorOptions(
                                        'elfinderuser',
                                        array_merge(['editorplaceholder' => "Добавьте получаемые учащимися знания и навыки"], \app\helpers\ckeditor\CKConfig::add_charlimit(\app\helpers\ckeditor\CKConfig::DEFAULT, 2000))
                                ),
                                'containerOptions' => [
                                        'class' => 'editor_container',
                                ],
                        ]); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb10">Добавить изображение или галерею</h4>
                        <p class="mt0 mb20">
                            Рекомендуем размещать фото с уже проведенных программ, документы/сертификаты подстверждающие
                            авторство и уникальность программы и т.д.
                        </p>
                        <?= $form->field($eduprog_model, 'report', ['options' => ['class' => '']])->widget('app\widgets\multiimage\MultiimageWidget', ['preloaded' => $preloaded_report]); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb10">Стоимость обучения</h4>
                        <?= $form->field($eduprog_model, 'cost_text', ['template' => '{input}{error}{hint}'])->widget(CKEditor::class, [
                                'editorOptions' => ElFinder::ckeditorOptions(
                                        'elfinderuser',
                                        array_merge(['editorplaceholder' => "Опишите, что входит в стоимость обучения"], \app\helpers\ckeditor\CKConfig::add_charlimit(\app\helpers\ckeditor\CKConfig::DEFAULT, 2000))
                                ),
                                'containerOptions' => [
                                        'class' => 'editor_container',
                                ],
                        ]); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb10">Структура программы</h4>
                        <p class="mt0 mb20">В этом разделе вам необходимо указать из каких модулей и блоков состоит
                            образовательная программа.</p>
                        <h4 class="lk_step_title font20 mb10">Подробно опишите каждый модуль или блок программы.</h4>

                        <!-- элементы структуры добавляются в js-structure-list -->
                        <div id="structure_list" class="drag-list js-structure-list js-dragndrop-list">
                            <?php $key_max_structure = 0;
                            if (!empty($eduprog_model->structure)) {
                                foreach ($eduprog_model->structure as $key_last => $structurerow) {
                                    if ($key_max_structure < $key_last) {
                                        $key_max_structure = $key_last;
                                    }
                                    ?>
                                    <div id="structure_<?= $key_last; ?>"
                                         class="drag-element drag-element-mobile js-dragndrop-element"
                                         data-sort="<?= $key_last; ?>">
                                        <?php
                                        // системные поля, присутствуют всегда
                                        echo $form->field($eduprog_model, 'structure[' . $key_last . '][order]', ['template' => '{input}{error}{hint}', 'options' => ['style' => 'display:none;']])->hiddenInput(['class' => 'order_field']);
                                        echo $form->field($eduprog_model, 'structure[' . $key_last . '][visible]', ['template' => '{input}{error}{hint}', 'options' => ['style' => 'display:none;']])->hiddenInput(['class' => 'visible_field']);
                                        ?>
                                        <div class="drag-element-infos">
                                            <div id="structure_name_text_<?= $key_last; ?>"
                                                 class="drag-element-info drag-element-name"><?= $eduprog_model->structure[$key_last]['name']; ?></div>
                                        </div>
                                        <div class="drag-burger drag-burger-element"></div>
                                        <div class="edit-element js-edit-structure" data-id="<?= $key_last ?>"
                                             data-fancyelement="#structure_modal_<?= $key_last; ?>"></div>
                                        <div class="remove-element js-remove-structure"></div>
                                        <div style="display:none;">
                                            <!-- модалка видео -->
                                            <div class="modal" id="structure_modal_<?= $key_last; ?>">
                                                <div class="modal_content">
                                                    <a href="#" class="modal_close" data-fancybox-close>x</a>
                                                    <div class="modal_title">Добавить</div>
                                                    <?php echo $form->field($eduprog_model, 'structure[' . $key_last . '][name]', ['template' => '<label class="ip_label">Название модуля/блока</label> {input}{error}{hint}', 'options' => ['class' => 'ip_cell w100']])->textInput(['class' => 'transname input_text', 'data-nametext' => '#structure_name_text_' . $key_last, 'placeholder' => 'Например: Блок 1']); ?>

                                                    <?= $form->field($eduprog_model, 'structure[' . $key_last . '][content]', ['template' => '<label class="ip_label">Подробно о модуле</label>{input}{error}{hint}', 'options' => ['class' => 'ip_cell w100']])->textarea(); ?>

                                                    <div class="ip_cell w100 mb0">
                                                        <button class="button blue big w100" data-fancybox-close>
                                                            Добавить
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="modal_overlay"></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                        <div class="ip_cell w100 mb0">
                            <button data-field="structure" data-cur_key="<?= $key_max_structure; ?>"
                                    class="button blue medium lk structure_in_gallery">Добавить
                            </button>
                        </div>
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb10">Внесите информацию о преподавателях / ведущих</h4>
                        <!-- элементы ведущих добавляются в js-speaker-list -->
                        <div id="lectors_list" class="drag-list js-speaker-list js-dragndrop-list">
                            <?php $key_max_lectors = 0;
                            if (!empty($eduprog_model->lectors)) {
                                foreach ($eduprog_model->lectors as $key_last => $lectorsrow) {
                                    if ($key_max_lectors < $key_last) {
                                        $key_max_lectors = $key_last;
                                    }

                                    $lectorimage = false;
                                    if ($original && $original->currentModeration) {
                                        // пробуем достать иображение из модерации
                                        if (!empty($lectorsrow['image'])) {
                                            $lectorimage = $original->currentModeration->getThumb('lectorimage', 'main', false, $lectorsrow['image'][0]);
                                        }
                                    }
                                    if (empty($lectorimage)) {
                                        if (!empty($lectorsrow['image'])) {
                                            $lectorimage = $original->getThumb('lectorimage', 'main', false, $lectorsrow['image'][0]);
                                        }
                                    }

                                    ?>
                                    <div id="lectors_<?= $key_last; ?>"
                                         class="drag-element drag-element-mobile js-dragndrop-element"
                                         data-sort="<?= $key_last; ?>">
                                        <?php
                                        // системные поля, присутствуют всегда
                                        echo $form->field($eduprog_model, 'lectors[' . $key_last . '][order]', ['template' => '{input}{error}{hint}', 'options' => ['style' => 'display:none;']])->hiddenInput(['class' => 'order_field']);
                                        echo $form->field($eduprog_model, 'lectors[' . $key_last . '][visible]', ['template' => '{input}{error}{hint}', 'options' => ['style' => 'display:none;']])->hiddenInput(['class' => 'visible_field']);
                                        // холдер для загруженных изображений
                                        // нужно скрытое поле, способное хранить массив данных
                                        $data_ids = array_combine($eduprog_model->lectors[$key_last]['image'], $eduprog_model->lectors[$key_last]['image']);
                                        echo $form->field($eduprog_model, 'lectors[' . $key_last . '][image]', ['template' => '{input}{error}{hint}', 'options' => ['style' => 'display:none;']])->dropDownList($data_ids, ['multiple' => 'multiple']);

                                        // фио заполняется из профиля, если выбран пользователь.
                                        $lector = false;
                                        if (!empty($eduprog_model->lectors[$key_last]['user_id']) && ($lector = UserAR::find()->andWhere(['id' => $eduprog_model->lectors[$key_last]['user_id']])->visible(['expert', 'exporg'])->one())) {
                                            $fio = $lector->profile->halfname;
                                        } else {
                                            $fio = $eduprog_model->lectors[$key_last]['fio'];
                                        } ?>
                                        <div class="drag-element-infos">
                                            <div id="lectors_name_text_<?= $key_last; ?>"
                                                 class="drag-element-info drag-element-name"><?= $fio; ?></div>
                                            <?php echo $form->field($eduprog_model, 'lectorimage_loader[' . $key_last . '][]', ['template' => '{error}{hint}'])->hiddenInput(); ?>
                                            <?php echo $form->field($eduprog_model, 'lectors[' . $key_last . '][content]', ['template' => '{error}{hint}'])->hiddenInput(); ?>
                                        </div>
                                        <div class="drag-burger drag-burger-element"></div>
                                        <div class="edit-element js-edit-lectors"
                                             data-fancyelement="#lectors_modal_<?= $key_last; ?>"></div>
                                        <div class="remove-element js-remove-lectors"></div>
                                        <div style="display:none;">
                                            <!-- модалка видео -->
                                            <div class="modal" id="lectors_modal_<?= $key_last; ?>">
                                                <div class="modal_content">
                                                    <a href="#" class="modal_close" data-fancybox-close>x</a>
                                                    <div class="modal_title">Добавить преподавателя/ведущего</div>
                                                    <div class="lenta-menu">
                                                        <a href="#!"
                                                           class="tab tab-trigger<?= (empty($eduprog_model->lectors[$key_last]['user_id'])) ? ' active' : ''; ?>"
                                                           data-tab="Добавить">Добавить</a>
                                                        <a href="#!"
                                                           class="tab tab-trigger<?= (!empty($eduprog_model->lectors[$key_last]['user_id'])) ? ' active' : ''; ?>"
                                                           data-tab="Выбрать на сайте">Выбрать на сайте</a>
                                                    </div>
                                                    <div class="tabs-content">
                                                        <div class="tab-item<?= (empty($eduprog_model->lectors[$key_last]['user_id'])) ? ' active' : ''; ?>"
                                                             data-tab="Добавить">
                                                            <?php echo $form->field($eduprog_model, 'lectors[' . $key_last . '][fio]', ['template' => '<label class="ip_label">Фамилия Имя Отчество</label> {input}{error}{hint}', 'options' => ['class' => 'ip_cell w100']])->textInput(['class' => 'transname input_text', 'data-nametext' => '#lectors_name_text_' . $key_last, 'placeholder' => '']); ?>
                                                            <?php echo $form->field($eduprog_model, 'lectors[' . $key_last . '][content]', ['template' => '<label class="ip_label">Регалии преподавателя</label> {input}{error}{hint}', 'options' => ['class' => 'ip_cell w100']])->textArea(['placeholder' => '']); ?>
                                                            <h4 class="lk_step_title mb10">Фотография преподавателя</h4>
                                                            <div class="file_upload_box horizontal <?= (!empty($lectorimage)) ? 'added' : ''; ?> uploadContainer need-crop"
                                                                 data-aspectRatioX="1" data-aspectRatioY="1"
                                                                 data-minCropBoxWidth="285">
                                                                <div class="dropzone_local simulateAvatarUpload">
                                                                    <img src="<?= $lectorimage; ?>" alt=""
                                                                         class="preview-photo">
                                                                    <button type="button"
                                                                            class="button blue small crop_button"
                                                                            style="display: none;">Применить
                                                                    </button>
                                                                    <a href="#"
                                                                       data-target="lkeduprog-lectors-<?= $key_last; ?>-image"
                                                                       class="remove-photo"><i class="fa fa-remove"></i></a>
                                                                </div>
                                                                <?php echo $form->field($eduprog_model, 'lectorimage_loader[' . $key_last . '][]', ['template' => '<a href="#" class="button-o gray upload_button simulateAvatarUpload">Загрузить фото 285х285 px (1:1)</a> <p>Формат: jpg, png, jpeg. <br>Максимальный вес: 2Мб <br>Рекомендованный размер: 285х285 px</p> {input}{error}{hint}', 'options' => ['class' => 'file_upload_info']])->fileInput(['class' => 'image_upload']); ?>
                                                            </div>
                                                        </div>
                                                        <div class="tab-item<?= (!empty($eduprog_model->lectors[$key_last]['user_id'])) ? ' active' : ''; ?>"
                                                             data-tab="Выбрать на сайте">
                                                            <?php
                                                            $lectors_data = [];
                                                            if (!empty($eduprog_model->lectors[$key_last]['user_id'])) {
                                                                $item_user = UserAR::find()->andWhere(['id' => $eduprog_model->lectors[$key_last]['user_id']])->visible(['expert'])->one();
                                                                $lectors_data[$item_user->id] = $item_user->profile->halfname;
                                                            } else {
                                                                $item_user = false;
                                                            }
                                                            echo $form->field($eduprog_model, 'lectors[' . $key_last . '][user_id]', ['template' => '<label class="ip_label">Поиск по Экспертам и Высшему Экспертному совету</label> {input}{error}{hint}', 'options' => ['class' => 'ip_cell w100']])->dropDownList($lectors_data, ['placeholder' => 'Начните вводить ФИО', 'class' => 'select_lector', 'style' => 'width:100%', 'data-container' => '#lector_info_' . $key_last, 'data-nametext' => '#lectors_name_text_' . $key_last]); ?>
                                                            <div id="lector_info_<?= $key_last; ?>"
                                                                 class="search-speaker-result">
                                                                <?php if (!empty($item_user)) {
                                                                    echo $this->render('_lector', ['item' => $item_user]);
                                                                } ?>
                                                            </div>
                                                        </div>
                                                        <?php echo $form->field($eduprog_model, 'lectors[' . $key_last . '][video_link]', ['template' => '<label class="ip_label">Ссылка на видеопрезентацию (Youtube, VK, Rutube)</label> {input}{error}{hint}', 'options' => ['class' => 'ip_cell w100']])->textInput(['placeholder' => '']); ?>
                                                        <div class="ip_cell w100 mb0">
                                                            <button class="button blue big w100" data-fancybox-close>
                                                                Добавить
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="modal_overlay"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                        <div class="ip_cell w100 mb0">
                            <button data-field="lectors" data-cur_key="<?= $key_max_lectors; ?>"
                                    class="button blue medium lk lectors_in_gallery">Добавить
                            </button>
                        </div>
                    </main>
                </div>
                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb10">Кому подойдет программа</h4>
                        <?= $form->field($eduprog_model, 'suits_for', ['template' => '{input}{error}{hint}'])->widget(CKEditor::class, [
                                'editorOptions' => ElFinder::ckeditorOptions(
                                        'elfinderuser',
                                        array_merge(['editorplaceholder' => "Опишите, кому подойдет программа"], \app\helpers\ckeditor\CKConfig::add_charlimit(\app\helpers\ckeditor\CKConfig::DEFAULT, 2000))
                                ),
                                'containerOptions' => [
                                        'class' => 'editor_container',
                                ],
                        ]); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb15">Другая информация о программе</h4>
                        <p class="mt0 mb20">Дополнительная информация о программе. Если оставить поле “Заголовок” не
                            заполненным, то будет использован стандартный заголовок “Другая информация о программе”.</p>
                        <?= $form->field($eduprog_model, 'block_title', ['template' => '<div class="symbols_counter_box">{input}<span class="symbols_counter"></span></div>{error}{hint}'])->textInput(['autocomplete' => 'off', 'placeholder' => "Заголовок", 'class' => 'input_text limitedSybmbols', 'maxlength' => 90]); ?>
                        <?= $form->field($eduprog_model, 'block_text', ['template' => '{input}{error}{hint}'])->widget(CKEditor::class, [
                                'editorOptions' => ElFinder::ckeditorOptions(
                                        'elfinderuser',
                                        array_merge(['editorplaceholder' => "Рекомендуем рассказать об актуальности темы обучения"], \app\helpers\ckeditor\CKConfig::add_charlimit(\app\helpers\ckeditor\CKConfig::DEFAULT, 2000))
                                ),
                                'containerOptions' => [
                                        'class' => 'editor_container',
                                ],
                        ]); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb15">Сферы применения</h4>
                        <p class="mt0 mb20">Если оставить поле “Заголовок” не заполненным, то будет использован
                            стандартный заголовок “Сферы применения”.</p>
                        <?= $form->field($eduprog_model, 'works_title', ['template' => '<div class="symbols_counter_box">{input}<span class="symbols_counter"></span></div>{error}{hint}'])->textInput(['autocomplete' => 'off', 'placeholder' => "Заголовок", 'class' => 'input_text limitedSybmbols', 'maxlength' => 90]); ?>
                        <?= $form->field($eduprog_model, 'works_text', ['template' => '{input}{error}{hint}'])->widget(CKEditor::class, [
                                'editorOptions' => ElFinder::ckeditorOptions(
                                        'elfinderuser',
                                        array_merge(['editorplaceholder' => "Опишите, как и где слушатель сможет применить полученные знания и навыки."], \app\helpers\ckeditor\CKConfig::add_charlimit(\app\helpers\ckeditor\CKConfig::DEFAULT, 2000))
                                ),
                                'containerOptions' => [
                                        'class' => 'editor_container',
                                ],
                        ]); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb15">Выберите ключевые слова</h4>
                        <p class="mt0 mb20">Выберите 5 ключевых слов из предложенных.<br>Это нужно для более быстрого и
                            удобного поиска программы на сайте.</p>
                        <?= $form->field($eduprog_model, 'keywords', ['template' => '{input}{error}{hint}'])->widget(KeywordWidget::class)->label(''); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb15">Укажите теги</h4>
                        <p class="mt0 mb20">Выберите из предложенных и при необходимости добавьте свои.</p>
                        <?= $form->field($eduprog_model, 'tags')->dropDownList($eduprog_model->getTagsList(), ['class' => "pretty_tags_ns_max10", 'multiple' => 'multiple']); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <div class="lk_content">
                        <div class="ip_cell w100 flex justify-between buttons-wrapper mb0">
                            <a href="<?= Url::toRoute([$model->getUrlPath(), 'step' => 2, 'id' => $original->id]); ?>"
                               class="button-o gray medium">Вернуться</a>
                            <button class="button blue medium lk">Продолжить</button>
                        </div>
                    </div>
                </div>


                <?php ob_start();  // запись в буфер блока для новой записи видео
                $key_empty = 'change_me'; ?>
                <div id="video_<?= $key_empty; ?>" class="drag-element drag-element-mobile js-dragndrop-element"
                     data-sort="<?= $key_empty; ?>">
                    <?php
                    // системные поля, присутствуют всегда
                    echo $form->field($eduprog_model, 'video[' . $key_empty . '][order]', ['template' => '{input}{error}{hint}', 'options' => ['style' => 'display:none;']])->hiddenInput(['class' => 'order_field']);
                    echo $form->field($eduprog_model, 'video[' . $key_empty . '][visible]', ['template' => '{input}{error}{hint}', 'options' => ['style' => 'display:none;']])->hiddenInput(['class' => 'visible_field']);
                    ?>
                    <div class="drag-element-infos">
                        <div id="video_name_text_<?= $key_empty; ?>" class="drag-element-info drag-element-name"></div>
                        <?php echo $form->field($eduprog_model, 'videoimage_loader[' . $key_empty . '][]', ['template' => '{error}{hint}'])->hiddenInput(); ?>
                    </div>
                    <div class="drag-burger drag-burger-element"></div>
                    <div class="edit-element js-edit-video" data-fancyelement="#video_modal_<?= $key_empty; ?>"></div>
                    <div class="remove-element js-remove-video"></div>
                    <div style="display:none;">
                        <!-- модалка видео -->
                        <div class="modal" id="video_modal_<?= $key_empty; ?>">
                            <div class="modal_content">
                                <a href="#" class="modal_close" data-fancybox-close>x</a>
                                <div class="modal_title">Видео</div>
                                <?php echo $form->field($eduprog_model, 'video[' . $key_empty . '][name]', ['template' => '<label class="ip_label">Название видео</label> {input}{error}{hint}', 'options' => ['class' => 'ip_cell w100']])->textInput(['class' => 'transname input_text', 'data-nametext' => '#video_name_text_' . $key_empty]); ?>
                                <?php echo $form->field($eduprog_model, 'video[' . $key_empty . '][link]', ['template' => '<label class="ip_label">Ссылка на видео (Youtube, VK, Rutube)</label> {input}{error}{hint}', 'options' => ['class' => 'ip_cell w100']])->textInput(); ?>
                                <h4 class="lk_step_title mb10">Заставка для видео</h4>
                                <p class="modal_text mt0 mb20">Если заставка не добавлена, то будет использована обложка
                                    видео, добавленная в youtube</p>
                                <div class="file_upload_box horizontal uploadContainer need-crop" data-aspectRatioX="3"
                                     data-aspectRatioY="2" data-minCropBoxWidth="876" data-minWidth="876">
                                    <div class="dropzone_local simulateAvatarUpload">
                                        <img src="" alt="" class="preview-photo">
                                        <button type="button" class="button blue small crop_button"
                                                style="display: none;">Применить
                                        </button>
                                        <a href="#" class="remove-photo"><i class="fa fa-remove"></i></a>
                                    </div>
                                    <?php echo $form->field($eduprog_model, 'videoimage_loader[' . $key_empty . '][]', ['template' => '<a href="#" class="button-o gray upload_button simulateAvatarUpload">Загрузить обложку 876х584 px (3:2)</a> <p>Формат: jpg, png, jpeg. <br>Максимальный вес: 2Мб <br>Рекомендованный размер: 876х584 px</p> {input}{error}{hint}', 'options' => ['class' => 'file_upload_info']])->fileInput(['class' => 'image_upload']); ?>
                                </div>
                                <div class="ip_cell w100 mb0">
                                    <button class="button blue big w100" data-fancybox-close>Добавить видео</button>
                                </div>
                            </div>
                            <div class="modal_overlay"></div>
                        </div>
                    </div>
                </div>
                <?php $clear_html_for_video = ob_get_clean();
                $this->registerJsVar('video_newitem', $clear_html_for_video, $position = yii\web\View::POS_HEAD);
                $this->registerJsVar('video_error_settings', ['subfields' => ['name', 'link'], 'videoimage_params' => $eduprog_model->getValidateParamsMultifields('videoimage_loader')], $position = yii\web\View::POS_HEAD);
                ?>

                <?php ob_start();  // запись в буфер блока для новой записи структуры

                $editor_options = ElFinder::ckeditorOptions('elfinderuser', array_merge(['editorplaceholder' => "Добавьте подробное описание этого модуля"], \app\helpers\ckeditor\CKConfig::add_charlimit(\app\helpers\ckeditor\CKConfig::MINI, 2000)));

                $key_empty = 'change_me'; ?>
                <div id="structure_<?= $key_empty; ?>" class="drag-element drag-element-mobile js-dragndrop-element"
                     data-sort="<?= $key_empty; ?>">
                    <?php
                    // системные поля, присутствуют всегда
                    echo $form->field($eduprog_model, 'structure[' . $key_empty . '][order]', ['template' => '{input}{error}{hint}', 'options' => ['style' => 'display:none;']])->hiddenInput(['class' => 'order_field']);
                    echo $form->field($eduprog_model, 'structure[' . $key_empty . '][visible]', ['template' => '{input}{error}{hint}', 'options' => ['style' => 'display:none;']])->hiddenInput(['class' => 'visible_field']);
                    ?>
                    <div class="drag-element-infos">
                        <div id="structure_name_text_<?= $key_empty; ?>"
                             class="drag-element-info drag-element-name"></div>
                    </div>
                    <div class="drag-burger drag-burger-element"></div>
                    <div class="edit-element js-edit-structure" data-id="<?= $key_empty ?>"
                         data-fancyelement="#structure_modal_<?= $key_empty; ?>"></div>
                    <div class="remove-element js-remove-structure"></div>
                    <div style="display:none;">
                        <!-- модалка видео -->
                        <div class="modal" id="structure_modal_<?= $key_empty; ?>">
                            <div class="modal_content">
                                <a href="#" class="modal_close" data-fancybox-close>x</a>
                                <div class="modal_title">Добавить</div>

                                <?= $form->field($eduprog_model, 'structure[' . $key_empty . '][name]', ['template' => '<label class="ip_label">Название модуля/блока</label> {input}{error}{hint}', 'options' => ['class' => 'ip_cell w100']])->textInput(['class' => 'transname input_text', 'data-nametext' => '#structure_name_text_' . $key_empty, 'placeholder' => 'Например: Блок 1']); ?>
                                <?= $form->field($eduprog_model, 'structure[' . $key_empty . '][content]', ['template' => '<label class="ip_label">Подробно о модуле</label>{input}{error}{hint}', 'options' => ['class' => 'ip_cell w100']])->widget(CKEditor::class, [
                                        'editorOptions' => $editor_options,
                                ]); ?>

                                <div class="ip_cell w100 mb0">
                                    <button class="button blue big w100" data-fancybox-close>Добавить</button>
                                </div>
                            </div>
                            <div class="modal_overlay"></div>
                        </div>
                    </div>
                </div>
                <?php $clear_html_for_structure = ob_get_clean();
                $this->registerJsVar('structure_newitem', $clear_html_for_structure, $position = yii\web\View::POS_HEAD);
                $this->registerJsVar('structure_error_settings', ['subfields' => ['name', 'content'], 'wysiwyg' => ['content' => $editor_options]], $position = yii\web\View::POS_HEAD);
                ?>

                <?php ob_start();  // запись в буфер блока для новой записи преподавателя
                $key_empty = 'change_me'; ?>
                <div id="lectors_<?= $key_empty; ?>" class="drag-element drag-element-mobile js-dragndrop-element"
                     data-sort="<?= $key_empty; ?>">
                    <?php
                    // системные поля, присутствуют всегда
                    echo $form->field($eduprog_model, 'lectors[' . $key_empty . '][order]', ['template' => '{input}{error}{hint}', 'options' => ['style' => 'display:none;']])->hiddenInput(['class' => 'order_field']);
                    echo $form->field($eduprog_model, 'lectors[' . $key_empty . '][visible]', ['template' => '{input}{error}{hint}', 'options' => ['style' => 'display:none;']])->hiddenInput(['class' => 'visible_field']);
                    ?>
                    <div class="drag-element-infos">
                        <div id="lectors_name_text_<?= $key_empty; ?>"
                             class="drag-element-info drag-element-name"></div>
                        <?php echo $form->field($eduprog_model, 'lectorimage_loader[' . $key_empty . '][]', ['template' => '{error}{hint}'])->hiddenInput(); ?>
                        <?php echo $form->field($eduprog_model, 'lectors[' . $key_empty . '][content]', ['template' => '{error}{hint}'])->hiddenInput(); ?>
                    </div>
                    <div class="drag-burger drag-burger-element"></div>
                    <div class="edit-element js-edit-lectors"
                         data-fancyelement="#lectors_modal_<?= $key_empty; ?>"></div>
                    <div class="remove-element js-remove-lectors"></div>
                    <div style="display:none;">
                        <!-- модалка видео -->
                        <div class="modal" id="lectors_modal_<?= $key_empty; ?>">
                            <div class="modal_content">
                                <a href="#" class="modal_close" data-fancybox-close>x</a>
                                <div class="modal_title">Добавить преподавателя/ведущего</div>
                                <div class="lenta-menu">
                                    <a href="#!" class="tab tab-trigger active" data-tab="Добавить">Добавить</a>
                                    <a href="#!" class="tab tab-trigger" data-tab="Выбрать на сайте">Выбрать на
                                        сайте</a>
                                </div>
                                <div class="tabs-content">
                                    <div class="tab-item active" data-tab="Добавить">
                                        <?php echo $form->field($eduprog_model, 'lectors[' . $key_empty . '][fio]', ['template' => '<label class="ip_label">Фамилия Имя Отчество</label> {input}{error}{hint}', 'options' => ['class' => 'ip_cell w100']])->textInput(['class' => 'transname input_text', 'data-nametext' => '#lectors_name_text_' . $key_empty, 'placeholder' => '']); ?>
                                        <?php echo $form->field($eduprog_model, 'lectors[' . $key_empty . '][content]', ['template' => '<label class="ip_label">Регалии преподавателя</label> {input}{error}{hint}', 'options' => ['class' => 'ip_cell w100']])->textArea(['placeholder' => '']); ?>

                                        <h4 class="lk_step_title mb10">Фотография преподавателя</h4>
                                        <div class="file_upload_box horizontal uploadContainer need-crop"
                                             data-aspectRatioX="1" data-aspectRatioY="1" data-minCropBoxWidth="285">
                                            <div class="dropzone_local simulateAvatarUpload">
                                                <img src="" alt="" class="preview-photo">
                                                <button type="button" class="button blue small crop_button"
                                                        style="display: none;">Применить
                                                </button>
                                                <a href="#" class="remove-photo"><i class="fa fa-remove"></i></a>
                                            </div>
                                            <?php echo $form->field($eduprog_model, 'lectorimage_loader[' . $key_empty . '][]', ['template' => '<a href="#" class="button-o gray upload_button simulateAvatarUpload">Загрузить фото 285х285 px (1:1)</a> <p>Формат: jpg, png, jpeg. <br>Максимальный вес: 2Мб <br>Рекомендованный размер: 285х285 px</p> {input}{error}{hint}', 'options' => ['class' => 'file_upload_info']])->fileInput(['class' => 'image_upload']); ?>
                                        </div>
                                    </div>
                                    <div class="tab-item" data-tab="Выбрать на сайте">
                                        <?php echo $form->field($eduprog_model, 'lectors[' . $key_empty . '][user_id]', ['template' => '<label class="ip_label">Поиск по Экспертам и Высшему Экспертному совету</label> {input}{error}{hint}', 'options' => ['class' => 'ip_cell w100']])->dropDownList([], ['placeholder' => 'Начните вводить ФИО', 'class' => 'select_lector', 'style' => 'width:100%', 'data-container' => '#lector_info_' . $key_empty, 'data-nametext' => '#lectors_name_text_' . $key_empty]); ?>
                                        <div id="lector_info_<?= $key_empty; ?>" class="search-speaker-result"></div>
                                    </div>

                                    <?php echo $form->field($eduprog_model, 'lectors[' . $key_empty . '][video_link]', ['template' => '<label class="ip_label">Ссылка на видеопрезентацию (Youtube, VK, Rutube)</label> {input}{error}{hint}', 'options' => ['class' => 'ip_cell w100']])->textInput(['placeholder' => '']); ?>
                                    <div class="ip_cell w100 mb0">
                                        <button class="button blue big w100" data-fancybox-close>Сохранить</button>
                                    </div>
                                </div>
                                <div class="modal_overlay"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $clear_html_for_lectors = ob_get_clean();
                $this->registerJsVar('lectors_newitem', $clear_html_for_lectors, $position = yii\web\View::POS_HEAD);
                $this->registerJsVar('lectors_error_settings', ['subfields' => ['fio', 'content', 'user_id', 'video_link'], 'lectorimage_params' => $eduprog_model->getValidateParamsMultifields('lectorimage_loader')], $position = yii\web\View::POS_HEAD);
                ?>

                <?php ActiveForm::end(); ?>
            </div>
            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
        </div>
    </main>

    <div class="modal" id="fail_service_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Ошибка создания программы</div>
                <p>При сохранении данных возникли ошибки. <?= \app\helpers\MainHelper::getHelpText() ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>


<?php
$url = Url::toRoute(['/pages/eduprog/saveeduprog/', 'step' => 3]);
$url_search_lector = Url::toRoute(['/pages/eduprog/search-lector/']);
$url_info_lector = Url::toRoute(['/pages/eduprog/info-lector/']);
$js = <<<JS
    
    // удаление изображений в мультифилдах
    $('body').on('click', '.remove-photo:not(.event)', function(){
        rodak = $(this).closest('.uploadContainer');

        /* если пользователь видит оригинальное изображение, а не загруженное */
        if ($(rodak.find('.image_upload')[0]).val().length == 0) {
            /* очистить поле select */
            if ($('#'+$(this).data('target')).length > 0) {
                $('#'+$(this).data('target')).val('');
            }
        }

    });

    $('#eduprog-form').on('beforeSubmit', function(event){
        var formData = new FormData($('#eduprog-form')[0]);
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
                    $('#fail_service_modal .modal_title').html('Ошибка создания программы');
                    $('#fail_service_modal p').html(data.message);
                    modalPos('#fail_service_modal');

                    $('#eduprog-form').yiiActiveForm('updateMessages', data.messages, false);
                }
            }
        });
        return false;
    });
    $('#eduprog-form').on('submit', function(e){
        e.preventDefault();
        return false;
    });

    $('body').on('keyup change paste','.transname', function(e){
        $($(this).data('nametext')).html($(this).val());
    });

    $('body').on('click','.video_in_gallery', function(e){
        e.preventDefault();
        // добавить элемент в список 
        let field_name = $(this).data('field');
        let new_html = window[field_name + '_newitem'];
        let field_settings = window[field_name + '_error_settings'];
        let cur_key = parseInt($(this).attr('data-cur_key'));
        cur_key = cur_key + 1;
        new_html = new_html.replace(/change_me/g,cur_key);
        $('#video_list').append(new_html);
        $(this).attr('data-cur_key',cur_key);

        // добавить динамическую валидацию полей
        for (key in field_settings.subfields) {
            $('#eduprog-form').yiiActiveForm('add', {
                'id': 'lkeduprog-'+field_name+'-'+cur_key+'-'+field_settings.subfields[key],
                'name': field_name+'['+cur_key+']['+field_settings.subfields[key]+']',
                'container': '.field-lkeduprog-'+field_name+'-'+cur_key+'-'+field_settings.subfields[key],
                'input': '#lkeduprog-'+field_name+'-'+cur_key+'-'+field_settings.subfields[key],
                'error': '.help-block',
                'enableAjaxValidation':true
            });
        }

        let form = $('#eduprog-form');
        // отдельно добавляем ошибку на картинку
        $('#eduprog-form').yiiActiveForm('add', {
            'id': 'lkeduprog-videoimage_loader-'+cur_key,
            'name': 'videoimage_loader['+cur_key+'][]',
            'container': '.field-lkeduprog-videoimage_loader-'+cur_key,
            'input': '#lkeduprog-videoimage_loader-'+cur_key,
            'error': '.help-block',
            'enableClientValidation':true,
            validate:  function (attribute, value, messages, deferred, form) {
                yii.validation.image(attribute, messages, field_settings.videoimage_params, deferred);
            }
        });

        // открыть модалку из элемента
        $.fancybox.open({
            src: '#video_modal_'+cur_key,
            type: 'inline',
            parentEl: "#eduprog-form",
        });

    });

    // удаление видео
    $('body').on('click','.js-remove-video', function(e) {
        $(this).closest('.drag-element').remove();
    });

    // добавление в структуру
    $('body').on('click','.structure_in_gallery', function(e){
        e.preventDefault();
        // добавить элемент в список 
        let field_name = $(this).data('field');
        let new_html = window[field_name + '_newitem'];
        let field_settings = window[field_name + '_error_settings'];
        let cur_key = parseInt($(this).attr('data-cur_key'));
        cur_key = cur_key + 1;
        new_html = new_html.replace(/change_me/g,cur_key);
        $('#structure_list').append(new_html);
        $(this).attr('data-cur_key',cur_key);

        // добавить динамическую валидацию полей
        for (key in field_settings.subfields) {
            $('#eduprog-form').yiiActiveForm('add', {
                'id': 'lkeduprog-'+field_name+'-'+cur_key+'-'+field_settings.subfields[key],
                'name': field_name+'['+cur_key+']['+field_settings.subfields[key]+']',
                'container': '.field-lkeduprog-'+field_name+'-'+cur_key+'-'+field_settings.subfields[key],
                'input': '#lkeduprog-'+field_name+'-'+cur_key+'-'+field_settings.subfields[key],
                'error': '.help-block',
                'enableAjaxValidation':true
            });
        }
        if(CKEDITOR){
            if(CKEDITOR.instances['lkeduprog-'+field_name+'-'+cur_key+'-content']){
                CKEDITOR.instances['lkeduprog-'+field_name+'-'+cur_key+'-content'].destroy();
            }
        }
        // инициировать CKEditor
        CKEDITOR.replace('lkeduprog-'+field_name+'-'+cur_key+'-content', field_settings.wysiwyg['content']);
        // открыть модалку из элемента
        $.fancybox.open({
            src: '#structure_modal_'+cur_key,
            type: 'inline',
            parentEl: "#eduprog-form",
        });

    });

    // удаление структуры
    $('body').on('click','.js-remove-structure', function(e) {
        $(this).closest('.drag-element').remove();
    });

    // добавление преподавателя 
    $('body').on('click','.lectors_in_gallery', function(e){
        e.preventDefault();
        // добавить элемент в список 
        let field_name = $(this).data('field');
        let new_html = window[field_name + '_newitem'];
        let field_settings = window[field_name + '_error_settings'];
        let cur_key = parseInt($(this).attr('data-cur_key'));
        cur_key = cur_key + 1;
        new_html = new_html.replace(/change_me/g,cur_key);
        $('#lectors_list').append(new_html);
        $(this).attr('data-cur_key',cur_key);

        init_lectors_select();

        // добавить динамическую валидацию полей
        for (key in field_settings.subfields) {
            $('#eduprog-form').yiiActiveForm('add', {
                'id': 'lkeduprog-'+field_name+'-'+cur_key+'-'+field_settings.subfields[key],
                'name': field_name+'['+cur_key+']['+field_settings.subfields[key]+']',
                'container': '.field-lkeduprog-'+field_name+'-'+cur_key+'-'+field_settings.subfields[key],
                'input': '#lkeduprog-'+field_name+'-'+cur_key+'-'+field_settings.subfields[key],
                'error': '.help-block',
                'enableAjaxValidation':true
            });
        }

        let form = $('#eduprog-form');
        // отдельно добавляем ошибку на картинку
        $('#eduprog-form').yiiActiveForm('add', {
            'id': 'lkeduprog-lectorimage_loader-'+cur_key,
            'name': 'lectorimage_loader['+cur_key+'][]',
            'container': '.field-lkeduprog-lectorimage_loader-'+cur_key,
            'input': '#lkeduprog-lectorimage_loader-'+cur_key,
            'error': '.help-block',
            'enableClientValidation':true,
            validate:  function (attribute, value, messages, deferred, form) {
                yii.validation.image(attribute, messages, field_settings.lectorimage_params, deferred);
            }
        });

        // открыть модалку из элемента
        $.fancybox.open({
            src: '#lectors_modal_'+cur_key,
            type: 'inline',
            parentEl: "#eduprog-form",
        });

    });

    // удаление преподавателя
    $('body').on('click','.js-remove-lectors', function(e) {
        $(this).closest('.drag-element').remove();
    });

    function init_lectors_select() {
        // поиск по экспертам 

        $('.select_lector').each(function() {
            var dropdownParent = $(this).closest('.modal');
            
            $(this).select2({
                placeholder: 'Поиск по Экспертам (введите имя)',
                dropdownParent: dropdownParent,
                minimumInputLength: 4,
                // dropdownParent: $('#myModal')
                language: {
                    noResults: function () {
                      return 'Ничего не найдено';
                    },
                    searching: function () {
                      return 'Поиск…';
                    },
                    errorLoading: function () {
                      return 'Результаты не могут быть загружены';
                    },
                    inputTooShort: function(args) { 
                        return "Введите еще минимум "+ (parseInt(args.minimum) - parseInt(args.input.length))+" символ(а)";
                    }
                },
                ajax: {
                    delay: 300,
                    url: '{$url_search_lector}',
                    data: function (params) {
                        var queryParameters = {
                          q: params.term
                        }

                        return queryParameters;
                    },
                    processResults: function (data) {
                        return {
                          results: data.items
                        };
                      },
                    dataType: 'json'
                }
            });
        });

    }

    init_lectors_select();

    $('body').on('change','.select_lector', function() {
        let param = yii.getCsrfParam();
        let token = yii.getCsrfToken();
        let data = {};
        let container = $(this).data('container');
        let nametext = $(this).data('nametext');
        data[param] = token;
        data['user_id'] = $(this).val();
        $.ajax({
            type: 'POST',
            url: '{$url_info_lector}',
            data: data,
            success: function (data) {
                if (data.status == 'success') {
                    // вывести контент в блок
                    $(container).html(data.html);
                    // заменить фио на плашке
                    $(nametext).html(data.fio);
                } else {
                    alert('Невозможно получить данные об эксперте');
                }
            }
        });
    });

    $('body').on('click', '.js-edit-video, .js-edit-lectors', function(e) {
        let id = $(this).data('id');
        let field_settings = window['structure_error_settings'];
        
        e.preventDefault();
        
        $.fancybox.open({
            src: $(this).data('fancyelement'),
            type: 'inline',
            parentEl: "#eduprog-form",
        });
    });
    
    $('body').on('click', '.js-edit-structure', function(e) {
        let id = $(this).data('id');
        let field_settings = window['structure_error_settings'];
        
        e.preventDefault();
        
        $.fancybox.open({
            src: $(this).data('fancyelement'),
            type: 'inline',
            parentEl: "#eduprog-form",
        });
        if(CKEDITOR){
            if(CKEDITOR.instances['lkeduprog-structure-'+id+'-content']){
                CKEDITOR.instances['lkeduprog-structure-'+id+'-content'].destroy();
            }
        }
        // инициировать CKEditor
        CKEDITOR.replace('lkeduprog-structure-'+id+'-content', field_settings.wysiwyg['content']);
    });

JS;
$this->registerJs($js);
?>