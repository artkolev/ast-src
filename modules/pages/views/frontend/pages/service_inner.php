<?php

use app\helpers\MainHelper;
use app\modules\service\models\Service;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var Service $model
 */

/* TODO: переделать типы услуг с 0,1,2 на online, offline, hybrid в базе */
$service_kind = '';
switch ($model->kind) {
    case Service::KIND_ONLINE:
        $service_kind = 'online';
        break;
    case Service::KIND_OFFLINE:
        $service_kind = 'offline';
        break;
    case Service::KIND_HYBRID:
        $service_kind = 'hybrid';
        break;
}

/* место оказания услуги */
$place_html = '';
if ($model->kind == Service::KIND_OFFLINE || $model->kind == Service::KIND_HYBRID) {
    $place = [];
    if (!empty($model->city)) {
        $place[] = 'г. ' . $model->city->name;
    }
    if (!empty($model->place)) {
        $place[] = $model->place;
    }
    if (!empty($place)) {
        $place_html = '<div class="event-side-info address">' . implode('<br>', $place) . '</div>';
    }
}
if (($model->kind == Service::KIND_ONLINE || $model->kind == Service::KIND_HYBRID) && !empty($model->platform)) {
    $place_html .= '<div class="event-side-info where">' . $model->platform . '</div>';
}


