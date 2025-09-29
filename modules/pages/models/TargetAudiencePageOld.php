<?php
/**
 * @modelDescr Страница не используется - проверить и удалить
 */

namespace app\modules\pages\models;

use app\modules\admin\behaviors\SaveFilesRelation;
use app\modules\admin\behaviors\SaveOneVarRelation;
use app\modules\admin\behaviors\Serialize;
use app\modules\admin\components\FilestoreModel;
use app\modules\service_type\models\ServiceType;
use yii\helpers\ArrayHelper;

class TargetAudiencePageOld extends Page
{
    public static $name_for_list = "Целевые аудитории(старый шаблон)";
    public static $count_for_list = 1;
    public $view = 'target_audience_old';
    public $action_id = 'pages/pages/target-audience-old';
    public $image_loader;
    public $image_mobile_loader;

    public $popular;
    public $subtitle;

    public function getFieldTabs()
    {
        return [
            'main' => 'Основное',
            'popular' => 'Популярные виды услуг',
            'seo' => 'SEO',
        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'image' => 'Изображение',
            'image_loader' => 'Изображение',
            'image_mobile' => 'Изображение для мобильных',
            'image_mobile_loader' => 'Изображение для мобильных',
            'subtitle' => 'Подзаголовок',
            'content_seo' => 'SEO-текст',
            'popular' => 'Популярные виды услуг',
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
                        'subtitle',
                        'popular',
                    ],
                ],
            ],
            'serialize' => [
                'class' => Serialize::class,
                'relations' => [
                    'popular',
                    'content_seo',
                ],
            ],
            'saveFiles' => [
                'class' => SaveFilesRelation::class,
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
                ]
            ],
        ]);
    }

    public function getFields($tab)
    {
        $fields = [
            'main' => [
                'name' => 'text',
                'subtitle' => 'text',
                'url' => 'text',
                'content' => 'wysiwyg',
                'content_seo' => 'wysiwyg',
                'image' => 'image',
                'image_mobile' => 'image',
                'order' => 'integer',
                'visible' => 'boolean',
            ],
            'popular' => [
                // поле
                'popular' => [
                    // тип поля
                    'type' => 'multifields',
                    // перечень полей мультиполя
                    'fields' => [
                        'type' => [
                            'visible_field' => true,
                            'type' => 'options',
                            'name' => 'Тип услуги',
                            'options_list' => $this->getPopularList(),
                            'htmlClass' => 'select_pretty',
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

    // сохранение/удаление/валидация

    public function getPopularList()
    {
        return ArrayHelper::map(ServiceType::find()->all(), 'id', 'name');
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['start_module'], 'default', 'value' => 'target_audience'],
            [['popular', 'subtitle', 'content_seo'], 'safe'],
        ]);
    }

    public function getPopular()
    {
        return ServiceType::find()->where(['IN', 'id', array_keys(ArrayHelper::map($this->popular, 'type', 'type'))])->all();
    }

    public function getImage()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => TargetAudiencePageOld::class, 'keeper_field' => 'image']);
    }

    public function getImage_mobile()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => TargetAudiencePageOld::class, 'keeper_field' => 'image_mobile']);
    }
}
