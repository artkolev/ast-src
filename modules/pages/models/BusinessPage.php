<?php
/**
 * @modelDescr Страница с дизайном Академия для бизнеса
 */

namespace app\modules\pages\models;

use app\helpers\Constants;
use app\modules\admin\behaviors\SaveFilesRelation;
use app\modules\admin\behaviors\SaveOneVarRelation;
use app\modules\admin\behaviors\Serialize;
use app\modules\admin\components\FilestoreModel;
use app\modules\users\models\UserAR;
use yii\helpers\ArrayHelper;

class BusinessPage extends Page
{
    public static $name_for_list = "Академия для бизнеса";
    public static $count_for_list = 1;
    public static $expertsSelectList;
    public static $expertsList;
    public $view = 'business';
    public $action_id = 'pages/pages/business';
    public $image_loader;
    public $block1_title;
    public $block1_left_button_title;
    public $block1_left_button_url;
    public $block1_right_button_title;
    public $block1_right_button_url;
    public $block1_span;

    public $block_teams_title;
    public $block_teams_text;
    public $teams;
    public $teams_card_type;

    public $block_for_orgs_title;
    public $block_for_orgs_text;
    public $for_orgs;

    public $block_boss_org_title;
    public $block_boss_org_text;
    public $boss_org;
    public $boss_card_type;

    public $services_title;
    public $services_text;
    public $services_buttons;
    public $service_image_loader;

    public $block_how_we_work_title;
    public $block_how_we_work_text;
    public $how_we_work;
    public $how_we_work_link;
    public $how_we_work_link_button;
    public $how_we_work_card_type;

    public $block_for_business_title;
    public $block_for_business_text;
    public $for_business;

    public $block_feedback_title;
    public $block_feedback_text;
    public $feedback;
    public $feedback_link;
    public $feedback_link_button;

    public $block_experts_help_title;
    public $block_experts_help_text;
    public $experts_help;

    public $ast_experts_title;
    public $ast_experts_text;
    public $ast_experts_cards;
    public $experts_banner_title;
    public $exterts_button_text;
    public $experts_banner_link;

    public $block_leading_experts_title;
    public $block_leading_experts_text;
    public $leading_experts;

    public $block_faq_title;
    public $block_faq_text;
    public $faq;

