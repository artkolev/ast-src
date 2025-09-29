<?php

namespace app\modules\pages\models;

use app\modules\admin\behaviors\SaveOneVarRelation;

class PaymentTinkoffPage extends Page
{
    public static $name_for_list = "страницу Оплаты Tinkoff";
    public $view = 'payment_tinkoff';
    public $action_id = 'pages/pages/page';
    public $content_after;
    public $terminalkey;
    public $is_frame;
    public $price;
    public $description;
    public $email;
    public $taxation;
    public $payment_method;
    public $payment_object;
    public $tax;

    public function behaviors()
    {
        $parent_behaviors = parent::behaviors();
        return array_merge($parent_behaviors, [
            'saveOneVar' => [
                'class' => SaveOneVarRelation::class,
                'relations' => [
                    'single' => [
                        'content_after',
                        'terminalkey',
                        'is_frame',
                        'price',
                        'description',
                        'email',
                        'taxation',
                        'payment_method',
                        'payment_object',
                        'tax',
                    ],
                ],
            ],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'content' => 'Текст перед формой оплаты',
            'content_after' => 'Текст после формы оплаты',
            'terminalkey' => 'Ключ терминала Tinkoff',
            'is_frame' => 'Открыть платёжный виджет во всплывающем окне',
            'price' => 'Сумма платежа (руб.)',
            'description' => 'Описание платежа',
            'email' => 'E-mail организации',
            'taxation' => 'Система налогообложения',
            'payment_method' => 'Признак способа расчета',
            'payment_object' => 'Признак предмета расчета',
            'tax' => 'Ставка НДС',
        ]);
    }

    // сохранение/удаление/валидация
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['content_after', 'is_frame'], 'safe'],
            [['terminalkey', 'price', 'description', 'email', 'taxation', 'payment_method', 'payment_object', 'tax'], 'required'],
        ]);
    }

    public function getFieldTabs()
    {
        return [
            'main' => 'Основное',
            'tinkoff' => 'Виджет оплаты',
            'seo' => 'SEO',
        ];
    }

    public function getFields($tab = false)
    {
        $fields = [
            'main' => [
                'name' => 'text',
                'url' => 'text',
                'content' => 'wysiwyg',
                'content_after' => 'wysiwyg',
                'order' => 'integer',
                'visible' => 'boolean',
                'parent_id' => [
                    'type' => 'options',
                    'optionList' => $this->getSectionList(),
                ],
            ],
            'tinkoff' => [
                'terminalkey' => 'text',
                'is_frame' => 'boolean',
                'price' => 'integer',
                'description' => [
                    'type' => 'text',
                    'hint' => 'В описании нельзя использовать двойные кавычки " и теги',
                ],
                'email' => [
                    'type' => 'text',
                    'hint' => 'Будет передан для формирования чека',
                ],
                'taxation' => [
                    'type' => 'options',
                    'optionList' => [
                        'usn_income' => 'Упрощенная (доходы)',
                        'usn_income_outcome' => 'Упрощенная (доходы минус расходы)',
                        'osn' => 'Общая',
                        'patent' => 'Петентная',
                        'envd' => 'Единый налог на вмененный доход',
                        'esn' => 'Единый сельскохозяйственный налог',
                    ],
                    'hint' => 'Будет передана для формирования чека',
                ],
                'payment_method' => [
                    'type' => 'options',
                    'optionList' => [
                        'full_prepayment' => 'Предоплата 100%',
                        'full_payment' => 'Полный рассчет',
                        'prepayment' => 'Предоплата',
                        'advance' => 'Аванс',
                        'partial_payment' => 'Частичный расчет и кредит',
                        'credit' => 'Передача в кредит',
                        'credit_payment' => 'Оплата кредита',
                    ],
                    'hint' => 'Будет передан для формирования чека',
                ],
                'payment_object' => [
                    'type' => 'options',
                    'optionList' => [
                        'service' => 'Услуга',
                        'commodity' => 'Товар',
                        'excise' => 'Подакцизный товар',
                        'job' => 'Работа',
                        'gambling_bet' => 'Ставка азартной игры',
                        'gambling_prize' => 'Выигрыш азартной игры',
                        'lottery' => 'Лотерейный билет',
                        'lottery_prize' => 'Выигрыш лотереи',
                        'intellectual_activity' => 'Предоставление результатов интеллектуальной деятельности',
                        'payment' => 'Платёж',
                        'agent_commission' => 'Агентское вознаграждение',
                        'composite' => 'Составной предмет расчета',
                        'another' => 'Иной предмет расчета',
                    ],
                    'hint' => 'Будет передан для формирования чека',
                ],
                'tax' => [
                    'type' => 'options',
                    'optionList' => [
                        'none' => 'Без НДС',
                        'vat0' => '0%',
                        'vat10' => '10%',
                        'vat20' => '20%',
                        'vat110' => '10/110',
                        'vat120' => '20/120',
                    ],
                    'hint' => 'Будет передана для формирования чека',
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
            ],
        ];
        $mesh_fields = array_merge($fields['main'], $fields['seo']);
        return $tab ? $fields[$tab] : $mesh_fields;
    }
}
