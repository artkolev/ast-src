<?php

/**
 * @modelDescr Страница-каталог проектов Академии
 */

namespace app\modules\pages\models;

use app\modules\admin\behaviors\SaveFilesRelation;
use app\modules\admin\behaviors\SaveOneVarRelation;
use app\modules\admin\behaviors\Serialize;
use app\modules\admin\components\FilestoreModel;

class AdventCalendar extends Page
{
    public static $name_for_list = "Адвент-календарь";
    public static $count_for_list = 1;
    public $view = 'advent_calendar';
    public $action_id = 'pages/pages/advent-calendar';
    public $cards;
    public $title;
    public $card_banner_loader;
    public $card_promo_loader;

    public $banner_image_loader;
    public $banner_image_mobile_loader;
    public $title_image_loader;
    public $modal_image_loader;
    public $meta_og_image_loader;

    public function behaviors()
    {
        $parent_behaviors = parent::behaviors();
        return array_merge($parent_behaviors, [
            'saveOneVar' => [
                'class' => SaveOneVarRelation::class,
                'relations' => [
                    'single' => [
                        'cards',
                        'title',
                    ],
                ],
            ],
            'serialize' => [
                'class' => Serialize::class,
                'relations' => [
                    'cards',
                ],
            ],
            'saveFiles' => [
                'class' => SaveFilesRelation::class,
                'file_path' => 'files/upload/advent/',
                'relations' => [
                    'meta_og_image' => [
                        'default' => 'img/opengraph_default.jpg',
                        'type' => 'single', // multiple
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, png, jpeg',
                            'maxSize' => 1024 * 1024, // 1Мб
                            'minHeight' => 200,
                            'minWidth' => 300,
                        ],
                        'main' => [
                            'width' => 320,
                            'height' => 220,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'banner_image' => [
                        'type' => 'single', // multiple
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, png, jpeg, svg',
                            'maxSize' => 3 * 1024 * 1024, // 3Мб
                        ],
                        'main' => [
                            'width' => 1335,
                            'height' => 490,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'modal_image' => [
                        'type' => 'single', // multiple
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, png, jpeg, svg',
                            'maxSize' => 3 * 1024 * 1024, // 3Мб
                        ],
                        'main' => [
                            'width' => 764,
                            'height' => 1080,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'banner_image_mobile' => [
                        'type' => 'single', // multiple
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, png, jpeg, svg',
                            'maxSize' => 3 * 1024 * 1024, // 3Мб
                        ],
                        'main' => [
                            'width' => 431,
                            'height' => 281,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'title_image' => [
                        'type' => 'single', // multiple
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, png, jpeg, svg',
                            'maxSize' => 3 * 1024 * 1024, // 3Мб
                        ],
                        'main' => [
                            'width' => 1335,
                            'height' => 490,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'card_banner' => [
                        'type' => 'multirow', // multiple
                        'multifield' => 'cards',
                        'multifield_fieldname' => 'image_banner',
                        'multifield_type' => 'single',
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, jpeg, png, gif, svg',
                            'maxSize' => 1 * 1024 * 1024, // 1Мб
                        ],
                        'main' => [
                            'width' => 800,
                            'height' => 600,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'card_promo' => [
                        'type' => 'multirow', // multiple
                        'multifield' => 'cards',
                        'multifield_fieldname' => 'image_promo',
                        'multifield_type' => 'single',
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, jpeg, png, gif, svg',
                            'maxSize' => 1 * 1024 * 1024, // 1Мб
                        ],
                        'main' => [
                            'width' => 800,
                            'height' => 600,
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
            'cards' => 'Карточки',
            'banner_image' => 'Изображение баннера',
            'banner_image_loader' => 'Изображение баннера',
            'banner_image_mobile' => 'Изображение баннера (мобильная версия)',
            'banner_image_mobile_loader' => 'Изображение баннера (мобильная версия)',
            'modal_image' => 'Изображение в модалке для 31 декабря',
            'modal_image_loader' => 'Изображение в модалке для 31 декабря',
            'title_image' => 'Изображение заголовка',
            'title_image_loader' => 'Изображение заголовка',
            'title' => 'Заголовок',
        ]);
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['cards', 'title'], 'safe'],
        ]);
    }

    public function getFieldTabs()
    {
        return [
            'main' => 'Основное',
            'cards' => 'Карточки',
            'seo' => 'SEO',
        ];
    }

    public function getFields($tab)
    {
        $fields = [
            'main' => [
                'name' => 'text',
                'url' => 'text',
                'content' => 'wysiwyg',
                'title' => 'text',
                'banner_image' => 'image',
                'banner_image_mobile' => 'image',
                'modal_image' => 'image',
                'title_image' => 'image',
                'order' => 'integer',
                'visible' => 'boolean',
            ],
            'cards' => [
                'cards' => [
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
                            'name' => 'Ссылка',
                        ],
                        'button_text' => [
                            'type' => 'text',
                            'name' => 'Текст на кнопке',
                        ],
                        'image_promo' => [
                            'type' => 'image',
                            'source_field' => 'card_promo',
                            'name' => 'Изображение обложка',
                        ],
                        'image_banner' => [
                            'type' => 'image',
                            'source_field' => 'card_banner',
                            'name' => 'Изображение оборот',
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

    public function getCard_banner()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => self::class, 'keeper_field' => 'card_banner']);
    }

    public function getCard_promo()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => self::class, 'keeper_field' => 'card_promo']);
    }

    public function getModal_image()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => self::class, 'keeper_field' => 'modal_image']);
    }

    public function getBanner_image()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => self::class, 'keeper_field' => 'banner_image']);
    }

    public function getBanner_image_mobile()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => self::class, 'keeper_field' => 'banner_image_mobile']);
    }

    public function getTitle_image()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => self::class, 'keeper_field' => 'title_image']);
    }

    public function getMeta_og_image()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => self::class, 'keeper_field' => 'meta_og_image']);
    }
}
