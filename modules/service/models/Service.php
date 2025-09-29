<?php
/**
 * @modelDescr Услуги
 */

namespace app\modules\service\models;

use app\jobs\BitrixEntityUpdateJob;
use app\modules\admin\behaviors\ClearRelation;
use app\modules\admin\behaviors\SaveDatesRelation;
use app\modules\admin\behaviors\SaveFilesRelation;
use app\modules\admin\behaviors\SaveSetsBehaviour;
use app\modules\admin\behaviors\UrlBehaviour;
use app\modules\admin\components\DeepModel;
use app\modules\admin\components\FilestoreModel;
use app\modules\bitrixcrm\models\Bitrixcrm;
use app\modules\bitrixcrm\models\Competence;
use app\modules\bitrixcrm\models\Solvtask;
use app\modules\keywords\models\Keyword;
use app\modules\order\models\Order;
use app\modules\queries\models\Queries;
use app\modules\reference\models\City;
use app\modules\service\models\query\ServiceQuery;
use app\modules\servicemoder\models\Servicemoder;
use app\modules\users\models\UserAR;
use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * @property int $id
 * @property int $user_id
 * @property int $direction_id
 * @property int $visible
 * @property int $type
 * @property int $order
 * @property int $service_id
 * @property string $name
 * @property string $description
 * @property string $short_description
 * @property string $special_descr
 * @property string $price_descr
 * @property string $video`
 * @property int $created_at
 * @property int $updated_at
 * @property int $city_id
 * @property int $price
 * @property int $old_price
 * @property int $type_id
 * @property string $platform
 * @property string $status
 * @property string $url
 * @property string $place
 * @property FilestoreModel[] $image
 * @property UploadedFile[] $image_loader
 * @property FilestoreModel $videoimage
 * @property UploadedFile $videoimage_loader
 * @property array $remove_image
 * @property int $kind
 * @property int $target_audience
 * @property string $vis_fiz
 * @property string $vis_ur
 * @property string $vis_expert
 * @property string $vis_exporg
 *
 * @property string $kindName // онлайн, офлайн
 *
 * @property Servicemoder $currentModeration возвращает модерацию для работы из редактирования услуги (NEW && PENDING)
 * @property Servicemoder $processedModeration возвращает модерацию, которая в данный момент на рассмотрении (MODERATE)
 * @property Servicemoder $lastModeration последняя обработанная модерация (для отображения причины отказа или доработки)
 * @property Servicemoder[] $servicemoder Все модедерации услуги
 * @property string $moderationStatus // статус услгуи по стутусу модерации
 * @property Competence[] $competence
 * @property Solvtask[] $solvtask
 *
 * тестовое задание
 * @property string $text
 * @property int $date
 * @property string $datetime
 * @property UploadedFile $file_loader
 * @property UploadedFile[] $multiimage_loader
 */
class Service extends DeepModel implements \app\interfaces\SitemapInterface
{
    public const TYPE_TYPICAL = 0;   // типовая услуга
    public const TYPE_CUSTOM = 1;   // нетиповая услуга

    public const KIND_OFFLINE = 0;   // офлайн-услуга
    public const KIND_ONLINE = 1;   // онлайн-услуга
    public const KIND_HYBRID = 2;   // онлайн и офлайн тип услуги

    // Статусы услуги
    public const STATUS_DRAFT = 'draft';
    public const STATUS_FIRST_MODERATE = 'first_moderate';
    public const STATUS_WAIT_EDIT_FIRST_MODERATE = 'wait_edit_first_moderate';
    public const STATUS_WAIT_MODERATE = 'wait_moderate';
    public const STATUS_WAIT_EDIT_MODERATE = 'wait_edit_moderate';
    public const STATUS_PUBLIC = 'public'; // отображаются в каталоге
    public const STATUS_DECLINED = 'declined';
    public const STATUS_ARCHIVE = 'archive';

    public const STATUS_NAMES = [
        self::STATUS_DRAFT => 'Черновик',
        self::STATUS_FIRST_MODERATE => 'На модерации', // Первичная модерация
        self::STATUS_WAIT_EDIT_FIRST_MODERATE => 'Ждет изменений', // Ждет изменений на первичной модерации
        self::STATUS_WAIT_MODERATE => 'На модерации',
        self::STATUS_WAIT_EDIT_MODERATE => 'Ждет изменений',
        self::STATUS_PUBLIC => 'Опубликована',
        self::STATUS_DECLINED => 'Отклонена',
        self::STATUS_ARCHIVE => 'Удалена'
    ];

    // при таких статусах редактирование услуги невозможно
    public const NOT_EDIT_STATUS = [
        Service::STATUS_FIRST_MODERATE,
        Service::STATUS_WAIT_MODERATE,
        Service::STATUS_DECLINED,
        Service::STATUS_ARCHIVE
    ];

    // статусы доступные в каталоге
    public const CATALOG_VISIBLE_STATUSES = [
        Service::STATUS_PUBLIC,
        Service::STATUS_WAIT_EDIT_MODERATE,
        Service::STATUS_WAIT_MODERATE
    ];

    // статусы первичной модерации
    public const FIRST_MODERATE_STATUSES = [
        Service::STATUS_DRAFT,
        Service::STATUS_FIRST_MODERATE,
        Service::STATUS_WAIT_EDIT_FIRST_MODERATE
    ];
    public static $userList;
    public static $userSelectList;
    public static $directionSelectList;
    public static $directionList;
    public static $cityList;
    public static $citySelectList;
    public static $solvtaskList;
    public static $solvtaskSelectList;
    public static $competenceList;
    public static $competenceSelectList;
    public static $groupList;
    public static $groupSelectList;
    public static $targetaurienceList;
    public static $targetaurienceSelectList;
    public static $platformList;
    public static $platformSelectList;
    public $view = 'service_inner';
    public $image_loader;
    public $videoimage_loader;
    public $meta_og_image_loader;
    public $file_loader;
    public $multiimage_loader;

    public static function tableName()
    {
        return '{{%service}}';
    }

    public static function findForSitemap()
    {
        $query = Service::find()->where(['vis_fiz' => 1])->visible();
        /* видимость по автору */
        $query->leftJoin('user as author', 'author.id = service.user_id');
        $query->leftJoin('organization', 'organization.user_id = service.user_id');
        /* отображать услуги от АСТ, либо от активного пользователя, не скрытого в каталогах, с разрешением на работу в маркетплейс */
        $query->andWhere(['OR', ['service.user_id' => 0], ['AND', ['author.status' => UserAR::STATUS_ACTIVE], ['author.visible' => 1], ['organization.can_service' => 1]]]);
        return $query;
    }

    public static function find()
    {
        return new ServiceQuery(get_called_class());
    }

    public static function findVisible()
    {
        return Service::find()->visible()->visibleByRole()->visibleAuthor();
    }

    public static function findShowInnerPage()
    {
        $query = self::find()->where(['IN', 'service.status', Service::CATALOG_VISIBLE_STATUSES]);

        /* видимость по автору */
        $query->leftJoin('user as author', 'author.id = service.user_id');
        /* отображать услуги от активного пользователя, не скрытого в каталогах */
        $query->andWhere(['AND', ['author.status' => UserAR::STATUS_ACTIVE], ['author.visible' => 1]]);

        return $query;
    }

    public function behaviors()
    {
        $parent_behaviors = parent::behaviors();
        return array_merge($parent_behaviors, [
            'clearDelete' => [
                'class' => ClearRelation::class,
                'relations' => [
                    'orders',
                    'queries',
                    'serviceb24',
                ],
            ],
            'urlBehaviour' => [
                'class' => UrlBehaviour::class,
                'attributes' => ['name'],
            ],
            'meta_robots' => [
                'class' => SaveSetsBehaviour::class,
                'attributes' => ['meta_robots_tag'],
            ],
            'saveRelations' => [
                'class' => SaveRelationsBehavior::class,
                'relations' => [
                    'keywords',
                    'target_audience',
                    'competence',
                    'solvtask',
                ],
            ],
            'saveFiles' => [
                'class' => SaveFilesRelation::class,
                'file_path' => 'files/upload/service/',
                'relations' => [
                    'image' => [
                        'type' => 'multiple', // multiple
                        // 'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 20,
                            'extensions' => 'jpg, png, jpeg',
                            'maxSize' => 2 * 1024 * 1024, // 1Мб
                            // 'minWidth' => 730,
                        ],
                        'main' => [
                            'width' => 730,
                            'height' => 265,
                            'quality' => 90,
                            'mode' => 'inset',
                        ],
                    ],
                    'videoimage' => [
                        'type' => 'single', // multiple
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, png, jpeg',
                            'maxSize' => 2 * 1024 * 1024, // 1Мб
                            'minWidth' => 730,
                            'minHeight' => 486,
                        ],
                        'main' => [
                            'width' => 730,
                            'height' => 486,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'meta_og_image' => [
                        'default' => 'img/opengraph_default.jpg',
                        'type' => 'single', // multiple
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, png, jpeg',
                            'maxSize' => 1024 * 1024, // 1Мб
                            'minHeight' => 200,
                            'minWidth' => 300,
                        ],
                        'main' => [
                            'width' => 320,
                            'height' => 220,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'file' => [
                        'type' => 'single',
                        'validator' => 'file',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'pdf, zip, txt',
                            'maxSize' => 20 * 1024 * 1024,
                        ],
                    ],
                    'multiimage' => [
                        'type' => 'multiple',
                        'validator' => 'image',
                        'validate' => [
                            'extensions' => 'jpg, jpeg, png',
                            'maxSize' => 5 * 1024 * 1024,
                        ],
                        'main' => [
                            'width' => 300,
                            'height' => 200,
                            'quality' => 90,
                            'mode' => 'inset',
                        ],
                    ],
                ],
            ],
            'saveDates' => [
                'class' => SaveDatesRelation::class,
                'relations' => [
                    'date',
                    'datetime',
                ],
            ],
        ]);
    }

    public function beforeValidate()
    {
        if (Yii::$app instanceof \yii\web\Application) {
            $post = Yii::$app->request->post();
            if (!empty($post['Service']['competence'])) {
                foreach ($post['Service']['competence'] as $key => $tag) {
                    if ((int)$tag == 0) {
                        $this->competence[$key]->name = $tag;
                    }
                }
            }
            if (!empty($post['Service']['solvtask'])) {
                foreach ($post['Service']['solvtask'] as $key => $tag) {
                    if ((int)$tag == 0) {
                        $this->solvtask[$key]->name = $tag;
                    }
                }
            }
            if (!empty($post['Service']['target_audience'])) {
                foreach ($post['Service']['target_audience'] as $key => $tag) {
                    if ((int)$tag == 0) {
                        $this->target_audience[$key]->name = $tag;
                    }
                }
            }
        }
        $this->short_description = $this->trimNewlineTags($this->short_description);
        return parent::beforeValidate();
    }

    public function trimNewlineTags($str)
    {
        return mb_substr(trim(str_replace(['<p>', '<br>', '</p>', '</br>', '<br />', '\r', '\n'], ' ', strip_tags($str, ['br', 'p']))), 0, 200, 'UTF-8');
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->updateBitrixModel();
        parent::afterSave($insert, $changedAttributes);
    }

    /* является ли услуга опубликованной */

    /**
     * Вызов функции для обновления данных в битриксе
     *
     * @return void
     */
    public function updateBitrixModel(): void
    {
        Yii::$app->q_bitrix->push(new BitrixEntityUpdateJob([
            'model_class' => $this->serviceb24::className(),
            'id' => $this->serviceb24->id,
        ]));
    }

    // сохранение/удаление/валидация

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name' => 'Название услуги',
            'user_id' => 'Пользователь',
            'b24_service_id' => 'ELEMENT_CODE в битрикс24',
            'b24_element_id' => 'ID в битрикс24',
            'direction_id' => 'Кафедра',
            'city_id' => 'Город',
            'description' => 'Описание',
            'short_description' => 'Краткое описание',
            'price' => 'Стоимость',
            'old_price' => 'Старая цена',
            'kind' => 'Формат услуги',
            'type' => 'Тип', // не меняется, задается только при создании
            'type_id' => 'Вид услуги',
            'solvtask' => 'Решаемые задачи',
            'target_audience' => 'Целевые аудитории',
            'competence' => 'Специализации',
            'platform' => 'Платформа',
            'vis_fiz' => 'Отображать для физлиц',
            'vis_ur' => 'Отображать для юрлиц',
            'vis_expert' => 'Отображать для экспертов',
            'vis_exporg' => 'Отображать для экспертных организаций',
            'url' => 'Url-адрес',
            'place' => 'Место проведения',
            'special_descr' => 'Правила предоставления услуги',
            'price_descr' => 'Что входит в стоимость?',
            'video' => 'Ссылка на видео Youtube, VK, Rutube',
            'image' => 'Галерея фото',
            'image_loader' => 'Галерея фото',
            'videoimage' => 'Изображение для видео',
            'videoimage_loader' => 'Изображение для видео',
            'show_in_banner' => 'Отображать в баннере',
            'status' => 'Статус',
            'keywords' => 'Ключевые слова',
            'text' => 'Текст',
            'date' => 'Дата',
            'datetime' => 'Дата и время',
            'file_loader' => 'Загружаемый файл',
            'multiimage_loader' => 'Загружаемые изображения',
        ]);
    }

    public function canPublish()
    {
        $can_public = true;

        /* если услуга скрыта */
        if ($this->visible == 0) {
            $can_public = false;
        }

        if (!in_array($this->status, self::CATALOG_VISIBLE_STATUSES)) {
            $can_public = false;
        }

        /* если у услуги есть автор */
        if ($this->user) {
            /* если автор скрытый пользователь */
            if ($this->user->visible == 0) {
                $can_public = false;
            }
            /* если автор не активный пользователь */
            if ($this->user->status != UserAR::STATUS_ACTIVE) {
                $can_public = false;
            }
            /* если организация имеет разрешение на работу в маркетплейс */
            if ($this->user->organization->can_service != 1) {
                $can_public = false;
            }
        }

        /* видимость по ролям */
        if (Yii::$app->user->isGuest) {
            /* гость приравнивается к физлицу */
            $role = 'fizusr';
        } else {
            $role = Yii::$app->user->identity->userAR->role;
        }
        switch ($role) {
            /* Эксперт */
            case 'expert':
                if ($this->vis_expert == 0) {
                    $can_public = false;
                }
                break;
            /* Экспертная организация */
            case 'exporg':
                if ($this->vis_exporg == 0) {
                    $can_public = false;
                }
                break;
            /* Юрлицо */
            case 'urusr':
                if ($this->vis_ur == 0) {
                    $can_public = false;
                }
                break;
            /* Физлицо */
            case 'fizusr':
                if ($this->vis_fiz == 0) {
                    $can_public = false;
                }
                break;
            /* Для админа и МКС нет ограничений на области видимости */
            case 'admin':
            case 'mks':
                break;
            /* Для остальных область видимости как у Физлица */
            default:
                if ($this->vis_fiz == 0) {
                    $can_public = false;
                }
                break;
        }

        return $can_public;
    }

    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_PUBLIC],
            ['order', 'default', 'value' => 1],
            ['show_in_banner', 'default', 'value' => 1],
            [['order', 'visible', 'url', 'solvtask', 'target_audience', 'competence'], 'safe'],
            [['name', 'user_id', 'direction_id', 'kind', 'type', 'status'], 'required'],
            [['description', 'short_description', 'price', 'city_id', 'platform', 'old_price', 'vis_fiz', 'vis_ur', 'vis_expert', 'vis_exporg', 'type_id', 'b24_service_id', 'b24_element_id'], 'safe'],
            [['url'], 'unique'],
            [['h1_tag', 'meta_title', 'meta_description', 'meta_og_title', 'meta_og_description', 'meta_keywords', 'meta_robots_tag', 'keywords'], 'safe'],
            [['keywords'], 'limitArray', 'limit' => 5],
            [['place', 'special_descr', 'price_descr', 'video'], 'safe'],

            /* правила - как на фронте */
            [['place'], 'textLength', 'max' => 30],
            [['name'], 'textLength', 'max' => 70],
            [['short_description'], 'textLength', 'max' => 200],
            [['description'], 'textLength', 'max' => 2000],
            [['price_descr'], 'textLength', 'max' => 600],
            [['special_descr'], 'textLength', 'max' => 1200],
            [['city_id'], 'checkCity'],
            [['platform'], 'checkPlatform'],
            ['place', 'checkPlace'],
            ['video', 'multiHostVideo'],

            [['type', 'type_id'], 'required'],

            [['text', 'date', 'datetime', 'file', 'multiimage'], 'safe'],
        ];
    }

    public function checkCity($attribute, $params)
    {
        if ($this->kind == Service::KIND_OFFLINE || $this->kind == Service::KIND_HYBRID) {
            if (empty($this->city_id)) {
                $this->addError($attribute, 'Для офлайн или гибридной услуги обязательно указать город');
            }
        }
    }

    public function checkPlace($attribute, $params)
    {
        if ($this->kind == Service::KIND_OFFLINE || $this->kind == Service::KIND_HYBRID) {
            if (empty($this->place)) {
                $this->addError($attribute, 'Для офлайн или гибридной услуги обязательно указать место проведения');
            }
        }
    }

    public function checkPlatform($attribute, $params)
    {
        if ($this->kind == Service::KIND_ONLINE || $this->kind == Service::KIND_HYBRID) {
            if (empty($this->platform)) {
                $this->addError($attribute, 'Для онлайн или гибридной услуги обязательно указать платформу');
            }
        }
    }

    public function getFieldTabs()
    {
        return [
            'main' => 'Основное',
            'descr' => 'Описание',
            'seo' => 'SEO',
            'banner' => 'Баннер',
        ];
    }

    public function getFields($tab)
    {
        $fields = [
            'main' => [
                'name' => [
                    'text',
                    'hint' => 'Максимальное количество символов - 70',
                ],
                'url' => 'text',
                'status' => 'onlyview',
                'user_id' => [
                    'type' => 'options',
                    'optionList' => $this->getUserList(),
                ],
                'city_id' => [
                    'type' => 'options',
                    'optionList' => $this->getCityList(),
                ],
                'direction_id' => [
                    'type' => 'options',
                    'optionList' => $this->getDirectionList(),
                ],
                'kind' => [
                    'type' => 'options',
                    'optionList' => $this->getKindList(),
                ],
                'type' => [
                    'type' => 'options',
                    'optionList' => $this->getTypeList(),
                    'only' => ['create'],
                ],
                'type_id' => [
                    'type' => 'options',
                    'optionList' => $this->getGroupList(),
                    'htmlOptions' => [
                        'class' => 'select_pretty',
                        'style' => 'width:60%',
                        'prompt' => ['text' => 'Не задано', 'options' => ['value' => 0]],
                    ],
                ],
                'solvtask' => [
                    'type' => 'options',
                    'optionList' => $this->getSolvtaskList(true),
                    'htmlOptions' => [
                        'class' => 'select_pretty',
                        'style' => 'width:60%',
                        'multiple' => 'multiple',
                    ],
                ],
                'target_audience' => [
                    'type' => 'options',
                    'optionList' => $this->getTargetAudienceList(),
                    'htmlOptions' => [
                        'class' => 'select_pretty',
                        'style' => 'width:60%',
                        'multiple' => 'multiple',
                    ],
                ],
                'keywords' => [
                    'type' => 'options',
                    'optionList' => Keyword::getKeywordList(),
                    'htmlOptions' => [
                        'class' => 'select_pretty',
                        'style' => 'width:60%',
                        'multiple' => 'multiple',
                    ],
                ],
                'competence' => [
                    'type' => 'options',
                    'optionList' => $this->getCompetenceList(true),
                    'htmlOptions' => [
                        'class' => 'select_pretty',
                        'style' => 'width:60%',
                        'multiple' => 'multiple',
                    ],
                ],
                'short_description' => 'text',
                'description' => 'wysiwyg',
                'price' => 'integer',
                'old_price' => 'integer',
                'platform' => [
                    'type' => 'options',
                    'optionList' => $this->getPlatformList(),
                    'htmlOptions' => [
                        'class' => 'pretty_tags_ns',
                        'style' => 'width:60%',
                    ],
                ],
                'visible' => 'boolean',
                'vis_fiz' => 'boolean',
                'vis_ur' => 'boolean',
                'vis_expert' => 'boolean',
                'vis_exporg' => 'boolean',
                'order' => 'integer',
                'text' => 'text',
                'date' => 'date',
                'datetime' => 'datetime',
                'file' => [
                    'type' => 'file',
                    'hint' => 'Допустима загрузка файлов с расширениями pdf, zip, txt до 20Мб',
                ],
                'multiimage' => [
                    'type' => 'multiimage',
                    'hint' => 'Допустима загрузка изображений с расширениями jpg, jpeg, png до 5Мб каждая',
                ]
            ],
            'descr' => [
                'place' => 'textarea',
                'special_descr' => 'wysiwyg',
                'price_descr' => 'wysiwyg',
                'image' => 'multiimage',
                'video' => 'text',
                'videoimage' => 'image',
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
            ],
            'banner' => [
                'show_in_banner' => 'boolean',
            ]
        ];
        return $tab ? $fields[$tab] : $fields;
    }

    public static function getUserList($select_format = false, $roles = ['expert', 'exporg'])
    {
        if (($select_format and !isset(self::$userSelectList)) or (!$select_format and !isset(self::$userList))) {

            $items = (new \yii\db\Query())
                ->select(['user.id as id', "CONCAT(profile.name,' ',profile.surname) as halfname", 'profile.organization_name as organization_name', 'auth_assignment.item_name as role'])
                ->from('user')
                ->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id')
                ->leftJoin('profile', 'profile.user_id = user.id')
                ->leftJoin('organization', 'organization.user_id = user.id')
                ->where(['IN', 'auth_assignment.item_name', $roles])
                ->andWhere(['status' => UserAR::STATUS_ACTIVE])
                ->andWhere(['user.visible' => 1])
                ->andWhere(['organization.can_service' => 1])
                ->orderBy(['profile.surname' => 'asc', 'profile.name' => 'asc'])
                ->all();

            $result = [];
            $select_result = [];
            foreach ($items as $user) {
                $select_result[] = ['id' => $user['id'], 'text' => ($user['role'] == 'exporg' ? $user['organization_name'] : $user['halfname'])];
                $result[$user['id']] = ($user['role'] == 'exporg' ? $user['organization_name'] : $user['halfname']);
            }
            self::$userSelectList = $select_result;
            self::$userList = $result;
        }
        return $select_format ? self::$userSelectList : self::$userList;
    }

    public static function getCityList($select_format = false)
    {
        if (($select_format and !isset(self::$citySelectList)) or (!$select_format and !isset(self::$cityList))) {
            $result = ArrayHelper::map(City::find()->where(['visible' => 1])->orderBy(['order' => SORT_ASC, 'name' => SORT_ASC])->all(), 'id', 'name');
            $select_result = [];
            foreach ($result as $key => $value) {
                $select_result[] = ['id' => $key, 'text' => $value];
            }
            self::$citySelectList = $select_result;
            self::$cityList = $result;
        }
        return $select_format ? self::$citySelectList : self::$cityList;
    }

    // в админку

    public static function getDirectionList($select_format = false)
    {
        if (($select_format and !isset(self::$directionSelectList)) or (!$select_format and !isset(self::$directionList))) {
            $result = ArrayHelper::map(\app\modules\direction\models\Direction::find()->where(['visible' => 1])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');
            $select_result = [];
            foreach ($result as $key => $value) {
                $select_result[] = ['id' => $key, 'text' => $value];
            }
            self::$directionSelectList = $select_result;
            self::$directionList = $result;
        }
        return $select_format ? self::$directionSelectList : self::$directionList;
    }

    // на фронт для создания услуги

    public static function getKindList()
    {
        return [
            self::KIND_ONLINE => 'Онлайн',
            self::KIND_OFFLINE => 'Офлайн',
            self::KIND_HYBRID => 'Онлайн и Офлайн',
        ];
    }

    public static function getTypeList()
    {
        return [
            self::TYPE_TYPICAL => 'Типовая',
            self::TYPE_CUSTOM => 'Индивидуальная',
        ];
    }

    public static function getGroupList($ignore_forfilters = false, $select_format = false)
    {
        if (($select_format and !isset(self::$groupSelectList)) or (!$select_format and !isset(self::$groupList))) {
            $result = ArrayHelper::map(\app\modules\service_type\models\ServiceType::find()->all(), 'id', 'name');
            $select_result = [];
            foreach ($result as $key => $value) {
                $select_result[] = ['id' => $key, 'text' => $value];
            }
            self::$groupSelectList = $select_result;
            self::$groupList = $result;
        }
        return $select_format ? self::$groupSelectList : self::$groupList;
    }

    public static function getSolvtaskList($ignore_forfilters = false, $select_format = false)
    {
        if (($select_format and !isset(self::$solvtaskSelectList)) or (!$select_format and !isset(self::$solvtaskList))) {
            $list = \app\modules\reference\models\Solvtask::find()->where(['visible' => 1]);
            if (!$ignore_forfilters) {
                $list->andWhere(['OR',
                    ['forfilter' => 1],
                    ['owner_id' => Yii::$app->user->isGuest ? null : Yii::$app->user->identity->userAR->id],
                ]);
            }
            $result = ArrayHelper::map($list->all(), 'id', 'name');
            $select_result = [];
            foreach ($result as $key => $value) {
                $select_result[] = ['id' => $key, 'text' => $value];
            }
            self::$solvtaskSelectList = $select_result;
            self::$solvtaskList = $result;
        }
        return $select_format ? self::$solvtaskSelectList : self::$solvtaskList;
    }

    public static function getTargetAudienceList($select_format = false)
    {
        if (($select_format and !isset(self::$targetaurienceSelectList)) or (!$select_format and !isset(self::$targetaurienceList))) {
            $result = ArrayHelper::map(\app\modules\target_audience\models\TargetAudience::find()->where(['visible' => 1])->all(), 'id', 'name');
            $select_result = [];
            foreach ($result as $key => $value) {
                $select_result[] = ['id' => $key, 'text' => $value];
            }
            self::$targetaurienceSelectList = $select_result;
            self::$targetaurienceList = $result;
        }
        return $select_format ? self::$targetaurienceSelectList : self::$targetaurienceList;
    }

    public static function getCompetenceList($ignore_forfilters = false, $select_format = false)
    {
        if (($select_format and !isset(self::$competenceSelectList)) or (!$select_format and !isset(self::$competenceList))) {
            $list = \app\modules\reference\models\Competence::find()->where(['visible' => 1]);
            if (!$ignore_forfilters) {
                $list->andWhere(['OR',
                    ['forfilter' => 1],
                    ['owner_id' => Yii::$app->user->isGuest ? null : Yii::$app->user->identity->userAR->id],
                ]);
            }
            $result = ArrayHelper::map($list->all(), 'id', 'name');
            $select_result = [];
            foreach ($result as $key => $value) {
                $select_result[] = ['id' => $key, 'text' => $value];
            }
            self::$competenceSelectList = $select_result;
            self::$competenceList = $result;
        }
        return $select_format ? self::$competenceSelectList : self::$competenceList;
    }

    public function getPlatformList($select_format = false)
    {
        if (($select_format and !isset(self::$platformSelectList)) or (!$select_format and !isset(self::$platformList))) {
            $result = ArrayHelper::map(\app\modules\reference\models\Platform::find()->where(['visible' => 1])->all(), 'name', 'name');
            if (!empty($this->platform) && !in_array($this->platform, $result)) {
                $result = array_merge($result, [$this->platform => $this->platform]);
            }
            $select_result = [];
            foreach ($result as $key => $value) {
                $select_result[] = ['id' => $key, 'text' => $value];
            }
            self::$platformSelectList = $select_result;
            self::$platformList = $result;
        }
        return $select_format ? self::$platformSelectList : self::$platformList;
    }

    public function getUser()
    {
        return $this->hasOne(UserAR::class, ['id' => 'user_id']);
    }

    public function getKeywords()
    {
        return $this->hasMany(Keyword::class, ['id' => 'keyword_id'])
            ->viaTable('models_keywords', ['entity_id' => 'id', 'entity_model' => 'keywordsClassName']);
    }

    public function currentPlatformList()
    {
        $result = ArrayHelper::map(\app\modules\reference\models\Platform::find()->where(['visible' => 1])->all(), 'name', 'name');
        $userPlatform = $this->currentModeration && !empty($this->currentModeration->platform) ? $this->currentModeration->platform : $this->platform;
        if (!empty($userPlatform) && !in_array($userPlatform, $result)) {
            $result = array_merge($result, [$userPlatform => $userPlatform]);
        }

        return $result;
    }

    public function getTarget_audience()
    {
        return $this->hasMany(\app\modules\target_audience\models\TargetAudience::class, ['id' => 'target_audience_id'])->viaTable('service_ref_target_audience', ['service_id' => 'id']);
    }

    public function getSolvtask()
    {
        return $this->hasMany(\app\modules\reference\models\Solvtask::class, ['id' => 'solvtask_id'])->viaTable('service_ref_solvtask', ['service_id' => 'id']);
    }

    public function getCompetence()
    {
        return $this->hasMany(\app\modules\reference\models\Competence::class, ['id' => 'competence_id'])->viaTable('service_ref_competence', ['service_id' => 'id']);
    }

    public function getServiceType()
    {
        return $this->hasOne(\app\modules\service_type\models\ServiceType::class, ['id' => 'type_id']);
    }

    public function getDirection()
    {
        return $this->hasOne(\app\modules\direction\models\Direction::class, ['id' => 'direction_id']);
    }

    public function getQueries()
    {
        return $this->hasMany(Queries::class, ['service_id' => 'id']);
    }

    public function getOrders()
    {
        return $this->hasMany(Order::class, ['service_id' => 'id']);
    }

    public function getCity()
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    public function getKindName($type = false)
    {
        $stat = ($type !== false) ? $type : $this->kind;
        $type_list = $this->getKindList();
        return $type_list[$stat] ? $type_list[$stat] : 'Не определен';
    }

    public function getTypeName($type = false)
    {
        $stat = ($type !== false) ? $type : $this->type;
        $type_list = $this->getTypeList();
        return $type_list[$stat] ? $type_list[$stat] : 'Не определен';
    }

    public function getSolvtaskNames()
    {
        return implode(", ", ArrayHelper::map($this->solvtask, 'name', 'name'));
    }

    public function getTarget_audienceNames()
    {
        return implode(", ", ArrayHelper::map($this->target_audience, 'name', 'name'));
    }

    public function getCompetenceNames()
    {
        return implode(", ", ArrayHelper::map($this->competence, 'name', 'name'));
    }

    public function getServiceb24($just_look = false)
    {
        $service = \app\modules\bitrixcrm\models\Service::find()->where(['entity_id' => $this->id, 'type' => Bitrixcrm::BITRIX_TYPE_GL_SERVICES, 'entity_model' => Service::class])->one();
        if (!$service and !$just_look) {
            // если лида еще не было, то создаем новый.
            $service = new \app\modules\bitrixcrm\models\Service();
            $service->entity_model = Service::class;
            $service->entity_id = $this->id;
            $service->save();
        }
        return $service;
    }

    public function getUrlPath($params = false)
    {
        $service_model = \app\modules\pages\models\Servicepage::find()->where(['model' => 'app\modules\pages\models\Servicepage', 'visible' => 1])->one();
        if ($service_model) {
            $path = $service_model->getTreePath();
            $path = trim($path, '/') . '/' . $this->url;
        }
        $query = '';
        if ($params) {
            $query = '?' . http_build_query($params);
        }
        return '/' . trim($path, '/') . '/' . $query;
    }

    public function getImage()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => Service::class, 'keeper_field' => 'image']);
    }

    public function getVideoimage()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => Service::class, 'keeper_field' => 'videoimage']);
    }

    /* возвращает все заявки на модерацию */

    public function getMeta_og_image()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => Service::class, 'keeper_field' => 'meta_og_image']);
    }

    /* возвращает модерацию для работы из редактирования услуги */

    /** Нужно для битрикса и миграции статусов */
    public function getModerationStatus()
    {
        $ms = $this->getCurrentModeration();
        if ($ms) {
            return $ms->status;
        }
        $ms = $this->getProcessedModeration();
        if ($ms) {
            return $ms->status;
        }
        $ms = $this->getLastModeration();
        if ($ms) {
            return $ms->status;
        }

        return Servicemoder::STATUS_ACCEPTED;

    }

    /* возвращает модерацию, которая в данный момент на рассмотрении */

    public function getCurrentModeration()
    {
        return $this->getServicemoder()->andWhere(['IN', 'status', [Servicemoder::STATUS_NEW, Servicemoder::STATUS_PENDING]])->one();
    }

    /* последняя обработанная модерация (для отображения причины отказа или доработки) */

    public function getServicemoder()
    {
        return $this->hasMany(Servicemoder::class, ['service_id' => 'id']);
    }

    /* при создании новой модерации нужно заполнить все поля текущими данными услуги (кроме изображений) */
    /* запись о модерации здесь не должна сохраняться. Только заполняться. */

    public function getProcessedModeration()
    {
        return $this->getServicemoder()->andWhere(['status' => Servicemoder::STATUS_MODERATE])->orderBy(['created_at' => SORT_DESC])->one();
    }

    /* Учитывает основные параметры видимости */

    public function getLastModeration()
    {
        return $this->getServicemoder()->andWhere(['IN', 'status', [Servicemoder::STATUS_PENDING, Servicemoder::STATUS_ACCEPTED, Servicemoder::STATUS_DECLINED]])->orderBy(['created_at' => SORT_DESC])->one();
    }

    public function addNewModeration()
    {
        $moderation = new Servicemoder();
        $moderation->status = Servicemoder::STATUS_NEW;

        $moderation->service_id = $this->id;
        $moderation->kind = $this->kind;
        $moderation->name = $this->name;
        $moderation->description = $this->description;
        $moderation->short_description = $this->short_description;
        $moderation->special_descr = $this->special_descr;
        $moderation->price_descr = $this->price_descr;
        $moderation->video = $this->video;
        $moderation->direction_id = $this->direction_id;
        $moderation->city_id = $this->city_id;
        $moderation->price = $this->price;
        $moderation->old_price = $this->old_price;
        $moderation->type_id = $this->type_id;
        $moderation->solvtask = $this->solvtask;
        $moderation->target_audience = $this->target_audience;
        $moderation->competence = $this->competence;
        $moderation->platform = $this->platform;
        $moderation->url = $this->url;
        $moderation->place = $this->place;

        return $moderation;
    }

    /* Для вывода страницы просмотра услуги */

    public function setVisibility(string $type): void
    {
        switch ($type) {
            case 'all':
                $this->vis_fiz = 1;
                $this->vis_ur = 1;
                break;
            case 'experts':
                $this->vis_fiz = 0;
                $this->vis_ur = 0;
                break;
        }
        $this->vis_expert = 1;
        $this->vis_exporg = 1;
    }

    public function getFile()
    {
        return $this
            ->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])
            ->andWhere(['keeper_class' => Service::class, 'keeper_field' => 'file']);
    }

    public function getMultiimage()
    {
        return $this
            ->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])
            ->andWhere(['keeper_class' => Service::class, 'keeper_field' => 'multiimage'])
            ->orderBy(['order' => SORT_ASC]);
    }
}
