<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>

<main class="sec content_sec sec-bg-pic">
    <?php if (!empty($model->back_image)) { ?>
        <div class="pic-bg">
            <img src="<?= $model->getThumb('back_image', 'main'); ?>"
                 alt="<?= str_replace('"', '&quot;', $model->name); ?>">
        </div>
    <?php } ?>
    <div class="container wide">
        <h1><?= $model->getNameForView(); ?></h1>
        <div class="subheader"><?= $model->description; ?></div>
    </div>
</main>
<?php if ((!empty($model->content))
        or (!empty($model->expert) or (!empty($model->academic)) or (!empty($model->exporg)))
        or (!empty($events))
        or (!empty($blogs))
        or (!empty($projects))
        or (!empty($materials))
) { ?>
    <main class="sec nav-wrap">
        <div class="container wide">

            <ul class="nav">
                <?php if (!empty($model->content)) { ?>
                    <li class="current"><a class="scrollTo" href="#about">О кафедре</a></li>
                <?php } ?>
                <?php if (!empty($model->expert) or (!empty($model->academic)) or (!empty($model->exporg))) { ?>
                    <li><a class="scrollTo" href="#experts">Эксперты</a></li>
                <?php } ?>
                <?php if (!empty($events)) { ?>
                    <li><a class="scrollTo" href="#events">Мероприятия</a></li>
                <?php } ?>
                <?php if (!empty($blogs)) { ?>
                    <li><a class="scrollTo" href="#blog">Блог</a></li>
                <?php } ?>
                <?php if (!empty($projects)) { ?>
                    <li><a class="scrollTo" href="#project">Проекты</a></li>
                <?php } ?>
                <?php if (!empty($materials)) { ?>
                    <li><a class="scrollTo" href="#material">База знаний</a></li>
                <?php } ?>
                <?php if (!empty($model->requirement) && (!empty($model->join_us_title) or !empty($model->join_us_text))) { ?>
                    <li><a class="scrollTo" href="#requirement">Стать экспертом</a></li>
                <?php } ?>
            </ul>
        </div>
    </main>
<?php } ?>
<main class="sec">
    <div class="container wide page-promo">
        <?php if (!empty($model->video_link) or !empty($model->video_image)) { ?>
            <div class="index_video-area ver-3">
                <div class="index_video-right">
                    <?php if (!empty($model->video_link)) { ?>
                        <div class="youtube_preview">
                            <a href="<?= $model->video_link; ?>" data-fancybox class="youtube_link">
                                <?= Html::img($model->getThumb('video_image', 'main'), ['alt' => $model->video_title, 'loading' => 'lazy']); ?>
                            </a>
                        </div>
                    <?php } elseif (!empty($model->video_image)) { ?>
                        <?= Html::img($model->getThumb('video_image', 'main'), ['alt' => $model->video_title, 'loading' => 'lazy']); ?>
                    <?php } ?>
                </div>
                <article class="index_video-left">
                    <h2><?= $model->video_title; ?></h2>
                    <?= $model->video_descr; ?>
                </article>
            </div>
        <?php } ?>
    </div>
</main>

<?php if (!empty($model->content)) { ?>
    <main class="sec content_txt sec-bg-gray" id="about">
        <div class="container wide">
            <div class="txt_expand">
                <div class="text-content"><?= $model->content; ?></div>
                <button class="button-o arr_down">Развернуть</button>
            </div>
        </div>
    </main>
<?php } ?>

<?= \app\modules\banner\widgets\banner\DirectionBannerWidget::widget(['id' => $model->id]); ?>

