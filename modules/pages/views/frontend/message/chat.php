<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">
        <div class="lk_maincol">
            <div class="lk_block">
                <header class="lk_block_header">
                    <h1 class="lk_block_title-big"><?= $model->getNameForView(); ?></h1>
                    <?= $model->content; ?>
                </header>
            </div>
            <?= \app\modules\message\widgets\message\MessageWidget::widget(['chat_id' => $chat->id]); ?>
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>