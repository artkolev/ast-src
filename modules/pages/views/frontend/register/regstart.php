<?php

use app\modules\pages\models\Regexpert;
use app\modules\pages\models\Regfizusr;
use app\modules\pages\models\Regurusr;
use yii\helpers\Url;

?>
<section class="sec reg-section">
    <div class="container wide">
        <h1 class="page-title"><?= $model->getNameForView(); ?></h1>
        <div class="subheader">
            <p><?= $model->content; ?></p>
        </div>
        <div class="reg-choose-wrapper">
            <?php if ($regfizusrPage = Regfizusr::find()->where(['model' => Regfizusr::class, 'visible' => 1])->one()) { ?>
                <a href="<?= Url::toRoute($regfizusrPage->getUrlPath()); ?>" class="reg-choose">
                    <span>Зарегистрироваться</span>
                    <div class="go-to-btn"></div>
                </a>
            <?php } ?>
            <?php if ($regurusrPage = Regurusr::find()->where(['model' => Regurusr::class, 'visible' => 1])->one()) { ?>
                <a href="<?= Url::toRoute($regurusrPage->getUrlPath()); ?>" class="reg-choose">
                    <span>Зарегистрировать <br>Организацию</span>
                    <div class="go-to-btn"></div>
                </a>
            <?php } ?>
            <?php if ($regexpertPage = Regexpert::find()->where(['model' => Regexpert::class, 'visible' => 1])->one()) { ?>
                <a href="<?= Url::toRoute($regexpertPage->getUrlPath()); ?>" class="reg-choose">
                    <span>Присоединиться к <br>Экспертному <br>сообществу</span>
                    <div class="go-to-btn"></div>
                </a>
            <?php } ?>
        </div>
    </div>
</section>