<?php

use yii\helpers\ArrayHelper;

?>
    <main class="sec content_sec">
        <div class="container wide">
            <h1 class="services-block-title"><a href="<?= $model->getUrlPath(); ?>"
                                                class="services-back-button"></a><?= $model->name; ?></h1>
            <div class="subheader">Эксперты, которые работают с этим видом услуги</div>

            <form action="" method="post" class="filter-mob-form mobile_filters_form services-filters">

                <div class="mob_search_box">
                    <div class="mob-filter-buttons">
                        <button type="button" class="mob-filter-btn">Все фильтры</button>
                        <button type="button" class="mob-filter-clear-all  clear-filter-all_js">Сбросить</button>
                    </div>

                    <div class="custom_dropdown_box mob-sort-box">
                        <span class="clear-btn"></span>
                        <a href="#" class="custom_dropdown-link"
                           data-placeholder="Сортировать по названию">Сортировка</a>
                        <div class="custom_dropdown-list checkboxes_js mScrollbarCustom simplebar">
                            <div class="ip_cell custom_dropdown-choice">
                                <input type="radio" class="rd allChecks">
                                <label>Сортировать по умолчанию</label>
                            </div>
                            <div class="ip_cell custom_dropdown-choice">
                                <input type="radio" name="sort" class="rd filter_jmaka" value="1">
                                <label>Сортировать по названию &#8593;</label>
                            </div>
                            <div class="ip_cell custom_dropdown-choice">
                                <input type="radio" name="sort" class="rd filter_jmaka" value="0">
                                <label>Сортировать по названию &#8595;</label>
                            </div>
                        </div>
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
                            <li class="filter-nav__item" data-filter="filter-01">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link filter-nav__link-ca"
                                       href="#"><span>Целевые аудитории</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li>
                            <li class="filter-nav__item" data-filter="filter-1">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link filter-nav__link-tasks"
                                       href="#"><span>Решаемая задача</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li>
                            <li class="filter-nav__item" data-filter="filter-2">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link filter-nav__link-competences" href="#"><span>Специализация</span><i></i></a>
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

                    <div class="filter-nav__sub filter_search_container" data-filter="filter-01">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Целевые аудитории</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                            <div class="filter-nav__search">
                                <input type="search" class="input_text ip_search ip_search2 filter_search_input"
                                       placeholder="Введите название"/>
                                <button class="ip_search2-button filter_search_input-button"></button>
                            </div>
                        </div>
                        <div class="filter-nav__sub-container filter-nav__sub-container-ajax">
                            <div class="filter-nav__sub-container-inner">
                                <div class="custom_dropdown_title not-found-title" style="display: none;">Ничего не
                                    найдено
                                </div>
                                <div class="filter-nav__sub-container filter_search_list">
                                    <div class="alphabetic_list alphabetic_ca_list-mobile"></div>
                                </div>
                            </div>
                            <div class="filter-nav__show-inner">
                                <button class="button long filter-nav__show-btn filter-mob-nav__show-btn" type="button">
                                    Применить
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="filter-nav__sub filter_search_container" data-filter="filter-1">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Решаемая задача</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                            <div class="filter-nav__search">
                                <input type="search" class="input_text ip_search ip_search2 filter_search_input"
                                       placeholder="Введите название задачи"/>
                                <button class="ip_search2-button filter_search_input-button"></button>
                            </div>
                        </div>
                        <div class="filter-nav__sub-container filter-nav__sub-container-ajax">
                            <div class="filter-nav__sub-container-inner">
                                <div class="custom_dropdown_title not-found-title" style="display: none;">Ничего не
                                    найдено
                                </div>
                                <div class="filter-nav__sub-container filter_search_list">
                                    <div class="alphabetic_list alphabetic_task_list-mobile"></div>
                                </div>
                            </div>
                            <div class="filter-nav__show-inner">
                                <button class="button long filter-nav__show-btn filter-mob-nav__show-btn" type="button">
                                    Применить
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="filter-nav__sub filter_search_container" data-filter="filter-2">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Специализация</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                            <div action="" method="post" class="filter-nav__search">
                                <input type="search" class="input_text ip_search ip_search2 filter_search_input"
                                       placeholder="Введите название специализации"/>
                                <button class="ip_search2-button filter_search_input-button"></button>
                            </div>
                        </div>
                        <div class="filter-nav__sub-container filter-nav__sub-container-ajax">
                            <div class="filter-nav__sub-container-inner">
                                <div class="custom_dropdown_title not-found-title" style="display: none;">Ничего не
                                    найдено
                                </div>
                                <div class="filter-nav__sub-container filter_search_list">
                                    <div class="alphabetic_list alphabetic_competence_list-mobile"></div>
                                </div>
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
                            <div class="filter-nav__sub-container-inner">
                                <div class="custom_dropdown-row">
                                    <input type="checkbox" class="ch custom_dropdown-choice mob-all-checks">
                                    <label>Все</label>
                                </div>
                                <?php foreach ([['name' => 'Гибридное', 'id' => 2, 'value' => 'hybrid'], ['name' => 'Онлайн', 'id' => 1, 'value' => 'online'], ['name' => 'Офлайн', 'id' => 0, 'value' => 'offline']] as $kinds) { ?>
                                    <div class="custom_dropdown-row">
                                        <input id="m_event_<?= $kinds['id']; ?>"
                                               type="checkbox" <?= (in_array($kinds['id'], $terms['kinds'])) ? 'checked="checked"' : ''; ?>
                                               name="kinds[]" class="ch custom_dropdown-choice"
                                               value="<?= $kinds['value']; ?>" data-value="<?= $kinds['name']; ?>"/>
                                        <label><?= $kinds['name']; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="filter-nav__show-inner">
                                <button class="button long filter-nav__show-btn" type="button">Применить</button>
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
                        <div class="filter-nav__sub-container filter_search_list">
                            <div class="filter-nav__sub-container-inner">
                                <div class="city-filter-sidebar city-filter-sidebar-mobile checkboxes_js">
                                    <?php foreach ($cities_pop as $city) { ?>
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
                                <button class="button long filter-nav__show-btn" type="button">Применить</button>
                            </div>
                        </div>
                    </div>
                    <!-- </form> -->
                </nav>
            </form>

            <form class="filters-desktop-form desktop_filters_form services-filters directions_search_box desktop-visible">
                <div class="search-sort-flex-wrapper">
                    <div class="search_flex-wrapper">
                        <div class="search_flex">
                            <div class="custom_dropdown_box filters">
                                <div class="custom_dropdown_box">
                                    <span class="clear-btn"></span>
                                    <a href="#" class="custom_dropdown-link custom_dropdown-link-ca"
                                       data-placeholder="Целевые аудитории"></a>
                                    <div class="custom_dropdown-list filter_search_container">
                                        <a href="#!" class="close-custom_dropdown-list"></a>
                                        <div class="custom_dropdown-top">
                                            <div class="custom_dropdown-search">
                                                <input type="text"
                                                       class="input_text ip_search ip_search2 filter_search_input"
                                                       placeholder="Введите название "/>
                                                <button class="ip_search2-button filter_search_input-button"></button>
                                            </div>
                                            <div class="custom_dropdown-selected">
                                                <div class="custom_dropdown-selected-title">Выбрано:</div>
                                                <div class="custom_dropdown-selected-content mScrollbarCustom simplebar"></div>
                                                <button class="button small shadow custom_dropdown-selected-button">
                                                    Применить
                                                </button>
                                            </div>
                                        </div>
                                        <div class="custom_dropdown-list-inner checkboxes_js">
                                            <div class="custom_dropdown_item">
                                                <div class="custom_dropdown_title">По алфавиту</div>
                                                <div class="custom_dropdown_title not-found-title"
                                                     style="display: none;">Ничего не найдено
                                                </div>
                                                <div class="alphabetic_list alphabetic_ca_list-desktop">
                                                    <div class="circularG-wrapper">
                                                        <div class="circularG circularG_1"></div>
                                                        <div class="circularG circularG_2"></div>
                                                        <div class="circularG circularG_3"></div>
                                                        <div class="circularG circularG_4"></div>
                                                        <div class="circularG circularG_5"></div>
                                                        <div class="circularG circularG_6"></div>
                                                        <div class="circularG circularG_7"></div>
                                                        <div class="circularG circularG_8"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="custom_dropdown_box filters">
                                <div class="custom_dropdown_box">
                                    <span class="clear-btn"></span>
                                    <a href="#" class="custom_dropdown-link custom_dropdown-link-tasks"
                                       data-placeholder="Решаемые задачи"></a>
                                    <div class="custom_dropdown-list filter_search_container">
                                        <a href="#!" class="close-custom_dropdown-list"></a>
                                        <div class="custom_dropdown-top">
                                            <div class="custom_dropdown-search">
                                                <input type="text"
                                                       class="input_text ip_search ip_search2 filter_search_input"
                                                       placeholder="Введите название "/>
                                                <button class="ip_search2-button filter_search_input-button"></button>
                                            </div>
                                            <div class="custom_dropdown-selected">
                                                <div class="custom_dropdown-selected-title">Выбрано:</div>
                                                <div class="custom_dropdown-selected-content mScrollbarCustom simplebar"></div>
                                                <button class="button small shadow custom_dropdown-selected-button">
                                                    Применить
                                                </button>
                                            </div>
                                        </div>
                                        <div class="custom_dropdown-list-inner checkboxes_js">
                                            <div class="custom_dropdown_item">
                                                <div class="custom_dropdown_title">По алфавиту</div>
                                                <div class="custom_dropdown_title not-found-title"
                                                     style="display: none;">Ничего не найдено
                                                </div>
                                                <div class="alphabetic_list alphabetic_task_list-desktop">
                                                    <div class="circularG-wrapper">
                                                        <div class="circularG circularG_1"></div>
                                                        <div class="circularG circularG_2"></div>
                                                        <div class="circularG circularG_3"></div>
                                                        <div class="circularG circularG_4"></div>
                                                        <div class="circularG circularG_5"></div>
                                                        <div class="circularG circularG_6"></div>
                                                        <div class="circularG circularG_7"></div>
                                                        <div class="circularG circularG_8"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="custom_dropdown_box filters">
                                <div class="custom_dropdown_box">
                                    <span class="clear-btn"></span>
                                    <a href="#" class="custom_dropdown-link custom_dropdown-link-competences"
                                       data-placeholder="Специализации"></a>
                                    <div class="custom_dropdown-list filter_search_container">
                                        <a href="#!" class="close-custom_dropdown-list"></a>
                                        <div class="custom_dropdown-top">
                                            <div class="custom_dropdown-search">
                                                <input type="text"
                                                       class="input_text ip_search ip_search2 filter_search_input"
                                                       placeholder="Введите название "/>
                                                <button class="ip_search2-button filter_search_input-button"></button>
                                            </div>
                                            <div class="custom_dropdown-selected">
                                                <div class="custom_dropdown-selected-title">Выбрано:</div>
                                                <div class="custom_dropdown-selected-content  mScrollbarCustom simplebar"></div>
                                                <button class="button small shadow custom_dropdown-selected-button">
                                                    Применить
                                                </button>
                                            </div>
                                        </div>
                                        <div class="custom_dropdown-list-inner checkboxes_js">
                                            <div class="custom_dropdown_item">
                                                <div class="custom_dropdown_title">По алфавиту</div>
                                                <div class="custom_dropdown_title not-found-title"
                                                     style="display: none;">Ничего не найдено
                                                </div>
                                                <div class="alphabetic_list alphabetic_competence_list-desktop">
                                                    <div class="circularG-wrapper">
                                                        <div class="circularG circularG_1"></div>
                                                        <div class="circularG circularG_2"></div>
                                                        <div class="circularG circularG_3"></div>
                                                        <div class="circularG circularG_4"></div>
                                                        <div class="circularG circularG_5"></div>
                                                        <div class="circularG circularG_6"></div>
                                                        <div class="circularG circularG_7"></div>
                                                        <div class="circularG circularG_8"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="search_flex">
                            <div class="custom_dropdown_box">
                                <span class="clear-btn"></span>
                                <a href="#" class="custom_dropdown-link" data-placeholder="Формат"></a>
                                <div class="custom_dropdown-list checkboxes_js mScrollbarCustom simplebar">
                                    <div class="ip_cell custom_dropdown-choice">
                                        <input type="checkbox" name="" class="filter_jmaka rd allChecks" value>
                                        <label>Все</label>
                                    </div>
                                    <?php foreach ([['name' => 'Гибридная', 'id' => 2, 'value' => 'hybrid'], ['name' => 'Онлайн', 'id' => 1, 'value' => 'online'], ['name' => 'Офлайн', 'id' => 0, 'value' => 'offline']] as $kinds) { ?>
                                        <div class="ip_cell">
                                            <input type="checkbox" <?= (in_array($kinds['id'], $terms['kinds'])) ? 'checked="checked"' : ''; ?>
                                                   name="kinds[]" class="filter_jmaka rd"
                                                   value="<?= $kinds['value']; ?>"/>
                                            <label><?= $kinds['name']; ?></label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="custom_dropdown_box filters">
                                <div class="custom_dropdown_box">
                                    <span class="clear-btn"></span>
                                    <a href="#" class="custom_dropdown-link custom_dropdown-link-cities"
                                       data-placeholder="Город"></a>
                                    <div class="custom_dropdown-list filter_search_container">
                                        <div class="custom_dropdown-top">
                                            <a href="#!" class="close-custom_dropdown-list"></a>
                                            <div class="custom_dropdown-search">
                                                <input type="text"
                                                       class="input_text ip_search ip_search2 filter_search_input"
                                                       placeholder="Введите название "/>
                                                <button class="ip_search2-button filter_search_input-button"></button>
                                            </div>
                                            <div class="custom_dropdown-selected">
                                                <div class="custom_dropdown-selected-title">Выбрано:</div>
                                                <div class="custom_dropdown-selected-content mScrollbarCustom simplebar"></div>
                                                <button class="button small shadow custom_dropdown-selected-button">
                                                    Применить
                                                </button>
                                            </div>
                                        </div>
                                        <div class="custom_dropdown-list-inner checkboxes_js">
                                            <?php if (!empty($cities_pop)) { ?>
                                                <div class="custom_dropdown_item filter_search_popular">
                                                    <div class="custom_dropdown_title">Популярные</div>
                                                    <div class="popular_list filter_search_list">
                                                        <?php foreach ($cities_pop as $city) { ?>
                                                            <div class="custom_dropdown-row">
                                                                <input class="ch custom_dropdown-choice input-popular" <?= (in_array($city->id, $terms['city'])) ? 'checked="checked"' : ''; ?>
                                                                       data-id="city_<?= $city->id; ?>"
                                                                       data-value="<?= $city->name; ?>" type="checkbox"
                                                                       id="pop_city_<?= $city->id; ?>"
                                                                       value="<?= $city->id; ?>"/>
                                                                <label><?= $city->name; ?></label>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div class="custom_dropdown_item">
                                                <div class="custom_dropdown_title">По алфавиту</div>
                                                <div class="alphabetic_list alphabetic_city_list-desktop">
                                                    <div class="circularG-wrapper">
                                                        <div class="circularG circularG_1"></div>
                                                        <div class="circularG circularG_2"></div>
                                                        <div class="circularG circularG_3"></div>
                                                        <div class="circularG circularG_4"></div>
                                                        <div class="circularG circularG_5"></div>
                                                        <div class="circularG circularG_6"></div>
                                                        <div class="circularG circularG_7"></div>
                                                        <div class="circularG circularG_8"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="custom_dropdown_box">
                                <a href="#" class="custom_dropdown-link" data-placeholder="Стоимость"></a>
                                <div class="custom_dropdown-list">
                                    <h3>Стоимость услуги</h3>
                                    <div class="range-filter  range-filter-price_js">
                                        <div class="range-filter_item">
                                            <input type="number" placeholder="от 0"
                                                   class="range-filter_input range-filter_input-from" name="price_from"
                                                   value="<?= isset($terms['price']) && isset($terms['price']['from']) ? $terms['price']['from'] : ''; ?>">
                                            <button type="button" class="clear-input-btn"></button>
                                        </div>
                                        <div class="range-filter_item">
                                            <input type="number" placeholder="до 100 000"
                                                   class="range-filter_input range-filter_input-to" name="price_to"
                                                   value="<?= isset($terms['price']) && isset($terms['price']['to']) ? $terms['price']['to'] : ''; ?>">
                                            <button type="button" class="clear-input-btn"></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="sort-flex-wrapper">
                        <div class="custom_dropdown_box">
                            <span class="clear-btn"></span>
                            <a href="#" class="custom_dropdown-link" data-placeholder="Сортировать по названию">Сортировка</a>
                            <div class="custom_dropdown-list checkboxes_js mScrollbarCustom simplebar">
                                <div class="ip_cell custom_dropdown-choice">
                                    <input type="radio" class="rd allChecks">
                                    <label>Сортировать по умолчанию</label>
                                </div>
                                <div class="ip_cell custom_dropdown-choice">
                                    <input type="radio" name="sort" class="rd filter_jmaka" value="1">
                                    <label>Сортировать по названию &#8593;</label>
                                </div>
                                <div class="ip_cell custom_dropdown-choice">
                                    <input type="radio" name="sort" class="rd filter_jmaka" value="0">
                                    <label>Сортировать по названию &#8595;</label>
                                </div>
                            </div>
                        </div>
                        <a href="#" class="event_reset-btn button lightGray filters_reset disabled">Сбросить все</a>
                    </div>
                </div>
                <div class="filters">
                    <div class="filters-list-selected"></div>
                </div>
            </form>

            <div id="services_expert_items" class="services-expert-items">
                <?php if (!empty($items)) { ?>
                    <?= $this->render('_service_types_box', ['items' => $items]); ?>
                <?php } ?>
            </div>
        </div>

        <div class="sec services-seo-block">
            <div class="container wide">
                <?= $model->content; ?>
            </div>
        </div>

        <?php if (!empty($audience_list)) { ?>
            <div class="sec services-category-list-block">
                <div class="container wide">
                    <h2>Категории</h2>
                    <div class="services-category-list">
                        <?php foreach ($audience_list as $el) { ?>
                            <?php if ($el->url) { ?>
                                <a href="<?= $el->getUrlPath(); ?>" class="services-category "><?= $el->name; ?></a>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php if (!empty($target_audience_catalog->getPopular())) { ?>
            <div class="sec services-kind-block services-slider-block">
                <div class="container wide">
                    <h2>Популярные виды услуг</h2>
                </div>
                <div class="container services-slider-container">
                    <div class="services-square-slider owl-carousel owl-theme">
                        <?php foreach ($target_audience_catalog->getPopular() as $service_type) { ?>
                            <a href="<?= $service_type->getUrlPath(); ?>" class="services-square-slide">
                                <div class="services-square-slide-bg visible-over650"
                                     style="background: url() center no-repeat #F8F8F8; background-size: cover;"></div>
                                <div class="services-square-slide-bg visible-less650"
                                     style="background: url() center no-repeat #F8F8F8; background-size: cover;"></div>
                                <div class="services-square-slide-name"><?= $service_type->name; ?></div>
                            </a>
                        <?php } ?>
                        <!-- <div class="services-square-slide-fake"></div>
                        <div class="services-square-slide-fake"></div> -->
                    </div>
                </div>
            </div>
        <?php } ?>
    </main>

<?php
$this->registerJsFile('/js/filters.js', ['depends' => [app\assets\AppAsset::class]]);

$cities_list = ArrayHelper::map($cities_short, 'id', 'id');
$this->registerJsVar('excluded', $cities_list, $position = yii\web\View::POS_HEAD);

$target_audience_list = ArrayHelper::map($selected_target_audience, 'id', 'id');
$this->registerJsVar('excluded_target_audience', $target_audience_list, $position = yii\web\View::POS_HEAD);

$tasks_list = ArrayHelper::map($selected_tasks, 'id', 'id');
$this->registerJsVar('excluded_task', $tasks_list, $position = yii\web\View::POS_HEAD);

$competence_list = ArrayHelper::map($selected_competences, 'id', 'id');
$this->registerJsVar('excluded_competence', $competence_list, $position = yii\web\View::POS_HEAD);

$this->registerJsVar('filter_suffix', '-servicetypes', $position = yii\web\View::POS_HEAD);
$this->registerJsVar('filter_roles', $model->id, $position = yii\web\View::POS_HEAD);

$url = $model->getUrlPath();
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

	$('body').on('change','.range-filter_input', function(e) {
		if(($('.filters-desktop-form .range-filter_input-from').val() != '' && $('.filters-desktop-form .range-filter_input-to').val() != '')) {
			$(this).closest('form').submit();
		}
	});

	function cityFilterMob() {
		let click = 1;
		$('.filter-nav__link-city').click(function(){
			if(click == 1) {
				$.ajax({
					type: 'GET',
					url: '/filter/cities'+filter_suffix+'/?roles='+filter_roles,
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
	if(window.innerWidth < 1100) {
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
				} else {
					// сообщение об ошибке на страницу
					$('#services_expert_items').html('<h3 class="pb20"><i>К сожалению, по вашему запросу ничего не найдено</i></h3>');
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
				}
				history.pushState(null, null, '{$url}?'+new_url);
		    }
		});
		return false;
	});
JS;
$this->registerJs($js);
?>