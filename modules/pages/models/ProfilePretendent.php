<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница второго шага заявки на присоединение к академии (Претендент - Эксперт). Заполнение анкеты претендента.
 */

namespace app\modules\pages\models;

use app\modules\admin\behaviors\SaveOneVarRelation;

class ProfilePretendent extends Page
{
    public static $name_for_list = "заполнения Анкеты претендента";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'profile_anketa';
    public $action_id = 'pages/profile/pretendent';
    public $profarea_title;
    public $profarea_text;

    public $main_title;
    public $main_text;

    public $education_title;
    public $education_text;

    public $career_title;
    public $career_text;

    public $history_title;
    public $history_text;

    public $docs_title;
    public $docs_text;

    public function behaviors()
    {
        $parent_behaviors = parent::behaviors();
        return array_merge($parent_behaviors, [
            'saveOneVar' => [
                'class' => SaveOneVarRelation::class,
                'relations' => [
                    'single' => [
                        'profarea_title',
                        'profarea_text',
                        'main_title',
                        'main_text',
                        'education_title',
                        'education_text',
                        'career_title',
                        'career_text',
                        'history_title',
                        'history_text',
                        'docs_title',
                        'docs_text'
                    ],
                ],
            ],
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
                'profarea_title' => 'text',
                'profarea_text' => 'text',
                'main_title' => 'text',
                'main_text' => 'text',
                'education_title' => 'text',
                'education_text' => 'text',
                'career_title' => 'text',
                'career_text' => 'text',
                'history_title' => 'text',
                'history_text' => 'text',
                'docs_title' => 'text',
                'docs_text' => 'text',
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

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'profarea_title' => 'Заголовок блока Профессиональная область',
            'main_title' => 'Заголовок блока Основное',
            'education_title' => 'Заголовок блока Образование',
            'career_title' => 'Заголовок блока Профессиональный опыт',
            'history_title' => 'Заголовок блока Личная история',
            'docs_title' => 'Заголовок блока Документы',
            'profarea_text' => 'Текст блока Профессиональная область',
            'main_text' => 'Текст блока Основное',
            'education_text' => 'Текст блока Образование',
            'career_text' => 'Текст блока Профессиональный опыт',
            'history_text' => 'Текст блока Личная история',
            'docs_text' => 'Текст блока Документы',
        ]);
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['profarea_title', 'main_title', 'education_title', 'career_title', 'history_title', 'docs_title', 'profarea_text', 'main_text', 'education_text', 'career_text', 'history_text', 'docs_text'], 'safe'],
        ]);
    }
}
