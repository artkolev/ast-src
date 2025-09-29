<?php
/*
    @descr Новости слушателя ДПО в ЛК
    @var $model Class app\modules\pages\models\LKEduprogViewMemberNews; текущая страница
    @action pages/eduprog/eduprog-view-member-news
*/

use yii\helpers\Url;

?>

<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">
        <div class="lk_maincol">
            <?php if (!empty($parent_page)) { ?>
                <div class="ip_cell w100">
                    <a href="<?= Url::toRoute([$parent_page->getUrlPath(), 'id' => $member->eduprog_id]); ?>"
                       class="button-o back">Программа</a>
                </div>
            <?php } ?>

            <?= $this->render('_expert_member_card', ['member' => $member]); ?>
            <?= $this->render('_change_status_member_engine', ['member_type' => 'member_page']); ?>
            <?= $this->render('_expert_member_submenu', ['member' => $member, 'model' => $model]); ?>

            <div class="lk_block">
                <main class="lk_content">
                    <?php if (!empty($member->visibleNews)) {
                        foreach ($member->visibleNews as $news) { ?>
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
                        <p>Для выбранного слушателя нет опубликованных новостей</p>
                    <?php } ?>
                </main>
            </div>
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>