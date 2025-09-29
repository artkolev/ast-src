<?php
?>
    <main class="sec content_sec">
        <div class="container wide">
            <h1 class="page-title"><?= $model->getNameForView(); ?></h1>
            <div class="subheader">
                <?= $model->content; ?>
            </div>
            <form method="get" class="mobile_filters_form filter-mob-form">
                <div class="mob_search_box">
                    <div class="mob-filter-buttons">
                        <button type="button" class="mob-filter-btn">Все фильтры</button>
                        <button type="button" class="mob-filter-clear-all  clear-filter-all_js">Сбросить</button>
                    </div>
                    <div class="mob-search-wrapper">
                        <input type="text" name="q" value="<?= $terms['q']; ?>" class="input_text ip_search"
                               placeholder="Поиск ">
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
                            <li class="filter-nav__item" data-filter="filter-start-program">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Старт программы</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li>
                            <li class="filter-nav__item" data-filter="filter-open-register">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Дополнительно</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li>
                            <li class="filter-nav__item" data-filter="filter-price">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Стоимость</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li>
                            <li class="filter-nav__item" data-filter="filter-format">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Формат обучения</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li>
                            <li class="filter-nav__item" data-filter="filter-type">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Вид программы</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li>
                            <li class="filter-nav__item" data-filter="filter-duration">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Длительность обучения</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li>
                            <li class="filter-nav__item" data-filter="filter-hours">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Количество часов</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li>
                            <li class="filter-nav__item" data-filter="filter-themes">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Кафедра</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li>
                            <li class="filter-nav__item" data-filter="filter-city">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Город</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li>
                        </ul>
                        <div class="filter-nav__show-inner">
                            <button class="button long filter-nav__show-btn js-mob-filters" type="submit"><span
                                        id="mobile_count">Показать</span></button>
                        </div>
                    </div>
                    <div class="filter-nav__sub filter_search_container" data-filter="filter-start-program">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Старт программы</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                        </div>
                        <div class="filter-nav__sub-container filter_search_list">
                            <div class="filter-nav__sub-container-inner">
                                <?php
                                for ($i = 0; $i < 12; $i++) {
                                    $date = strtotime("+" . $i . " month", time());
                                    $name = Yii::$app->formatter->asDatetime($date, 'LLLL'); ?>
                                    <div class="custom_dropdown-row">
                                        <input class="ch custom_dropdown-choice" <?= (in_array(date('n', $date), $terms['start_mounth']) ? 'checked="checked"' : ''); ?>
                                               type="checkbox" id="m-month<?= $i; ?>" name="start_mounth[]"
                                               data-value="<?= $name; ?>" value="<?= date('n', $date); ?>"/>
                                        <label><?= $name; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="filter-nav__show-inner">
                                <button class="button long filter-nav__show-btn" type="button">Применить</button>
                            </div>
                        </div>
                    </div>
                    <div class="filter-nav__sub filter_search_container" data-filter="filter-open-register">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Идёт набор</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                        </div>
                        <div class="filter-nav__sub-container">
                            <div class="filter-nav__sub-container-inner filter_search_list mScrollbarCustom simplebar">
                                <div class="custom_dropdown-row">
                                    <input id="m_js-reg-open" class="ch normal custom_dropdown-choice js-open-reg"
                                           type="checkbox" <?= ($terms['registration_open'] == 1) ? 'checked="checked"' : ''; ?>
                                           data-value="Идет набор">
                                    <label for="m_js-reg-open">Идет набор</label>
                                    <input type="hidden" name="registration_open"
                                           value="<?= ($terms['registration_open'] == 1) ? '1' : '0'; ?>">
                                </div>
                                <div class="custom_dropdown-row">
                                    <input id="m_js-corporative" class="ch normal custom_dropdown-choice js-corporative"
                                           type="checkbox" <?= ($terms['is_corporative'] == 1) ? 'checked="checked"' : ''; ?>
                                           data-value="Корпоративные программы">
                                    <label for="m_js-corporative">Корпоративные программы</label>
                                    <input type="hidden" name="is_corporative"
                                           value="<?= ($terms['is_corporative'] == 1) ? '1' : '0'; ?>">
                                </div>
                            </div>
                            <div class="filter-nav__show-inner">
                                <button class="button long filter-nav__show-btn" type="button">Применить</button>
                            </div>
                        </div>
                    </div>
                    <div class="filter-nav__sub filter_search_container" data-filter="filter-price">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Стоимость</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                        </div>
                        <div class="filter-nav__sub-container filter_search_list">
                            <div class="filter-nav__sub-container-inner">
                                <?php
                                $i = 0;
                                foreach ($prices_list as $value => $name) {
                                    $i++; ?>
                                    <div class="custom_dropdown-row">
                                        <input class="ch normal custom_dropdown-choice radio-toggle" <?= ($terms['price'] == $value) ? 'checked="checked"' : ''; ?>
                                               type="radio" id="m-price<?= $i; ?>" name="price"
                                               data-value="<?= $name; ?>" value="<?= $value; ?>"/>
                                        <label for="m-price<?= $i; ?>"><?= $name; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="filter-nav__show-inner">
                                <button class="button long filter-nav__show-btn" type="button">Применить</button>
                            </div>
                        </div>
                    </div>
                    <div class="filter-nav__sub filter_search_container" data-filter="filter-duration">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Длительность обучения</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                        </div>
                        <div class="filter-nav__sub-container filter_search_list">
                            <div class="filter-nav__sub-container-inner">
                                <?php
                                $i = 0;
                                foreach ($duration_list as $value => $name) {
                                    $i++; ?>
                                    <div class="custom_dropdown-row">
                                        <input class="ch custom_dropdown-choice" <?= ($terms['duration'] == $value) ? 'checked="checked"' : ''; ?>
                                               type="checkbox" id="m-duration<?= $i; ?>" data-value="<?= $name; ?>"
                                               name="duration" value="<?= $value; ?>"/>
                                        <label><?= $name; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="filter-nav__show-inner">
                                <button class="button long filter-nav__show-btn" type="button">Применить</button>
                            </div>
                        </div>
                    </div>
                    <div class="filter-nav__sub filter_search_container" data-filter="filter-hours">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Количество часов</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                        </div>
                        <div class="filter-nav__sub-container filter_search_list">
                            <div class="filter-nav__sub-container-inner">
                                <?php
                                $i = 0;
                                foreach ($hours_list as $value => $name) {
                                    $i++; ?>
                                    <div class="custom_dropdown-row">
                                        <input class="ch custom_dropdown-choice" <?= ($terms['hours'] == $value) ? 'checked="checked"' : ''; ?>
                                               type="checkbox" id="m-hours<?= $i; ?>" data-value="<?= $name; ?>"
                                               name="hours" value="<?= $value; ?>"/>
                                        <label><?= $name; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="filter-nav__show-inner">
                                <button class="button long filter-nav__show-btn" type="button">Применить</button>
                            </div>
                        </div>
                    </div>
                    <div class="filter-nav__sub filter_search_container" data-filter="filter-themes">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Кафедра</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                            <div class="filter-nav__search">
                                <input type="search" class="input_text ip_search ip_search2 filter_search_input"
                                       placeholder="Введите тематику"/>
                                <button class="ip_search2-button filter_search_input-button"></button>
                            </div>
                        </div>
                        <div class="filter-nav__sub-container">
                            <div class="filter-nav__sub-container-inner filter_search_list mScrollbarCustom simplebar">
                                <?php
                                $i = 0;
                                foreach ($directions_list as $value => $name) {
                                    $i++; ?>
                                    <div class="custom_dropdown-row">
                                        <input class="ch custom_dropdown-choice" <?= (in_array($value, $terms['directions']) ? 'checked="checked"' : ''); ?>
                                               type="checkbox" id="m-direction<?= $i; ?>" data-value="<?= $name; ?>"
                                               name="directions[]" value="<?= $value; ?>"/>
                                        <label><?= $name; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="filter-nav__show-inner">
                                <button class="button long filter-nav__show-btn" type="button">Применить</button>
                            </div>
                        </div>
                    </div>
                    <div class="filter-nav__sub filter_search_container" data-filter="filter-format">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Формат обучения</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                        </div>
                        <div class="filter-nav__sub-container filter_search_list">
                            <div class="filter-nav__sub-container-inner">
                                <?php
                                $i = 0;
                                foreach ($format_list as $value => $name) {
                                    $i++; ?>
                                    <div class="custom_dropdown-row">
                                        <input class="ch custom_dropdown-choice" <?= (in_array($value, $terms['formats']) ? 'checked="checked"' : ''); ?>
                                               type="checkbox" id="m-format<?= $i; ?>" data-value="<?= $name; ?>"
                                               name="formats[]" value="<?= $value; ?>"/>
                                        <label><?= $name; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="filter-nav__show-inner">
                                <button class="button long filter-nav__show-btn" type="button">Применить</button>
                            </div>
                        </div>
                    </div>
                    <div class="filter-nav__sub filter_search_container" data-filter="filter-type">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Вид программы</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                        </div>
                        <div class="filter-nav__sub-container filter_search_list">
                            <div class="filter-nav__sub-container-inner">
                                <?php
                                $i = 0;
                                foreach ($category_list as $value => $name) {
                                    $i++; ?>
                                    <div class="custom_dropdown-row">
                                        <input class="ch custom_dropdown-choice" <?= (in_array($value, $terms['category']) ? 'checked="checked"' : ''); ?>
                                               type="checkbox" id="m-category<?= $i; ?>" data-value="<?= $name; ?>"
                                               name="category[]" value="<?= $value; ?>"/>
                                        <label><?= $name; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="filter-nav__show-inner">
                                <button class="button long filter-nav__show-btn" type="button">Применить</button>
                            </div>
                        </div>
                    </div>
                    <div class="filter-nav__sub filter_search_container" data-filter="filter-city">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Город</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                            <div class="filter-nav__search">
                                <input type="search" class="input_text ip_search filter_search_input"
                                       placeholder="Введите название города"/>
                            </div>
                        </div>
                        <div class="filter-nav__sub-container ">
                            <div class="filter-nav__sub-container-inner">
                                <div class="city-filter-sidebar checkboxes_js mScrollbarCustom simplebar">
                                    <?php
                                    $i = 0;
                                    foreach ($cities_list as $value => $name) {
                                        $i++; ?>
                                        <div class="custom_dropdown-row">
                                            <input class="ch custom_dropdown-choice" <?= (in_array($value, $terms['city']) ? 'checked="checked"' : ''); ?>
                                                   type="checkbox" id="m-city<?= $i; ?>" data-value="<?= $name; ?>"
                                                   name="city[]" value="<?= $value; ?>"/>
                                            <label><?= $name; ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="filter-nav__show-inner">
                                <button class="button long filter-nav__show-btn" type="button">Применить</button>
                            </div>
                        </div>
                    </div>
                </nav>
            </form>
            <form method="get"
                  class="desktop_filters_form columns_box columns_box-filters filters-desktop-form all-events-form">
                <main class="main_col">
                    <?php /*  скрыто меню
                    $page_events = \app\modules\pages\models\Eventspage::find()->where(['model' => 'app\modules\pages\models\Eventspage', 'visible' => 1])->one();
$page_eduprog = \app\modules\pages\models\EduprogPage::find()->where(['model' => 'app\modules\pages\models\EduprogPage', 'visible' => 1])->one();
?>
				<div class="lenta-menu owl-carousel owl-theme">
					<?php if (!empty($page_events)) { ?><a href="<?= $page_events->getUrlPath(); ?>"><?= $page_events->getNameForView(); ?></a> <?php } ?>
					<?php if (!empty($page_eduprog)) { ?><a href="<?= $page_eduprog->getUrlPath(); ?>" class="active"><?= $page_eduprog->getNameForView(); ?></a> <?php } ?>
				</div>
				*/ ?>
                    <div class="filters filters-keywords">
                        <div class="filters-list-selected"></div>
                    </div>
                    <div class="all-events-list">
                        <div id="all-eduprog-cards" class="all-events-cards">
                            <?= $this->render('_eduprog_box', ['items' => $items, 'model' => $model, 'promo_items_ids' => $promo_items_ids]); ?>
                        </div>
                    </div>
                    <div id="pager_content">
                        <?= \app\widgets\pagination\LinkPager::widget(['pages' => $pages, 'container' => '#all-eduprog-cards']); ?>
                    </div>
                </main>
                <aside class="sidebar_col">
                    <div class="search_flex">
                        <div class="ip_cell search-wrapper">
                            <input type="text" name="q" value="<?= $terms['q']; ?>" class="input_text ip_search"
                                   placeholder="Введите название "/>
                            <button class="button-o button-search" type="submit">Применить</button>
                        </div>
                    </div>
                    <div class="sidebar_box">
                        <h3>Старт программы</h3>
                        <div class="months-slider owl-carousel owl-theme">
                            <?php $i = 0;
                            for ($i = 0; $i < 12; $i++) {
                                $date = strtotime("+" . $i . " month", time());
                                $name = Yii::$app->formatter->asDatetime($date, 'LLLL'); ?>
                                <div class="ip_cell-tag">
                                    <input type="checkbox"
                                           name="start_mounth[]" <?= (in_array(date('n', $date), $terms['start_mounth']) ? 'checked="checked"' : ''); ?>
                                           value="<?= date('n', $date); ?>" class="ch-tag filter_jmaka"/>
                                    <label><?= $name; ?></label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="sidebar_box">
                        <div class="checkboxes_js mScrollbarCustom simplebar">
                            <div class="ip_cell">
                                <input id="js-reg-open" type="checkbox"
                                       class="ch normal js-open-reg" <?= ($terms['registration_open'] == 1) ? 'checked="checked"' : ''; ?> />
                                <label for="js-reg-open">Идет набор</label>
                                <input type="hidden" name="registration_open"
                                       value="<?= ($terms['registration_open'] == 1) ? '1' : '0'; ?>"/>
                            </div>
                            <div class="ip_cell">
                                <input id="js-corporative" type="checkbox"
                                       class="ch normal js-corporative" <?= ($terms['is_corporative'] == 1) ? 'checked="checked"' : ''; ?> />
                                <label for="js-corporative">Корпоративные программы</label>
                                <input type="hidden" name="is_corporative"
                                       value="<?= ($terms['is_corporative'] == 1) ? '1' : '0'; ?>"/>
                            </div>
                        </div>
                    </div>
                    <div class="sidebar_box">
                        <h3>Стоимость</h3>
                        <div class="checkboxes_js mScrollbarCustom simplebar">
                            <div class="ip_cells-two-columns">
                                <?php $i = 0;
                                foreach ($prices_list as $value => $name) {
                                    $i++; ?>
                                    <div class="ip_cell">
                                        <input id="price_<?= $i; ?>" type="radio"
                                               name="price" <?= ($terms['price'] == $value) ? 'checked="checked"' : ''; ?>
                                               class="ch normal radio-toggle" value="<?= $value; ?>"/>
                                        <label for="price_<?= $i; ?>"><?= $name; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="sidebar_box">
                        <h3>Формат обучения</h3>
                        <div class="checkboxes_js">
                            <?php foreach ($format_list as $value => $name) { ?>
                                <div class="ip_cell">
                                    <input type="checkbox"
                                           name="formats[]" <?= (in_array($value, $terms['formats']) ? 'checked="checked"' : ''); ?>
                                           class="ch filter_jmaka" value="<?= $value; ?>"/>
                                    <label><?= $name; ?></label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="sidebar_box">
                        <h3>Вид программы</h3>
                        <div class="checkboxes_js mScrollbarCustom simplebar">
                            <?php foreach ($category_list as $value => $name) { ?>
                                <div class="ip_cell">
                                    <input type="checkbox"
                                           name="category[]" <?= (in_array($value, $terms['category']) ? 'checked="checked"' : ''); ?>
                                           class="ch filter_jmaka" value="<?= $value; ?>"/>
                                    <label><?= $name; ?></label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="sidebar_box">
                        <h3>Длительность обучения</h3>
                        <div class="checkboxes_js mScrollbarCustom simplebar">
                            <div class="ip_cells-two-columns">
                                <?php $i = 0;
                                foreach ($duration_list as $value => $name) {
                                    $i++; ?>
                                    <div class="ip_cell">
                                        <input id="duration_<?= $i; ?>" type="radio"
                                               name="duration" <?= ($terms['duration'] == $value) ? 'checked="checked"' : ''; ?>
                                               class="ch normal radio-toggle" value="<?= $value; ?>"/>
                                        <label for="duration_<?= $i; ?>"><?= $name; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="sidebar_box">
                        <h3>Количество часов</h3>
                        <div class="checkboxes_js mScrollbarCustom simplebar">
                            <div class="ip_cells-two-columns">
                                <?php $i = 0;
                                foreach ($hours_list as $value => $name) {
                                    $i++; ?>
                                    <div class="ip_cell">
                                        <input id="hours_<?= $i; ?>" type="radio"
                                               name="hours" <?= ($terms['hours'] == $value) ? 'checked="checked"' : ''; ?>
                                               class="ch normal radio-toggle" value="<?= $value; ?>"/>
                                        <label for="hours_<?= $i; ?>"><?= $name; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="sidebar_box">
                        <h3>Кафедра</h3>
                        <div class="checkboxes_js mScrollbarCustom simplebar">
                            <?php
                            $i = 0;
                            $dop_attr = '';
                            foreach ($directions_list as $value => $name) {
                                $i++; ?>
                                <div class="ip_cell" <?= $dop_attr; ?>>
                                    <input type="checkbox"
                                           name="directions[]" <?= (in_array($value, $terms['directions']) ? 'checked="checked"' : ''); ?>
                                           class="ch filter_jmaka" value="<?= $value; ?>"/>
                                    <label><?= $name; ?></label>
                                </div>
                                <?php
                                if ($i == 4) {
                                    $dop_attr = 'style="display: none;"'; ?>
                                    <div class="filters_buttons">
                                        <a href="#" class="button-o small button-o-grey button-more-filters">Показать
                                            еще</a>
                                    </div>
                                <?php }
                            } ?>
                        </div>
                    </div>
                    <div class="sidebar_box">
                        <h3>Город</h3>
                        <div class="city-form">
                            <input type="text" class="input_text ip_search filter_search_input"
                                   placeholder="Введите город "/>
                        </div>
                        <div class="city-filter-sidebar checkboxes_js mScrollbarCustom simplebar">
                            <?php foreach ($cities_list as $value => $name) { ?>
                                <div class="ip_cell">
                                    <input type="checkbox"
                                           name="city[]" <?= (in_array($value, $terms['city']) ? 'checked="checked"' : ''); ?>
                                           class="ch filter_jmaka" value="<?= $value; ?>"/>
                                    <label><?= $name; ?></label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="filters_buttons">
                        <a href="#" class="button button-o button-o-grey filters_reset">Сбросить фильтры</a>
                    </div>
                </aside>
            </form>
        </div>
    </main>
<?php
$this->registerCssFile('/css/style-blog.css', ['depends' => [app\assets\AppAsset::class]]);
$this->registerJsFile('/js/filters.js', ['depends' => [app\assets\AppAsset::class]]);
$this->registerJsFile('/js/main-blog.js', ['depends' => [app\assets\AppAsset::class]]);

$url = $model->getUrlPath();

$js = <<<JS
	if(window.innerWidth > 1100) {
		$('body').on('change','.custom_dropdown-selected-button, .filter_jmaka, .tap_tap_change', function(e) {
			$(this).closest('form').submit();
		});
	} else {
		$('body').on('click','.mob-search-button, .js-mob-filters, .filter-mob-nav__show-btn', function(e) {
			e.preventDefault();
			$(this).closest('form').submit();
		});
	}

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

	$('.radio-toggle').on('click', function(e) {
		e.preventDefault();
		setTimeout(
			() => $(this).prop("checked", !this.checked).trigger("change")
		);
	});
	$('.radio-toggle').on('change', function(e) {
		$(this).closest('form').submit();
	});
    
    $('.js-open-reg').click(function(e) {
		if($(this).prop('checked') == true) {
			$(this).parent().find('[name="registration_open"]').val('1');
		} else {
			$(this).parent().find('[name="registration_open"]').val('0');
		}
		setTimeout(
			() => $(this).closest('form').submit()
		);
	});
    
    $('.js-corporative').click(function(e) {
		if($(this).prop('checked') == true) {
			$(this).parent().find('[name="is_corporative"]').val('1');
		} else {
			$(this).parent().find('[name="is_corporative"]').val('0');
		}
		setTimeout(
			() => $(this).closest('form').submit()
		);
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
					// заменить содержимое all-eduprog-cards
					$('#all-eduprog-cards').html(data.html);
					$('#pager_content').html(data.pager);
				} else {
					// сообщение об ошибке на страницу
					$('#all-eduprog-cards').html('<h3 class="pb20"><i>К сожалению, по вашему запросу ничего не найдено</i></h3>');
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
					// заменить содержимое all-eduprog-cards
					$('#all-eduprog-cards').html(data.html);
					// написать кол-во результатов
					// $('#mobile_count').html(data.count);
					// показать плашку с результатами
					$('.filter-nav__show-inner').addClass('filter-nav__show-inner--show');
					$('.filter-nav__main').addClass('filter-nav__main--checked');
					$('#pager_content').html(data.pager);
				} else {
					// сообщение об ошибке на страницу
					$('#all-eduprog-cards').html('<h3 class="pb20"><i>К сожалению, по вашему запросу ничего не найдено</i></h3>');
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