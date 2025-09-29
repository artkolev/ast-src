<?php
/**
 * @modelDescr Страница-каталог Программ ДПО (модуль eduprog)
 */

namespace app\modules\pages\models;

use app\modules\admin\behaviors\ClearRelation;
use Yii;
use yii\helpers\Url;

class EduprogPage extends Page
{
    public static $name_for_list = "Каталог программ ДПО";
    public static $count_for_list = 1;
    public $accessLevel = 'free';
    public $view = 'eduprog';
    public $action_id = 'pages/pages/eduprog';

    public function behaviors()
    {
        $parent_behaviors = parent::behaviors();
        return array_merge($parent_behaviors, [
            'clearDelete' => [
                'class' => ClearRelation::class,
                'relations' => [
                    'ads',
                ],
            ],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'ads' => 'Реклама',
        ]);
    }

    public function getFieldTabs()
    {
        return [
            'main' => 'Основное',
            'seo' => 'SEO',
        ];
    }

    public function getRelationTabs()
    {
        return [
            'ads' => 'Реклама',
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

    public function getRelations($tab)
    {
        $relations = [
            'ads' => [
                'ads' => [
                    'type' => 'relation',
                    'buttons' => [
                        'add_page' => [
                            'class' => 'success',
                            'name' => 'Создать баннер',
                            'url' => Url::toRoute(['/admin/pages_helper/lentapageads/create', 'LentaPageAds' => ['page_id' => $this->id], 'return_url' => '/admin/' . Yii::$app->controller->module->id . '/' . Yii::$app->controller->id . '/update/?id=' . $this->id, 'anchor' => 'ads']),
                        ],
                    ],
                    'search_class' => '\app\modules\pages_helper\models\LentaPageAdsSearch',
                    'title' => 'Редактирование баннеров',
                ],
            ],
        ];
        return $tab ? $relations[$tab] : $relations;
    }

    // сохранение/удаление/валидация
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['start_module'], 'default', 'value' => 'eduprog'],
        ]);
    }

    public function getAds()
    {
        return $this->hasMany(\app\modules\pages_helper\models\LentaPageAds::class, ['page_id' => 'id'])->andWhere(['visible' => 1])->orderBy(['order' => SORT_ASC]);
    }
}
