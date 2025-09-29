<?php
/*
    @descr Содержимое программы ДПО в ЛК клиента
    @var $model Class app\modules\pages\models\LKEduprogClientContent; текущая страница
    @action pages/eduprog/eduprog-client-content
*/

use app\helpers\MainHelper;
use yii\widgets\Pjax;

?>
<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">
        <?php Pjax::begin(['id' => 'content_page', 'options' => ['class' => 'lk_maincol']]); ?>
        <?= $this->render('_client_eduprog_card', ['eduprog' => $eduprog]); ?>
        <?= $this->render('_client_submenu', ['eduprog' => $eduprog, 'model' => $model]); ?>
        <div class="lk_block">
            <main class="lk_content">
                <?php $structure = MainHelper::cleanInvisibleMultifield($eduprog->structure);
                if (!empty($structure)) { ?>
                    <h4 class="lk_step_title font20">Структура программы</h4>
                    <div class="accordion_box faq_box">
                        <?php foreach ($structure as $item) { ?>
                            <div class="accordion_item">
                                <h5 class="accordion_title"><?= $item['name']; ?></h5>
                                <div class="accordion_desc"><?= $item['content']; ?></div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </main>
        </div>
        <?php Pjax::end(); ?>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>