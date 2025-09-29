<?php
/*
    @descr Новости по программа ДПО в ЛК клиента (главная страница просмотра программы)
    @var $model Class app\modules\pages\models\LKEduprogClientNews; текущая страница
    @action pages/eduprog/eduprog-client-news
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
                <?php if (!empty($eduprog->publicTrainingproc)) { ?>
                    <?php foreach ($eduprog->publicTrainingproc as $trainingproc) { ?>
                        <article class="chat_message_box client">
                            <div class="chat_message" style="background-color: #F8F8F8;">
                                <?= $trainingproc->content; ?>
                            </div>
                            <div class="chat_message_date">
                                <span><?= Yii::$app->formatter->asDatetime($trainingproc->public_date, 'php:d.m.Y, H:i'); ?></span>
                                <span><?= $trainingproc->sender->profile->halfname ?></span>
                            </div>
                        </article>
                    <?php } ?>
                <?php } else { ?>
                    <p>Записи по выбранной программе еще не опубликованы</p>
                <?php } ?>
            </main>
        </div>
        <?php Pjax::end(); ?>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>