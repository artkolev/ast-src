<section class="sec content_sec">
    <div class="container small670">
        <div class="form_content_fosform">
            <h2><?= $model->getNameForView(); ?></h2>
            <?= $parent->content; ?>
            <div class="subheader"><?= $model->content; ?></div>
        </div>
        <?= \app\modules\formsresult\widgets\formsresult\FormsresultWidget::widget(['form_model' => $model]); ?>
    </div>
</section>