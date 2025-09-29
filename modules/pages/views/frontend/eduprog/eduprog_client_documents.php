<?php
/*
    @descr Документы учасников программы ДПО в ЛК клиента
    @var $model Class app\modules\pages\models\LKEduprogClientDocuments; текущая страница
    @action pages/eduprog/eduprog-client-documents
*/

use yii\widgets\Pjax;

?>
<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">
        <?php Pjax::begin(['id' => 'content_page', 'options' => ['class' => 'lk_maincol']]); ?>
        <?= $this->render('_client_eduprog_card', ['eduprog' => $eduprog]); ?>
        <?= $this->render('_client_submenu', ['eduprog' => $eduprog, 'model' => $model]); ?>

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
                    $has_docs = true;
                    foreach ($member->documents as $key => $doc_item) { ?>
                        <a href="<?= $member->getFile('documents', $key); ?>" data-pjax="0" target="_blank"
                           class="link_file"><?= $doc_item->name; ?></a> <br>
                    <?php }
                }
                if (!$has_docs) { ?>
                    <p>у вас нет актуальных документов</p>
                <?php } ?>
            </main>
        </div>
        <!--                 <div class="ip_cell w100 flex flex-end mb0">
                        <a href="" class="button blue medium lk">Запросить копию договора</a>
                    </div> -->
        <?php Pjax::end(); ?>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>