<?php
/**
 * @modelDescr Страница-каталог пользователей с ролью Экспертный совет на кафедре (модуль users)
 */

namespace app\modules\pages\models;

use app\modules\admin\behaviors\SaveOneVarRelation;

class AcademyCatalog extends Page
{
    /* единый view для всех 3-х каталогов */
    public static $name_for_list = "Каталог экспертного совета";
    public static $count_for_list = 1;
    public static $accepted_roles = ['academ'];
    public $view = 'expert_catalog';
    public $action_id = 'pages/pages/academy';

    // перечень ролей пользователей, страницы которых считаются подразделами этого каталога. 
    // роль academ объединена с ролью expert, все страницы переехали в каталог ExpertCatalog, запись о допустимой роли 'academ' оставить пока нужен редирект для старых ссылок на внутренние страницы ВЭС. Редирект прописан в UrlManager модуля users
    public $content_seo;

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'content_seo' => 'SEO-текст',
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
                        'content_seo',
                    ],
                ],
            ],
        ]);
    }

    // сохранение/удаление/валидация
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['start_module', 'default', 'value' => 'users'],
            [['content_seo'], 'safe'],
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
                'content_seo' => 'wysiwyg',
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
}
