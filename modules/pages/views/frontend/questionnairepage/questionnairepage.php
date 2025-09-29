<?php

use app\modules\questionnaire\widgets\questionnaire\QuestionnaireWidget;

?>
<div class="wrapper">
    <section class="sec section-page section-testing gray_bg">
        <div class="container small780">
            <div class="lk_block">
                <div class="lk_content">
                    <h2><?= $model->name; ?></h2>
                    <p><?= $model->text; ?></p>
                </div>
            </div>
            <div class="lk_block">
                <div class="lk_content">
                    <?= QuestionnaireWidget::widget(['form_model' => $model]); ?>
                </div>
            </div>
        </div>
    </section>
</div>