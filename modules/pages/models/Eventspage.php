<?php
/**
 * @modelDescr Страница-каталог Мероприятий (модуль events)
 */

namespace app\modules\pages\models;

use app\modules\admin\behaviors\ClearRelation;
use app\modules\admin\behaviors\SaveOneVarRelation;
use app\modules\admin\behaviors\Serialize;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class Eventspage extends Page
{
    public static $name_for_list = "Каталог мероприятий";
    public static $count_for_list = 1;
    public static $promoEventsSelectList;
    public static $promoEventsList;
    public $view = 'events';
    public $action_id = 'pages/pages/events';
    public $promo_events;

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
            'saveOneVar' => [
                'class' => SaveOneVarRelation::class,
                'relations' => [
                    'single' => [
                        'promo_events',
                    ],
                ],
            ],
            'serialize' => [
                'class' => Serialize::class,
                'relations' => [
                    'promo_events',
                ],
            ],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'promo_events' => 'Промо записи',
            'ads' => 'Реклама',
        ]);
    }

    public function getFieldTabs()
    {
        return [
            'main' => 'Основное',
            'promo_events' => 'Промо записи',
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
            'promo_events' => [
                'promo_events' => [
                    'type' => 'multifields',
                    'fields' => [
                        'promo' => [
                            'visible_field' => true,
                            'type' => 'options',
                            'name' => 'Запись',
                            'options_list' => $this->getPromoEventsList(),
                            'htmlClass' => 'select_pretty',
                        ],
                    ],
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

    public function getPromoEventsList($select_format = false)
    {
        if (($select_format and !isset(self::$promoEventsSelectList)) or (!$select_format and !isset(self::$promoEventsList))) {
            $result = [];
            $select_result = [];
            $result[0] = 'Не выбрано';

            $result += ArrayHelper::map(\app\modules\events\models\Events::findVisible()->orderBy(['name' => 'asc'])->all(), 'id', 'name');
            foreach ($result as $key => $value) {
                $select_result[] = ['id' => $key, 'text' => $value];
            }
            self::$promoEventsSelectList = $select_result;
            self::$promoEventsList = $result;
        }
        return $select_format ? self::$promoEventsSelectList : self::$promoEventsList;
    }

    // сохранение/удаление/валидация

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

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['start_module'], 'default', 'value' => 'events'],
            [['promo_events'], 'safe'],
        ]);
    }

    public function getPromo_events_ids()
    {
        $pop = $this->promo_events;
        foreach ($pop as $key => $value) {
            if ($value['visible'] != 1) {
                unset($pop[$key]);
            }
        }
        if (!empty($pop)) {
            usort($pop, function ($a, $b) {
                return $a['order'] <=> $b['order'];
            });
        }
        return array_keys(ArrayHelper::map($pop, 'promo', 'promo'));
    }

    public function getAds()
    {
        return $this->hasMany(\app\modules\pages_helper\models\LentaPageAds::class, ['page_id' => 'id'])->andWhere(['visible' => 1])->orderBy(['order' => SORT_ASC]);
    }
}
