<?php
/*
    @descr Сообщения слушателя ДПО в ЛК
    @var $model Class app\modules\pages\models\LKEduprogViewMemberChat; текущая страница
    @action pages/eduprog/eduprog-view-member-chat
*/

use yii\helpers\Url;

?>

<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">
        <div class="lk_maincol">
            <?php if (!empty($parent_page)) { ?>
                <div class="ip_cell w100">
                    <a href="<?= Url::toRoute([$parent_page->getUrlPath(), 'id' => $member->eduprog_id]); ?>"
                       class="button-o back">Программа</a>
                </div>
            <?php } ?>

            <?= $this->render('_expert_member_card', ['member' => $member]); ?>
            <?= $this->render('_change_status_member_engine', ['member_type' => 'member_page']); ?>
            <?= $this->render('_expert_member_submenu', ['member' => $member, 'model' => $model]); ?>

            <div class="lk_block">
                <main class="lk_content">
                    <?= \app\modules\message\widgets\message\MessageWidget::widget(['eduprog' => $member->eduprog, 'eduprog_user_id' => $member->user_id]); ?>
                </main>
            </div>
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>