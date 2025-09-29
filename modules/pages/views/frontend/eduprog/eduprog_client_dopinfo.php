<?php
/*
    @descr Дополнительная информация по программе ДПО в ЛК клиента
    @var $model Class app\modules\pages\models\LKEduprogClientDopinfo; текущая страница
    @action pages/eduprog/eduprog-client-dopinfo
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
                <h4 class="lk_step_title font20">Дополнительная информация</h4>
                <?php $has_docs = false; ?>
                <?php if (!empty($eduprog->dopinfo)) {
                    $has_docs = true;
                    foreach ($eduprog->dopinfo as $key => $info_item) { ?>
                        <a href="<?= $eduprog->getFile('dopinfo', $key); ?>" data-pjax="0" target="_blank"
                           class="link_file"><?= $info_item->name; ?></a> <br>
                    <?php } ?>
                <?php } ?>
                <?php if (!empty($member) && !empty($member->dopinfo)) {
                    foreach ($member->dopinfo as $key => $doc_item) { ?>
                        <a href="<?= $member->getFile('dopinfo', $key); ?>" data-pjax="0" target="_blank"
                           class="link_file"><?= $doc_item->name; ?></a> <br>
                    <?php }
                } ?>
            </main>
        </div>
        <?php Pjax::end(); ?>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>