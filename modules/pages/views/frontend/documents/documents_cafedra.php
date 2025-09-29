<?php

use app\modules\direction\models\Direction;

/**
 * @var $model
 * @var Direction $cafedra
 */
?>
<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">
        <div class="lk_maincol">
            <div class="lk_block">
                <header class="lk_block_header">
                    <h1 class="lk_block_title-big"><?= $model->getNameForView(); ?></h1>
                    <?= $model->content; ?>
                </header>
                <main class="lk_content">
                    <?php if (!is_null($cafedra) && !empty($cafedra->requirement)) { ?>
                        <a href="<?= $cafedra->getFile('requirement'); ?>"
                           class="link_file"><?= $cafedra->requirement->name; ?></a> <br>
                    <?php }
                    if (!empty($model->docs)) {
                        foreach ($model->docs as $key => $file) { ?>
                            <a href="<?= $model->getFile('docs', $key); ?>" class="link_file"><?= $file->name; ?></a>
                            <br>
                        <?php }
                    } ?>
                </main>
            </div>
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>