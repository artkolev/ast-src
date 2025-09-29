<?php

use app\modules\pages\models\SupportPage;
use yii\helpers\Url;

?>
<section class="sec reg-section">
    <div class="container wide">
        <div class="reg-block">
            <h1 class="page-title">Ошибка</h1>
            <div class="reg-block-text">
                При попытке восстановления пароля произошла ошибка. <?= \app\helpers\MainHelper::getHelpText() ?>
            </div>
            <?php if ($supportpagePage = SupportPage::find()->where(['model' => SupportPage::class, 'visible' => 1])->one()) { ?>
                <div class="reg-support-link-wrapper">
                    <a href="<?= Url::toRoute($supportpagePage->getUrlPath()); ?>" class="reg-support-link">Написать в
                        техподдержку</a>
                </div>
            <?php } ?>
        </div>
    </div>
</section>