<?php

use app\modules\service\models\Service;
use yii\helpers\Html;

/**
 * @var Service $items
 */

$service_catalog = app\modules\pages\models\ServiceTypePage::find()->where(['model' => app\modules\pages\models\ServiceTypePage::class, 'visible' => 1])->one();
?>

<?php if (!empty($items)) { ?>
    <?php foreach ($items as $service) { ?>
        <div class="all-services-card" onclick="window.location='<?= $service->getUrlPath(); ?>'">
            <div class="all-services-card-info">
                <?php if ($service->type == Service::TYPE_TYPICAL) { ?>
                    <div data-service="<?= $service->id; ?>" class="all-services-card-price orderCreate">
                        <?= number_format($service->price, 0, '', '&nbsp;'); ?>
                        ₽ <?php if ($service->old_price && $service->old_price > $service->price) { ?><span
                                class="service_item-price-old-price"><?= number_format($service->old_price, 0, '', '&nbsp;'); ?>
                            руб.</span><?php } ?>
                    </div>
                <?php } ?>
                <?php if ($service->type == Service::TYPE_CUSTOM) { ?>
                    <div data-service="<?= $service->id; ?>" class="all-services-card-price negotiable">
                        Цена договорная
                    </div>
                <?php } ?>
                <a href="<?= $service->getUrlPath(); ?>" class="all-services-card-title"><?= $service->name; ?></a>
                <div class="all-services-card-text"><?= $service->short_description; ?></div><!--todo 200 символов-->
            </div>
            <div class="all-services-card-author-wrapper">
                <div class="all-services-card-tags">
                    <?= Html::a($service->serviceType->name, $service_catalog->getUrlPath() . '?service_types[]=' . $service->serviceType->id, ['class' => 'all-services-card-tag', 'data-type_id' => $service->serviceType->id, 'data-type_name' => $service->serviceType->name]); ?>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } ?>