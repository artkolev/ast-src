<?php

use app\models\ServiceForm;
use app\modules\service\models\Service;
use yii\widgets\ActiveForm;

/**
 * @var \yii\web\View $this
 * @var ServiceForm $modelform
 * @var ActiveForm $form
 */

?>

<div class="lk_block">
    <main class="lk_content">
        <?= $form->field($modelform, 'id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
        <?= $form->field($modelform, 'service_type', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
        <?= $form->field($modelform, 'step', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
        <h4 class="lk_step_title font20 mb15">Название услуги</h4>
        <?= $form->field($modelform, 'name', ['template' => '{label}{input}<span class="symbols_counter">' . ServiceForm::MAX_LENGTH_NAME . ' символов</span>{error}{hint}', 'options' => ['class' => 'ip_cell w100 symbols_counter_box']])->textInput(['placeholder' => 'Наименование', 'class' => "input_text limitedSybmbols", 'maxlength' => ServiceForm::MAX_LENGTH_NAME]); ?>
        <h4 class="lk_step_title font20 mb15">Подзаголовок</h4>
        <?= $form->field($modelform, 'short_description', ['template' => '{label}{input}<span class="symbols_counter">' . ServiceForm::MAX_LENGTH_SHORT_DESCRIPTION . ' символов</span>{error}{hint}', 'options' => ['class' => 'ip_cell w100 symbols_counter_box']])->textarea(['placeholder' => 'Краткое описание услуги', 'class' => "input_text limitedSybmbols", 'maxlength' => ServiceForm::MAX_LENGTH_SHORT_DESCRIPTION]); ?>
        <h4 class="lk_step_title">Выберите вид услуги*</h4>
        <?= $form->field($modelform, 'type_id', ['template' => '{input}{error}{hint}'])->dropDownList(Service::getGroupList(), ['prompt' => 'Не указан', 'class' => "pretty_select"]); ?>

        <h4 class="lk_step_title">Выберите формат услуги*</h4>
        <?= $form->field($modelform, 'kind', ['template' => '{input}{error}{hint}'])->radioList(
                Service::getKindList(),
                [
                        'item' => function ($index, $label, $name, $checked, $value) {
                            return '<div class="ip_cell mr50"><input type="radio" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked' : '') . ' class="ch" data-kind="kind_' . $value . '" tabindex="3"><label class="notmark">' . ucwords($label) . '</label></div>';
                        }
                ]
        )->label(false); ?>
    </main>
</div>