<?php
/*
    @descr Текст о необходимости регистрации на маркетплейс или лицензируемых услуг
    @var $model Class app\modules\pages\models\LKEduprogEdit; текущая страница
    @var $content text выводимое сообщение
    @action pages/eduprog/eduprogedit
*/
?>
<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">
        <div class="lk_maincol">
            <div class="lk_block">
                <header class="lk_block_header">
                    <h1 class="lk_block_title-big mb0"><?= $model->getNameForView(); ?></h1>
                </header>
                <div class="lk_block">
                    <main class="lk_content lk_content-basic">
                        <?= $content; ?>
                    </main>
                </div>
            </div>
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>