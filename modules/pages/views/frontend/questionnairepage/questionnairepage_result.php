<?php

use app\modules\questionnaire\widgets\questionnaire\QuestionnaireResultWidget;

?>
<div class="wrapper">
    <section class="sec section-page section-testing gray_bg">
        <div class="container small780">
            <div class="lk_block">
                <div class="lk_content" style="overflow-wrap: break-word;">
                    <h2><?= $model->questionnaire->name; ?></h2>
                    <p><?= $model->questionnaire->letter_text; ?></p>
                </div>
            </div>
            <?= QuestionnaireResultWidget::widget(['form_model' => $model]); ?>
        </div>
    </section>
</div>