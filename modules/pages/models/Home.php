<?php
/**
 * @modelDescr Главная страница сайта
 */

namespace app\modules\pages\models;

use app\modules\admin\behaviors\ClearRelation;
use app\modules\admin\behaviors\SaveFilesRelation;
use app\modules\admin\behaviors\SaveOneVarRelation;
use app\modules\admin\behaviors\Serialize;
use app\modules\admin\components\FilestoreModel;
use app\modules\events\models\Events;
use app\modules\service\models\Service;
use app\modules\users\models\UserAR;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class Home extends Page
{
    public static $name_for_list = "Главную страницу";
    public static $count_for_list = 1;
    public $view = 'index';
    public $action_id = 'pages/pages/index';
    public $show_projects;
    public $show_blog;
    public $show_events;
    public $show_slider;
    public $show_academ;
    public $show_expert;

    public $button1_text;
    public $button1_link;
    public $button2_text;
    public $button2_link;

    public $join_show;
    public $join_title;
    public $join_text;
    public $join_button_text;
    public $join_button_link;

    public $help_show;
    public $help_title;
    public $services_subtitle;

    public $plus_image_loader;
    public $plus_show;
    public $plus_title;
    public $plus_text;
    public $plus_button_text;
    public $plus_button_link;
    public $pluses;

    public $startwork_banner1_image_loader;
    public $startwork_banner2_image_loader;

    public $startwork_show;
    public $startwork_title;
    public $startwork_steps;
    public $startwork_banner1_title;
    public $startwork_banner2_title;
    public $startwork_banner1_button_text;
    public $startwork_banner2_button_text;
    public $startwork_banner1_button_link;
    public $startwork_banner2_button_link;
    public $startwork_steps_last_title;
    public $startwork_steps_last_button_text;
    public $startwork_steps_last_button_link;

    public $slider;
    public $sliderimage_loader;
    public $slidermobileimage_loader;

    public function behaviors()
    {
        $parent_behaviors = parent::behaviors();
        unset($parent_behaviors['urlBehaviour']);
        return array_merge($parent_behaviors, [
            0 => [
                'class' => SaveOneVarRelation::class,
                'relations' => [
                    'single' => [
                        'show_projects',
                        'show_blog',
                        'show_events',
                        'show_slider',
                        'show_academ',
                        'show_expert',
                        'button1_text',
                        'button1_link',
                        'button2_text',
                        'button2_link',
                        'join_show',
                        'join_title',
                        'join_text',
                        'join_button_text',
                        'join_button_link',
                        'help_show',
                        'help_title',
                        'services_subtitle',
                        'plus_show',
                        'plus_title',
                        'plus_text',
                        'plus_button_text',
                        'plus_button_link',
                        'pluses',
                        'startwork_show',
                        'startwork_title',
                        'startwork_steps',
                        'startwork_banner1_title',
                        'startwork_banner2_title',
                        'startwork_banner1_button_text',
                        'startwork_banner2_button_text',
                        'startwork_banner1_button_link',
                        'startwork_banner2_button_link',
                        'startwork_steps_last_title',
                        'startwork_steps_last_button_text',
                        'startwork_steps_last_button_link',
                        'slider',
                    ],
                ],
            ],
            1 => [
                'class' => Serialize::class,
                'relations' => [
                    'pluses',
                    'startwork_steps',
                    'slider',
                ],
            ],
            'clearDelete' => [
                'class' => ClearRelation::class,
                'relations' => [
                    'homesocial',
                    'expertshelp',
                ],
            ],
            'saveFiles' => [
                'class' => SaveFilesRelation::class,
                'file_path' => 'files/upload/home/',
                'relations' => [
                    'plus_image' => [
                        'type' => 'single', // multiple
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, png, jpeg, svg',
                            'maxSize' => 2 * 1024 * 1024, // 2Мб
                        ],
                        'main' => [
                            'width' => 542,
                            'height' => 639,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'startwork_banner1_image' => [
                        'type' => 'single', // multiple
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, png, jpeg, svg',
                            'maxSize' => 2 * 1024 * 1024, // 2Мб
                        ],
                        'main' => [
                            'width' => 322,
                            'height' => 195,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'startwork_banner2_image' => [
                        'type' => 'single', // multiple
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, png, jpeg, svg',
                            'maxSize' => 2 * 1024 * 1024, // 2Мб
                        ],
                        'main' => [
                            'width' => 322,
                            'height' => 195,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'sliderimage' => [
                        'type' => 'multirow',
                        'multifield' => 'slider',
                        'multifield_fieldname' => 'slider_image',
                        'multifield_type' => 'single',
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, jpeg, png, svg',
                            'maxSize' => 1024 * 1024 * 2, // 1Мб
                            'minWidth' => 1345,
                            'minHeight' => 250,
                        ],
                        'main' => [
                            'width' => 1345,
                            'height' => 250,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'slidermobileimage' => [
                        'type' => 'multirow',
                        'multifield' => 'slider',
                        'multifield_fieldname' => 'slider_mobile_image',
                        'multifield_type' => 'single',
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, jpeg, png, svg',
                            'maxSize' => 1024 * 1024 * 2, // 1Мб
                            'minWidth' => 375,
                            'minHeight' => 250,
                        ],
                        'main' => [
                            'width' => 375,
                            'height' => 250,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'show_projects' => 'Отображать блок "Проекты"',
            'show_blog' => 'Отображать блок "Новости"',
            'show_events' => 'Отображать блок "Мероприятия"',
            'show_slider' => 'Отображать блок "Слайдер"',
            'show_academ' => 'Отображать блок "Высший экспертный совет"',
            'show_expert' => 'Отображать блок "Эксперты"',
            'button1_text' => 'Текст на кнопке 1',
            'button1_link' => 'Ссылка с кнопки 1',
            'button2_text' => 'Текст на кнопке 2',
            'button2_link' => 'Ссылка с кнопки 2',
            'join_show' => 'Отображать блок',
            'join_title' => 'Заголовок',
            'join_text' => 'Текст',
            'join_button_text' => 'Текст на кнопке',
            'join_button_link' => 'Ссылка с кнопки',
            'help_show' => 'Отображать блок Эксперты помогают',
            'help_title' => 'Заголовок блока Эксперты помогают',
            'services_subtitle' => 'Подзаголовок блока Популярные услуги',
            'expertshelp' => 'Эксперты помогают',
            'plus_image' => 'Изображение',
            'plus_image_loader' => 'Изображение',
            'plus_show' => 'Отображать',
            'plus_title' => 'Заголовок',
            'plus_text' => 'Текст',
            'plus_button_text' => 'Текст на кнопке',
            'plus_button_link' => 'Ссылка с кнопки',
            'pluses' => 'Плюсы',
            'startwork_show' => 'Отображать',
            'startwork_title' => 'Заголовок',
            'startwork_steps' => 'Шаги',
            'startwork_banner1_title' => 'Заголовок баннера 1',
            'startwork_banner2_title' => 'Заголовок баннера 2',
            'startwork_banner1_button_text' => 'Текст на кнопке 1',
            'startwork_banner2_button_text' => 'Текст на кнопке 2',
            'startwork_banner1_button_link' => 'Ссылка с кнопки 1',
            'startwork_banner2_button_link' => 'Ссылка с кнопки 2',
            'startwork_steps_last_title' => 'Заголовок последнего элемента',
            'startwork_steps_last_button_text' => 'Текст на кнопке последнего элемента',
            'startwork_steps_last_button_link' => 'Ссылка с кнопки последнего элемента',
            'startwork_banner1_image_loader' => 'Изображение баннера 1',
            'startwork_banner1_image' => 'Изображение баннера 1',
            'startwork_banner2_image_loader' => 'Изображение баннера 2',
            'startwork_banner2_image' => 'Изображение баннера 2',
            'slider' => 'Слайды',
        ]);
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['show_projects', 'show_blog', 'show_events', 'show_slider', 'show_academ', 'show_expert'], 'safe'],
            [['button1_text', 'button1_link', 'button2_text', 'button2_link'], 'safe'],
            [['join_title', 'join_text', 'join_button_text', 'join_button_link', 'join_show'], 'safe'],
            [['help_show', 'help_title', 'services_subtitle'], 'safe'],
            [['plus_show', 'plus_title', 'plus_text', 'plus_button_text', 'plus_button_link', 'pluses'], 'safe'],
            [['startwork_show', 'startwork_title', 'startwork_steps', 'startwork_banner1_title', 'startwork_banner2_title', 'startwork_banner1_button_text', 'startwork_banner2_button_text', 'startwork_banner1_button_link', 'startwork_banner2_button_link', 'startwork_steps_last_title', 'startwork_steps_last_button_text', 'startwork_steps_last_button_link'], 'safe'],
            [['slider'], 'safe'],
        ]);
    }

    public function getFieldTabs()
    {
        return [
            'main' => 'Основное',
            'blocks' => 'Блоки',
            'pluses' => 'Плюсы',
            'join' => 'Присоединяйтесь',
            'startwork' => 'Начать работу',
            'slider' => 'Слайдер',
            'seo' => 'SEO',
        ];
    }

    public function getRelationTabs()
    {
        return [
            'help' => 'Эксперты помогают',
            'homesocial' => 'Блоки с картинками',
        ];
    }

    public function getFields($tab)
    {
        $fields = [
            'main' => [
                'name' => 'text',
                'url' => 'text',
                'content' => 'text',
                'button1_text' => 'text',
                'button1_link' => 'text',
                'button2_text' => 'text',
                'button2_link' => 'text',
                'order' => 'integer',
                'visible' => 'boolean',
                'parent_id' => [
                    'type' => 'options',
                    'optionList' => $this->getSectionList(),
                ],
            ],
            'blocks' => [
                'help_show' => 'boolean',
                'help_title' => 'text',
                'services_subtitle' => 'text',
                // 'show_slider' => 'boolean',
                // 'show_academ' => 'boolean',
                // 'show_expert' => 'boolean',
                // 'show_projects' => 'boolean',
                // 'show_blog' => 'boolean',
                'show_events' => 'boolean',
            ],
            'pluses' => [
                'plus_show' => 'boolean',
                'plus_title' => 'text',
                'plus_text' => 'textarea',
                'plus_image' => 'image',
                'plus_button_text' => 'text',
                'plus_button_link' => 'text',
                'pluses' => [
                    'type' => 'multifields',
                    'fields' => [
                        'name' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Название',
                        ],
                    ],
                ],
            ],
            'join' => [
                'join_show' => 'boolean',
                'join_title' => 'text',
                'join_text' => 'textarea',
                'join_button_text' => 'text',
                'join_button_link' => 'text',
            ],
            'startwork' => [
                'startwork_show' => 'boolean',
                'startwork_title' => 'text',
                'startwork_steps' => [
                    'type' => 'multifields',
                    'fields' => [
                        'name' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Название',
                        ],
                        'descr' => [
                            'visible_field' => true,
                            'type' => 'textarea',
                            'name' => 'Подробно',
                        ],
                    ],
                ],
                'startwork_steps_last_title' => 'text',
                'startwork_steps_last_button_text' => 'text',
                'startwork_steps_last_button_link' => 'text',
                'startwork_banner1_title' => 'text',
                'startwork_banner1_image' => 'image',
                'startwork_banner1_button_text' => 'text',
                'startwork_banner1_button_link' => 'text',
                'startwork_banner2_title' => 'text',
                'startwork_banner2_image' => 'image',
                'startwork_banner2_button_text' => 'text',
                'startwork_banner2_button_link' => 'text',
            ],
            'slider' => [
                'slider' => [ // Единичный слайд
                    'type' => 'multifields',
                    'fields' => [
                        'name' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Название',
                        ],
                        'url' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'URL',
                        ],
                        'use_fancybox' => [
                            'visible_field' => true,
                            'type' => 'boolean',
                            'name' => 'Открывать видео в fancybox',
                        ],
                        'slider_image' => [
                            'type' => 'image',
                            'source_field' => 'sliderimage',
                            'name' => 'Изображение',
                        ],
                        'slider_mobile_image' => [
                            'type' => 'image',
                            'source_field' => 'slidermobileimage',
                            'name' => 'Изображение для мобильного',
                        ],
                    ],
                ],
            ],
            'seo' => [
                'h1_tag' => 'text',
                'meta_title' => 'text',
                'meta_description' => 'textarea',
                'meta_keywords' => 'textarea',
                'meta_og_title' => 'text',
                'meta_og_description' => 'textarea',
                'meta_og_image' => 'image',
                'meta_robots_tag' => [
                    'type' => 'options',
                    'htmlOptions' => [
                        'class' => 'select_pretty',
                        'style' => 'width:60%',
                        'multiple' => 'multiple',
                    ],
                    'optionList' => $this->getRobotsList(),
                    'hint' => '<p><b>noindex</b> - Страница не будет участвовать в результатах поиска.<br><b>nofollow</b> - Не переходить по ссылкам на странице.<br><b>none</b> - Соответствует директивам noindex, nofollow.<br><b>noarchive</b> - Не показывать ссылку на сохраненную копию в результатах поиска.<br><b>noyaca</b> - Не использовать сформированное автоматически описание.<br><b>all</b> - Соответствует директивам index и follow — разрешено индексировать текст и ссылки на странице.<br></p>',
                ],
                'unset_from_sitemap' => 'boolean',
            ]
        ];
        return $tab ? $fields[$tab] : $fields;
    }

    public function getRelations($tab)
    {
        $relations = [
            'help' => [
                'expertshelp' => [
                    'type' => 'relation',
                    'buttons' => [
                        'add_page' => [
                            'class' => 'success',
                            'name' => 'Создать блок',
                            'url' => Url::toRoute(['/admin/target_audience/faq/create', 'TargetAudiencePageFaq' => ['page_id' => $this->id], 'return_url' => '/admin/' . Yii::$app->controller->module->id . '/' . Yii::$app->controller->id . '/update/?id=' . $this->id, 'anchor' => 'help']),
                        ],
                    ],
                    'search_class' => '\app\modules\target_audience\models\TargetAudiencePageFaqSearch',
                    'title' => 'Редактирование блока',
                ],
            ],
            'homesocial' => [
                'homesocial' => [
                    'type' => 'relation',
                    'buttons' => [
                        'add_page' => [
                            'class' => 'success',
                            'name' => 'Создать блок',
                            'url' => Url::toRoute(['/admin/homesocial/default/create', 'Homesocial' => ['page_id' => $this->id], 'return_url' => '/admin/' . Yii::$app->controller->module->id . '/' . Yii::$app->controller->id . '/update/?id=' . $this->id, 'anchor' => 'homesocial']),
                        ],
                    ],
                    'search_class' => '\app\modules\homesocial\models\HomesocialSearch',
                    'title' => 'Редактирование блоков',
                ],
            ],
        ];
        return $tab ? $relations[$tab] : $relations;
    }

    public function getHomesocial()
    {
        return $this->hasMany(\app\modules\homesocial\models\Homesocial::class, ['page_id' => 'id'])->andWhere(['visible' => 1])->orderBy(['order' => SORT_ASC]);
    }

    public function getExpertshelp()
    {
        return $this->hasMany(\app\modules\target_audience\models\TargetAudiencePageFaq::class, ['page_id' => 'id'])->andWhere(['visible' => 1])->orderBy(['order' => SORT_ASC]);
    }

    public function getPlus_image()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => Home::class, 'keeper_field' => 'plus_image']);
    }

    public function getStartwork_banner1_image()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => Home::class, 'keeper_field' => 'startwork_banner1_image']);
    }

    public function getStartwork_banner2_image()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => Home::class, 'keeper_field' => 'startwork_banner2_image']);
    }

    public function getSliderimage()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => self::class, 'keeper_field' => 'sliderimage']);
    }

    public function getSlidermobileimage()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => self::class, 'keeper_field' => 'slidermobileimage']);
    }

    public function getExpertsWorking()
    {
        $expertspage = new ExpertsPage();
        $items = $expertspage->getExperts();
        return $items;
    }

    public function getExpertsClickable()
    {
        $services_ids = ArrayHelper::map(Service::find()->select(['id', 'user_id', 'visible'])->where(['visible' => 1])->distinct()->all(), 'user_id', 'user_id');
        $events_ids = ArrayHelper::map(Events::findVisibleForCatalog()->distinct()->all(), 'author_id', 'author_id');
        $query = UserAR::find()->visible(['expert']);
        $limit = 4 * 4;
        // выбираем только с картинками
        $query->leftJoin('profile', 'profile.user_id = user.id');
        $query->leftJoin('file_store', 'file_store.keeper_id = profile.id');
        $query->andWhere(['file_store.keeper_class' => 'app\modules\users\models\Profile', 'file_store.keeper_field' => 'image']);


        $query->andWhere(['OR',
            ['IN', 'user.id', $services_ids],
            ['IN', 'user.id', $events_ids],
        ]);

        $query->orderBy('RAND()');
        $query->limit($limit);

        return $query->all();
    }
}
