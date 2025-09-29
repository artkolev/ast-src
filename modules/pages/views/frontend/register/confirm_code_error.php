<?php

use app\modules\pages\models\Regfizusr;
use app\modules\pages\models\SupportPage;
use yii\helpers\Url;

?>
<section class="sec reg-section">
    <div class="container wide">
        <div class="reg-block">
            <h1 class="page-title">Ошибка</h1>
            <!-- Все комменты ниже для вариации с смс -->
            <div class="reg-block-text">
                <?= $error; ?>
                <?= \app\helpers\MainHelper::getHelpText() ?>
                <!-- Мы отправили SMS c кодом на номер -->
            </div>
            <?php if ($supportpagePage = SupportPage::find()->where(['model' => SupportPage::class, 'visible' => 1])->one()) { ?>
                <div class="reg-support-link-wrapper">
                    <a href="<?= Url::toRoute($supportpagePage->getUrlPath()); ?>" class="reg-support-link">Написать в
                        техподдержку</a>
                </div>
            <?php } ?>
        </div>
        <?php if ($regfizusrPage = Regfizusr::find()->where(['model' => Regfizusr::class, 'visible' => 1])->one()) { ?>
            <div class="first-visit">
                Если вы впервые на сайте, <a href="<?= $regfizusrPage->getUrlPath(); ?>">зарегистрируйтесь</a>
            </div>
        <?php } ?>
    </div>
</section>