<?php
/*
    @descr Страница просмотра новости для программы ДПО в ЛК Организатора
    @var $model Class app\modules\pages\models\LKEduprogViewNewsCreate; текущая страница
    @var $news_model Class app\modules\eduprog\models\News; моедль новости программы
    @var $message_page Class app\modules\pages\models\LKEduprogViewNews; страница списка новостей программы
    @var $members_page Class app\modules\pages\models\LKEduprogViewMembers; страница всех слушателей программы
    @var $member_view_page Class app\modules\pages\models\LKEduprogViewMemberNews; страница слушателя программы (просмотр новостей)
    @action pages/eduprog/eduprog-view-news-create
*/

use app\modules\eduprog\models\News;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">
        <div class="lk_maincol">
            <div class="ip_cell w100">
                <?php if ($message_page) { ?>
                    <a href="<?= Url::toRoute([$message_page->getUrlPath(), 'id' => $news_model->eduprog_id]); ?>"
                       class="button-o back">Вернуться</a>
                <?php } ?>
            </div>
            <div class="lk-block-header-no-bg">
                <h1 class="lk_block_title-big mb20"><?= $news_model->name ?></h1>
                <div class="lk-dpo-news-info">
                    <div class="lk-dpo-news-published">
                        Отправлено: <?= Yii::$app->formatter->asDatetime($news_model->public_date, 'php:d.m.Y, H:i'); ?></div>
                    <div class="lk-dpo-news-recipients">Получатели:
                        <?php if ($news_model->recipient == News::RECIPIENT_ALL) {
                            echo Html::a('Все слушатели', ($members_page ? Url::toRoute([$members_page->getUrlPath(), 'id' => $news_model->eduprog_id]) : false));
                        } elseif (!empty($news_model->recipient_members)) {
                            $member_links = [];
                            foreach ($news_model->recipient_members as $member) {
                                $member_links[] = Html::a($member->user->profile->halfname, $member_view_page ? Url::toRoute([$member_view_page->getUrlPath(), 'id' => $member->id]) : false);
                            }
                            echo implode(', ', $member_links);
                        } ?>
                    </div>
                </div>
            </div>
            <div class="lk_block">
                <main class="lk_content">
                    <div class="lk-dpo-news-page-content">
                        <?= $news_model->content; ?>
                        <?php if ($news_model->has_tariff_button) { ?>
                            <a href="<?= Url::toRoute([$news_model->eduprog->getUrlPath(), '#' => 'tickets_box']); ?>"
                               target="_blank" class="button small">Оплатить</a>
                        <?php } ?>
                    </div>
                </main>
            </div>
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>
