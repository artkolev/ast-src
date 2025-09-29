<?php

use app\modules\users\models\Organization;

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
                    <?php switch ($type) {
                        case Organization::TYPE_OOO:
                            $relat = 'docsooo';
                            break;
                        case Organization::TYPE_IP:
                            $relat = 'docsip';
                            break;
                        case Organization::TYPE_SELFBUSY:
                            $relat = 'docsselfbusy';
                            break;
                    }
                    if (!empty($model->{$relat})) {
                        foreach ($model->{$relat} as $key => $file) { ?>
                            <a href="<?= $model->getFile($relat, $key); ?>" class="link_file"><?= $file->name; ?></a>
                            <br>
                        <?php }
                    } ?>
                </main>
            </div>
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>