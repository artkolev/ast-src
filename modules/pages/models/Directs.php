<?php
/**
 * @modelDescr Страница-каталог Кафедр сайта (модуль direction)
 */

namespace app\modules\pages\models;

use app\modules\admin\behaviors\SaveOneVarRelation;

class Directs extends Page
{
    public static $name_for_list = "Каталог кафедр";

    /* Внешние переменные */
    public static $count_for_list = 1;
    public $view = 'directs';
    public $academtitle;
    public $experttitle;
    public $exporgtitle;
    public $academtext;
    public $experttext;
    public $exporgtext;
    public $action_id = 'pages/pages/directs';

    public function behaviors()
    {
        $parent_behaviors = parent::behaviors();
        return array_merge($parent_behaviors, [
            'saveOneVar' => [
                'class' => SaveOneVarRelation::class,
                'relations' => [
                    'single' => [
                        'academtitle',
                        'experttitle',
                        'exporgtitle',
                        'academtext',
                        'experttext',
                        'exporgtext',
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
                'academtitle' => 'text',
                'academtext' => 'text',
                'experttitle' => 'text',
                'experttext' => 'text',
                'exporgtitle' => 'text',
                'exporgtext' => 'text',
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

    // сохранение/удаление/валидация
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['academtitle', 'experttitle', 'exporgtitle'], 'safe'],
            [['academtext', 'experttext', 'exporgtext'], 'safe'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'academtitle' => 'Заголовок блока "Экспертный совет" (если не заполнено у Кафедры)',
            'experttitle' => 'Заголовок блока "Эксперты" (если не заполнено у Кафедры)',
            'exporgtitle' => 'Заголовок блока "Экспертные организации" (если не заполнено у Кафедры)',
            'academtext' => 'Текст над блоком "Экспертный совет" (если не заполнено у Кафедры)',
            'experttext' => 'Текст над блоком "Эксперты" (если не заполнено у Кафедры)',
            'exporgtext' => 'Текст над блоком "Экспертные организации" (если не заполнено у Кафедры)',
        ]);
    }
}
