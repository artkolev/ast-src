<?php

use app\widgets\login\LoginWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

?>
    <main class="sec content_sec">
        <div class="container wide">
            <h1><?= $model->page_title ? $model->page_title : ($service_type_page->page_title ? $service_type_page->page_title : 'Услуги'); ?></h1>
            <div class="subheader"><?= $model->page_subtitle ? $model->page_subtitle : ($service_type_page->page_subtitle ? $service_type_page->page_subtitle : 'Эксперты, которые работают с этим видом услуги'); ?></div>
            <form action="" method="post" class="filter-mob-form mobile_filters_form services-filters">

                <div class="mob_search_box">
                    <div class="mob-filter-buttons">
                        <button type="button" class="mob-filter-btn">Все фильтры</button>
                        <button type="button" class="mob-filter-clear-all  clear-filter-all_js">Сбросить</button>
                    </div>
                    <div class="mob-search-wrapper">
                        <input type="text" name="query" value="<?= $terms['search']; ?>" class="input_text ip_search"
                               placeholder="Поиск"/>
                        <button type="submit" class="mob-search-button">Применить</button>
                    </div>
                </div>

                <div class="nav-overlay"></div>
                <nav class="filter-nav">
                    <!-- <form action="" method="POST" class="filter-nav__form"> -->
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
                            <li class="filter-nav__item" data-filter="filter-111">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Вид услуги</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li>
                            <li class="filter-nav__item" data-filter="filter-4">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Формат</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li>
                            <li class="filter-nav__item  filter-nav__item--range" data-filter="filter-5">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Стоимость</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-range_js">Сбросить</a>
                                </div>
                                <div class="range-filter  range-filter-price_js">
                                    <div class="range-filter_item">
                                        <input type="number" placeholder="от 0" data-min="0"
                                               class="range-filter_input range-filter_input-from" name="price_from"
                                               value="<?= isset($terms['price']) && isset($terms['price']['from']) ? $terms['price']['from'] : ''; ?>">
                                        <button type="button" class="clear-input-btn"></button>
                                    </div>
                                    <div class="range-filter_item">
                                        <input type="number" placeholder="до 100 000" data-max="100000"
                                               class="range-filter_input range-filter_input-to" name="price_to"
                                               value="<?= isset($terms['price']) && isset($terms['price']['to']) ? $terms['price']['to'] : ''; ?>">
                                        <button type="button" class="clear-input-btn"></button>
                                    </div>
                                </div>
                            </li>
                            <li class="filter-nav__item" data-filter="filter-333">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Для кого</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li>
                            <li class="filter-nav__item" data-filter="filter-334">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Кафедра</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li>
                            <li class="filter-nav__item" data-filter="filter-6">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link filter-nav__link-city" href="#"><span>Город</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li>
                        </ul>
                        <div class="filter-nav__show-inner">
                            <button class="button long filter-nav__show-btn js-filter-nav__show-btn" type="submit">
                                Показать
                            </button>
                        </div>
                    </div>

                    <div class="filter-nav__sub filter_search_container" data-filter="filter-111">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Вид услуги</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                            <div action="" method="post" class="filter-nav__search">
                                <input type="search" class="input_text ip_search ip_search2 filter_search_input"
                                       placeholder="Введите название"/>
                                <button class="ip_search2-button filter_search_input-button"></button>
                            </div>
                        </div>
                        <div class="custom_dropdown_title not-found-title" style="display: none;">Ничего не найдено
                        </div>
                        <div class="filter-nav__sub-container filter_search_list">
                            <div class="filter-nav__sub-container-inner mScrollbarCustom simplebar">
                                <div class="custom_dropdown-row">
                                    <input class="ch custom_dropdown-choice mob-all-checks" type="checkbox"
                                           id="all_service_types" data-value="Все"/>
                                    <label>Все</label>
                                </div>
                                <?php foreach ($service_types as $item) { ?>
                                    <div class="custom_dropdown-row">
                                        <input id="m_directs_<?= $item->id; ?>"
                                               class="ch custom_dropdown-choice" <?= (in_array($item->id, $terms['service_types'])) ? 'checked="checked"' : ''; ?>
                                               name="service_types[]" class="filter_jmaka ch" type="checkbox"
                                               data-value="<?= $item->name; ?>" value="<?= $item->id; ?>"/>
                                        <label><?= $item->name; ?></label>
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

                    <div class="filter-nav__sub filter_search_container" data-filter="filter-4">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Формат</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                        </div>
                        <div class="filter-nav__sub-container filter_search_list">
                            <div class="filter-nav__sub-container-inner mScrollbarCustom simplebar">
                                <div class="custom_dropdown-row">
                                    <input id="m_service_kind_0"
                                           class="ch custom_dropdown-choice" <?= (in_array(0, $terms['service_kind'])) ? 'checked="checked"' : ''; ?>
                                           name="service_kind[]" class="filter_jmaka ch" type="checkbox"
                                           data-value="Офлайн" value="offline"/>
                                    <label>Офлайн</label>
                                </div>
                                <div class="custom_dropdown-row">
                                    <input id="m_service_kind_1"
                                           class="ch custom_dropdown-choice" <?= (in_array(1, $terms['service_kind'])) ? 'checked="checked"' : ''; ?>
                                           name="service_kind[]" class="filter_jmaka ch" type="checkbox"
                                           data-value="Онлайн" value="online"/>
                                    <label>Онлайн</label>
                                </div>
                                <div class="custom_dropdown-row">
                                    <input id="m_service_kind_2"
                                           class="ch custom_dropdown-choice" <?= (in_array(2, $terms['service_kind'])) ? 'checked="checked"' : ''; ?>
                                           name="service_kind[]" class="filter_jmaka ch" type="checkbox"
                                           data-value="Онлайн и Офлайн" value="hybrid"/>
                                    <label>Онлайн и Офлайн</label>
                                </div>
                            </div>
                            <div class="filter-nav__show-inner">
                                <button class="button long filter-nav__show-btn filter-mob-nav__show-btn" type="button">
                                    Применить
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="filter-nav__sub filter_search_container" data-filter="filter-333">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Для кого</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                            <div action="" method="post" class="filter-nav__search">
                                <input type="search" class="input_text ip_search ip_search2 filter_search_input"
                                       placeholder="Введите название"/>
                                <button class="ip_search2-button filter_search_input-button"></button>
                            </div>
                        </div>
                        <div class="custom_dropdown_title not-found-title" style="display: none;">Ничего не найдено
                        </div>
                        <div class="filter-nav__sub-container filter_search_list">
                            <div class="filter-nav__sub-container-inner mScrollbarCustom simplebar">
                                <div class="custom_dropdown-row">
                                    <input class="ch custom_dropdown-choice mob-all-checks" type="checkbox"
                                           id="all_target_audiences" data-value="Все"/>
                                    <label>Все</label>
                                </div>
                                <?php foreach ($audience_list as $item) { ?>
                                    <div class="custom_dropdown-row">
                                        <input id="m_target_audiences_<?= $item->id; ?>"
                                               class="ch custom_dropdown-choice" <?= (in_array($item->id, $terms['target_audience'])) ? 'checked="checked"' : ''; ?>
                                               name="target_audience[]" class="filter_jmaka ch" type="checkbox"
                                               data-value="<?= $item->name; ?>" value="<?= $item->id; ?>"/>
                                        <label><?= $item->name; ?></label>
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

                    <div class="filter-nav__sub filter_search_container" data-filter="filter-334">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Кафедра</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                            <div action="" method="post" class="filter-nav__search">
                                <input type="search" class="input_text ip_search ip_search2 filter_search_input"
                                       placeholder="Введите название"/>
                                <button class="ip_search2-button filter_search_input-button"></button>
                            </div>
                        </div>
                        <div class="custom_dropdown_title not-found-title" style="display: none;">Ничего не найдено
                        </div>
                        <div class="filter-nav__sub-container filter_search_list">
                            <div class="filter-nav__sub-container-inner mScrollbarCustom simplebar">
                                <div class="custom_dropdown-row">
                                    <input class="ch custom_dropdown-choice mob-all-checks" type="checkbox"
                                           id="all_directs" data-value="Все"/>
                                    <label>Все</label>
                                </div>
                                <?php foreach ($directs as $item) { ?>
                                    <div class="custom_dropdown-row">
                                        <input id="m_directs_<?= $item->id; ?>"
                                               class="ch custom_dropdown-choice" <?= (in_array($item->id, $terms['directs'])) ? 'checked="checked"' : ''; ?>
                                               name="directs[]" class="filter_jmaka ch" type="checkbox"
                                               data-value="<?= $item->name; ?>" value="<?= $item->id; ?>"/>
                                        <label><?= $item->name; ?></label>
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

                    <div class="filter-nav__sub filter_search_container" data-filter="filter-6">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Город</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                            <div class="filter-nav__search">
                                <input type="search" class="input_text ip_search ip_search2 filter_search_input"
                                       placeholder="Введите город"/>
                                <button class="ip_search2-button filter_search_input-button"></button>
                            </div>
                        </div>
                        <div class="custom_dropdown_title not-found-title" style="display: none;">Ничего не найдено
                        </div>
                        <div class="filter-nav__sub-container filter_search_list">
                            <div class="filter-nav__sub-container-inner">
                                <div class="city-filter-sidebar city-filter-sidebar-mobile checkboxes_js">
                                    <?php foreach ($cities_short as $city) { ?>
                                        <div class="custom_dropdown-row <?= (in_array($city->id, $terms['city'])) ? 'custom_dropdown-row-choise' : ''; ?>">
                                            <input id="m_city_<?= $city->id; ?>"
                                                   class="ch custom_dropdown-choice" <?= (in_array($city->id, $terms['city'])) ? 'checked="checked"' : ''; ?>
                                                   type="checkbox" name="city[]" data-value="<?= $city->name; ?>"
                                                   value="<?= $city->id; ?>"/>
                                            <label><?= $city->name; ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
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
            <form action="" method="get" class="columns_box  columns_box-filters filters-desktop-form">
                <main class="main_col">
                    <div class="directions_search_box  filters">
                        <!--
                        <div class="filters-list-selected"></div>
                        <div class="expert_item-tags">
                            <a class="tag" href="#" data-tagid="21" data-tagname="психологическое консультирование"><b class="tag-hovered">психологическое консультирование</b><span>психологическое консультирование</span></a>
                            <a class="tag" href="#" data-tagid="21" data-tagname="психологическое"><b class="tag-hovered">психологическое</b><span>психологическое</span></a>
                            <a class="tag" href="#" data-tagid="21" data-tagname="консультирование"><b class="tag-hovered">консультирование</b><span>консультирование</span></a>
                            <a class="tag" href="#" data-tagid="21" data-tagname="психоло"><b class="tag-hovered">психоло</b><span>психоло</span></a>
                        </div>
                        -->
                    </div>
                    <div class="filters filters-keywords">
                        <div class="filters-list-selected"></div>
                    </div>

                    <div id="services_expert_items">
                        <?php if (!empty($items)) { ?>
                            <?= $this->render('_service_types_box', ['items' => $items]); ?>
                        <?php } ?>
                    </div>

                    <div id="pager_content">
                        <?= app\widgets\pagination\LinkPager::widget(['pages' => $pages, 'container' => '#services_expert_items']); ?>
                    </div>
                </main>

                <aside class="sidebar_col">

                    <div class="search_flex">
                        <div class="ip_cell search-wrapper">
                            <input type="text" name="query" value="<?= $terms['search']; ?>"
                                   class="input_text ip_search" placeholder="Введите название ">
                            <button class="button-o button-search" type="submit">Применить</button>
                        </div>
                    </div>

                    <?php if (!empty($service_types)) { ?>
                        <div class="sidebar_box">
                            <h3>Вид услуги</h3>
                            <div class="checkboxes_js mScrollbarCustom simplebar">
                                <div class="ip_cell">
                                    <input type="checkbox" name="" class="ch allChecks filter_jmaka" value=""/>
                                    <label>Все</label>
                                </div>
                                <?php foreach ($service_types as $item) { ?>
                                    <div class="ip_cell">
                                        <input type="checkbox" <?= (in_array($item->id, $terms['service_types'])) ? 'checked="checked"' : ''; ?>
                                               name="service_types[]" class="filter_jmaka ch"
                                               value="<?= $item->id; ?>"/>
                                        <label><?= $item->name; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="sidebar_box">
                        <h3>Формат оказания услуг</h3>
                        <div class="checkboxes_js">
                            <div class="ip_cell">
                                <input type="checkbox" <?= (in_array(0, $terms['service_kind'])) ? 'checked="checked"' : ''; ?>
                                       name="service_kind[]" value="offline" class="filter_jmaka ch"/>
                                <label>Офлайн</label>
                            </div>
                            <div class="ip_cell">
                                <input type="checkbox" <?= (in_array(1, $terms['service_kind'])) ? 'checked="checked"' : ''; ?>
                                       name="service_kind[]" value="online" class="filter_jmaka ch"/>
                                <label>Онлайн</label>
                            </div>
                            <div class="ip_cell">
                                <input type="checkbox" <?= (in_array(2, $terms['service_kind'])) ? 'checked="checked"' : ''; ?>
                                       name="service_kind[]" value="hybrid" class="filter_jmaka ch"/>
                                <label>Онлайн и Офлайн</label>
                            </div>
                        </div>
                    </div>
                    <div class="sidebar_box">
                        <h3>Стоимость услуги</h3>
                        <div class="range-filter  range-filter-price_js">
                            <div class="range-filter_item">
                                <input type="number" name="price_from" placeholder="от 0"
                                       class="numbersOnly range-filter_input range-filter_input-from"
                                       value="<?= isset($terms['price']) ? $terms['price']['from'] : ''; ?>">
                                <button type="button" class="clear-input-btn"></button>
                            </div>
                            <div class="range-filter_item">
                                <input type="number" name="price_to" placeholder="до 100 000"
                                       class="numbersOnly range-filter_input range-filter_input-to"
                                       value="<?= isset($terms['price']) ? $terms['price']['to'] : ''; ?>">
                                <button type="button" class="clear-input-btn"></button>
                            </div>
                        </div>
                        <!-- <button type="submit" class="button-o small button-o-grey">OK</button> -->
                    </div>

                    <div class="sidebar_box">
                        <h3>Для кого</h3>
                        <div class="checkboxes_js mScrollbarCustom simplebar">
                            <div class="ip_cell">
                                <input type="checkbox" name="" class="ch allChecks filter_jmaka" value=""/>
                                <label>Все</label>
                            </div>
                            <?php foreach ($audience_list as $item) { ?>
                                <div class="ip_cell">
                                    <input type="checkbox" <?= (in_array($item->id, $terms['target_audience'])) ? 'checked="checked"' : ''; ?>
                                           name="target_audience[]" class="filter_jmaka ch" value="<?= $item->id; ?>"/>
                                    <label><?= $item->name; ?></label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="sidebar_box">
                        <h3>Кафедра</h3>
                        <div class="checkboxes_js mScrollbarCustom simplebar">
                            <div class="ip_cell">
                                <input type="checkbox" name="" class="ch allChecks filter_jmaka" value=""/>
                                <label>Все</label>
                            </div>
                            <?php foreach ($directs as $item) { ?>
                                <div class="ip_cell">
                                    <input type="checkbox" <?= (in_array($item->id, $terms['directs'])) ? 'checked="checked"' : ''; ?>
                                           name="directs[]" class="filter_jmaka ch" value="<?= $item->id; ?>"/>
                                    <label><?= $item->name; ?></label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="sidebar_box">
                        <h3>Город</h3>
                        <div class="city-form">
                            <input type="text" class="input_text ip_search filter_search_input"
                                   placeholder="Введите город "/>
                        </div>
                        <div class="city-filter-sidebar city-filter-sidebar-desktop checkboxes_js">
                            <?php foreach ($cities_short as $city) { ?>
                                <div class="ip_cell <?= (in_array($city->id, $terms['city'])) ? 'ip_cell-choise' : ''; ?>">
                                    <input type="checkbox" <?= (in_array($city->id, $terms['city'])) ? 'checked="checked"' : ''; ?>
                                           name="city[]" value="<?= $city->id; ?>" class="filter_jmaka ch"/>
                                    <label><?= $city->name; ?></label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- <a href="#" class="button-o button-o-grey go_filter">Все фильтры</a> -->
                    <a href="#" class="button button-o button-o-grey filters_reset">Сбросить фильтры</a>

                    <?= \app\modules\banner\widgets\banner\PageBannerWidget::widget(['id' => $this->params['page_id']]); ?>
                </aside>
            </form>

            <?= \app\modules\banner\widgets\banner\PageBannerWidget::widget(['id' => $this->params['page_id'], 'mobile' => '1']); ?>

            </form>
        </div>
    </main>
<?= \app\modules\queries\widgets\queries\QueriesWidget::widget(); ?>
<?= \app\modules\feedacadem\widgets\feedacadem\FeedacademWidget::widget(); ?>
<?= \app\modules\pages\widgets\ordercreate\OrderCreateWidget::widget(); ?>
<?php
$this->registerJsFile('/js/filters.js', ['depends' => [app\assets\AppAsset::class]]);

$this->registerJsVar('filter_suffix', '-servicetypes', $position = yii\web\View::POS_HEAD);

$cities_list = ArrayHelper::map($cities_short, 'id', 'id');
$this->registerJsVar('excluded', $cities_list, $position = yii\web\View::POS_HEAD);

$target_audience_list = ArrayHelper::map($selected_target_audience, 'id', 'id');
$this->registerJsVar('excluded_target_audience', $target_audience_list, $position = yii\web\View::POS_HEAD);

$tasks_list = ArrayHelper::map($selected_tasks, 'id', 'id');
$this->registerJsVar('excluded_task', $tasks_list, $position = yii\web\View::POS_HEAD);

$competence_list = ArrayHelper::map($selected_competences, 'id', 'id');
$this->registerJsVar('excluded_competence', $competence_list, $position = yii\web\View::POS_HEAD);

$this->registerJsVar('excluded_servicegroup', '', $position = yii\web\View::POS_HEAD);

$url = Url::toRoute(['/service_types/']);
$js = <<<JS
	if(window.innerWidth > 1100) {
		$('body').on('change','.custom_dropdown-selected-button, .filter_jmaka, .tap_tap_change', function(e) {
			$(this).closest('form').submit();
		});
		if($('.filters-desktop-form input:checked').length) {
			$('.event_reset-btn').removeClass('disabled');
		}
		$('body').on('change','.custom_dropdown-choice', function(e) {
	    	let parent = $(this).closest('.custom_dropdown-list');
	    	if(parent.find('.filter_selected_item').length < 2) {
				$(this).closest('form').submit();
			}
		});
	} else {
		$('body').on('change', '.js-filter-nav__show-btn', function (e) {
			$('.filter-mob-form.events-filters').submit();

		});
	}

	$('body').on('keyup change','.range-filter_input', function(e) {
		if(($('.filters-desktop-form .range-filter_input-from').val() != '' && $('.filters-desktop-form .range-filter_input-to').val() != '')) {
			$(this).closest('form').submit();
		}
	});

	// выполняется на document.ready
	function cityFilterDesk() {
		$.ajax({
			type: 'GET',
			url: '/filter/cities'+filter_suffix+'/?roles=',
			processData: true,
			data: {exclude: excluded},
			dataType: 'json',
			success: function(data) {
				if (data.status == 'success') {
					// список городов приходит в data.result
					$.each(data.result.result, function(index, city) {
	                    $('.city-filter-sidebar-desktop').append('<div class="ip_cell"><input type="checkbox" name="city[]" value="'+ city.id +'" class="filter_jmaka ch" /><label>'+ city.name +'</label></div>')
	                });
	                $('.city-filter-sidebar-desktop').mCustomScrollbar();
				} else {
					// что-то пошло не так и списка городов нету
					
				}
		    }
		});
	};

	function cityFilterMob() {
		let click = 1;
		$('.filter-nav__link-city').click(function(){
			if(click == 1) {
				$.ajax({
					type: 'GET',
					url: '/filter/cities'+filter_suffix+'/?roles=',
					processData: true,
					data: {exclude: excluded},
					dataType: 'json',
					success: function(data) {
						if (data.status == 'success') {
							// список городов приходит в data.result
							$.each(data.result.result, function(index, city) {
			                    $('.city-filter-sidebar-mobile').append('<div class="custom_dropdown-row"><input id="m_city_'+ city.id +'" class="ch custom_dropdown-choice" type="checkbox" name="city[]" data-value="'+ city.name +'" value="'+ city.id +'" /><label>'+ city.name +'</label></div>')
			                });
			                $('.city-filter-sidebar-mobile').mCustomScrollbar();
						} else {
							// что-то пошло не так и списка городов нету
							
						}
				    }
				});
				click++;
			}
		});
	};
	if(window.innerWidth > 1100) {
		cityFilterDesk();
	} else {
		cityFilterMob();
	}

	$('body').on('submit','.filters-desktop-form', function(e) {
		let new_url = $(this).serialize();
		$.ajax({
			type: 'GET',
			url: '{$url}?'+new_url,
			processData: true,
			dataType: 'json',
			success: function(data) {
				if (data.status == 'success') {
					// заменить содержимое event_items
					$('#services_expert_items').html(data.html);
					$('#pager_content').html(data.pager);
				} else {
					// сообщение об ошибке на страницу
					$('#services_expert_items').html('<h3 class="pb20"><i>К сожалению, по вашему запросу ничего не найдено</i></h3>');
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
					// заменить содержимое event_items
					$('#services_expert_items').html(data.html);
					// написать кол-во результатов
					// $('#mobile_count').html(data.count);
					// показать плашку с результатами
					$('.filter-nav__show-inner').addClass('filter-nav__show-inner--show');
					$('.filter-nav__main').addClass('filter-nav__main--checked');
					// закрыть мобильные фильтры
					$('.filter-nav').removeClass('filter-nav--open');
					$('.nav-overlay').removeClass('nav-overlay--open');
					$('body').removeClass('modal-open');
					$('.filter-nav__sub').removeClass('filter-nav__sub--open');
					$('#pager_content').html(data.pager);
				} else {
					// сообщение об ошибке на страницу
					$('#services_expert_items').html('<h3 class="pb20"><i>К сожалению, по вашему запросу ничего не найдено</i></h3>');
					// написать кол-во результатов
					$('#mobile_count').html('Предложения не найдены');
					// показать плашку с результатами
					$('.filter-nav__show-inner').addClass('filter-nav__show-inner--show');
					$('.filter-nav__main').addClass('filter-nav__main--checked');
					// закрыть мобильные фильтры
					$('.filter-nav').removeClass('filter-nav--open');
					$('.nav-overlay').removeClass('nav-overlay--open');
					$('body').removeClass('modal-open');
					$('.filter-nav__sub').removeClass('filter-nav__sub--open');
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
		let solvtask = searchParams.get("solvtask");
		let competence = searchParams.get("competence");
		let keyword = searchParams.get("keyword");
		if(solvtask !== null) {
			$('.filters-list-selected').append(createLabelTag(solvtask));
			$('.filters_reset').removeClass('disabled');
		}
		if(competence !== null) {
			$('.filters-list-selected').append(createLabelTag(competence));
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
?>