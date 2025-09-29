<?php

use app\models\ServiceForm;
use app\modules\keywords\widgets\KeywordWidget;
use app\modules\service\models\Service;
use yii\widgets\ActiveForm;

/**
 * @var \yii\web\View $this
 * @var ServiceForm $modelform
 * @var ActiveForm $form
 * @var Service $original
 */

$moderation = $original->currentModeration;
?>

<?= $form->field($modelform, 'id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
<?= $form->field($modelform, 'step', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>

<div class="lk_block">
    <main class="lk_content">
        <h4 class="lk_step_title mt20">Укажите ключевые слова</h4>
        <p>Выберите из предложенных и при необходимости добавьте свои. Это нужно для более быстрого и удобного поиска
            услуги на сайте.</p>
        <?= $form->field($modelform, 'competence', ['options' => ['class' => 'mt20']])->dropDownList(Service::getCompetenceList(), ['placeholder' => 'Выберите из списка', 'class' => "pretty_tags_ns_max5", 'multiple' => 'multiple']); ?>
        <?= $form->field($modelform, 'solvtask', ['options' => ['class' => 'mt20 mb40']])->dropDownList(Service::getSolvtaskList(), ['placeholder' => 'Выберите из списка', 'class' => "pretty_tags_ns_max5", 'multiple' => 'multiple']); ?>
        <?= $form->field($modelform, 'keywords')->widget(KeywordWidget::class)->label(''); ?>
    </main>
</div>