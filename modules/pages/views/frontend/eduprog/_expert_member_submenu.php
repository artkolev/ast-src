<?php
/*
    отображение меню просмотра слушателя программы в ЛК Эксперта
*/

use app\modules\pages\models\LKEduprogViewMemberChat;
use app\modules\pages\models\LKEduprogViewMemberDocs;
use app\modules\pages\models\LKEduprogViewMemberNews;
use app\modules\pages\models\LKEduprogViewMemberOrders;
use app\modules\pages\models\LKEduprogViewMemberTrainingproc;
use yii\helpers\Url;

$news_page = LKEduprogViewMemberNews::find()->where(['model' => LKEduprogViewMemberNews::class, 'visible' => 1])->one();
$trainingproc_page = LKEduprogViewMemberTrainingproc::find()->where(['model' => LKEduprogViewMemberTrainingproc::class, 'visible' => 1])->one();
$message_page = LKEduprogViewMemberChat::find()->where(['model' => LKEduprogViewMemberChat::class, 'visible' => 1])->one();
$order_page = LKEduprogViewMemberOrders::find()->where(['model' => LKEduprogViewMemberOrders::class, 'visible' => 1])->one();
$docs_page = LKEduprogViewMemberDocs::find()->where(['model' => LKEduprogViewMemberDocs::class, 'visible' => 1])->one();
?>
<div class="lk-dpo-submenu">
    <?php if (!empty($trainingproc_page)) { ?>
        <a href="<?= Url::toRoute([$trainingproc_page->getUrlPath(), 'id' => $member->id]); ?>"
           class="lk-dpo-submenu-element<?= ($model instanceof $trainingproc_page) ? ' active' : ''; ?>">Порядок
            обучения</a>
    <?php } ?>
    <?php if (!empty($news_page)) { ?>
        <a href="<?= Url::toRoute([$news_page->getUrlPath(), 'id' => $member->id]); ?>"
           class="lk-dpo-submenu-element<?= ($model instanceof $news_page) ? ' active' : ''; ?>">Новости</a>
    <?php } ?>
    <?php if (!empty($message_page)) { ?>
        <a href="<?= Url::toRoute([$message_page->getUrlPath(), 'id' => $member->id]); ?>"
           class="lk-dpo-submenu-element<?= ($model instanceof $message_page) ? ' active' : ''; ?>">Сообщения</a>
    <?php } ?>
    <?php if (!empty($order_page)) { ?>
        <a href="<?= Url::toRoute([$order_page->getUrlPath(), 'id' => $member->id]); ?>"
           class="lk-dpo-submenu-element<?= ($model instanceof $order_page) ? ' active' : ''; ?>">История платежей</a>
    <?php } ?>
    <?php if (!empty($docs_page)) { ?>
        <a href="<?= Url::toRoute([$docs_page->getUrlPath(), 'id' => $member->id]); ?>"
           class="lk-dpo-submenu-element<?= ($model instanceof $docs_page) ? ' active' : ''; ?>">Документы</a>
    <?php } ?>
</div>