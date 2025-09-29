<?php

use app\models\ServiceForm;
use app\modules\service\models\Service;
use yii\widgets\ActiveForm;

/**
 * @var \yii\web\View $this
 * @var ServiceForm $modelform
 * @var ActiveForm $form
 * @var Service $original
 */
?>

<?= $form->field($modelform, 'id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
<?= $form->field($modelform, 'step', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
<?= $form->field($modelform, 'kind', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
<?= $form->field($modelform, 'service_type', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>

<?php if ($modelform->service_type == Service::TYPE_TYPICAL) { ?>
    <div class="lk_block">
        <main class="lk_content">
            <h4 class="lk_step_title mt20">Цена, руб.</h4>
            <?= $form->field($modelform, 'price')->textInput(['placeholder' => '1000', 'class' => 'input_text onlyNumber'])->label(false); ?>

            <h4 class="lk_step_title mt20">Цена без скидки, руб.</h4>
            <?= $form->field($modelform, 'old_price')->textInput(['placeholder' => 'Заполните, если нужно показать старую цену', 'class' => 'input_text onlyNumber']); ?>
        </main>
    </div>
<?php } ?>

<div class="lk_block">
    <main class="lk_content">
        <?php if ($modelform->kind == Service::KIND_OFFLINE || $modelform->kind == Service::KIND_HYBRID) { ?>
            <?= $form->field($modelform, 'city_id')->dropDownList(Service::getCityList(), ['class' => "pretty_select kind_1"]); ?>
            <?= $form->field($modelform, 'place', ['template' => '{label}{input}<span class="symbols_counter">30 символов</span>{error}{hint}', 'options' => ['class' => 'ip_cell w100 symbols_counter_box kind_1']])->textInput(['placeholder' => 'Улица или название учреждения', 'class' => "input_text limitedSybmbols", 'maxlength' => 30]); ?>
        <?php } ?>
        <?php if ($modelform->kind == Service::KIND_ONLINE || $modelform->kind == Service::KIND_HYBRID) { ?>
            <?= $form->field($modelform, 'platform')->dropDownList($original->currentPlatformList(), ['placeholder' => 'Например, Zoom или Google Meet', 'class' => "kind_0 pretty_tags_ns_max5"]); ?>
        <?php } ?>
    </main>
</div>

<div class="lk_block">
    <main class="lk_content">
        <h4 class="lk_step_title font20 mb15">Целевая аудитория</h4>
        <?= $form->field($modelform, 'target_audience')->dropDownList(Service::getTargetAudienceList(), ['class' => "pretty_select_max5", 'multiple' => 'multiple']); ?>
    </main>
</div>