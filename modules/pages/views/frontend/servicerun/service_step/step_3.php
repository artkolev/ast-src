<?php

use app\models\ServiceForm;
use kitsunefet\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use yii\widgets\ActiveForm;

/**
 * @var \yii\web\View $this
 * @var ServiceForm $modelform
 * @var ActiveForm $form
 */

?>

<?= $form->field($modelform, 'id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
<?= $form->field($modelform, 'step', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>

<div class="lk_block">
    <main class="lk_content">
        <h4 class="lk_step_title mt20">Подробно об услуге</h4>
        <?= $form->field($modelform, 'description')->widget(CKEditor::class, [
                'editorOptions' => ElFinder::ckeditorOptions(
                        'elfinderuser',
                        array_merge(['editorplaceholder' => 'Рекомендуем указать кому будет полезна Ваша услуга.
                                Каким клиентам подходит услуга?
                                Какие задачи решает?
                                Если есть уникальные особенности, то укажите их (например, авторская методика, результаты исследований).'], \app\helpers\ckeditor\CKConfig::add_charlimit(\app\helpers\ckeditor\CKConfig::DEFAULT, 2000))
                ),
                'containerOptions' => [
                        'class' => 'editor_container',
                ],
        ]); ?>
        <h4 class="lk_step_title mt20">Что входит в стоимость оказания услуги</h4>
        <?= $form->field($modelform, 'price_descr')->widget(CKEditor::class, [
                'editorOptions' => ElFinder::ckeditorOptions(
                        'elfinderuser',
                        array_merge(['editorplaceholder' => 'Рекоменуем указать продолжительность предоставления услуги, возможность получить учебные материалы, видеозаписи и т.п.'], \app\helpers\ckeditor\CKConfig::add_charlimit(\app\helpers\ckeditor\CKConfig::DEFAULT, 500))
                ),
                'containerOptions' => [
                        'class' => 'editor_container',
                ],
        ]); ?>
        <h4 class="lk_step_title mt20">Правила предоставления услуги</h4>
        <?= $form->field($modelform, 'special_descr')->widget(CKEditor::class, [
                'editorOptions' => ElFinder::ckeditorOptions(
                        'elfinderuser',
                        array_merge(['editorplaceholder' => 'Рекомендуем указать требования и особые условия для клиента. Например, наличие профильного образования или нацеленность на результат.'], \app\helpers\ckeditor\CKConfig::add_charlimit(\app\helpers\ckeditor\CKConfig::DEFAULT, 1200))
                ),
                'containerOptions' => [
                        'class' => 'editor_container',
                ],
        ]); ?>
    </main>
</div>

