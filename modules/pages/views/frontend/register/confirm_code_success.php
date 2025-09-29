<?php

use app\modules\pages\models\AcademyCatalog;
use app\modules\pages\models\Eventspage;

$eventspage = Eventspage::find()->where(['model' => Eventspage::class, 'visible' => 1])->one();
$expertpage = AcademyCatalog::find()->where(['model' => AcademyCatalog::class, 'visible' => 1])->one();
?>
<section class="sec reg-section">
    <div class="container wide">
        <div class="reg-block">
            <h1 class="page-title"><?= $model->getNameForView(); ?></h1>
            <!-- Все комменты ниже для вариации с смс -->
            <div class="reg-block-text">
                <?= $model->content; ?>
            </div>
            <br><br>
            <?php if ($eventspage) { ?>
                <a href="<?= $eventspage->getUrlPath(); ?>" class="button small">Перейти к мероприятиям</a>
            <?php } ?>
            <?php if ($expertpage) { ?>
                <a href="<?= $expertpage->getUrlPath(); ?>" class="button small">Найти эксперта</a>
            <?php } ?>
            <?php if (empty($expertpage) && empty($eventspage)) { ?>
                <a href="/" class="button small">ОК, вернуться на главную</a>
            <?php } ?>
        </div>
    </div>
</section>