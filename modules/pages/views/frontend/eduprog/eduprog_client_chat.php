<?php
/*
    @descr Сообщения по программе ДПО в ЛК клиента
    @var $model Class app\modules\pages\models\LKEduprogClientChat; текущая страница
    @action pages/eduprog/eduprog-client-chat
*/

use yii\widgets\Pjax;

?>
<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">
        <?php Pjax::begin(['id' => 'content_page', 'options' => ['class' => 'lk_maincol']]); ?>
        <?= $this->render('_client_eduprog_card', ['eduprog' => $eduprog]); ?>
        <?= $this->render('_client_submenu', ['eduprog' => $eduprog, 'model' => $model]); ?>

        <div class="lk_block">
            <main class="lk_content">
                <?= \app\modules\message\widgets\message\MessageWidget::widget(['eduprog' => $eduprog]); ?>
            </main>
        </div>
        <?php Pjax::end(); ?>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>