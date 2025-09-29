<?php

use app\helpers\MainHelper;
use app\modules\events\models\Events;
use app\widgets\login\LoginWidget;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var Events $model
 */

[$min_price, $form_min_price] = $model->priceBadge;

$eventHolder = $model->eventCardStatusText();
$itemClassList = [
        ($model->author_id == 0 || $model->author->profile->is_academy) ? 'academy' : false,
        $eventHolder->holder ? 'soldout' : false
];
$itemClassListStr = trim(implode(' ', $itemClassList));
?>
    <main class="sec content_sec section-event-page">
        <!-- если мероприятие завершено (soldout) - добавить класс soldout -->
        <div class="section-event-page-preview <?= $itemClassListStr; ?>">
            <!-- мобильная обложка здесь (если вдруг они разные) -->
            <div class="section-event-page-preview-img mobile-visible">
                <img src="<?= $model->getThumb('image', 'page_inner'); ?>"
                     alt="<?= str_replace('"', '&quot;', $model->name); ?>" loading="lazy">
                <?= $model->age ? '<div class="event-side-age-limit mobile-visible">' . $model->age->name . '</div>' : ''; ?>
                <?php if ($eventHolder->holder) { ?>
                    <!-- если soldout - выводить лейбл, если билетов нет, добавить класс closed -->
                    <div class="soldout-label closed"><?= $eventHolder->holder_text; ?></div>
                <?php } ?>
            </div>
            <div class="section-event-page-preview-short">
                <div class="event-page-short date">
                    <?= $model->getEventDateForView(false); ?>
                </div>
                <div class="event-page-short time"><?= $model->event_time_start; ?>
                    - <?= $model->event_time_end; ?></div>
                <!-- в ссылку указывать якорь до блока с покупкой билетов -->
                <?php if ($min_price && $model->statusFull != 'archive') { ?>
                    <a href="#tickets_box" class="buy-ticket-pinned anchor">
                        <div class="buy-ticket-pinned-price"><?= $min_price; ?></div>
                        <div
                                class="buy-ticket-pinned-btn"><?= ($min_price == 'Бесплатно' ? 'Оформить билет' : 'Купить билет'); ?></div>
                    </a>
                <?php } ?>
                <?= ''; /*
            <?php if($min_price) { ?>
                <a href="#tickets_box" class="buy-ticket-pinned anchor">
                    <div class="buy-ticket-pinned-price"><?=$min_price?></div>
                    <div class="buy-ticket-pinned-btn"><?=$model->statusFull == 'archive'?'Sold out':($min_price=='Бесплатно'?'Бесплатно':'Купить билет')?></div>
                </a>
            <?php } else if ($model->need_tariff && ($min_price === false)) { ?>
                <a href="#tickets_box" class="buy-ticket-pinned anchor">
                    <div class="buy-ticket-pinned-btn">Sold out</div>
                </a>
            <?php } */ ?>
            </div>
            <div class="section-event-page-preview-wrapper">
                <!-- десктоп обложка -->
                <div class="section-event-page-preview-img desktop-visible">
                    <img src="<?= $model->getThumb('image', 'page_inner'); ?>"
                         alt="<?= str_replace('"', '&quot;', $model->name); ?>" loading="lazy">
                    <?php if ($eventHolder->holder) { ?>
                        <!-- если soldout - выводить лейбл, если билетов нет, добавить класс closed -->
                        <div class="soldout-label closed"><?= $eventHolder->holder_text; ?></div>
                    <?php } ?>
                </div>
                <div class="section-event-page-preview-info">
                    <div class="section-event-page-type"><?= $model->format ? $model->format->name : ''; ?></div>
                    <h1 class="section-event-page-title"><?= $model->getNameForView(); ?></h1>
                    <div class="section-event-page-description"><?= $model->anons; ?></div>
                    <div class="blog-expert-list mobile-visible">
                        <?php $author = $model->getAuthorForView(); ?>
                        <a href="<?= $author['link']; ?>" class="blog-expert-element">
                            <div class="blog-expert-element-img">
                                <img src="<?= $author['image']; ?>" alt="<?= $author['name']; ?>" loading="lazy">
                            </div>
                            <div class="blog-expert-element-info">
                                <div class="blog-expert-element-name"><?= $author['name']; ?></div>
                                <div class="blog-expert-element-text"><?= $author['about']; ?></div>
                            </div>
                        </a>
                    </div>
                    <div class="section-event-page-tags">
                        <a <?= $events_catalog ? 'href="' . $events_catalog->getUrlPath() . '?event_format[]=' . $model->type . '"' : ''; ?>
                                class="section-event-page-tag"><?= $model->typeName; ?></a>
                    </div>
                    <?php $place_data = $model->getPrettyPlaceForView(); ?>
                    <?php if (!empty($place_data)) { ?>
                        <div class="event-side-info-wrapper mobile-visible">
                            <?= (!empty($place_data['offline'])) ? '<div class="event-side-info address">' . $place_data['offline'] . '</div>' : ''; ?>
                            <?= (!empty($place_data['online'])) ? '<div class="event-side-info where">' . $place_data['online'] . '</div>' : ''; ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="block-event-page">
            <div class="container wide">
                <?= $this->render('_social_box_lenta', ['model' => $model]); ?>
                <div class="blog-page-content">
                    <?= $this->render('_social_box_lenta_mobile', ['model' => $model, 'text' => 'Поделиться', 'title' => $model->getNameForView()]); ?>
                    <div class="blog-page-poster-wrapper">
                        <div class="blog-right-column desktop-visible">
                            <div class="blog-expert-list desktop-visible">
                                <a href="<?= $author['link']; ?>" class="blog-expert-element">
                                    <div class="blog-expert-element-img">
                                        <img src="<?= $author['image']; ?>" alt="<?= $author['name']; ?>"
                                             loading="lazy">
                                    </div>
                                    <div class="blog-expert-element-info">
                                        <div class="blog-expert-element-name"><?= $author['name']; ?></div>
                                        <div class="blog-expert-element-text"><?= $author['about']; ?></div>
                                    </div>
                                </a>
                            </div>
                            <?php if (!empty($place_data)) { ?>
                                <div class="event-side-info-wrapper desktop-visible">
                                    <?= (!empty($place_data['offline'])) ? '<div class="event-side-info address">' . $place_data['offline'] . '</div>' : ''; ?>
                                    <?= (!empty($place_data['online'])) ? '<div class="event-side-info where">' . $place_data['online'] . '</div>' : ''; ?>
                                </div>
                            <?php } ?>
                            <?php if ($model->tags) { ?>
                                <div class="expert_item-tags desktop-visible">
                                    <?php foreach ($model->tags as $tag) { ?>
                                        <?= Html::a('<b class="tag-hovered">' . mb_strtolower($tag->name, 'UTF-8') . '</b><span>' . mb_strtolower($tag->name, 'UTF-8') . '</span>', $events_catalog->getUrlPath() . '?tag=' . urlencode($tag->name), ['class' => 'tag', 'data-tag_id' => $tag->id, 'data-tag_name' => $tag->name]); ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?php if ($model->keywords) { ?>
                                <div class="expert_item-tags desktop-visible">
                                    <?php foreach ($model->keywords as $keyword) { ?>
                                        <?= Html::a('<b class="tag-hovered">' . mb_strtolower($keyword->name, 'UTF-8') . '</b><span>' . mb_strtolower($keyword->name, 'UTF-8') . '</span>', $events_catalog->getUrlPath() . '?keyword=' . urlencode($keyword->name), ['class' => 'tag', 'data-tag_id' => $keyword->id, 'data-tag_name' => $keyword->name]); ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?= $model->age ? '<div class="event-side-age-limit desktop-visible">' . $model->age->name . '</div>' : ''; ?>
                        </div>
                    </div>
                    <div class="blog-page-text-wrapper">
                        <div class="blog-page-text">
                            <?php if (!empty($model->video1_link)) { ?>
                                <?php if (!empty($model->video1)) { ?>
                                    <div class="youtube_preview">
                                        <?= $model->video1_name ? '<div class="podpis">' . $model->video1_name . '</div>' : ''; ?>
                                        <?= MainHelper::getMultiEmbededAddress($model->video1_link, image_url: $model->video1 ? $model->getThumb('video1', 'main') : '', image_name: $model->video1_name ?? ''); ?>
                                    </div>
                                <?php } else { ?>
                                    <div class="youtube_preview">
                                        <?= MainHelper::getMultiEmbededAddress($model->video1_link); ?>
                                        <?= MainHelper::getMultiEmbededAddress($model->video1_link); ?>
                                        <?= ($model->video1_name ? '<div class="podpis">' . $model->video1_name . '</div>' : ''); ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                            <?= $model->content; ?>
                            <?php if (!empty($model->video2_link)) { ?>
                                <?php if (!empty($model->video2)) { ?>
                                    <div class="youtube_preview">
                                        <?= ($model->video2_name ? '<div class="podpis">' . $model->video2_name . '</div>' : ''); ?>
                                        <?= MainHelper::getMultiEmbededAddress($model->video2_link, image_url: $model->video2 ? $model->getThumb('video2', 'main') : '', image_name: $model->video2_name ?? ''); ?>
                                    </div>
                                <?php } else { ?>
                                    <div class="youtube_preview">
                                        <?= ($model->video2_name ? '<div class="podpis">' . $model->video2_name . '</div>' : ''); ?>
                                        <?= MainHelper::getMultiEmbededAddress($model->video2_link); ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                            <?php if (!empty($model->report)) { ?>
                                <?php if (!empty($model->report_title)) { ?>
                                    <h2><?= $model->report_title; ?></h2>
                                <?php } ?>
                                <div class="eventpage_article-info masonry-block">
                                    <?php foreach ($model->report as $key => $image) { ?>
                                        <a href="<?= $image->src; ?>" data-fancybox="gallery" class="masonry-item"><img
                                                    src="<?= $model->getThumb('report', 'main', $key); ?>"
                                                    alt="<?= str_replace('"', '&quot;', $image->name); ?>"/></a>
                                    <?php } ?>
                                </div>
                                <?php if (!empty($model->report_sub)) { ?>
                                    <div class="podpis"><?= $model->report_sub; ?></div>
                                <?php } ?>
                            <?php } ?>
                            <?php if (!empty($model->dop_content)) { ?>
                                <div id="rules" class="event-rules-block">
                                    <div class="event-rules-title">Правила проведения мероприятия</div>
                                    <div class="event-rules-text"><?= $model->dop_content; ?></div>
                                </div>
                            <?php } ?>
                            <?php if (!empty($model->forms)) { ?>
                                <div class="mt30">
                                    <?php foreach ($model->forms as $form) {
                                        if ($form->hide_button) {
                                            continue;
                                        } ?>
                                        <a class="btn button" target="blank"
                                           href="<?= $form->getUrlPath(); ?>"><?= $form->button_name; ?></a>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php /*if ($model->getLastChangedateForFront()) {
                    $old_date = $model->getLastChangedateForFront();?>
                    <div class="blog-page-text-wrapper">
                        <div class="bilets-end">
                            <div class="bilets-end-title">Время проведения мероприятия было перенесено</div>
                            <div class="bilets-end-text">с <?=app\helpers\MainHelper::printDateRange($old_date, 'old_event_date','old_event_date_end');?> <?=$old_date->old_event_time_start;?> - <?=$old_date->old_event_time_end;?> <br>на <?=app\helpers\MainHelper::printDateRange($model);?> <?=$model->event_time_start;?> - <?=$model->event_time_end;?></div>
                        </div>
                    </div>
                <?php }*/ ?>
                    <?php if ($model->need_tariff) { ?>
                        <div id="tickets_box" class="blog-page-text-wrapper">
                            <?php /* если мероприятие отменено - формы не выводим. */
                            if ($model->status == Events::STATUS_CANCELLED) { ?>
                                <div class="bilets-end">
                                    <div class="bilets-end-title">Мероприятие отменено</div>
                                    <div class="bilets-end-text">Для возврата купленных билетов направьте заявку в
                                        свободной форме на <a href="mailto:help@ast-academy.ru">help@ast-academy.ru</a>
                                    </div>
                                </div>
                                <?php
                                /* если нет ни одного доступного тарифа */
                            } elseif (($min_price === false) or ($model->statusFull == 'archive')) { ?>
                                <div class="bilets-end">
                                    <div class="bilets-end-title">Продажа билетов завершена</div>
                                    <div class="bilets-end-text">Все билеты распроданы</div>
                                </div>
                                <?php
                                /* иначе выводим формы */
                            } else {
                                /* смотрим сколько форм доступно к продаже */
                                $total_forms = [];
                                foreach ($model->tariff_list as $tariff) {
                                    if (in_array($tariff->event_form_id, $total_forms)) {
                                        continue;
                                    }
                                    if ($model->canBuyTarif($tariff, 1)) {
                                        $total_forms[] = $tariff->event_form_id;
                                    }
                                }
                                /* если несколько форм */
                                if (count($total_forms) > 1) { ?>
                                    <div class="buy-tickets-box">
                                        <div class="buy-tickets-title">Выберите билеты</div>
                                        <div
                                                class="buy-tickets-date"><?= ($model->format ? $model->format->name : ''); ?> <?= $model->name; ?></div>
                                        <div class="buy-tickets-tabs">
                                            <?php
                                            $active_tab = true;
                                            foreach ($model->eventsForms as $form_event) {
                                                // если форма не входит в список отображаемых - пропускаем.
                                                if (!in_array($form_event->id, $total_forms)) {
                                                    continue;
                                                } ?>
                                                <div
                                                        class="buy-tickets-tab tab-trigger<?= $active_tab ? ' active' : ''; ?>"
                                                        data-tab="tickets_box_<?= $form_event->id; ?>"><?= $form_event->name; ?></div>
                                                <?php $active_tab = false; ?>
                                            <?php } ?>
                                        </div>
                                        <div class="tariff-tabs-content">
                                            <?php
                                            $active_tab = true;
                                            foreach ($model->eventsForms as $form_event) {
                                                // если форма не входит в список отображаемых - пропускаем.
                                                if (!in_array($form_event->id, $total_forms)) {
                                                    continue;
                                                } ?>
                                                <div id="tickets_box_<?= $form_event->id; ?>"
                                                     class="buy-tickets-wrapper tab-item<?= $active_tab ? ' active' : ''; ?>"
                                                     data-tab="tickets_box_<?= $form_event->id; ?>">
                                                    <?= $this->render('_events_tariff_form', ['form_event' => $form_event]); ?>
                                                </div>
                                                <?php $active_tab = false; ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php
                                    /* если форма одна */
                                } else {
                                    /* total_forms всегда будет иметь 1 значение, т.к. условием определено, что у мероприятия есть минимум 1 действующий тариф */
                                    $form_event = \app\modules\eventsform\models\Eventsform::findOne($total_forms[0]);
                                    if ($form_event) { ?>
                                        <!-- задать айдишник чтобы якорь приводил сразу к этому блоку -->
                                        <div id="tickets_box_<?= $form_event->id; ?>" class="buy-tickets-box">
                                            <div class="buy-tickets-title">Выберите билеты</div>
                                            <div
                                                    class="buy-tickets-date"><?= ($model->format ? $model->format->name : ''); ?> <?= $model->name; ?></div>
                                            <div class="buy-tickets-wrapper">
                                                <?= $this->render('_events_tariff_form', ['form_event' => $form_event]); ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </main>

<?php if ($model->tags) { ?>
    <section class="sec content_sec section-event-page3 mobile-visible">
        <div class="container wide">
            <div class="blog-right-column">
                <div class="expert_item-tags mobile-visible">
                    <?php foreach ($model->tags as $tag) { ?>
                        <?= Html::a('<b class="tag-hovered">' . mb_strtolower($tag->name, 'UTF-8') . '</b><span>' . mb_strtolower($tag->name, 'UTF-8') . '</span>', $events_catalog->getUrlPath() . '?tag=' . urlencode($tag->name), ['class' => 'tag', 'data-tag_id' => $tag->id, 'data-tag_name' => $tag->name]); ?>
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
                        <?= Html::a('<b class="tag-hovered">' . mb_strtolower($keyword->name, 'UTF-8') . '</b><span>' . mb_strtolower($keyword->name, 'UTF-8') . '</span>', $events_catalog->getUrlPath() . '?keyword=' . urlencode($keyword->name), ['class' => 'tag', 'data-tag_id' => $keyword->id, 'data-tag_name' => $keyword->name]); ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
<?php } ?>

<?php if (!empty($closest_events)) { ?>
    <section class="sec content_sec section-event-page2">
        <div class="container wide container-slider">
            <div class="section-event-page-title2 with_button">Другие мероприятия <a
                        href="<?= $events_catalog->getUrlPath(); ?>" class="button see_all">Смотреть все</a></div>
            <div class="blog-page-4card-slider all-events-compilation owl-carousel owl-theme" data-loop="true"
                 data-autoplay="true" data-timeout="5000">
                <?php foreach ($closest_events as $item) { ?>
                    <div class="blog-page-4card-slide">
                        <a href="<?= $item->getUrlPath(); ?>" class="blog-page-4card-slide-img">
                            <img src="<?= $item->getThumb('image', 'main'); ?>"
                                 alt="<?= str_replace('"', '&quot;', $item->name); ?>">
                        </a>
                        <div class="blog-page-4card-slide-info">
                            <?php $author = $item->getAuthorForView(); ?>
                            <a href="<?= $author['link']; ?>" class="all-events-card-author">
                                <div class="all-events-card-author-img">
                                    <img src="<?= $author['image']; ?>" alt="<?= $author['name']; ?>">
                                </div>
                                <div class="all-events-card-author-name"><?= $author['name']; ?></div>
                            </a>
                            <div class="all-events-card-dates"><?= $item->getEventDateForView(); ?></div>
                            <div class="all-events-card-type"><?= ($item->format ? $item->format->name : ''); ?></div>
                            <a href="<?= $item->getUrlPath(); ?>"
                               class="blog-page-4card-slide-title"><?= $item->name; ?></a>
                            <?php [$price, $form_id] = $item->priceBadge;
                            if ($price) { ?>
                                <a href="<?= $item->getUrlPath() . '#tickets_box_' . $form_id; ?>"
                                   class="all-events-card-price"><?= $price; ?></a>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>

<?= ''; // /=\app\modules\banner\widgets\banner\PageBannerWidget::widget(['inner_page'=>'event','id'=>$model->id]); ?>
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

    <div class="modal" id="fail_order_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Оформление заказа</div>
                <p>При оформление заказа возникла ошибка.</p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
<?php
$login_page = \app\modules\pages\models\Login::find()->where(['model' => \app\modules\pages\models\Login::class, 'visible' => 1])->one();
$login_url = $login_page ? $login_page->getUrlPath() : false;
$register_page = \app\modules\pages\models\Regfizusr::find()->where(['model' => \app\modules\pages\models\Regfizusr::class, 'visible' => 1])->one();
$register_url = $register_page ? $register_page->getUrlPath() : false;
?>
<?php
$url_order = Url::toRoute(['/pages/activities/createorder']);
$command_text = 'return `${(+price).toLocaleString()} ${currency}`;';
$js = <<<JS
	
	$('.bilet_qty').change(function () {
		let tariff = $(this).closest('.buy-tickets-tariff');
		let tickets_id = $(this).closest('.buy-tickets-wrapper').attr('data-tickets-id');
		let ticket_count = parseInt($(this).val());
		let price = tariff.find('.buy-tickets-price-js').data('price');

		let total_count_hidden = tariff.find('.buy-tickets-price-count-js');
		total_count_hidden.val(price*ticket_count);

		if(ticket_count > 0) {
			tariff.addClass('choise');
		} else tariff.removeClass('choise');

		total(tickets_id);
	});

	function total(id) {
		let parent = $('.buy-tickets-wrapper[data-tickets-id="'+ id +'"]');
		let total = parent.find('.tickets-total-js');

		// считаем сумму билетов
		let totalSum = 0;
		parent.find('.buy-tickets-price-count-js').each(function () {
			totalSum = totalSum + parseInt($(this).val());
		});
		parent.find('.tickets-total-hidden-js').val(totalSum);

		// считаем кол-во билетов
		let totalCount = 0;
		parent.find('.bilet_qty').each(function () {
			totalCount = totalCount + parseInt($(this).val());
		});
		parent.find('.tickets-total-count-hidden-js').val(totalCount);

		// если результат больше 0 - делаем активной кнопку
		if(totalCount > 0 && totalSum > 0) {
			total.removeClass('disabled');
			// выводим текст в кнопку
			total.html('Купить ' + totalCount + ' ' + declOfNum(totalCount, ['билет', 'билета', 'билетов']) + ' за <span class="price-space-js">' + totalSum + ' ₽</span>');
		} else if(totalCount > 0 && totalSum == 0) {
			total.removeClass('disabled');
			// выводим текст в кнопку
			if(totalCount > 1) {
				total.html('Оформить билеты');
			} else total.html('Оформить билет')
		} else {
			total.addClass('disabled');
			// выводим текст в кнопку
			total.html('Выберите билеты');
		}

		// ставим пробелы в цену
		total.find('.price-space-js').text((i, text) => {
		  	const [ price, currency ] = text.split(' ');
		  	{$command_text}
		});
	}

	$('.buy-tickets-wrapper').each(function (i, e) {
		$(this).attr('data-tickets-id', i+1);
	});

	function declOfNum(n, text_forms) {  
	    n = Math.abs(n) % 100; 
	    var n1 = n % 10;
	    if (n > 10 && n < 20) { return text_forms[2]; }
	    if (n1 > 1 && n1 < 5) { return text_forms[1]; }
	    if (n1 == 1) { return text_forms[0]; }
	    return text_forms[2];
	}

	$('.buy_tickets').click(function(e) {
		e.preventDefault();
		let order_data = {};
		$(this).closest('.buy-tickets-wrapper').find('.bilet_qty').each(function() {
			if ($(this).val() > 0) {
				order_data[$(this).data('tariff')] = $(this).val();
			}
		});
		let param = yii.getCsrfParam();
		let token = yii.getCsrfToken();
		$.ajax({
			type: 'POST',
			url: '{$url_order}',
			processData: true,
			dataType: 'json',
			data: {order_data:order_data,param:token},
			success: function(data){
				if (data.status == 'success') {
					// в случае успеха редирект на страницу оплаты заказа
					if (data.redirect_to) {
						window.location.href = data.redirect_to;
					} else {
						$('#fail_order_modal .success_box p').html(data.message);
						modalPos('#fail_order_modal');
					}
				} else if (data.status == 'need_register') {
                    $.fancybox.open($('#need_auth'));
                } else {
                    // в случае ошибки вывести сообщение
                    $('#fail_order_modal .success_box p').html(data.message);
                    modalPos('#fail_order_modal');
                }
			}
		});
	});

JS;
$this->registerJs($js);
?>