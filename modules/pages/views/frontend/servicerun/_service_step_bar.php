<?php

use app\models\ServiceForm;
use app\modules\pages\models\ServiceEdit;
use yii\helpers\Url;

/**
 * @var \yii\web\View $this
 * @var ServiceEdit $model
 * @var ServiceForm $modelform
 * @var int $step
 */

$stepNames = [
        'Описание',
        'Условия',
        'Об услуге',
        'Теги',
        'Фото и Видео',
        'Публикация',
];
?>
<div class="lk-event-reg-steps">
    <?php for ($i = 1; $i <= ServiceForm::STEP_COUNT; $i++) {
        $url = false;
        $active = $step == $i;
        if ($model && ($i < $step || $model instanceof ServiceEdit)) {
            $url = Url::toRoute([$model->getUrlPath(), 'step' => $i, 'id' => $modelform->id]);
            $active = true;
        }
        $lastStep = ($step > $i); ?>
        <div class="lk-event-reg-step <?= $active ? 'active' : ''; ?>"
             <?php if ($url) { ?>onclick="location.href='<?= $url; ?>'"<?php } ?>>
            <div class="lk-event-reg-step-num">
                <?php if ($lastStep) { ?>
                    <img src="/img/i_check1-white.svg" alt="">
                <?php } else { ?>
                    <?= $i; ?>
                <?php } ?>
            </div>
            <div class="lk-event-reg-step-name"><?= $stepNames[$i - 1]; ?></div>
        </div>
    <?php } ?>
</div>