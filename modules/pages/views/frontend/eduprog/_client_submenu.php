<?php
/*
    отображение меню просмотра программы в ЛК Клиента
*/

use app\helpers\MainHelper;
use app\modules\eduprog\models\EduprogMember;
use app\modules\pages\models\LKEduprogClientChat;
use app\modules\pages\models\LKEduprogClientContent;
use app\modules\pages\models\LKEduprogClientDocuments;
use app\modules\pages\models\LKEduprogClientDopinfo;
use app\modules\pages\models\LKEduprogClientNews;
use app\modules\pages\models\LKEduprogClientOrder;
use app\modules\pages\models\LKEduprogClientPrice;
use app\modules\pages\models\LKEduprogClientTrainingproc;
use yii\helpers\Url;

$member = $eduprog->getMemberByUser(Yii::$app->user->identity->id);
// новости и сообщения отображаются только для зачисленных пользователей
$news_page = ($member->status == EduprogMember::STATUS_ACTIVE) ? LKEduprogClientNews::find()->where(['model' => LKEduprogClientNews::class, 'visible' => 1])->one() : false;
$trainingproc_page = LKEduprogClientTrainingproc::find()->where(['model' => LKEduprogClientTrainingproc::class, 'visible' => 1])->one();
$message_page = ($member->status == EduprogMember::STATUS_ACTIVE) ? LKEduprogClientChat::find()->where(['model' => LKEduprogClientChat::class, 'visible' => 1])->one() : false;
$price_page = LKEduprogClientPrice::find()->where(['model' => LKEduprogClientPrice::class, 'visible' => 1])->one();
$order_page = LKEduprogClientOrder::find()->where(['model' => LKEduprogClientOrder::class, 'visible' => 1])->one();
$docs_page = LKEduprogClientDocuments::find()->where(['model' => LKEduprogClientDocuments::class, 'visible' => 1])->one();
$content_page = LKEduprogClientContent::find()->where(['model' => LKEduprogClientContent::class, 'visible' => 1])->one();
$dopinfo_page = LKEduprogClientDopinfo::find()->where(['model' => LKEduprogClientDopinfo::class, 'visible' => 1])->one();

$structure = MainHelper::cleanInvisibleMultifield($eduprog->structure);
?>

<div class="lk-dpo-submenu">
    <?php if (!empty($content_page) && !empty($structure)) { ?>
        <a href="<?= Url::toRoute([$content_page->getUrlPath(), 'id' => $eduprog->id]); ?>"
           class="lk-dpo-submenu-element<?= ($model instanceof $content_page) ? ' active' : ''; ?>">Содержание
            программы</a>
    <?php } ?>
    <?php if (!empty($trainingproc_page)) { ?>
        <a href="<?= Url::toRoute([$trainingproc_page->getUrlPath(), 'id' => $eduprog->id]); ?>"
           class="lk-dpo-submenu-element<?= ($model instanceof $trainingproc_page) ? ' active' : ''; ?>">Порядок
            обучения</a>
    <?php } ?>
    <?php if (!empty($news_page)) { ?>
        <a href="<?= Url::toRoute([$news_page->getUrlPath(), 'id' => $eduprog->id]); ?>"
           class="lk-dpo-submenu-element<?= ($model instanceof $news_page) ? ' active' : ''; ?>">Новости</a>
    <?php } ?>
    <?php if (!empty($message_page)) { ?>
        <a href="<?= Url::toRoute([$message_page->getUrlPath(), 'id' => $eduprog->id]); ?>"
           class="lk-dpo-submenu-element<?= ($model instanceof $message_page) ? ' active' : ''; ?>">Сообщения</a>
    <?php } ?>
    <?php if (!empty($price_page)) { ?>
        <a href="<?= Url::toRoute([$price_page->getUrlPath(), 'id' => $eduprog->id]); ?>"
           class="lk-dpo-submenu-element<?= ($model instanceof $price_page) ? ' active' : ''; ?>">Стоимость обучения и
            условия участия</a>
    <?php } ?>
    <?php if (!empty($order_page)) { ?>
        <a href="<?= Url::toRoute([$order_page->getUrlPath(), 'id' => $eduprog->id]); ?>"
           class="lk-dpo-submenu-element<?= ($model instanceof $order_page) ? ' active' : ''; ?>">Платежи</a>
    <?php } ?>
    <?php if (!empty($docs_page)) { ?>
        <a href="<?= Url::toRoute([$docs_page->getUrlPath(), 'id' => $eduprog->id]); ?>"
           class="lk-dpo-submenu-element<?= ($model instanceof $docs_page) ? ' active' : ''; ?>">Документы</a>
    <?php } ?>
    <?php if (!empty($dopinfo_page)) { ?>
        <a href="<?= Url::toRoute([$dopinfo_page->getUrlPath(), 'id' => $eduprog->id]); ?>"
           class="lk-dpo-submenu-element<?= ($model instanceof $dopinfo_page) ? ' active' : ''; ?>">Дополнительная
            информация</a>
    <?php } ?>
</div>