<?php

use yii\helpers\ArrayHelper;

?>
    <main class="sec content_sec sec-bg-pic sec-bg-pic--narrow">
        <?php if (!empty($model->back_image)) { ?>
            <div class="pic-bg">
                <img src="<?= $model->getThumb('back_image', 'main'); ?>"
                     alt="<?= str_replace('"', '&quot;', $model->name); ?>">
            </div>
        <?php } ?>
        <div class="container wide">
            <h1><?= $model->getNameForView(); ?></h1>
            <?php if (!empty($model->content)) { ?>
                <div class="subheader"><?= $model->content; ?></div>
            <?php } ?>
        </div>
    </main>

    <main class="sec content_sec  filters-page">
        <div class="container wide">
            <form action="<?= $academic_page->getUrlPath(); ?>" method="get"
                  class="mobile_filters_form filter-mob-form">
                <div class="mob_search_box">
                    <div class="mob-filter-buttons">
                        <button type="button" class="mob-filter-btn">Все фильтры</button>
                        <button type="button" class="mob-filter-clear-all clear-filter-all_js">Сбросить</button>
                    </div>
                </div>
                <div class="nav-overlay nav-overlay--open"></div>
                <nav class="filter-nav filter-nav--open">
                    <div class="filter-nav__main">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__close js-filter-nav__show-btn"
                                     onclick="javascript:history.back(); return false;"></div>
                                <div class="filter-nav__title">Подбор эксперта</div>
                                <button class="mob-filter-clear  clear-filter-all_js" type="button">Сбросить всё
                                </button>
                            </div>
                        </div>
                        <ul class="filter-nav__list mScrollbarCustom simplebar">
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
                            <li class="filter-nav__item" data-filter="filter-3">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link filter-nav__link-servicegroup"
                                       href="#"><span>Тип услуг</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li>
                            <li class="filter-nav__item" data-filter="filter-4">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Кафедра оказания услуг</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li>
                            <li class="filter-nav__item" data-filter="filter-5">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Формат</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li>
                            <li class="filter-nav__item  filter-nav__item--range" data-filter="filter-6">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Стоимость</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-range_js">Сбросить</a>
                                </div>
                                <div class="range-filter  range-filter-price_js">
                                    <div class="range-filter_item">
                                        <input type="number" name="price_from" placeholder="от 0" data-min="0"
                                               class="range-filter_input range-filter_input-from" value="">
                                        <button type="button" class="clear-input-btn" type="button"></button>
                                    </div>
                                    <div class="range-filter_item">
                                        <input type="number" name="price_to" placeholder="до 100 000" data-min="1000000"
                                               class="range-filter_input range-filter_input-to" value="">
                                        <button type="button" class="clear-input-btn" type="button"></button>
                                    </div>
                                </div>
                            </li>
                            <li class="filter-nav__item" data-filter="filter-7">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link filter-nav__link-city" href="#"><span>Город</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li>
                            <!-- <li class="filter-nav__item" data-filter="filter-8">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Пол</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-item_js">Сбросить</a>
                                </div>
                                <div class="filter-nav__selected-list"></div>
                            </li> -->
                            <!-- <li class="filter-nav__item  filter-nav__item--range" data-filter="filter-9">
                                <div class="filter-nav__item-top">
                                    <a class="filter-nav__link" href="#"><span>Возраст</span><i></i></a>
                                    <a href="#" class="filter-nav__clear  clear-filter-range_js">Сбросить</a>
                                </div>
                                <div class="range-filter  range-filter-age_js">
                                    <div class="range-filter_item">
                                        <input type="number" name="age_from" placeholder="от 18" data-min="18" class="range-filter_input range-filter_input-from" value="">
                                        <button class="clear-input-btn" type="button"></button>
                                    </div>
                                    <div class="range-filter_item">
                                        <input type="number" name="age_to" placeholder="до 100" data-min="150" class="range-filter_input range-filter_input-to" value="">
                                        <button class="clear-input-btn" type="button"></button>
                                    </div>
                                </div>
                            </li> -->
                        </ul>
                        <div class="filter-nav__show-inner">
                            <button class="button long filter-nav__show-btn js-filter-nav__show-btn" type="submit"><span
                                        id="mobile_count">Показать</span></button>
                        </div>
                    </div>
                    <div class="filter-nav__sub filter_search_container tasks" data-filter="filter-1">
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
                                <div class="filter-nav__tabs-container filter_search_list">
                                    <div class="alphabetic_list alphabetic_task_list-mobile"></div>
                                </div>
                            </div>
                            <div class="filter-nav__show-inner">
                                <button class="button long filter-nav__show-btn" type="button">Применить</button>
                            </div>
                        </div>
                    </div>
                    <div class="filter-nav__sub filter_search_container competence" data-filter="filter-2">
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
                                <div class="filter-nav__tabs-container filter_search_list">
                                    <div class="alphabetic_list alphabetic_competence_list-mobile"></div>
                                </div>
                            </div>
                            <div class="filter-nav__show-inner">
                                <button class="button long filter-nav__show-btn" type="button">Применить</button>
                            </div>
                        </div>
                    </div>
                    <div class="filter-nav__sub filter_search_container servicegroup" data-filter="filter-3">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Тип услуг</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                            <div action="" method="post" class="filter-nav__search">
                                <input type="search" class="input_text ip_search ip_search2 filter_search_input"
                                       placeholder="Введите название типа"/>
                                <button class="ip_search2-button filter_search_input-button"></button>
                            </div>
                        </div>
                        <div class="filter-nav__sub-container filter-nav__sub-container-ajax">
                            <div class="filter-nav__sub-container-inner">
                                <div class="custom_dropdown_title not-found-title" style="display: none;">Ничего не
                                    найдено
                                </div>
                                <div class="filter-nav__tabs-container filter_search_list">
                                    <div class="alphabetic_list alphabetic_servicegroup_list-mobile"></div>
                                </div>
                            </div>
                            <div class="filter-nav__show-inner">
                                <button class="button long filter-nav__show-btn" type="button">Применить</button>
                            </div>
                        </div>
                    </div>
                    <div class="filter-nav__sub filter_search_container" data-filter="filter-4">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Кафедра</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                            <div action="" method="post" class="filter-nav__search">
                                <input type="search" class="input_text ip_search ip_search2 filter_search_input"
                                       placeholder="Введите название кафедры"/>
                                <button class="ip_search2-button filter_search_input-button"></button>
                            </div>
                        </div>
                        <div class="custom_dropdown_title not-found-title" style="display: none;">Ничего не найдено
                        </div>
                        <div class="filter-nav__sub-container filter_search_list">
                            <div class="filter-nav__sub-container-inner mScrollbarCustom simplebar">
                                <div class="custom_dropdown-row">
                                    <input class="ch custom_dropdown-choice mob-all-checks" type="checkbox"
                                           id="all_directions" data-value="Все"/>
                                    <label>Все</label>
                                </div>
                                <?php foreach ($directions as $direction) { ?>
                                    <div class="custom_dropdown-row">
                                        <input id="m_directs_<?= $direction->id; ?>" class="ch custom_dropdown-choice"
                                               name="directs[]" class="filter_jmaka rd" type="checkbox"
                                               data-value="<?= $direction->name; ?>" value="<?= $direction->id; ?>"/>
                                        <label><?= $direction->name; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="filter-nav__show-inner">
                                <button class="button long filter-nav__show-btn" type="button">Применить</button>
                            </div>
                        </div>
                    </div>
                    <div class="filter-nav__sub filter_search_container" data-filter="filter-5">
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
                                    <input id="m_servtype_online" class="ch custom_dropdown-choice" data-value="Онлайн"
                                           type="checkbox" name="service_type[]" value="online"/>
                                    <label>Онлайн</label>
                                </div>
                                <div class="custom_dropdown-row">
                                    <input id="m_servtype_offline" class="ch custom_dropdown-choice" data-value="Офлайн"
                                           type="checkbox" name="service_type[]" value="offline"/>
                                    <label>Офлайн</label>
                                </div>
                            </div>
                            <div class="filter-nav__show-inner">
                                <button class="button long filter-nav__show-btn" type="button">Применить</button>
                            </div>
                        </div>
                    </div>
                    <div class="filter-nav__sub filter_search_container" data-filter="filter-7">
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
                                        <div class="custom_dropdown-row">
                                            <input id="m_city_<?= $city->id; ?>" class="ch custom_dropdown-choice"
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

                    <!-- <div class="filter-nav__sub filter_search_container" data-filter="filter-8">
                        <div class="filter-nav__header">
                            <div class="filter-nav__header-top">
                                <div class="filter-nav__back"></div>
                                <div class="filter-nav__title">Пол</div>
                                <button class="mob-filter-clear  clear-filter-item_js" type="button">Сбросить</button>
                            </div>
                        </div>
                        <div class="filter-nav__sub-container filter_search_list">
                            <div class="filter-nav__sub-container-inner">
                                <div class="custom_dropdown-row">
                                    <input class="ch custom_dropdown-choice mob-all-checks" type="checkbox" value="Не важно"/>
                                    <label>Не важно</label>
                                </div>
                                <div class="custom_dropdown-row">
                                    <input id="m_servtype_male" class="ch custom_dropdown-choice" type="checkbox" name="gender[]" value="male" data-value="Мужской" />
                                    <label>Мужской</label>
                                </div>
                                <div class="custom_dropdown-row">
                                    <input id="m_servtype_female" class="ch custom_dropdown-choice" type="checkbox" name="gender[]" value="female" data-value="Женский"/>
                                    <label>Женский</label>
                                </div>
                            </div>
                            <div class="filter-nav__show-inner">
                                <button class="button long filter-nav__show-btn" type="button">Применить</button>
                            </div>
                        </div>
                    </div> -->
                </nav>
            </form>

            <form action="<?= $academic_page->getUrlPath(); ?>" method="get"
                  class="columns_box filters-desktop-form desktop_filters_form">
                <main class="main_col">
                    <div class="experts_filters_accordions">
                        <!-- ACCORDION ITEM -->
                        <div class="experts_filters-accordion_item opened" id="filter-1">
                            <h3 class="experts_filters-accordion_title">Решаемая задача</h3>
                            <div class="experts_filters-accordion_content noborder">
                                <div class="custom_dropdown-list filter_search_container standalone">
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
                                        </div>
                                    </div>
                                    <div class="custom_dropdown-list-inner checkboxes_js">
                                        <div class="custom_dropdown_item">
                                            <div class="custom_dropdown_title">По алфавиту</div>
                                            <div class="alphabetic_list alphabetic-all-filters_task_list-desktop">
                                                <?php if (!empty($tasks_pop)) {
                                                    foreach ($tasks_pop as $task) { ?>
                                                        <div class="custom_dropdown-row">
                                                            <input name="task[]" class="ch custom_dropdown-choice"
                                                                   data-id="task_<?= $task->id; ?>"
                                                                   data-value="<?= $task->name; ?>" type="checkbox"
                                                                   id="task_<?= $task->id; ?>"
                                                                   value="<?= $task->id; ?>"/>
                                                            <label><?= $task->name; ?></label>
                                                        </div>
                                                    <?php }
                                                } ?>
                                            </div>
                                            <div class="show-more-btn-wrapper">
                                                <button class="button long show-more-btn tasks">Показать еще</button>
                                            </div>
                                        </div>
                                        <!-- <?php if (!empty($tasks_pop)) { ?>
										<div class="custom_dropdown_item filter_search_popular">
											<div class="custom_dropdown_title">Популярные</div>
											<div class="popular_list filter_search_list">
												<?php foreach ($tasks_pop as $task) { ?>
													<div class="custom_dropdown-row">
														<input class="ch custom_dropdown-choice input-popular" data-id="task_<?= $task->id; ?>" data-value="<?= $task->name; ?>" type="checkbox" id="pop_task_<?= $task->id; ?>" value="<?= $task->id; ?>" />
														<label><?= $task->name; ?></label>
													</div>
												<?php } ?>
											</div>
										</div>
									<?php } ?> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ACCORDION ITEM -->
                        <div class="experts_filters-accordion_item opened" id="filter-2">
                            <h3 class="experts_filters-accordion_title">Специализации</h3>
                            <div class="experts_filters-accordion_content noborder">
                                <div class="custom_dropdown-list filter_search_container standalone">
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
                                        </div>
                                    </div>
                                    <div class="custom_dropdown-list-inner checkboxes_js">
                                        <div class="custom_dropdown_item">
                                            <div class="custom_dropdown_title">По алфавиту</div>
                                            <div class="alphabetic_list alphabetic-all-filters_competence_list-desktop">
                                                <?php if (!empty($competence_pop)) {
                                                    foreach ($competence_pop as $competence) { ?>
                                                        <div class="custom_dropdown-row">
                                                            <input name="competence[]" class="ch custom_dropdown-choice"
                                                                   data-id="competence_<?= $competence->id; ?>"
                                                                   data-value="<?= $competence->name; ?>"
                                                                   type="checkbox"
                                                                   id="competence_<?= $competence->id; ?>"
                                                                   value="<?= $competence->id; ?>"/>
                                                            <label><?= $competence->name; ?></label>
                                                        </div>
                                                    <?php }
                                                } ?>
                                            </div>
                                            <div class="show-more-btn-wrapper">
                                                <button class="button long show-more-btn competence">Показать еще
                                                </button>
                                            </div>
                                        </div>
                                        <!-- <?php if (!empty($competence_pop)) { ?>
										<div class="custom_dropdown_item filter_search_popular">
											<div class="custom_dropdown_title">Популярные</div>
											<div class="popular_list filter_search_list">
												<?php foreach ($competence_pop as $competence) { ?>
													<div class="custom_dropdown-row">
														<input class="ch custom_dropdown-choice input-popular" data-id="competence_<?= $competence->id; ?>" data-value="<?= $competence->name; ?>" type="checkbox" id="pop_competence_<?= $competence->id; ?>" value="<?= $competence->id; ?>" />
														<label><?= $competence->name; ?></label>
													</div>
												<?php } ?>
											</div>
										</div>
									<?php } ?> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ACCORDION ITEM -->
                        <div class="experts_filters-accordion_item opened" id="filter-3">
                            <h3 class="experts_filters-accordion_title">Вид услуги</h3>
                            <div class="experts_filters-accordion_content">
                                <?php foreach ($all_servgroups as $servgroup) { ?>
                                    <div class="checkbox_fullwidth_cell">
                                        <input class="ch custom_dropdown-choice"
                                               type="checkbox" <?= (in_array($servgroup->id, $terms['servgroup'])) ? 'checked="checked"' : ''; ?>
                                               data-value="<?= $servgroup->name; ?>"
                                               id="servgroup_<?= $servgroup->id; ?>" name="servgroup[]"
                                               value="<?= $servgroup->id; ?>">
                                        <label><?= $servgroup->name; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <!-- ACCORDION ITEM -->
                        <div class="experts_filters-accordion_item opened" id="filter-4">
                            <h3 class="experts_filters-accordion_title">Формат оказания услуги</h3>
                            <div class="experts_filters-accordion_content">
                                <div class="checkbox_fullwidth_cell">
                                    <input class="ch custom_dropdown-choice" data-value="Онлайн" type="checkbox"
                                           name="service_type[]" value="online">
                                    <label>Онлайн</label>
                                </div>
                                <div class="checkbox_fullwidth_cell">
                                    <input class="ch custom_dropdown-choice" data-value="Офлайн" type="checkbox"
                                           name="service_type[]" value="offline">
                                    <label>Офлайн</label>
                                </div>
                            </div>
                        </div>
                        <!-- ACCORDION ITEM -->
                        <div class="experts_filters-accordion_item opened" id="filter-5">
                            <h3 class="experts_filters-accordion_title">Кафедра</h3>
                            <div class="experts_filters-accordion_content noborder">
                                <?php foreach ($directions as $direction) { ?>
                                    <div class="checkbox_fullwidth_cell">
                                        <input class="ch custom_dropdown-choice" type="checkbox"
                                               data-value="<?= $direction->name; ?>" name="directs[]"
                                               value="<?= $direction->id; ?>">
                                        <label><?= $direction->name; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- ACCORDION ITEM -->
                        <div class="experts_filters-accordion_item opened" id="filter-6">
                            <h3 class="experts_filters-accordion_title">Стоимость услуги</h3>
                            <div class="experts_filters-accordion_content">
                                <div class="range-filter  range-filter-price_js">
                                    <div class="range-filter_item">
                                        <input type="number" name="price_from" placeholder="от 0"
                                               class="range-filter_input range-filter_input-from">
                                        <button type="button" class="clear-input-btn"></button>
                                    </div>
                                    <div class="range-filter_item">
                                        <input type="number" name="price_to" placeholder="до 100 000"
                                               class="range-filter_input range-filter_input-to">
                                        <button type="button" class="clear-input-btn"></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ACCORDION ITEM -->
                        <div class="experts_filters-accordion_item opened" id="filter-7">
                            <h3 class="experts_filters-accordion_title">Город</h3>
                            <div class="experts_filters-accordion_content noborder">
                                <div class="custom_dropdown-list filter_search_container standalone">
                                    <div class="custom_dropdown-top">
                                        <div class="custom_dropdown-search">
                                            <input type="text"
                                                   class="input_text ip_search ip_search2 filter_search_input"
                                                   placeholder="Введите название города"/>
                                            <button class="ip_search2-button filter_search_input-button"></button>
                                        </div>
                                        <div class="custom_dropdown-selected">
                                            <div class="custom_dropdown-selected-title">Выбрано:</div>
                                            <div class="custom_dropdown-selected-content mScrollbarCustom simplebar"></div>
                                        </div>
                                    </div>
                                    <div class="custom_dropdown-list-inner checkboxes_js">
                                        <div class="custom_dropdown_item">
                                            <div class="custom_dropdown_title">По алфавиту</div>
                                            <div class="alphabetic_list alphabetic-all-filters_city_list-desktop">
                                                <?php if (!empty($cities_pop)) {
                                                    foreach ($cities_pop as $city) { ?>
                                                        <div class="custom_dropdown-row">
                                                            <input class="ch custom_dropdown-choice"
                                                                   data-value="<?= $city->name; ?>" type="checkbox"
                                                                   id="city_<?= $city->id; ?>" name="city[]"
                                                                   value="<?= $city->id; ?>"/>
                                                            <label><?= $city->name; ?></label>
                                                        </div>
                                                    <?php }
                                                } ?>
                                            </div>
                                            <div class="show-more-btn-wrapper">
                                                <button class="button long show-more-btn cities">Показать еще</button>
                                            </div>
                                        </div>
                                        <?php if (!empty($cities_pop)) { ?>
                                            <div class="custom_dropdown_item filter_search_popular">
                                                <div class="custom_dropdown_title">Популярные</div>
                                                <div class="popular_list filter_search_list">
                                                    <?php foreach ($cities_pop as $city) { ?>
                                                        <div class="custom_dropdown-row">
                                                            <input class="ch custom_dropdown-choice input-popular"
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
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ACCORDION ITEM -->
                        <!-- <div class="experts_filters-accordion_item opened" id="filter-8">
                            <h3 class="experts_filters-accordion_title">Пол</h3>
                            <div class="experts_filters-accordion_content">
                                <div class="checkbox_fullwidth_cell">
                                    <input class="ch custom_dropdown-choice allChecks" type="checkbox">
                                    <label>Не важно</label>
                                </div>
                                <div class="checkbox_fullwidth_cell">
                                    <input class="ch custom_dropdown-choice" type="checkbox" name="gender[]" value="male">
                                    <label>Мужской</label>
                                </div>
                                <div class="checkbox_fullwidth_cell">
                                    <input class="ch custom_dropdown-choice" type="checkbox" name="gender[]" value="female">
                                    <label>Женский</label>
                                </div>
                            </div>
                        </div> -->
                        <!-- ACCORDION ITEM -->
                        <!-- <div class="experts_filters-accordion_item opened" id="filter-9">
                            <h3 class="experts_filters-accordion_title">Возраст</h3>
                            <div class="experts_filters-accordion_content">
                                <div class="range-filter  range-filter-age_js">
                                    <div class="range-filter_item">
                                        <input type="number" name="age_from" placeholder="от 18" class="range-filter_input range-filter_input-from" value="">
                                        <button type="button" class="clear-input-btn"></button>
                                    </div>
                                    <div class="range-filter_item">
                                        <input type="number" name="age_to" placeholder="до 100" class="range-filter_input range-filter_input-to" value="">
                                        <button type="button" class="clear-input-btn"></button>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                    <div class="help_find_expert_box">
                        <div class="help_find_expert_inner">
                            <h2>Помощь в подборе эксперта</h2>
                            <p>Отправьте запрос, чтобы мы подобрали эксперта для решения именно вашей задачи.</p>

                            <a href="#" class="button white filtersfos_show">Отправить запрос</a>
                        </div>
                    </div>
                </main>

                <aside class="sidebar_col filters_sidebar">
                    <div class="sidebar_box mb30 flow_block">
                        <h3><b>Фильтры</b></h3>
                        <div class="mScrollbarCustom simplebar">
                            <div class="filters_sidebar_row" data-id="filter-1">
                                <a href="#filter-1" class="filters_sidebar_anchor">Задача <i
                                            class="fa fa-angle-down"></i></a>
                                <div class="filters_sidebar_picked"></div>
                            </div>
                            <div class="filters_sidebar_row" data-id="filter-2">
                                <a href="#filter-2" class="filters_sidebar_anchor">Специализация <i
                                            class="fa fa-angle-down"></i></a>
                                <div class="filters_sidebar_picked"></div>
                            </div>
                            <div class="filters_sidebar_row" data-id="filter-3">
                                <a href="#filter-3" class="filters_sidebar_anchor">Вид услуги <i
                                            class="fa fa-angle-down"></i></a>
                                <div class="filters_sidebar_picked"></div>
                            </div>
                            <div class="filters_sidebar_row" data-id="filter-4">
                                <a href="#filter-4" class="filters_sidebar_anchor">Формат <i
                                            class="fa fa-angle-down"></i></a>
                                <div class="filters_sidebar_picked"></div>
                            </div>
                            <div class="filters_sidebar_row" data-id="filter-5">
                                <a href="#filter-5" class="filters_sidebar_anchor">Кафедра <i
                                            class="fa fa-angle-down"></i></a>
                                <div class="filters_sidebar_picked"></div>
                            </div>
                            <div class="filters_sidebar_row" data-id="filter-6">
                                <a href="#filter-6" class="filters_sidebar_anchor">Стоимость <i
                                            class="fa fa-angle-down"></i></a>
                                <div class="filters_sidebar_picked"></div>
                            </div>
                            <div class="filters_sidebar_row" data-id="filter-7">
                                <a href="#filter-7" class="filters_sidebar_anchor">Город <i
                                            class="fa fa-angle-down"></i></a>
                                <div class="filters_sidebar_picked"></div>
                            </div>
                            <!-- <div class="filters_sidebar_row" data-id="filter-8">
                                <a href="#filter-8" class="filters_sidebar_anchor">Пол <i class="fa fa-angle-down"></i></a>
                                <div class="filters_sidebar_picked"></div>
                            </div> -->
                            <!-- <div class="filters_sidebar_row" data-id="filter-9">
                                <a href="#filter-9" class="filters_sidebar_anchor">Возраст <i class="fa fa-angle-down"></i></a>
                                <div class="filters_sidebar_picked"></div>
                            </div> -->
                        </div>
                        <div class="filters_sidebar_buttons">
                            <a id="count_offers" href="" class="button">Показать предложения</a>
                            <a href="#" class="button lightGray filters_reset">Сбросить фильтры</a>
                        </div>
                    </div>

                    <?= \app\modules\banner\widgets\banner\PageBannerWidget::widget(['id' => $this->params['page_id']]); ?>

                </aside>
            </form>

            <?= \app\modules\banner\widgets\banner\PageBannerWidget::widget(['id' => $this->params['page_id'], 'mobile' => '1']); ?>

        </div>
    </main>
<?= \app\modules\filtersfos\widgets\filtersfos\FiltersfosWidget::widget(); ?>
<?php
$this->registerJsFile('/js/filters.js', ['depends' => [app\assets\AppAsset::class]]);

$cities_list = ArrayHelper::map($cities_short, 'id', 'id');
$this->registerJsVar('excluded', $cities_list, $position = yii\web\View::POS_HEAD);

$tasks_list = ArrayHelper::map($tasks, 'id', 'id');
$this->registerJsVar('excluded_task', $tasks_list, $position = yii\web\View::POS_HEAD);

$competence_list = ArrayHelper::map($competences, 'id', 'id');
$this->registerJsVar('excluded_competence', $competence_list, $position = yii\web\View::POS_HEAD);

$servicegroup_list = ArrayHelper::map($servgroups, 'id', 'id');
$this->registerJsVar('excluded_servicegroup', $servicegroup_list, $position = yii\web\View::POS_HEAD);

// $this->registerJsVar('filter_roles','', $position = yii\web\View::POS_HEAD);

$filter_roles = 'academ';
$this->registerJsVar('filter_roles', $filter_roles, $position = yii\web\View::POS_HEAD);

$url = $academic_page->getUrlPath();
$js = <<<JS

	$('#count_offers, #mobile_count').click(function(e){
		e.preventDefault();
		let new_url = $(this).closest('form').serialize();
		window.location.href = '{$url}?'+new_url;
	});
	
	$('.filter-nav__close, .nav-overlay, .filter-nav__main .filter-nav__show-inner .filter-nav__show-btn').not('.js-filter-nav__show-btn').on('click', function (ev) {
        ev.preventDefault();
        $('.filter-nav').removeClass('filter-nav--open');
        $('.nav-overlay').removeClass('nav-overlay--open');
        $('body').removeClass('modal-open');
        $('.filter-nav__sub').removeClass('filter-nav__sub--open');
	});

	function cityFilterMob() {
		let click = 1;
		$('.filter-nav__link-city').click(function(){
			if(click == 1) {
				$.ajax({
					type: 'GET',
					url: '/filter/cities/?roles=academ',
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
JS;
$this->registerJs($js);
?>