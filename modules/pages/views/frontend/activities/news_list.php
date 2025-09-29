<?php

use app\modules\pages\models\LKNewsEdit;
use yii\helpers\Url;

$statuses = [
        'published' => 0,
        'waiting' => 0,
        'over' => 0,
];
$colors = [
        'published' => 'blue',
        'waiting' => 'yellow',
        'over' => 'gray',
];

foreach ($items as $news) {
    $statuses[$news->isTimeToPublish()]++;
}
$view_page = LKNewsEdit::find()->where(['model' => LKNewsEdit::class, 'visible' => 1])->one();
$view_url = (!empty($view_page) ? $view_page->getUrlPath() : false);
?>
<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">
        <div class="lk_maincol">
            <div class="lk_block mb10">
                <header class="lk_block_header">
                    <h1 class="lk_block_title-big"><?= $model->getNameForView(); ?></h1>
                    <?= $model->content; ?>
                </header>
            </div>
            <?php if (!empty($items)) { ?>
                <!-- BLOCK-->
                <div class="lk_block">
                    <main class="lk_content">
                        <div class="lk_orders_summary">
                            <div class="lk_order_sum_item">
                                <h3 class="lk_block_title">Опубликованы</h3>
                                <b class="tblue"><?= $statuses['published']; ?></b>
                            </div>
                            <div class="lk_order_sum_item">
                                <h3 class="lk_block_title">В очереди</h3>
                                <b class="tyellow"><?= $statuses['waiting']; ?></b>
                            </div>
                            <div class="lk_order_sum_item">
                                <h3 class="lk_block_title">Не опубликованы</h3>
                                <b class="tgray"><?= $statuses['over']; ?></b>
                            </div>
                        </div>
                    </main>
                </div>
                <?php foreach ($items as $item) {
                    $status = $item->isTimeToPublish(); ?>
                    <div class="lk_order_item">
                        <a href="<?= ($view_url ? Url::toRoute([$view_url, 'id' => $item->id]) : ''); ?>"
                           class="lk_order_more <?= $colors[$status]; ?>"><i class="fa fa-angle-right"></i></a>
                        <div class="lk_order_item_info">
                            <div>
                                <p>
                                    <span class="tgray">Дата новости:</span> <?= Yii::$app->formatter->asDatetime($item->created_at, 'dd.MM.y'); ?>
                                </p>
                            </div>
                            <div>
                                <p><?= $item->name; ?></p>
                            </div>
                        </div>
                        <!-- <a href="" class="button-o small lk">Назначить дедлайн</a> -->
                        <!-- <a href="" class="button small lk yellow">Подтверждаю, что услуга оказана, закрыть заказ</a> -->
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="lk_block">
                    <main class="lk_content">
                        У вас нет созданных новостей.
                    </main>
                </div>
            <?php } ?>
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>