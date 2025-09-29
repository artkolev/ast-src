<?php

use app\modules\pages\models\Eventspage;

$eventspage = Eventspage::find()->where(['model' => Eventspage::class, 'visible' => 1])->one();
?>
<section class="sec content_sec">
    <div class="container">
        <section class="register_form_success">
            <div class="ib">
                <img src="/img/envelope_big.svg" alt=""/>
                <h1><?= $model->getNameForView(); ?></h1>
                <p><?= $message; ?></p>
                <a href="<?= (!empty($eventspage)) ? $eventspage->getUrlPath() : '/'; ?>"
                   class="button small">ОК<?= (!empty($eventspage)) ? '' : ', вернуться на главную'; ?></a>
            </div>
        </section>
    </div>
</section>
