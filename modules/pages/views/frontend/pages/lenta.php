<?php

$page_lenta = \app\modules\pages\models\Lentapage::find()->where(['model' => 'app\modules\pages\models\Lentapage', 'visible' => 1])->one();
$page_material = \app\modules\pages\models\LentaMaterialpage::find()->where(['model' => 'app\modules\pages\models\LentaMaterialpage', 'visible' => 1])->one();
$page_blog = \app\modules\pages\models\LentaBlogpage::find()->where(['model' => 'app\modules\pages\models\LentaBlogpage', 'visible' => 1])->one();
$page_news = \app\modules\pages\models\LentaNewspage::find()->where(['model' => 'app\modules\pages\models\LentaNewspage', 'visible' => 1])->one();
$page_project = \app\modules\pages\models\LentaProjectpage::find()->where(['model' => 'app\modules\pages\models\LentaProjectpage', 'visible' => 1])->one();
?>
    <main class="sec content_sec section-blog">
        <div class="container wide">
            <div class="blog-content">
                <h1 class="page-title">Лента</h1>
                <div class="lenta-menu">
                    <?php if (!empty($page_lenta)) { ?><a
                        href="<?= $page_lenta->getUrlPath(); ?>" <?php if ($mode == 'lenta') {
                            echo 'class="active"';
                        } ?>>Актуальное</a> <?php } ?>
                    <?php if (!empty($page_material)) { ?><a
                        href="<?= $page_material->getUrlPath(); ?>" <?php if ($mode == 'material') {
                            echo 'class="active"';
                        } ?>>База знаний</a> <?php } ?>
                    <?php if (!empty($page_blog)) { ?><a
                        href="<?= $page_blog->getUrlPath(); ?>" <?php if ($mode == 'blog') {
                            echo 'class="active"';
                        } ?>>Блог</a> <?php } ?>
                    <?php if (!empty($page_news)) { ?><a
                        href="<?= $page_news->getUrlPath(); ?>" <?php if ($mode == 'news') {
                            echo 'class="active"';
                        } ?>>Новости</a> <?php } ?>
                    <?php if (!empty($page_project)) { ?><a
                        href="<?= $page_project->getUrlPath(); ?>" <?php if ($mode == 'project') {
                            echo 'class="active"';
                        } ?>>Портфолио</a> <?php } ?>
                </div>
                <form method="get" class="mobile_filters_form filter-mob-form">

                    <div class="mob_search_box">
                        <div class="mob-filter-buttons">
                            <button type="button" class="mob-filter-btn">Все фильтры</button>
                            <button type="button" class="mob-filter-clear-all  clear-filter-all_js">Сбросить</button>
                        </div>
                        <div class="mob-search-wrapper">
                            <input type="text" name="q" class="input_text ip_search" placeholder="Поиск"
                                   value="<?= html_entity_decode($search_text); ?>">
                            <button type="button" class="mob-search-button">Применить</button>
                        </div>
                    </div>

                    <div class="nav-overlay"></div>
                    <nav class="filter-nav">
                        <div class="filter-nav__main">
                            <div class="filter-nav__header">
                                <div class="filter-nav__header-top">
                                    <div class="filter-nav__close"></div>
                                    <div class="filter-nav__title">Фильтры</div>
                                    <button class="mob-filter-clear  clear-filter-all_js" type="button">Сбросить всё
                                    </button>
                                </div>
                            </div>
                            <ul class="filter-nav__list mScrollbarCustom simplebar">
                                <li class="filter-nav__item" data-filter="filter-directions">
                                    <div class="filter-nav__item-top">
                                        <a class="filter-nav__link" href="#"><span>Кафедры</span><i></i></a>
                                        <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                    </div>
                                    <div class="filter-nav__selected-list"></div>
                                </li>
                            </ul>
                            <div class="filter-nav__show-inner">
                                <button class="button long filter-nav__show-btn js-mob-filters" type="submit">Показать
                                </button>
                            </div>
                        </div>

                        <div class="filter-nav__sub filter_search_container" data-filter="filter-directions">
                            <div class="filter-nav__header">
                                <div class="filter-nav__header-top">
                                    <div class="filter-nav__back"></div>
                                    <div class="filter-nav__title">Кафедры</div>
                                    <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить
                                    </button>
                                </div>
                            </div>
                            <div class="filter-nav__sub-container">
                                <div class="filter-nav__sub-container-inner filter_search_list mScrollbarCustom simplebar">
                                    <?php foreach ($directions as $i => $direction) { ?>
                                        <div class="custom_dropdown-row">
                                            <input id="m_direction_<?= $direction->id; ?>"
                                                   class="ch custom_dropdown-choice <?= (in_array($direction->id, $terms['directs'])) ? 'checked="checked"' : ''; ?>"
                                                   type="checkbox" name="directs[]"
                                                   data-value="<?= $direction->name; ?>"
                                                   value="<?= $direction->id; ?>"/>
                                            <label><?= $direction->name; ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="filter-nav__show-inner">
                                    <button class="button long filter-nav__show-btn filter-mob-nav__show-btn"
                                            type="button">Применить
                                    </button>
                                </div>
                            </div>
                        </div>
                    </nav>
                    <div class="filters filters-keywords">
                        <div class="filters-list-selected"></div>
                    </div>
                </form>
                <div class="expert_item-tags mobile-visible">
                    <?php foreach ($model->pageTags as $i => $tag) { ?>
                        <a class="tag <?= Yii::$app->request->get('tag') == $tag->name ? 'active' : ''; ?>"
                           href="<?= strtok($_SERVER["REQUEST_URI"], '?') . (Yii::$app->request->get('tag') == $tag->name ? '' : ('?tag=' . urlencode($tag->name))); ?>"
                           data-tagid="<?= $i; ?>" data-tagname="<?= $tag->name; ?>"><b
                                    class="tag-hovered"><?= $tag->name; ?></b><span><?= $tag->name; ?></span></a>
                    <?php } ?>
                </div>
                <div id="promo-lenta" class="blog-first-wrapper">
                    <?php if (!empty($promo_lenta)) { ?>
                        <div class="lenta-actuals">
                            <div id="lenta-actuals-append" class="lenta-actuals-append">
                                <?= $this->render('_lenta_promo_box', ['items' => $promo_lenta, 'model' => $model]); ?>
                            </div>
                            <?php if ($promo_lenta_show_more) { ?><a href="#!"
                                                                     class="button-o small small2 lenta-actuals-more">Загрузить
                                еще</a><?php } ?>
                        </div>
                    <?php } ?>
                    <div class="blog-right-column desktop-visible">
                        <form class="search_flex desktop-visible">
                            <div class="ip_cell search-wrapper">
                                <input type="text" name="q" value="<?= html_entity_decode($search_text); ?>"
                                       class="input_text ip_search" placeholder="Поиск">
                                <button class="button-o button-search" type="submit">Применить</button>
                            </div>
                        </form>
                        <form method="get"
                              class="desktop_filters_form columns_box columns_box-filters filters-desktop-form all-events-form">
                            <div class="sidebar_box">
                                <h3>Кафедры</h3>
                                <div class="checkboxes_js mScrollbarCustom simplebar">
                                    <?php foreach ($directions as $i => $direction) { ?>
                                        <div class="ip_cell">
                                            <input type="checkbox"
                                                   class="ch filter_jmaka" <?= (in_array($direction->id, $terms['directs'])) ? 'checked="checked"' : ''; ?>
                                                   name="directs[]" value="<?= $direction->id; ?>"/>
                                            <label><?= $direction->name; ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </form>
                        <div class="expert_item-tags">
                            <?php foreach ($model->pageTags as $i => $tag) { ?>
                                <a class="tag <?= Yii::$app->request->get('tag') == $tag->name ? 'active' : ''; ?>"
                                   href="<?= strtok($_SERVER["REQUEST_URI"], '?') . (Yii::$app->request->get('tag') == $tag->name ? '' : ('?tag=' . urlencode($tag->name))); ?>"
                                   data-tagid="<?= $i; ?>" data-tagname="<?= $tag->name; ?>"><b
                                            class="tag-hovered"><?= $tag->name; ?></b><span><?= $tag->name; ?></span></a>
                            <?php } ?>
                        </div>

                        <div class="blog-read-more-block">
                            <div class="blog-read-more-block-title">ТОП-3 ПОПУЛЯРНЫХ</div>
                            <?php foreach ($model->getTrending(3) as $item) { ?>
                                <a href="<?= $item->getUrlPath(); ?>" class="blog-read-more">
                                    <div class="blog-read-more-title"><?= $item->name; ?></div>
                                    <div class="blog-read-more-text"><?= mb_strimwidth(strip_tags($item->description), 0, 50, '...', 'UTF8'); ?></div>
                                </a>
                            <?php } ?>
                        </div>

                        <?= $this->render('_lenta_directs_box', ['items' => $directs_lenta, 'model' => $model, 'mobile' => false]); ?>

                        <?php if (!empty($model->getExperts())) { ?>
                            <div class="blog-expert-list desktop-visible">
                                <div class="blog-expert-list-title">ЭКСПЕРТЫ БЛОГА</div>
                                <?php foreach ($model->getExperts() as $expert) { ?>
                                    <a href="<?= $expert->getUrlPath(); ?>" class="blog-expert-element">
                                        <div class="blog-expert-element-img">
                                            <img src="<?= $expert->profile->getThumb('image', 'main'); ?>" alt=""
                                                 loading="lazy">
                                        </div>
                                        <div class="blog-expert-element-info">
                                            <div class="blog-expert-element-name"><?= $expert->profile->halfname; ?></div>
                                            <div class="blog-expert-element-text"><?= $expert->profile->about_myself; ?>    </div>
                                        </div>
                                    </a>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <?php /*
            <div class="blog-read-more-block mobile-visible">
                <div class="blog-read-more-block-title">ТОП-3 ПОПУЛЯРНЫХ</div>
                <?php foreach($model->getTrending(3) as $item){ ?>
                <a href="<?=$item->getUrlPath()?>" class="blog-read-more">
                    <div class="blog-read-more-title"><?=$item->name?></div>
                    <div class="blog-read-more-text"><?=mb_strimwidth(strip_tags($item->description),0,50,'...','UTF8');?></div>
                </a>
                <?php }?>
            </div>
            <?php */ ?>

                <?= $this->render('_lenta_directs_box', ['items' => $directs_lenta, 'model' => $model, 'mobile' => true]); ?>

                <div id="lenta_content" class="blog-cards">
                    <?= $this->render('_lenta_box', ['ads' => $ads, 'image_first' => $image_first, 'image_first_mobile' => $image_first_mobile, 'first' => $first, 'pages' => $pages, 'blocks' => $blocks, 'items' => $items, 'model' => $model]); ?>
                </div>
                <div id="pager_content">
                    <?= \app\widgets\pagination\LinkPager::widget(['pages' => $pages, 'container' => '#lenta_content', 'showMore' => true]); ?>
                </div>

                <?php if (!empty($model->getExperts())) { ?>
                    <div class="blog-expert-list mobile-visible">
                        <div class="blog-expert-list-title">ЭКСПЕРТНОЕ МНЕНИЕ</div>
                        <?php foreach ($model->getExperts() as $expert) { ?>
                            <a href="<?= $expert->getUrlPath(); ?>" class="blog-expert-element">
                                <div class="blog-expert-element-img">
                                    <img src="<?= $expert->profile->getThumb('image', 'main'); ?>" alt=""
                                         loading="lazy">
                                </div>
                                <div class="blog-expert-element-info">
                                    <div class="blog-expert-element-name"><?= $expert->profile->halfname; ?></div>
                                    <div class="blog-expert-element-text"><?= $expert->profile->about_myself; ?>    </div>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                <?php } ?>

            </div>
        </div>
    </main>
<?php
$this->registerCssFile('/css/style-blog.css', ['depends' => [app\assets\AppAsset::class]]);
$this->registerJsFile('/js/filters.js', ['depends' => [app\assets\AppAsset::class]]);
$this->registerJsFile('/js/main-blog.js', ['depends' => [app\assets\AppAsset::class]]);

$this->registerJsVar('filter_suffix', '-lenta', $position = yii\web\View::POS_HEAD);
$url = $model->getUrlPath();
$js = <<<JS
	$(document).ajaxStop(function(){
		var slider_blog_banner = $('.blog-page-banner-slider');
		if(slider_blog_banner.length){
			slider_blog_banner.trigger('destroy.owl.carousel');
			slider_blog_banner.owlCarousel({
				center: false,
				items: 1,
				loop: true,
				nav: false,
				dots: true,
				margin: 10,
				mouseDrag: true,
				touchDrag: true,
				autoplay: slider_blog_banner.data('autoplay'),
				autoplayTimeout: slider_blog_banner.data('timeout'),
				autoplaySpeed: 1300,
				autoplayHoverPause: true,
				navSpeed: 1300,
				autoHeight: true
			});
		}
		
		var slider_4card = $('.blog-page-4card-slider');
		if(slider_4card.length){
			slider_4card.trigger('destroy.owl.carousel');
			slider_4card.owlCarousel({
				center: false,
				items: 4,
				loop: true,
				nav: true,
				dots: false,
				margin: 20,
				mouseDrag: true,
				touchDrag: true,
				navSpeed: 1300,
				responsive: {
					0: {
						items: 1.3,
						nav: false
					},
					500: {
						items: 2
					},
					769: {
						items: 3
					},
					1100: {
						items: 4
					}
				}
			});
		}

		var slider_4card2 = $('.blog-page-4card-slider2');
		if(slider_4card2.length){
			slider_4card2.trigger('destroy.owl.carousel');
			slider_4card2.owlCarousel({
				loop: true,
				center: false,
				items: 4,
				loop: true,
				nav: true,
				dots: false,
				margin: 20,
				mouseDrag: true,
				touchDrag: true,
				navSpeed: 1300,
				responsive: {
					0: {
						items: 1.3,
						nav: false
					},
					500: {
						items: 2
					},
					769: {
						items: 3
					},
					1100: {
						items: 4
					}
				}
			});
		}

		var slider_3card = $('.blog-page-3card-slider');
		if(slider_3card.length){
			slider_3card.trigger('destroy.owl.carousel');
			slider_3card.owlCarousel({
				loop: true,
				center: false,
				items: 3,
				loop: true,
				nav: true,
				dots: false,
				margin: 20,
				mouseDrag: true,
				touchDrag: true,
				navSpeed: 1300,
				responsive: {
					0: {
						items: 1.2,
						nav: false
					},
					500: {
						items: 2
					},
					767: {
						items: 3
					}
				}
			});
		}

		if (window.innerWidth <= 1100) {
			var slider_lenta_cards = $('.lenta-cards-slider');
			if(slider_lenta_cards.length){
				slider_lenta_cards.trigger('destroy.owl.carousel');
				slider_lenta_cards.owlCarousel({
					loop: true,
					center: false,
					items: 2,
					loop: true,
					nav: false,
					dots: false,
					margin: 20,
					mouseDrag: true,
					touchDrag: true,
					navSpeed: 1300,
					responsive: {
						0: {
							items: 1.2
						},
						768: {
							items: 2
						}
					}
				});
			}
		}
	});

	let promo_lenta_page = 1;
	$('body').on('click', '.lenta-actuals-more', function(e) {
		e.preventDefault();
		let ths = $(this);
		ths.hide();
		let url = new URL(window.location.href);
		promo_lenta_page++;
		url.searchParams.append('promo_lenta_page', promo_lenta_page);
		$.ajax({
			type: 'GET',
			url: url,
			processData: true,
			dataType: 'json',
			success: function(data) {
				if (data.status == 'success') {
					if (!data.show_more) {
						ths.hide();
					}
					else{
						ths.show();
					}
					$('#lenta-actuals-append').append(data.html);
				}
				else {
					ths.hide();
				}
			}
		});
		return false;
	});

	if(window.innerWidth > 1100) {
		$('body').on('change','.custom_dropdown-selected-button, .filter_jmaka, .tap_tap_change', function(e) {
			$(this).closest('form').submit();
		});
	} else {
		$('body').on('click','.mob-search-button, .js-mob-filters', function(e) {
			e.preventDefault();
			$(this).closest('form').submit();
		});
	}

	$('body').on('submit','.desktop_filters_form', function(e) {
		let new_url = $(this).serialize();
		$.ajax({
			type: 'GET',
			url: '{$url}?'+new_url,
			processData: true,
			dataType: 'json',
			success: function(data) {
				if (data.status == 'success') {
					$('#lenta_content').html(data.html);
					$('#pager_content').html(data.pager);
				} else {
					// сообщение об ошибке на страницу
					$('#lenta_content').html('<h3 class="pb20"><i>К сожалению, по вашему запросу ничего не найдено</i></h3>');
					$('#pager_content').html(data.pager);
				}
				history.pushState(null, null, '{$url}?'+new_url);
		    }
		});
		return false;
	});

	$('body').on('submit','.mobile_filters_form', function(e) {
		let new_url = $(this).serialize();
		$.ajax({
			type: 'GET',
			url: '{$url}?'+new_url,
			processData: true,
			dataType: 'json',
			success: function(data) {
				if (data.status == 'success') {
					$('#lenta_content').html(data.html);
					// написать кол-во результатов
					// $('#mobile_count').html(data.count);
					// показать плашку с результатами
					$('.filter-nav__show-inner').addClass('filter-nav__show-inner--show');
					$('.filter-nav__main').addClass('filter-nav__main--checked');
					$('#pager_content').html(data.pager);
				} else {
					// сообщение об ошибке на страницу
					$('#lenta_content').html('<h3 class="pb20"><i>К сожалению, по вашему запросу ничего не найдено</i></h3>');
					// написать кол-во результатов
					// $('#mobile_count').html('Предложения не найдены');
					// показать плашку с результатами
					$('.filter-nav__show-inner').addClass('filter-nav__show-inner--show');
					$('.filter-nav__main').addClass('filter-nav__main--checked');
					$('#pager_content').html(data.pager);
				}
				history.pushState(null, null, '{$url}?'+new_url);
		    }
		});
		return false;
	});

	const createLabelTag = (name) => {
        return '<label class="filter_selected_item filter_selected_item-tag">'+ name +'<input type="hidden" name="tag" value="'+ name +'"></label>';
	};
    
	function tags() {
		let paramsString = String(document.location.search);
		let searchParams = new URLSearchParams(paramsString);
		let tag = searchParams.get("tag");
		let keyword = searchParams.get("keyword");
		if(tag !== null) {
			$('.filters-list-selected').append(createLabelTag(tag));
			$('.filters_reset').removeClass('disabled');
		}	
		if(keyword !== null) {
			$('.filters-list-selected').append(createLabelTag(keyword));
			$('.filters_reset').removeClass('disabled');
		}	
	}
	tags();

	$('.filter_selected_item-tag').click(function(e) {
		$(this).hide();
		$(this).find('input').val('');
		$(this).closest('form').submit();
	});
JS;
$this->registerJs($js);
$this->registerJsFile('/js/main-blog.js', ['depends' => [app\assets\AppAsset::class]]);
$this->registerCssFile('/css/style-blog.css', ['depends' => [app\assets\AppAsset::class]]);
?>