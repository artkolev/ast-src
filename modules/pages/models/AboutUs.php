<?php

namespace app\modules\pages\models;

use app\modules\admin\behaviors\SaveFilesRelation;
use app\modules\admin\behaviors\SaveOneVarRelation;
use app\modules\admin\behaviors\Serialize;
use app\modules\admin\components\FilestoreModel;
use app\modules\users\models\UserAR;
use yii\helpers\ArrayHelper;

/**
 * @modelDescr Страница сайта с дизайном - Миссии академии
 *
 * @property array $header_missions
 * @property array $header_missions_slider
 * @property string $ast_today_title
 * @property string $ast_experts_title
 * @property string $ast_experts_card_type
 * @property array $ast_experts_cards
 * @property string $ast_council_title
 * @property string $ast_council_text
 * @property string $ast_council_card_type
 * @property array $ast_council_cards
 * @property string $ast_members_title
 * @property array $ast_today_cards
 * @property array $ast_members_cards
 * @property array $key_metric_section
 * @property string $after_key_metric_text
 * @property string $banner_title
 * @property string $banner_text
 * @property array $banner_buttons
 * @property string $banner2_title
 * @property string $banner2_text
 * @property array $banner2_buttons
 * @property string $partners_title
 * @property string $partners_text
 * @property array $partners_buttons
 * @property string $partners_slider_title
 * @property array $partners_slider_cards
 * @property string $ast_projects_title
 * @property array $ast_projects_cards
 *
 * @property UserAR[] $astExpertsCards
 * @property UserAR[] $astCouncilsCards
 */
class AboutUs extends Page
{
    public const EXPERT_CARD_TYPE_STATIC = 'static';
    public const EXPERT_CARD_TYPE_SLIDER = 'slider';
    public const EXPERT_CARD_TYPES = [
        self::EXPERT_CARD_TYPE_STATIC => 'Карточки',
        self::EXPERT_CARD_TYPE_SLIDER => 'Слайдер в одну строку',
    ];
    public const FILE_TYPE_IMAGE = 'image';
    public const FILE_TYPE_VIDEO = 'video';
    public const FILE_TYPES = [
        self::FILE_TYPE_IMAGE => 'Картинка',
        self::FILE_TYPE_VIDEO => 'Видео',
    ];
    public static $name_for_list = "страницу Миссии академии";
    public $view = 'about_us';
    public $action_id = 'pages/pages/about-us';
    public $ast_today_title;
    public $ast_members_title;
    public $after_key_metric_text;
    public $missionslider_loader;
    public $asttodaycards_loader;
    public $astmemberscards_loader;
    public $partnersslidercards_loader;
    public $astprojectscards_loader;
    public $astprojectscardstablet_loader;
    public $astprojectscardsmobile_loader;
    public $astprojectscardsmobilemin_loader;
    public $astprojectscardsfile_loader;
    public $header_missions;
    public $header_missions_slider;
    public $ast_today_cards;
    public $ast_experts_title;
    public $ast_experts_card_type;
    public $ast_experts_cards;
    public $ast_council_title;
    public $ast_council_text;
    public $ast_council_card_type;
    public $ast_council_cards;
    public $banner_title;
    public $banner_text;
    public $banner_buttons;
    public $banner2_title;
    public $banner2_text;
    public $banner2_buttons;
    public $partners_title;
    public $partners_text;
    public $partners_buttons;
    public $partners_slider_title;
    public $partners_slider_cards;
    public $ast_projects_title;
    public $ast_projects_cards;
    public $ast_members_cards;
    public $key_metric_section;

