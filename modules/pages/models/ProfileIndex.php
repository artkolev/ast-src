<?php
/**
 * @modelDescr Страница Личного Кабинета - главная страница пользователького ЛК. Содержит форму для редактирования основных данных пользователя и смены пароля
 */

namespace app\modules\pages\models;

use app\modules\admin\behaviors\SaveOneVarRelation;

class ProfileIndex extends Page
{
    public static $name_for_list = "Профиль пользователя";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'profile_index';
    public $action_id = 'pages/profile/index';
    public $academ_content;
    public $expert_content;
    public $exporg_content;
    public $urusr_content;
    public $fizusr_content;

    public function behaviors()
    {
        $parent_behaviors = parent::behaviors();
        return array_merge($parent_behaviors, [
            'saveOneVar' => [
                'class' => SaveOneVarRelation::class,
                'relations' => [
                    'single' => [
                        'academ_content',
                        'expert_content',
                        'exporg_content',
                        'urusr_content',
                        'fizusr_content',
                    ],
                ],
            ],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'content' => 'Текст на странице для всех',
            'academ_content' => 'Текст на странице для ВЭС',
            'expert_content' => 'Текст на странице для Эксперта',
            'exporg_content' => 'Текст на странице для ЭО',
            'urusr_content' => 'Текст на странице для Юрлица',
            'fizusr_content' => 'Текст на странице для Физлица',
        ]);
    }

    public function getFieldTabs()
    {
        return [
            'main' => 'Основное',
            'seo' => 'SEO',
        ];
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['academ_content', 'expert_content', 'exporg_content', 'urusr_content', 'fizusr_content'], 'safe'],
        ]);
    }

    public function getFields($tab)
    {
        $fields = [
            'main' => [
                'name' => 'text',
                'url' => 'text',
                'content' => 'wysiwyg',
                'academ_content' => 'wysiwyg',
                'expert_content' => 'wysiwyg',
                'exporg_content' => 'wysiwyg',
                'urusr_content' => 'wysiwyg',
                'fizusr_content' => 'wysiwyg',
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
