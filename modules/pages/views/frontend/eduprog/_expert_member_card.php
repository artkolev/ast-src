<?php
/*
 *   отображение карточки слушателя программы в ЛК Эксперта
 */

use app\modules\eduprog\models\EduprogMember;

?>
<div id="member_<?= $member->id ?>" class="lk-block-header-no-bg">
    <h4 class="lk_step_title font20 mb10"><?= $member->user ? $member->user->profile->halfname : 'Пользователь не найден'; ?></h4>
    <p>Статус: <?= $member->statusName; ?></p>
    <div class="buttons buttons-row">
        <?php if ($member->status == EduprogMember::STATUS_WAITING) { ?>
            <a data-member="<?= $member->id ?>" data-status="<?= EduprogMember::STATUS_ACTIVE ?>"
               class="button-o blue mini change-status-js">Зачислить</a>
            <a data-action="reject" data-member="<?= $member->id ?>" class="button-o blue mini show-ask-modal-js">Отклонить</a>
        <?php } ?>
        <?php if ($member->status == EduprogMember::STATUS_ACTIVE) { ?>
            <a data-action="expell" data-member="<?= $member->id ?>" class="button-o blue mini show-ask-modal-js">Отчислить</a>
        <?php } ?>
    </div>
</div>