<?php

use app\models\ServiceForm;
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
        <h4 class="lk_step_title mt20">Кому отображать</h4>
        <?= $form->field($modelform, 'vis_for', ['template' => '{input}{error}{hint}'])->radioList(
                $modelform->getVisList(),
                [
                        'item' => function ($index, $label, $name, $checked, $value) {
                            return '<div class="ip_cell w100"><input type="radio" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked' : '') . ' class="ch" data-kind="kind_' . $value . '" tabindex="3"><label class="notmark">' . $label . '</label></div>';
                        }
                ]
        )->label(false); ?>
    </main>
</div>
