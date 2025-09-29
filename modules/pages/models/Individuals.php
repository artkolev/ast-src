<?php
/**
 * @modelDescr Страница с дизайном Физлицам
 */

namespace app\modules\pages\models;

use app\modules\admin\behaviors\ClearRelation;
use app\modules\admin\behaviors\SaveFilesRelation;
use app\modules\admin\behaviors\SaveOneVarRelation;
use app\modules\admin\components\FilestoreModel;
use Yii;
use yii\helpers\Url;

class Individuals extends Page
{
    public static $name_for_list = "страницу Физлицам";
    public $view = 'individuals';
    public $action_id = 'pages/pages/individuals';
    public $fizlic_title;
    public $fizlic_text;
    public $fizlic_find_specialist_text;
    public $fizlic_find_specialist_url;
    public $fizlic_send_request_text;
    public $fizlic_video_url;
    public $fizlic_video_title;
    public $fizlic_video_text;
    public $fizlic_video_checkbox;
    public $fizlic_i_find_title;
    public $fizlic_i_find_col1_text;
    public $fizlic_i_find_col1_url;
    public $fizlic_i_find_col2_text;
    public $fizlic_i_find_col2_url;
    public $fizlic_i_find_col3_text;
    public $fizlic_i_find_col3_url;
    public $fizlic_why_we_title;
    public $fizlic_why_we_col1_text;
    public $fizlic_why_we_col2_text;
    public $fizlic_why_we_col3_text;
    public $fizlic_why_we_find_specialist_text;
    public $fizlic_why_we_find_specialist_url;
    public $fizlic_why_we_send_request_text;
    public $fizlic_actual_theme_title;
    public $fizlic_reviews_title;
    public $fizlic_for_whom_title;
    public $fizlic_for_whom_block_fizlic_title;
    public $fizlic_for_whom_block_fizlic_text;
    public $fizlic_for_whom_block_fizlic_btn_text;
    public $fizlic_for_whom_block_fizlic_btn_url;
    public $fizlic_for_whom_block_business_title;
    public $fizlic_for_whom_block_business_text;
    public $fizlic_for_whom_block_business_btn_text;
    public $fizlic_for_whom_block_business_btn_url;

    public $video_image_loader;
    public $image2_loader;
    public $image3_loader;

