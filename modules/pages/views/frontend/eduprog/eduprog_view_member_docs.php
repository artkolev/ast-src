<?php
/*
    @descr Документы слушателя ДПО в ЛК 
    @var $model Class app\modules\pages\models\LKEduprogViewMemberDocs; текущая страница
    @action pages/eduprog/eduprog-view-member-docs
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
                    <h4 class="lk_step_title font20">Документы</h4>
                    <?php $has_docs = false; ?>
                    <?php if (!empty($contracts)) {
                        $has_docs = true;
                        foreach ($contracts as $contract_item) { ?>
                            <a href="<?= $contract_item->getPdfLink(); ?>" data-pjax="0" target="_blank"
                               class="link_file"><?= $contract_item->name; ?></a> <br>
                        <?php } ?>
                    <?php } ?>
                    <?php if (!empty($member) && !empty($member->documents)) {
                        foreach ($member->documents as $key => $doc_item) { ?>
                            <a href="<?= $member->getFile('documents', $key); ?>" data-pjax="0" target="_blank"
                               class="link_file"><?= $doc_item->name; ?></a> <br>
                        <?php }
                    }
                    if (!$has_docs) { ?>
                        <p>Актуальные документы не найдены</p>
                    <?php } ?>
                </main>
            </div>
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>