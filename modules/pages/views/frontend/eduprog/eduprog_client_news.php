<?php
/*
    @descr Новости по программа ДПО в ЛК клиента (главная страница просмотра программы)
    @var $model Class app\modules\pages\models\LKEduprogClientNews; текущая страница
    @action pages/eduprog/eduprog-client-news
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
                <?php if (!empty($member->visibleNews)) { ?>
                    <?php foreach ($member->visibleNews as $news) {
                        $news->readNotification($member->user_id); ?>
                        <article class="chat_message_box client">
                            <div class="chat_message">
                                <?= $news->content; ?>
                                <?php if ($news->has_tariff_button) { ?>
                                    <a href="<?= Url::toRoute([$news->eduprog->getUrlPath(), '#' => 'tickets_box']); ?>"
                                       target="_blank" class="button small">Оплатить</a>
                                <?php } ?>
                            </div>
                            <div class="chat_message_date">
                                <span><?= Yii::$app->formatter->asDatetime($news->public_date, 'php:d.m.Y, H:i'); ?></span>
                                <span><?= $news->sender->profile->halfname ?></span>
                            </div>
                        </article>
                    <?php } ?>
                <?php } else { ?>
                    <p>Новости по выбранной программе еще не опубликованы</p>
                <?php } ?>
            </main>
        </div>
        <?php Pjax::end(); ?>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>