    public function behaviors()
    {
        $parent_behaviors = parent::behaviors();
        return array_merge($parent_behaviors, [
            'clearDelete' => [
                'class' => ClearRelation::class,
                'relations' => [
                    'individualthemes',
                    'individualacademlist',
                    'individualreviews',
                ],
            ],
            'saveFiles' => [
                'class' => SaveFilesRelation::class,
                'relations' => [
                    'video_image' => [
                        'type' => 'single', // multiple
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, jpeg, png, svg',
                            'maxSize' => 2 * 1024 * 1024, // 3Мб
                        ],
                        'main' => [
                            'width' => 635,
                            'height' => 477,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'image2' => [
                        'type' => 'single', // multiple
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'svg',
                            'maxSize' => 2 * 1024 * 1024, // 3Мб
                        ],
                    ],
                    'image3' => [
                        'type' => 'single', // multiple
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'svg',
                            'maxSize' => 2 * 1024 * 1024, // 3Мб
                        ],
                    ],
                ]
            ],
            'saveOneVar' => [
                'class' => SaveOneVarRelation::class,
                'relations' => [
                    'single' => [
                        'fizlic_title',
                        'fizlic_text',
                        'fizlic_find_specialist_text',
                        'fizlic_find_specialist_url',
                        'fizlic_send_request_text',
                        'fizlic_video_url',
                        'fizlic_video_title',
                        'fizlic_video_text',
                        'fizlic_video_checkbox',
                        'fizlic_i_find_title',
                        'fizlic_i_find_col1_text',
                        'fizlic_i_find_col1_url',
                        'fizlic_i_find_col2_text',
                        'fizlic_i_find_col2_url',
                        'fizlic_i_find_col3_text',
                        'fizlic_i_find_col3_url',
                        'fizlic_why_we_title',
                        'fizlic_why_we_col1_text',
                        'fizlic_why_we_col2_text',
                        'fizlic_why_we_col3_text',
                        'fizlic_why_we_find_specialist_text',
                        'fizlic_why_we_find_specialist_url',
                        'fizlic_why_we_send_request_text',
                        'fizlic_actual_theme_title',
                        'fizlic_reviews_title',
                        'fizlic_for_whom_title',
                        'fizlic_for_whom_block_fizlic_title',
                        'fizlic_for_whom_block_fizlic_text',
                        'fizlic_for_whom_block_fizlic_btn_text',
                        'fizlic_for_whom_block_fizlic_btn_url',
                        'fizlic_for_whom_block_business_title',
                        'fizlic_for_whom_block_business_text',
                        'fizlic_for_whom_block_business_btn_text',
                        'fizlic_for_whom_block_business_btn_url',
                    ],
                ],
            ],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'fizlic_title' => 'Заголовок страницы',
            'fizlic_text' => 'Подзаголовок страницы',
            'fizlic_find_specialist_text' => 'Текст на кнопке Найти специалиста',
            'fizlic_find_specialist_url' => 'Ссылка на кнопке Найти специалиста',
            'fizlic_send_request_text' => 'Текст на кнопке Отправить запрос',
            'video_image' => 'Картинка заглушки для видео',
            'video_image_loader' => 'Картинка заглушки для видео',
            'fizlic_video_url' => 'Ссылка на видео Youtube, VK, Rutube',
            'fizlic_video_title' => 'Заголовок к видео',
            'fizlic_video_text' => 'Текстовое поле под видео',
            'fizlic_video_checkbox' => 'Не отображать блок видео',
            'fizlic_i_find_title' => 'Заголовок блока Я ищу',
            'fizlic_i_find_col1_text' => 'Текст Я ищу (круг 1)',
            'fizlic_i_find_col1_url' => 'Ссылка Я ищу (круг 1)',
            'fizlic_i_find_col2_text' => 'Текст Я ищу (круг 2)',
            'fizlic_i_find_col2_url' => 'Ссылка Я ищу (круг 2)',
            'fizlic_i_find_col3_text' => 'Текст Я ищу (круг 3)',
            'fizlic_i_find_col3_url' => 'Ссылка Я ищу (круг 3)',
            'fizlic_why_we_title' => 'Заголовок Почему мы',
            'fizlic_why_we_col1_text' => 'Текст блока Почему мы (колонка 1)',
            'fizlic_why_we_col2_text' => 'Текст блока Почему мы (колонка 2)',
            'fizlic_why_we_col3_text' => 'Текст блока Почему мы (колонка 3)',
            'fizlic_why_we_find_specialist_text' => 'Текст на кнопке Найти специалиста',
            'fizlic_why_we_find_specialist_url' => 'Ссылка на кнопке Найти специалиста',
            'fizlic_why_we_send_request_text' => 'Текст на кнопке Отправить запрос',
            'fizlic_actual_theme_title' => 'Подзаголовок Актуальные темы',
            'fizlic_reviews_title' => 'Подзаголовок Отзывы',
            'fizlic_for_whom_title' => 'Подзаголовок Для кого',
            'fizlic_for_whom_block_fizlic_title' => 'Подзаголовок в блоке Для Физлиц',
            'fizlic_for_whom_block_fizlic_text' => 'Текст в блоке Для Физлиц',
            'fizlic_for_whom_block_fizlic_btn_text' => 'Текст кнопки в блоке Для Физлиц',
            'fizlic_for_whom_block_fizlic_btn_url' => 'Ссылка на кнопке в блоке Для Физлиц',
            'image2' => 'Картинка в блоке Для Физлиц',
            'image2_loader' => 'Картинка в блоке Для Физлиц',
            'fizlic_for_whom_block_business_title' => 'Подзаголовок в блоке Для Бизнеса',
            'fizlic_for_whom_block_business_text' => 'Текст в блоке Для Бизнеса',
            'fizlic_for_whom_block_business_btn_text' => 'Текст кнопки в блоке Для Бизнеса',
            'fizlic_for_whom_block_business_btn_url' => 'Ссылка на кнопке в блоке Для Бизнеса',
            'image3' => 'Картинка в блоке Для Бизнеса',
            'image3_loader' => 'Картинка в блоке Для Бизнеса',
        ]);
    }

    // сохранение/удаление/валидация
    public function rules()
    {
        return array_merge(parent::rules(), [
            [[
                'fizlic_title',
                'fizlic_text',
                'fizlic_find_specialist_text',
                'fizlic_find_specialist_url',
                'fizlic_send_request_text',
                'fizlic_video_url',
                'fizlic_video_title',
                'fizlic_video_text',
                'fizlic_video_checkbox',
                'fizlic_i_find_title',
                'fizlic_i_find_col1_text',
                'fizlic_i_find_col1_url',
                'fizlic_i_find_col2_text',
                'fizlic_i_find_col2_url',
                'fizlic_i_find_col3_text',
                'fizlic_i_find_col3_url',
                'fizlic_why_we_title',
                'fizlic_why_we_col1_text',
                'fizlic_why_we_col2_text',
                'fizlic_why_we_col3_text',
                'fizlic_why_we_find_specialist_text',
                'fizlic_why_we_find_specialist_url',
                'fizlic_why_we_send_request_text',
                'fizlic_actual_theme_title',
                'fizlic_reviews_title',
                'fizlic_for_whom_title',
                'fizlic_for_whom_block_fizlic_title',
                'fizlic_for_whom_block_fizlic_text',
                'fizlic_for_whom_block_fizlic_btn_text',
                'fizlic_for_whom_block_fizlic_btn_url',
                'fizlic_for_whom_block_business_title',
                'fizlic_for_whom_block_business_text',
                'fizlic_for_whom_block_business_btn_text',
                'fizlic_for_whom_block_business_btn_url',
            ], 'safe'],
        ]);
    }

    public function getFieldTabs()
    {
        return [
            'main' => 'Основное',
            'info' => 'Дополнительные поля',
            'video' => 'Блок видео',
            'isearch' => 'Блок Я ищу',
            'whywe' => 'Блок Почему мы',
            'forwhom' => 'Блок Для кого',
            'titles' => 'Заголовки блоков',
            'seo' => 'SEO',
        ];
    }

    public function getRelationTabs()
    {
        return [
            'actual' => 'Блок Актуальные темы',
            'reviews' => 'Блок Отзывы',
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
            'info' => [
                'fizlic_title' => 'text',
                'fizlic_text' => 'wysiwyg',
                'fizlic_find_specialist_text' => 'text',
                'fizlic_find_specialist_url' => 'text',
                'fizlic_send_request_text' => 'text',
            ],
            'video' => [
                'fizlic_video_checkbox' => 'boolean',
                'video_image' => 'image',
                'fizlic_video_url' => 'text',
                'fizlic_video_title' => 'text',
                'fizlic_video_text' => 'wysiwyg',
            ],
            'isearch' => [
                'fizlic_i_find_title' => 'text',
                'fizlic_i_find_col1_text' => 'text',
                'fizlic_i_find_col1_url' => 'text',
                'fizlic_i_find_col2_text' => 'text',
                'fizlic_i_find_col2_url' => 'text',
                'fizlic_i_find_col3_text' => 'text',
                'fizlic_i_find_col3_url' => 'text',
            ],
            'whywe' => [
                'fizlic_why_we_title' => 'text',
                'fizlic_why_we_col1_text' => 'wysiwyg',
                'fizlic_why_we_col2_text' => 'wysiwyg',
                'fizlic_why_we_col3_text' => 'wysiwyg',
                'fizlic_why_we_find_specialist_text' => 'text',
                'fizlic_why_we_find_specialist_url' => 'text',
                'fizlic_why_we_send_request_text' => 'text',
            ],
            'titles' => [
                'fizlic_reviews_title' => 'text',
                'fizlic_actual_theme_title' => 'text',
            ],
            'forwhom' => [
                'fizlic_for_whom_title' => 'text',
                'fizlic_for_whom_block_fizlic_title' => 'text',
                'fizlic_for_whom_block_fizlic_text' => 'textarea',
                'fizlic_for_whom_block_fizlic_btn_text' => 'text',
                'fizlic_for_whom_block_fizlic_btn_url' => 'text',
                'image2' => 'image',
                'fizlic_for_whom_block_business_title' => 'text',
                'fizlic_for_whom_block_business_text' => 'textarea',
                'fizlic_for_whom_block_business_btn_text' => 'text',
                'fizlic_for_whom_block_business_btn_url' => 'text',
                'image3' => 'image',
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
            ],
        ];
        return $tab ? $fields[$tab] : $fields;
    }

    public function getRelations($tab)
    {
        $relations = [
            'actual' => [
                'individualthemes' => [
                    'type' => 'relation',
                    'buttons' => [
                        'add_page' => [
                            'class' => 'success',
                            'name' => 'Создать тему',
                            'url' => Url::toRoute(['/admin/individualthemes/default/create', 'Individualthemes' => ['page_id' => $this->id], 'return_url' => '/admin/' . Yii::$app->controller->module->id . '/' . Yii::$app->controller->id . '/update/?id=' . $this->id, 'anchor' => 'actual']),
                        ],
                    ],
                    'search_class' => '\app\modules\individualthemes\models\IndividualthemesSearch',
                    'title' => 'Редактирование тем',
                ],
                'individualacademlist' => [
                    'type' => 'relation',
                    'buttons' => [
                        'add_page' => [
                            'class' => 'success',
                            'name' => 'Добавить участника',
                            'url' => Url::toRoute(['/admin/individualacademlist/default/create', 'Individualacademlist' => ['page_id' => $this->id], 'return_url' => '/admin/' . Yii::$app->controller->module->id . '/' . Yii::$app->controller->id . '/update/?id=' . $this->id, 'anchor' => 'actual']),
                        ],
                    ],
                    'search_class' => '\app\modules\individualacademlist\models\IndividualacademlistSearch',
                    'title' => 'Редактирование ученого совета',
                ],
            ],
            'reviews' => [
                'individualreviews' => [
                    'type' => 'relation',
                    'buttons' => [
                        'add_page' => [
                            'class' => 'success',
                            'name' => 'Создать отзыв',
                            'url' => Url::toRoute(['/admin/individualreviews/default/create', 'Individualreviews' => ['page_id' => $this->id], 'return_url' => '/admin/' . Yii::$app->controller->module->id . '/' . Yii::$app->controller->id . '/update/?id=' . $this->id]),
                        ],
                    ],
                    'search_class' => '\app\modules\individualreviews\models\IndividualreviewsSearch',
                    'title' => 'Редактирование отзывов',
                ],
            ],
        ];
        return $tab ? $relations[$tab] : $relations;
    }

    public function getVideo_image()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => Individuals::class, 'keeper_field' => 'video_image']);
    }

    public function getImage2()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => Individuals::class, 'keeper_field' => 'image2']);
    }

    public function getImage3()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => Individuals::class, 'keeper_field' => 'image3']);
    }

    public function getIndividualthemes()
    {
        return $this->hasMany(\app\modules\individualthemes\models\Individualthemes::class, ['page_id' => 'id'])->andWhere(['visible' => 1])->orderBy(['order' => SORT_ASC]);
    }

    public function getIndividualacademlist()
    {
        return $this->hasMany(\app\modules\individualacademlist\models\Individualacademlist::class, ['page_id' => 'id'])->andWhere(['visible' => 1])->orderBy(['order' => SORT_ASC]);
    }

    public function getIndividualreviews()
    {
        return $this->hasMany(\app\modules\individualreviews\models\Individualreviews::class, ['page_id' => 'id'])->andWhere(['visible' => 1])->orderBy(['order' => SORT_ASC]);
    }
}
