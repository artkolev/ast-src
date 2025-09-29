<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница, которая отображается Претенденту после выбора "Другой" или неактивной кафедры на первом шаге заявки на присоединение к академии (Претендент - Эксперт)
 */

namespace app\modules\pages\models;

use app\modules\admin\behaviors\SaveOneVarRelation;

class ProfileNoCafedra extends Page
{
    public static $name_for_list = "выбора другой или неактивной Кафедры претендентом";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'profile_no_cafedra';
    public $action_id = 'pages/profile/nocafedra';
    public $nocafedra;

    public function behaviors()
    {
        $parent_behaviors = parent::behaviors();
        return array_merge($parent_behaviors, [
            'saveOneVar' => [
                'class' => SaveOneVarRelation::class,
                'relations' => [
                    'single' => [
                        'nocafedra',
                    ],
                ],
            ],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'content' => 'Текст на странице, если выбрана кафедра в процессе формирования',
            'nocafedra' => 'Текст на странице, если выбрана кафедра "Другое"',
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
                'nocafedra' => 'wysiwyg',
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

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['nocafedra'], 'safe'],
        ]);
    }
}