    public function behaviors()
    {
        $parent_behaviors = parent::behaviors();
        return array_merge($parent_behaviors, [
            'saveFiles' => [
                'class' => SaveFilesRelation::class,
                'relations' => [
                    'missionslider' => [
                        'type' => 'multirow', // multiple
                        'multifield' => 'header_missions_slider',
                        'multifield_fieldname' => 'person_image',
                        'multifield_type' => 'single',
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, jpeg, png',
                            'maxSize' => 2 * 1024 * 1024, // 3Мб
                            'minWidth' => 1920,
                            'minHeight' => 1080,
                        ],
                        'main' => [
                            'width' => 1920,
                            'height' => 1080,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'asttodaycards' => [
                        'type' => 'multirow', // multiple
                        'multifield' => 'ast_today_cards',
                        'multifield_fieldname' => 'card_image',
                        'multifield_type' => 'single',
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, jpeg, png, svg',
                            'maxSize' => 2 * 1024 * 1024, // 3Мб
                            'minWidth' => 500,
                            'minHeight' => 500,
                        ],
                        'main' => [
                            'width' => 500,
                            'height' => 500,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'astmemberscards' => [
                        'type' => 'multirow', // multiple
                        'multifield' => 'ast_members_cards',
                        'multifield_fieldname' => 'card_image',
                        'multifield_type' => 'single',
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, jpeg, png, svg',
                            'maxSize' => 2 * 1024 * 1024, // 3Мб
                            'minWidth' => 500,
                            'minHeight' => 500,
                        ],
                        'main' => [
                            'width' => 500,
                            'height' => 500,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'partnersslidercards' => [
                        'type' => 'multirow', // multiple
                        'multifield' => 'partners_slider_cards',
                        'multifield_fieldname' => 'card_image',
                        'multifield_type' => 'single',
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, jpeg, png, svg',
                            'maxSize' => 2 * 1024 * 1024, // 3Мб
                            'minWidth' => 211,
                            'minHeight' => 150,
                        ],
                        'main' => [
                            'width' => 211,
                            'height' => 150,
                            'quality' => 90,
                            'mode' => 'inset',
                        ],
                    ],
                    'astprojectscards' => [
                        'type' => 'multirow', // multiple
                        'multifield' => 'ast_projects_cards',
                        'multifield_fieldname' => 'card_image',
                        'multifield_type' => 'single',
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, jpeg, png',
                            'maxSize' => 2 * 1024 * 1024, // 3Мб
                            'minWidth' => 1500,
                            'minHeight' => 600,
                        ],
                        'main' => [
                            'width' => 1500,
                            'height' => 600,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'astprojectscardstablet' => [
                        'type' => 'multirow', // multiple
                        'multifield' => 'ast_projects_cards',
                        'multifield_fieldname' => 'card_image_tablet',
                        'multifield_type' => 'single',
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, jpeg, png',
                            'maxSize' => 2 * 1024 * 1024, // 3Мб
                            'minWidth' => 1024,
                            'minHeight' => 450,
                        ],
                        'main' => [
                            'width' => 1024,
                            'height' => 450,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'astprojectscardsmobile' => [
                        'type' => 'multirow', // multiple
                        'multifield' => 'ast_projects_cards',
                        'multifield_fieldname' => 'card_image_mobile',
                        'multifield_type' => 'single',
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, jpeg, png',
                            'maxSize' => 2 * 1024 * 1024, // 3Мб
                            'minWidth' => 630,
                            'minHeight' => 400,
                        ],
                        'main' => [
                            'width' => 630,
                            'height' => 400,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'astprojectscardsmobilemin' => [
                        'type' => 'multirow', // multiple
                        'multifield' => 'ast_projects_cards',
                        'multifield_fieldname' => 'card_image_mobile_min',
                        'multifield_type' => 'single',
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, jpeg, png',
                            'maxSize' => 2 * 1024 * 1024, // 3Мб
                            'minWidth' => 300,
                            'minHeight' => 350,
                        ],
                        'main' => [
                            'width' => 300,
                            'height' => 350,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'astprojectscardsfile' => [
                        'type' => 'multirow', // multiple
                        'multifield' => 'ast_projects_cards',
                        'multifield_fieldname' => 'card_video',
                        'multifield_type' => 'single',
                        'validator' => 'file',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'mp4',
                            'maxSize' => 2 * 1024 * 1024, // 3Мб
                        ],
                    ],
                ]
            ],
            'saveOneVar' => [
                'class' => SaveOneVarRelation::class,
                'relations' => [
                    'single' => [
                        'header_missions',
                        'header_missions_slider',
                        'ast_today_cards',
                        'ast_members_cards',
                        'ast_today_title',

                        'ast_experts_title',
                        'ast_experts_cards',
                        'ast_experts_card_type',
                        'ast_council_title',
                        'ast_council_text',
                        'ast_council_cards',
                        'ast_council_card_type',
                        'ast_members_title',
                        'banner_title',
                        'banner_text',
                        'banner_buttons',
                        'banner2_title',
                        'banner2_text',
                        'banner2_buttons',
                        'partners_title',
                        'partners_text',
                        'partners_buttons',
                        'partners_slider_title',
                        'partners_slider_cards',
                        'ast_projects_title',
                        'ast_projects_cards',

                        'key_metric_section',
                        'after_key_metric_text',
                    ],
                ],
            ],
            1 => [
                'class' => Serialize::class,
                'relations' => [
                    'header_missions',
                    'header_missions_slider',
                    'ast_today_cards',
                    'ast_experts_cards',
                    'ast_members_cards',
                    'ast_council_cards',
                    'key_metric_section',
                    'banner_buttons',
                    'banner2_buttons',
                    'partners_buttons',
                    'partners_slider_cards',
                    'ast_projects_cards',
                ],
            ],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'content' => 'Текст блока миссий',
            'header_missions' => 'Список миссий',
            'header_missions_slider' => 'Слайдер',
            'ast_today_cards' => 'Карточки элементов',
            'partners_slider_cards' => 'Карточки элементов',
            'ast_experts_cards' => 'Карточки элементов',
            'ast_council_cards' => 'Карточки элементов',
            'ast_members_cards' => 'Карточки элементов',
            'ast_projects_cards' => 'Карточки элементов',

            'key_metric_section' => 'Ключевые показатели',
            'after_key_metric_text' => 'Текст под ключевыми показателями',

            'ast_today_title' => 'Название секции',
            'ast_members_title' => 'Название секции',
            'ast_experts_title' => 'Название секции',
            'ast_council_title' => 'Название секции',
            'ast_council_text' => 'Текст',
            'ast_experts_card_type' => 'Тип отображения',
            'ast_council_card_type' => 'Тип отображения',
            'banner_title' => 'Название секции',
            'banner_text' => 'Текст',
            'banner_buttons' => 'Кнопки баннера',
            'banner2_title' => 'Название секции',
            'banner2_text' => 'Текст',
            'banner2_buttons' => 'Кнопки баннера',
            'partners_title' => 'Название секции',
            'ast_projects_title' => 'Название секции',
            'partners_slider_title' => 'Название секции',
            'partners_text' => 'Текст',
            'partners_buttons' => 'Кнопки баннера',

            'image' => 'Изображение',
            'image_loader' => 'Изображение',

        ]);
    }

    // сохранение/удаление/валидация
    public function rules()
    {
        return array_merge(parent::rules(), [
            [[
                'header_missions',
                'header_missions_slider',
                'ast_today_cards',

                'ast_members_cards',

                'ast_today_title',
                'ast_members_title',
                'ast_experts_title',
                'ast_experts_card_type',
                'ast_experts_cards',
                'ast_council_title',
                'ast_council_text',
                'ast_council_card_type',
                'ast_council_cards',
                'banner_title',
                'banner_text',
                'banner_buttons',
                'banner2_title',
                'banner2_text',
                'banner2_buttons',
                'partners_title',
                'partners_text',
                'partners_buttons',
                'partners_slider_title',
                'partners_slider_cards',
                'ast_projects_title',
                'ast_projects_cards',

                'key_metric_section',
                'after_key_metric_text'
            ], 'safe'],
        ]);
    }

    public function getFieldTabs()
    {
        return [
            'main' => 'Основное',
            'key_metric_section_tab' => 'Ключевые показатели',
            'banner2_section_tab' => 'Блок с баннером',
            'ast_today_section_tab' => 'Академия сегодня',
            'ast_experts_section_tab' => 'Почетные члены',
            'ast_council_section_tab' => 'Ученый совет',
            'ast_members_section_tab' => 'Участники Академии',
            'banner_section_tab' => 'Присоединяйтесь к Академии',
            'partners_section_tab' => 'Партнерство с Академией',
            'partners_slider_section_tab' => 'Партнеры слайдер',
            'ast_projects_section_tab' => 'Проекты академии',
            'seo' => 'SEO',
        ];
    }

    public function getRelationTabs()
    {
        return [];
    }

    public function getFields($tab)
    {
        $fields = [
            'main' => [
                'name' => 'text',
                'url' => 'text',
                'content' => 'textarea',
                'header_missions' => [
                    'type' => 'multifields',
                    'fields' => [
                        'name' => [
                            'visible_field' => true,
                            'type' => 'textarea',
                            'name' => 'Описание миссии',
                        ],
                    ],
                ],
                'header_missions_slider' => [
                    'type' => 'multifields',
                    'fields' => [
                        'person' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Имя Фамилия',
                        ],
                        'person_post' => [
                            'visible_field' => false,
                            'type' => 'text',
                            'name' => 'Должность',
                        ],
                        'person_link' => [
                            'visible_field' => false,
                            'type' => 'text',
                            'name' => 'Ссылка на профиль',
                        ],
                        'person_text' => [
                            'visible_field' => false,
                            'type' => 'textarea',
                            'name' => 'Текст в карточке',
                        ],
                        'person_image' => [
                            'type' => 'image',
                            'source_field' => 'missionslider',
                            'name' => 'Изображение',
                        ],
                    ],
                    'hint' => 'Для добавления/изменения элемента используте иконку <b>карандаша</b><br>Рекомендуемый размер изображения: <b>1920 * 1080</b>',
                ],
                'order' => 'integer',
                'visible' => 'boolean',
                'parent_id' => [
                    'type' => 'options',
                    'optionList' => $this->getSectionList(),
                ],
            ],
            'key_metric_section_tab' => [
                'key_metric_section' => [
                    'type' => 'multifields',
                    'fields' => [
                        'value' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Значение',
                        ],
                        'name' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Текст',
                        ],
                    ],
                    'hint' => '<b>Максимум 4 показателя</b>',
                ],
                'after_key_metric_text' => 'wysiwyg',
            ],
            'banner2_section_tab' => [
                'banner2_title' => 'text',
                'banner2_text' => 'textarea',
                'banner2_buttons' => [
                    'type' => 'multifields',
                    'fields' => [
                        'name' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Текст кнопки',
                        ],
                        'link' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Url кнопки',
                        ],
                    ],
                ],
            ],
            'ast_today_section_tab' => [
                'ast_today_title' => 'text',
                'ast_today_cards' => [
                    'type' => 'multifields',
                    'fields' => [
                        'card_name' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Заголовок',
                        ],
                        'card_text' => [
                            'visible_field' => false,
                            'type' => 'textarea',
                            'name' => 'Описание',
                        ],
                        'card_link' => [
                            'visible_field' => false,
                            'type' => 'text',
                            'name' => 'Url',
                        ],
                        'card_image' => [
                            'type' => 'image',
                            'source_field' => 'asttodaycards',
                            'name' => 'Изображение',
                        ],
                    ],
                    'hint' => 'Для добавления/изменения элемента используте иконку <b>карандаша</b><br>Рекомендуемый размер изображения: <b>500 * 500</b>',
                ],
            ],
            'ast_experts_section_tab' => [
                'ast_experts_title' => 'text',
                'ast_experts_card_type' => [
                    'type' => 'options',
                    'optionList' => self::EXPERT_CARD_TYPES,
                ],
                'ast_experts_cards' => [
                    'type' => 'multifields',
                    'fields' => [
                        'expert' => [
                            'visible_field' => true,
                            'type' => 'options',
                            'name' => 'Выберите вариант',
                            'htmlClass' => 'select_pretty',
                            'options_list' => $this->astExpertList(),
                        ],
                    ],
                ],
            ],
            'ast_council_section_tab' => [
                'ast_council_title' => 'text',
                'ast_council_text' => 'wysiwyg',
                'ast_council_card_type' => [
                    'type' => 'options',
                    'optionList' => self::EXPERT_CARD_TYPES,
                ],
                'ast_council_cards' => [
                    'type' => 'multifields',
                    'fields' => [
                        'expert' => [
                            'visible_field' => true,
                            'type' => 'options',
                            'name' => 'Выберите вариант',
                            'htmlClass' => 'select_pretty',
                            'options_list' => $this->astExpertList(),
                        ],
                    ],
                ],
            ],
            'ast_members_section_tab' => [
                'ast_members_title' => 'text',
                'ast_members_cards' => [
                    'type' => 'multifields',
                    'fields' => [
                        'card_name' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Заголовок',
                        ],
                        'card_text' => [
                            'visible_field' => false,
                            'type' => 'textarea',
                            'name' => 'Описание',
                        ],
                        'card_link' => [
                            'visible_field' => false,
                            'type' => 'text',
                            'name' => 'Url',
                        ],
                        'card_image' => [
                            'type' => 'image',
                            'source_field' => 'astmemberscards',
                            'name' => 'Изображение',
                        ],
                    ],
                    'hint' => 'Для добавления/изменения элемента используте иконку <b>карандаша</b><br>Рекомендуемый размер изображения: <b>500 * 500</b>',
                ],
            ],
            'banner_section_tab' => [
                'banner_title' => 'text',
                'banner_text' => 'textarea',
                'banner_buttons' => [
                    'type' => 'multifields',
                    'fields' => [
                        'name' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Текст кнопки',
                        ],
                        'link' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Url кнопки',
                        ],
                    ],
                ],
            ],
            'partners_section_tab' => [
                'partners_title' => 'text',
                'partners_text' => 'textarea',
                'partners_buttons' => [
                    'type' => 'multifields',
                    'fields' => [
                        'name' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Текст кнопки',
                        ],
                        'link' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Url кнопки',
                        ],
                    ],
                ],
            ],
            'ast_projects_section_tab' => [
                'ast_projects_title' => 'text',
                'ast_projects_cards' => [
                    'type' => 'multifields',
                    'fields' => [
                        'card_name' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Название слайда',
                        ],
                        'card_link' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Url слайда',
                        ],
                        'card_type' => [
                            'visible_field' => false,
                            'type' => 'options',
                            'name' => 'Тип',
                            'options_list' => AboutUs::FILE_TYPES,
                        ],
                        'card_video' => [
                            'type' => 'file',
                            'source_field' => 'astprojectscardsfile',
                            'name' => 'Видео',
                        ],
                        'card_image' => [
                            'type' => 'image',
                            'source_field' => 'astprojectscards',
                            'name' => 'Изображение для десктопа',
                        ],
                        'card_image_tablet' => [
                            'type' => 'image',
                            'source_field' => 'astprojectscardstablet',
                            'name' => 'Изображение для планшета',
                        ],
                        'card_image_mobile' => [
                            'type' => 'image',
                            'source_field' => 'astprojectscardsmobile',
                            'name' => 'Изображение для мобильных',
                        ],
                        'card_image_mobile_min' => [
                            'type' => 'image',
                            'source_field' => 'astprojectscardsmobilemin',
                            'name' => 'Изображение для мобильных (мини)',
                        ],
                    ],
                    'hint' => 'Для добавления/изменения элемента используте иконку <b>карандаша</b><br>Отображаться будет тот тип который указан в поле <b>Тип</b>',
                ],
            ],
            'partners_slider_section_tab' => [
                'partners_slider_title' => 'text',
                'partners_slider_cards' => [
                    'type' => 'multifields',
                    'fields' => [
                        'card_name' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Название',
                        ],
                        'card_link' => [
                            'visible_field' => false,
                            'type' => 'text',
                            'name' => 'Url',
                        ],
                        'card_image' => [
                            'type' => 'image',
                            'source_field' => 'partnersslidercards',
                            'name' => 'Изображение',
                        ],
                    ],
                    'hint' => 'Для добавления/изменения элемента используте иконку <b>карандаша</b>',
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
            ],
        ];
        return $tab ? $fields[$tab] : $fields;
    }

    public function astExpertList(): array
    {
        $items = (new \yii\db\Query())
            ->select(['user.id as id', "CONCAT(profile.name,' ',profile.surname) as halfname", 'profile.organization_name as organization_name', 'auth_assignment.item_name as role'])
            ->from('user')
            ->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id')
            ->leftJoin('profile', 'profile.user_id = user.id')
            ->innerJoin('user_direction', 'user_direction.user_id = user.id AND user_direction.role = "academ"')
            ->where(['IN', 'auth_assignment.item_name', ['expert']])
            ->andWhere(['status' => UserAR::STATUS_ACTIVE])
            ->orderBy(['profile.surname' => 'asc', 'profile.name' => 'asc'])
            ->all();

        return ArrayHelper::map($items, 'id', 'halfname');
    }

    public function getRelations($tab)
    {
        $relations = [];

        return $tab ? $relations[$tab] : $relations;
    }

    /**
     * @return UserAR[]
     */
    public function getAstExpertsCards(): array
    {
        $expertList = [];
        foreach ($this->ast_experts_cards as $ast_experts_card) {
            if ((bool)$ast_experts_card['visible']) {
                $expertList[$ast_experts_card['expert']] = UserAR::findOne($ast_experts_card['expert']);
            }
        }

        return $expertList;
    }

    /**
     * @return UserAR[]
     */
    public function getAstCouncilsCards(): array
    {
        $expertList = [];
        foreach ($this->ast_council_cards as $ast_council_card) {
            if ((bool)$ast_council_card['visible']) {
                $expertList[$ast_council_card['expert']] = UserAR::findOne($ast_council_card['expert']);
            }
        }

        return $expertList;
    }

    public function getAstmemberscards()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => AboutUs::class, 'keeper_field' => 'astmemberscards']);
    }

    public function getAsttodaycards()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => AboutUs::class, 'keeper_field' => 'asttodaycards']);
    }

    public function getMissionslider()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => AboutUs::class, 'keeper_field' => 'missionslider']);
    }

    public function getPartnersslidercards()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => AboutUs::class, 'keeper_field' => 'partnersslidercards']);
    }

    public function getAstprojectscardsfile()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => AboutUs::class, 'keeper_field' => 'astprojectscardsfile']);
    }

    public function getAstprojectscards()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => AboutUs::class, 'keeper_field' => 'astprojectscards']);
    }

    public function getAstprojectscardstablet()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => AboutUs::class, 'keeper_field' => 'astprojectscardstablet']);
    }

    public function getAstprojectscardsmobile()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => AboutUs::class, 'keeper_field' => 'astprojectscardsmobile']);
    }

    public function getAstprojectscardsmobilemin()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => AboutUs::class, 'keeper_field' => 'astprojectscardsmobilemin']);
    }
}
