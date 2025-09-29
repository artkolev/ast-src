<?php
/**
 * @modelDescr Страница с дизайном "Каталог услуг"
 */

namespace app\modules\pages\models;

use app\modules\admin\behaviors\ClearRelation;
use app\modules\admin\behaviors\SaveFilesRelation;
use app\modules\admin\behaviors\SaveOneVarRelation;
use app\modules\admin\behaviors\Serialize;
use app\modules\admin\components\FilestoreModel;
use app\modules\service_type\models\ServiceType;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class TargetAudiencePage extends Page
{
    public static $name_for_list = "Целевые аудитории";
    public static $count_for_list = 1;
    public $view = 'target_audience';
    public $action_id = 'pages/pages/target-audience';
    public $image_loader;
    public $image_mobile_loader;

    public $popular;
    public $slider;
    public $anchors;
    public $faq_list;
    public $card_list;
    public $faqimage_loader;
    public $cardsimage_loader;
    public $sliderimage_loader;
    public $slidermobileimage_loader;

    public $block1_title;
    public $block1_left_button_title;
    public $block1_left_button_url;
    public $block1_right_button_title;
    public $block1_right_button_url;
    public $block2_title;
    public $block2_text;
    public $block3_title;
    public $block4_title;
    public $block5_title;
    public $block5_text;

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'image' => 'Изображение',
            'image_loader' => 'Изображение',
            'image_mobile' => 'Изображение для мобильных',
            'image_mobile_loader' => 'Изображение для мобильных',
            'popular' => 'Популярные виды услуг',
            'problems' => 'Блок Проблемы',
            'faq_list' => 'Блок Эксперты помогают',
            'card_list' => 'Блок с карточками',
            'anchors' => 'Якоря',
            'block1_title' => 'Заголовок первого блока',
            'block1_left_button_title' => 'Текст левой кнопки',
            'block1_left_button_url' => 'Ссылка на левой кнопке',
            'block1_right_button_title' => 'Текст правой кнопки',
            'block1_right_button_url' => 'Ссылка на правой кнопке',
            'block2_title' => 'Заголовок блока "В чем мы можем быть полезны"',
            'block2_text' => 'Текст блока "В чем мы можем быть полезны"',
            'block3_title' => 'Заголовок третьего блока',
            'block4_title' => 'Заголовок четвертого блока',
            'block5_title' => 'Заголовок блока "У меня есть запрос"',
            'block5_text' => 'Текст блока "У меня есть запрос"',
            'slider' => 'Слайды'
        ]);
    }

    public function behaviors()
    {
        $parent_behaviors = parent::behaviors();
        return array_merge($parent_behaviors, [
            'saveOneVar' => [
                'class' => SaveOneVarRelation::class,
                'relations' => [
                    'single' => [
                        'block1_title',
                        'block1_left_button_title',
                        'block1_left_button_url',
                        'block1_right_button_title',
                        'block1_right_button_url',
                        'block2_title',
                        'block2_text',
                        'block3_title',
                        'block4_title',
                        'block5_title',
                        'block5_text',
                        'popular',
                        'anchors',
                        'faq_list',
                        'card_list',
                        'slider'
                    ],
                ],
            ],
            'serialize' => [
                'class' => Serialize::class,
                'relations' => [
                    'popular',
                    'slider',
                    'anchors',
                    'faq_list',
                    'card_list'
                ],
            ],
            'clearDelete' => [
                'class' => ClearRelation::class,
                'relations' => [
                    'problems',
                ],
            ],
            'saveFiles' => [
                'class' => SaveFilesRelation::class,
                'file_path' => 'files/upload/target_audience_page/',
                'relations' => [
                    'image' => [
                        'type' => 'single', // multiple
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, jpeg, png',
                            'maxSize' => 2 * 1024 * 1024, // 3Мб
                        ],
                        'main' => [
                            'width' => 1920,
                            'height' => 360,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'image_mobile' => [
                        'type' => 'single', // multiple
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, jpeg, png',
                            'maxSize' => 2 * 1024 * 1024, // 3Мб
                        ],
                        'main' => [
                            'width' => 320,
                            'height' => 160,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'faqimage' => [
                        'type' => 'multirow', // multiple
                        'multifield' => 'faq_list',
                        'multifield_fieldname' => 'image',
                        'multifield_type' => 'single',
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, png, jpeg, svg',
                            'maxSize' => 1024 * 1024, // 1Мб
                            'minHeight' => 29,
                            'minWidth' => 29,
                        ],
                        'main' => [
                            'width' => 29,
                            'height' => 29,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'cardsimage' => [
                        'type' => 'multirow', // multiple
                        'multifield' => 'card_list',
                        'multifield_fieldname' => 'image',
                        'multifield_type' => 'single',
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, png, jpeg, svg',
                            'maxSize' => 1024 * 1024, // 1Мб
                            'minHeight' => 195,
                            'minWidth' => 320,
                        ],
                        'main' => [
                            'width' => 320,
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
                ]
            ],
        ]);
    }

    public function getFieldTabs()
    {
        return [
            'main' => 'Основное',
            'anchors' => 'Якоря',
            'faq' => 'Блок Эксперты помогают',
            'popular' => 'Популярные виды услуг',
            'slider' => 'Слайдер',
            'titles' => 'Заголовки блоков',
            'cards' => 'Блок с карточками',
            'seo' => 'SEO',
        ];
    }

    public function getRelationTabs()
    {
        return [
            'problems' => 'Блок Проблемы',
        ];
    }

    public function getFields($tab)
    {
        $fields = [
            'main' => [
                'name' => 'text',
                'block1_title' => 'text',
                'block1_left_button_title' => 'text',
                'block1_left_button_url' => 'text',
                'block1_right_button_title' => 'text',
                'block1_right_button_url' => 'text',
                'url' => 'text',
                'content' => 'wysiwyg',
                // 'image' => 'image',
                // 'image_mobile' => 'image',
                'order' => 'integer',
                'visible' => 'boolean',
            ],
            'anchors' => [
                'anchors' => [
                    'type' => 'multifields',
                    // перечень полей мультиполя
                    'fields' => [
                        'name' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Название',
                        ],
                        'link' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Url-адрес',
                        ],
                    ],
                ],
            ],
            'faq' => [
                'block3_title' => 'text',
                'faq_list' => [
                    'type' => 'multifields',
                    'fields' => [
                        'name' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Название',
                        ],
                        'description' => [
                            'visible_field' => true,
                            'type' => 'textarea',
                            'name' => 'Описание',
                        ],
                        'link' => [
                            'type' => 'text',
                            'name' => 'Url-адрес',
                        ],
                        'image' => [
                            'type' => 'image',
                            'source_field' => 'faqimage',
                            'name' => 'Изображение',
                        ],
                    ],
                ],
            ],
            'popular' => [
                'block4_title' => 'text',
                // поле
                'popular' => [
                    // тип поля
                    'type' => 'multifields',
                    // перечень полей мультиполя
                    'fields' => [
                        'popular' => [
                            'visible_field' => true,
                            'type' => 'options',
                            'name' => 'Тип услуги',
                            'options_list' => $this->getPopularList(),
                            'htmlClass' => 'select_pretty',
                        ],
                    ],
                ],
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
            'titles' => [
                'block2_title' => 'text',
                'block2_text' => 'text',
                'block5_title' => 'text',
                'block5_text' => 'text',
            ],
            'cards' => [
                'card_list' => [
                    'type' => 'multifields',
                    'fields' => [
                        'name' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Название',
                        ],
                        'link' => [
                            'type' => 'text',
                            'name' => 'Url-адрес',
                        ],
                        'link_title' => [
                            'type' => 'text',
                            'name' => 'Текст на кнопке',
                        ],
                        'image' => [
                            'type' => 'image',
                            'source_field' => 'cardsimage',
                            'name' => 'Изображение',
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
            ],
        ];
        return $tab ? $fields[$tab] : $fields;
    }

    public function getPopularList()
    {
        return ArrayHelper::map(ServiceType::find()->all(), 'id', 'name');
    }

    // сохранение/удаление/валидация

    public function getRelations($tab)
    {
        $relations = [
            'problems' => [
                'problems' => [
                    'type' => 'relation',
                    'buttons' => [
                        'add_page' => [
                            'class' => 'success',
                            'name' => 'Создать проблему',
                            'url' => Url::toRoute(['/admin/target_audience/problems/create', 'TargetAudiencePageProblem' => ['page_id' => $this->id], 'return_url' => '/admin/' . Yii::$app->controller->module->id . '/' . Yii::$app->controller->id . '/update/?id=' . $this->id, 'anchor' => 'problems']),
                        ],
                    ],
                    'search_class' => '\app\modules\target_audience\models\TargetAudiencePageProblemSearch',
                    'title' => 'Редактирование проблемы',
                ],
            ],
        ];
        return $tab ? $relations[$tab] : $relations;
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['start_module'], 'default', 'value' => 'target_audience'],
            [['popular', 'faq_list', 'card_list', 'problems', 'anchors', 'slider'], 'safe'],
            [['block1_title', 'block1_left_button_title', 'block1_left_button_url', 'block1_right_button_title', 'block1_right_button_url', 'block2_title', 'block2_text', 'block3_title', 'block4_title', 'block5_title', 'block5_text',], 'safe'],
        ]);
    }

    public function getPopularTypes()
    {
        $pop = $this->popular;
        foreach ($pop as $key => $value) {
            if ($value['visible'] != 1) {
                unset($pop[$key]);
            }
        }
        if (!empty($pop)) {
            usort($pop, function ($a, $b) {
                return $a['order'] <=> $b['order'];
            });
        }
        $keys = implode(',', array_keys(ArrayHelper::map($pop, 'popular', 'popular')));
        $ret = ServiceType::find()
            ->where(['visible' => 1])
            ->andWhere(['IN', 'id', array_keys(ArrayHelper::map($pop, 'popular', 'popular'))]);
        if (!empty($keys)) {
            $ret->orderBy([new \yii\db\Expression('FIELD (id, ' . $keys . ')')]);
        }
        return $ret->all();
    }

    public function getProblems()
    {
        return $this->hasMany(\app\modules\target_audience\models\TargetAudiencePageProblem::class, ['page_id' => 'id'])->andWhere(['visible' => 1])->orderBy(['order' => SORT_ASC]);
    }

    public function getFaqimage()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => self::class, 'keeper_field' => 'faqimage']);
    }

    public function getCardsimage()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => self::class, 'keeper_field' => 'cardsimage']);
    }

    public function getSliderimage()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => self::class, 'keeper_field' => 'sliderimage']);
    }

    public function getSlidermobileimage()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => self::class, 'keeper_field' => 'slidermobileimage']);
    }

    public function getImage()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => TargetAudiencePage::class, 'keeper_field' => 'image']);
    }

    public function getImage_mobile()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => TargetAudiencePage::class, 'keeper_field' => 'image_mobile']);
    }
}
