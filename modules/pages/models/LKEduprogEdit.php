<?php
/**
 * @modelDescr Страница Личного кабинета - Создание/редактирование программы ДПО
 */

namespace app\modules\pages\models;

use app\modules\admin\behaviors\SaveOneVarRelation;

class LKEduprogEdit extends Page
{
    public static $name_for_list = "страницу Создания/Редактирования программы ДПО в ЛК (Организатор)";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'eduprog_edit';
    public $action_id = 'pages/eduprog/eduprogedit';

    /* доп контент */
    public $need_marketplace;
    public $need_license;

    public function behaviors()
    {
        $parent_behaviors = parent::behaviors();
        return array_merge($parent_behaviors, [
            'saveOneVar' => [
                'class' => SaveOneVarRelation::class,
                'relations' => [
                    'single' => [
                        'need_marketplace',
                        'need_license',
                    ],
                ],
            ],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'need_marketplace' => 'Текст для пользователей, не зарегистрированных на маркетплейс',
            'need_license' => 'Текст для пользователей, не имеющих лицензии на образовательные программы',
        ]);
    }

    public function getFieldTabs()
    {
        return [
            'main' => 'Основное',
            'register' => 'Требуется регистрация',
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
                'order' => 'integer',
                'visible' => 'boolean',
                'parent_id' => [
                    'type' => 'options',
                    'optionList' => $this->getSectionList(),
                ],
            ],
            'register' => [
                'need_marketplace' => 'wysiwyg',
                'need_license' => 'wysiwyg',
            ],
            'seo' => [
                'h1_tag' => 'text',
                'meta_title' => 'text',
                'meta_description' => 'textarea',
                'meta_keywords' => 'textarea',
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
            [['need_marketplace', 'need_license'], 'safe'],
        ]);
    }
}
