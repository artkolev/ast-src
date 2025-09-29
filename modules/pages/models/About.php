<?php
/**
 * @modelDescr Страница сайта с дизайном О компании (государству)
 */

namespace app\modules\pages\models;

use app\modules\admin\behaviors\SaveFilesRelation;
use app\modules\admin\behaviors\SaveOneVarRelation;
use app\modules\admin\behaviors\Serialize;
use app\modules\admin\components\FilestoreModel;

class About extends Page
{
    public static $name_for_list = "страницу О компании";
    public $view = 'about';
    public $action_id = 'pages/pages/about';
    public $academy_title;
    public $about_slider;
    public $about_preims;
    public $about_perk;
    public $about_howto;

    public $presentation_loader;
    public $sliderimage_loader;
    public $perkimage_loader;
    public $howtoimage_loader;

    public function behaviors()
    {
        $parent_behaviors = parent::behaviors();
        return array_merge($parent_behaviors, [
            'saveFiles' => [
                'class' => SaveFilesRelation::class,
                'file_path' => 'files/upload/about/',
                'relations' => [
                    'presentation' => [
                        'type' => 'single', // multiple
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'pdf, zip',
                            'maxSize' => 20 * 1024 * 1024, // 3Мб
                        ],
                    ],
                    'sliderimage' => [
                        'type' => 'multirow', // multiple
                        'multifield' => 'about_slider',
                        'multifield_fieldname' => 'image',
                        'multifield_type' => 'single',
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, png, jpeg',
                            'maxSize' => 3 * 1024 * 1024, // 3Мб
                        ],
                        'main' => [
                            'width' => 1400,
                            'height' => 568,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'perkimage' => [
                        'type' => 'multirow', // multiple
                        'multifield' => 'about_perk',
                        'multifield_fieldname' => 'image',
                        'multifield_type' => 'single',
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'svg',
                            'maxSize' => 1 * 1024 * 1024, // 3Мб
                        ],
                    ],
                    'howtoimage' => [
                        'type' => 'multirow', // multiple
                        'multifield' => 'about_howto',
                        'multifield_fieldname' => 'image',
                        'multifield_type' => 'single',
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, jpeg, png',
                            'maxSize' => 1 * 1024 * 1024, // 3Мб
                        ],
                        'main' => [
                            'width' => 396,
                            'height' => 300,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                ],
            ],
            'saveOneVar' => [
                'class' => SaveOneVarRelation::class,
                'relations' => [
                    'single' => [
                        'academy_title',
                        'about_slider',
                        'about_preims',
                        'about_perk',
                        'about_howto',
                    ],
                ],
            ],
            1 => [
                'class' => Serialize::class,
                'relations' => [
                    'about_slider',
                    'about_preims',
                    'about_perk',
                    'about_howto',
                ],
            ],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'presentation' => 'Презентация',
            'presentation_loader' => 'Презентация',
            'academy_title' => 'Заголовок Академия экспертам',
            'about_slider' => 'Слайдер',
            'about_preims' => 'Преимущества',
            'about_perk' => 'Академия Экспертам',
            'about_howto' => 'Как стать экспертом',
        ]);
    }

    // сохранение/удаление/валидация
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['academy_title', 'about_slider', 'about_preims', 'about_perk', 'about_howto'], 'safe'],
        ]);
    }

    public function getFieldTabs()
    {
        return [
            'main' => 'Основное',
            'info' => 'Дополнительные поля',
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
                'content' => 'wysiwyg',
                'academy_title' => 'text',
                'presentation' => 'file',
                'order' => 'integer',
                'visible' => 'boolean',
                'parent_id' => [
                    'type' => 'options',
                    'optionList' => $this->getSectionList(),
                ],
            ],
            'info' => [
                'about_slider' => [
                    'type' => 'multifields',
                    'fields' => [
                        'name' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Название',
                        ],
                        'content' => [
                            'visible_field' => true,
                            'type' => 'textarea',
                            'name' => 'Описание',
                        ],
                        'image' => [
                            'type' => 'image',
                            'source_field' => 'sliderimage',
                            'name' => 'Изображение',
                        ],
                        'button_name' => [
                            'type' => 'text',
                            'name' => 'Название кнопки',
                        ],
                        'button_link' => [
                            'type' => 'text',
                            'name' => 'Ссылка с кнопки',
                        ],
                        'button_class' => [
                            'type' => 'text',
                            'name' => 'Класс кнопки',
                        ],
                        'button_name2' => [
                            'type' => 'text',
                            'name' => 'Название кнопки 2',
                        ],
                        'button_link2' => [
                            'type' => 'text',
                            'name' => 'Ссылка с кнопки 2',
                        ],
                        'button_class2' => [
                            'type' => 'text',
                            'name' => 'Класс кнопки 2',
                        ],
                    ],
                    'hint' => 'Для добавления/изменения элемента используте иконку <b>карандаша</b><br>Рекомендуемый размер изображения: <b>1400 * 568</b>',
                ],
                'about_preims' => [
                    'type' => 'multifields',
                    'fields' => [
                        'name' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Название',
                        ],
                    ],
                ],
                'about_perk' => [
                    'type' => 'multifields',
                    'fields' => [
                        'name' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Название',
                        ],
                        'image' => [
                            'type' => 'image',
                            'source_field' => 'perkimage',
                            'name' => 'Изображение',
                        ],
                    ],
                ],
                'about_howto' => [
                    'type' => 'multifields',
                    'fields' => [
                        'name' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Название',
                        ],
                        'description' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Описание',
                        ],
                        'image' => [
                            'type' => 'image',
                            'source_field' => 'howtoimage',
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

    public function getSliderimage()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => self::class, 'keeper_field' => 'sliderimage']);
    }

    public function getPerkimage()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => self::class, 'keeper_field' => 'perkimage']);
    }

    public function getHowtoimage()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => self::class, 'keeper_field' => 'howtoimage']);
    }

    public function getPresentation()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => About::class, 'keeper_field' => 'presentation']);
    }
}
