<?php
/**
 * @modelDescr Страница с дизайном Академия HR-менеджерам
 */

namespace app\modules\pages\models;

use app\modules\admin\behaviors\ClearRelation;
use app\modules\admin\behaviors\SaveFilesRelation;
use app\modules\admin\behaviors\SaveOneVarRelation;
use app\modules\admin\behaviors\Serialize;
use app\modules\admin\components\FilestoreModel;
use app\modules\lenta\models\Project;
use app\modules\users\models\UserAR;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class HrsPage extends Page
{
    public static $name_for_list = "Академия HR-менеджерам";
    public static $count_for_list = 1;
    public static $expertsSelectList;
    public static $expertsList;
    public $view = 'hrs';
    public $action_id = 'pages/pages/hrs';
    public $image_loader;
    public $image_mobile_loader;

    public $problematic;
    public $proposal;
    public $experts;
    public $faq;
    public $projects;

    public $block1_title;
    public $block1_left_button_title;
    public $block1_left_button_url;
    public $block1_right_button_title;
    public $block1_right_button_url;
    public $block1_span;
    public $block_problematic_title;
    public $block_problematic_text;
    public $block_proposal_title;
    public $block_proposal_url;
    public $block_experts_title;
    public $block_personal_title;
    public $block_personal_text;
    public $block_join_title;
    public $block_join_text;
    public $block_personal_url;
    public $block_smi_title;
    public $block_smi_url;
    public $block_faq_title;
    public $block_projects_title;
    public $block_projects_url;

    public function getFieldTabs()
    {
        return [
            'main' => 'Основное',
            'problematic' => 'Блок Проблематики',
            'proposal' => 'Как получать услуги Академии',
            'join' => 'Привлекайте проверенных экспертов',
            'experts' => 'Эксперты',
            'titles' => 'Блок Академия в СМИ',
            'projects' => 'Проекты',
            'faq' => 'Частые вопросы',
            'personal' => 'Персональная консультация',
            'seo' => 'SEO',
        ];
    }

    public function getRelationTabs()
    {
        return [
            'features' => 'Возможности',
            'smi' => 'Публикации в СМИ',
        ];
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'image' => 'Изображение',
            'image_loader' => 'Изображение',
            'image_mobile' => 'Изображение для мобильных',
            'image_mobile_loader' => 'Изображение для мобильных',
            'problematic' => 'Блок Проблематики',
            'faq' => 'Частые вопросы',
            'quotes' => 'Цитаты',
            'features' => 'Руководителям и организациям',
            'proposal' => 'Как получить корпоративное предложение',
            'problems' => 'Эксперты помогают',
            'experts' => 'Эксперты',
            'projects' => 'Проекты',
            'smi' => 'Академия в СМИ',
            'block1_span' => 'Подсвеченная часть заголовка первого блока',
            'block1_title' => 'Заголовок первого блока',
            'block1_left_button_title' => 'Текст левой кнопки',
            'block1_left_button_url' => 'Ссылка на левой кнопке',
            'block1_right_button_title' => 'Текст правой кнопки',
            'block1_right_button_url' => 'Ссылка на правой кнопке',
            'block_problematic_title' => 'Заголовок блока',
            'block_problematic_text' => 'Текст блока',
            'block_features_text' => 'Текст блока',
            'block_proposal_title' => 'Заголовок блока',
            'block_proposal_url' => 'Ссылка блока',
            'block_experts_title' => 'Заголовок блока',
            'block_personal_title' => 'Заголовок блока',
            'block_personal_text' => 'Текст блока',
            'block_join_title' => 'Заголовок блока',
            'block_join_text' => 'Текст блока',
            'block_personal_url' => 'Ссылка блока',
            'block_smi_title' => 'Заголовок блока',
            'block_smi_url' => 'Ссылка "Смотреть все"',
            'block_faq_title' => 'Заголовок блока',
            'block_projects_title' => 'Заголовок блока',
            'block_projects_url' => 'Ссылка блока',
        ]);
    }

    public function behaviors()
    {
        $parent_behaviors = parent::behaviors();
        return array_merge($parent_behaviors, [
            'clearDelete' => [
                'class' => ClearRelation::class,
                'relations' => [
                    'smi',
                    'features',
                ],
            ],
            'saveOneVar' => [
                'class' => SaveOneVarRelation::class,
                'relations' => [
                    'single' => [
                        'block1_span',
                        'block1_title',
                        'block1_left_button_title',
                        'block1_left_button_url',
                        'block1_right_button_title',
                        'block1_right_button_url',
                        'block_problematic_title',
                        'block_problematic_text',
                        'block_proposal_title',
                        'block_proposal_url',
                        'block_experts_title',
                        'block_personal_title',
                        'block_personal_text',
                        'block_join_title',
                        'block_join_text',
                        'block_personal_url',
                        'block_faq_title',
                        'block_smi_title',
                        'block_smi_url',
                        'block_projects_title',
                        'block_projects_url',
                        'problematic',
                        'proposal',
                        'faq',
                        'experts',
                        'projects',
                    ],
                ],
            ],
            'serialize' => [
                'class' => Serialize::class,
                'relations' => [
                    'problematic',
                    'proposal',
                    'faq',
                    'experts',
                    'projects',
                ],
            ],
            'saveFiles' => [
                'class' => SaveFilesRelation::class,
                'relations' => [
                    'image' => [
                        'type' => 'single', // multiple
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, jpeg, png',
                            'maxSize' => 2 * 1024 * 1024, // 3Мб
                        ],
                        'main' => [
                            'width' => 895,
                            'height' => 600,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'image_mobile' => [
                        'type' => 'single', // multiple
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, jpeg, png',
                            'maxSize' => 2 * 1024 * 1024, // 3Мб
                        ],
                        'main' => [
                            'width' => 320,
                            'height' => 160,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                ]
            ],
        ]);
    }

    public function getFields($tab)
    {
        $fields = [
            'main' => [
                'name' => 'text',
                'block1_span' => 'text',
                'block1_title' => 'text',
                'block1_left_button_title' => 'text',
                'block1_left_button_url' => 'text',
                'block1_right_button_title' => 'text',
                'block1_right_button_url' => 'text',
                'url' => 'text',
                'content' => 'wysiwyg',
                'image' => 'image',
                'image_mobile' => 'image',
                'order' => 'integer',
                'visible' => 'boolean',
            ],
            'problematic' => [
                'block_problematic_title' => 'text',
                'block_problematic_text' => 'text',
                'problematic' => [
                    'type' => 'multifields',
                    'fields' => [
                        'title' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Заголовок',
                        ],
                        'text' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Текст',
                        ],
                    ],
                ],
            ],
            'proposal' => [
                'block_proposal_title' => 'text',
                'block_proposal_url' => 'text',
                'proposal' => [
                    'type' => 'multifields',
                    'fields' => [
                        'title' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Заголовок',
                        ],
                        'text' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Текст',
                        ],
                        'url' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Ссылка',
                        ],
                    ],
                ],
            ],
            'join' => [
                'block_join_title' => 'text',
                'block_join_text' => 'text',
            ],
            'experts' => [
                'block_experts_title' => 'text',
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
            'titles' => [
                'block_smi_title' => 'text',
                'block_smi_url' => 'text',
            ],
            'projects' => [
                'block_projects_title' => 'text',
                'block_projects_url' => 'text',
                'projects' => [
                    'type' => 'multifields',
                    'fields' => [
                        'project' => [
                            'visible_field' => true,
                            'type' => 'options',
                            'name' => 'Проект',
                            'options_list' => $this->getProjectsList(),
                            'htmlClass' => 'select_pretty',
                        ],
                    ],
                ],
            ],
            'faq' => [
                'block_faq_title' => 'text',
                'faq' => [
                    'type' => 'multifields',
                    'fields' => [
                        'title' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Вопрос',
                        ],
                        'text' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Ответ',
                        ],
                    ],
                ],
            ],
            'personal' => [
                'block_personal_title' => 'text',
                'block_personal_text' => 'text',
                'block_personal_url' => 'text',
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

    // сохранение/удаление/валидация

    public function getProjectsList()
    {
        $query = Project::findVisible()
            ->orderBy(['name' => 'asc']);
        $res = [];
        foreach ($query->all() as $key => $value) {
            $res[$value['id']] = $value['name'];
        }
        return $res;
    }

    public function getRelations($tab)
    {
        $relations = [
            'features' => [
                'features' => [
                    'type' => 'relation',
                    'buttons' => [
                        'add_page' => [
                            'class' => 'success',
                            'name' => 'Создать',
                            'url' => Url::toRoute(['/admin/pages_helper/hrspagefeatures/create', 'HrsPageFeature' => ['page_id' => $this->id], 'return_url' => '/admin/' . Yii::$app->controller->module->id . '/' . Yii::$app->controller->id . '/update/?id=' . $this->id, 'anchor' => 'features']),
                        ],
                    ],
                    'search_class' => '\app\modules\pages_helper\models\HrsPageFeatureSearch',
                    'title' => 'Редактирование',
                ],
            ],
            'smi' => [
                'smi' => [
                    'type' => 'relation',
                    'buttons' => [
                        'add_page' => [
                            'class' => 'success',
                            'name' => 'Создать',
                            'url' => Url::toRoute(['/admin/pages_helper/hrspagesmi/create', 'HrsPageSmi' => ['page_id' => $this->id], 'return_url' => '/admin/' . Yii::$app->controller->module->id . '/' . Yii::$app->controller->id . '/update/?id=' . $this->id, 'anchor' => 'smi']),
                        ],
                    ],
                    'search_class' => '\app\modules\pages_helper\models\HrsPageSmiSearch',
                    'title' => 'Редактирование',
                ],
            ],
        ];
        return $tab ? $relations[$tab] : $relations;
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['block1_span', 'default', 'value' => ''],
            ['block1_title', 'default', 'value' => ''],
            ['block1_left_button_title', 'default', 'value' => ''],
            ['block1_left_button_url', 'default', 'value' => ''],
            ['block1_right_button_title', 'default', 'value' => ''],
            ['block1_right_button_url', 'default', 'value' => ''],
            ['block_problematic_title', 'default', 'value' => ''],
            ['block_problematic_text', 'default', 'value' => ''],
            ['block_proposal_title', 'default', 'value' => ''],
            ['block_proposal_url', 'default', 'value' => ''],
            ['block_experts_title', 'default', 'value' => ''],
            ['block_personal_title', 'default', 'value' => ''],
            ['block_personal_text', 'default', 'value' => ''],
            ['block_join_title', 'default', 'value' => ''],
            ['block_join_text', 'default', 'value' => ''],
            ['block_personal_url', 'default', 'value' => ''],
            ['block_smi_title', 'default', 'value' => ''],
            ['block_faq_title', 'default', 'value' => ''],
            ['block_projects_title', 'default', 'value' => ''],
            ['block_projects_url', 'default', 'value' => ''],
            [['quotes', 'problematic', 'features', 'proposal', 'problems', 'experts', 'faq', 'smi', 'projects',
                'block1_span', 'block1_title', 'block1_left_button_title', 'block1_left_button_url', 'block1_right_button_title', 'block1_right_button_url',
                'block_quotes_title', 'block_quotes_text', 'block_problematic_title', 'block_problematic_text', 'block_proposal_title', 'block_proposal_url', 'block_experts_title',
                'block_join_title', 'block_join_text', 'block_personal_title', 'block_personal_text', 'block_personal_url', 'block_smi_title', 'block_smi_url', 'block_faq_title',
                'block_projects_title', 'block_projects_url',], 'safe'],
        ]);
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

    public function getProjects()
    {
        $pop = $this->projects;
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
        $keys = implode(',', array_keys(ArrayHelper::map($pop, 'project', 'project')));
        $ret = Project::findVisible()
            ->andWhere(['IN', 'lenta.id', array_keys(ArrayHelper::map($pop, 'project', 'project'))]);
        if (!empty($keys)) {
            $ret->orderBy([new \yii\db\Expression('FIELD (lenta.id, ' . $keys . ')')]);
        }
        return $ret->all();
    }

    public function getSmi()
    {
        return $this->hasMany(\app\modules\pages_helper\models\HrsPageSmi::class, ['page_id' => 'id'])->andWhere(['visible' => 1])->orderBy(['order' => SORT_ASC]);
    }

    public function getFeatures()
    {
        return $this->hasMany(\app\modules\pages_helper\models\HrsPageFeature::class, ['page_id' => 'id'])->andWhere(['visible' => 1])->orderBy(['order' => SORT_ASC]);
    }

    public function getImage()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => HrsPage::class, 'keeper_field' => 'image']);
    }

    public function getImage_mobile()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => HrsPage::class, 'keeper_field' => 'image_mobile']);
    }
}