<?php $zebra = 0; ?>
<?php if (!empty($model->directionquote)) {
    $zebra++; ?>
    <main class="sec content_sec teachers">
        <div class="container wide">
            <div class="teachers_slider owl-carousel dotsNavTeachers" data-autoplay="0">
                <?php foreach ($model->directionquote as $item) { ?>
                    <div class="item">
                        <div class="teacher_slide">
                            <div class="teacher_slide-info">
                                <?php if ($quoteprofile = $item->quoteProfile) { ?>
                                    <div class="teacher_slide-img"><?= Html::a(Html::img($item->getThumb('image', 'main'), ['alt' => $item->name]), $quoteprofile->getUrlPath()); ?></div>
                                    <div class="teacher_slide-name"><?= Html::a($quoteprofile->profile->halfname, $quoteprofile->getUrlPath()); ?></div>
                                <?php } else { ?>
                                    <div class="teacher_slide-img"><?= Html::img($item->getThumb('image', 'main'), ['alt' => $item->name]); ?></div>
                                    <div class="teacher_slide-name"><?= $item->name; ?></div>
                                <?php } ?>
                                <div class="teacher_slide-post"><?= $item->office; ?></div>
                            </div>
                            <blockquote class="teacher_slide-text">
                                <p><?= $item->content; ?></p>
                            </blockquote>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </main>
<?php } ?>
<?php if (!empty($model->academic)) { ?>
    <main class="sec experts sec-bg-gray" id="experts">
        <div class="container wide">
            <h2 class="with_button">
                <?= $model->academtitle ? $model->academtitle : $parent->academtitle; ?>
                <?php echo Html::a('Показать всех', $academ_catalog->getUrlPath(['directs[]' => $model->id]), ['class' => 'button see_all']); ?>
            </h2>
            <p class="subheader"><?= $model->academtext ? $model->academtext : $parent->academtext; ?></p>
            <div class="index_experts_box">
                <?php foreach ($model->academic as $user) { ?>
                    <div class="expert_item expert_item_spec expert_item_spec_blue <?= $user->id == $model->leader?->id ? 'js-hide-mobile' : ''; ?>">
                        <div class="expert_item-img_box">
                            <?= Html::a(Html::img($user->profile->getThumb('image', 'prev'), ['alt' => $user->profile->fullname]), $user->getUrlPath(), ['class' => 'expert_item-img']); ?>
                            <?= app\widgets\shield\ShieldWidget::widget(['user' => $user]); ?>
                        </div>
                        <div class="expert_item-info">
                            <h5><a href="<?= $user->getUrlPath(); ?>"><?= $user->profile->halfname; ?></a></h5>
                            <div class="expert_item-city"><?= $user->profile->city->name; ?></div>
                            <div class="expert_item-desc">
                                <p><?= $user->profile->about_myself; ?></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </main>
<?php } ?>
<?php if (!empty($model->expert)) { ?>
    <main class="sec experts sec-bg-gray" <?= (empty($model->academic) ? 'id="experts"' : ''); ?>>
        <div class="container wide">
            <h2 class="with_button">
                <?= $model->experttitle ? $model->experttitle : $parent->experttitle; ?>
                <?php echo Html::a('Все эксперты', $expert_catalog->getUrlPath(['directs[]' => $model->id]), ['class' => 'button see_all']); ?>
            </h2>
            <p class="subheader"><?= $model->experttext ? $model->experttext : $parent->experttext; ?></p>
            <div class="index_experts_box">
                <?php foreach ($model->expert as $key => $user) { ?>
                    <div class="expert_item expert_item_spec">
                        <div class="expert_item-img_box">
                            <?= Html::a(Html::img($user->profile->getThumb('image', 'prev'), ['alt' => $user->profile->fullname]), $user->getUrlPath(), ['class' => 'expert_item-img']); ?>
                            <?= app\widgets\shield\ShieldWidget::widget(['user' => $user]); ?>
                        </div>
                        <div class="expert_item-info">
                            <h5><a href="<?= $user->getUrlPath(); ?>"><?= $user->profile->halfname; ?></a></h5>
                            <div class="expert_item-city"><?= $user->profile->city->name; ?></div>
                            <div class="expert_item-desc">
                                <p><?= $user->profile->about_myself; ?></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </main>
<?php } ?>
<?php if (!empty($model->directionhonorablelist)) { ?>
    <main class="sec experts sec-bg-gray" id="experts">
        <div class="container wide">
            <h2>
                Почетные Эксперты
            </h2>
            <p class="subheader"><?= $model->academtext ? $model->academtext : $parent->academtext; ?></p>
            <div class="index_experts_box">
                <?php foreach ($model->directionhonorablelist as $user) { ?>
                    <div class="expert_item expert_item_spec expert_item_spec_blue <?= $user->id == $model->leader?->id ? 'js-hide-mobile' : ''; ?>">
                        <div class="expert_item-img_box">
                            <?= Html::a(Html::img($user->profile->getThumb('image', 'prev'), ['alt' => $user->profile->fullname]), $user->getUrlPath(), ['class' => 'expert_item-img']); ?>
                            <span class="expert_rang blue">
							<?php
                            echo Html::img('/img/rang-academik.svg', ['alt' => 'Почетный эксперт']);
                            echo Html::tag('span', 'Почетный эксперт');
                            ?>
							</span>
                        </div>
                        <div class="expert_item-info">
                            <h5><a href="<?= $user->getUrlPath(); ?>"><?= $user->profile->halfname; ?></a></h5>
                            <div class="expert_item-city"><?= $user->profile->city->name; ?></div>
                            <div class="expert_item-desc">
                                <p><?= $user->profile->about_myself; ?></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </main>
<?php } ?>
<?php if (!empty($model->exporg)) { ?>
    <main class="sec experts sec-bg-gray" <?= ((empty($model->academic) and empty($model->expert)) ? 'id="experts"' : ''); ?>>
        <div class="container wide">
            <h2 class="with_button">
                <?= $model->exporgtitle ? $model->exporgtitle : $parent->exporgtitle; ?>
                <?php echo Html::a('Смотреть все', $exporg_catalog->getUrlPath(['directs[]' => $model->id]), ['class' => 'button see_all']); ?>
            </h2>
            <p class="subheader"><?= $model->exporgtext ? $model->exporgtext : $parent->exporgtext; ?></p>
            <div class="index_experts_box">
                <?php foreach ($model->exporg as $key => $user) {
                    if ($key > 2) {
                        continue;
                    } ?>
                    <!-- EXPERT -->
                    <div class="expert_item expert_item_spec expert_item_spec_org">
                        <div class="expert_item-img_box">
                            <?= Html::a(Html::img($user->profile->getThumb('image', 'prev'), ['alt' => $user->profile->fullname]), $user->getUrlPath(), ['class' => 'expert_item-img']); ?>
                            <?= app\widgets\shield\ShieldWidget::widget(['user' => $user]); ?>
                        </div>
                        <div class="expert_item-info">
                            <h5><a href="<?= $user->getUrlPath(); ?>"><?= $user->profile->halfname; ?></a></h5>
                            <div class="expert_item-city"><?= $user->profile->city->name; ?></div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </main>
<?php } ?>
<?php if (!empty($blogs)) { ?>
    <section class="sec content_sec section-blog-page2" id="blog">
        <div class="container wide container-slider">
            <h2 class="with_button">Блог кафедры <a href="<?= $blog_catalog->getUrlPath(['direct' => $model->id]); ?>"
                                                    class="button see_all">Смотреть все</a></h2>
            <div class="blog-page-4card-slider owl-carousel owl-theme" data-loop="true" data-autoplay="true"
                 data-timeout="5000">
                <?php foreach ($blogs as $item) { ?>
                    <a href="<?= $item->getUrlPath(); ?>" class="blog-page-4card-slide">
                        <div class="blog-page-4card-slide-img">
                            <img src="<?= $item->getThumb('image', 'main'); ?>"
                                 alt="<?= str_replace('"', '&quot;', $item->name); ?>">
                        </div>
                        <div class="blog-page-4card-slide-info">
                            <div class="blog-page-4card-slide-title"><?= $item->name; ?></div>
                            <div class="blog-page-4card-slide-text-wrapper">
                                <div class="blog-page-4card-slide-date"><?= \Yii::$app->formatter->asDate($item->published, 'php:d.m.Y'); ?></div>
                                <!-- <div class="blog-page-4card-slide-viewed"><?= $item->views; ?></div> -->
                            </div>
                        </div>
                    </a>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>
<?php if (!empty($materials)) { ?>
    <section class="sec content_sec section-blog-page2" id="material">
        <div class="container wide container-slider">
            <h2 class="with_button">База знаний кафедры <a
                        href="<?= $material_catalog->getUrlPath(['direct' => $model->id]); ?>" class="button see_all">Смотреть
                    все</a></h2>
            <div class="blog-page-4card-slider owl-carousel owl-theme" data-loop="true" data-autoplay="true"
                 data-timeout="5000">
                <?php foreach ($materials as $item) { ?>
                    <a href="<?= $item->getUrlPath(); ?>" class="blog-page-4card-slide">
                        <div class="blog-page-4card-slide-img">
                            <img src="<?= $item->getThumb('image', 'main'); ?>"
                                 alt="<?= str_replace('"', '&quot;', $item->name); ?>">
                        </div>
                        <div class="blog-page-4card-slide-info">
                            <div class="blog-page-4card-slide-title"><?= $item->name; ?></div>
                            <div class="blog-page-4card-slide-text-wrapper">
                                <div class="blog-page-4card-slide-date"><?= \Yii::$app->formatter->asDate($item->published, 'php:d.m.Y'); ?></div>
                                <!-- <div class="blog-page-4card-slide-viewed"><?= $item->views; ?></div> -->
                            </div>
                        </div>
                    </a>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>
<?php if (!empty($events)) { ?>
    <section class="sec content_sec section-event-page2 white-bg" id="events">
        <div class="container wide container-slider">
            <h2 class="with_button">Мероприятия <a
                        href="<?= $events_catalog->getUrlPath(['directs[]' => $model->id, 'registration_open' => 0]); ?>"
                        class="button see_all">Смотреть все</a></h2>
            <div class="blog-page-4card-slider all-events-compilation owl-carousel owl-theme" data-loop="true"
                 data-autoplay="true" data-timeout="5000">
                <?php foreach ($events as $item) { ?>
                    <?php $eventHolder = $item->eventCardStatusText(); ?>
                    <div class="blog-page-4card-slide <?= $eventHolder->holder ? 'soldout' : false; ?>">
                        <a href="<?= $item->getUrlPath(); ?>" class="blog-page-4card-slide-img">
                            <img src="<?= $item->getThumb('image', 'main'); ?>"
                                 alt="<?= str_replace('"', '&quot;', $item->name); ?>">
                            <?php if ($eventHolder->holder) { ?>
                                <!-- если soldout - выводить лейбл, если билетов нет, добавить класс closed -->
                                <div class="soldout-label closed"><?= $eventHolder->holder_text; ?></div>
                            <?php } ?>
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
                            <?php if (!$eventHolder->holder) { ?>
                                <?php [$price, $form_id] = $item->priceBadge; ?>
                                <?php if ($price) { ?>
                                    <a href="<?= $item->getUrlPath() . '#tickets_box_' . $form_id; ?>"
                                       class="all-events-card-price"><?= $price; ?></a>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>
<?php if (!empty($model->cert_link) or !empty($model->cert_image)) { ?>
    <section class="sec content_sec section-blog-page2">
        <div class="container wide">
            <h2><?= $model->cert_title; ?></h2>
            <div class="index_video-area ver-3 ver-4">
                <article class="index_video-left">
                    <?= $model->cert_text; ?>
                </article>
                <div class="index_video-right">
                    <?php if (!empty($model->cert_link)) { ?>
                        <div class="youtube_preview">
                            <a href="<?= $model->cert_link; ?>" data-fancybox class="youtube_link">
                                <?= Html::img($model->getThumb('cert_image', 'main'), ['alt' => $model->cert_title, 'loading' => 'lazy']); ?>
                            </a>
                        </div>
                    <?php } elseif (!empty($model->cert_image)) { ?>
                        <?= Html::img($model->getThumb('cert_image', 'main'), ['alt' => $model->cert_title, 'loading' => 'lazy']); ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
<?php } ?>
<?php if (!empty($services)) { ?>
    <div class="sec section-page section-popular-services white-bg">
        <div class="container wide">
            <h2 class="with_button">Услуги кафедры <a
                        href="<?= $service_page->getUrlPath(['directs[]' => $model->id]); ?>" class="button see_all">Смотреть
                    все</a></h2>
            <div class="services-slider default-slider-3 owl-carousel owl-theme" data-loop="true" data-autoplay="true"
                 data-timeout="5000">
                <?php foreach ($services as $item) { ?>
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
                            <a href="<?= $item->getUrlPath(); ?>" class="services-slide-text"
                               title="<?= $item->name; ?>"><?= $item->name; ?></a>
                            <div class="services-slide-text2"><?= $item->short_description; ?></div>
                            <div class="services-slide-tags">
                                <span class="services-slide-tag"><?= $item->getKindName(); ?></span>
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
            </div>
        </div>
    </div>
<?php } ?>
<?php if (!empty($projects)) { ?>
    <section class="sec content_sec section-blog-page2" id="project">
        <div class="container wide container-slider">
            <h2 class="with_button">Проекты кафедры <a
                        href="<?= $project_catalog->getUrlPath(['direct' => $model->id]); ?>" class="button see_all">Смотреть
                    все</a></h2>
            <div class="blog-page-4card-slider owl-carousel owl-theme" data-loop="true" data-autoplay="true"
                 data-timeout="5000">
                <?php foreach ($projects as $item) { ?>
                    <a href="<?= $item->getUrlPath(); ?>" class="blog-page-4card-slide">
                        <div class="blog-page-4card-slide-img">
                            <img src="<?= $item->getThumb('image', 'main'); ?>"
                                 alt="<?= str_replace('"', '&quot;', $item->name); ?>">
                        </div>
                        <div class="blog-page-4card-slide-info">
                            <div class="blog-page-4card-slide-title"><?= $item->name; ?></div>
                            <div class="blog-page-4card-slide-text-wrapper">
                                <div class="blog-page-4card-slide-date"><?= \Yii::$app->formatter->asDate($item->published, 'php:d.m.Y'); ?></div>
                                <!-- <div class="blog-page-4card-slide-viewed"><?= $item->views; ?></div> -->
                            </div>
                        </div>
                    </a>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>
<?php if (!empty($model->join_us_title) or !empty($model->join_us_text)) { ?>
    <section class="sec join_us_box" id="requirement">
        <span class="join_us_bg" data-parallax></span>
        <div class="container wide">
            <?php if (!empty($model->join_us_title)) { ?>
                <div class="join_us_box-title"><?= $model->join_us_title; ?></div>
            <?php } ?>
            <?= $model->join_us_text; ?>
            <div class="join_us_button">
                <?php if (!empty($model->join_us_button_link) && !empty($model->join_us_button_name)) {
                    echo Html::a($model->join_us_button_name, $model->join_us_button_link, ['class' => 'button white']);
                } ?>
                <?php if (!empty($model->requirement) && !empty($model->join_us_requirement_name)) {
                    echo Html::a($model->join_us_requirement_name, $model->getFile('requirement'), ['class' => 'button white']);
                } ?>
                <?php if (!empty($model->ethic_codex) && !empty($model->ethic_codex_button_text)) {
                    echo Html::a($model->ethic_codex_button_text, $model->getFile('ethic_codex'), ['class' => 'button white']);
                } ?>
                <?php if (!empty($model->join_us_4_button_link) && !empty($model->join_us_4_button_name)) {
                    echo Html::a($model->join_us_4_button_name, $model->join_us_4_button_link, ['class' => 'button white']);
                } ?>
            </div>
        </div>
    </section>
<?php } ?>

<?php
$js = <<<JS
$(window).on('load', function() {
	if(window.innerWidth < 1100 && $('.js-hide-mobile').length) {
		$('.index_experts_box').each(function(e, i){
			let index = $(this).find('.js-hide-mobile').parent().index();
			if (index >= 0) {
				$(this).trigger('remove.owl.carousel', index).trigger('refresh.owl.carousel');
			}
		});
	}
});
JS;
$this->registerJs($js);
$this->registerJsFile('/js/main-blog.js', ['depends' => [app\assets\AppAsset::class]]);
$this->registerCssFile('/css/style-blog.css', ['depends' => [app\assets\AppAsset::class]]);
?>
