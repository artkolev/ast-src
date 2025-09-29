<?php
/*
    отображение карточки слушателя в ЛК Эксперта в списке слушателей
*/

use app\modules\eduprog\models\EduprogMember;
use app\modules\eduprogorder\models\EduprogorderItem;

?>
<?php if (!empty($order_item)) { ?>
    <div class="moderator-table-tr">
        <div class="moderator-table-body table-width-default"><?= $order_item->order?->orderNum ?></div>
        <div class="moderator-table-body table-width-default"><?= $order_item->itemNum ?></div>
        <div class="moderator-table-body table-width-middle"><?= $order_item->order?->user?->profile?->halfname ?></div>
        <div class="moderator-table-body table-width-middle"><?= $order_item->order?->user?->email ?></div>
        <div class="moderator-table-body table-width-default"><?= (!empty($order_item->order?->payment?->payment_date) ? Yii::$app->formatter->asDatetime($order_item->order?->payment?->payment_date, 'd MMMM y') : ''); ?></div>
        <div class="moderator-table-body table-width-default"><?= (!empty($order_item->order?->payment?->refund_date) ? Yii::$app->formatter->asDatetime($order_item->order?->payment?->refund_date, 'd MMMM y') : ''); ?></div>
        <div class="moderator-table-body table-width-default"><?= $order_item->statusName ?></div>
        <div class="moderator-table-body table-width-long"><?= $order_item->tariff?->name ?></div>
        <div class="moderator-table-body table-width-middle"><?= $order_item->user?->profile?->halfname ?></div>
        <div class="moderator-table-body table-width-middle"><?= $order_item->user?->email ?></div>
        <div class="moderator-table-body table-width-default"><?= number_format($order_item->price, 0, '.', ' ') ?></div>
        <div class="moderator-table-body table-width-default"><?= number_format($order_item->order?->price, 0, '.', ' ') ?></div>
    </div>
<?php } ?>