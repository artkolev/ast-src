<?php

namespace app\modules\pages\controllers\frontend;

use app\models\FMBillChangeDate;
use app\models\FMBillChangeStatus;
use app\models\FMBillExport;
use app\models\FMBillPay;
use app\modules\pages\models\Login;
use app\modules\payment\models\Payment;
use app\modules\payment\models\PaymentHistory;
use app\modules\payment_system\models\BillAnoSystem;
use app\modules\payment_system\models\BillSystem;
use app\modules\payment_system\models\ProdamusAnoSystem;
use app\modules\payment_system\models\ProdamusSystem;
use moonland\phpexcel\Excel;
use Yii;
use yii\web\Response;

/**
 *  Контроллер ЛК финансового менеджера
 */
class FinmanController extends LKController
{
    /**
     * Страница работы со счетами ЛК ФМ
     * @param \app\modules\pages\models\Page $model
     * @return string|string[]|Yii\web\Response
     */
    public function actionIndex($model, $id = false)
    {
        $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
        // доступно только финансовому менеджеру
        if (Yii::$app->user->isGuest or !in_array(Yii::$app->user->identity->userAR->role, ['finman'])) {
            return $this->redirect($login_page->getUrlPath());
        }

        // получаем выбранный счет
        $error_messages = [];
        $id = Yii::$app->request->post('id', $id);

        $bill = Payment::find()
            ->where(['OR',
                ['payment_system' => BillSystem::class],
                ['payment_system' => BillAnoSystem::class],
                ['AND',
                    ['status' => Payment::STATUS_ACCEPTED],
                    ['OR',
                        ['payment_system' => ProdamusSystem::class],
                        ['payment_system' => ProdamusAnoSystem::class],
                    ],
                ],
            ])
            ->andWhere(['id' => $id])->one();
        if (!empty($id) && !$bill) {
            $error_messages[] = 'Запрашиваемый счет не найден';
        }

        /* ошибка, если не аякс */
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (empty($error_messages)) {
                return [
                    'status' => 'success',
                    'html' => $this->renderPartial('_bill_info', ['bill' => $bill]),
                ];
            }
            return [
                'status' => 'fail',
                'title' => 'Ошибка при поиске счета',
                'message' => implode('<br>', $error_messages),
            ];

        }
        if (empty($model)) {
            throw new \yii\web\NotFoundHttpException('Ошибка загрузки страницы');
        }
        $this->setMeta($model);

        $pay_model = new FMBillPay();
        $date_model = new FMBillChangeDate();
        $status_model = new FMBillChangeStatus();

