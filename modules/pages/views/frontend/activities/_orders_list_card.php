<?php
/*
    отображение карточки билета в ЛК Эксперта
*/
?>
<?php if (!empty($order_item)) { ?>
    <tr class="open_result" data-result="<?= $order_item->id; ?>">
        <td class="desktop-visible"><?= Yii::$app->formatter->asDatetime($order_item->created_at, 'php:d.m.Y'); ?></td>
        <td class="desktop-visible"><?= $order_item->surname . ' ' . $order_item->surname . ' ' . $order_item->patronymic; ?></td>
        <td class="desktop-visible"><?= $order_item->email; ?></td>
        <td class="mobile-visible">
            <?= Yii::$app->formatter->asDatetime($order_item->created_at, 'php:d.m.Y'); ?><br>
            <?= $order_item->surname . ' ' . $order_item->surname . ' ' . $order_item->patronymic; ?><br>
            <?= $order_item->email; ?><br>
            <?= $order_item->phone; ?>
        </td>
        <td class="centered"><?= $order_item->phone; ?></td>
    </tr>
<?php } ?>
