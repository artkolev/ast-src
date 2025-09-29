<main class="sec content_sec">
    <div class="container wide">
        <h1><?= $model->getNameForView(); ?></h1>
        <div class="subheader">
            <?= $model->content; ?>
        </div>
        <form method="get" class="filter-mob-form mobile_filters_form events-filters">

            <div class="mob_search_box">
                <div class="mob-filter-buttons">
                    <button type="button" class="mob-filter-btn">Все фильтры</button>
                    <button type="button" class="mob-filter-clear-all  clear-filter-all_js">Сбросить</button>
                </div>
                <div class="mob-search-wrapper">
                    <input type="text" name="q" value="<?= $search_text; ?>" class="input_text ip_search"
                           placeholder="Введите название материала, кафедры, автора"/>
                    <button type="button" class="mob-search-button">Применить</button>
                </div>
            </div>

            <div class="nav-overlay"></div>
            <nav class="filter-nav">
                <!-- <form method="POST" class="filter-nav__form"> -->
                <div class="filter-nav__main">
                    <div class="filter-nav__header">
                        <div class="filter-nav__header-top">
                            <div class="filter-nav__close"></div>
                            <div class="filter-nav__title">Фильтры</div>
                            <button class="mob-filter-clear  clear-filter-all_js" type="button">Сбросить всё</button>
                        </div>
                    </div>
                    <ul class="filter-nav__list mScrollbarCustom simplebar">
                        <li class="filter-nav__item" data-filter="filter-111">
                            <div class="filter-nav__item-top">
                                <a class="filter-nav__link" href="#"><span>Кафедра оказания услуг</span><i></i></a>
                                <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                            </div>
                            <div class="filter-nav__selected-list"></div>
                        </li>
                    </ul>
                    <div class="filter-nav__show-inner">
                        <button class="button long filter-nav__show-btn" type="submit">Показать предложения</button>
                    </div>
                </div>

                <div class="filter-nav__sub filter_search_container" data-filter="filter-111">
                    <div class="filter-nav__header">
                        <div class="filter-nav__header-top">
                            <div class="filter-nav__back"></div>
                            <div class="filter-nav__title">Кафедры</div>
                            <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                        </div>
                        <div action="" method="post" class="filter-nav__search">
                            <input type="search" class="input_text ip_search ip_search2 filter_search_input"
                                   placeholder="Введите название кафедры"/>
                            <button class="ip_search2-button filter_search_input-button"></button>
                        </div>
                    </div>
                    <div class="custom_dropdown_title not-found-title" style="display: none;">Ничего не найдено</div>
                    <div class="filter-nav__sub-container filter_search_list">
                        <div class="filter-nav__sub-container-inner mScrollbarCustom simplebar">
                            <div class="custom_dropdown-row">
                                <input class="ch custom_dropdown-choice mob-all-checks" type="checkbox" name="direct"
                                       id="all_directions" value="Все"/>
                                <label>Все</label>
                            </div>
                            <?php foreach ($directions as $direction) { ?>
                                <div class="custom_dropdown-row">
                                    <input id="m_directs_<?= $direction->id; ?>"
                                           class="ch custom_dropdown-choice filter_jmaka" <?= ($direct == $direction->id) ? 'checked="checked"' : ''; ?>
                                           name="direct" type="checkbox" data-value="<?= $direction->name; ?>"
                                           value="<?= $direction->id; ?>"/>
                                    <label><?= $direction->name; ?></label>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="filter-nav__show-inner">
                            <button class="button long filter-nav__show-btn filter-mob-nav__show-btn" type="button">
                                Применить
                            </button>
                        </div>
                    </div>
                </div>
                <!-- </form> -->
            </nav>
        </form>

        <form method="get" class="columns_box  columns_box-filters filters-desktop-form desktop_filters_form">
            <main class="main_col">
                <div class="directions_search_box  filters filters-materials">
                    <div class="search_flex">
                        <div class="ip_cell">
                            <input type="text" name="q" value="<?= $search_text; ?>" class="input_text ip_search"
                                   placeholder="Введите название материала, кафедры, автора"/>
                            <button class="button-o button-search" type="submit">Начать поиск</button>
                        </div>
                    </div>

                    <div class="filters-list-selected">

                    </div>

                </div>

                <div id="material_content">
                    <?= $this->render('_material_box', ['items' => $items, 'model' => $model]); ?>
                </div>
                <div id="pager_content">
                    <?= app\widgets\pagination\LinkPager::widget(['pages' => $pages, 'container' => '#material_content']); ?>
                </div>

            </main>

            <aside class="sidebar_col">
                <?php if (!empty($directions)) { ?>
                    <div class="sidebar_box mb30">
                        <h3>Кафедры</h3>
                        <div class="city-form">
                            <input type="text" class="input_text ip_search filter_search_input" placeholder="Поиск"/>
                        </div>
                        <div class="city-filter-sidebar checkboxes_js mScrollbarCustom simplebar">
                            <div class="ip_cell">
                                <input type="checkbox" class="rd allChecks filter_jmaka"/>
                                <label>Все</label>
                            </div>
                            <?php foreach ($directions as $direction) { ?>
                                <div class="ip_cell">
                                    <input class="rd filter_jmaka" <?= ($direct == $direction->id) ? 'checked="checked"' : ''; ?>
                                           type="checkbox" name="direct" value="<?= $direction->id; ?>"/>
                                    <label><?= $direction->name; ?></label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <?= \app\modules\banner\widgets\banner\PageBannerWidget::widget(['id' => $this->params['page_id']]); ?>

            </aside>
        </form>

        <?= \app\modules\banner\widgets\banner\PageBannerWidget::widget(['id' => $this->params['page_id'], 'mobile' => '1']); ?>

    </div>
</main>
<?php
$this->registerJsFile('/js/filters.js', ['depends' => [app\assets\AppAsset::class]]);

$url = $model->getUrlPath();

$js = <<<JS
	$('body').on('change','.custom_dropdown-selected-button, .filter_jmaka, .tap_tap_change', function(e) {
		e.preventDefault();
		$(this).closest('form').submit();
	});

	$('body').on('click','.mob-search-button, .js-mob-filters, .filter-mob-nav__show-btn', function(e) {
		e.preventDefault();
		$(this).closest('form').submit();
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
		}	
		if(keyword !== null) {
			$('.filters-list-selected').append(createLabelTag(keyword));
		}	
	}
	tags();

	$('.filter_selected_item-tag').click(function(e) {
		$(this).hide();
		$(this).find('input').val('');
		$(this).closest('form').submit();
	});

	$('body').on('submit','.desktop_filters_form', function(e) {
		let new_url = $(this).serialize();
		$.ajax({
			type: 'GET',
			url: '{$url}?'+new_url,
			processData: true,
			dataType: 'json',
			success: function(data) {
				if (data.status == 'success') {
					// заменить содержимое expert_box
					$('#material_content').html(data.html);
					$('#pager_content').html(data.pager);

					$('.tags_box, .expert_item-tags').each(function () {
				        var that = $(this);
				        var hiddens = that.find('.tag.hide').length;
				        that.find('.tag.more u').text(hiddens);
				        if ($(this).find('.hide').length) {
				            $(this).find('.tag.more').show();
				        }
				    });
				    $('.tag.more').click(function () {
				        $(this).closest('.tags_box, .expert_item-tags').find('.tag.hide').removeClass('hide');
				        $(this).closest('.tags_box, .expert_item-tags').find('.tag.hide_mobile').removeClass('hide_mobile');
				        // $(this).closest('.tags_box, .expert_item-tags').find('.tag').css('display', 'inline-block');
				        $(this).remove();
				        return false;
				    });

					
				} else {
					// сообщение об ошибке на страницу
					$('#material_content').html('<h3 class="pb20"><i>К сожалению, по вашему запросу ничего не найдено</i></h3>');
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
					// заменить содержимое expert_box
					$('#material_content').html(data.html);
					// написать кол-во результатов
					// $('#mobile_count').html(data.count);
					// показать плашку с результатами
					$('.filter-nav__show-inner').addClass('filter-nav__show-inner--show');
					$('.filter-nav__main').addClass('filter-nav__main--checked');
					$('#pager_content').html(data.pager);

					$('.tags_box, .expert_item-tags').each(function () {
				        var that = $(this);
				        var hiddens = that.find('.tag.hide, .tag.hide_mobile').length;
				        that.find('.tag.more u').text(hiddens);
				        if ($(this).find('.hide, .hide_mobile').length) {
				            $(this).find('.tag.more').show();
				        }
				    });
				    $('.tag.more').click(function () {
				        $(this).closest('.tags_box, .expert_item-tags').find('.tag.hide').removeClass('hide');
				        $(this).closest('.tags_box, .expert_item-tags').find('.tag.hide_mobile').removeClass('hide_mobile');
				        // $(this).closest('.tags_box, .expert_item-tags').find('.tag').css('display', 'inline-block');
				        $(this).remove();
				        return false;
				    });
					
				} else {
					// сообщение об ошибке на страницу
					$('#material_content').html('<h3 class="pb20"><i>К сожалению, по вашему запросу ничего не найдено</i></h3>');
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
JS;
$this->registerJs($js);
?>