?>
    <main class="sec content_sec section-event-page section-service-page">
        <div class="section-event-page-preview">
            <div class="section-event-page-preview-short">
                <?php if ($model->user->canPublish()) { ?>
                    <a href="<?= $model->user->getUrlPath(); ?>#services" class="button return-catalog-btn">Посмотреть
                        все услуги<?= ($model->user->role != 'exporg') ? ' эксперта' : ''; ?></a>
                <?php } ?>
                <?php if ($model->type == Service::TYPE_TYPICAL) { ?>
                    <div class="buy-ticket-pinned">
                        <?php if (!empty((float)$model->old_price)) { ?>
                            <div class="buy-ticket-pinned-price have-old-price"><?= number_format($model->price, 0, '.', ' '); ?>
                                ₽
                                <span class="buy-ticket-pinned-old-price"><?= number_format($model->old_price, 0, '.', ' '); ?> ₽</span>
                            </div>
                        <?php } else { ?>
                            <div class="buy-ticket-pinned-price"><?= number_format($model->price, 0, '.', ' '); ?>₽
                            </div>
                        <?php } ?>
                        <div data-service="<?= $model->id; ?>" class="buy-ticket-pinned-btn orderCreate">Оплатить</div>
                    </div>
                <?php } elseif ($model->type == Service::TYPE_CUSTOM) { ?>
                    <!-- если кнопка Узнать стоимость, то удаляем buy-ticket-pinned-price и меняем текст кнопки-->
                    <div class="buy-ticket-pinned">
                        <div data-service="<?= $model->id; ?>" class="buy-ticket-pinned-btn queryCreate">Узнать
                            стоимость
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="section-event-page-preview-wrapper">
                <div class="section-event-page-preview-info">
                    <?php if ($model->serviceType) { ?>
                        <div class="section-event-page-type"><?= $model->serviceType->name; ?></div>
                    <?php } ?>
                    <h1 class="section-event-page-title"><?= $model->getNameForView(); ?></h1>
                    <div class="section-event-page-description"><?= $model->short_description; ?></div>
                    <div class="blog-expert-list mobile-visible">
                        <a href="<?= $model->user->canPublish() ? $model->user->getUrlPath() : '#!'; ?>"
                           class="blog-expert-element">
                            <div class="blog-expert-element-img">
                                <img src="<?= $model->user->profile->getThumb('image', 'main'); ?>"
                                     alt="<?= $model->user->profile->halfname; ?>" loading="lazy">
                            </div>
                            <div class="blog-expert-element-info">
                                <div class="blog-expert-element-name"><?= $model->user->profile->halfname; ?></div>
                                <div class="blog-expert-element-text"><?= $model->user->profile->about_myself; ?></div>
                            </div>
                        </a>
                    </div>
                    <div class="section-event-page-tags">
                        <a href="<?= Url::toRoute([$service_catalog->getUrlPath(), 'service_kind[]' => $service_kind]); ?>"
                           class="section-event-page-tag"><?= $model->kindName; ?></a>
                    </div>
                    <div class="event-side-info-wrapper mobile-visible">
                        <?= $place_html; ?>
                    </div>
                </div>
            </div>
            <?= ''; // \app\modules\banner\widgets\banner\PageBannerWidget::widget(['inner_page'=>'service','id'=>$model->id]); ?>
        </div>

        <div class="block-event-page">
            <div class="container wide">
                <?= $this->render('_social_box_lenta', ['model' => $model]); ?>
                <div class="blog-page-content">
                    <?= $this->render('_social_box_lenta_mobile', ['model' => $model, 'text' => 'Поделиться', 'title' => $model->getNameForView()]); ?>
                    <div class="blog-page-poster-wrapper">
                        <div class="blog-right-column desktop-visible">
                            <div class="blog-expert-list desktop-visible">
                                <a href="<?= $model->user->canPublish() ? $model->user->getUrlPath() : '#!'; ?>"
                                   class="blog-expert-element">
                                    <div class="blog-expert-element-img">
                                        <img src="<?= $model->user->profile->getThumb('image', 'main'); ?>"
                                             alt="<?= $model->user->profile->halfname; ?>" loading="lazy">
                                    </div>
                                    <div class="blog-expert-element-info">
                                        <div class="blog-expert-element-name"><?= $model->user->profile->halfname; ?></div>
                                        <div class="blog-expert-element-text"><?= $model->user->profile->about_myself; ?></div>
                                    </div>
                                </a>
                            </div>
                            <div class="event-side-info-wrapper desktop-visible">
                                <?= $place_html; ?>
                            </div>

                            <?php if (!empty($model->solvtask) || !empty($model->competence)) { ?>
                                <div class="expert_item-tags desktop-visible">
                                    <?php foreach ($model->solvtask as $item) { ?>
                                        <?= Html::a('<b class="tag-hovered">' . mb_strtolower($item->name, 'UTF-8') . '</b><span>' . mb_strtolower($item->name, 'UTF-8') . '</span></a>', $service_catalog->getUrlPath() . '?solvtask=' . urlencode($item->name), ['class' => 'tag']); ?>
                                    <?php } ?>
                                    <?php foreach ($model->competence as $item) { ?>
                                        <?= Html::a('<b class="tag-hovered">' . mb_strtolower($item->name, 'UTF-8') . '</b><span>' . mb_strtolower($item->name, 'UTF-8') . '</span></a>', $service_catalog->getUrlPath() . '?competence=' . urlencode($item->name), ['class' => 'tag']); ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?php if (!empty($model->keywords)) { ?>
                                <div class="expert_item-tags desktop-visible">
                                    <?php foreach ($model->keywords as $item) { ?>
                                        <?= Html::a('<b class="tag-hovered">' . mb_strtolower($item->name, 'UTF-8') . '</b><span>' . mb_strtolower($item->name, 'UTF-8') . '</span></a>', $service_catalog->getUrlPath() . '?keyword=' . urlencode($item->name), ['class' => 'tag']); ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?php if ($model->target_audience) { ?>
                                <div class="blog-expert-list-title desktop-visible">Целевая аудитория</div>
                                <div class="expert_item-tags desktop-visible">
                                    <?php foreach ($model->target_audience as $item) { ?>
                                        <?= Html::a('<b class="tag-hovered">' . mb_strtolower($item->name, 'UTF-8') . '</b><span>' . mb_strtolower($item->name, 'UTF-8') . '</span>', $service_catalog->getUrlPath() . '?target_audience[]=' . $item->id, ['class' => 'tag']); ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="blog-page-text-wrapper">
                        <div class="blog-page-text">
                            <?php if (!empty($model->video)) { ?>
                                <div class="eventpage_article-info">
                                    <?php if (!empty($model->videoimage)) { ?>
                                        <div class="youtube_preview">
                                            <?= MainHelper::getMultiEmbededAddress($model->video, image_url: $model->videoimage ? $model->getThumb('videoimage', 'main') : '', image_name: $model->name ?? ''); ?>
                                        </div>
                                    <?php } else { ?>
                                        <div class="youtube_preview">
                                            <?= MainHelper::getMultiEmbededAddress($model->video); ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?= $model->description; ?>
                            <?php if (!empty($model->image)) { ?>
                                <div class="eventpage_article-info masonry-block">
                                    <?php foreach ($model->image as $key => $image) { ?>
                                        <a href="<?= $image->src; ?>" data-fancybox="along" class="masonry-item"><img
                                                    src="<?= $model->image[$key]->src; ?>" alt=""/></a>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?php if (!empty($model->special_descr)) { ?>
                                <div id="rules" class="event-rules-block">
                                    <div class="event-rules-title">Правила предоставления услуги</div>
                                    <div class="event-rules-text"><?= $model->special_descr; ?></div>
                                </div>
                            <?php } ?>
                            <?php if (!empty($model->price_descr)) { ?>
                                <div class="service-special-block">
                                    <div class="service-special-title">Что входит в стоимость?</div>
                                    <div class="service-special-text"><?= $model->price_descr; ?></div>
                                </div>
                            <?php } ?>

                            <?php if (!empty($model->text)) { ?>
                                <div class="test-text-block">
                                    <div class="text-title">Текст</div>
                                    <div class="text-text"><?= $model->text; ?></div>
                                </div>
                            <?php } ?>
                            <?php if (!empty($model->date)) { ?>
                                <div class="test-text-block">
                                    <div class="text-title">Дата</div>
                                    <div class="text-text"><?= $model->date; ?></div>
                                </div>
                            <?php } ?>
                            <?php if (!empty($model->datetime)) { ?>
                                <div class="test-text-block">
                                    <div class="text-title">Дата и время</div>
                                    <div class="text-text"><?= $model->datetime; ?></div>
                                </div>
                            <?php } ?>
                            <?php if (!empty($model->file)) { ?>
                                <div class="test-text-block">
                                    <div class="text-title">Файл</div>
                                    <div class="text-text">
                                        <a href="<?= $model->file->src ?>" target="_blank" class="academy_slide2">
                                            <?= $model->file->name ?>
                                        </a>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (!empty($model->multiimage)) { ?>
                                <div class="test-text-block">
                                    <div class="text-title">изображения</div>
                                    <?php foreach ($model->multiimage as $key => $image) { ?>
                                        <?= Html::img(
                                                $model->getThumb('multiimage', 'main', $key),
                                                [
                                                        'style' => 'max-width:300px; max-height:200px',
                                                        'alt' => $image->name
                                                ]
                                        ) ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?php if (!empty($model->solvtask) || !empty($model->competence)) { ?>
    <section class="sec content_sec section-event-page3 mobile-visible">
        <div class="container wide">
            <div class="blog-right-column">
                <div class="expert_item-tags mobile-visible">
                    <?php foreach ($model->solvtask as $item) { ?>
                        <?= Html::a('<b class="tag-hovered">' . mb_strtolower($item->name, 'UTF-8') . '</b><span>' . mb_strtolower($item->name, 'UTF-8') . '</span></a>', $service_catalog->getUrlPath() . '?solvtask=' . urlencode($item->name), ['class' => 'tag']); ?>
                    <?php } ?>
                    <?php foreach ($model->competence as $item) { ?>
                        <?= Html::a('<b class="tag-hovered">' . mb_strtolower($item->name, 'UTF-8') . '</b><span>' . mb_strtolower($item->name, 'UTF-8') . '</span></a>', $service_catalog->getUrlPath() . '?competence=' . urlencode($item->name), ['class' => 'tag']); ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
<?php } ?>
<?php if ($model->keywords) { ?>
    <section class="sec content_sec section-event-page3 mobile-visible">
        <div class="container wide">
            <div class="blog-right-column">
                <div class="expert_item-tags mobile-visible">
                    <?php foreach ($model->keywords as $keyword) { ?>
                        <?= Html::a('<b class="tag-hovered">' . mb_strtolower($keyword->name, 'UTF-8') . '</b><span>' . mb_strtolower($keyword->name, 'UTF-8') . '</span>', $service_catalog->getUrlPath() . '?keyword=' . urlencode($keyword->name), ['class' => 'tag', 'data-tag_id' => $keyword->id, 'data-tag_name' => $keyword->name]); ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
<?php } ?>
<?php if (!empty($more_services)) { ?>
    <section class="sec content_sec section-event-page2">
        <div class="container wide">
            <div class="section-event-page-title2 with_button">Другие услуги <?php if ($target_audience_catalog) { ?><a
                    href="<?= $target_audience_catalog->getUrlPath(); ?>" class="button see_all">Смотреть
                        все</a><?php } ?></div>
            <div class="services-slider default-slider-3 owl-carousel owl-theme" data-loop="true" data-autoplay="true"
                 data-timeout="5000">
                <?php foreach ($more_services as $item) {
                    $service_kind = '';
                    switch ($item->kind) {
                        case Service::KIND_ONLINE:
                            $service_kind = 'online';
                            break;
                        case Service::KIND_OFFLINE:
                            $service_kind = 'offline';
                            break;
                        case Service::KIND_HYBRID:
                            $service_kind = 'hybrid';
                            break;
                    }
                    ?>
                    <div class="services-slide">
                        <div class="services-slide-autor">
                            <a href="<?= $item->user->canPublish() ? $item->user->getUrlPath() : '#!'; ?>"
                               class="services-slide-autor-img">
                                <img src="<?= $item->user->profile->getThumb('image', 'main'); ?>"
                                     alt="<?= $item->user->profile->halfname; ?>">
                            </a>
                            <div class="services-slide-autor-info">
                                <a href="<?= $item->user->canPublish() ? $item->user->getUrlPath() : '#!'; ?>"
                                   class="services-slide-autor-name"><?= $item->user->profile->halfname; ?></a>
                                <div class="services-slide-autor-status"><?= $item->user->profile->about_myself; ?></div>
                            </div>
                        </div>
                        <div class="services-slide-service-info">
                            <a href="<?= $item->getUrlPath(); ?>" class="services-slide-text"
                               title="<?= $item->name; ?>"><?= $item->name; ?></a>
                            <div class="services-slide-text2"><?= $item->short_description; ?></div>
                            <div class="services-slide-tags">
                                <a class="tag set_filter"
                                   href="<?= Url::toRoute([$service_catalog->getUrlPath(), 'service_kind[]' => $service_kind]); ?>"><b
                                            class="tag-hovered"><?= $item->kindName; ?></b><span><?= $item->kindName; ?></span></a>
                                <?php if ($item->serviceType) { ?>
                                    <a class="tag set_filter"
                                       href="<?= Url::toRoute([$service_catalog->getUrlPath(), 'service_types[]' => $item->serviceType->id]); ?>"><b
                                                class="tag-hovered"><?= $item->serviceType->name; ?></b><span><?= $item->serviceType->name; ?></span></a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>

<?php if ($footer_banner) { ?>
    <section class="sec content_sec section-event-page2">
        <div class="container wide">
            <div class="blog-page-long-banner">
                <?php if (!empty($footer_banner->link)) { ?><a href="<?= $footer_banner->link; ?>" target="_blank"
                                                               rel="nofollow"><?php } ?>
                    <img src="<?= $footer_banner->getThumb('image', 'main'); ?>" alt="<?= $footer_banner->name; ?>"
                         class="visible-over650" loading="lazy">
                    <img src="<?= $footer_banner->getThumb('image_mobile', 'main'); ?>"
                         alt="<?= $footer_banner->name; ?>" class="visible-less650" loading="lazy">
                    <?php if (!empty($footer_banner->link)) { ?></a><?php } ?>
            </div>
        </div>
    </section>
<?php } ?>

<?= \app\modules\queries\widgets\queries\QueriesWidget::widget(); ?>
<?= \app\modules\pages\widgets\ordercreate\OrderCreateWidget::widget(); ?>