<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница первого шага подачи заявки на присоединение к Маркетплейс для Юрлица. Содержит форму для заполнения данных об организации
 */

namespace app\modules\pages\models;

use app\modules\admin\behaviors\SaveFilesRelation;
use app\modules\admin\components\FilestoreModel;

class PServiceRegOOO extends Page
{
    public static $name_for_list = "Форма регистрации услуг как Юрлицо";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'service_regform_ooo';
    public $action_id = 'pages/service/regformooo';
    public $primer_loader;

    public function getFieldTabs()
    {
        return [
            'main' => 'Основное',
            'seo' => 'SEO',
        ];
    }

    public function behaviors()
    {
        $parent_behaviors = parent::behaviors();
        return array_merge($parent_behaviors, [
            'saveFiles' => [
                'class' => SaveFilesRelation::class,
                'relations' => [
                    'primer' => [
                        'type' => 'single', // multiple
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'zip, docx, rar, pdf',
                            'maxSize' => 5 * 1024 * 1024, // 3Мб
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'primer' => 'Образец соглашения об аналоге собственноручной подписи',
            'primer_loader' => 'Образец соглашения об аналоге собственноручной подписи',
        ]);
    }

    public function getFields($tab)
    {
        $fields = [
            'main' => [
                'name' => 'text',
                'url' => 'text',
                'content' => 'wysiwyg',
                'primer' => 'file',
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
            ]
        ];
        return $tab ? $fields[$tab] : $fields;
    }

    public function getPrimer()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => PServiceRegOOO::class, 'keeper_field' => 'primer']);
    }
}
