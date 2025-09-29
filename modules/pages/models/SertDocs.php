<?php
/**
 * @modelDescr Страница Личного кабинета - Доступна пользователям НЕ зарегистрированным на Маркетплейс, содержит текст и загруженные файлы
 */

namespace app\modules\pages\models;

use app\modules\admin\behaviors\SaveFilesRelation;
use app\modules\admin\components\FilestoreModel;

class SertDocs extends Page
{
    public static $name_for_list = "Сертификаты в ЛК";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'documents_sert';
    public $action_id = 'pages/documents/documentssert';
    public $docs_loader;

    public function behaviors()
    {
        $parent_behaviors = parent::behaviors();
        return array_merge($parent_behaviors, [
            'saveFiles' => [
                'class' => SaveFilesRelation::class,
                'file_path' => 'files/upload/service/',
                'relations' => [
                    'docs' => [
                        'type' => 'multiple', // multiple
                        'validate' => [
                            'maxFiles' => 20,
                            'extensions' => 'pdf, doc, docx, zip',
                            'maxSize' => 20 * 1024 * 1024, // 1Мб
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'docs' => 'Документы для пользователей',
            'docs_loader' => 'Документы для пользователей',
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
                'docs' => 'multifile',
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

    public function getDocs()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => SertDocs::class, 'keeper_field' => 'docs'])->orderBy(['order' => SORT_ASC]);
    }
}
