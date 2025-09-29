<?php

use app\modules\pages\models\ServiceTypePage;
use yii\helpers\Url;

$servicetypepage = ServiceTypePage::find()->where(['model' => ServiceTypePage::class])->one();

if (!empty($items)) { ?>
    <div class="sec section-page section-popular-services gray-bg">
        <div class="container wide">
            <h3 class="section-page-title with_button">Популярные услуги <a
                        href="<?= Url::toRoute([$servicetypepage->getUrlPath(), 'random' => '1']); ?>"
                        class="button see_all">Смотреть все</a></h3>
            <?php if (!empty($services_subtitle)) { ?>
                <div class="section-page-text"><?= $services_subtitle; ?></div><?php } ?>
            <div class="services-slider default-slider-3 owl-carousel owl-theme" data-loop="<?= $loop; ?>"
                 data-autoplay="<?= $autoplay; ?>" data-timeout="<?= $autoplayTimeout; ?>">
                <?php foreach ($items as $item) { ?>
                    <div class="services-slide">
                        <div class="services-slide-autor">
                            <?php if ($item->user && $item->user->profile->image) { ?>
                                <a href="<?= $item->user->getUrlPath(); ?>" class="services-slide-autor-img">
                                    <img src="<?= $item->user->profile->getThumb('image', 'main'); ?>"
                                         alt="<?= $item->user->profile->halfname; ?>">
                                </a>
                                <div class="services-slide-autor-info">
                                    <a href="<?= $item->user->getUrlPath(); ?>"
                                       class="services-slide-autor-name"><?= $item->user->profile->halfname; ?></a>
                                    <div class="services-slide-autor-status"><?= $item->user->getRoleName(); ?></div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="services-slide-service-info">
                            <h4 class="services-slide-text" title="<?= $item->name; ?>"><a
                                        href="<?= $item->getUrlPath(); ?>"><?= $item->name; ?></a></h4>
                            <div class="services-slide-text2"><?= $item->short_description; ?></div>
                            <div class="services-slide-tags">
                                <span class="services-slide-tag"><?= $item->getKindName(); ?></span><br>
                                <?php if (!empty($item->serviceType)) { ?>
                                    <a class="tag set_filter"
                                       href="<?= Url::toRoute([$service_page->getUrlPath(), 'service_types[]' => $item->serviceType->id]); ?>"
                                       data-tagname="<?= $item->serviceType->name; ?>"><b
                                                class="tag-hovered"><?= $item->serviceType->name; ?></b><span><?= $item->serviceType->name; ?></span></a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="services-slide-banner">
                    <div class="experts-academy-banner-title">Посмотреть все услуги</div>
                    <a href="<?= Url::toRoute([$servicetypepage->getUrlPath(), 'random' => '1']); ?>"
                       class="button white">Перейти в каталог</a>
                </div>
            </div>
        </div>
    </div>
<?php } ?>