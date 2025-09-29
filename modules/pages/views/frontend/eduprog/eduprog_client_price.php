<?php
/*
    @descr Стоимость участия в программе ДПО в ЛК клиента
    @var $model Class app\modules\pages\models\LKEduprogClientPrice; текущая страница
    @action pages/eduprog/eduprog-client-price
*/

use yii\helpers\Url;
use yii\widgets\Pjax;

?>
<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">
        <?php Pjax::begin(['id' => 'content_page', 'options' => ['class' => 'lk_maincol']]); ?>
        <?= $this->render('_client_eduprog_card', ['eduprog' => $eduprog]); ?>
        <?= $this->render('_client_submenu', ['eduprog' => $eduprog, 'model' => $model]); ?>
        <div class="lk_block">
            <main class="lk_content">
                <h4 class="lk_step_title font20 mb10">Стоимость обучения</h4>
                <div class="mb20">
                    <?= $eduprog->cost_text; ?>
                </div>
                <a href="<?= Url::toRoute([$eduprog->getUrlPath(), '#' => 'tickets_box']); ?>" data-pjax="0"
                   target="_blank" class="button small">К тарифам</a>
            </main>
        </div>
        <?php Pjax::end(); ?>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>