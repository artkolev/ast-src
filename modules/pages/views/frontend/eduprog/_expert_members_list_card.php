<?php
/*
    отображение карточки слушателя в ЛК Эксперта в списке слушателей
*/

use app\modules\eduprog\models\EduprogMember;
use app\modules\eduprogorder\models\EduprogorderItem;
use app\modules\pages\models\LKEduprogViewMemberChat;
use yii\helpers\Html;
use yii\helpers\Url;

$message_page = LKEduprogViewMemberChat::find()->where(['model' => LKEduprogViewMemberChat::class, 'visible' => 1])->one();

// найти все тарифы в действующих заказах для слушателя
$tariffs = EduprogorderItem::find()->select(['eduprog_tariff.name as tariff'])
        ->leftJoin('eduprog_order', 'eduprog_order.id = eduprog_orderitem.eduprogorder_id')
        ->leftJoin('eduprog_tariff', 'eduprog_tariff.id = eduprog_orderitem.tariff_id')
        ->where(['eduprog_order.is_payed' => 1, 'eduprog_orderitem.user_id' => $member->user_id, 'eduprog_order.eduprog_id' => $member->eduprog_id])
        ->asArray()->column();

$notifications_count = $member->eduprog->countOrganizatorMessageNotice($member->user_id);
?>

<tr <?= ($notifications_count > 0) ? 'class="table-have-notice"' : '' ?> id="member_<?= $member->id ?>">
    <td>
        <a class="name-field-for-modal-js"
           href="<?= Url::toRoute([$member_url, 'id' => $member->id]) ?>"><?= $member->memberNum ? '№' . $member->memberNum . '. ' : ''; ?><?= $member->user?->profile?->halfname; ?></a>
        <br>

        <div class="table-notice-text">
            <?php if ($notifications_count > 0) { ?>
                <div class="table-notice">+<?= $notifications_count ?></div><?php } ?>
            <?= Html::a('Сообщения', $message_page ? Url::toRoute([$message_page->getUrlPath(), 'id' => $member->id]) : false); ?>
        </div>
        <div class="mobile-visible">
            Email:
            <div class="break-word"><?= $member->user?->email; ?></div>
            <br>
            Тариф: <?= implode(', ', $tariffs); ?>
            Статус: <?= $member->statusName; ?>
            <div class="buttons">
                <?php if ($member->status == EduprogMember::STATUS_WAITING) { ?>
                    <a data-member="<?= $member->id ?>" data-status="<?= EduprogMember::STATUS_ACTIVE ?>"
                       class="button-o blue mini change-status-js">Зачислить</a>
                    <a data-action="reject" data-member="<?= $member->id ?>"
                       class="button-o blue mini show-ask-modal-js">Отклонить</a>
                <?php } ?>
                <?php if ($member->status == EduprogMember::STATUS_ACTIVE) { ?>
                    <a data-action="expell" data-member="<?= $member->id ?>"
                       class="button-o blue mini show-ask-modal-js">Отчислить</a>
                <?php } ?>
            </div>
        </div>
    </td>
    <td class="desktop-visible">
        <div class="break-word"><?= $member->user?->email; ?></div>
    </td>
    <td class="desktop-visible"><?= implode(', ', $tariffs); ?></td>
    <td class="desktop-visible">
        <?= $member->statusName; ?>
        <div class="buttons">
            <?php if ($member->status == EduprogMember::STATUS_WAITING) { ?>
                <a data-member="<?= $member->id ?>" data-status="<?= EduprogMember::STATUS_ACTIVE ?>"
                   class="button-o blue mini change-status-js">Зачислить</a>
                <a data-action="reject" data-member="<?= $member->id ?>" class="button-o blue mini show-ask-modal-js">Отклонить</a>
            <?php } ?>
            <?php if ($member->status == EduprogMember::STATUS_ACTIVE) { ?>
                <a data-action="expell" data-member="<?= $member->id ?>" class="button-o blue mini show-ask-modal-js">Отчислить</a>
            <?php } ?>
        </div>
    </td>
</tr>