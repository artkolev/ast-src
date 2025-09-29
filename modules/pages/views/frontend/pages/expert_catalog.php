<?php

use app\modules\pages\models\AcademyCatalog;
use app\modules\pages\models\AllCatalog;
use app\modules\pages\models\ExpertCatalog;
use app\modules\pages\models\ExporgCatalog;
use app\modules\service\models\Service;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$this->registerCssFile('/css/style-blog.css', ['depends' => [app\assets\AppAsset::class]]);
?>
    <main class="sec content_sec">
        <div class="container wide">
            <h1 class="page-title"><?= $model->getNameForView(); ?></h1>
            <?php if (!empty($model->content)) { ?>
                <div class="subheader"><?= $model->content; ?></div>
            <?php } ?>
            <form method="get" class="mobile_filters_form filter-mob-form">
                <div class="mob_search_box">
                    <div class="mob-filter-buttons">
                        <button type="button" class="mob-filter-btn">Все фильтры</button>
                        <button type="button" class="mob-filter-clear-all clear-filter-all_js">Сбросить</button>
                    </div>
                    <div class="mob-search-wrapper">
                        <input type="text" name="query" value="<?= $terms['search']; ?>" class="input_text ip_search"
                               placeholder="Поиск"/>
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
                                               class="range-filter_input range-filter_input-from"
                                               value="<?= isset($terms['price']) ? $terms['price']['from'] : ''; ?>">
                                        <button type="button" class="clear-input-btn" type="button"></button>
                                    </div>
                                    <div class="range-filter_item">
                                        <input type="number" name="price_to" placeholder="до 100 000" data-min="1000000"
                                               class="range-filter_input range-filter_input-to"
                                               value="<?= isset($terms['price']) ? $terms['price']['to'] : ''; ?>">
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
                        </ul>
                        <div class="filter-nav__show-inner">
                            <button class="button long filter-nav__show-btn js-mob-filters" type="submit"><span
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
                                <div class="filter-nav__sub-container filter_search_list">
                                    <div class="alphabetic_list alphabetic_servicegroup_list-mobile"></div>
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
                                        <input id="m_directs_<?= $direction->id; ?>"
                                               class="ch custom_dropdown-choice" <?= (in_array($direction->id, $terms['directs'])) ? 'checked="checked"' : ''; ?>
                                               name="directs[]" class="filter_jmaka ch" type="checkbox"
                                               data-value="<?= $direction->name; ?>" value="<?= $direction->id; ?>"/>
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
                                <?= Html::checkboxList(
                                        'service_type',
                                        $terms['service_type'],
                                        Service::getKindList(),
                                        ['tag' => null, 'item' => function ($index, $label, $name, $checked, $value) {
                                            $return = '<div class="custom_dropdown-row"><input id="servtype_' . $value . '" type="checkbox" ' . ($checked ? 'checked="checked" ' : '') . ' data-value="' . $label . '" name="' . $name . '" value="' . $value . '" class="ch custom_dropdown-choice"><label>' . $label . '</label></div>';
                                            return $return;
                                        }
                                        ]
                                ); ?>
                            </div>
                            <div class="filter-nav__show-inner">
                                <button class="button long filter-nav__show-btn filter-mob-nav__show-btn" type="button">
                                    Применить
                                </button>
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
                </nav>
            </form>

            <form method="get"
                  class="desktop_filters_form columns_box columns_box-filters filters-desktop-form all-events-form">
                <main class="main_col">
                    <div class="lenta-menu">
                        <?php if ($allCatalog = AllCatalog::find()->where(['model' => AllCatalog::class, 'visible' => 1])->one()) { ?>
                            <a href="<?= $allCatalog->getUrlPath(); ?>"
                               class="<?= '/' . Yii::$app->request->pathInfo == $allCatalog->getUrlPath() ? 'active' : ''; ?>">Все</a>
                        <?php } ?>
                        <?php if ($academyCatalog = AcademyCatalog::find()->where(['model' => AcademyCatalog::class, 'visible' => 1])->one()) { ?>
                            <a href="<?= $academyCatalog->getUrlPath(); ?>"
                               class="<?= '/' . Yii::$app->request->pathInfo == $academyCatalog->getUrlPath() ? 'active' : ''; ?>">Экспертный
                                совет</a>
                        <?php } ?>
                        <?php if ($expertCatalog = ExpertCatalog::find()->where(['model' => ExpertCatalog::class, 'visible' => 1])->one()) { ?>
                            <a href="<?= $expertCatalog->getUrlPath(); ?>"
                               class="<?= '/' . Yii::$app->request->pathInfo == $expertCatalog->getUrlPath() ? 'active' : ''; ?>">Эксперты</a>
                        <?php } ?>
                        <?php if ($exporgCatalog = ExporgCatalog::find()->where(['model' => ExporgCatalog::class, 'visible' => 1])->one()) { ?>
                            <a href="<?= $exporgCatalog->getUrlPath(); ?>"
                               class="<?= '/' . Yii::$app->request->pathInfo == $exporgCatalog->getUrlPath() ? 'active' : ''; ?>">Экспертные
                                организации</a>
                        <?php } ?>
                        <!--                    Пока скрыть-->
                        <!--                    <a href="/about/#h_sovet">Почетные эксперты</a>-->
                        <!--                    <a href="/about/#inv_sovet">Приглашенные эксперты</a>-->
                    </div>
                    <div class="directions_search_box filters">
                        <div class="search_flex">
                            <!-- фильтр решаемых задач -->
                            <div class="custom_dropdown_box">
                                <span class="clear-btn"></span>
                                <a href="#" class="custom_dropdown-link custom_dropdown-link-tasks"
                                   data-placeholder="Решаемая задача"></a>
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
                                            <div class="custom_dropdown_title not-found-title" style="display: none;">
                                                Ничего не найдено
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
                                            <div class="custom_dropdown_title not-found-title" style="display: none;">
                                                Ничего не найдено
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
                            <div class="custom_dropdown_box">
                                <span class="clear-btn"></span>
                                <a href="#" class="custom_dropdown-link custom_dropdown-link-servicegroup"
                                   data-placeholder="Вид услуги"></a>
                                <div class="custom_dropdown-list custom_dropdown-list-right filter_search_container">
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
                                            <div class="custom_dropdown-selected-content  mScrollbarCustom simplebar"></div>
                                            <button class="button small shadow custom_dropdown-selected-button">
                                                Применить
                                            </button>
                                        </div>
                                    </div>
                                    <div class="custom_dropdown-list-inner checkboxes_js">
                                        <div class="custom_dropdown_item">
                                            <div class="custom_dropdown_title">По алфавиту</div>
                                            <div class="custom_dropdown_title not-found-title" style="display: none;">
                                                Ничего не найдено
                                            </div>
                                            <div class="alphabetic_list alphabetic_servicegroup_list-desktop">
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
                        <div class="filters-list-selected"></div>
                    </div>
                    <div id="expert_content">
                        <?= $this->render('_expert_box', ['items' => $items]); ?>
                    </div>
                    <div id="pager_content">
                        <?= app\widgets\pagination\LinkPager::widget(['pages' => $pages]); ?>
                    </div>

                    <article class="experts_article">
                        <?= $model->content_seo; ?>
                    </article>
                </main>
                <aside class="sidebar_col">
                    <div class="search_flex">
                        <div class="ip_cell search-wrapper">
                            <input type="text" name="query" value="<?= $terms['search']; ?>"
                                   class="input_text ip_search" placeholder="Введите название "/>
                            <button class="button-o button-search" type="submit">Начать поиск</button>
                        </div>
                    </div>
                    <?php if (!empty($directions)) { ?>
                        <div class="sidebar_box">
                            <h3>Кафедры</h3>
                            <div class="checkboxes_js mScrollbarCustom simplebar">
                                <div class="ip_cell">
                                    <input type="checkbox" name="" class="ch allChecks filter_jmaka" value=""/>
                                    <label>Все</label>
                                </div>
                                <?php foreach ($directions as $direction) { ?>
                                    <div class="ip_cell">
                                        <input type="checkbox" <?= (in_array($direction->id, $terms['directs'])) ? 'checked="checked"' : ''; ?>
                                               name="directs[]" class="filter_jmaka ch" value="<?= $direction->id; ?>"/>
                                        <label><?= $direction->name; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="sidebar_box">
                        <h3>Формат оказания услуг</h3>
                        <div class="checkboxes_js">
                            <?= Html::checkboxList(
                                    'service_type',
                                    $terms['service_type'],
                                    Service::getKindList(),
                                    ['tag' => null, 'item' => function ($index, $label, $name, $checked, $value) {
                                        $return = '<div class="ip_cell"><input type="checkbox" ' . ($checked ? 'checked="checked" ' : '') . ' name="' . $name . '" value="' . $value . '" class="filter_jmaka ch"><label>' . $label . '</label></div>';
                                        return $return;
                                    }
                                    ]
                            ); ?>
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

                    <a href="#" class="button button-o button-o-grey filters_reset">Сбросить фильтры</a>
                    <?= \app\modules\banner\widgets\banner\PageBannerWidget::widget(['id' => $this->params['page_id']]); ?>
                </aside>
            </form>
            <?= \app\modules\banner\widgets\banner\PageBannerWidget::widget(['id' => $this->params['page_id'], 'mobile' => '1']); ?>
        </div>
    </main>

<?= \app\modules\feedacadem\widgets\feedacadem\FeedacademWidget::widget(); ?>
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

$this->registerJsVar('filter_roles', $filter_roles, $position = yii\web\View::POS_HEAD);

$url = $model->getUrlPath();
$url_filters = $filter_page?->getUrlPath();
$js = <<<JS
	/*$('.go_filter').click(function(e){
		e.preventDefault();
		let form = $(this).closest('form');
		let new_url = $(form).serialize();
		window.location.href = '{$url_filters}?'+new_url;
	});*/
	// выполняется на document.ready
	function cityFilterDesk() {
		$.ajax({
			type: 'GET',
			url: '/filter/cities/?roles='+filter_roles,
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
					url: '/filter/cities/?roles='+filter_roles,
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
	
	$('body').on('click','.academ_connect', function(e){
		e.preventDefault();
		$('#feedacadem-academ').val($(this).data('academ'));
		modalPos('#feedacadem_modal');
	});
	$('body').on('change','.custom_dropdown-selected-button, .filter_jmaka, .tap_tap_change', function(e) {
		$(this).closest('form').submit();
	});
	$('.custom_dropdown-selected-button').click(function(e) {
		$(this).closest('.custom_dropdown_box').removeClass('active');
	});
	if(window.innerWidth > 1100) {
		$('body').on('change','.custom_dropdown-choice', function(e) {
	    	let parent = $(this).closest('.custom_dropdown-list');
	    	if(parent.find('.filter_selected_item').length < 2) {
				$(this).closest('form').submit();
			}
		});
	}
	$('body').on('click','.mob-search-button, .js-mob-filters, .filter-mob-nav__show-btn', function(e) {
		e.preventDefault();
		$(this).closest('form').submit();
	});

	$('body').on('keyup change','.range-filter_input', function(e) {
		if(($('.filters-desktop-form .range-filter_input-from').val() != '' && $('.filters-desktop-form .range-filter_input-to').val() != '')) {
			$(this).closest('form').submit();
		}
	});
	
	let ex_click = 1;
	const createLabelTemplate = (inputId, name) => (
        `<label class="filter_selected_item" for="{$inputId}">{$name}</label>`
    );
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
					$('#expert_content').html(data.html);
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

					$('.expert_item-tags .tag').not('.more').on('click', function(ev) {
						ev.preventDefault();
						const currId = $(this).data('tagid');
						const currItems = $('.custom_dropdown_box .alphabetic_competence_list-desktop .ch:not(.input-popular)[value="' + currId + '"]');
						if(ex_click == 1) {
							$.ajax({
								type: 'GET',
								url: '/filter/competence'+ filter_suffix +'/?roles='+filter_roles,
								processData: true,
								dataType: 'json',
								success: function(data) {
									$('.alphabetic_competence_list-desktop .circularG-wrapper').hide();
									if (data.status == 'success') {
										$('.alphabetic_competence_list-desktop').empty();
										// список решаемых задач приходит в data.result
										$.each(data.result.alfabet, function(index, alphabet) {
											$('.alphabetic_competence_list-desktop').append('<div class="filter_search_list filter_search_list-competence-desktop" data-alphabet="'+ alphabet +'"></div>');
										});
										$.each(data.result.result, function(index, competence) {
											$('.filter_search_list-competence-desktop[data-alphabet="'+ competence.letter +'"]').append('<div class="custom_dropdown-row"><input class="ch custom_dropdown-choice" data-value="'+ competence.name +'" type="checkbox" id="competence_'+ competence.id +'" name="competence[]" value="'+ competence.id +'" /><label>'+ competence.name +'</label></div>');
											if($('#competence_'+ competence.id).val() == excluded_competence[competence.id]) {
												$('#competence_'+ competence.id).attr('checked', 'checked');
											}
										});
										const filterDesktopCheckedItems = $('.alphabetic_competence_list-desktop .ch:not(.input-popular):checked');
										if (filterDesktopCheckedItems.length > 0) {
											filterDesktopCheckedItems.each(function(i) {
												const currId = $(this).prop('id');
												const filterLabelContainer = $(this).parents('.custom_dropdown-list').find('.custom_dropdown-selected-content .mCSB_container');
												const filterBox = $(this).parents('.custom_dropdown_box');
												
												if(filterLabelContainer.find('.filter_selected_item[for="'+ $(this).prop('id') +'"]').length == 0) {
													filterLabelContainer.append(createLabelTemplate($(this).prop('id'), $(this).data('value')));
												}
												// allFiltersSelectedInner.append(createLabelTemplate($(this).prop('id'), $(this).data('value')));
												filterBox.addClass('custom_dropdown_box-clear');
												if (filterLabelContainer.find('.filter_selected_item').length) {
													$(this).parents('.custom_dropdown-list').find('.custom_dropdown-selected').addClass('selected-content--show');
												}
												$(this).parents('.custom_dropdown-list').find('.ch.input-popular[data-id="' + currId + '"]').prop('checked', true);
											});
										}

										if (!currItems.prop('checked')) {
											$('.custom_dropdown_box .alphabetic_competence_list-desktop .ch:not(.input-popular)[value="' + currId + '"]').prop('checked', true).trigger('change');
											// currItems.closest('form').submit();
										}
										
									} else {
										// что-то пошло не так и списка решаемых задач нету
										
									}
								}
							});
							ex_click++;
						} else {
							if (!currItems.prop('checked')) {
								$('.custom_dropdown_box .alphabetic_competence_list-desktop .ch:not(.input-popular)[value="' + currId + '"]').prop('checked', true).trigger('change');
								$('.custom_dropdown_box .alphabetic_competence_list-desktop .ch:not(.input-popular)[value="' + currId + '"]').closest('form').submit();
							}
						}
					});
				} else {
					// сообщение об ошибке на страницу
					$('#expert_content').html('<h3 class="pb20"><i>К сожалению, по вашему запросу ничего не найдено</i></h3>');
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
					$('#expert_content').html(data.html);
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
					$('.expert_item-tags .tag').not('.more').on('click', function(ev) {
						ev.preventDefault();
						const currId = $(this).data('tagid');
						const currItemsMobile = $('.filter-nav__sub .alphabetic_competence_list-mobile .ch:not(.input-popular)[value="' + currId + '"]');
						if(ex_click == 1) {
							$.ajax({
								type: 'GET',
								url: '/filter/competence'+ filter_suffix +'/?roles='+filter_roles,
								processData: true,
								dataType: 'json',
								success: function(data) {
									if (data.status == 'success') {
										let count = 0;
										$('.alphabetic_competence_list-mobile').empty();
										// список решаемых задач приходит в data.result
										$.each(data.result.alfabet, function(index, alphabet) {
											$('.alphabetic_competence_list-mobile').append('<div class="filter_search_list filter_search_list-competence-mobile" data-alphabet="'+ alphabet +'"></div>');
										});
										$.each(data.result.result, function(index, competence) {
											$('.filter_search_list-competence-mobile[data-alphabet="'+ competence.letter +'"]').append('<div class="custom_dropdown-row"><input class="ch custom_dropdown-choice" data-value="'+ competence.name + '" type="checkbox" id="m_competence_'+ competence.id +'" name="competence[]" value="'+ competence.id +'" /><label>'+ competence.name +'</label></div>');
											if($('#m_competence_'+ competence.id).val() == excluded_competence[competence.id]) {
												$('#m_competence_'+ competence.id).attr('checked', 'checked');
											}
											count++;
										});
										if ($('.filter_search_list-competence-mobile .custom_dropdown-row').length === count) {
											const filterCheckedItems = $('.competence .filter-nav__sub-container-ajax .ch:not(.input-popular):checked');
											if (filterCheckedItems.length > 0) {
												filterCheckedItems.each(function(i) {
													const itemId = $(this).prop('id');
													const itemValueId = $(this).prop('value');
													const itemValue = $(this).data('value');
													const filterId = $(this).parents('.filter-nav__sub').data('filter');
													const parentFilterItem = $('.filter-nav__item[data-filter="' + filterId + '"]');
													const filterLabelContainer = $('.filter-nav__item[data-filter="' + filterId + '"] .filter-nav__selected-list');
													$('.filter-nav__main').addClass('filter-nav__main--checked');
													parentFilterItem.addClass('filter-nav__item--selected');
													// filterLabelContainer.append(createLabelTemplate(itemId, itemValue));
													$(this).parents('.filter-nav__sub').addClass('filter-nav__sub--checked');
													$(this).parents('.filter-nav__sub').find('.ch.input-popular[data-id="' + itemId + '"]').prop('checked', true);
												});
											}
										}

										if (!currItemsMobile.prop('checked')) {
											$('.filter-nav__sub .alphabetic_competence_list-mobile .ch:not(.input-popular)[value="' + currId + '"]').prop('checked', true);
											$('.filter-nav__sub .alphabetic_competence_list-mobile .ch:not(.input-popular)[value="' + currId + '"]').closest('form').submit();
										}
										
									} else {
										// что-то пошло не так и списка решаемых задач нету
										
									}
								}
							});
							ex_click++;
						} else {
							if (!currItemsMobile.prop('checked')) {
								$('.filter-nav__sub .alphabetic_competence_list-mobile .ch:not(.input-popular)[value="' + currId + '"]').prop('checked', true).trigger('change');
								$('.filter-nav__sub .alphabetic_competence_list-mobile .ch:not(.input-popular)[value="' + currId + '"]').closest('form').submit();
							}
						}
					});

					let allow_filter_nav__sub_container_click = 1;
					$('.filter-nav__sub-container').on('change', '.ch:not(.input-popular)', function (e) {
						if(!allow_filter_nav__sub_container_click){
							return;
						}
						allow_filter_nav__sub_container_click = 0;
						const itemId = $(this).prop('id');
						const itemValue = $(this).data('value');
						const filterId = $(this).parents('.filter-nav__sub').data('filter');
						const parentFilterItem = $('.filter-nav__item[data-filter="' + filterId + '"]');
						const filterLabelContainer = $('.filter-nav__item[data-filter="' + filterId + '"] .filter-nav__selected-list');
						if ($(this).prop('checked')) {
							if($(this).hasClass('mob-all-checks')) {
								$(this).parent().parent().find('.ch:not(.mob-all-checks)').prop('checked', false).trigger('change');
							} else {
								$(this).parent().parent().find('.mob-all-checks').prop('checked', false).trigger('change');
							}
							parentFilterItem.addClass('filter-nav__item--selected');
							if(filterLabelContainer.find('.filter_selected_item[for="'+ itemId +'"]').length == 0) {
								filterLabelContainer.append(createLabelTemplate(itemId, itemValue));
							}
							$(this).parents('.filter-nav__sub').addClass('filter-nav__sub--checked');
							$(this).parents('.filter-nav__sub').find('.ch.input-popular[data-id="' + itemId + '"]').prop('checked', true);
						} else {
							let del_el = $(this).val();
							if($(this).prop('name') == 'task[]') {
								delete excluded_task[del_el];
							} else if($(this).prop('name') == 'competence[]') {
								delete excluded_competence[del_el];
							} else if($(this).prop('name') == 'servgroup[]') {
								delete excluded_competence[del_el];
							}
							filterLabelContainer.find('.filter_selected_item[for="' + itemId + '"]').remove();
							if (filterLabelContainer.find('.filter_selected_item').length == 0) {
								$(this).parents('.filter-nav__sub').removeClass('filter-nav__sub--checked');
								parentFilterItem.removeClass('filter-nav__item--selected');
							}
							$(this).parents('.filter-nav__sub').find('.ch.input-popular[data-id="' + itemId + '"]').prop('checked', false);
						}

						allow_filter_nav__sub_container_click = 1;
					});
				} else {
					// сообщение об ошибке на страницу
					$('#expert_content').html('<h3 class="pb20"><i>К сожалению, по вашему запросу ничего не найдено</i></h3>');
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
		let keyword = searchParams.get("keyword");
		if(keyword !== null) {
			$('.filters-list-selected').append(createLabelTag(keyword));
			$('.filters_reset').removeClass('disabled');
		}	
	}
	tags();
JS;
$this->registerJs($js);
?>