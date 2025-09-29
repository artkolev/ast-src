<?php
/**
 * @modelDescr Страница с дизайном Академия экспертам
 */

namespace app\modules\pages\models;

use app\modules\admin\behaviors\SaveFilesRelation;
use app\modules\admin\behaviors\SaveOneVarRelation;
use app\modules\admin\behaviors\Serialize;
use app\modules\admin\components\FilestoreModel;
use app\modules\users\models\UserAR;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * Class ExpertsPage
 *
 * @property FilestoreModel[] $proposalimage
 * @property FilestoreModel[] $smiimage
 * @property FilestoreModel[] $problematicimage
 * @property FilestoreModel $join
 * @property FilestoreModel $image_mobile
 * @property FilestoreModel $image
 *
 * @package app\modules\pages\models
 */
class ExpertsPage extends Page
{
    public static $name_for_list = "Академия экспертам";
    public static $count_for_list = 1;
    public static $expertsSelectList;
    public static $expertsList;
    public $view = 'experts';
    public $action_id = 'pages/pages/about_expert';
    public $image_loader;
    public $image_mobile_loader;
    public $join_loader;
    public $proposalimage_loader;
    public $smiimage_loader;
    public $problematicimage_loader;

    public $problematic;
    public $problematic2;
    public $problematic3;
    public $stats;
    public $experts;
    public $proposal;
    public $smi;

