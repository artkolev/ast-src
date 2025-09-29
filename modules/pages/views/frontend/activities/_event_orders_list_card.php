<?php
/*
    отображение карточки заказа из формы в ЛК Эксперта
*/
?>
<?php if (!empty($event_order_item)) { ?>
    <tr class="open_info" data-ticket="<?= $event_order_item->id; ?>">
        <td class="desktop-visible"><?= Yii::$app->formatter->asDatetime($event_order_item->created_at, 'php:d.m.Y'); ?></td>
        <td class="desktop-visible"><?= $event_order_item->name; ?></td>
        <td class="desktop-visible"><?= ($event_order_item->payment ? $event_order_item->payment->statusName : ($event_order_item->is_payed ? 'Бесплатно' : 'Платёж не создан')) ?></td>
        <td class="desktop-visible"><?= $event_order_item->user->profile->halfname; ?></td>
        <td class="desktop-visible"><?= $event_order_item->user->profile->phone; ?></td>
        <td class="mobile-visible">
            <?= Yii::$app->formatter->asDatetime($event_order_item->created_at, 'php:d.m.Y'); ?> <br>
            <?= $event_order_item->name; ?> <br>
            <?= ($event_order_item->payment ? $event_order_item->payment->statusName : ($event_order_item->is_payed ? 'Бесплатно' : 'Платёж не создан')) ?>
            <br>
            <?= $event_order_item->user->profile->halfname; ?> <br>
            <?= $event_order_item->user->profile->phone; ?>
        </td>
        <td class="centered"><?= number_format($event_order_item->price, 0, '.', '&nbsp;'); ?> ₽</td>
    </tr>
<?php } ?>
