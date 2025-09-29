<?php

use app\modules\events\models\Events;

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
                        <input type="text" name="q" value="<?= $search_text; ?>" class="input_text ip_search"
                               placeholder="Поиск ">
                        <button type="submit" class="mob-search-button">Применить</button>
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
                            <li class="filter-nav__item  filter-nav__item--range" data-filter="filter-date">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Дата проведения</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-range_js">Сбросить</a>
                                </div>
                                <div class="range-filter dateRange two-inputs">
                                    <div class="range-filter_item">
                                        <input type="text" name="date_from"
                                               value="<?= isset($terms['date']) && isset($terms['date']['from']) ? $terms['date']['from'] : ''; ?>"
                                               class="range-filter_input date-range1" placeholder="Дата от" size="20"/>
                                        <button type="button" class="clear-input-btn"></button>
                                    </div>
                                    <div class="range-filter_item">
                                        <input type="text" name="date_to"
                                               value="<?= isset($terms['date']) && isset($terms['date']['to']) ? $terms['date']['to'] : ''; ?>"
                                               class="range-filter_input date-range2" placeholder="Дата до" size="20"/>
                                        <button type="button" class="clear-input-btn"></button>
                                    </div>
                                </div>
                            </li>
                            <li class="filter-nav__item" data-filter="filter-extra">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Дополнительно</span><i></i></a>
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
                            <li class="filter-nav__item" data-filter="filter-price">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Стоимость</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li>
                            <li class="filter-nav__item" data-filter="filter-format">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Формат</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li>
                            <li class="filter-nav__item" data-filter="filter-type">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Вид мероприятия</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li>
                            <li class="filter-nav__item" data-filter="filter-city">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link filter-nav__link-city" href="#"><span>Город</span><i></i></a>
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

                    <div class="filter-nav__sub filter_search_container" data-filter="filter-extra">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Дополнительно</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                        </div>
                        <div class="filter-nav__sub-container">
                            <div class="filter-nav__sub-container-inner filter_search_list mScrollbarCustom simplebar">
                                <div class="custom_dropdown-row">
                                    <input id="m_js-reg-open" class="ch normal custom_dropdown-choice js-open-reg"
                                           type="checkbox" <?= ($terms['registration_open'] == 1) ? 'checked="checked"' : ''; ?>
                                           data-value="Открыта регистрация">
                                    <label for="m_js-reg-open">Открыта регистрация</label>
                                    <input type="hidden" name="registration_open"
                                           value="<?= ($terms['registration_open'] == 1) ? '1' : '0'; ?>">
                                </div>
                                <div class="custom_dropdown-row">
                                    <input id="m_is_ast"
                                           class="ch custom_dropdown-choice" <?= ($terms['is_ast'] == 1) ? 'checked="checked"' : ''; ?>
                                           type="checkbox" name="is_ast" data-value="Мероприятия Академии" value="1"/>
                                    <label>Мероприятия Академии</label>
                                </div>
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
                                       placeholder="Введите название кафедры"/>
                                <button class="ip_search2-button filter_search_input-button"></button>
                            </div>
                        </div>
                        <div class="filter-nav__sub-container">
                            <div class="filter-nav__sub-container-inner filter_search_list mScrollbarCustom simplebar">
                                <?php foreach ($directions as $i => $direction) { ?>
                                    <div class="custom_dropdown-row">
                                        <input id="m_direction_<?= $direction->id; ?>"
                                               class="ch custom_dropdown-choice <?= (in_array($direction->id, $terms['directs'])) ? 'checked="checked"' : ''; ?>"
                                               type="checkbox" name="directs[]" data-value="<?= $direction->name; ?>"
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

                    <div class="filter-nav__sub filter_search_container" data-filter="filter-price">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Стоимость, ₽</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                        </div>
                        <div class="filter-nav__sub-container filter_search_list">
                            <div class="filter-nav__sub-container-inner">
                                <?php foreach ([['name' => 'Бесплатные', 'id' => 1, 'value' => 'free'], ['name' => 'до 1 000 ₽', 'id' => 2, 'value' => '1000'], ['name' => 'до 5 000 ₽', 'id' => 3, 'value' => '5000'], ['name' => '5 000+ ₽', 'id' => 4, 'value' => '5000+']] as $price) { ?>
                                    <div class="custom_dropdown-row">
                                        <input id="m_price_<?= $price['id']; ?>"
                                               type="radio" <?= ($price['value'] == $terms['price']) ? 'checked="checked"' : ''; ?>
                                               name="price" class="ch normal custom_dropdown-choice radio-toggle"
                                               value="<?= $price['value']; ?>" data-value="<?= $price['name']; ?>"/>
                                        <label for="m_price_<?= $price['id']; ?>"><?= $price['name']; ?></label>
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

                    <div class="filter-nav__sub filter_search_container" data-filter="filter-format">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Формат</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                        </div>
                        <div class="filter-nav__sub-container filter_search_list">
                            <div class="filter-nav__sub-container-inner">
                                <?php foreach ([['name' => 'Онлайн', 'id' => 1, 'value' => Events::TYPE_ONLINE], ['name' => 'Офлайн', 'id' => 0, 'value' => Events::TYPE_OFFLINE], ['name' => 'Онлайн и Офлайн', 'id' => 2, 'value' => Events::TYPE_HYBRID]] as $event_format) { ?>
                                    <div class="custom_dropdown-row">
                                        <input id="m_events_formats_<?= $event_format['id']; ?>"
                                               type="checkbox" <?= (in_array($event_format['id'], $terms['event_format'])) ? 'checked="checked"' : ''; ?>
                                               name="event_format[]" class="ch custom_dropdown-choice filter_jmaka"
                                               value="<?= $event_format['value']; ?>"
                                               data-value="<?= $event_format['name']; ?>"/>
                                        <label><?= $event_format['name']; ?></label>
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

                    <div class="filter-nav__sub filter_search_container" data-filter="filter-type">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Вид мероприятия</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                            <div class="filter-nav__search">
                                <input type="search" class="input_text ip_search ip_search2 filter_search_input"
                                       placeholder="Введите вид мероприятия"/>
                                <button class="ip_search2-button filter_search_input-button"></button>
                            </div>
                        </div>
                        <div class="filter-nav__sub-container">
                            <div class="filter-nav__sub-container-inner filter_search_list mScrollbarCustom simplebar">
                                <?php foreach ($eventsformats as $i => $eventsformat) { ?>
                                    <div class="custom_dropdown-row">
                                        <input id="m_eventsformats_<?= $eventsformat->id; ?>"
                                               class="ch custom_dropdown-choice" <?= (in_array($eventsformat->id, $terms['eventsformats'])) ? 'checked="checked"' : ''; ?>
                                               type="checkbox" name="eventsformats[]"
                                               data-value="<?= $eventsformat->name; ?>"
                                               value="<?= $eventsformat->id; ?>"/>
                                        <label><?= $eventsformat->name; ?></label>
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

                    <div class="filter-nav__sub filter_search_container" data-filter="filter-city">
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
                                <div class="city-filter-sidebar city-filter-sidebar-mobile checkboxes_js mScrollbarCustom simplebar">
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

                </nav>
            </form>

            <form method="get"
                  class="desktop_filters_form columns_box columns_box-filters filters-desktop-form all-events-form">
                <main class="main_col">
                    <?php
                    // выключатель ДПО
                    if (false) { // Yii::$app->params['enable_dpo']) {
                        $page_events = \app\modules\pages\models\Eventspage::find()->where(['model' => 'app\modules\pages\models\Eventspage', 'visible' => 1])->one();
                        // временно отключен по просьбе Нино
                        $page_eduprog = null;
                        // $page_eduprog = \app\modules\pages\models\EduprogPage::find()->where(['model' => 'app\modules\pages\models\EduprogPage', 'visible' => 1])->one();
                        ?>
                        <div class="lenta-menu owl-carousel owl-theme">
                            <?php if (!empty($page_events)) { ?><a href="<?= $page_events->getUrlPath(); ?>"
                                                                   class="active"><?= $page_events->getNameForView(); ?></a> <?php } ?>
                            <?php if (!empty($page_eduprog)) { ?><a
                                href="<?= $page_eduprog->getUrlPath(); ?>"><?= $page_eduprog->getNameForView(); ?></a> <?php } ?>
                        </div>
                    <?php } ?>

                    <div class="filters filters-keywords">
                        <div class="filters-list-selected"></div>
                    </div>

                    <div class="all-events-list">
                        <div id="all-events-cards" class="all-events-cards">
                            <?= $this->render('_event_box', ['items' => $items, 'promo_items_ids' => $promo_items_ids]); ?>
                        </div>
                    </div>

                    <div id="pager_content">
                        <?= \app\widgets\pagination\LinkPager::widget(['pages' => $pages, 'container' => '#all-events-cards']); ?>
                    </div>

                </main>

                <aside class="sidebar_col">
                    <div class="search_flex">
                        <div class="ip_cell search-wrapper">
                            <input type="text" name="q" value="<?= $search_text; ?>" class="input_text ip_search"
                                   placeholder="Введите название ">
                            <button class="button-o button-search" type="submit">Применить</button>
                        </div>
                    </div>

                    <div class="sidebar_box">
                        <h3>Даты проведения</h3>
                        <?php /*
                    <div class="checkboxes_js mScrollbarCustom simplebar">
                        <?php foreach ([['name'=>'Сегодня','id'=>1,'value'=>'today'],['name'=>'Завтра','id'=>2,'value'=>'tomorrow'],['name'=>'На этих выходных','id'=>3,'value'=>'weekend']] as $event_xdate) { ?>
                            <div class="ip_cell">
                                <input id="date_event_<?=$event_xdate['id']?>" type="radio" <?=(($event_xdate['value']==$terms['event_xdate']))?'checked="checked"':''?> name="event_xdate" class="ch filter_jmaka radio-toggle" value="<?=$event_xdate['value']?>" data-value="<?=$event_xdate['name']?>"/>
                                <label><?=$event_xdate['name']?></label>
                            </div>
                        <?php } ?>
                    </div>
                    */ ?>
                        <div class="flex dateRange two-inputs">
                            <div class="ip_cell lined">
                                <input type="text" name="date_from"
                                       value="<?= isset($terms['date']) && isset($terms['date']['from']) ? $terms['date']['from'] : ''; ?>"
                                       class="input_text range-filter_input date-range1 uncheckbox"
                                       placeholder="Дата от" size="20"/>
                                <button type="button" class="clear-input-btn"></button>
                            </div>
                            <div class="ip_cell lined">
                                <input type="text" name="date_to"
                                       value="<?= isset($terms['date']) && isset($terms['date']['to']) ? $terms['date']['to'] : ''; ?>"
                                       class="input_text range-filter_input date-range2 uncheckbox"
                                       placeholder="Дата до" size="20"/>
                                <button type="button" class="clear-input-btn"></button>
                            </div>
                        </div>
                    </div>

                    <div class="sidebar_box">
                        <div class="checkboxes_js mScrollbarCustom simplebar">
                            <div class="ip_cell">
                                <input id="js-reg-open" type="checkbox"
                                       class="ch normal js-open-reg" <?= ($terms['registration_open'] == 1) ? 'checked="checked"' : ''; ?> />
                                <label for="js-reg-open">Открыта регистрация</label>
                                <input type="hidden" name="registration_open"
                                       value="<?= ($terms['registration_open'] == 1) ? '1' : '0'; ?>"/>
                            </div>
                            <div class="ip_cell">
                                <input type="checkbox"
                                       class="ch filter_jmaka" <?= ($terms['is_ast'] == 1) ? 'checked="checked"' : ''; ?>
                                       name="is_ast" value="1"/>
                                <label>Мероприятия Академии</label>
                            </div>
                        </div>
                    </div>

                    <div class="sidebar_box">
                        <h3>Кафедра</h3>
                        <div class="checkboxes_js mScrollbarCustom simplebar">
                            <?php foreach ($directions as $i => $direction) { ?>
                                <?php if ($i == 5) {
                                    echo '<div class="filters_buttons"><a href="#" class="button-o small button-o-grey button-more-filters">Показать еще</a></div>';
                                } ?>
                                <div class="ip_cell" <?php if ($i >= 5) {
                                    echo 'style="display:none;"';
                                } ?>>
                                    <input type="checkbox"
                                           class="ch filter_jmaka" <?= (in_array($direction->id, $terms['directs'])) ? 'checked="checked"' : ''; ?>
                                           name="directs[]" value="<?= $direction->id; ?>"/>
                                    <label><?= $direction->name; ?></label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="sidebar_box">
                        <h3>Стоимость, ₽</h3>
                        <div class="checkboxes_js mScrollbarCustom simplebar">
                            <?php foreach ([['name' => 'Бесплатные', 'id' => 1, 'value' => 'free'], ['name' => 'до 1 000 ₽', 'id' => 2, 'value' => '1000'], ['name' => 'до 5 000 ₽', 'id' => 3, 'value' => '5000'], ['name' => '5 000+ ₽', 'id' => 4, 'value' => '5000+']] as $price) { ?>
                                <div class="ip_cell">
                                    <input id="price_event_<?= $price['id']; ?>"
                                           type="radio" <?= ($price['value'] == $terms['price']) ? 'checked="checked"' : ''; ?>
                                           name="price" class="ch normal radio-toggle" value="<?= $price['value']; ?>"
                                           data-value="<?= $price['name']; ?>"/>
                                    <label for="price_event_<?= $price['id']; ?>"><?= $price['name']; ?></label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="sidebar_box">
                        <h3>Формат</h3>
                        <div class="checkboxes_js mScrollbarCustom simplebar">
                            <?php foreach ([['name' => 'Онлайн', 'id' => 1, 'value' => Events::TYPE_ONLINE], ['name' => 'Офлайн', 'id' => 0, 'value' => Events::TYPE_OFFLINE], ['name' => 'Онлайн и Офлайн', 'id' => 2, 'value' => Events::TYPE_HYBRID]] as $event_format) { ?>
                                <div class="ip_cell">
                                    <input id="m_event_<?= $event_format['id']; ?>"
                                           type="checkbox" <?= (in_array($event_format['id'], $terms['event_format'])) ? 'checked="checked"' : ''; ?>
                                           name="event_format[]" class="ch filter_jmaka"
                                           value="<?= $event_format['value']; ?>"
                                           data-value="<?= $event_format['name']; ?>"/>
                                    <label><?= $event_format['name']; ?></label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="sidebar_box">
                        <h3>Вид мероприятия</h3>
                        <div class="checkboxes_js mScrollbarCustom simplebar">
                            <?php foreach ($eventsformats as $i => $eventsformat) { ?>
                                <?php if ($i == 5) {
                                    echo '<div class="filters_buttons"><a href="#" class="button-o small button-o-grey button-more-filters">Показать еще</a></div>';
                                } ?>
                                <div class="ip_cell" <?php if ($i >= 5) {
                                    echo 'style="display:none;"';
                                } ?>>
                                    <input type="checkbox"
                                           class="ch filter_jmaka" <?= (in_array($eventsformat->id, $terms['eventsformats'])) ? 'checked="checked"' : ''; ?>
                                           name="eventsformats[]" value="<?= $eventsformat->id; ?>"/>
                                    <label><?= $eventsformat->name; ?></label>
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
                        <div class="city-filter-sidebar city-filter-sidebar-desktop checkboxes_js mScrollbarCustom simplebar">
                            <?php foreach ($cities_short as $city) { ?>
                                <div class="ip_cell <?= (in_array($city->id, $terms['city'])) ? 'ip_cell-choise' : ''; ?>">
                                    <input class="ch filter_jmaka"
                                           type="checkbox" <?= (in_array($city->id, $terms['city'])) ? 'checked="checked"' : ''; ?>
                                           name="city[]" value="<?= $city->id; ?>"/>
                                    <label><?= $city->name; ?></label>
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

$this->registerJsVar('filter_suffix', '-events', $position = yii\web\View::POS_HEAD);

$url = $model->getUrlPath();

$js = <<<JS
	if(window.innerWidth > 1100) {
        $('.filters-desktop-form .two-inputs').dateRangePicker({
            singleMonth: false,
            startOfWeek: 'monday',
            format: 'DD.MM.YYYY',
            language: 'ru',
            separator : ' по ',
            autoClose: true,
			showShortcuts: true,
			shortcuts : null,
			customShortcuts:
			[
				{
					name: 'Сегодня',
					dates : function()
					{
						var start = moment().toDate();
						var end = moment().toDate();
						return [start,end];
					}
				},
				{
					name: 'Завтра',
					dates : function()
					{
						var start = moment().add(1,'days').toDate();
						var end = moment().add(1,'days').toDate();
						return [start,end];
					}
				},
				{
					name: 'В выходные',
					dates : function()
					{
						var start = moment().day(6).toDate();
						var end = moment().day(7).toDate();
						return [start,end];
					}
				},
			],
            getValue: function()
            {
                if ($(this).find('.date-range1').val() && $(this).find('.date-range2').val() )
                    return $(this).find('.date-range1').val() + ' to ' + $(this).find('.date-range2').val();
                else
                    return '';
            },
            setValue: function(s,s1,s2)
            {
                $(this).find('.date-range1').val(s1);
                $(this).find('.date-range2').val(s2);
				$(this).closest('.sidebar_box').find('.ch').prop('checked', false);
				$(this).closest('form').submit();
            }
        });
    } else {
        $('.filter-mob-form .two-inputs').dateRangePicker({
            singleMonth: false,
            startOfWeek: 'monday',
            format: 'DD.MM.YYYY',
            language: 'ru',
            separator : ' по ',
            autoClose: true,
			showShortcuts: true,
			shortcuts : null,
			customShortcuts:
			[
				{
					name: 'Сегодня',
					dates : function()
					{
						var start = moment().toDate();
						var end = moment().toDate();
						return [start,end];
					}
				},
				{
					name: 'Завтра',
					dates : function()
					{
						var start = moment().add(1,'days').toDate();
						var end = moment().add(1,'days').toDate();
						return [start,end];
					}
				},
				{
					name: 'В выходные',
					dates : function()
					{
						var start = moment().day(6).toDate();
						var end = moment().day(7).toDate();
						return [start,end];
					}
				},
			],
            getValue: function()
            {
                if ($(this).find('.date-range1').val() && $(this).find('.date-range2').val() )
                    return $(this).find('.date-range1').val() + ' to ' + $(this).find('.date-range2').val();
                else
                    return '';
            },
            setValue: function(s,s1,s2)
            {
                $(this).find('.date-range1').val(s1);
                $(this).find('.date-range2').val(s2);
				$(this).closest('form').submit();
            }
        });
    }

	$('body').on('change','.range-filter_input', function(e) {
		if(($(this).parents('.dateRange').find('.date-range1').val() != '' && $(this).parents('.dateRange').find('.date-range2').val() != '')) {
			$(this).closest('form').submit();
			if(window.innerWidth <= 1100) {
				$('.filter-nav__show-inner').addClass('filter-nav__show-inner--show');
			}
		}
	});

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
	})

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

	$('body').on('submit','.desktop_filters_form', function(e) {
		let new_url = $(this).serialize();
		$.ajax({
			type: 'GET',
			url: '{$url}?'+new_url,
			processData: true,
			dataType: 'json',
			success: function(data) {
				if (data.status == 'success') {
					// заменить содержимое all-events-cards
					$('#all-events-cards').html(data.html);
					$('#pager_content').html(data.pager);
				} else {
					// сообщение об ошибке на страницу
					$('#all-events-cards').html('<h3 class="pb20"><i>К сожалению, по вашему запросу ничего не найдено</i></h3>');
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
					// заменить содержимое all-events-cards
					$('#all-events-cards').html(data.html);
					// написать кол-во результатов
					// $('#mobile_count').html(data.count);
					// показать плашку с результатами
					$('.filter-nav__show-inner').addClass('filter-nav__show-inner--show');
					$('.filter-nav__main').addClass('filter-nav__main--checked');
					$('#pager_content').html(data.pager);
				} else {
					// сообщение об ошибке на страницу
					$('#all-events-cards').html('<h3 class="pb20"><i>К сожалению, по вашему запросу ничего не найдено</i></h3>');
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