<?php
if (!empty($model->widget_code)) {
    Yii::$app->controller->view->registerJsFile($model->widget_code, ['position' => yii\web\View::POS_END]);
}
?>
<main class="sec content_sec">
    <div class="container">
        <section class="shrinked_section">
            <h1><?= $model->getNameForView(); ?></h1>
            <?= $model->content; ?>
        </section>
    </div>
</main>