<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">
        <div class="lk_maincol">
            <div class="lk_block mb10">
                <header class="lk_block_header">
                    <h1 class="lk_block_title-big"><?= $model->getNameForView(); ?></h1>
                    <?= $model->content; ?>
                </header>
            </div>
            <div class="lk_block">
                <main class="lk_content">
                    Просмотр заказа недоступен.
                </main>
            </div>
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>