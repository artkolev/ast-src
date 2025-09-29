<?php
/*
    отображение меню просмотра слушателя программы в ЛК Эксперта
*/

use app\modules\pages\models\LKEduprogViewMembers;
use app\modules\pages\models\LKEduprogViewNews;
use app\modules\pages\models\LKEduprogViewNewsCreate;
use app\modules\pages\models\LKEduprogViewOrders;
use app\modules\pages\models\LKEduprogViewTrainingproc;
use app\modules\pages\models\LKEduprogViewTrainingprocCreate;
use yii\helpers\Url;

$members_page = LKEduprogViewMembers::find()->where(['model' => LKEduprogViewMembers::class, 'visible' => 1])->one();
$orders_page = LKEduprogViewOrders::find()->where(['model' => LKEduprogViewOrders::class, 'visible' => 1])->one();
$message_page = LKEduprogViewNews::find()->where(['model' => LKEduprogViewNews::class, 'visible' => 1])->one();
$training_message_page = LKEduprogViewTrainingproc::find()->where(['model' => LKEduprogViewTrainingproc::class, 'visible' => 1])->one();
$create_page = LKEduprogViewNewsCreate::find()->where(['model' => LKEduprogViewNewsCreate::class, 'visible' => 1])->one();
$create_trainingproc_page = LKEduprogViewTrainingprocCreate::find()->where(['model' => LKEduprogViewTrainingprocCreate::class, 'visible' => 1])->one();

?>
<div class="lk-dpo-submenu">
    <?php if (!empty($members_page)) { ?>
        <a href="<?= Url::toRoute([$members_page->getUrlPath(), 'id' => $eduprog->id]); ?>"
           class="lk-dpo-submenu-element<?= ($model instanceof $members_page) ? ' active' : ''; ?>">Слушатели
            программы</a>
    <?php } ?>
    <?php if (!empty($orders_page)) { ?>
        <a href="<?= Url::toRoute([$orders_page->getUrlPath(), 'id' => $eduprog->id]); ?>"
           class="lk-dpo-submenu-element<?= ($model instanceof $orders_page) ? ' active' : ''; ?>">Заказы</a>
    <?php } ?>
    <?php if (!empty($message_page)) { ?>
        <a href="<?= Url::toRoute([$message_page->getUrlPath(), 'id' => $eduprog->id]); ?>"
           class="lk-dpo-submenu-element<?= ($model instanceof $message_page) ? ' active' : ''; ?>">Новости
            программы</a>
    <?php } ?>
    <?php if (!empty($training_message_page)) { ?>
        <a href="<?= Url::toRoute([$training_message_page->getUrlPath(), 'id' => $eduprog->id]); ?>"
           class="lk-dpo-submenu-element<?= ($model instanceof $training_message_page) ? ' active' : ''; ?>">Порядок
            обучения</a>
    <?php } ?>

    <?php if (($model instanceof LKEduprogViewTrainingproc) && !empty($create_trainingproc_page)) { ?>
        <a href="<?= Url::toRoute([$create_trainingproc_page->getUrlPath(), 'eduprog_id' => $eduprog->id]); ?>"
           class="button-create-news">Создать порядок обучения</a>
    <?php } ?>

    <?php if (($model instanceof $message_page) && !empty($create_page)) { ?>
        <a href="<?= Url::toRoute([$create_page->getUrlPath(), 'eduprog_id' => $eduprog->id]); ?>"
           class="button-create-news">Создать новость</a>
    <?php } ?>
</div>