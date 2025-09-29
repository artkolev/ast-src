<?php

use app\modules\pages\models\QueriesArchive;

$queries_list = QueriesArchive::find()->where(['model' => QueriesArchive::class, 'visible' => 1])->one();
$queries_url = (!empty($queries_list)) ? $queries_list->getUrlPath() : false;
?>
<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">
        <div class="lk_maincol">
            <?php if ($queries_url) { ?>
                <a href="<?= $queries_url; ?>" class="button-o back">Вернуться к запросам</a>
            <?php } ?>
            <div class="lk_order_item">
                <div class="lk_order_item_info-basic">
                    <div class="grid-template-a">
                        <div><h4>Запрос №:</h4>
                            <p><?= $query->queryNum; ?></p></div>
                        <div><h4>Отправлен:</h4>
                            <p><?= Yii::$app->formatter->asDatetime($query->created_at, 'd.MM.y'); ?></p></div>
                    </div>
                    <div class="grid-template-b">
                        <div><h4>Статус:</h4>
                            <p class="blue">Закрыт</p></div>
                        <div><h4>Клиент:</h4>
                            <p><?= $query->user->profile->halfname; ?></p></div>
                    </div>
                </div>
            </div>

            <div class="lk_block">
                <main class="lk_content lk_content-basic">
                    <p>Услуга:</p>
                    <h4><?= $query->service_name; ?></h4>
                    <?= !empty($query->service_descr) ? '<h4>' . $query->service_descr . '</h4>' : ''; ?>
                    <?php if (!empty($query->user_comment)) { ?>
                        <p>Комментарий:</p>
                        <h5><?= $query->user_comment; ?></h5>
                    <?php } ?>
                    <?php if (!empty($query->offered_datestart) or !empty($query->offered_dateend) or !empty($query->offered_price) or !empty($query->special)) { ?>
                        <p>Оговоренные условия:</p>
                        <?php if (!empty($query->offered_datestart) or !empty($query->offered_dateend) or !empty($query->offered_price)) { ?>
                            <h4>
                                Срок: <?= Yii::$app->formatter->asDatetime($query->offered_datestart, 'd.MM.y'); ?>
                                -<?= Yii::$app->formatter->asDatetime($query->offered_dateend, 'd.MM.y'); ?><br/>
                                Цена: <?= number_format($query->offered_price, 0, '', ' '); ?> руб.
                            </h4>
                        <?php } ?>
                        <?php if (!empty($query->special)) { ?>
                            <p>Особые условия:</p>
                            <h4><?= $query->special; ?></h4>
                        <?php } ?>
                    <?php } ?>
                    <p>Запрос создан:</p>
                    <h4><?= Yii::$app->formatter->asDatetime($query->created_at, 'd.MM.y'); ?></h4>

                    <p>Запрос отклонен:</p>
                    <h4><?= Yii::$app->formatter->asDatetime($query->closed_at, 'd.MM.y'); ?></h4>

                    <p>Служба клиентского сервиса закрыла запрос</p>
                    <?php if (!empty($query->comment)) { ?>
                        <h4>Комментарий: <br> <?= $query->comment; ?></h4>
                    <?php } ?>
                    <br>
                    <a href="#" class="button lk gray open_slidebox" data-slide_box="history">История запроса</a>
                </main>
            </div>
            <div id="history" class="lk_block slide_box hidden">
                <main class="lk_content">
                    <h1 class="lk_block_title">История запроса</h1>
                    <section class="timeline_box">
                        <?php foreach ($query->history as $event) { ?>
                            <article class="timeline-row">
                                <div class="timeline-date"><?= Yii::$app->formatter->asDatetime($event->created_at, 'php:d.m.Y H:i'); ?></div>
                                <div class="timeline-info">
                                    <p><?= $event->event; ?></p>
                                </div>
                            </article>
                        <?php } ?>
                    </section>
                </main>
            </div>
            <?= \app\modules\message\widgets\message\MessageWidget::widget(['query' => $query]); ?>
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>