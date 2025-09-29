<?php

use app\modules\pages\models\Directs;

$directs = Directs::find()->where(['model' => Directs::class, 'visible' => 1])->one();
?>
<section class="sec content_sec">
    <div class="container">
        <section class="register_form_success">
            <div class="ib">
                <img src="/img/envelope_big.svg" alt=""/>
                <h1><?= $model->getNameForView(); ?></h1>
                <?= $model->content; ?>
            </div>
        </section>
    </div>
</section>