    public $faq;
    public $block1_title;
    public $block1_left_button_title;
    public $block1_left_button_url;
    public $block1_right_button_title;
    public $block1_right_button_url;
    public $block1_span;
    public $block_problematic_title;
    public $block_problematic_text;
    public $block_problematic2_title;
    public $block_problematic2_text;
    public $block_problematic3_title;
    public $block_problematic3_text;
    public $block_features_title;
    public $block_proposal_title;
    public $block_proposal_text;
    public $block_proposal_url;
    public $block_experts_title;
    public $block_faq_title;
    public $block_personal_title;
    public $block_personal_text;
    public $block_personal_url;
    public $block_smi_title;
    public $block_smi_url;
    public $block_join_title;
    public $block_join_text;
    public $block_join_button_link;
    public $block_join_button_text;
    public $block_stats_title;
    public $block_stats_text;

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'image' => 'Изображение',
            'image_loader' => 'Изображение',
            'join' => 'Изображение',
            'join_loader' => 'Изображение',
            'image_mobile' => 'Изображение для мобильных',
            'image_mobile_loader' => 'Изображение для мобильных',
            'problematic' => 'Академия как проф сообщество',
            'problematic2' => 'Академия как маркетплейс',
            'problematic3' => 'Академия помогает находить клиентов',
            'faq' => 'Частые вопросы',
            'quotes' => 'Цитаты',
            'features' => 'Руководителям и организациям',
            'proposal' => 'Как получить корпоративное предложение',
            'problems' => 'Эксперты помогают',
            'experts' => 'Эксперты',
            'smi' => 'Академия в СМИ',
            'stats' => 'Академия в цифрах',
            'block1_span' => 'Подсвеченная часть заголовка первого блока',
            'block1_title' => 'Заголовок первого блока',
            'block1_left_button_title' => 'Текст левой кнопки',
            'block1_left_button_url' => 'Ссылка на левой кнопке',
            'block1_right_button_title' => 'Текст правой кнопки',
            'block1_right_button_url' => 'Ссылка на правой кнопке',
            'block_problematic_title' => 'Заголовок блока',
            'block_problematic_text' => 'Текст блока',
            'block_problematic2_title' => 'Заголовок блока маркетплейс',
            'block_problematic2_text' => 'Текст блока маркетплейс',
            'block_problematic3_title' => 'Заголовок блока',
            'block_problematic3_text' => 'Текст блока',
            'block_features_title' => 'Заголовок блока',
            'block_features_text' => 'Текст блока',
            'block_proposal_title' => 'Заголовок блока',
            'block_proposal_text' => 'Текст блока',
            'block_proposal_url' => 'Ссылка блока',
            'block_problems_title' => 'Заголовок блока',
            'block_experts_title' => 'Заголовок блока',
            'block_faq_title' => 'Заголовок блока',
            'block_personal_title' => 'Заголовок блока',
            'block_personal_text' => 'Текст блока',
            'block_join_title' => 'Заголовок блока',
            'block_join_text' => 'Текст блока',
            'block_join_button_link' => 'Ссылка с кнопки',
            'block_join_button_text' => 'Текст на кнопке',
            'block_stats_title' => 'Заголовок блока',
            'block_stats_text' => 'Текст блока',
            'block_personal_url' => 'Ссылка блока',
            'block_smi_title' => 'Заголовок блока',
            'block_smi_url' => 'Ссылка "Смотреть все"',
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
                        'block1_span',
                        'block1_title',
                        'block1_left_button_title',
                        'block1_left_button_url',
                        'block1_right_button_title',
                        'block1_right_button_url',
                        'block_problematic_title',
                        'block_problematic_text',
                        'block_problematic2_title',
                        'block_problematic2_text',
                        'block_problematic3_title',
                        'block_problematic3_text',
                        'block_features_title',
                        'block_proposal_title',
                        'block_proposal_text',
                        'block_proposal_url',
                        'block_experts_title',
                        'block_faq_title',
                        'block_personal_title',
                        'block_personal_text',
                        'block_join_title',
                        'block_join_text',
                        'block_stats_title',
                        'block_stats_text',
                        'block_personal_url',
                        'block_smi_title',
                        'block_smi_url',
                        'problematic3',
                        'faq',
                        'stats',
                        'experts',
                        'block_join_button_link',
                        'block_join_button_text',
                        'proposal',
                        'problematic',
                        'problematic2',
                        'smi',
                    ],
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
                    'join' => [
                        'type' => 'single', // multiple
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, jpeg, png',
                            'maxSize' => 2 * 1024 * 1024, // 3Мб
                        ],
                        'main' => [
                            'width' => 488,
                            'height' => 325,
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
                    'proposalimage' => [
                        'type' => 'multirow',
                        'multifield' => 'proposal',
                        'multifield_fieldname' => 'proposal_image',
                        'multifield_type' => 'single',
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, png, jpeg, svg',
                            'maxSize' => 1024 * 1024, // 1Мб
                            'minHeight' => 260,
                            'minWidth' => 196,
                        ],
                        'main' => [
                            'width' => 260,
                            'height' => 196,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'smiimage' => [
                        'type' => 'multirow',
                        'multifield' => 'smi',
                        'multifield_fieldname' => 'smi_image',
                        'multifield_type' => 'single',
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, png, jpeg, svg',
                            'maxSize' => 1024 * 1024, // 1Мб
                            'minHeight' => 20,
                            'minWidth' => 75,
                        ],
                        'main' => [
                            'width' => 75,
                            'height' => 20,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'problematicimage' => [
                        'type' => 'multirow',
                        'multifield' => 'problematic',
                        'multifield_fieldname' => 'problematic_image',
                        'multifield_type' => 'single',
                        'validator' => 'image',
                        'validate' => [
                            'maxFiles' => 1,
                            'extensions' => 'jpg, png, jpeg, svg',
                            'maxSize' => 1024 * 1024, // 1Мб
                            'minHeight' => 35,
                            'minWidth' => 35,
                        ],
                        'main' => [
                            'width' => 220,
                            'height' => 220,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                ]
            ],
            4 => [
                'class' => Serialize::class,
                'relations' => [
                    'problematic3',
                    'faq',
                    'stats',
                    'experts',
                    'proposal',
                    'problematic',
                    'problematic2',
                    'smi',
                ],
            ],
        ]);
    }

    public function getFieldTabs()
    {
        return [
            'main' => 'Основное',
            'problematic' => 'Академия как проф сообщество',
            'experts' => 'Эксперты',
            'problematic2' => 'Академия как маркетплейс',
            'personal' => 'Присоединяйтесь к Академии',
            'problematic3' => 'Академия помогает находить клиентов',
            'join' => 'Станьте частью сообщества',
            'stats' => 'Академия для экспертов',
            'proposal' => 'Как стать экспертом Академии',
            'faq' => 'Частые вопросы',
            'smi' => 'Академия в СМИ',
            'seo' => 'SEO',
        ];
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
                'problematic' => [
                    'type' => 'multifields',
                    'fields' => [
                        'name' => [
                            'type' => 'text',
                            'visible_field' => true,
                            'name' => 'Заголовок',
                        ],
                        'link' => [
                            'type' => 'text',
                            'visible_field' => true,
                            'name' => 'Ссылка',
                        ],
                        'description' => [
                            'type' => 'textarea',
                            'visible_field' => true,
                            'name' => 'Текст',
                        ],
                        'problematic_image' => [
                            'type' => 'image',
                            'source_field' => 'problematicimage',
                            'name' => 'Изображение',
                        ],
                    ]
                ]
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
            'problematic2' => [
                'block_problematic2_title' => 'text',
                'block_problematic2_text' => 'text',
                'problematic2' => [
                    'type' => 'multifields',
                    'fields' => [
                        'name' => [
                            'type' => 'text',
                            'visible_field' => true,
                            'name' => 'Заголовок',
                        ],
                        'description' => [
                            'type' => 'textarea',
                            'visible_field' => true,
                            'name' => 'Текст',
                        ],
                        'link' => [
                            'type' => 'text',
                            'visible_field' => true,
                            'name' => 'Ссылка',
                        ],
                    ]
                ]
            ],
            'personal' => [
                'block_personal_title' => 'text',
                'block_personal_text' => 'text',
            ],
            'problematic3' => [
                'block_problematic3_title' => 'text',
                'block_problematic3_text' => 'text',
                'problematic3' => [
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
            'join' => [
                'block_join_title' => 'text',
                'block_join_text' => 'text',
                'join' => 'image',
                'block_join_button_link' => 'text',
                'block_join_button_text' => 'text',
            ],
            'stats' => [
                'block_stats_title' => 'text',
                'block_stats_text' => 'text',
                'stats' => [
                    'type' => 'multifields',
                    'fields' => [
                        'num' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Число',
                        ],
                        'title' => [
                            'type' => 'text',
                            'visible_field' => true,
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
                'block_proposal_text' => 'text',
                'proposal' => [
                    'type' => 'multifields',
                    'fields' => [
                        'name' => [
                            'type' => 'text',
                            'visible_field' => true,
                            'name' => 'Заголовок',
                        ],
                        'description' => [
                            'type' => 'textarea',
                            'visible_field' => true,
                            'name' => 'Текст',
                        ],
                        'proposal_image' => [
                            'type' => 'image',
                            'source_field' => 'proposalimage',
                            'name' => 'Изображение',
                        ],
                    ]
                ]
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
            'smi' => [
                'block_smi_title' => 'text',
                'block_smi_url' => 'text',
                'smi' => [
                    'type' => 'multifields',
                    'fields' => [
                        'name' => [
                            'type' => 'text',
                            'visible_field' => true,
                            'name' => 'Заголовок статьи',
                        ],
                        'url' => [
                            'type' => 'text',
                            'visible_field' => true,
                            'name' => 'Ссылка на статью',
                        ],
                        'date' => [
                            'type' => 'text',
                            'visible_field' => false,
                            'name' => 'Дата',
                        ],
                        'smi_image' => [
                            'type' => 'image',
                            'source_field' => 'smiimage',
                            'name' => 'Изображение',
                        ],
                    ]
                ]
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

    // сохранение/удаление/валидация

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
            ['block_problematic2_title', 'default', 'value' => ''],
            ['block_problematic2_text', 'default', 'value' => ''],
            ['block_problematic3_title', 'default', 'value' => ''],
            ['block_problematic3_text', 'default', 'value' => ''],
            ['block_features_title', 'default', 'value' => ''],
            ['block_proposal_title', 'default', 'value' => ''],
            ['block_proposal_text', 'default', 'value' => ''],
            ['block_proposal_url', 'default', 'value' => ''],
            ['block_experts_title', 'default', 'value' => ''],
            ['block_faq_title', 'default', 'value' => ''],
            ['block_personal_title', 'default', 'value' => ''],
            ['block_personal_text', 'default', 'value' => ''],
            ['block_join_title', 'default', 'value' => ''],
            ['block_join_text', 'default', 'value' => ''],
            ['block_stats_title', 'default', 'value' => ''],
            ['block_stats_text', 'default', 'value' => ''],
            ['block_personal_url', 'default', 'value' => ''],
            ['block_smi_title', 'default', 'value' => ''],
            [['quotes', 'problematic', 'problematic2', 'problematic3', 'features', 'proposal', 'problems', 'experts', 'faq', 'smi', 'stats',
                'block1_span', 'block1_title', 'block1_left_button_title', 'block1_left_button_url', 'block1_right_button_title', 'block1_right_button_url',
                'block_quotes_title', 'block_quotes_text', 'block_problematic3_title', 'block_problematic3_text', 'block_problematic2_title', 'block_problematic2_text', 'block_problematic_title', 'block_problematic_text', 'block_features_title', 'block_proposal_title', 'block_proposal_url', 'block_problems_title', 'block_experts_title',
                'block_faq_title', 'block_personal_title', 'block_personal_text', 'block_join_title', 'block_join_text', 'block_personal_url', 'block_smi_title', 'block_smi_url',
                'block_stats_text', 'block_stats_title', 'block_proposal_text', 'block_join_button_link', 'block_join_button_text'], 'safe'],
        ]);
    }

    public function getExperts()
    {
        $pop = $this->experts;
        if (!empty($pop)) {
            foreach ($pop as $key => $value) {
                if ($value['visible'] != 1) {
                    unset($pop[$key]);
                }
            }
            usort($pop, function ($a, $b) {
                return $a['order'] <=> $b['order'];
            });
        } else {
            $pop = [];
        }

        // добавляем случайных пользователей, если их меньше 6 или не кратно 3
        $pop_keys = array_keys(ArrayHelper::map($pop, 'expert', 'expert'));
        $count = UserAR::find()->andWhere(['IN', 'id', $pop_keys])->visible(['expert', 'exporg'])->distinct()->count();
        if ($count < 12) {
            $extra_users = 12 - $count;
        } else {
            $extra_users = ($count % 3) ? (3 - $count % 3) : 0;
        }
        if ($extra_users > 0) {
            $roles = ['expert'];
            $items = (new \yii\db\Query())
                ->select(['user.id as expert', "CONCAT(profile.name,' ',profile.surname) as halfname", 'profile.organization_name as organization_name', 'auth_assignment.item_name as role'])
                ->from('user')
                ->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id')
                ->leftJoin('profile', 'profile.user_id = user.id')
                ->where(['IN', 'auth_assignment.item_name', $roles])
                ->andWhere(['status' => UserAR::STATUS_ACTIVE])
                ->andWhere(['user.visible' => 1])
                ->andWhere(['NOT IN', 'user.id', $pop_keys])
                ->orderBy(new Expression('rand()'))
                ->limit($extra_users)
                ->all();
            $pop = array_merge($pop, $items);
            $pop_keys = array_keys(ArrayHelper::map($pop, 'expert', 'expert'));
        }

        $keys = implode(',', $pop_keys);
        $ret = UserAR::find()
            ->andWhere(['IN', 'id', $pop_keys])
            ->visible(['expert', 'exporg']);
        if (!empty($keys)) {
            $ret->orderBy([new Expression('FIELD (id, ' . $keys . ')')]);
        }
        return $ret->all();
    }

    public function getImage()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => ExpertsPage::class, 'keeper_field' => 'image']);
    }

    public function getProposalimage()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => ExpertsPage::class, 'keeper_field' => 'proposalimage']);
    }

    public function getSmiimage()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => ExpertsPage::class, 'keeper_field' => 'smiimage']);
    }

    public function getProblematicimage()
    {
        return $this->hasMany(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => ExpertsPage::class, 'keeper_field' => 'problematicimage']);
    }

    public function getJoin()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => ExpertsPage::class, 'keeper_field' => 'join']);
    }

    public function getImage_mobile()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => ExpertsPage::class, 'keeper_field' => 'image_mobile']);
    }
}
