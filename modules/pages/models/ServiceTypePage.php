<?php
/**
 * @modelDescr Страница-каталог услуг (модуль service)
 */

namespace app\modules\pages\models;

use app\modules\admin\behaviors\ClearRelation;
use app\modules\admin\behaviors\SaveOneVarRelation;
use Yii;
use yii\helpers\Url;

class ServiceTypePage extends Page
{
    public static $name_for_list = "Целевые аудитории";
    public static $count_for_list = 1;
    public $view = 'service_type';
    public $action_id = 'pages/pages/service-type';
    public $page_title;
    public $page_subtitle;

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'content_seo' => 'SEO-текст',
            'ads' => 'Реклама',
            'page_title' => 'Заголовок страницы по умолчанию',
            'page_subtitle' => 'Подзаголовок страницы по умолчанию',
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
                        'page_title',
                        'page_subtitle',
                    ],
                ],
            ],
            'clearDelete' => [
                'class' => ClearRelation::class,
                'relations' => [
                    'ads',
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
                'page_title' => 'text',
                'page_subtitle' => 'text',
                'content' => 'wysiwyg',
                'content_seo' => 'wysiwyg',
                'order' => 'integer',
                'visible' => 'boolean',
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
            [['start_module'], 'default', 'value' => 'service_type'],
            [['content_seo', 'page_title', 'page_subtitle'], 'safe'],
        ]);
    }

    public function getAds()
    {
        return $this->hasMany(\app\modules\pages_helper\models\LentaPageAds::class, ['page_id' => 'id'])->andWhere(['visible' => 1])->orderBy(['order' => SORT_ASC]);
    }
}
