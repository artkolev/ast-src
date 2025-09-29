<?php

/**
 * @modelDescr Текстовая страница сайта. Базовая модель для создания остальных страниц.
 */

namespace app\modules\pages\models;

use app\interfaces\SitemapInterface;
use app\modules\admin\behaviors\SaveFilesRelation;
use app\modules\admin\behaviors\SaveSetsBehaviour;
use app\modules\admin\behaviors\UrlBehaviour;
use app\modules\admin\components\DeepModel;
use app\modules\admin\components\FilestoreModel;
use Yii;

class Page extends DeepModel implements SitemapInterface
{
    /* используется при формировании ссылки "Посмотреть на сайте" в гриде в админке */
    public static $name_for_list = "страницу";
    public static $treePath = [];
    public $meta_og_image_loader;
    public $accessLevel = 'free';
    public $view = 'page';
    public $action_id = 'pages/pages/page';

    public static function instantiate($row)
    {
        // если класса модели не существует - грузим с помощью Page
        if (!empty($row['model']) && file_exists(Yii::getAlias('@' . str_replace("\\", '/', $row['model'])) . '.php')) {
            return new $row['model']();
        }
        return new self();

    }

    public static function tableName()
    {
        return '{{%pages}}';
    }

    public static function findForSitemap()
    {
        return Page::find()->where(['visible' => 1, 'unset_from_sitemap' => 0]);
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'urlBehaviour' => [
                'class' => UrlBehaviour::class,
                'attributes' => ['name'],
            ],
            'meta_robots' => [
                'class' => SaveSetsBehaviour::class,
                'attributes' => ['meta_robots_tag'],
            ],
            'saveFiles' => [
                'class' => SaveFilesRelation::class,
                'file_path' => 'files/upload/page/',
                'relations' => [
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
                ],
            ]
        ]);
    }

    // сохранение/удаление/валидация

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name' => 'Название',
            'parent_id' => 'Верхний уровень',
            'content' => 'Содержимое',
            'url' => 'Url адрес',
            'order' => 'Порядок',
            'visible' => 'Отображать',
            'start_module' => 'Подключает модуль',
            'unset_from_sitemap' => 'Исключить страницу из sitemap.xml',
            'model' => 'Модель',
        ]);
    }

    public function rules()
    {
        return [
            ['unset_from_sitemap', 'default', 'value' => 0],
            ['order', 'default', 'value' => 1],
            ['model', 'default', 'value' => static::class],
            [['name', 'content', 'url', 'order', 'visible', 'parent_id', 'start_module'], 'safe'],
            [['name'], 'required'],
            [['h1_tag', 'meta_title', 'meta_description', 'meta_og_title', 'meta_og_description', 'meta_keywords', 'unset_from_sitemap', 'meta_robots_tag'], 'safe'],
        ];
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
                'content' => [
                    'type' => 'wysiwyg',
                    'CKconfig' => \app\helpers\ckeditor\CKConfig::ADMIN_DEFAULT_PAGE,
                ],
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

    public function getUrlPath($params = false)
    {
        if (!isset(self::$treePath) or !isset(self::$treePath[$this->id])) {
            self::$treePath[$this->id] = $this->getTreePath();
        }
        $query = '';
        if ($params) {
            $query = '?' . http_build_query($params);
        }
        return '/' . trim(self::$treePath[$this->id], '/') . '/' . $query;
    }

    public function canPublish()
    {
        return $this->visible;
    }

    public function getMeta_og_image()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => Page::class, 'keeper_field' => 'meta_og_image']);
    }
}