    public $banner_title;
    public $banner_text;
    public $banner_buttons;

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'image' => 'Изображение',
            'image_loader' => 'Изображение',
            'service_image' => 'Изображение блока услуги',
            'service_image_loader' => 'Изображение блока услуги',
            'block1_span' => 'Подсвеченная часть заголовка первого блока',
            'block1_title' => 'Заголовок первого блока',
            'block1_left_button_title' => 'Текст левой кнопки первого блока',
            'block1_left_button_url' => 'Ссылка на левой кнопке первого блока',
            'block1_right_button_title' => 'Текст правой кнопки первого блока',
            'block1_right_button_url' => 'Ссылка на правой кнопке первого блока',
            'block_teams_title' => 'Заголовок блока "Командам"',
            'block_teams_text' => 'Текст блока "Командам"',
            'block_for_orgs_title' => 'Заголовок блока "Эксперты Академии для организации"',
            'block_for_orgs_text' => 'Текст блока "Эксперты Академии для организации"',
            'block_boss_org_title' => 'Заголовок блока "Руководителям и организациям"',
            'block_boss_org_text' => 'Текст блока "Руководителям и организациям"',
            'services_title' => 'Заголовок блока услуг',
            'services_text' => 'Текст блока услуг',
            'block_how_we_work_title' => 'Заголовок блока "Как мы работаем"',
            'block_how_we_work_text' => 'Текст блока "Как мы работаем"',
            'how_we_work_link' => 'Ссылка на кнопке блока "Как мы работаем"',
            'how_we_work_link_button' => 'Текст на кнопке блока "Как мы работаем"',
            'block_for_business_title' => 'Заголовок блока "Академия социальных технологий бизнесу"',
            'block_for_business_text' => 'Текст блока "Академия социальных технологий бизнесу"',
            'block_feedback_title' => 'Заголовок блока "Отзывы наших клиентов"',
            'block_feedback_text' => 'Текст блока "Отзывы наших клиентов"',
            'feedback_link' => 'Ссылка на кнопке блока "Отзывы наших клиентов"',
            'feedback_link_button' => 'Текст на кнопке блока "Отзывы наших клиентов"',
            'block_experts_help_title' => 'Заголовок блока "Эксперты помогают"',
            'block_experts_help_text' => 'Текст блока "Эксперты помогают"',
            'ast_experts_title' => 'Заголовок блока экспертов',
            'ast_experts_text' => 'Текст блока экспертов',
            'block_leading_experts_title' => 'Заголовок блока "Работайте с ведущими экспертами Академии"',
            'block_leading_experts_text' => 'Текст блока "Работайте с ведущими экспертами Академии"',
            'block_faq_title' => 'Заголовок блока "Частые вопросы"',
            'block_faq_text' => 'Текст блока "Частые вопросы"',
            'banner_title' => 'Заголовок блока "Персональная консультация Академии"',
            'banner_text' => 'Текст блока "Персональная консультация Академии"',
            'teams' => 'Командам',
            'for_orgs' => 'Эксперты Академии для организации',
            'boss_org' => 'Руководителям и организациям',
            'services_buttons' => 'Кнопки услуги академии',
            'how_we_work' => 'Как мы работаем',
            'for_business' => 'Академия социальных технологий бизнесу',
            'feedback' => 'Отзывы наших клиентов',
            'experts_help' => 'Эксперты помогают',
            'ast_experts_cards' => 'Эксперты Академии',
            'leading_experts' => 'Работайте с ведущими экспертами Академии',
            'faq' => 'Частые вопросы',
            'banner_buttons' => 'Персональная консультация Академии',
            'experts_banner_title' => 'Заголовок баннера',
            'exterts_button_text' => 'Текст на кнопке баннера',
            'experts_banner_link' => 'Ссылка на кнопке баннера',
            'how_we_work_card_type' => 'Тип отображения',
            'boss_card_type' => 'Тип отображения',
            'teams_card_type' => 'Тип отображения',
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
                        'teams',
                        'for_orgs',
                        'boss_org',
                        'services_buttons',
                        'how_we_work',
                        'for_business',
                        'feedback',
                        'experts_help',
                        'ast_experts_cards',
                        'leading_experts',
                        'faq',
                        'banner_buttons',
                        'block1_span',
                        'block1_title',
                        'block1_left_button_title',
                        'block1_left_button_url',
                        'block1_right_button_title',
                        'block1_right_button_url',
                        'block_teams_title',
                        'block_teams_text',
                        'block_for_orgs_title',
                        'block_for_orgs_text',
                        'block_boss_org_title',
                        'block_boss_org_text',
                        'services_title',
                        'services_text',
                        'block_how_we_work_title',
                        'block_how_we_work_text',
                        'how_we_work_link',
                        'how_we_work_link_button',
                        'block_for_business_title',
                        'block_for_business_text',
                        'block_feedback_title',
                        'block_feedback_text',
                        'feedback_link',
                        'feedback_link_button',
                        'block_experts_help_title',
                        'block_experts_help_text',
                        'ast_experts_title',
                        'ast_experts_text',
                        'block_leading_experts_title',
                        'block_leading_experts_text',
                        'block_faq_title',
                        'block_faq_text',
                        'banner_title',
                        'banner_text',
                        'experts_banner_title',
                        'exterts_button_text',
                        'experts_banner_link',
                        'how_we_work_card_type',
                        'boss_card_type',
                        'teams_card_type',
                    ],
                ],
            ],
            'serialize' => [
                'class' => Serialize::class,
                'relations' => [
                    'teams',
                    'for_orgs',
                    'boss_org',
                    'services_buttons',
                    'how_we_work',
                    'for_business',
                    'feedback',
                    'experts_help',
                    'ast_experts_cards',
                    'leading_experts',
                    'faq',
                    'banner_buttons',
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
                            'extensions' => 'jpg, png, jpeg',
                            'maxSize' => 2 * 1024 * 1024, // 1Мб
                            'minWidth' => 650,
                            'minHeight' => 432,
                        ],
                        'main' => [
                            'width' => 650,
                            'height' => 432,
                            'quality' => 90,
                            'mode' => 'outbound',
                        ],
                    ],
                    'service_image' => [
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
                ]
            ],
        ]);
    }

    public function getFieldTabs()
    {
        return [
            'main' => 'Основное',
            'teams_tab' => 'Командам',
            'for_orgs_tab' => 'Эксперты Академии для организации',
            'boss_org_tab' => 'Руководителям и организациям',
            'services_tab' => 'Услуги Академии',
            'how_we_work_tab' => 'Как мы работаем',
            'for_business_tab' => 'Академия социальных технологий бизнесу',
            'feedback_tab' => 'Отзывы наших клиентов',
            'experts_help_tab' => 'Эксперты помогают',
            'experts_tab' => 'Эксперты Академии',
            'leading_experts_tab' => 'Работайте с ведущими экспертами Академии',
            'faq_tab' => 'Частые вопросы',
            'banner_tab' => 'Персональная консультация Академии',
            'seo' => 'SEO',
        ];
    }

    public function getRelationTabs()
    {
        return [];
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
                'order' => 'integer',
                'visible' => 'boolean',
            ],
            'teams_tab' => [
                'block_teams_title' => 'text',
                'block_teams_text' => 'text',
                'teams_card_type' => [
                    'type' => 'options',
                    'optionList' => Constants::CARD_TYPES,
                ],
                'teams' => [
                    'type' => 'multifields',
                    'fields' => [
                        'title' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Заголовок',
                        ],
                        'text' => [
                            'visible_field' => false,
                            'type' => 'text',
                            'name' => 'Текст',
                        ],
                    ],
                ],
            ],
            'for_orgs_tab' => [
                'block_for_orgs_title' => 'text',
                'block_for_orgs_text' => 'text',
                'for_orgs' => [
                    'type' => 'multifields',
                    'fields' => [
                        'title' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Заголовок',
                        ],
                        'text' => [
                            'visible_field' => false,
                            'type' => 'text',
                            'name' => 'Текст',
                        ],
                    ],
                ],
            ],
            'boss_org_tab' => [
                'block_boss_org_title' => 'text',
                'block_boss_org_text' => 'text',
                'boss_card_type' => [
                    'type' => 'options',
                    'optionList' => Constants::CARD_TYPES,
                ],
                'boss_org' => [
                    'type' => 'multifields',
                    'fields' => [
                        'title' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Заголовок',
                        ],
                        'text' => [
                            'visible_field' => false,
                            'type' => 'text',
                            'name' => 'Текст',
                        ],
                    ],
                ],
            ],
            'services_tab' => [
                'services_title' => 'text',
                'services_text' => 'text',
                'services_buttons' => [
                    'type' => 'multifields',
                    'fields' => [
                        'name' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Заголовок',
                        ],
                        'link' => [
                            'visible_field' => false,
                            'type' => 'text',
                            'name' => 'Url',
                        ],
                    ],
                ],
                'service_image' => 'image',
            ],
            'how_we_work_tab' => [
                'block_how_we_work_title' => 'text',
                'block_how_we_work_text' => 'text',
                'how_we_work_link' => 'text',
                'how_we_work_link_button' => 'text',
                'how_we_work_card_type' => [
                    'type' => 'options',
                    'optionList' => Constants::CARD_TYPES,
                ],
                'how_we_work' => [
                    'type' => 'multifields',
                    'fields' => [
                        'title' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Заголовок',
                        ],
                        'text' => [
                            'visible_field' => false,
                            'type' => 'text',
                            'name' => 'Текст',
                        ],
                    ],
                ],
            ],
            'for_business_tab' => [
                'block_for_business_title' => 'text',
                'block_for_business_text' => 'text',
                'for_business' => [
                    'type' => 'multifields',
                    'fields' => [
                        'title' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Заголовок',
                        ],
                        'text' => [
                            'visible_field' => false,
                            'type' => 'text',
                            'name' => 'Текст',
                        ],
                    ],
                ],
            ],
            'feedback_tab' => [
                'block_feedback_title' => 'text',
                'block_feedback_text' => 'text',
                'feedback_link' => 'text',
                'feedback_link_button' => 'text',
                'feedback' => [
                    'type' => 'multifields',
                    'fields' => [
                        'title' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Заголовок',
                        ],
                        'status' => [
                            'visible_field' => false,
                            'type' => 'text',
                            'name' => 'Должность',
                        ],
                        'text' => [
                            'visible_field' => false,
                            'type' => 'text',
                            'name' => 'Текст',
                        ],
                    ],
                ],
            ],
            'experts_help_tab' => [
                'block_experts_help_title' => 'text',
                'block_experts_help_text' => 'text',
                'experts_help' => [
                    'type' => 'multifields',
                    'fields' => [
                        'title' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Заголовок',
                        ],
                        'text' => [
                            'visible_field' => false,
                            'type' => 'text',
                            'name' => 'Текст',
                        ],
                        'link' => [
                            'visible_field' => false,
                            'type' => 'text',
                            'name' => 'Url',
                        ],
                    ],
                ],
            ],
            'experts_tab' => [
                'ast_experts_title' => 'text',
                'ast_experts_text' => 'text',
                'experts_banner_title' => 'text',
                'exterts_button_text' => 'text',
                'experts_banner_link' => 'text',
                'ast_experts_cards' => [
                    'type' => 'multifields',
                    'fields' => [
                        'expert' => [
                            'visible_field' => true,
                            'type' => 'options',
                            'name' => 'Выберите вариант',
                            'htmlClass' => 'select_pretty',
                            'options_list' => $this->astExpertList(),
                        ],
                    ],
                ],
            ],
            'leading_experts_tab' => [
                'block_leading_experts_title' => 'text',
                'block_leading_experts_text' => 'text',
                'leading_experts' => [
                    'type' => 'multifields',
                    'fields' => [
                        'title' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Заголовок',
                        ],
                        'text' => [
                            'visible_field' => false,
                            'type' => 'text',
                            'name' => 'Текст',
                        ],
                    ],
                ],
            ],
            'faq_tab' => [
                'block_faq_title' => 'text',
                'block_faq_text' => 'text',
                'faq' => [
                    'type' => 'multifields',
                    'fields' => [
                        'title' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Заголовок',
                        ],
                        'text' => [
                            'visible_field' => false,
                            'type' => 'text',
                            'name' => 'Текст',
                        ],
                    ],
                ],
            ],
            'banner_tab' => [
                'banner_title' => 'text',
                'banner_text' => 'textarea',
                'banner_buttons' => [
                    'type' => 'multifields',
                    'fields' => [
                        'name' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Текст кнопки',
                        ],
                        'link' => [
                            'visible_field' => true,
                            'type' => 'text',
                            'name' => 'Url кнопки',
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

    public function astExpertList(): array
    {
        $items = (new \yii\db\Query())
            ->select(['user.id as id', "CONCAT(profile.name,' ',profile.surname) as halfname", 'profile.organization_name as organization_name', 'auth_assignment.item_name as role'])
            ->from('user')
            ->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id')
            ->leftJoin('profile', 'profile.user_id = user.id')
            ->innerJoin('user_direction', 'user_direction.user_id = user.id AND user_direction.role = "academ"')
            ->where(['IN', 'auth_assignment.item_name', ['expert']])
            ->andWhere(['status' => UserAR::STATUS_ACTIVE])
            ->orderBy(['profile.surname' => 'asc', 'profile.name' => 'asc'])
            ->all();

        return ArrayHelper::map($items, 'id', 'halfname');
    }

    // сохранение/удаление/валидация

    public function getRelations($tab)
    {
        $relations = [];

        return $tab ? $relations[$tab] : $relations;
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [[
                'block1_span',
                'block1_title',
                'block1_left_button_title',
                'block1_left_button_url',
                'block1_right_button_title',
                'block1_right_button_url',
                'block_teams_title',
                'block_teams_text',
                'teams',
                'block_for_orgs_title',
                'block_for_orgs_text',
                'for_orgs',
                'block_boss_org_title',
                'block_boss_org_text',
                'boss_org',
                'services_title',
                'services_text',
                'services_buttons',
                'block_how_we_work_title',
                'block_how_we_work_text',
                'how_we_work',
                'block_for_business_title',
                'block_for_business_text',
                'for_business',
                'block_feedback_title',
                'block_feedback_text',
                'feedback',
                'block_experts_help_title',
                'block_experts_help_text',
                'experts_help',
                'ast_experts_title',
                'ast_experts_text',
                'ast_experts_cards',
                'block_leading_experts_title',
                'block_leading_experts_text',
                'leading_experts',
                'block_faq_title',
                'block_faq_text',
                'faq',
                'banner_title',
                'banner_text',
                'banner_buttons',
                'how_we_work_link',
                'how_we_work_link_button',
                'feedback_link',
                'feedback_link_button',
                'experts_banner_title',
                'exterts_button_text',
                'experts_banner_link',
                'how_we_work_card_type',
                'boss_card_type',
                'teams_card_type',
            ], 'safe'],
        ]);
    }

    /**
     * @return UserAR[]
     */
    public function getAstExpertsCards(): array
    {
        $expertList = [];
        foreach ($this->ast_experts_cards as $ast_experts_card) {
            if ((bool)$ast_experts_card['visible']) {
                $expertList[$ast_experts_card['expert']] = UserAR::findOne($ast_experts_card['expert']);
            }
        }

        return $expertList;
    }

    public function getImage()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => BusinessPage::class, 'keeper_field' => 'image']);
    }

    public function getService_image()
    {
        return $this->hasOne(FilestoreModel::class, ['keeper_id' => 'id'])->andWhere(['keeper_class' => BusinessPage::class, 'keeper_field' => 'service_image']);
    }
}