        return $this->render($model->view, [
            'model' => $model,
            'bill' => $bill,
            'error_messages' => $error_messages,
            'pay_model' => $pay_model,
            'date_model' => $date_model,
            'status_model' => $status_model,
        ]);
    }

    /**
     * Страница выгрузки счетов
     * @param \app\modules\pages\models\Page $model
     * @return Yii\web\Response|bool|string
     */
    public function actionBillsexport($model)
    {
        // недоступно неавторизованному пользователю
        $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
        // доступно только финансовому менеджеру
        if (Yii::$app->user->isGuest or !in_array(Yii::$app->user->identity->userAR->role, ['finman'])) {
            return $this->redirect($login_page->getUrlPath());
        }

        $modelform = new FMBillExport();
        $this->setMeta($model);

        return $this->render($model->view, ['model' => $model, 'modelform' => $modelform]);
    }

    /* формирование выгрузки */
    public function actionExport()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->user->isGuest or !in_array(Yii::$app->user->identity->userAR->role, ['finman'])) {
            return [
                'status' => 'fail',
                'message' => 'Недостаточно прав',
            ];
        }

        // проверить валидацию
        $modelform = new FMBillExport();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            $columns = [];
            $columns[] = 'created_at:text:Дата выставления счета';
            $columns[] = 'payment_id:text:Номер счета';
            $columns[] = 'summ:text:Сумма';
            $columns[] = 'status:text:Статус';
            $columns[] = 'contragent_phone:text:Телефон';
            $columns[] = 'contragent_email:text:Email';
            $columns[] = 'contragent_edo:text:ЭДО';
            $columns[] = 'payment_date:text:Дата оплаты';
            $columns[] = 'refund_date:text:Дата отмены платежа';

            $models = Payment::find()->where(
                ['OR',
                    ['payment_system' => BillSystem::class],
                    ['payment_system' => BillAnoSystem::class],
                ]
            )
                ->andWhere(['>=', 'created_at', strtotime($modelform->date_start . ' 00:00:00')])
                ->andWhere(['<=', 'created_at', strtotime($modelform->date_stop . ' 23:59:59')])
                ->all();

            $models_data = [];
            foreach ($models as $key_d => $model) {
                $models_data[$key_d] = [
                    'created_at' => Yii::$app->formatter->asDatetime($model->created_at, 'php:d.m.Y H:i'),
                    'payment_id' => $model->payment_id ? $model->payment_id : '',
                    'summ' => $model->summ ? $model->summ : '',
                    'status' => $model->statusName,
                    'contragent_phone' => $model->contragent_phone,
                    'contragent_email' => $model->contragent_email,
                    'contragent_edo' => $model->contragent_edo ? 'Да' : 'Нет',
                    'payment_date' => $model->payment_date ? Yii::$app->formatter->asDatetime($model->payment_date, 'php:d.m.Y H:i') : '',
                    'refund_date' => $model->refund_date ? Yii::$app->formatter->asDatetime($model->refund_date, 'php:d.m.Y H:i') : '',
                ];
            }
            $data =
                Excel::export([
                    'autoSize' => true,
                    'models' => $models_data,
                    'columns' => $columns,
                    // возвращает файл на скачивание
                    'asAttachment' => true,
                    // с таким именем
                    'fileName' => 'Выгрузка счетов',
                ]);
            // return $data;
        } else {
            return [
                'status' => 'fail',
                'message' => 'Параметры заданы неверно',
            ];
        }
    }

    /* запрос от select2 */
    public function actionBillsearch($search = '')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        // пустой ответ
        $ret_data = [
            'results' => [],
        ];

        if (Yii::$app->user->isGuest or !in_array(Yii::$app->user->identity->userAR->role, ['finman'])) {
            return $ret_data;
        }
        if (empty($search)) {
            return $ret_data;
        }
        /* выдавать весь список нет смысла, показываем первые 20 результатов */
        $bills = Payment::find()->where(['OR',
            ['payment_system' => BillSystem::class],
            ['payment_system' => BillAnoSystem::class],
            ['AND',
                ['status' => Payment::STATUS_ACCEPTED],
                ['OR',
                    ['payment_system' => ProdamusSystem::class],
                    ['payment_system' => ProdamusAnoSystem::class],
                ],
            ],
        ])->andWhere(['LIKE', 'payment_id', $search])->limit(20)->orderBy(['created_at' => SORT_DESC])->all();

        foreach ($bills as $bill) {
            $ret_data['results'][] = ['id' => $bill->id, 'text' => (in_array($bill->payment_system, [ProdamusSystem::class, ProdamusAnoSystem::class]) ? '(Prodamus) ' : '(Счет) ') . $bill->payment_id];
        }
        return $ret_data;
    }

    /* Оплата счета */
    public function actionBillpaydate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'title' => 'Ошибка при оплате счета',
                'message' => 'Ошибка типа запроса',
            ];
        }
        /* ошибка, если не финменеджер */
        if (Yii::$app->user->isGuest or !in_array(Yii::$app->user->identity->userAR->role, ['finman'])) {
            return [
                'status' => 'fail',
                'title' => 'Ошибка при оплате счета',
                'message' => 'Недостаточно прав',
            ];
        }

        // проверить валидацию
        $modelform = new FMBillPay();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // валидация отсеивает все возможные ошибки при оплате счета
            $bill = Payment::find()->where(
                ['OR',
                    ['payment_system' => BillSystem::class],
                    ['payment_system' => BillAnoSystem::class],
                ]
            )
                ->andWhere(['id' => $modelform->id, 'status' => Payment::STATUS_NEW])
                ->one();
            if ($bill) {
                $bill->status = Payment::STATUS_ACCEPTED;
                $bill->payment_date = $modelform->date;
                if ($bill->save()) {
                    // логирование
                    PaymentHistory::add($bill->id, 'Платёж провёл финансовый менеджер ' . Yii::$app->user->identity->userAR->profile->fullname . ' (ID=' . Yii::$app->user->id . ')');
                    $bill->ownermodel->applyPayment();

                    if ($bill->is_postpay) {
                        $bill->ownermodel->updateBitrixModel();
                    }
                    return [
                        'status' => 'success',
                        'html' => $this->renderPartial('_bill_info', ['bill' => $bill]),
                    ];
                }
                return [
                    'status' => 'fail',
                    'title' => 'Ошибка при оплате счета',
                    'message' => 'При сохранении данных возникла ошибка. Обратитесь к администратору. Позиции по заказу будут считаться оплаченными, но статус счета не изменён.',
                ];


            }
            return [
                'status' => 'fail',
                'title' => 'Ошибка при оплате счета',
                'message' => 'Указанный счет не найден, обновите страницу',
            ];

        }
        return [
            'status' => 'fail',
            'title' => 'Ошибка при оплате счета',
            'message' => 'Неизвестная ошибка, обратитесь к администратору',
        ];

    }

    /* Смена даты оплата счета */
    public function actionBillchangedate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'title' => 'Ошибка при смене даты',
                'message' => 'Ошибка типа запроса',
            ];
        }
        /* ошибка, если не финменеджер */
        if (Yii::$app->user->isGuest or !in_array(Yii::$app->user->identity->userAR->role, ['finman'])) {
            return [
                'status' => 'fail',
                'title' => 'Ошибка при смене даты',
                'message' => 'Недостаточно прав',
            ];
        }
        // проверить валидацию
        $modelform = new FMBillChangeDate();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // валидация отсеивает все возможные ошибки при смене даты
            $bill = Payment::find()->where(
                ['OR',
                    ['payment_system' => BillSystem::class],
                    ['payment_system' => BillAnoSystem::class],
                ]
            )
                ->andWhere(['id' => $modelform->id, 'status' => Payment::STATUS_ACCEPTED])
                ->one();
            if ($bill) {
                $old_date = $bill->payment_date;
                $bill->payment_date = $modelform->date;
                if ($bill->save()) {
                    // логирование
                    PaymentHistory::add($bill->id, 'Изменена дата оплаты счета с ' . $old_date . ' на ' . $modelform->date . ' Финансовый менеджер ' . Yii::$app->user->identity->userAR->profile->fullname . ' (ID=' . Yii::$app->user->id . ')');
                    return [
                        'status' => 'success',
                        'html' => $this->renderPartial('_bill_info', ['bill' => $bill]),
                    ];
                }
                return [
                    'status' => 'fail',
                    'title' => 'Ошибка при сохранении',
                    'message' => 'Новая дата оплаты не зафиксирована. Обратитесь к администратору',
                ];

            }
            return [
                'status' => 'fail',
                'title' => 'Ошибка при смене даты',
                'message' => 'Указанный счет не найден, обновите страницу',
            ];

        }
        return [
            'status' => 'fail',
            'title' => 'Ошибка при смене даты',
            'message' => 'Неизвестная ошибка, обратитесь к администратору',
        ];

    }

    /* Смена даты оплата счета */
    public function actionBillchangestatus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'title' => 'Ошибка при смене статуса',
                'message' => 'Ошибка типа запроса',
            ];
        }
        /* ошибка, если не финменеджер */
        if (Yii::$app->user->isGuest or !in_array(Yii::$app->user->identity->userAR->role, ['finman'])) {
            return [
                'status' => 'fail',
                'title' => 'Ошибка при оплате статуса',
                'message' => 'Недостаточно прав',
            ];
        }
        // проверить валидацию
        $modelform = new FMBillChangeStatus();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            // валидация отсеивает все возможные ошибки при смене статуса
            $bill = Payment::find()->where(
                ['OR',
                    ['payment_system' => BillSystem::class],
                    ['payment_system' => BillAnoSystem::class],
                ]
            )
                ->andWhere(['id' => $modelform->id, 'status' => Payment::STATUS_ACCEPTED])
                ->one();
            if ($bill) {
                if ($modelform->status == Payment::STATUS_NEW) {
                    // отменить ошибочный платеж
                    $bill->status = Payment::STATUS_NEW;
                    $bill->payment_date = null;
                    if ($bill->save()) {
                        // логирование
                        if ($bill->is_postpay) {
                            PaymentHistory::add($bill->id, 'Оплата по постоплате отменена. Счет возвращен в статус Постоплата. Финансовый менеджер ' . Yii::$app->user->identity->userAR->profile->fullname . ' (ID=' . Yii::$app->user->id . ')');
                            $bill->ownermodel->updateBitrixModel();
                        } else {
                            PaymentHistory::add($bill->id, 'Оплата по счету отменена. Счет возвращен в статус Не оплачен. Финансовый менеджер ' . Yii::$app->user->identity->userAR->profile->fullname . ' (ID=' . Yii::$app->user->id . ')');
                            $bill->ownermodel->cancelPayment();
                        }
                    }
                }
                if ($modelform->status == Payment::STATUS_DECLINED) {
                    // вернуть платёж
                    $bill->status = Payment::STATUS_DECLINED;
                    $bill->refund_date = date('d.m.Y');
                    if ($bill->save()) {
                        PaymentHistory::add($bill->id, 'Проведён возврат денежных средств по счету. Финансовый менеджер ' . Yii::$app->user->identity->userAR->profile->fullname . ' (ID=' . Yii::$app->user->id . ')');
                        $bill->ownermodel->refundPayment();
                    }
                }
                if ($bill->save()) {
                    return [
                        'status' => 'success',
                        'html' => $this->renderPartial('_bill_info', ['bill' => $bill]),
                    ];
                }
                return [
                    'status' => 'fail',
                    'title' => 'Ошибка при сохранении',
                    'message' => 'Новая дата оплаты не зафиксирована. Обратитесь к администратору',
                ];

            }
            // возврат продамус
            $bill = Payment::find()->where(['OR',
                ['payment_system' => ProdamusSystem::class],
                ['payment_system' => ProdamusAnoSystem::class],
            ])
                ->andWhere(['id' => $modelform->id, 'status' => Payment::STATUS_ACCEPTED])
                ->one();
            if ($bill) {
                if ($modelform->status == Payment::STATUS_DECLINED) {
                    // вернуть платёж
                    $bill->status = Payment::STATUS_DECLINED;
                    $bill->refund_date = date('d.m.Y');
                    if ($bill->save()) {
                        PaymentHistory::add($bill->id, 'Проведён возврат денежных средств по счету. Финансовый менеджер ' . Yii::$app->user->identity->userAR->profile->fullname . ' (ID=' . Yii::$app->user->id . ')');
                        $bill->ownermodel->refundPayment();
                        return [
                            'status' => 'success',
                            'html' => $this->renderPartial('_bill_info', ['bill' => $bill]),
                        ];
                    }

                    return [
                        'status' => 'fail',
                        'title' => 'Ошибка при сохранении',
                        'message' => 'Ошибка изменения статуса. Обратитесь к администратору',
                    ];

                }
            } else {
                return [
                    'status' => 'fail',
                    'title' => 'Ошибка при оплате статуса',
                    'message' => 'Указанный счет не найден, обновите страницу',
                ];
            }

        }
        return [
            'status' => 'fail',
            'title' => 'Ошибка при оплате статуса',
            'message' => 'Неизвестная ошибка, обратитесь к администратору',
        ];

    }

    /* Присвоить признак Постоплаты */
    public function actionBillpostpay()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'title' => 'Ошибка при присваивании признака постоплаты',
                'message' => 'Ошибка типа запроса',
            ];
        }

        /* ошибка, если не финменеджер */
        if (Yii::$app->user->isGuest or !in_array(Yii::$app->user->identity->userAR->role, ['finman'])) {
            return [
                'status' => 'fail',
                'title' => 'Ошибка при присваивании признака постоплаты',
                'message' => 'Недостаточно прав',
            ];
        }

        $id = Yii::$app->request->post('payment_id', false);
        $is_postpay = Yii::$app->request->post('is_postpay', false);

        if (!$id) {
            return [
                'status' => 'fail',
                'title' => 'Ошибка при присваивании признака постоплаты',
                'message' => 'ID счета не передан',
            ];
        }

        $bill = Payment::find()->where(['OR',
            ['payment_system' => BillSystem::class],
            ['payment_system' => BillAnoSystem::class],
        ])
            ->andWhere(['id' => $id, 'status' => Payment::STATUS_NEW])
            ->one();

        if (!$bill) {
            return [
                'status' => 'fail',
                'title' => 'Ошибка при присваивании признака постоплаты',
                'message' => 'Счет не найден',
            ];
        }

        $bill->is_postpay = !!$is_postpay;
        if (!$bill->save()) {
            return [
                'status' => 'fail',
                'title' => 'Ошибка при сохранении',
                'message' => 'Признак постоплаты не присвоен. Обратитесь к администратору',
            ];
        }

        if ($bill->is_postpay) {
            PaymentHistory::add($bill->id, 'Финансовый менеджер ' . Yii::$app->user->identity->userAR->profile->fullname . ' (ID=' . Yii::$app->user->id . ') оформил платеж как постоплату');
            $bill->ownermodel->applyPayment();
        } else {
            PaymentHistory::add($bill->id, 'Финансовый менеджер ' . Yii::$app->user->identity->userAR->profile->fullname . ' (ID=' . Yii::$app->user->id . ') вернул платеж с постоплаты на оплату по счету');
            $bill->ownermodel->cancelPayment();
        }

        return [
            'status' => 'success',
            'html' => $this->renderPartial('_bill_info', ['bill' => $bill]),
        ];
    }
}
