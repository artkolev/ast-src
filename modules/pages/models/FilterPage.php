<?php
/**
 * @modelDescr Страница с фильтрами по каталогу Экспертов и Экспертного совета, на текущий момент отключена
 */

namespace app\modules\pages\models;

use app\modules\admin\behaviors\SaveFilesRelation;
use app\modules\admin\components\FilestoreModel;

class FilterPage extends Page
{
    public static $name_for_list = "страницу Фильтрации УАСТ";
    public static $count_for_list = 1;
    public $view = 'filter';
    public $action_id = 'pages/pages/filter';
    public $image_loader;

    public function behaviors()
    {
        $parent_behaviors = parent::behaviors();
        return array_merge($parent_behaviors, [
            'saveFiles' => [
                'class' => SaveFilesRelation::class,
                'relations' => [
                    'image' => [
                        'type' => 'single', // multiple
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, jpeg, png',
                            'maxSize' => 2 * 1024 * 1024, // 3Мб
                        ],
                        'main' => [
                            'width' => 1920,
                            'height' => 287,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                ]
            ],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'image' => 'Изображение',
            'image_loader' => 'Изображение',
        ]);
    }

    public function getFieldTabs()
    {
        return [
            'main' => 'Основное',
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
                'image' => 'image',
                'order' => 'integer',
                'visible' => 'boolean',
                'parent_id' => [
                    'type' => 'options',
                    'optionList' => $this->getSectionList(),
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

    public function getImage()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => FilterPage::class, 'keeper_field' => 'image']);
    }
}
