<?php
/**
 * @modelDescr Страница-каталог раздела Ленты (модуль lenta) - Актуальное
 */

namespace app\modules\pages\models;

use app\modules\admin\behaviors\ClearRelation;
use app\modules\admin\behaviors\SaveFilesRelation;
use app\modules\admin\behaviors\SaveOneVarRelation;
use app\modules\admin\behaviors\Serialize;
use app\modules\admin\components\FilestoreModel;
use app\modules\users\models\UserAR;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class Lentapage extends Page
{
    public static $name_for_list = "каталог Ленты (Актуальное)";
    public static $count_for_list = 1;
    public static $expertsSelectList;
    public static $expertsList;
    public static $tagsSelectList;
    public static $tagsList;
    public static $promoServiceSelectList;
    public static $promoServiceList;
    public static $promoLentaSelectList;
    public static $promoLentaList;
    public $view = 'lenta';
    public $action_id = 'pages/pages/lenta';
    public $image_first_loader;
    public $image_first_mobile_loader;
    public $first_lenta;
    public $promo_lenta;
    public $promo_service;
    public $tags;
    public $experts;

    public static function getTrending($limit = 5, $offset = 0)
    {
        return \app\modules\lenta\models\Lenta::findVisible()->orderBy(['trending_score' => SORT_DESC, 'views' => SORT_DESC])->offset($offset)->limit($limit)->all();
    }

    public function behaviors()
    {
        $parent_behaviors = parent::behaviors();
        return array_merge($parent_behaviors, [
            'clearDelete' => [
                'class' => ClearRelation::class,
                'relations' => [
                    'ads',
                    'blocks',
                ],
            ],
            'saveOneVar' => [
                'class' => SaveOneVarRelation::class,
                'relations' => [
                    'single' => [
                        'promo_lenta',
                        'promo_service',
                        'tags',
                        'experts',
                        'first_lenta',
                    ],
                ],
            ],
            'serialize' => [
                'class' => Serialize::class,
                'relations' => [
                    'promo_lenta',
                    'promo_service',
                    'tags',
                    'experts',
                    'first_lenta',
                ],
            ],
            'saveFiles' => [
                'class' => SaveFilesRelation::class,
                'file_path' => 'files/upload/lenta/',
                'relations' => [
                    'image_first' => [
                        'type' => 'single',
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, jpeg, png, gif, svg',
                            'maxSize' => 1024 * 1024 * 5,
                            'minWidth' => 900,
                            'minHeight' => 300,
                        ],
                        'main' => [
                            'width' => 900,
                            'height' => 300,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'image_first_mobile' => [
                        'type' => 'single',
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, jpeg, png, gif, svg',
                            'maxSize' => 1024 * 1024 * 5,
                            'minWidth' => 420,
                            'minHeight' => 280,
                        ],
                        'main' => [
                            'width' => 420,
                            'height' => 280,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'experts' => 'Эксперты',
            'ads' => 'Реклама',
            'tags' => 'Теги',
            'promo_lenta' => 'Промо записи',
            'promo_service' => 'Промо услуга',
            'blocks' => 'Материалы между статьями',
            'image_first' => 'Изображение',
            'image_first_loader' => 'Изображение',
            'image_first_mobile' => 'Изображение для мобильного',
            'image_first_mobile_loader' => 'Изображение для мобильного',
            'first_lenta' => 'Закрепленный материал',
        ]);
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['promo_lenta', 'promo_service', 'tags', 'blocks', 'ads', 'experts', 'first_lenta'], 'safe'],
        ]);
    }

    public function getFieldTabs()
    {
        return [
            'main' => 'Основное',
            'first_lenta' => 'Закрепленный материал',
            'experts' => 'Эксперты',
            'tags' => 'Теги',
            'promo_lenta' => 'Промо записи',
            // 'promo_service' => 'Промо услуга',
            'seo' => 'SEO',
        ];
    }

    public function getRelationTabs()
    {
        return [
            'ads' => 'Реклама',
            'blocks' => 'Материалы между статьями',
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
            ],
            'first_lenta' => [
                'image_first' => 'image',
                'image_first_mobile' => 'image',
                'first_lenta' => [
                    'type' => 'multifields',
                    'fields' => [
                        'promo' => [
                            'visible_field' => true,
                            'type' => 'options',
                            'name' => 'Запись',
                            'options_list' => $this->getPromoLentaList(),
                            'htmlClass' => 'select_pretty',
                        ],
                    ],
                ],
            ],
            'promo_lenta' => [
                'promo_lenta' => [
                    'type' => 'multifields',
                    'fields' => [
                        'promo' => [
                            'visible_field' => true,
                            'type' => 'options',
                            'name' => 'Запись',
                            'options_list' => $this->getPromoLentaList(),
                            'htmlClass' => 'select_pretty',
                        ],
                    ],
                ],
            ],
            'promo_service' => [
                'promo_service' => [
                    'type' => 'multifields',
                    'fields' => [
                        'service' => [
                            'visible_field' => true,
                            'type' => 'options',
                            'name' => 'Услуга',
                            'options_list' => $this->getPromoServiceList(),
                            'htmlClass' => 'select_pretty',
                        ],
                    ],
                ],
            ],
            'tags' => [
                'tags' => [
                    'type' => 'multifields',
                    'fields' => [
                        'tag' => [
                            'visible_field' => true,
                            'type' => 'options',
                            'name' => 'Тег',
                            'options_list' => $this->getTagsList(),
                            'htmlClass' => 'select_pretty',
                        ],
                    ],
                ],
            ],
            'experts' => [
                'experts' => [
                    'type' => 'multifields',
                    'fields' => [
                        'expert' => [
                            'visible_field' => true,
                            'type' => 'options',
                            'name' => 'Эксперт',
                            'options_list' => $this->getExpertsList(),
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

    public function getPromoLentaList($select_format = false)
    {
        if (($select_format and !isset(self::$promoLentaSelectList)) or (!$select_format and !isset(self::$promoLentaList))) {
            $result = [];
            $select_result = [];
            $result[0] = 'Не выбрано';

            $result += ArrayHelper::map(\app\modules\lenta\models\Lenta::findVisible()->orderBy(['name' => 'asc'])->all(), 'id', 'name');
            foreach ($result as $key => $value) {
                $select_result[] = ['id' => $key, 'text' => $value];
            }
            self::$promoLentaSelectList = $select_result;
            self::$promoLentaList = $result;
        }
        return $select_format ? self::$promoLentaSelectList : self::$promoLentaList;
    }

    public function getPromoServiceList($select_format = false)
    {
        if (($select_format and !isset(self::$promoServiceSelectList)) or (!$select_format and !isset(self::$promoServiceList))) {
            $result = [];
            $select_result = [];
            $result[0] = 'Не выбрано';

            $result += ArrayHelper::map(\app\modules\service\models\Service::findVisible()->all(), 'id', 'name');
            foreach ($result as $key => $value) {
                $select_result[] = ['id' => $key, 'text' => $value];
            }
            self::$promoServiceSelectList = $select_result;
            self::$promoServiceList = $result;
        }
        return $select_format ? self::$promoServiceSelectList : self::$promoServiceList;
    }

    public function getTagsList($select_format = false)
    {
        if (($select_format and !isset(self::$tagsSelectList)) or (!$select_format and !isset(self::$tagsList))) {
            $result = [];
            $select_result = [];
            $result[0] = 'Не выбрано';

            $result += ArrayHelper::map(\app\modules\reference\models\Lentatag::find()->all(), 'id', 'name');
            foreach ($result as $key => $value) {
                $select_result[] = ['id' => $key, 'text' => $value];
            }
            self::$tagsSelectList = $select_result;
            self::$tagsList = $result;
        }
        return $select_format ? self::$tagsSelectList : self::$tagsList;
    }

    public function getExpertsList($select_format = false)
    {
        if (($select_format and !isset(self::$expertsSelectList)) or (!$select_format and !isset(self::$expertsList))) {

            $roles = ['expert'];

            $items = (new \yii\db\Query())
                ->select(['user.id as id', "CONCAT(profile.name,' ',profile.surname) as halfname", 'profile.organization_name as organization_name', 'auth_assignment.item_name as role'])
                ->from('user')
                ->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id')
                ->leftJoin('profile', 'profile.user_id = user.id')
                ->where(['IN', 'auth_assignment.item_name', $roles])
                ->andWhere(['status' => UserAR::STATUS_ACTIVE])
                ->andWhere(['user.visible' => 1])
                ->orderBy(['profile.surname' => 'asc', 'profile.name' => 'asc'])
                ->all();

            $result = [];
            $select_result = [];
            $result[0] = 'Администрация';

            $select_result[] = ['id' => 0, 'text' => 'Администрация'];
            foreach ($items as $user) {
                $select_result[] = ['id' => $user['id'], 'text' => ($user['role'] == 'exporg' ? $user['organization_name'] : $user['halfname'])];
                $result[$user['id']] = ($user['role'] == 'exporg' ? $user['organization_name'] : $user['halfname']);
            }
            self::$expertsSelectList = $select_result;
            self::$expertsList = $result;
        }
        return $select_format ? self::$expertsSelectList : self::$expertsList;
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
                            'name' => 'Создать',
                            'url' => Url::toRoute(['/admin/pages_helper/lentapageads/create', 'LentaPageAds' => ['page_id' => $this->id], 'return_url' => '/admin/' . Yii::$app->controller->module->id . '/' . Yii::$app->controller->id . '/update/?id=' . $this->id, 'anchor' => 'ads']),
                        ],
                    ],
                    'search_class' => '\app\modules\pages_helper\models\LentaPageAdsSearch',
                    'title' => 'Редактирование',
                ],
            ],
            'blocks' => [
                'blocks' => [
                    'type' => 'relation',
                    'buttons' => [
                        'add_actual' => [
                            'class' => 'success',
                            'name' => 'Создать блок Актуальное',
                            'url' => Url::toRoute(['/admin/pages_helper/lentapageblock/create', 'view' => 'actual', 'LentaPageBlockActual' => ['page_id' => $this->id, 'view' => 'actual'], 'return_url' => '/admin/' . Yii::$app->controller->module->id . '/' . Yii::$app->controller->id . '/update/?id=' . $this->id, 'anchor' => 'blocks']),
                        ],
                        'add_events' => [
                            'class' => 'success',
                            'name' => 'Создать блок Мероприятия',
                            'url' => Url::toRoute(['/admin/pages_helper/lentapageblock/create', 'view' => 'events', 'LentaPageBlockEvents' => ['page_id' => $this->id, 'view' => 'events'], 'return_url' => '/admin/' . Yii::$app->controller->module->id . '/' . Yii::$app->controller->id . '/update/?id=' . $this->id, 'anchor' => 'blocks']),
                        ],
                        'add_favorites' => [
                            'class' => 'success',
                            'name' => 'Создать блок Избранное',
                            'url' => Url::toRoute(['/admin/pages_helper/lentapageblock/create', 'view' => 'favorites', 'LentaPageBlockFavorites' => ['page_id' => $this->id, 'view' => 'favorites'], 'return_url' => '/admin/' . Yii::$app->controller->module->id . '/' . Yii::$app->controller->id . '/update/?id=' . $this->id, 'anchor' => 'blocks']),
                        ],
                        'add_services' => [
                            'class' => 'success',
                            'name' => 'Создать блок Услуги',
                            'url' => Url::toRoute(['/admin/pages_helper/lentapageblock/create', 'view' => 'services', 'LentaPageBlockServices' => ['page_id' => $this->id, 'view' => 'services'], 'return_url' => '/admin/' . Yii::$app->controller->module->id . '/' . Yii::$app->controller->id . '/update/?id=' . $this->id, 'anchor' => 'blocks']),
                        ],
                        'add_slider' => [
                            'class' => 'success',
                            'name' => 'Создать слайдер',
                            'url' => Url::toRoute(['/admin/pages_helper/lentapageblock/create', 'view' => 'slider', 'LentaPageBlockSlider' => ['page_id' => $this->id, 'view' => 'slider'], 'return_url' => '/admin/' . Yii::$app->controller->module->id . '/' . Yii::$app->controller->id . '/update/?id=' . $this->id, 'anchor' => 'blocks']),
                        ],
                    ],
                    'search_class' => '\app\modules\pages_helper\models\LentaPageBlockSearch',
                    'title' => 'Редактирование',
                ],
            ],
        ];
        return $tab ? $relations[$tab] : $relations;
    }

    public function getExperts()
    {
        $pop = $this->experts;
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
        $keys = implode(',', array_keys(ArrayHelper::map($pop, 'expert', 'expert')));
        $ret = UserAR::find()
            ->andWhere(['IN', 'id', array_keys(ArrayHelper::map($pop, 'expert', 'expert'))])
            ->visible(['expert', 'exporg', 'fizusr', 'urusr']);
        if (!empty($keys)) {
            $ret->orderBy([new \yii\db\Expression('FIELD (id, ' . $keys . ')')]);
        }
        return $ret->all();
    }

    public function getPageTags()
    {
        $pop = $this->tags;
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
        $keys = implode(',', array_keys(ArrayHelper::map($pop, 'tag', 'tag')));
        $ret = \app\modules\reference\models\Lentatag::find()
            ->where(['visible' => 1])
            ->andWhere(['IN', 'id', array_keys(ArrayHelper::map($pop, 'tag', 'tag'))]);
        if (!empty($keys)) {
            $ret->orderBy([new \yii\db\Expression('FIELD (id, ' . $keys . ')')]);
        }
        return $ret->all();
    }

    public function getPromo_lenta($query = false)
    {
        $pop = $this->promo_lenta;
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
        $keys = implode(',', array_keys(ArrayHelper::map($pop, 'promo', 'promo')));
        $ret = \app\modules\lenta\models\Lenta::findVisible()->andWhere(['IN', 'lenta.id', array_keys(ArrayHelper::map($pop, 'promo', 'promo'))]);
        if (!empty($keys)) {
            $ret->orderBy([new \yii\db\Expression('FIELD (lenta.id, ' . $keys . ')')]);
        }
        if ($query) {
            return $ret;
        }
        return $ret->all();
    }

    public function getPromo_service()
    {
        $pop = $this->promo_service;
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
        $keys = implode(',', array_keys(ArrayHelper::map($pop, 'service', 'service')));
        $ret = \app\modules\service\models\Service::findVisible()
            ->andWhere(['IN', 'service.id', array_keys(ArrayHelper::map($pop, 'service', 'service'))]);
        if (!empty($keys)) {
            $ret->orderBy([new \yii\db\Expression('FIELD (service.id, ' . $keys . ')')]);
        }
        return $ret->all();
    }

    public function getFirst_lenta()
    {
        $pop = $this->first_lenta;
        foreach ($pop as $key => $value) {
            if ($value['visible'] != 1) {
                unset($pop[$key]);
            }
        }
        $ret = \app\modules\lenta\models\Lenta::findVisible()
            ->andWhere(['IN', 'lenta.id', array_keys(ArrayHelper::map($pop, 'promo', 'promo'))])
            ->orderBy(new \yii\db\Expression('rand()'));
        return $ret->one();
    }

    public function getImage_first()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => Lentapage::class, 'keeper_field' => 'image_first']);
    }

    public function getImage_first_mobile()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => Lentapage::class, 'keeper_field' => 'image_first_mobile']);
    }

    public function getBlocks()
    {
        return $this->hasMany(\app\modules\pages_helper\models\LentaPageBlock::class, ['page_id' => 'id'])->andWhere(['visible' => 1])->orderBy(['order' => SORT_ASC]);
    }

    public function getAds()
    {
        return $this->hasMany(\app\modules\pages_helper\models\LentaPageAds::class, ['page_id' => 'id'])->andWhere(['visible' => 1])->orderBy(['order' => SORT_ASC]);
    }
}
