<?php

namespace app\modules\pages\controllers\frontend;

use app\helpers\MainHelper;
use app\models\EduprogOrderForm;
use app\models\LKEduprog;
use app\models\LKEduprogCancel as EduprogCancelForm;
use app\models\LKEduprogFilterOrders;
use app\models\LKEduprogNews;
use app\models\LKEduprogRegform;
use app\models\LKEduprogTariff;
use app\models\LKEduprogTrainingproc;
use app\modules\admin\components\FilestoreModel;
use app\modules\educontractitem\models\Educontractitem;
use app\modules\eduprog\models\Eduprog;
use app\modules\eduprog\models\EduprogDatechange;
use app\modules\eduprog\models\EduprogForm;
use app\modules\eduprog\models\EduprogMember;
use app\modules\eduprog\models\EduprogTrainingproc;
use app\modules\eduprog\models\News;
use app\modules\eduprog\models\Tariff;
use app\modules\eduprog\models\TariffPrice;
use app\modules\eduprogmoder\models\Eduprogmoder;
use app\modules\eduprogorder\models\Eduprogorder;
use app\modules\eduprogorder\models\EduprogorderItem;
use app\modules\formagree\models\Formagree;
use app\modules\formsresult\models\Formsresult;
use app\modules\pages\models\EduprogPage;
use app\modules\pages\models\EduprogTicketbuy;
use app\modules\pages\models\LKEduprogCancel;
use app\modules\pages\models\LKEduprogClientList;
use app\modules\pages\models\LKEduprogClientOrder;
use app\modules\pages\models\LKEduprogEdit;
use app\modules\pages\models\LKEduprogList;
use app\modules\pages\models\LKEduprogViewMemberNews;
use app\modules\pages\models\LKEduprogViewMemberOrders;
use app\modules\pages\models\LKEduprogViewMembers;
use app\modules\pages\models\LKEduprogViewNews;
use app\modules\pages\models\LKEduprogViewTrainingproc;
use app\modules\pages\models\Login;
use app\modules\pages\models\ProfileIndex;
use app\modules\pages\models\SelectPayment;
use app\modules\payment\models\Payment;
use app\modules\payment_system\models\PaymentSystem;
use app\modules\settings\models\SettingsText;
use app\modules\users\models\Organization;
use app\modules\users\models\UserAR;
use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\UploadedFile;

class EduprogController extends LKController
{
    /* требования для создания заказа программы:
     * запрос пришел аяксом (прямого создания заказа нет - только с кнопки на странице)
     * тариф активен
     * дата публикации тарифа актуальна
     * автор мероприятия не является текущим пользователем (нельзя купить билет у самого себя)
     * автор мероприятия имеет статус Активен
     * автор мероприятия относится к Экспертным организациям АСТ (exporg) и имеет разрешение на создание услуг ($user->organization->can_service) и публикацию ДПО ($user->organization->license_service) и зарегистрирован на Маркетплейс в качестве Юрлица (ООО)
     * даты окончания программы не прошла
     * у программы есть действующий договор для ОП

     * тариф имеет актуальную цену
     * доступны к покупке указанные тарифы в указанном количестве
     */

    /* проверка возможности создания заказа с выбранными тарифами программы */
    public function actionCheckorder()
    {
        // выключатель ДПО
        if (!Yii::$app->params['enable_dpo']) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        $user = Yii::$app->user->identity->userAR;
        // находим тарифы
        $post = Yii::$app->request->post();
        if (!empty($post['order_data']) && is_array($post['order_data'])) {
            $tariffs = Tariff::find()->where(['IN', 'id', array_keys($post['order_data'])])->andWhere(['visible' => 1, 'deleted' => 0])->andWhere(['<=', 'start_publish', new \yii\db\Expression('CURDATE()')])->andWhere(['>=', 'end_publish', new \yii\db\Expression('CURDATE()')])->all();
            if (count($tariffs) != count($post['order_data'])) {
                return [
                    'status' => 'fail',
                    'message' => 'Один или несколько тарифов не могут быть приобретены.',
                ];
            }
            $eduprog = $tariffs[0]->eduprog;
            $eduprog_author = $eduprog->author;
            if ($eduprog->is_corporative) {
                return [
                    'status' => 'fail',
                    'message' => 'Заказы на корпоративные программы оформляются через заявку.',
                ];
            }
            if ($eduprog_author->id == $user->id) {
                return [
                    'status' => 'fail',
                    'message' => 'Нельзя купить билет у самого себя.',
                ];
            }
            if ($eduprog_author->status != UserAR::STATUS_ACTIVE) {
                return [
                    'status' => 'fail',
                    'message' => 'Организатор программы прекратил свою деятельность',
                ];
            }
            if (!in_array($eduprog_author->role, ['exporg']) or ($eduprog_author->organization->type_mp != Organization::TYPE_OOO)) {
                return [
                    'status' => 'fail',
                    'message' => 'Организатор программы более не сотрудничает с АСТ',
                ];
            }
            if (!$eduprog_author->organization->can_service or !$eduprog_author->organization->license_service) {
                return [
                    'status' => 'fail',
                    'message' => 'Организатор программы не иммет разрешения на образовательную деятельность',
                ];
            }
            if (strtotime($eduprog->date_stop) < strtotime(date('Y-m-d'))) {
                return [
                    'status' => 'fail',
                    'message' => 'Программа завершена. Прием заказов окончен.',
                ];
            }
            if (empty($eduprog->eduContracts)) {
                return [
                    'status' => 'fail',
                    'message' => 'Договор, необходимый для начала обучения, еще не согласован.',
                ];
            }

            foreach ($tariffs as $tariff) {
                if ($tariff->currentPrice === false) {
                    return [
                        'status' => 'fail',
                        'message' => 'Цена на тариф неактуальна, невозможно приобрести участие',
                    ];
                }
                if ($eduprog_author->id != $tariff->eduprog->author->id) {
                    return [
                        'status' => 'fail',
                        'message' => 'Нельзя в одном заказе приобрести участие у разных организаторов',
                    ];
                }
                if ($eduprog->id != $tariff->eduprog_id) {
                    return [
                        'status' => 'fail',
                        'message' => 'Нельзя в одном заказе приобрести участие на разные мероприятия',
                    ];
                }
                if (!$eduprog->canBuyTarif($tariff->id, $post['order_data'][$tariff->id])) {
                    return [
                        'status' => 'fail',
                        'message' => 'Тариф "' . $tariff->name . '" недоступен',
                    ];
                }
            }
            // если все проверки пройдены - редирект на создание мероприятия
            $data = base64_encode(http_build_query($post['order_data']));
            $order_page = EduprogTicketbuy::find()->where(['model' => EduprogTicketbuy::class, 'visible' => 1])->one();
            if ($order_page) {
                $order_url = Url::toRoute([$order_page->getUrlPath(), 'data' => $data]);
                if (Yii::$app->user->isGuest) {
                    Yii::$app->session->remove('createorder_after_login');
                    Yii::$app->session->set('redirect_after_login', $order_url);
                    return [
                        'status' => 'need_register',
                    ];
                }
                // редирект на адрес
                return [
                    'status' => 'success',
                    'redirect_to' => $order_url,
                    'message' => 'Оформляем заказ билетов',
                ];

            }
            return [
                'status' => 'fail',
                'message' => 'Продажа билетов временно приостановлена.',
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Параметры заказа заданы неверно',
        ];

    }

    /* страница оформления заказа */
    public function actionTicketbuy($model)
    {
        // выключатель ДПО
        if (!Yii::$app->params['enable_dpo']) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $data = Yii::$app->request->get('data', false);
        $user = Yii::$app->user->identity->userAR;
        $eduprog_catalog = EduprogPage::find()->where(['model' => EduprogPage::class, 'visible' => 1])->one();
        if (!empty($data)) {
            parse_str(base64_decode($data), $data);
            if (!empty($data) and is_array($data)) {
                /* находим тарифы из заказа - только актуальные и опубликованные */
                $tariffs = Tariff::find()
                    ->where(['IN', 'eduprog_tariff.id', array_keys($data)])
                    ->leftJoin('eduprog_price', 'eduprog_price.tariff_id = eduprog_tariff.id')
                    ->andWhere(['visible' => 1, 'deleted' => 0])
                    ->andWhere(['<=', 'eduprog_tariff.start_publish', new \yii\db\Expression('CURDATE()')])
                    ->andWhere(['>=', 'eduprog_tariff.end_publish', new \yii\db\Expression('CURDATE()')])
                    ->andWhere(['<=', 'eduprog_price.start_publish', new \yii\db\Expression('CURDATE()')])
                    ->andWhere(['>=', 'eduprog_price.end_publish', new \yii\db\Expression('CURDATE()')])
                    ->orderBy(['eduprog_price.price' => SORT_DESC])
                    ->all();
                if (!empty($tariffs) and (count($tariffs) == count($data))) {
                    /* принимаем что программа - это программа из первого попавшегося тарифа, т.к. нельзя приобрести участие на две программы в одном заказе */
                    $eduprog = $tariffs[0]->eduprog;
                    if (!$eduprog->is_corporative) {
                        $eduprog_author = $eduprog->author;
                        if ($eduprog_author->id != $user->id) {
                            /* проверяем возможность продажи билетов на программу */
                            if ($eduprog->canSale()) {
                                $current_summ = 0;
                                foreach ($tariffs as $tariff) {
                                    /* проверяем возможность покупки билетов по тарифу */
                                    if (!$eduprog->canBuyTarif($tariff->id, $data[$tariff->id])) {
                                        $error_message = 'Невозможно приобрести участие по тарифу "' . $tariff->name . '". Вернитесь к <a href="' . $tariff->eduprog->getUrlPath() . '">программе</a> и повторите ваш заказ.';
                                    }
                                    /* проверяем наличие действующей цены */
                                    if ($tariff->currentPrice === false) {
                                        $error_message = 'Цена на тариф неактуальна, невозможно приобрести билет. Вернитесь к <a href="' . $tariff->eduprog->getUrlPath() . '">программе</a> и повторите ваш заказ.';
                                        break;
                                    }
                                    if ($eduprog_author->id != $tariff->eduprog->author->id) {
                                        $error_message = 'Нельзя в одном заказе приобрести участие у разных организаторов';
                                        break;
                                    }
                                    if ($eduprog->id != $tariff->eduprog_id) {
                                        $error_message = 'Нельзя в одном заказе приобрести участие на разные программы';
                                        break;
                                    }
                                    $current_summ += $tariff->currentPrice * $data[$tariff->id];
                                }
                            } else {
                                $error_message = 'Продажи на данную программу закрыты.';
                            }
                        } else {
                            $error_message = 'Нельзя купить билет у самого себя.';
                        }
                    } else {
                        $error_message = 'Заказы на корпоративные программы оформляются через заявку.';
                    }
                } else {
                    $error_message = 'Один или несколько тарифов не могут быть приобретены.';
                }
            } else {
                $error_message = 'Отсутствуют параметры заказа. Вернитесь к <a href="' . $eduprog_catalog->getUrlPath() . '">программе</a> и повторите ваш заказ.';
            }
        } else {
            $error_message = 'Отсутствуют параметры заказа. Вернитесь к <a href="' . $eduprog_catalog->getUrlPath() . '">программе</a> и повторите ваш заказ.';
        }

        $this->setMeta($model);
        if (!empty($error_message)) {
            // страница ошибки
            return $this->render('error_order', ['model' => $model, 'error_message' => $error_message]);
        }
        // страница формы
        $modelform = new EduprogOrderForm();
        $modelform->loadFromUserProfile($user->profile);
        $modelform->eduprog_id = $eduprog->id;
        $modelform->eduprogform_id = $tariffs[0]->eduprogForm->id;
        $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();

        return $this->render($model->view, ['model' => $model, 'login_page' => $login_page, 'eduprog' => $eduprog, 'current_summ' => $current_summ, 'modelform' => $modelform, 'tariffs' => $tariffs, 'tickets' => $data]);

    }

    /* оформление заказа */
    public function actionCreateOrder()
    {
        // выключатель ДПО
        if (!Yii::$app->params['enable_dpo']) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        // валидируем данные с формы
        $modelform = new EduprogOrderForm();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            $user = Yii::$app->user->identity->userAR;
            /* находим первый попавшийся тариф */
            $first_tariff = Tariff::find()->where(['id' => (int)reset($modelform->ticket_info['tariff']), 'deleted' => 0])->one();
            if (!$first_tariff) {
                return [
                    'status' => 'fail',
                    'message' => 'Заказ по одному из перечисленных тарифов невозможен',
                ];
            }
            /* если тариф существует - находим программу */
            $eduprog = $first_tariff->eduprog;
            if (!$eduprog) {
                return [
                    'status' => 'fail',
                    'message' => 'Программа ДПО недоступна',
                ];
            }

            /* проверяем доступность продаж по программе */
            if (!$eduprog->canSale()) {
                return [
                    'status' => 'fail',
                    'message' => 'Продажи по выбранной программе закрыты',
                ];
            }

            /* проверить доступность продаж по каждому тарифу */
            $count_by_tariff = [];
            foreach ($modelform->ticket_info['tariff'] as $key => $tariff_id) {
                /* считаем количество билетов по тарифу */
                $count_by_tariff[$tariff_id] = ($count_by_tariff[$tariff_id] ? 1 : $count_by_tariff[$tariff_id] + 1);
                /* для каждого билета смотрим тариф */
                /* если тариф не существует по нему не может быть продажи для выбранного мероприятия */
                if (!$eduprog->canBuyTarif($tariff_id, $count_by_tariff[$tariff_id])) {
                    /* вывести ошибку */
                    return [
                        'status' => 'fail',
                        'message' => 'Невозможно приобрести билет по одному из тарифов.',
                    ];
                }
            }

            /* проверить регистрацию пользователей из списка слушателей */
            $modelform->ticket_info['user_id'] = [];
            foreach ($modelform->ticket_info['email'] as $key => $email) {
                /* ищем пользователя по Email */
                $participant_user = UserAR::find()->where(['email' => $email])->one();
                if ($participant_user) {
                    if (!in_array($participant_user->role, ['expert', 'exporg', 'fizusr', 'urusr'])) {
                        /* вывести ошибку */
                        return [
                            'status' => 'fail',
                            'message' => 'Пользователь ' . $participant_user->profile->halfname . ' (' . $participant_user->email . ') не может принимать участие в программах ДПО',
                        ];
                    }
                    if ($participant_user->status == UserAR::STATUS_DELETED) {
                        return [
                            'status' => 'fail',
                            'message' => 'Пользователь ' . $participant_user->profile->halfname . ' (' . $participant_user->email . ') удален и не сможет принимать участие в программах ДПО',
                        ];
                    }
                    $modelform->ticket_info['user_id'][$key] = $participant_user->id;

                    $existingOrder = EduprogOrderItem::find()
                        ->where(['tariff_id' => $tariff_id, 'eduprog_orderitem.user_id' => $participant_user->id, 'status' => EduprogorderItem::STATUS_NEW])
                        ->innerJoin('eduprog_order', 'eduprog_order.id = eduprog_orderitem.eduprogorder_id AND eduprog_order.is_payed = TRUE')
                        ->exists();

                    if ($existingOrder) {
                        return [
                            'status' => 'fail',
                            'message' => "Для {$email} тариф был оплачен",
                        ];
                    }
                } else {
                    /* зарегистрировать пользователя */
                    $new_user_id = UserAR::registerNewFizurs($modelform->ticket_info['surname'][$key], $modelform->ticket_info['name'][$key], $modelform->ticket_info['patronymic'][$key], $email, '', md5(Yii::$app->security->generateRandomString(12)));
                    if ($new_user_id !== false) {
                        $modelform->ticket_info['user_id'][$key] = $new_user_id;
                    } else {
                        return [
                            'status' => 'fail',
                            'message' => 'Невозможно зарегистрировать пользователя с email ' . $email . '. ' . \app\helpers\MainHelper::getHelpText(),
                        ];
                    }
                }
            }

            $total_summ = 0;

            /* создаем заказ */
            $new_order = new Eduprogorder();
            $new_order->user_id = $user->id;
            $new_order->form_id = $first_tariff->eduprogform_id;
            $new_order->eduprog_id = $eduprog->id;
            $new_order->name = 'Новый заказ';
            $new_order->price = $total_summ;
            $new_order->is_payed = 0;
            $new_order->visible = 1;
            if ($new_order->save()) {
                // записать соглашения
                if (!empty($modelform->agrees)) {
                    foreach ($modelform->agrees as $agree) {
                        if ($modelform->agreements[$agree->id] == 1) {
                            $agree_sign = new \app\modules\usersigns\models\Usersigns();
                            $agree_sign->user_id = $user->id;
                            $agree_sign->form_model = Formagree::TYPE_EDUPROGTICKET;
                            $agree_sign->form_id = $new_order->id;
                            $agree_sign->agreement_id = $agree->id;
                            $agree_sign->comment = $agree->label_text;
                            $agree_sign->save();
                        }
                    }
                }

                // создать подписанные договора
                if (!empty($modelform->contracts)) {
                    foreach ($modelform->contractList as $contract) {
                        if ($modelform->contracts[$contract->id] == 1) {
                            $contract_sign = new \app\modules\educontractitem\models\Educontractitem();
                            $contract_sign->name = 'Новый договор';
                            $contract_sign->user_id = $user->id;
                            $contract_sign->order_id = $new_order->id;
                            $contract_sign->eduprog_id = $new_order->eduprog_id;
                            $contract_sign->contract_id = $contract->id;
                            $contract_sign->visible = 1;
                            $contract_sign->contract_date = date('d.m.Y');
                            if ($contract_sign->save()) {
                                $contract_sign->setContractNumber();
                            }

                        }
                    }
                }

                $count_by_tariff = [];
                foreach ($modelform->ticket_info['tariff'] as $key => $tariff_id) {
                    /* считаем количество билетов по тарифу */
                    $count_by_tariff[$tariff_id] = ($count_by_tariff[$tariff_id] ? 1 : $count_by_tariff[$tariff_id] + 1);
                    /* для каждого билета смотрим тариф */
                    $tariff = Tariff::find()->where(['id' => (int)$tariff_id, 'deleted' => 0])->one();
                    /* если тариф не существует по нему не может быть продажи для выбранного мероприятия */
                    if (empty($tariff)) {
                        /* удалить заказ и вывести ошибку */
                        $new_order->delete();
                        return [
                            'status' => 'fail',
                            'message' => 'Невозможно приобрести билет по одному из тарифов.',
                        ];
                    }

                    $new_item = new EduprogorderItem();
                    $new_item->eduprogorder_id = $new_order->id;
                    $new_item->tariff_id = $tariff->id;
                    $new_item->status = EduprogorderItem::STATUS_NEW;
                    $new_item->user_id = $modelform->ticket_info['user_id'][$key];
                    $new_item->price = $tariff->currentPrice;
                    // сохранение доп.полей билета
                    $form_data = [];
                    $form_fields = $tariff->eduprogForm->form_fields;
                    foreach ($form_fields as $field) {
                        $form_data[$field['name']] = $modelform->ticket_info[$field['sysname']][$key];
                    }
                    $new_item->form_data = $form_data;
                    $new_item->visible = 1;

                    if (!$new_item->save()) {
                        // удалить заказ и вывести ошибку
                        $log_path = Yii::getAlias('@app/logs/');
                        file_put_contents($log_path . 'eduprog_buyticket.txt', date('d.m.Y H:i:s') . ' - Ошибка создания билета ' . $new_item->id . "\r\n" . var_export($new_item->errors, true) . "\r\n", FILE_APPEND);
                        $new_order->delete();
                        return [
                            'status' => 'fail',
                            'message' => 'Добавление билета невозможно. Повторите заказ.',
                        ];
                    }
                }

                $new_order->refresh();
                /* если итоговая сумма заказа равна 0 - заказ считать оплаченным */
                if ($new_order->price == 0) {
                    $new_order->applyPayment();
                    // редирект на страницу Заказов ДПО
                    $payment_page = LKEduprogClientOrder::find()->where(['model' => LKEduprogClientOrder::class, 'visible' => 1])->one();
                    if ($payment_page) {
                        $payment_url = Url::toRoute([$payment_page->getUrlPath(), 'id' => $eduprog->id]);
                    } else {
                        $payment_url = '/';
                    }
                } else {
                    // редирект на страницу выбора оплаты
                    $payment_page = SelectPayment::find()->where(['model' => SelectPayment::class, 'visible' => 1])->one();
                    $payment_url = (!empty($payment_page)) ? Url::toRoute([$payment_page->getUrlPath(), 'category' => PaymentSystem::USEDIN_EDUPROG, 'id' => $new_order->id]) : false;
                }
                $modelform->updateUserProfile($user->profile);
                // редирект на страницу оплаты
                return [
                    'status' => 'success',
                    'redirect_to' => $payment_url,
                    'message' => 'Заказ создан',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Создание заказа невозможно. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'При оформлении заказа возникли ошибки. ' . \app\helpers\MainHelper::getHelpText(),
        ];
    }

    /* страница списка программ */
    public function actionEduproglist($model)
    {
        // выключатель ДПО
        if (!Yii::$app->params['enable_dpo']) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        /* страница доступна только ЭО с регистрацией в качестве Юрлица */
        if (!in_array($user->role, ['exporg']) or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        /* ищем программы текущего пользователя (корпоративные не отображаем в ЛК) */
        $eduprog_query = Eduprog::find()->andWhere(['author_id' => $user->id, 'visible' => 1, 'is_corporative' => 0])->andWhere(['!=', 'status', Eduprog::STATUS_DECLINED]);

        $curr_status = Yii::$app->request->get('status', '');
        /* фильтрация по выбранному статусу */
        switch ($curr_status) {
            case 'published':
                // по логике программы, у которых закончился срок публикации сюда не должны попадать, но другой категории для них нет. Если их исключить - то найти их вообще не будет возможности.
                $eduprog_query->andWhere(['status' => Eduprog::STATUS_PUBLIC])->andWhere(['<=', 'start_publish', new \yii\db\Expression('CURDATE()')])->andWhere(['>=', 'date_stop', new \yii\db\Expression('CURDATE()')]);
                break;
            case 'planned':
                $eduprog_query->andWhere(['status' => Eduprog::STATUS_PUBLIC])->andWhere(['>', 'start_publish', new \yii\db\Expression('CURDATE()')])->andWhere(['>=', 'date_stop', new \yii\db\Expression('CURDATE()')]);
                break;
            case 'moderate':
                $eduprog_query->andWhere(['status' => Eduprog::STATUS_MODERATE]);
                break;
            case 'cancelled':
                $eduprog_query->andWhere(['status' => Eduprog::STATUS_CANCELLED]);
                break;
            case 'moderate_edit':
                $eduprog_query->andWhere(['status' => Eduprog::STATUS_NEED_EDIT]);
                break;
            // не выводится сейчас
            case 'invisible':
                $eduprog_query->andWhere(['status' => Eduprog::STATUS_PUBLIC, 'eduprog.visible' => 0])->andWhere(['status' => Eduprog::STATUS_PUBLIC])->andWhere(['>', 'date_stop', new \yii\db\Expression('CURDATE()')]);
                break;
            case 'draft':
                $eduprog_query->andWhere(['status' => Eduprog::STATUS_NEW]);
                break;
            case 'archive':
                $eduprog_query->andWhere(['status' => Eduprog::STATUS_PUBLIC])->andWhere(['<', 'date_stop', new \yii\db\Expression('CURDATE()')]);
                break;
        }

        $eduprog_query->orderBy([new \yii\db\Expression('FIELD( status, "' . implode('","', [Eduprog::STATUS_NEED_EDIT, Eduprog::STATUS_MODERATE, Eduprog::STATUS_NEW, Eduprog::STATUS_PUBLIC, Eduprog::STATUS_CANCELLED]) . '")'), 'date_stop' => SORT_DESC]);

        $eduprog_items = $eduprog_query->all();

        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'items' => $eduprog_items, 'curr_status' => $curr_status]);
    }

    /* страница создания/редактирования мероприятия */
    public function actionEduprogedit($model)
    {
        // выключатель ДПО
        if (!Yii::$app->params['enable_dpo']) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        /* проверка разрешения публикации ДПО */
        if (!$user->organization->can_service) {
            /* выводим сообщение о необходимости регистрации на маркетплейс */
            return $this->render($model->view, ['model' => $model, 'content' => $model->need_marketplace]);
        }

        /* проверка разрешения публикации ДПО */
        if (!$user->organization->license_service) {
            /* выводим сообщение о необходимости регистрации на маркетплейс */
            return $this->render($model->view, ['model' => $model, 'content' => $model->need_license]);
        }

        /* страница доступна только ЭО с регистрацией в качестве Юрлица */
        if (!in_array($user->role, ['exporg']) or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        $curr_step = Yii::$app->request->get('step', '1');

        $eduprog_id = Yii::$app->request->get('id');
        $original_eduprog = false;

        /* страница списка программ ДПО */
        $eduprog_page = LKEduprogList::find()->where(['model' => LKEduprogList::class, 'visible' => 1])->one();

        if ($eduprog_id) {
            $original_eduprog = Eduprog::findOne((int)$eduprog_id);

            /* если не нашли или редактировать пытается не автор, то редирект на список. */
            if ((!$original_eduprog) or ($original_eduprog->author_id != $user->id)) {
                return $this->redirect($eduprog_page->getUrlPath());
            }

            /* программы на модерации, отменённые и отклонённые редактировать нельзя */
            if (in_array($original_eduprog->status, [Eduprog::STATUS_MODERATE, Eduprog::STATUS_CANCELLED, Eduprog::STATUS_DECLINED])) {
                return $this->redirect($eduprog_page->getUrlPath());
            }

            /* корпоративные программы из ЛК редактировать нельзя */
            if ($original_eduprog->is_corporative) {
                return $this->redirect($eduprog_page->getUrlPath());
            }

        } elseif ($curr_step != 1) {
            /* если id не указан и это не первый шаг, то редирект на список */
            return $this->redirect($eduprog_page->getUrlPath());
        }

        $eduprog_model = new LKEduprog();
        if ($original_eduprog) {
            $eduprog_model->loadFromEduprog($original_eduprog);
        }
        switch ($curr_step) {
            case '2':
                $eduprog_model->step = 'step2';
                $eduprog_model->scenario = 'step2';
                if (empty($eduprog_model->contact_email)) {
                    $eduprog_model->contact_email = $user->email;
                }
                break;
            case '3':
                $eduprog_model->step = 'step3';
                $eduprog_model->scenario = 'step3';
                break;
            case '4': // на этом шаге форма не нужна
                break;
            case '5':
                $eduprog_model->step = 'step5';
                $eduprog_model->scenario = 'step5';
                break;
            case '6':
                $eduprog_model->step = 'step6';
                $eduprog_model->scenario = 'step6';
                break;
            case '1':
            default:
                $curr_step = '1';
                $eduprog_model->step = 'step1';
                $eduprog_model->scenario = 'step1';
                break;
        }
        $this->setMeta($model);
        return $this->render($model->view . '_step_' . $curr_step, ['model' => $model, 'eduprog_model' => $eduprog_model, 'original' => $original_eduprog]);
    }

    public function actionSaveeduprog()
    {
        // выключатель ДПО
        if (!Yii::$app->params['enable_dpo']) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /** @var UserAR $user Пользователь */
        $user = Yii::$app->user->identity->userAR;

        /* публиковать программы могут только ЭО с разрешениями и в качетсве ООО */
        if (!in_array($user->role, ['exporg']) or !$user->organization->can_service or !$user->organization->license_service or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на создание программы',
            ];
        }

        /* проверяем указанный шаг и загружаем соответствующий сценарий для модели LKEduprog */
        $curr_step = Yii::$app->request->get('step', '1');
        if (in_array($curr_step, ['1', '2', '3', '4', '5', '6'])) {
            $modelform = new LKEduprog();
            $modelform->scenario = 'step' . $curr_step;
        } else {
            return [
                'status' => 'fail',
                'message' => 'Неверные параметры',
            ];
        }

        if ($modelform->sanitize(Yii::$app->request->post())) {
            // загружаем изображения для валидации на бэке (все изображения у нас на 3-м шаге)
            if ($curr_step == 3) {
                $modelform->report = UploadedFile::getInstances($modelform, 'report');
                $modelform->image = UploadedFile::getInstance($modelform, 'image');
                $modelform->videoimage_loader = UploadedFile::getInstances($modelform, 'videoimage_loader');
                $modelform->lectorimage_loader = UploadedFile::getInstances($modelform, 'lectorimage_loader');
            }

            // загружаем файл оферты для валидации на бэке
            if ($curr_step == 6) {
                $modelform->oferta = UploadedFile::getInstance($modelform, 'oferta');
            }

            if ($modelform->validate()) {
                /* получаем данные из POST */
                $post = Yii::$app->request->post();

                /* если в полученных данных указан id - ищем программу */
                if (!empty($modelform->id)) {
                    $new_eduprog = Eduprog::findOne((int)$modelform->id);
                    /* если текущий пользователь не является организатором программы - ошибка. */
                    if ($new_eduprog?->author_id != $user->id) {
                        return [
                            'status' => 'fail',
                            'message' => 'У вас нет прав на редактирование мероприятия',
                        ];
                    }
                    /* иначе создаем новое */
                } else {
                    if (is_null($user->directionM)) {
                        return [
                            'status' => 'fail',
                            'message' => 'Ошибка. Главная кафедра не задана',
                        ];
                    }
                    $new_eduprog = new Eduprog();
                    /* заполняем обязательные поля (name и category_id должно быть заполнены на первом шаге.) */

                    $new_eduprog->date_start = date('d.m.Y');
                    $new_eduprog->date_stop = date('d.m.Y');
                    $new_eduprog->date_close = date('d.m.Y', strtotime('+1 day'));
                    $new_eduprog->direction_id = $user->directionM->id;
                    $new_eduprog->author_id = $user->id;
                    $new_eduprog->city_id = $user->profile->city_id;
                    $new_eduprog->status = Eduprog::STATUS_NEW;
                    $new_eduprog->visible = 1;
                }

                /* в зависимости от текущего шага выполняем необходимые действия */
                /* заявка на модерацию заполняется только если есть изменённые данные, подлежащие модерации */
                $moderation = false;

                switch ($curr_step) {
                    case '1':
                        /* модерация требуется, если мероприятие новое, или поля name, category_id, age_id, qualification, hours были изменены */
                        if ($new_eduprog->isNewRecord
                            or ($new_eduprog->name != $modelform->name)
                            or ($new_eduprog->category_id != $modelform->category_id)
                            or ($new_eduprog->age_id != $modelform->age_id)
                            or ($new_eduprog->qualification != $modelform->qualification)
                            or ($new_eduprog->member_total != $modelform->member_total)
                            or ($new_eduprog->hours != $modelform->hours)) {

                            /* если мероприятие новое или у него нет текущей записи о модерации, то создаем новую модерацию */
                            if ($new_eduprog->isNewRecord or empty($new_eduprog->currentModeration)) {
                                $moderation = $new_eduprog->addNewModeration();
                            } else {
                                /* достаем текущую заявку на модерацию */
                                $moderation = $new_eduprog->currentModeration;
                            }

                            /* если мероприятие новое, то */
                            if ($new_eduprog->isNewRecord) {
                                /* модерация первичная */
                                $moderation->first_moderation = true;
                                /* поля name и licensed заполняем и для мероприятия тоже */
                                $new_eduprog->name = $modelform->name;
                                $new_eduprog->member_total = $modelform->member_total;
                                $new_eduprog->category_id = $modelform->category_id;
                                $new_eduprog->age_id = $modelform->age_id;
                                $new_eduprog->qualification = $modelform->qualification;
                                $new_eduprog->hours = $modelform->hours;
                            }

                            /* заполняем поля модерации */
                            $moderation->name = $modelform->name;
                            $moderation->member_total = $modelform->member_total;
                            $moderation->category_id = $modelform->category_id;
                            $moderation->age_id = $modelform->age_id;
                            $moderation->qualification = $modelform->qualification;
                            $moderation->hours = $modelform->hours;
                        }

                        break;
                    case '2':
                        if (empty($new_eduprog->ordersAll)) {
                            // если есть заказы - то дата меняется через заявку
                            $new_eduprog->date_start = $modelform->date_start;
                            $new_eduprog->date_stop = $modelform->date_stop;
                            $new_eduprog->date_close = $modelform->date_close;
                        }

                        /* если были отредактированы модерируемые поля, то добавляем данные в модерацию */
                        if (($new_eduprog->date_stop_sale != $modelform->date_stop_sale)
                            or ($new_eduprog->shedule_text != $modelform->shedule_text)
                            or ($new_eduprog->format != $modelform->format)
                            or ($new_eduprog->city_id != $modelform->city_id)
                            or ($new_eduprog->address != $modelform->address)
                            or ($new_eduprog->place != $modelform->place)
                            or ($new_eduprog->rules != $modelform->rules)
                            or ($new_eduprog->contact_email != $modelform->contact_email)
                            or ($new_eduprog->contact_phone != $modelform->contact_phone)
                            or ($new_eduprog->contact_wa != $modelform->contact_wa)
                            or ($new_eduprog->contact_telegram != $modelform->contact_telegram)
                        ) {

                            /* если у мероприятия нет текущей записи о модерации, то создаем новую модерацию */
                            if (empty($new_eduprog->currentModeration)) {
                                $moderation = $new_eduprog->addNewModeration();
                            } else {
                                /* достаем текущую заявку на модерацию */
                                $moderation = $new_eduprog->currentModeration;
                            }

                            /* если мероприятие новое, то заполняем поля и для мероприятия */
                            if ($new_eduprog->status == Eduprog::STATUS_NEW) {
                                $new_eduprog->date_close = $modelform->date_close;
                                $new_eduprog->date_stop_sale = $modelform->date_stop_sale;
                                $new_eduprog->shedule_text = $modelform->shedule_text;
                                $new_eduprog->format = $modelform->format;
                                $new_eduprog->city_id = $modelform->city_id;
                                $new_eduprog->address = $modelform->address;
                                $new_eduprog->place = $modelform->place;
                                $new_eduprog->rules = $modelform->rules;
                                $new_eduprog->contact_email = $modelform->contact_email;
                                $new_eduprog->contact_phone = $modelform->contact_phone;
                                $new_eduprog->contact_wa = $modelform->contact_wa;
                                $new_eduprog->contact_telegram = $modelform->contact_telegram;
                            }

                            /* заполняем поля модерации */
                            $moderation->date_close = $modelform->date_close;
                            $moderation->date_stop_sale = $modelform->date_stop_sale;
                            $moderation->shedule_text = $modelform->shedule_text;
                            $moderation->format = $modelform->format;
                            $moderation->city_id = $modelform->city_id;
                            $moderation->address = $modelform->address;
                            $moderation->place = $modelform->place;
                            $moderation->rules = $modelform->rules;
                            $moderation->contact_email = $modelform->contact_email;
                            $moderation->contact_phone = $modelform->contact_phone;
                            $moderation->contact_wa = $modelform->contact_wa;
                            $moderation->contact_telegram = $modelform->contact_telegram;
                        }
                        break;
                    case '3':
                        /* если были отмечены флаги "Удалить изображение" */
                        /* удаляем то изображение, которое пользователь видел, когда нажимал "Удалить" */
                        $attributes = ['image'];
                        foreach ($attributes as $image_attribute) {
                            if ($modelform->{$image_attribute . '_delete'}) {
                                /* если есть текущая модерация и у модели модерации есть загруженное изображение в этом поле - удаляем */
                                if (!empty($new_eduprog->currentModeration) && !empty($new_eduprog->currentModeration->{$image_attribute})) {
                                    $new_eduprog->currentModeration->{$image_attribute}->delete();
                                } elseif (!empty($new_eduprog->{$image_attribute})) {
                                    /* иначе - если в модели мероприятия есть загруженное изображение - удаляем */
                                    $new_eduprog->{$image_attribute}->delete();
                                }
                            }
                        }
                        $preloaded_report = $new_eduprog->report;
                        if ($new_eduprog->currentModeration) {
                            $preloaded_report = array_merge($preloaded_report, $new_eduprog->currentModeration->report);
                            if (!empty($new_eduprog->currentModeration->remove_report)) {
                                foreach ($preloaded_report as $key => $image) {
                                    if (in_array($image->id, $new_eduprog->currentModeration->remove_report)) {
                                        unset($preloaded_report[$key]);
                                    }
                                }
                            }
                        }
                        $preloaded_ids = ArrayHelper::map($preloaded_report, 'id', 'id');
                        /* если предзагруженные изображения отличаются от предзагруженных в форме - значит что-то удалили - отправляем в модерацию */
                        $model_preloaded = $modelform->report_preload ? $modelform->report_preload : [];
                        $report_to_delete = array_diff($preloaded_ids, $model_preloaded);

                        /* обрабатываем данные о видео */
                        $video_data = (!empty($modelform->video) ? $modelform->video : []);
                        $videoimage_loader = [];
                        if (!empty($video_data) && is_array($video_data)) {
                            // если есть видео - заполняем порядок и видимость, загружаем и сохраняем изображения для модели модерации
                            $order = 0;
                            foreach ($video_data as $key => $row) {
                                $image_loaded = UploadedFile::getInstances($modelform, 'videoimage_loader[' . $key . ']');
                                if (empty($row['name']) && empty($row['link']) && empty($image_loaded)) {
                                    unset($video_data[$key]);
                                } else {
                                    $video_data[$key]['visible'] = 1;
                                    $video_data[$key]['order'] = $order++;
                                    $videoimage_loader[$key] = $image_loaded;
                                }
                            }
                            $modelform->video = $video_data;
                        }
                        foreach ($videoimage_loader as $i => $instance) {
                            if (empty($instance)) {
                                unset($videoimage_loader[$i]);
                            }
                        }

                        /* обрабатываем данные о структуре */
                        $structure_data = (!empty($modelform->structure) ? $modelform->structure : []);
                        if (!empty($structure_data) && is_array($structure_data)) {
                            // если есть видео - заполняем порядок и видимость, загружаем и сохраняем изображения для модели модерации
                            $order = 0;
                            foreach ($structure_data as $key => $row) {
                                if (empty($row['name']) && empty($row['content'])) {
                                    unset($structure_data[$key]);
                                } else {
                                    $structure_data[$key]['visible'] = 1;
                                    $structure_data[$key]['order'] = $order++;
                                }
                            }
                            $modelform->structure = $structure_data;
                        }

                        /* обрабатываем данные о преподавателях */
                        $lectors_data = $modelform->lectors;
                        $lectorimage_loader = [];
                        if (!empty($lectors_data) && is_array($lectors_data)) {
                            // если есть преподаватели - заполняем порядок и видимость, загружаем и сохраняем изображения для модели модерации
                            $order = 0;
                            foreach ($lectors_data as $key => $row) {
                                $image_loaded = UploadedFile::getInstances($modelform, 'lectorimage_loader[' . $key . ']');
                                if (empty($row['fio']) && empty($row['content']) && empty($row['user_id']) && empty($row['video_link']) && empty($image_loaded)) {
                                    unset($lectors_data[$key]);
                                } else {
                                    $lectors_data[$key]['visible'] = 1;
                                    $lectors_data[$key]['order'] = $order++;
                                    $lectorimage_loader[$key] = $image_loaded;
                                }
                            }
                            $modelform->lectors = $lectors_data;
                        }
                        foreach ($lectorimage_loader as $i => $instance) {
                            if (empty($instance)) {
                                unset($lectorimage_loader[$i]);
                            }
                        }

                        /* проверяем теги */
                        $exist_tags = array_values(ArrayHelper::map($new_eduprog->tags, 'id', 'id'));
                        if (empty($modelform->tags)) {
                            $modelform->tags = [];
                        }

                        /* проверяем ключевые слова */
                        $exist_keywords = array_values(ArrayHelper::map($new_eduprog->keywords, 'id', 'id'));
                        if (empty($modelform->keywords)) {
                            $modelform->keywords = [];
                        }

                        /* если были отредактированы модерируемые поля, или были загружены изображения, то добавляем данные в модерацию */
                        if (($new_eduprog->content != $modelform->content)
                            or ($new_eduprog->video_title != $modelform->video_title)
                            or (MainHelper::diff_multifields($new_eduprog->video, $modelform->video, ['name', 'link', 'image']))
                            or (MainHelper::diff_multifields($new_eduprog->structure, $modelform->structure, ['name', 'content']))
                            or (MainHelper::diff_multifields($new_eduprog->lectors, $modelform->lectors, ['fio', 'content', 'video_link', 'image']))
                            or ($new_eduprog->learn != $modelform->learn)
                            or ($new_eduprog->cost_text != $modelform->cost_text)
                            or ($new_eduprog->suits_for != $modelform->suits_for)
                            or ($new_eduprog->block_title != $modelform->block_title)
                            or ($new_eduprog->block_text != $modelform->block_text)
                            or ($new_eduprog->works_title != $modelform->works_title)
                            or ($new_eduprog->works_text != $modelform->works_text)
                            or (!empty(array_diff($modelform->tags, $exist_tags)) or !empty(array_diff($exist_tags, $modelform->tags)))
                            or (!empty(array_diff($modelform->keywords, $exist_keywords)) or !empty(array_diff($exist_keywords, $modelform->keywords)))
                            or (!empty($modelform->image))
                            or (!empty($modelform->report))
                            or (!empty($videoimage_loader))
                            or (!empty($lectorimage_loader))
                            or (!empty($report_to_delete))
                        ) {

                            /* если у программы нет текущей записи о модерации, то создаем новую модерацию */
                            if (empty($new_eduprog->currentModeration)) {
                                $moderation = $new_eduprog->addNewModeration();
                            } else {
                                /* достаем текущую заявку на модерацию */
                                $moderation = $new_eduprog->currentModeration;
                            }

                            /* если мероприятие новое, то заполняем поля и для мероприятия */
                            if ($new_eduprog->status == Eduprog::STATUS_NEW) {
                                $new_eduprog->content = $modelform->content;
                                $new_eduprog->video = $modelform->video;
                                $new_eduprog->structure = $modelform->structure;
                                $new_eduprog->lectors = $modelform->lectors;
                                $new_eduprog->video_title = $modelform->video_title;
                                $new_eduprog->learn = $modelform->learn;
                                $new_eduprog->cost_text = $modelform->cost_text;
                                $new_eduprog->suits_for = $modelform->suits_for;
                                $new_eduprog->block_title = $modelform->block_title;
                                $new_eduprog->block_text = $modelform->block_text;
                                $new_eduprog->works_title = $modelform->works_title;
                                $new_eduprog->works_text = $modelform->works_text;
                                // теги не заполняем, иначе придется добавлять в справочник непромодерированные теги
                            }

                            /* заполняем поля модерации */
                            $moderation->content = $modelform->content;
                            $moderation->video = $modelform->video;
                            $moderation->structure = $modelform->structure;
                            $moderation->lectors = $modelform->lectors;
                            $moderation->video_title = $modelform->video_title;
                            $moderation->learn = $modelform->learn;
                            $moderation->cost_text = $modelform->cost_text;
                            $moderation->suits_for = $modelform->suits_for;
                            $moderation->block_title = $modelform->block_title;
                            $moderation->block_text = $modelform->block_text;
                            $moderation->works_title = $modelform->works_title;
                            $moderation->works_text = $modelform->works_text;
                            $moderation->tags = $modelform->tags;
                            $moderation->keywords = $modelform->keywords;

                            /* если были загружены изображения - добавляем их только к заявке на модерацию (чтобы не дублировать) */
                            $moderation->image_loader = $modelform->image;

                            /* добавляем файлы галереи к заявке на модерацию */
                            if (!empty($modelform->report)) {
                                $moderation->report_loader = $modelform->report;
                            }

                            if (!empty($report_to_delete)) {
                                foreach ($report_to_delete as $id_to_del) {
                                    // если нужно удалить изображение, привязанное к заявке на модерацию - удаляем.
                                    // если нужно удалить изображение, привязанное к модели мероприятия - добавляем в список к удалению.
                                    $image = FilestoreModel::findOne($id_to_del);
                                    if (($image->keeper_class == Eduprog::class) && ($image->keeper_id == $new_eduprog->id)) {
                                        $remove_report = is_array($moderation->remove_report) ? $moderation->remove_report : [];
                                        $moderation->remove_report = array_merge($remove_report, [$id_to_del]);
                                    } elseif (($image->keeper_class == Eduprogmoder::class) && ($image->keeper_id == $moderation->id)) {
                                        $image->delete();
                                    }
                                }
                            }
                            /* добавляем файлы видеоизображений к заявке на модерацию */
                            if (!empty($videoimage_loader)) {
                                $moderation->videoimage_loader = $videoimage_loader;
                            }

                            /* добавляем файлы видеоизображений к заявке на модерацию */
                            if (!empty($lectorimage_loader)) {
                                $moderation->lectorimage_loader = $lectorimage_loader;
                            }
                        }

                        break;
                    // case '4': // do nothing. На четвертом шаге редактируются другие сущности. В Программе ничего не изменяется. break;
                    case '5':
                        /* если были отредактированы модерируемые поля, то добавляем данные в модерацию */
                        if (($new_eduprog->success_text != $modelform->success_text)
                            or ($new_eduprog->success_letter != $modelform->success_letter)
                        ) {

                            /* если у мероприятия нет текущей записи о модерации, то создаем новую модерацию */
                            if (empty($new_eduprog->currentModeration)) {
                                $moderation = $new_eduprog->addNewModeration();
                            } else {
                                /* достаем текущую заявку на модерацию */
                                $moderation = $new_eduprog->currentModeration;
                            }

                            /* если мероприятие новое, то заполняем поля и для мероприятия */
                            if ($new_eduprog->status == Eduprog::STATUS_NEW) {
                                $new_eduprog->success_text = $modelform->success_text;
                                $new_eduprog->success_letter = $modelform->success_letter;
                            }

                            /* заполняем поля модерации */
                            $moderation->success_text = $modelform->success_text;
                            $moderation->success_letter = $modelform->success_letter;
                        }
                        break;

                    case '6':
                        $moderation = false;
                        /* достаем текущую запись о модерации, если есть */
                        if (!empty($new_eduprog->currentModeration)) {
                            $moderation = $new_eduprog->currentModeration;
                        }
                        /* если был загружен файл оферты, то добавляем данные в модерацию */
                        if (!empty($modelform->oferta)) {
                            /* если у мероприятия нет текущей записи о модерации, то создаем новую модерацию */
                            if (empty($moderation)) {
                                $moderation = $new_eduprog->addNewModeration();
                            }
                            /* добавляем оферту только к заявке на модерацию (чтобы не дублировать) */
                            $moderation->oferta_loader = $modelform->oferta;
                        } else {
                            // если файл не был загружен - проверяем наличие договора
                            if (!(($moderation && !empty($moderation->oferta)) or ($new_eduprog && !empty($new_eduprog->oferta)))) {
                                // файл договора отсутствует и не был загружен
                                return [
                                    'status' => 'fail',
                                    'message' => 'Необходимо загрузить файл договора оферты',
                                ];
                            }
                        }
                        if ($modelform->start_publish_late == 1) {
                            // время публикации и часовой пояс пока не учитываю (сроки...)
                            $new_eduprog->start_publish = $modelform->start_publish_date;
                        } else {
                            $new_eduprog->start_publish = date('d.m.Y');
                        }

                        /* достаем текущую заявку на модерацию, если есть и если не искали ранее на этом шаге */
                        if (!isset($moderation)) {
                            $moderation = $new_eduprog->currentModeration;
                        }
                        // выбор пользователя - сохранить в черновик или отправить на модерацию
                        $moderate_mode = Yii::$app->request->post('moderate_mode', 0);
                        if ($moderate_mode == 1 && !empty($moderation)) {
                            /* если у мероприятия есть заявка на модерацию в статусе Новая - то отправляем мероприятие на модерацию */
                            $new_eduprog->status = Eduprog::STATUS_MODERATE;
                            $moderation->status = Eduprogmoder::STATUS_MODERATE;
                        }
                        break;
                }
                /* сохраняем программу */
                if ($new_eduprog->save()) {
                    /* если для программы есть изменения в модерации */
                    if (!empty($moderation)) {
                        /* если заявка на модерацию новая, привязываем её к мероприятию */
                        if ($moderation->isNewRecord) {
                            $moderation->eduprog_id = $new_eduprog->id;
                        }
                        /* сохраняем заявку на модерацию */
                        if (!$moderation->save()) {
                            /* если не сохранилось - пишем лог и выводим ошибку пользователю */
                            $log_path = Yii::getAlias('@app/logs/');
                            file_put_contents($log_path . 'eduprog_edit_log.txt', date('d.m.Y H:i:s') . ' - Ошибка сохранения заявки на модерацию к программе ' . $new_eduprog->id . "\r\n" . var_export($moderation->errors, true) . "\r\n", FILE_APPEND);
                            return [
                                'status' => 'fail',
                                'message' => 'При сохранении программы возникла ошибка. ' . \app\helpers\MainHelper::getHelpText(),
                            ];
                        }
                    }
                    /* следующий шаг */
                    $next_step = ++$curr_step;
                    /* если шаги закончились */
                    if ($next_step > 6) {
                        // готово, возвращаемся к списку программ
                        $next_page = LKEduprogList::find()->where(['model' => LKEduprogList::class, 'visible' => 1])->one();
                        $redirect_to = $next_page->getUrlPath();
                        $message = SettingsText::getInfo('eduprog_moder_modal');
                    } else {
                        // следующий шаг
                        $next_page = LKEduprogEdit::find()->where(['model' => LKEduprogEdit::class, 'visible' => 1])->one();
                        $redirect_to = Url::toRoute([$next_page->getUrlPath(), 'step' => $next_step, 'id' => $new_eduprog->id]);
                        $message = 'Переход на следующий шаг.';
                    }
                    /* возвращаем результат */
                    return [
                        'status' => 'success',
                        'redirect_to' => $redirect_to,
                        'message' => $message,
                        /* если программа на модерации - то показать сообщение о модерации */
                        'show_message' => ($new_eduprog->status == Eduprog::STATUS_MODERATE ? 'show' : 'hide'), // show
                    ];
                }
                /* если программа не сохранилось - пишем лог и выдаем ошибку пользователю */
                $log_path = Yii::getAlias('@app/logs/');
                file_put_contents($log_path . 'eduprog_edit_log.txt', date('d.m.Y H:i:s') . ' - Ошибка сохранения программы ' . $new_eduprog->id . "\r\n" . var_export($new_eduprog->errors, true) . "\r\n", FILE_APPEND);

                $errors = [];
                foreach ($new_eduprog->errors as $field_error) {
                    $errors[] = implode("<br>", $field_error);
                }
                return [
                    'status' => 'fail',
                    'message' => 'При сохранении программы возникли ошибки:<br> ' . implode('', $errors) . ' <br> ' . \app\helpers\MainHelper::getHelpText(),
                ];

            }
            // не прошла валидация формы
            $log_path = Yii::getAlias('@app/logs/');
            file_put_contents($log_path . 'eduprog_edit_log.txt', date('d.m.Y H:i:s') . " - Ошибка валидации формы \r\n" . var_export($modelform->errors, true) . "\r\n", FILE_APPEND);

            $errors = [];
            foreach ($modelform->errors as $field_error) {
                $errors[] = implode("<br>", $field_error);
            }
            return [
                'status' => 'fail',
                'message' => 'При сохранении программы возникли ошибки:<br> ' . implode('', $errors) . ' <br> ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        // не ajax-запрос или не загружаются данные в форму
        return [
            'status' => 'fail',
            'message' => 'При сохранении данных возникла ошибка. ' . \app\helpers\MainHelper::getHelpText(),
        ];
    }

    // поиск экспертов для формы выбора Преподавателей курса
    public function actionSearchLector()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->user->isGuest) {
            return ['status' => 'fail', 'message' => 'Вы не авторизованы'];
        }
        if (Yii::$app->request->isAjax) {
            $user = Yii::$app->user->identity->userAR;
            // обрабатываем только ajax-запросы
            $get = Yii::$app->request->get();
            $q = mb_strtolower($get['q'], 'UTF8');
            // найти пользователей, у которых в ФИО встречается заданная строка

            $profiles = UserAR::find()
                ->leftJoin('profile', 'profile.user_id = user.id')
                ->where(
                    ['or',
                        ['LIKE', 'LCASE(CONCAT(`profile`.`surname`," ",`profile`.`name`))', $q],
                        ['LIKE', 'LCASE(CONCAT(`profile`.`name`," ",`profile`.`surname`))', $q],
                    ]
                )
                ->visible(['expert'])
                ->andWhere(['!=', 'user.id', $user->id]);
            $profiles = $profiles->all();
            $items = [];
            foreach ($profiles as $item) {
                $items[] = ['id' => $item->profile->user_id, 'text' => $item->profile->halfname];
            }
            return [
                'status' => 'success',
                'items' => $items,
            ];
        }
        return [
            'status' => 'fail',
        ];

    }

    /* данные о преподавателе/пользователе */
    public function actionInfoLector()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->user->isGuest) {
            return ['status' => 'fail', 'message' => 'Вы не авторизованы'];
        }
        if (Yii::$app->request->isAjax) {
            // обрабатываем только ajax-запросы
            $user_id = Yii::$app->request->post('user_id', false);
            if ($user_id) {
                $user = UserAR::find()->where(['id' => $user_id, 'status' => UserAR::STATUS_ACTIVE])->one();
                if ($user && in_array($user->role, ['expert', 'exporg'])) {
                    $html = $this->renderPartial('_lector', ['item' => $user]);
                    return [
                        'status' => 'success',
                        'html' => $html,
                        'fio' => $user->profile->halfname,
                        'message' => 'Пользователь найден',
                    ];
                }
                return [
                    'status' => 'success',
                    'html' => '<p>Выбранного пользователя нельзя указать в качестве преподавателя</p>',
                    'message' => 'Пользователь не найден',
                ];

            }
        }
        return [
            'status' => 'fail',
        ];
    }

    /* добавление/редактирование формы регистрации на программу */
    public function actionSaveRegform()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        $log_path = Yii::getAlias('@app/logs/');

        // валидируем данные с формы
        $modelform = new LKEduprogRegform();
        if ($modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            $user = Yii::$app->user->identity->userAR;
            // проверить право редактировать/создавать формы для указанной программы
            $eduprog = Eduprog::findOne((int)$modelform->eduprog_id);
            if ($eduprog->author_id != $user->id) {
                return [
                    'status' => 'fail',
                    'message' => 'Невозможно добавить форму к данной программе',
                ];
            }
            if (!empty($modelform->id)) {
                // найти форму, которую редактируем
                $eduprog_form = EduprogForm::findOne((int)$modelform->id);
                if ($eduprog_form && ($eduprog_form->eduprog_id == $eduprog->id)) {
                    $eduprog_form->name = $modelform->name;
                    $eduprog_form->form_fields = $modelform->fields;
                    if ($eduprog_form->save()) {
                        unset($eduprog->eduprogFormsAll);
                        // всё ок, вывести на странице блок с формой вместо пустого блока
                        return [
                            'status' => 'success',
                            'action' => 'replace',
                            'selector' => '#regform_' . $eduprog_form->id,
                            'total_forms' => count($eduprog->eduprogFormsAll),
                            'html_form' => $this->renderPartial('_registerform', ['form' => $eduprog_form]),
                            'message' => 'Форма обновлена',
                        ];
                    }
                    file_put_contents($log_path . 'eduprog_saveregform.txt', date('d.m.Y H:i:s') . " - Ошибка создания формы \r\n" . var_export($eduprog_form->errors, true) . "\r\n", FILE_APPEND);
                    return [
                        'status' => 'fail',
                        'message' => 'При сохранении данных возникла ошибка. ' . \app\helpers\MainHelper::getHelpText(),
                    ];

                }
                return [
                    'status' => 'fail',
                    'message' => 'Редактируемая форма не найдена',
                ];

            }
            if (count($eduprog->eduprogFormsAll) >= 2) {
                // c фронта нельзя добавлять больше 2-х форм
                return [
                    'status' => 'fail',
                    'message' => 'Допустимое количество форм регистрации превышено',
                ];
            }
            // создаём новую форму
            $eduprog_form = new EduprogForm();
            $eduprog_form->eduprog_id = $eduprog->id;
            $eduprog_form->name = $modelform->name;
            $eduprog_form->form_fields = $modelform->fields;
            $eduprog_form->visible = 1;
            $eduprog_form->order = count($eduprog->eduprogFormsAll);
            if ($eduprog_form->save()) {
                unset($eduprog->eduprogFormsAll);
                // всё ок, вывести на странице блок с формой вместо пустого блока
                return [
                    'status' => 'success',
                    'action' => 'append',
                    'selector' => '#regform_container',
                    'total_forms' => count($eduprog->eduprogFormsAll),
                    'html_form' => $this->renderPartial('_registerform', ['form' => $eduprog_form]),
                    'message' => 'Форма создана',
                ];
            }
            file_put_contents($log_path . 'eduprog_saveregform.txt', date('d.m.Y H:i:s') . " - Ошибка создания формы \r\n" . var_export($eduprog_form->errors, true) . "\r\n", FILE_APPEND);
            return [
                'status' => 'fail',
                'message' => 'При сохранении данных возникла ошибка. ' . \app\helpers\MainHelper::getHelpText(),
            ];


        }
        return [
            'status' => 'fail',
            'message' => 'Ошибка при проверке введенных данных',
        ];

    }

    /* изменить отображение формы регистрации на программу ДПО */
    public function actionSwitchVisibleForm()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->user->isGuest) {
            return ['status' => 'fail', 'message' => 'Вы не авторизованы'];
        }
        if (Yii::$app->request->isAjax) {
            // обрабатываем только ajax-запросы
            $form_id = Yii::$app->request->get('form_id', false);
            if ($form_id) {
                $regform = EduprogForm::find()->where(['id' => $form_id])->one();
                if (!empty($regform) && !empty($regform->eduprog) && ($regform->eduprog->author_id == Yii::$app->user->id)) {
                    // форма принадлежит пользователю - можно менять
                    $visible = (bool)$regform->visible;
                    $regform->visible = !$visible;
                    $regform->updateAttributes(['visible' => $regform->visible]);
                    return [
                        'status' => 'success',
                        'visible' => $regform->visible,
                        'message' => 'Форма ' . ($regform->visible ? 'активирована' : 'скрыта'),
                    ];
                }
                return [
                    'status' => 'fail',
                    'message' => 'Вы не можете редактировать указанную форму',
                ];

            }
        }
        return [
            'status' => 'fail',
        ];
    }

    /* удалить форму регистрации на программу ДПО */
    public function actionRemoveForm()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->user->isGuest) {
            return ['status' => 'fail', 'message' => 'Вы не авторизованы'];
        }
        if (Yii::$app->request->isAjax) {
            // обрабатываем только ajax-запросы
            $form_id = Yii::$app->request->get('form_id', false);
            if ($form_id) {
                $regform = EduprogForm::find()->where(['id' => $form_id])->one();
                if (!empty($regform) && !empty($regform->eduprog) && ($regform->eduprog->author_id == Yii::$app->user->id)) {
                    $eduprog = $regform->eduprog;
                    // форма принадлежит пользователю - можно удалять
                    if ($regform->delete()) {
                        return [
                            'status' => 'success',
                            'total_forms' => count($eduprog->eduprogFormsAll),
                            'message' => 'Форма удалена',
                        ];
                    }
                    return [
                        'status' => 'fail',
                        'message' => 'Невозможно удалить форму. ' . \app\helpers\MainHelper::getHelpText(),
                    ];

                }
                return [
                    'status' => 'fail',
                    'message' => 'Вы не можете удалить указанную форму',
                ];

            }
        }
        return [
            'status' => 'fail',
            'message' => 'Невозможно выполнить действие',
        ];
    }

    /* возвращает html полей формы регистрации на ДПО */
    public function actionFieldsHtml()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->user->isGuest) {
            return ['status' => 'fail', 'message' => 'Вы не авторизованы'];
        }
        if (Yii::$app->request->isAjax) {
            // обрабатываем только ajax-запросы
            $form_id = Yii::$app->request->get('form_id', false);
            if ($form_id) {
                $regform = EduprogForm::find()->where(['id' => $form_id])->one();
                if (!empty($regform) && !empty($regform->eduprog) && ($regform->eduprog->author_id == Yii::$app->user->id)) {
                    // форма принадлежит пользователю - можно редактировать
                    return [
                        'status' => 'success',
                        'html' => $this->renderPartial('_formfields', ['form' => $regform]),
                        'message' => 'Поля формы',
                    ];
                }
                return [
                    'status' => 'fail',
                    'message' => 'Вы не можете редактировать указанную форму',
                ];

            }
        }
        return [
            'status' => 'fail',
            'message' => 'Невозможно выполнить действие',
        ];
    }

    /* добавление/редактирование тарифа к форме регистрации на программу */
    public function actionSaveTariff()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->user->isGuest) {
            return ['status' => 'fail', 'message' => 'Вы не авторизованы'];
        }
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        $log_path = Yii::getAlias('@app/logs/');

        // валидируем данные с формы
        $modelform = new LKEduprogTariff();
        if ($modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            $user = Yii::$app->user->identity->userAR;
            // проверить право редактировать/создавать формы для указанной программы
            $eduprog = Eduprog::findOne((int)$modelform->eduprog_id);
            if (empty($eduprog) or ($eduprog->author_id != $user->id)) {
                return [
                    'status' => 'fail',
                    'message' => 'Невозможно добавить тариф к данной программе',
                ];
            }
            // проверить форму, к которой добавляется тариф
            $eduprog_form = EduprogForm::findOne((int)$modelform->eduprogform_id);
            if (empty($eduprog_form) or ($eduprog_form->eduprog_id != $eduprog->id)) {
                return [
                    'status' => 'fail',
                    'message' => 'Невозможно добавить тариф к данной форме',
                ];
            }
            $action = '';
            $selector = '';
            // проверить наличие и корректность цен
            if (!empty($modelform->id)) {
                // найти тариф, который редактируем
                $eduprog_tariff = Tariff::findOne((int)$modelform->id);
                if (empty($eduprog_tariff) or ($eduprog_tariff->eduprog_id != $eduprog->id) or ($eduprog_tariff->eduprogform_id != $eduprog_form->id)) {
                    return [
                        'status' => 'fail',
                        'message' => 'Невозможно отредактировать указанный тариф',
                    ];
                }
                // заполняем поля тарифа из формы
                $eduprog_tariff->name = $modelform->name;
                $eduprog_tariff->description = $modelform->description;
                $eduprog_tariff->start_publish = $modelform->start_publish; // дата должна передаваться в формате "d.m.Y"
                $eduprog_tariff->end_publish = $modelform->end_publish; // дата должна передаваться в формате "d.m.Y"
                $eduprog_tariff->limit_tickets = !(bool)$modelform->unlimit_tickets; // на фронте используется формулировка "Не ограничивать"
                $eduprog_tariff->tickets_count = $modelform->tickets_count;

                $action = 'replace';
                $selector = '#tariff_line_' . $eduprog_tariff->id;
            } else {
                // создаём новый тариф
                $eduprog_tariff = new Tariff();
                $eduprog_tariff->eduprog_id = $eduprog->id;
                $eduprog_tariff->eduprogform_id = $eduprog_form->id;
                $eduprog_tariff->name = $modelform->name;
                $eduprog_tariff->description = $modelform->description;
                $eduprog_tariff->start_publish = $modelform->start_publish; // дата должна передаваться в формате "d.m.Y"
                $eduprog_tariff->end_publish = $modelform->end_publish; // дата должна передаваться в формате "d.m.Y"
                $eduprog_tariff->limit_tickets = !(bool)$modelform->unlimit_tickets; // на фронте используется формулировка "Не ограничивать"
                $eduprog_tariff->tickets_count = (int)$modelform->tickets_count;
                $eduprog_tariff->visible = 1; // по умолчанию тариф активен

                $action = 'append';
                $selector = '#regform_' . $eduprog_form->id . ' .js-tarif-list';
            }
            // сохраняем тариф
            if ($eduprog_tariff->save()) {
                // сохранить цены, если былы переданы
                if (!empty($modelform->prices) && is_array($modelform->prices)) {
                    $prices = $modelform->prices;
                    // заполнить первый элемент датой начала публикации тарифа
                    $prices[0]['start_publish'] = $modelform->start_publish;
                    // удалить элементы, в которых не заполнена ни дата, ни цена и сформировать массив с ключами timestamp
                    $sorted_prices = [];
                    $id_existing_prices = [];
                    foreach ($prices as $key => $price_row) {
                        // цена будет сохранена, только если заполнены оба поля
                        if (($price_row['price'] != '') && ($price_row['price'] >= 0) && !empty($price_row['start_publish'])) {
                            $sorted_prices[strtotime($price_row['start_publish'])] = $price_row;
                            if (!empty($price_row['id'])) {
                                $id_existing_prices[] = $price_row['id'];
                            }
                        }
                    }

                    // отсортировать цены по датам начала и заполнить даты окончания
                    ksort($sorted_prices);
                    $dates = array_keys($sorted_prices);
                    foreach ($dates as $key => $date) {
                        $date_to = (isset($dates[$key + 1]) ? ($dates[$key + 1] - 24 * 60 * 60) : strtotime($modelform->end_publish));
                        $sorted_prices[$date]['end_publish'] = date('d.m.Y', $date_to);
                    }

                    // удалить цены, принадлежащие данному тарифу, но отсутствующие в новом списке цен
                    $old_prices = TariffPrice::find()->where(['tariff_id' => $eduprog_tariff->id])->andWhere(['NOT IN', 'id', $id_existing_prices])->all();
                    foreach ($old_prices as $price) {
                        $price->delete();
                    }

                    // создать/обновить цены
                    foreach ($sorted_prices as $price_row) {
                        $price_item = false;
                        if (!empty($price_row['id'])) {
                            // найти цену
                            $price_item = TariffPrice::find()->where(['id' => $price_row['id'], 'tariff_id' => $eduprog_tariff->id])->one();
                        }
                        if (empty($price_item)) {
                            $price_item = new TariffPrice();
                            $price_item->tariff_id = $eduprog_tariff->id;
                            $price_item->eduprog_id = $eduprog->id;
                        }

                        $price_item->price = $price_row['price'];
                        $price_item->start_publish = $price_row['start_publish'];
                        $price_item->end_publish = $price_row['end_publish'];

                        if (!$price_item->save()) {
                            // не сохраняется цена
                            file_put_contents($log_path . 'eduprog_saveprice.txt', date('d.m.Y H:i:s') . " - Ошибка сохранения цены \r\n" . var_export($price_item->errors, true) . "\r\n" . var_export($modelform->attributes, true), FILE_APPEND);
                            return [
                                'status' => 'fail',
                                'message' => 'При сохранении данных возникла ошибка. ' . \app\helpers\MainHelper::getHelpText(),
                            ];
                        }
                    }
                }
                // всё ок, вывести на странице блок с формой вместо пустого блока
                return [
                    'status' => 'success',
                    'action' => $action,
                    'selector' => $selector,
                    'html_tariff' => $this->renderPartial('_tariff', ['tariff' => $eduprog_tariff]),
                    'message' => 'Тариф создан',
                ];
            }
            file_put_contents($log_path . 'eduprog_savetariff.txt', date('d.m.Y H:i:s') . " - Ошибка создания формы \r\n" . var_export($eduprog_form->errors, true) . "\r\n", FILE_APPEND);
            return [
                'status' => 'fail',
                'message' => 'При сохранении данных возникла ошибка. ' . \app\helpers\MainHelper::getHelpText(),
            ];


        }
        return [
            'status' => 'fail',
            'message' => 'Ошибка при проверке введенных данных',
        ];

    }

    /* добавление/редактирование формы регистрации на программу */
    public function actionSaveNews()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        $log_path = Yii::getAlias('@app/logs/');

        // валидируем данные с формы
        $modelform = new LKEduprogNews();
        if ($modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            $user = Yii::$app->user->identity->userAR;
            // проверить право редактировать/создавать формы для указанной программы
            $eduprog = Eduprog::findOne((int)$modelform->eduprog_id);
            if ($eduprog->author_id != $user->id) {
                return [
                    'status' => 'fail',
                    'message' => 'Невозможно добавить новость к данной программе',
                ];
            }
            if (!empty($modelform->id)) {
                // найти новость, которую редактируем
                $eduprog_news = News::findOne((int)$modelform->id);
                if (empty($eduprog_news) or ($eduprog_news->eduprog_id != $eduprog->id)) {
                    return [
                        'status' => 'fail',
                        'message' => 'Редактируемая новость не найдена',
                    ];
                }
                // if (in_array($eduprog_news->status, [News::STATUS_PUBLIC, News::STATUS_DELETED])) {
                //    return [
                //        'status' => 'fail',
                //        'message' => 'Новость уже опубликована, редактирование невозможно',
                //    ];
                // }
            } else {
                // создаём новую новость
                $eduprog_news = new News();
                $eduprog_news->eduprog_id = $eduprog->id;
            }
            // переносим только те поля, которые может редактировать организатор
            $eduprog_news->sender_id = $user->id;
            $eduprog_news->name = $modelform->name;
            $eduprog_news->recipient = $modelform->recipient;
            $eduprog_news->recipient_users = $modelform->recipient_users;
            $eduprog_news->content = $modelform->content;
            $eduprog_news->has_tariff_button = $modelform->has_tariff_button;
            if ($modelform->start_publish_late) {
                // формат должен быть d.m.Y H:i:s
                $eduprog_news->public_date = trim($modelform->start_publish_date) . ' ' . trim($modelform->start_publish_time) . ':00';
            } else {
                $eduprog_news->public_date = time();
            }

            if ($eduprog_news->save()) {
                // всё ок, редирект на список новостей
                $message_page = LKEduprogViewNews::find()->where(['model' => LKEduprogViewNews::class, 'visible' => 1])->one();
                if ($message_page) {
                    $redirect_url = Url::toRoute([$message_page->getUrlPath(), 'id' => $eduprog->id]);
                } else {
                    $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
                    $redirect_url = $profile_page->getUrlPath();
                }
                return [
                    'status' => 'success',
                    'redirect_to' => $redirect_url,
                    'message' => 'Новость сохранена',
                ];
            }
            file_put_contents($log_path . 'eduprog_savenews.txt', date('d.m.Y H:i:s') . " - Ошибка сохранения новости \r\n" . var_export($eduprog_news->errors, true) . "\r\n", FILE_APPEND);
            return [
                'status' => 'fail',
                'message' => 'При сохранении данных возникла ошибка. ' . \app\helpers\MainHelper::getHelpText(),
            ];
        }
        return [
            'status' => 'fail',
            'message' => 'Ошибка при проверке введенных данных',
        ];

    }

    /* сохранение порядка обучения на программу */
    public function actionSaveTrainingproc()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        $log_path = Yii::getAlias('@app/logs/');

        // валидируем данные с формы
        $modelform = new LKEduprogTrainingproc();
        if ($modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            $user = Yii::$app->user->identity->userAR;
            // проверить право редактировать/создавать формы для указанной программы
            $eduprog = Eduprog::findOne((int)$modelform->eduprog_id);
            if ($eduprog->author_id != $user->id) {
                return [
                    'status' => 'fail',
                    'message' => 'Невозможно добавить порядок обучения к данной программе',
                ];
            }
            if (!empty($modelform->id)) {
                // найти порядок обучения, которую редактируем
                $eduprog_trainingproc = EduprogTrainingproc::findOne((int)$modelform->id);
                if (empty($eduprog_trainingproc) or ($eduprog_trainingproc->eduprog_id != $eduprog->id)) {
                    return [
                        'status' => 'fail',
                        'message' => 'Редактируемый порядок обучения не найден',
                    ];
                }
            } else {
                // создаём новую запись
                $eduprog_trainingproc = new EduprogTrainingproc();
                $eduprog_trainingproc->eduprog_id = $eduprog->id;
            }
            // переносим только те поля, которые может редактировать организатор
            $eduprog_trainingproc->sender_id = $user->id;
            $eduprog_trainingproc->name = $modelform->name;
            $eduprog_trainingproc->content = $modelform->content;
            if ($modelform->start_publish_late) {
                // формат должен быть d.m.Y H:i:s
                $eduprog_trainingproc->public_date = trim($modelform->start_publish_date) . ' ' . trim($modelform->start_publish_time) . ':00';
            } else {
                $eduprog_trainingproc->public_date = time();
            }

            if ($eduprog_trainingproc->save()) {
                // всё ок, редирект на список записей
                $message_page = LKEduprogViewTrainingproc::find()->where(['model' => LKEduprogViewTrainingproc::class, 'visible' => 1])->one();
                if ($message_page) {
                    $redirect_url = Url::toRoute([$message_page->getUrlPath(), 'id' => $eduprog->id]);
                } else {
                    $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
                    $redirect_url = $profile_page->getUrlPath();
                }
                return [
                    'status' => 'success',
                    'redirect_to' => $redirect_url,
                    'message' => 'Запись сохранена',
                ];
            }

            return [
                'status' => 'fail',
                'message' => 'При сохранении данных возникла ошибка. ' . \app\helpers\MainHelper::getHelpText(),
            ];
        }
        return [
            'status' => 'fail',
            'message' => 'Ошибка при проверке введенных данных',
        ];

    }

    /* изменить отображение тарифа на программу ДПО */
    public function actionSwitchVisibleTariff()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->user->isGuest) {
            return ['status' => 'fail', 'message' => 'Вы не авторизованы'];
        }
        if (Yii::$app->request->isAjax) {
            // обрабатываем только ajax-запросы
            $tariff_id = Yii::$app->request->get('tariff_id', false);
            if ($tariff_id) {
                $tariff = Tariff::find()->where(['id' => $tariff_id])->one();
                if (!empty($tariff) && !empty($tariff->eduprog) && ($tariff->eduprog->author_id == Yii::$app->user->id)) {
                    // форма принадлежит пользователю - можно менять
                    $visible = (bool)$tariff->visible;
                    $tariff->visible = !$visible;
                    $tariff->updateAttributes(['visible' => $tariff->visible]);
                    return [
                        'status' => 'success',
                        'visible' => $tariff->visible,
                        'message' => 'Тариф ' . ($tariff->visible ? 'активирован' : 'скрыт'),
                    ];
                }
                return [
                    'status' => 'fail',
                    'message' => 'Вы не можете редактировать указанный тариф',
                ];

            }
        }
        return [
            'status' => 'fail',
        ];
    }

    public function actionChangeMemberStatus($member_id, $status, $type)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->user->isGuest) {
            return ['status' => 'fail', 'message' => 'Вы не авторизованы'];
        }
        // обрабатываем только ajax-запросы
        if (Yii::$app->request->isAjax) {
            // пользователь должен быть ЭО с правом заниматься ДПО
            $user = Yii::$app->user->identity->userAR;
            if (!in_array($user->role, ['exporg']) or !$user->organization->can_service or !$user->organization->license_service) {
                return ['status' => 'fail', 'message' => 'У вас нет прав на работу с ДПО'];
            }
            $status_list = EduprogMember::getStatusList();
            if (!empty((int)$member_id) && isset($status_list[$status])) {
                $member = EduprogMember::findOne((int)$member_id);
                // если слушатель найден и он является слушателем программы текущего пользователя
                if ($member && ($member->eduprog?->author_id == $user->id)) {
                    if ($member->changeStatusMember($status)) {
                        $html_card = '';
                        switch ($type) {
                            case 'member_list':
                                $member_page = LKEduprogViewMemberNews::find()->where(['model' => LKEduprogViewMemberNews::class, 'visible' => 1])->one();
                                $member_page = LKEduprogViewMemberOrders::find()->where(['model' => LKEduprogViewMemberOrders::class, 'visible' => 1])->one();
                                $member_url = (!empty($member_page) ? $member_page->getUrlPath() : false);
                                $html_card = $this->renderPartial('_expert_members_list_card', ['member' => $member, 'member_url' => $member_url]);
                                break;
                            case 'member_page':
                                $html_card = $this->renderPartial('_expert_member_card', ['member' => $member]);
                                break;
                        }
                        return [
                            'status' => 'success',
                            'new_status' => $member->status,
                            'member_card_html' => $html_card,
                            'message' => 'Статус изменён',
                        ];
                    }
                    return [
                        'status' => 'fail',
                        'message' => 'Изменение статуса не возможно',
                    ];

                }
                return [
                    'status' => 'fail',
                    'message' => 'Слушатель не найден',
                ];

            }
            return [
                'status' => 'fail',
                'message' => 'Параметры переданы не верно',
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Невозможно выполнить действие',
        ];
    }

    public function actionRemoveTariff()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->user->isGuest) {
            return ['status' => 'fail', 'message' => 'Вы не авторизованы'];
        }
        if (Yii::$app->request->isAjax) {
            // обрабатываем только ajax-запросы
            $tariff_id = Yii::$app->request->get('tariff_id', false);
            if ($tariff_id) {
                $tariff = Tariff::find()->where(['id' => $tariff_id])->one();
                if (!empty($tariff) && !empty($tariff->eduprog) && ($tariff->eduprog->author_id == Yii::$app->user->id)) {
                    // тариф принадлежит пользователю - можно удалять
                    if ($tariff->delete() !== false) {
                        return [
                            'status' => 'success',
                            'message' => 'Тариф удален',
                        ];
                    }
                    return [
                        'status' => 'fail',
                        'message' => 'Невозможно удалить тариф. ' . \app\helpers\MainHelper::getHelpText(),
                    ];

                }
                return [
                    'status' => 'fail',
                    'message' => 'Вы не можете удалить указанный тариф',
                ];

            }
        }
        return [
            'status' => 'fail',
            'message' => 'Невозможно выполнить действие',
        ];
    }

    /* возвращает данные для формы тарифа для редактирвоания */
    public function actionFieldsTariff()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->user->isGuest) {
            return ['status' => 'fail', 'message' => 'Вы не авторизованы'];
        }
        if (Yii::$app->request->isAjax) {
            // обрабатываем только ajax-запросы
            $tariff_id = Yii::$app->request->get('tariff_id', false);
            if ($tariff_id) {
                $tariff = Tariff::find()->where(['id' => $tariff_id])->one();
                if (!empty($tariff) && !empty($tariff->eduprog) && ($tariff->eduprog->author_id == Yii::$app->user->id)) {
                    // тарифф принадлежит пользователю - можно редактировать
                    // заполняем поля с ключами, соответствующими аттрибутам формы LKEduprogTariff
                    $fields = [
                        'name' => $tariff->name,
                        'description' => $tariff->description,
                        'start_publish' => Yii::$app->formatter->asDatetime($tariff->start_publish, 'php:d.m.Y'),
                        'end_publish' => Yii::$app->formatter->asDatetime($tariff->end_publish, 'php:d.m.Y'),
                        'unlimit_tickets' => !(bool)$tariff->limit_tickets,
                        'tickets_count' => $tariff->tickets_count,
                    ];
                    $html = $this->renderPartial('_price_list', ['tariff' => $tariff]);
                    return [
                        'status' => 'success',
                        'fields' => $fields,
                        'html' => $html,
                        'message' => 'Поля тарифа',
                    ];
                }
                return [
                    'status' => 'fail',
                    'message' => 'Вы не можете редактировать указанный тариф',
                ];

            }
        }
        return [
            'status' => 'fail',
            'message' => 'Невозможно выполнить действие',
        ];
    }

    public function actionRemoveOferta()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->user->isGuest) {
            return ['status' => 'fail', 'message' => 'Вы не авторизованы'];
        }
        if (Yii::$app->request->isAjax) {
            // обрабатываем только ajax-запросы
            $file_id = Yii::$app->request->post('file_id', false);
            if ($file_id) {
                $oferta_file = FilestoreModel::findOne($file_id);
                // если модель-владелец файла принадлежит пользователю - то можно удалить
                $keeper_user = false;
                if (!empty($oferta_file) && !empty($oferta_file->keeper)) {
                    if ($oferta_file->keeper instanceof \app\modules\eduprogmoder\models\Eduprogmoder) {
                        $keeper_user = $oferta_file->keeper->eduprog->author_id;
                    } elseif ($oferta_file->keeper instanceof \app\modules\eduprog\models\Eduprog) {
                        $keeper_user = $oferta_file->keeper->author_id;
                    }
                    if ($keeper_user == Yii::$app->user->id) {
                        $oferta_file->delete();
                        return [
                            'status' => 'success',
                            'message' => 'Договор удален',
                        ];
                    }
                }
            }
            return [
                'status' => 'fail',
                'message' => 'Невозможно удалить договор. ' . \app\helpers\MainHelper::getHelpText(),
            ];
        }
        return [
            'status' => 'fail',
            'message' => 'Невозможно выполнить действие',
        ];
    }

    /* запрос на отмену программы */
    public function actionCancelEduprog()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* найти */
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['exporg']) or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на работу с программой',
            ];
        }

        $origin_id = Yii::$app->request->get('origin', null);
        if ($origin_id) {
            $origin_eduprog = Eduprog::findOne($origin_id);
            if (!empty($origin_eduprog) && ($origin_eduprog->author_id == $user->id)) {
                /* если программа в статусе Опубликовано или На доработке то его можно отменить */
                if (in_array($origin_eduprog->status, [Eduprog::STATUS_NEED_EDIT, Eduprog::STATUS_PUBLIC])) {
                    /* если программа еще не началась, либо на неё нет приобретённых билетов - то можно отменить */
                    if ((strtotime($origin_eduprog->date_start) > strtotime(date('d.m.Y'))) or empty($origin_eduprog->ordersAll)) {
                        /* если на программу уже были куплены билеты (даже если не оплачены) */
                        if (!empty($origin_eduprog->ordersAll)) {
                            /* редирект на страницу заполнения формы с письмом */
                            $cancel_page = LKEduprogCancel::find()->where(['model' => LKEduprogCancel::class, 'visible' => 1])->one();
                            if ($cancel_page) {
                                return [
                                    'status' => 'success',
                                    'redirect_to' => Url::toRoute([$cancel_page->getUrlPath(), 'id' => $origin_eduprog->id]),
                                    'message' => 'Переход на страницу отмены',
                                ];
                            }
                            return [
                                'status' => 'fail',
                                'message' => 'Отмена программы с оформленными заказами невозможна. ' . \app\helpers\MainHelper::getHelpText(),
                            ];

                        }
                        /* просто меняем статус на Отменено */
                        $origin_eduprog->status = Eduprog::STATUS_CANCELLED;
                        if ($origin_eduprog->save()) {
                            return [
                                'status' => 'success',
                                'message' => 'Программа успешно отменена',
                            ];
                        }
                        return [
                            'status' => 'fail',
                            'message' => 'Ошибка при изменении программы. ' . \app\helpers\MainHelper::getHelpText(),
                        ];


                    }
                    return [
                        'status' => 'fail',
                        'message' => 'Невозможно отменить программу, которая уже идет, если есть оформленные заказы',
                    ];

                }
                return [
                    'status' => 'fail',
                    'message' => 'Невозможно отменить программу. Условия не соблюдены.',
                ];

            }
            return [
                'status' => 'fail',
                'message' => 'Программа не найдена',
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Неверно переданы параметры. ' . \app\helpers\MainHelper::getHelpText(),
        ];
    }

    /* страница отмены программы */
    public function actionEduprogCancel($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только слушателям АСТ */
        if (!in_array($role, ['exporg']) or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        $eduprog_id = Yii::$app->request->get('id', null);

        /* страница списка программ */
        $eduprog_page = LKEduprogList::find()->where(['model' => LKEduprogList::class, 'visible' => 1])->one();

        /* если не указан id отменяемой программы */
        if (!$eduprog_id) {
            return $this->redirect($eduprog_page->getUrlPath());
        }

        $original_eduprog = Eduprog::findOne((int)$eduprog_id);

        /* если не нашли или редактировать пытается не автор, то редирект на список. */
        if ((!$original_eduprog) or ($original_eduprog->author_id != $user->id)) {
            return $this->redirect($eduprog_page->getUrlPath());
        }

        /* если программа не в статусе На доработке или Опубликовано, или она уже началась или у неё нет купленных билетов - редирект на список */
        if (!in_array($original_eduprog->status, [Eduprog::STATUS_NEED_EDIT, Eduprog::STATUS_PUBLIC])
            or (strtotime($original_eduprog->date_start) <= strtotime(date('d.m.Y'))
                or empty($original_eduprog->ordersAll))) {
            /* редирект - такое мероприятие нельзя отменять через форму */
            return $this->redirect($eduprog_page->getUrlPath());
        }

        $cancel_model = new EduprogCancelForm();
        $cancel_model->id = $original_eduprog->id;
        /* заполнить стандартный текст письма */
        if (!empty($original_eduprog->cancel_letter)) {
            $cancel_model->cancel_letter = $original_eduprog->cancel_letter;
        } else {
            if (!empty(SettingsText::getInfo('cancel_letter_eduprog'))) {
                $cancel_model->cancel_letter = SettingsText::getInfo('cancel_letter_eduprog');
            }
        }

        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'cancel_model' => $cancel_model, 'original' => $original_eduprog]);
    }

    /* отправка формы отмены программы */
    public function actionSendcancelform()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* найти пользователя */
        $user = Yii::$app->user->identity->userAR;

        /* публиковать мероприятия могут только Участники АСТ */
        if (!in_array($user->role, ['exporg']) or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на отмену программы',
            ];
        }

        $modelform = new EduprogCancelForm();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {
            /* находим программу, которую нужно отменить */
            $original_eduprog = Eduprog::findOne((int)$modelform->id);
            if (empty($original_eduprog)) {
                /* если не нашли - ошибка */
                return [
                    'status' => 'fail',
                    'message' => 'Программа не найдена',
                ];
            }

            /* если автор не соответствует текущему пользователю - ошибка */
            if ($original_eduprog->author_id != $user->id) {
                return [
                    'status' => 'fail',
                    'message' => 'У вас нет прав на редактирование программы',
                ];
            }

            /* если мероприятие ни в статусе На доработке или Опубликовано, или оно уже началось или у него нет купленных билетов - ошибка */
            if (!in_array($original_eduprog->status, [Eduprog::STATUS_NEED_EDIT, Eduprog::STATUS_PUBLIC])
                or (strtotime($original_eduprog->date_start) <= strtotime(date('d.m.Y')))
                or empty($original_eduprog->ordersAll)) {
                return [
                    'status' => 'fail',
                    'message' => 'Невозможно отменить программу. Условия не соблюдены.',
                ];
            }

            /* все ок - переносим поля, ставим статус и уведомляем администратора */
            $original_eduprog->cancel_reason = $modelform->cancel_reason;
            $original_eduprog->cancel_letter = $modelform->cancel_letter;
            $original_eduprog->status = Eduprog::STATUS_CANCELLED;
            if ($original_eduprog->save()) {
                /* отправляем уведомление администратору */
                $original_eduprog->sendCancelLetterAdmin();
                /* и сообщение пользователю */
                $redirect_page = LKEduprogList::find()->where(['model' => LKEduprogList::class, 'visible' => 1])->one();
                $redirect_to = $redirect_page->getUrlPath();
                return [
                    'status' => 'success',
                    'redirect_to' => $redirect_to,
                    'message' => 'Программа успешно отменена',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Невозможно отменить программу. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'При сохранении данных возникла ошибка. ' . \app\helpers\MainHelper::getHelpText(),
        ];
    }

    public function actionDeleteeduprog()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* найти */
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['exporg']) or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на удаление программы',
            ];
        }

        $origin_id = Yii::$app->request->get('origin', null);
        if ($origin_id) {
            $origin_eduprog = Eduprog::findOne($origin_id);
            if (!empty($origin_eduprog) && ($origin_eduprog->author_id == $user->id)) {
                /* пробуем удалить */
                if ($origin_eduprog->delete()) {
                    return [
                        'status' => 'success',
                        'message' => 'Программа успешно удалена',
                    ];
                }
                return [
                    'status' => 'fail',
                    'message' => 'Невозможно удалить программу. Условия не соблюдены.',
                ];

            }
            return [
                'status' => 'fail',
                'message' => 'Программа не найдена',
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Неверно переданы параметры. ' . \app\helpers\MainHelper::getHelpText(),
        ];

    }

    public function actionDeleteNews()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* найти */
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['exporg']) or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на удаление новости',
            ];
        }

        $origin_id = Yii::$app->request->get('origin', null);
        if ($origin_id) {
            $origin_news = News::findOne($origin_id);
            if (!empty($origin_news) && ($origin_news->sender_id == $user->id)) {
                /* пробуем удалить */
                if ($origin_news->delete() !== false) {
                    return [
                        'status' => 'success',
                        'message' => 'Новость успешно удалена',
                    ];
                }
                return [
                    'status' => 'fail',
                    'message' => 'Невозможно удалить новость.',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Невозможно удалить новость. Условия не соблюдены.',
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Неверно переданы параметры. ' . \app\helpers\MainHelper::getHelpText(),
        ];

    }

    public function actionDeleteTrainingproc()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* найти */
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['exporg']) or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на удаление записи',
            ];
        }

        $origin_id = Yii::$app->request->get('origin', null);
        if ($origin_id) {
            $origin_trainingproc = EduprogTrainingproc::findOne($origin_id);
            if (!empty($origin_trainingproc) && ($origin_trainingproc->sender_id == $user->id)) {
                /* пробуем удалить */
                if ($origin_trainingproc->delete() !== false) {
                    return [
                        'status' => 'success',
                        'message' => 'Запись успешно удалена',
                    ];
                }
                return [
                    'status' => 'fail',
                    'message' => 'Невозможно удалить запись.',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Невозможно удалить запись. Условия не соблюдены.',
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Неверно переданы параметры. ' . \app\helpers\MainHelper::getHelpText(),
        ];

    }

    public function actionCopyeduprog()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* найти */
        $user = Yii::$app->user->identity->userAR;
        if (!in_array($user->role, ['exporg']) or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на создание программы',
            ];
        }

        /* если пользователь без регистрации на МП - отправить на страницу создания программы. Там будет текст про регистрацию */
        if (!$user->organization->can_service or !$user->organization->license_service) {
            $edit_page = LKEduprogEdit::find()->where(['model' => LKEduprogEdit::class, 'visible' => 1])->one();
            $edit_url = (!empty($edit_page) ? $edit_page->getUrlPath() : false);
            return [
                'status' => 'success',
                'redirect_to' => $edit_url,
                'message' => 'Создание программы запрещено.',
            ];
        }

        $origin_id = Yii::$app->request->get('origin', null);
        if ($origin_id) {
            $origin_eduprog = Eduprog::findOne($origin_id);
            if (!empty($origin_eduprog) && ($origin_eduprog->author_id == $user->id)) {
                $copy_element = $origin_eduprog->addCopy();
                if ($copy_element) {
                    /* сразу при создании копии программы создаем первичную заявку на модерацию, т.к. пользователь может изменить только дату проведения (не является модерируемым полем) и отправить программу на публикацию. */
                    $moderation = $copy_element->addNewModeration();
                    $moderation->first_moderation = true;
                    $moderation->eduprog_id = $copy_element->id;
                    $moderation->save();

                    $edit_page = LKEduprogEdit::find()->where(['model' => LKEduprogEdit::class, 'visible' => 1])->one();
                    $edit_url = (!empty($edit_page) ? Url::toRoute([$edit_page->getUrlPath(), 'id' => $copy_element->id]) : false);
                    return [
                        'status' => 'success',
                        'redirect_to' => $edit_url,
                        'message' => 'Создана новая программа на основе выбранной',
                    ];
                }
                return [
                    'status' => 'fail',
                    'message' => 'Невозможно создать копию программы',
                ];

            }
            return [
                'status' => 'fail',
                'message' => 'Программа не найдена',
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'Неверно переданы параметры. ' . \app\helpers\MainHelper::getHelpText(),
        ];

    }

    /* страница списка слушателей программы (просмотр программы в ЛК Эксперта) */
    public function actionEduprogViewMembers($model, $id)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* страница доступна только участникам АСТ */
        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
        if (!in_array($user->role, ['exporg']) or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            return $this->redirect($profile_page->getUrlPath());
        }

        /* если что-то не так - редирект на страницу списка мероприятий - там разберется. */
        $eduprog_catalog = LKEduprogList::find()->where(['model' => LKEduprogList::class, 'visible' => '1'])->one();
        $redirect_page_url = (!empty($eduprog_catalog) ? $eduprog_catalog->getUrlPath() : $profile_page->getUrlPath());
        if (isset($id)) {
            $eduprog = Eduprog::findOne((int)$id);
            if ($eduprog->author_id == $user->id) {
                if (in_array($eduprog->status, [Eduprog::STATUS_MODERATE, Eduprog::STATUS_DECLINED]) or $eduprog->is_corporative) {
                    return $this->redirect($redirect_page_url);
                }
                $this->setMeta($model);
                /* обновить нотификации */
                $eduprog->markNotificationRead($eduprog->author_id);
                /* страница новостей по программе */
                $message_page = LKEduprogViewNews::find()->where(['model' => LKEduprogViewNews::class, 'visible' => 1])->one();
                /* количество и сумма заказов */
                $orders_data = Eduprogorder::find()->select(['COUNT(*) as count', 'SUM(price) as summ'])->where([
                    'eduprog_id' => $eduprog->id,
                    'is_payed' => 1,
                ])->asArray()->all()[0];
                /* модель формы фильтрации */
                $filter_form = new \app\models\LKEduprogFilterMembers();

                // список слушателей, для вывода на странице
                $members_query = EduprogMember::find()->where(['eduprog_member.eduprog_id' => $eduprog->id])->andWhere(['!=', 'eduprog_member.status', EduprogMember::STATUS_PAYMENTER]);

                $total_members_query = clone $members_query;
                $total_members_query->select(['COUNT(*) as count', 'status'])->groupBy(['status']);
                $total_info = ArrayHelper::map($total_members_query->asArray()->all(), 'status', 'count');

                if ($filter_form->sanitize(Yii::$app->request->post()) && $filter_form->validate()) {
                    // нужна фильтрация
                    if (!empty($filter_form->name)) {
                        $members_query->leftJoin('profile', 'profile.user_id = eduprog_member.user_id')
                            ->andWhere(['or',
                                ['LIKE', 'LCASE(CONCAT(`profile`.`surname`," ",`profile`.`name`))', $filter_form->name],
                                ['LIKE', 'LCASE(CONCAT(`profile`.`name`," ",`profile`.`surname`))', $filter_form->name],
                                ['LIKE', 'profile.organization_name', $filter_form->name],
                                ['LIKE', 'eduprog_member.memberNum', $filter_form->name], // ФИО и номер временно в одной колонке фильтруются.
                            ]);
                    }
                    if (!empty($filter_form->email)) {
                        $members_query->leftJoin('user', 'user.id = eduprog_member.user_id')
                            ->andWhere(['LIKE', 'email', $filter_form->email]);
                    }
                    if (!empty($filter_form->tariff)) {
                        $members_query->leftJoin('eduprog_orderitem', 'eduprog_orderitem.user_id = eduprog_member.user_id')
                            ->leftJoin('eduprog_order', 'eduprog_order.id = eduprog_orderitem.eduprogorder_id')
                            ->andWhere(['eduprog_order.is_payed' => 1, 'eduprog_orderitem.tariff_id' => $filter_form->tariff]);
                    }
                    if (!empty($filter_form->status)) {
                        $members_query->andWhere(['eduprog_member.status' => $filter_form->status]);
                    }
                }

                $members = $members_query
                    ->orderBy([new \yii\db\Expression('FIELD( eduprog_member.status, "' . implode('","', [EduprogMember::STATUS_WAITING, EduprogMember::STATUS_ACTIVE, EduprogMember::STATUS_COMPLETED, EduprogMember::STATUS_EXPELLED, EduprogMember::STATUS_REJECTED]) . '")'), 'eduprog_member.memberNum' => SORT_ASC,])
                    ->all();
                $member_page = LKEduprogViewMemberNews::find()->where(['model' => LKEduprogViewMemberNews::class, 'visible' => 1])->one();
                // $member_page = LKEduprogViewMemberOrders::find()->where(['model' => LKEduprogViewMemberOrders::class, 'visible' => 1])->one();
                $member_url = (!empty($member_page) ? $member_page->getUrlPath() : false);
                if (Yii::$app->request->isAjax) {
                    // если запрос пришел аяксом - то отдаем только список слушателей.
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    $html_data = '';
                    if (!empty($members)) {
                        foreach ($members as $member) {
                            $html_data .= $this->renderPartial('_expert_members_list_card', ['member' => $member, 'member_url' => $member_url]);
                        }
                    } else {
                        $html_data = '<tr><td colspan="4">По указанным параметрам слушатели не найдены.</tr>';
                    }
                    return [
                        'status' => 'success',
                        'html_data' => $html_data,
                    ];
                }

                $tariff_list = ArrayHelper::map(Tariff::find()->select(['id', 'name'])->where(['eduprog_id' => $eduprog->id])->asArray()->all(), 'id', 'name');
                // список статусов для фильтра
                $status_list = EduprogMember::getStatusList();
                unset($status_list[EduprogMember::STATUS_PAYMENTER]);
                unset($status_list[EduprogMember::STATUS_COMPLETED]);

                return $this->render($model->view, [
                    'model' => $model,
                    'eduprog' => $eduprog,
                    'eduprog_catalog' => $eduprog_catalog,
                    'filter_form' => $filter_form,
                    'tariff_list' => $tariff_list,
                    'status_list' => $status_list,
                    'total_info' => $total_info,
                    'members' => $members,
                    'member_url' => $member_url,
                ]);
            }
            return $this->redirect($redirect_page_url);

        }
        return $this->redirect($redirect_page_url);

    }

    /* страница списка заказов по программе (просмотр программы в ЛК Эксперта) */
    public function actionEduprogViewOrders($model, $id, $form_id = null)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* страница доступна только участникам АСТ */
        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
        if (!in_array($user->role, ['exporg']) or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            return $this->redirect($profile_page->getUrlPath());
        }

        /* если что-то не так - редирект на страницу списка программ - там разберется. */
        $eduprog_catalog = LKEduprogList::find()->where(['model' => LKEduprogList::class, 'visible' => '1'])->one();
        $redirect_page_url = (!empty($eduprog_catalog) ? $eduprog_catalog->getUrlPath() : $profile_page->getUrlPath());
        if (isset($id)) {
            $eduprog = Eduprog::findOne((int)$id);
            if ($eduprog->author_id == $user->id) {
                if (in_array($eduprog->status, [Eduprog::STATUS_MODERATE, Eduprog::STATUS_DECLINED]) or $eduprog->is_corporative) {
                    return $this->redirect($redirect_page_url);
                }
                $this->setMeta($model);

                // получить список форм (как на странице редактирования) и далее работаем только с 1-ой формой.
                $forms_list = $eduprog->eduprogFormsAll;
                if ($form_id) {
                    foreach ($forms_list as $key => $form) {
                        if ($form_id == $form->id) {
                            $active_form = $forms_list[$key];
                            break;
                        }
                    }
                } else {
                    $active_form = $forms_list[0];
                }
                /* количество и сумма заказов */
                $orders_data = Eduprogorder::find()->select(['COUNT(*) as count', 'SUM(price) as summ'])->where([
                    'eduprog_id' => $eduprog->id,
                    'form_id' => $active_form->id,
                    'is_payed' => 1,
                ])->asArray()->one();

                /* модель формы фильтрации */
                $filter_form = new \app\models\LKEduprogFilterOrders();

                // список позиций заказов, для вывода на странице
                $orders_ids = $eduprog->getOrders($active_form->id)->select(['id'])->asArray()->column();

                $order_items_query = EduprogorderItem::find()->where(['IN', 'eduprogorder_id', $orders_ids]);

                if ($filter_form->sanitize(Yii::$app->request->get()) && $filter_form->validate()) {
                    // нужна фильтрация
                    $order_items_query->joinWith(['order']);

                    if (!empty($filter_form->orderNum)) {
                        $order_items_query->andWhere(['LIKE', 'eduprog_order.orderNum', $filter_form->orderNum]);
                    }
                    if (!empty($filter_form->itemNum)) {
                        $order_items_query->andWhere(['eduprog_orderitem.itemNum' => $filter_form->itemNum]);
                    }
                    if (!empty($filter_form->status)) {
                        $order_items_query->joinWith(['order.payment']);
                        if ($filter_form->status == LKEduprogFilterOrders::STATUS_POSTPAY) {
                            $order_items_query->andWhere(['payment.is_postpay' => true]);
                            $order_items_query->andWhere(['payment.status' => Payment::STATUS_NEW]);
                        } elseif ($filter_form->status == LKEduprogFilterOrders::STATUS_POSTPAY_ACCEPTED) {
                            $order_items_query->andWhere(['payment.is_postpay' => true]);
                            $order_items_query->andWhere(['payment.status' => Payment::STATUS_ACCEPTED]);
                        } elseif ($filter_form->status == EduprogorderItem::STATUS_NEW) {
                            $order_items_query->andWhere(['eduprog_orderitem.status' => $filter_form->status]);
                            $order_items_query->andWhere(['payment.is_postpay' => false]);
                        } else {
                            $order_items_query->andWhere(['eduprog_orderitem.status' => $filter_form->status]);
                        }
                    }
                    if (!empty($filter_form->tariff)) {
                        $order_items_query->andWhere(['tariff_id' => $filter_form->tariff]);
                    }
                    if (!empty($filter_form->payer_name)) {
                        $order_items_query->leftJoin('profile as payer_profile', 'payer_profile.user_id = eduprog_order.user_id')
                            ->andWhere(['or',
                                ['LIKE', 'LCASE(CONCAT(`payer_profile`.`surname`," ",`payer_profile`.`name`))', $filter_form->payer_name],
                                ['LIKE', 'LCASE(CONCAT(`payer_profile`.`name`," ",`payer_profile`.`surname`))', $filter_form->payer_name],
                                ['LIKE', 'payer_profile.organization_name', $filter_form->payer_name],
                            ]);
                    }
                    if (!empty($filter_form->member_name)) {
                        $order_items_query->leftJoin('profile as member_profile', 'member_profile.user_id = eduprog_orderitem.user_id')
                            ->andWhere(['or',
                                ['LIKE', 'LCASE(CONCAT(`member_profile`.`surname`," ",`member_profile`.`name`))', $filter_form->member_name],
                                ['LIKE', 'LCASE(CONCAT(`member_profile`.`name`," ",`member_profile`.`surname`))', $filter_form->member_name],
                                ['LIKE', 'member_profile.organization_name', $filter_form->member_name],
                            ]);
                    }
                    if (!empty($filter_form->member_email)) {
                        $order_items_query->leftJoin('user as member', 'member.id = eduprog_orderitem.user_id')
                            ->andWhere(['LIKE', 'member.email', $filter_form->member_email]);
                    }
                    if (!empty($filter_form->payer_email)) {
                        $order_items_query->leftJoin('user as payer', 'payer.id = eduprog_order.user_id')
                            ->andWhere(['LIKE', 'payer.email', $filter_form->payer_email]);
                    }
                }

                // пагинация
                $countQuery = clone $order_items_query;
                $count_items = $countQuery->count();

                $pageparams = Yii::$app->request->get();
                unset($pageparams['model']);
                unset($pageparams['_csrf']);

                $pages = new Pagination([
                    'totalCount' => $count_items,
                    'defaultPageSize' => 20,
                    'route' => $model->getUrlPath(),
                    'params' => $pageparams,
                ]);

                $order_items = $order_items_query->offset($pages->offset)
                    ->limit($pages->limit)->orderBy('id DESC')
                    ->all();

                if (Yii::$app->request->isAjax) {
                    // если запрос пришел аяксом - то отдаем только список слушателей.
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    $html_data = '';
                    if (!empty($order_items)) {
                        foreach ($order_items as $order_item) {
                            $html_data .= $this->renderPartial('_expert_orders_list_card', ['order_item' => $order_item]);
                        }
                    } else {
                        $html_data = '<div class="table-empty-cells-text">По указанным параметрам заказы не найдены</div>';
                    }
                    return [
                        'status' => 'success',
                        'html' => $html_data,
                        'pager' => \app\widgets\pagination\LinkPager::widget(['pages' => $pages, 'is_ajax' => true]),
                    ];
                }

                $tariff_list = ArrayHelper::map(Tariff::find()->select(['id', 'name'])->where(['eduprog_id' => $eduprog->id])->asArray()->all(), 'id', 'name');
                $status_list = LKEduprogFilterOrders::getStatusList();
                return $this->render($model->view, [
                    'model' => $model,
                    'pages' => $pages,
                    'eduprog' => $eduprog,
                    'eduprog_catalog' => $eduprog_catalog,
                    'active_form' => $active_form,
                    'filter_form' => $filter_form,
                    'orders_data' => $orders_data,
                    'status_list' => $status_list,
                    'tariff_list' => $tariff_list,
                    'order_items' => $order_items,
                    'forms_list' => $forms_list,
                ]);
            }
            return $this->redirect($redirect_page_url);
        }
        return $this->redirect($redirect_page_url);

    }

    /* страница списка новостей слушателя программы (просмотр программы в ЛК Эксперта) */
    public function actionEduprogViewNews($model, $id, $status = 'all')
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* страница доступна только ЭО с правом публикации ДПО */
        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
        if (!in_array($user->role, ['exporg']) or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            return $this->redirect($profile_page->getUrlPath());
        }

        /* если что-то не так - редирект на страницу списка мероприятий - там разберется. */
        $eduprog_catalog = LKEduprogList::find()->where(['model' => LKEduprogList::class, 'visible' => '1'])->one();
        $redirect_page_url = (!empty($eduprog_catalog) ? $eduprog_catalog->getUrlPath() : $profile_page->getUrlPath());
        if (isset($id)) {
            $eduprog = Eduprog::findOne((int)$id);
            if ($eduprog?->author_id == $user->id) {
                if (in_array($eduprog->status, [Eduprog::STATUS_MODERATE, Eduprog::STATUS_DECLINED]) or $eduprog->is_corporative) {
                    return $this->redirect($redirect_page_url);
                }
                $this->setMeta($model);

                /* найти новости по программе, отфильтровать по статусу */
                $news_query = News::find()->where(['!=', 'status', News::STATUS_DELETED])->andWhere(['eduprog_id' => $eduprog->id, 'visible' => 1]);
                switch ($status) {
                    case News::STATUS_WAITING:
                        $news_query->andWhere(['status' => News::STATUS_WAITING]);
                        break;
                    case News::STATUS_PUBLIC:
                        $news_query->andWhere(['status' => News::STATUS_PUBLIC]);
                        break;
                    // любые другие значения $status просто не учитываем
                }
                $news_query->orderBy(['public_date' => SORT_DESC]);
                $news = $news_query->all();

                return $this->render($model->view, [
                    'model' => $model,
                    'news' => $news,
                    'eduprog' => $eduprog,
                    'eduprog_catalog' => $eduprog_catalog,
                ]);
            }
            return $this->redirect($redirect_page_url);
        }
        return $this->redirect($redirect_page_url);
    }

    /* страница порядок обучения слушателя программы (просмотр программы в ЛК Эксперта) */
    public function actionEduprogViewTrainingproc($model, $id, $status = 'all')
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* страница доступна только ЭО с правом публикации ДПО */
        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
        if (!in_array($user->role, ['exporg']) or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            return $this->redirect($profile_page->getUrlPath());
        }

        /* если что-то не так - редирект на страницу списка мероприятий - там разберется. */
        $eduprog_catalog = LKEduprogList::find()->where(['model' => LKEduprogList::class, 'visible' => '1'])->one();
        $redirect_page_url = (!empty($eduprog_catalog) ? $eduprog_catalog->getUrlPath() : $profile_page->getUrlPath());
        if (isset($id)) {
            $eduprog = Eduprog::findOne((int)$id);
            if ($eduprog?->author_id == $user->id) {
                if (in_array($eduprog->status, [Eduprog::STATUS_MODERATE, Eduprog::STATUS_DECLINED]) or $eduprog->is_corporative) {
                    return $this->redirect($redirect_page_url);
                }
                $this->setMeta($model);

                /* найти по программе, отфильтровать по статусу */
                $trainingproc_query = EduprogTrainingproc::find()->where(['!=', 'status', EduprogTrainingproc::STATUS_DELETED])->andWhere(['eduprog_id' => $eduprog->id, 'visible' => 1]);
                switch ($status) {
                    case EduprogTrainingproc::STATUS_WAITING:
                        $trainingproc_query->andWhere(['status' => EduprogTrainingproc::STATUS_WAITING]);
                        break;
                    case EduprogTrainingproc::STATUS_PUBLIC:
                        $trainingproc_query->andWhere(['status' => EduprogTrainingproc::STATUS_PUBLIC]);
                        break;
                    // любые другие значения $status просто не учитываем
                }
                $trainingproc_query->orderBy(['public_date' => SORT_DESC]);

                $trainingproc = $trainingproc_query->all();

                return $this->render($model->view, [
                    'model' => $model,
                    'trainingproc' => $trainingproc,
                    'eduprog' => $eduprog,
                    'eduprog_catalog' => $eduprog_catalog,
                ]);
            }
            return $this->redirect($redirect_page_url);
        }
        return $this->redirect($redirect_page_url);
    }

    /* страница создания/редактирования/копирования/просмотра новости программы */
    public function actionEduprogViewNewsCreate($model, $eduprog_id = false, $id = false, $copy = false)
    {
        // выключатель ДПО
        if (!Yii::$app->params['enable_dpo']) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        /* проверка разрешения публикации ДПО */
        if (!$user->organization->can_service) {
            /* выводим сообщение о необходимости регистрации на маркетплейс */
            return $this->render($model->view, ['model' => $model, 'content' => $model->need_marketplace]);
        }

        /* проверка разрешения публикации ДПО */
        if (!$user->organization->license_service) {
            /* выводим сообщение о необходимости регистрации на маркетплейс */
            return $this->render($model->view, ['model' => $model, 'content' => $model->need_license]);
        }

        /* страница доступна только ЭО с регистрацией в качестве Юрлица */
        if (!in_array($user->role, ['exporg']) or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        /* страница списка программ ДПО */
        $eduprog_page = LKEduprogList::find()->where(['model' => LKEduprogList::class, 'visible' => 1])->one();

        if (!($eduprog_id or $id or $copy)) {
            // если не заполнен ни один из параметров - то вернуться на страницу списка программ
            return $this->redirect($eduprog_page->getUrlPath());
        }

        // страница списка новостей
        $message_page = LKEduprogViewNews::find()->where(['model' => LKEduprogViewNews::class, 'visible' => 1])->one();
        // страница списка слушателей
        $members_page = LKEduprogViewMembers::find()->where(['model' => LKEduprogViewMembers::class, 'visible' => 1])->one();
        $member_view_page = LKEduprogViewMemberNews::find()->where(['model' => LKEduprogViewMemberNews::class, 'visible' => 1])->one();

        $eduprog_model = false;
        $news_model = false;

        $action = '';

        $news_form = new LKEduprogNews();

        if ($eduprog_id) {
            $eduprog_model = Eduprog::findOne((int)$eduprog_id);
            // создание новой новости
        }

        if ($copy) {
            $news_model = News::findOne((int)$copy);
            if (!$news_model) {
                // новость не найдена - вернуться на страницу списка программ
                return $this->redirect($eduprog_page->getUrlPath());
            }
            $eduprog_model = $news_model->eduprog;
            // создание новой новости на основе существующей, при условии, что она принадлежит текущему пользователю
            $news_form->loadFromNews($news_model);
            $news_form->id = null; // чтобы скопировать, а не редактировать
        }

        if ($id) {
            $news_model = News::findOne((int)$id);
            if (!$news_model) {
                // новость не найдена - вернуться на страницу списка программ
                return $this->redirect($eduprog_page->getUrlPath());
            }
            $eduprog_model = $news_model->eduprog;
            // редактирование или просмотр новости, в зависимости от статуса
            if ($news_model->status == News::STATUS_WAITING || $news_model->status == News::STATUS_PUBLIC) {
                $news_form->loadFromNews($news_model);
            } else {
                // страница просмотра
                $this->setMeta($model);
                return $this->render($model->view . '_read', ['model' => $model, 'news_model' => $news_model, 'members_page' => $members_page, 'member_view_page' => $member_view_page, 'message_page' => $message_page]);
            }
        }

        if ((!$eduprog_model) or ($eduprog_model->author_id != $user->id) or $eduprog_model->is_corporative) {
            // если не нашли или редактировать пытается не автор, то редирект на список
            return $this->redirect($eduprog_page->getUrlPath());
        }
        $news_form->eduprog_id = $eduprog_model->id;

        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'news_form' => $news_form, 'eduprog_model' => $eduprog_model, 'original' => $news_model, 'message_page' => $message_page]);
    }

    /* страница создания/редактирования/копирования/просмотра порядка обучения программы */
    public function actionEduprogViewTrainingprocCreate($model, $eduprog_id = false, $id = false, $copy = false)
    {
        // выключатель ДПО
        if (!Yii::$app->params['enable_dpo']) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        /* проверка разрешения публикации ДПО */
        if (!$user->organization->can_service) {
            /* выводим сообщение о необходимости регистрации на маркетплейс */
            return $this->render($model->view, ['model' => $model, 'content' => $model->need_marketplace]);
        }

        /* проверка разрешения публикации ДПО */
        if (!$user->organization->license_service) {
            /* выводим сообщение о необходимости регистрации на маркетплейс */
            return $this->render($model->view, ['model' => $model, 'content' => $model->need_license]);
        }

        /* страница доступна только ЭО с регистрацией в качестве Юрлица */
        if (!in_array($user->role, ['exporg']) or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        /* страница списка программ ДПО */
        $eduprog_page = LKEduprogList::find()->where(['model' => LKEduprogList::class, 'visible' => 1])->one();

        if (!($eduprog_id or $id or $copy)) {
            // если не заполнен ни один из параметров - то вернуться на страницу списка программ
            return $this->redirect($eduprog_page->getUrlPath());
        }

        // страница списка порядка обучения
        $message_page = LKEduprogViewTrainingproc::find()->where(['model' => LKEduprogViewTrainingproc::class, 'visible' => 1])->one();
        // страница списка слушателей
        $members_page = LKEduprogViewMembers::find()->where(['model' => LKEduprogViewMembers::class, 'visible' => 1])->one();
        $member_view_page = LKEduprogViewMemberNews::find()->where(['model' => LKEduprogViewMemberNews::class, 'visible' => 1])->one();

        $eduprog_model = false;
        $trainingproc_model = false;

        $action = '';

        $trainingproc_form = new LKEduprogTrainingproc();

        if ($eduprog_id) {
            $eduprog_model = Eduprog::findOne((int)$eduprog_id);
            // создание порядка обучения
        }

        if ($copy) {
            $trainingproc_model = EduprogTrainingproc::findOne((int)$copy);
            if (!$trainingproc_model) {
                // запись не найдена - вернуться на страницу списка программ
                return $this->redirect($eduprog_page->getUrlPath());
            }
            $eduprog_model = $trainingproc_model->eduprog;
            // создание новой записи на основе существующей, при условии, что она принадлежит текущему пользователю
            $trainingproc_form->loadFromTrainingProc($trainingproc_model);
            $trainingproc_form->id = null; // чтобы скопировать, а не редактировать
        }

        if ($id) {
            $trainingproc_model = EduprogTrainingproc::findOne((int)$id);
            if (!$trainingproc_model) {
                // запись не найдена - вернуться на страницу списка программ
                return $this->redirect($eduprog_page->getUrlPath());
            }
            $eduprog_model = $trainingproc_model->eduprog;
            // редактирование или просмотр записи, в зависимости от статуса
            if ($trainingproc_model->status == EduprogTrainingproc::STATUS_WAITING || $trainingproc_model->status == EduprogTrainingproc::STATUS_PUBLIC) {
                $trainingproc_form->loadFromTrainingProc($trainingproc_model);
            } else {
                // страница просмотра
                $this->setMeta($model);
                return $this->render($model->view . '_read', ['model' => $model, 'trainingproc_model' => $trainingproc_model, 'members_page' => $members_page, 'member_view_page' => $member_view_page, 'training_message_page' => $message_page]);
            }
        }

        if ((!$eduprog_model) or ($eduprog_model->author_id != $user->id) or $eduprog_model->is_corporative) {
            // если не нашли или редактировать пытается не автор, то редирект на список
            return $this->redirect($eduprog_page->getUrlPath());
        }
        $trainingproc_form->eduprog_id = $eduprog_model->id;

        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'trainingproc_form' => $trainingproc_form, 'eduprog_model' => $eduprog_model, 'original' => $trainingproc_model, 'training_message_page' => $message_page]);
    }

    /* ajax-выгрузка слушателей программы со страницы просмотра программы в ЛК организатора */
    public function actionExportMembers($eduprog_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* найти */
        $user = Yii::$app->user->identity->userAR;

        $eduprog = Eduprog::findOne((int)$eduprog_id);
        if (empty($eduprog) or ($eduprog->author_id != $user->id) or $eduprog->is_corporative) {
            return [
                'status' => 'fail',
                'message' => 'Программа не найдена',
            ];
        }

        $columns = [];
        $columns[] = 'number:raw:Номер слушателя';
        $columns[] = 'fio:raw:ФИО слушателя';
        $columns[] = 'email:text:E-mail';
        $columns[] = 'phone:text:Телефон';
        $columns[] = 'tariffes:raw:Оплаченные тарифы';
        $columns[] = 'status:text:Статус';
        $columns[] = 'summ:text:Сумма всех оплат';

        $data = [];
        $members = EduprogMember::find()->where(['eduprog_id' => $eduprog->id])->andWhere(['!=', 'status', EduprogMember::STATUS_PAYMENTER])->all();


        foreach ($members as $key => $member) {
            // собираем данные по билетам - берем билеты

            $orderitems = EduprogorderItem::find()
                ->select(['eduprog_tariff.id as tariff_id', 'eduprog_tariff.name as name', 'eduprog_orderitem.price as price'])
                ->leftJoin('eduprog_tariff', 'eduprog_tariff.id = eduprog_orderitem.tariff_id')
                ->leftJoin('eduprog_order', 'eduprog_order.id = eduprog_orderitem.eduprogorder_id')
                ->where([
                    'eduprog_order.eduprog_id' => $eduprog->id,
                    'eduprog_order.is_payed' => 1,
                    'eduprog_orderitem.user_id' => $member->user_id,
                    'eduprog_orderitem.status' => EduprogorderItem::STATUS_NEW
                ])
                ->asArray()->all();

            $tariffs_data = [];
            foreach ($orderitems as $orderitem) {
                $tariffs_data[$orderitem['tariff_id']] = [
                    'count' => ($tariffs_data[$orderitem['tariff_id']]['count'] ? $tariffs_data[$orderitem['tariff_id']]['count'] + 1 : 1),
                    'name' => $orderitem['name'],
                    // цена у одного тарифа может быть разная, если куплены в разное время
                    'total' => ($tariffs_data[$orderitem['tariff_id']]['total'] ? $tariffs_data[$orderitem['tariff_id']]['total'] + $orderitem['price'] : $orderitem['price']),
                ];
            }
            $tariff_names = [];
            $total_amount = 0;
            foreach ($tariffs_data as $tariff_info) {
                $tariff_names[] = $tariff_info['name'] . ' x' . $tariff_info['count'];
                $total_amount += $tariff_info['total'];
            }
            $data[$key] = [
                'number' => $member->memberNum,
                'fio' => ($member->user ? $member->user->profile->halfname : 'Пользователь не найден'),
                'email' => ($member->user ? $member->user->email : 'Пользователь не найден'),
                'phone' => ($member->user ? $member->user->profile->phone : 'Пользователь не найден'),
                'tariffes' => implode(', ', $tariff_names),
                'status' => $member->statusName,
                'summ' => $total_amount,
            ];
        }

        $data =
            \moonland\phpexcel\Excel::export([
                'autoSize' => true,
                'models' => $data,
                'columns' => $columns,
                // возвращает файл на скачивание
                'asAttachment' => true,
                // с таким именем
                'fileName' => 'Слушатели программы ' . $eduprog->name,
                'formatter' => \Yii::$app->getFormatter()->nullDisplay = null
            ]);
        return true;
    }

    /* ajax-выгрузка заказов программы со страницы списка заказов в ЛК организатора */
    public function actionExportOrders($eduprog_id, $form_id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* найти */
        $user = Yii::$app->user->identity->userAR;

        $eduprog = Eduprog::findOne((int)$eduprog_id);
        if (empty($eduprog) or ($eduprog->author_id != $user->id) or $eduprog->is_corporative) {
            return [
                'status' => 'fail',
                'message' => 'Программа не найдена',
            ];
        }

        $columns = [];
        $columns[] = 'name:raw:ФИО покупателя';
        $columns[] = 'email:text:E-mail';
        $columns[] = 'phone:text:Телефон';
        $columns[] = 'orderNum:text:Номер заказа';
        $columns[] = 'summ:text:Сумма';
        $columns[] = 'created_at:text:Дата создания';

        $models_data = [];
        $orders_list = $eduprog->getOrders($form_id)->all();
        if ($eduprog && !empty($orders_list)) {
            foreach ($orders_list as $key_d => $model) {
                $models_data[$key_d] = [
                    'name' => $model->user->profile->fullname,
                    'email' => $model->user->email,
                    'phone' => ' ' . $model->user->profile->phone,
                    'orderNum' => $model->orderNum,
                    'summ' => $model->price,
                    'created_at' => Yii::$app->formatter->asDatetime($model->created_at, 'dd.MM.y, HH:mm'),
                ];
            }
        }

        $data =
            \moonland\phpexcel\Excel::export([
                'autoSize' => true,
                'models' => $models_data,
                'columns' => $columns,
                // возвращает файл на скачивание
                'asAttachment' => true,
                // с таким именем
                'fileName' => 'Заказы программы ' . $eduprog->name,
            ]);
        return true;
    }

    /* ajax-выгрузка заказов программы со страницы списка позиций заказов в ЛК организатора */
    public function actionExportOrderItems($eduprog_id, $form_id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* найти */
        $user = Yii::$app->user->identity->userAR;

        $eduprog = Eduprog::findOne((int)$eduprog_id);
        if (empty($eduprog) or ($eduprog->author_id != $user->id) or $eduprog->is_corporative) {
            return [
                'status' => 'fail',
                'message' => 'Программа не найдена',
            ];
        }

        $columns = [];
        $columns[] = 'name:raw:ФИО покупателя';
        $columns[] = 'email:text:E-mail покупателя';
        $columns[] = 'phone:text:Телефон покупателя';
        $columns[] = 'orderNum:text:Номер заказа';
        $columns[] = 'created_at:text:Дата создания';
        $columns[] = 'form:raw:Форма регистрации';
        $columns[] = 'tariff:raw:Тариф';
        $columns[] = 'ticket_id:text:Идентификатор билета';
        $columns[] = 'price:text:Цена';
        $columns[] = 'ticket_name:raw:ФИО слушателя';
        $columns[] = 'ticket_email:text:Email слушателя';
        $columns[] = 'ticket_phone:text:Телефон слушателя';
        $columns[] = 'formData:raw:Дополнительные данные';


        $models_data = [];
        $orders_list = $eduprog->getOrders($form_id)->all();

        if ($eduprog && !empty($orders_list)) {

            foreach ($orders_list as $key_d => $order) {
                foreach ($order->items as $key_o => $ticket) {
                    $form_data = [];
                    foreach ($ticket->form_data as $field => $value) {
                        if (is_array($value)) {
                            $value = implode(';', $value);
                        }
                        $form_data[] = $field . ': ' . $value;
                    }

                    $models_data[$key_d . $key_o] = [
                        'name' => $order->user->profile->fullname,
                        'email' => $order->user->email,
                        'phone' => ' ' . $order->user->profile->phone,
                        'orderNum' => $order->orderNum,
                        'created_at' => Yii::$app->formatter->asDatetime($order->created_at, 'dd.MM.y, HH:mm'),
                        'form' => ((!empty($ticket->tariff) && (!empty($ticket->tariff->eduprogForm))) ? $ticket->tariff->eduprogForm->name : ''),
                        'tariff' => $ticket->tariff->name,
                        'ticket_id' => $ticket->id,
                        'price' => $ticket->price,
                        'ticket_name' => $ticket->user->profile->fullname,
                        'ticket_email' => $ticket->user->email,
                        'ticket_phone' => $ticket->user->profile->phone,
                        'formData' => implode(', ', $form_data),
                    ];
                }
            }
        }

        $data =
            \moonland\phpexcel\Excel::export([
                'autoSize' => true,
                'models' => $models_data,
                'columns' => $columns,
                // возвращает файл на скачивание
                'asAttachment' => true,
                // с таким именем
                'fileName' => 'Позиции заказов программы ' . $eduprog->name,
            ]);
        return true;
    }

    /* страница новостей по слушателю ДПО ЛК Эксперта */
    public function actionEduprogViewMemberNews($model, $id)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* страница доступна только участникам АСТ */
        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
        if (!in_array($user->role, ['exporg']) or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            return $this->redirect($profile_page->getUrlPath());
        }

        /* если что-то не так - редирект на страницу списка программ - там разберется. */
        $eduprog_catalog = LKEduprogList::find()->where(['model' => LKEduprogList::class, 'visible' => '1'])->one();
        $redirect_page_url = (!empty($eduprog_catalog) ? $eduprog_catalog->getUrlPath() : $profile_page->getUrlPath());
        if (isset($id)) {
            $member = EduprogMember::findOne((int)$id);
            if (!empty($member->eduprog) && ($member->eduprog->author_id == $user->id)) {
                if (in_array($member->eduprog->status, [Eduprog::STATUS_MODERATE, Eduprog::STATUS_DECLINED])) {
                    return $this->redirect($redirect_page_url);
                }
                $this->setMeta($model);
                /* страница просмотра данных о программе (стартовая страница - список слушателей) */
                $parent_page = LKEduprogViewMembers::find()->where(['model' => LKEduprogViewMembers::class, 'visible' => 1])->one();

                return $this->render($model->view, ['model' => $model, 'member' => $member, 'parent_page' => $parent_page]);
            }
            return $this->redirect($redirect_page_url);

        }
        return $this->redirect($redirect_page_url);

    }

    /* страница порядок обучения по слушателю ДПО ЛК Эксперта */
    public function actionEduprogViewMemberTrainingproc($model, $id)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* страница доступна только участникам АСТ */
        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
        if (!in_array($user->role, ['exporg']) or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            return $this->redirect($profile_page->getUrlPath());
        }

        /* если что-то не так - редирект на страницу списка программ - там разберется. */
        $eduprog_catalog = LKEduprogList::find()->where(['model' => LKEduprogList::class, 'visible' => '1'])->one();
        $redirect_page_url = (!empty($eduprog_catalog) ? $eduprog_catalog->getUrlPath() : $profile_page->getUrlPath());
        if (isset($id)) {
            $member = EduprogMember::findOne((int)$id);
            if (!empty($member->eduprog) && ($member->eduprog->author_id == $user->id)) {
                if (in_array($member->eduprog->status, [Eduprog::STATUS_MODERATE, Eduprog::STATUS_DECLINED])) {
                    return $this->redirect($redirect_page_url);
                }
                $this->setMeta($model);
                /* страница просмотра данных о программе (стартовая страница - список слушателей) */
                $parent_page = LKEduprogViewMembers::find()->where(['model' => LKEduprogViewMembers::class, 'visible' => 1])->one();

                return $this->render($model->view, ['model' => $model, 'member' => $member, 'parent_page' => $parent_page]);
            }
            return $this->redirect($redirect_page_url);

        }
        return $this->redirect($redirect_page_url);

    }

    /* страница сообщений со слушателем ДПО ЛК Эксперта */
    public function actionEduprogViewMemberChat($model, $id)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* страница доступна только участникам АСТ */
        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
        if (!in_array($user->role, ['exporg']) or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            return $this->redirect($profile_page->getUrlPath());
        }

        /* если что-то не так - редирект на страницу списка программ - там разберется. */
        $eduprog_catalog = LKEduprogList::find()->where(['model' => LKEduprogList::class, 'visible' => '1'])->one();
        $redirect_page_url = (!empty($eduprog_catalog) ? $eduprog_catalog->getUrlPath() : $profile_page->getUrlPath());
        if (isset($id)) {
            $member = EduprogMember::findOne((int)$id);
            if (!empty($member->eduprog) && ($member->eduprog->author_id == $user->id)) {
                if (in_array($member->eduprog->status, [Eduprog::STATUS_MODERATE, Eduprog::STATUS_DECLINED])) {
                    return $this->redirect($redirect_page_url);
                }
                $this->setMeta($model);
                /* страница просмотра данных о программе (стартовая страница - список слушателей) */
                $parent_page = LKEduprogViewMembers::find()->where(['model' => LKEduprogViewMembers::class, 'visible' => 1])->one();

                return $this->render($model->view, ['model' => $model, 'member' => $member, 'parent_page' => $parent_page]);
            }
            return $this->redirect($redirect_page_url);

        }
        return $this->redirect($redirect_page_url);

    }

    /* страница платежей по слушателю ДПО ЛК Эксперта */
    public function actionEduprogViewMemberOrders($model, $id)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* страница доступна только участникам АСТ */
        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
        if (!in_array($user->role, ['exporg']) or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            return $this->redirect($profile_page->getUrlPath());
        }

        /* если что-то не так - редирект на страницу списка программ - там разберется. */
        $eduprog_catalog = LKEduprogList::find()->where(['model' => LKEduprogList::class, 'visible' => '1'])->one();
        $redirect_page_url = (!empty($eduprog_catalog) ? $eduprog_catalog->getUrlPath() : $profile_page->getUrlPath());
        if (isset($id)) {
            $member = EduprogMember::findOne((int)$id);
            if (!empty($member->eduprog) && ($member->eduprog->author_id == $user->id)) {
                if (in_array($member->eduprog->status, [Eduprog::STATUS_MODERATE, Eduprog::STATUS_DECLINED])) {
                    return $this->redirect($redirect_page_url);
                }
                $this->setMeta($model);
                /* страница просмотра данных о программе (стартовая страница - список слушателей) */
                $parent_page = LKEduprogViewMembers::find()->where(['model' => LKEduprogViewMembers::class, 'visible' => 1])->one();

                $orders_of_member = Eduprogorder::find()
                    ->leftJoin('eduprog_orderitem', 'eduprog_orderitem.eduprogorder_id = eduprog_order.id')
                    ->where([
                        'eduprog_order.eduprog_id' => $member->eduprog->id,
                        'eduprog_order.is_payed' => true,
                    ])
                    ->andWhere(['OR', ['eduprog_order.user_id' => $member->user_id], ['eduprog_orderitem.user_id' => $member->user_id]])
                    ->all();

                return $this->render($model->view, ['model' => $model, 'member' => $member, 'parent_page' => $parent_page, 'orders' => $orders_of_member]);
            }
            return $this->redirect($redirect_page_url);

        }
        return $this->redirect($redirect_page_url);

    }

    /* страница документов по слушателю ДПО ЛК Эксперта */
    public function actionEduprogViewMemberDocs($model, $id)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* страница доступна только участникам АСТ */
        $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
        if (!in_array($user->role, ['exporg']) or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            return $this->redirect($profile_page->getUrlPath());
        }

        /* если что-то не так - редирект на страницу списка программ - там разберется. */
        $eduprog_catalog = LKEduprogList::find()->where(['model' => LKEduprogList::class, 'visible' => '1'])->one();
        $redirect_page_url = (!empty($eduprog_catalog) ? $eduprog_catalog->getUrlPath() : $profile_page->getUrlPath());
        if (isset($id)) {
            $member = EduprogMember::findOne((int)$id);
            if (!empty($member->eduprog) && ($member->eduprog->author_id == $user->id)) {
                if (in_array($member->eduprog->status, [Eduprog::STATUS_MODERATE, Eduprog::STATUS_DECLINED])) {
                    return $this->redirect($redirect_page_url);
                }
                $this->setMeta($model);
                /* страница просмотра данных о программе (стартовая страница - список слушателей) */
                $parent_page = LKEduprogViewMembers::find()->where(['model' => LKEduprogViewMembers::class, 'visible' => 1])->one();

                $orders_of_member = Eduprogorder::find()
                    ->select('eduprog_order.id')
                    ->leftJoin('eduprog_orderitem', 'eduprog_orderitem.eduprogorder_id = eduprog_order.id')
                    ->where([
                        'eduprog_order.eduprog_id' => $member->eduprog->id,
                        'eduprog_order.is_payed' => true,
                    ])
                    ->andWhere(['OR', ['eduprog_order.user_id' => $member->user_id], ['eduprog_orderitem.user_id' => $member->user_id]])
                    ->asArray()
                    ->column();
                $orders_of_member = array_unique($orders_of_member);

                $contracts = Educontractitem::find()->where(['IN', 'order_id', $orders_of_member])->andwhere(['visible' => 1])->all();

                return $this->render($model->view, ['model' => $model, 'member' => $member, 'parent_page' => $parent_page, 'contracts' => $contracts]);
            }
            return $this->redirect($redirect_page_url);

        }
        return $this->redirect($redirect_page_url);

    }

    /* страница списка программ ДПО в ЛК Слушателя */
    public function actionEduprogClientList($model)
    {
        // выключатель ДПО
        if (!Yii::$app->params['enable_dpo']) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* страница доступна всем залогиненым пользователям */

        $status_filter = Yii::$app->request->get('status', false);

        $items_query = Eduprog::findVisible()
            ->leftJoin('eduprog_member', 'eduprog_member.eduprog_id = eduprog.id')
            ->where(['eduprog_member.user_id' => $user->id]);
        switch ($status_filter) {
            case 'new':
                // и если слушатель не в статусе "обучается" и "ожидает"
                $items_query->andWhere(['>', 'date_stop', new \yii\db\Expression('CURDATE()')]);
                $items_query->andWhere(['IN', 'eduprog_member.status', [EduprogMember::STATUS_ACTIVE, EduprogMember::STATUS_WAITING]]);
                break;
            case 'archive':
                $items_query->andWhere(['<=', 'date_stop', new \yii\db\Expression('CURDATE()')]);
                $items_query->andWhere(['NOT IN', 'eduprog_member.status', [EduprogMember::STATUS_ACTIVE, EduprogMember::STATUS_WAITING]]);
                break;
        }
        $items = $items_query->all();

        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'items' => $items, 'status_filter' => $status_filter]);
    }

    /* страница Новости клиента ДПО */
    public function actionEduprogClientNews($model, $id)
    {
        // выключатель ДПО
        if (!Yii::$app->params['enable_dpo']) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* страницу может просматривать любой авторизованный пользователь */

        $eduprog_list_page = LKEduprogClientList::find()->where(['model' => LKEduprogClientList::class, 'visible' => 1])->one();

        $eduprog = Eduprog::findOne((int)$id);
        // проверить, что пользователь является слушателем этой программы

        $member = $eduprog->getMemberByUser($user->id);
        if ($eduprog && $member && ($member->status == EduprogMember::STATUS_ACTIVE)) {
            $this->setMeta($model);
            return $this->render($model->view, ['model' => $model, 'eduprog' => $eduprog, 'member' => $member]);
        }
        return $this->redirect($eduprog_list_page->getUrlPath());

    }

    /* страница Новости клиента ДПО */
    public function actionEduprogClientTrainingproc($model, $id)
    {
        // выключатель ДПО
        if (!Yii::$app->params['enable_dpo']) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* страницу может просматривать любой авторизованный пользователь */

        $eduprog_list_page = LKEduprogClientList::find()->where(['model' => LKEduprogClientList::class, 'visible' => 1])->one();

        $eduprog = Eduprog::findOne((int)$id);
        // проверить, что пользователь является слушателем этой программы

        $member = $eduprog->getMemberByUser($user->id);
        if ($eduprog && $member && ($member->status == EduprogMember::STATUS_ACTIVE)) {
            $this->setMeta($model);
            return $this->render($model->view, ['model' => $model, 'eduprog' => $eduprog, 'member' => $member]);
        }
        return $this->redirect($eduprog_list_page->getUrlPath());

    }

    /* страница Cообщения клиента ДПО */
    public function actionEduprogClientChat($model, $id)
    {
        // выключатель ДПО
        if (!Yii::$app->params['enable_dpo']) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* страницу может просматривать любой авторизованный пользователь */

        $eduprog_list_page = LKEduprogClientList::find()->where(['model' => LKEduprogClientList::class, 'visible' => 1])->one();

        $eduprog = Eduprog::findOne((int)$id);
        // проверить, что пользователь является слушателем этой программы

        $member = $eduprog->getMemberByUser($user->id);
        if ($eduprog && $member && ($member->status == EduprogMember::STATUS_ACTIVE)) {
            $this->setMeta($model);
            return $this->render($model->view, ['model' => $model, 'eduprog' => $eduprog, 'member' => $member]);
        }
        return $this->redirect($eduprog_list_page->getUrlPath());

    }

    /* страница Стоимость клиента ДПО */
    public function actionEduprogClientPrice($model, $id)
    {
        // выключатель ДПО
        if (!Yii::$app->params['enable_dpo']) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* страницу может просматривать любой авторизованный пользователь */

        $eduprog_list_page = LKEduprogClientList::find()->where(['model' => LKEduprogClientList::class, 'visible' => 1])->one();

        $eduprog = Eduprog::findOne((int)$id);
        if ($eduprog && $eduprog->isParticipantUser($user->id)) {
            $this->setMeta($model);
            return $this->render($model->view, ['model' => $model, 'eduprog' => $eduprog]);
        }
        return $this->redirect($eduprog_list_page->getUrlPath());

    }

    /* страница Платежи клиента ДПО */
    public function actionEduprogClientOrder($model, $id)
    {
        // выключатель ДПО
        if (!Yii::$app->params['enable_dpo']) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* страницу может просматривать любой авторизованный пользователь */

        $eduprog_list_page = LKEduprogClientList::find()->where(['model' => LKEduprogClientList::class, 'visible' => 1])->one();

        $eduprog = Eduprog::findOne((int)$id);
        if ($eduprog && $eduprog->isParticipantUser($user->id)) {
            $this->setMeta($model);
            $orders = Eduprogorder::find()->where(['eduprog_id' => $eduprog->id, 'user_id' => $user->id, 'is_payed' => 1])->orderBy('id DESC')->all();
            return $this->render($model->view, ['model' => $model, 'eduprog' => $eduprog, 'orders' => $orders]);
        }
        return $this->redirect($eduprog_list_page->getUrlPath());

    }

    /* страница Документы клиента ДПО */
    public function actionEduprogClientDocuments($model, $id)
    {
        // выключатель ДПО
        if (!Yii::$app->params['enable_dpo']) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* страницу может просматривать любой авторизованный пользователь */

        $eduprog_list_page = LKEduprogClientList::find()->where(['model' => LKEduprogClientList::class, 'visible' => 1])->one();

        $eduprog = Eduprog::findOne((int)$id);
        if ($eduprog && $eduprog->isParticipantUser($user->id)) {
            $this->setMeta($model);
            $contracts = Educontractitem::find()
                ->leftJoin('eduprog_order', 'eduprog_order.id = eduprog_contractitem.order_id')
                ->where(['eduprog_contractitem.user_id' => $user->id, 'eduprog_contractitem.eduprog_id' => $eduprog->id, 'eduprog_order.is_payed' => 1])
                ->all();
            $member = $eduprog->getMemberByUser($user->id);
            return $this->render($model->view, ['model' => $model, 'eduprog' => $eduprog, 'contracts' => $contracts, 'member' => $member]);
        }
        return $this->redirect($eduprog_list_page->getUrlPath());

    }

    /* страница Содержимое программы клиента ДПО */
    public function actionEduprogClientContent($model, $id)
    {
        // выключатель ДПО
        if (!Yii::$app->params['enable_dpo']) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* страницу может просматривать любой авторизованный пользователь */

        $eduprog_list_page = LKEduprogClientList::find()->where(['model' => LKEduprogClientList::class, 'visible' => 1])->one();

        $eduprog = Eduprog::findOne((int)$id);
        if ($eduprog && $eduprog->isParticipantUser($user->id)) {
            $this->setMeta($model);
            return $this->render($model->view, ['model' => $model, 'eduprog' => $eduprog]);
        }
        return $this->redirect($eduprog_list_page->getUrlPath());

    }

    /* страница Содержимое программы клиента ДПО */
    public function actionEduprogClientDopinfo($model, $id)
    {
        // выключатель ДПО
        if (!Yii::$app->params['enable_dpo']) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;
        /* страницу может просматривать любой авторизованный пользователь */
        $eduprog_list_page = LKEduprogClientList::find()->where(['model' => LKEduprogClientList::class, 'visible' => 1])->one();

        $eduprog = Eduprog::findOne((int)$id);
        if ($eduprog && $eduprog->isParticipantUser($user->id)) {
            $this->setMeta($model);
            $member = $eduprog->getMemberByUser($user->id);
            return $this->render($model->view, ['model' => $model, 'eduprog' => $eduprog, 'member' => $member]);
        }
        return $this->redirect($eduprog_list_page->getUrlPath());

    }

    /* страница переноса мероприятия */
    public function actionEduprogChangeDate($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ */
        if (!in_array($role, ['exporg']) or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        $eduprog_id = Yii::$app->request->get('id', null);

        /* страница списка программ */
        $eduprog_list_page = LKEduprogList::find()->where(['model' => LKEduprogList::class, 'visible' => 1])->one();

        /* если не указан id переносимого мероприятия */
        if (!$eduprog_id) {
            return $this->redirect($eduprog_list_page->getUrlPath());
        }

        $original_eduprog = Eduprog::findOne((int)$eduprog_id);

        /* если не нашли или редактировать пытается не автор, то редирект на список. */
        if ((!$original_eduprog) or ($original_eduprog->author_id != $user->id) or $original_eduprog->is_corporative) {
            return $this->redirect($eduprog_list_page->getUrlPath());
        }

        /* если мероприятие не на доработке или готов к публикации, либо архивное, либо у него нет проданных билетов - редирект на список */
        if (
            !in_array($original_eduprog->status, [Eduprog::STATUS_NEED_EDIT, Eduprog::STATUS_PUBLIC])
            or (strtotime($original_eduprog->date_stop) < strtotime(date('Y-m-d')))
            or empty($original_eduprog->ordersAll)
        ) {
            /* редирект - такую программу нельзя переносить через форму */
            return $this->redirect($eduprog_list_page->getUrlPath());
        }

        $changedate_model = new EduprogDatechange();
        $changedate_model->eduprog_id = $original_eduprog->id;
        $changedate_model->old_date_start = Yii::$app->formatter->asDate($original_eduprog->date_start, 'php:d.m.Y');
        $changedate_model->old_date_stop = Yii::$app->formatter->asDate($original_eduprog->date_stop, 'php:d.m.Y');
        $changedate_model->new_date_start = Yii::$app->formatter->asDate($original_eduprog->date_start, 'php:d.m.Y');
        $changedate_model->new_date_stop = Yii::$app->formatter->asDate($original_eduprog->date_stop, 'php:d.m.Y');

        /* заполнить стандартный текст письма */
        $changedate_model->letter_text = SettingsText::getInfo('changedate_letter_eduprog');

        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'changedate_model' => $changedate_model, 'original' => $original_eduprog]);
    }

    /* отправка формы переноса мероприятия */
    public function actionSendChangeDateForm()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* найти пользователя */
        $user = Yii::$app->user->identity->userAR;

        /* публиковать мероприятия могут только Участники АСТ */
        if (!in_array($user->role, ['exporg']) or ($user->organization->type_mp != Organization::TYPE_OOO)) {
            return [
                'status' => 'fail',
                'message' => 'У вас нет прав на отмену мероприятия',
            ];
        }

        $modelform = new EduprogDatechange();
        /* при сохранении все поля дозаполнятся автоматически */
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->save()) {
            /* после сейва чтоб даты прогрузились в нужном формате */
            $modelform->refresh();
            /* меняем дату проведения у мероприятия */
            $original_eduprog = $modelform->eduprog;
            $original_eduprog->date_start = $modelform->new_date_start;
            $original_eduprog->date_stop = $modelform->new_date_stop;

            // Если дата закрытия оказалась раньше даты окончания - сделать +1 день от даты окончания.
            $message_dop = '';
            if (strtotime($original_eduprog->date_stop) >= strtotime($original_eduprog->date_close)) {
                $original_eduprog->date_close = date('d.m.Y', strtotime($original_eduprog->date_stop) + 24 * 60 * 60);
                if (!empty($original_eduprog->currentModeration)) {
                    $moderation = $original_eduprog->currentModeration;
                    $moderation->date_close = $original_eduprog->date_close;
                    $moderation->save();
                }
                $message_dop = ' Дата закрытия программы и выдачи документов перенесена на ' . $original_eduprog->date_close;
            }

            if ($original_eduprog->save()) {
                /* отправляем уведомление администратору */
                $modelform->sendChangeDateLetterAdmin();
                /* и сообщение пользователю */
                $redirect_page = LKEduprogEdit::find()->where(['model' => LKEduprogEdit::class, 'visible' => 1])->one();
                $redirect_to = Url::toRoute([$redirect_page->getUrlPath(), 'id' => $original_eduprog->id, 'step' => 2]);
                return [
                    'status' => 'success',
                    'redirect_to' => $redirect_to,
                    'message' => 'Программа успешно перенесена.' . $message_dop,
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Невозможно изменить дату программы. ' . \app\helpers\MainHelper::getHelpText(),
            ];

        }
        return [
            'status' => 'fail',
            'message' => 'При сохранении данных возникла ошибка. ' . \app\helpers\MainHelper::getHelpText(),
        ];
    }

    public function actionGettariff()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* найти */
        $user = Yii::$app->user->identity->userAR;
        $post = Yii::$app->request->post();

        $tariff = Tariff::find()->where(['id' => (int)$post['tariff_id'], 'deleted' => 0])->one();
        if (!$tariff or ($tariff->event->author_id != $user->id)) {
            return [
                'status' => 'fail',
                'message' => 'Тариф не найден',
            ];
        }
        $prices = [];
        foreach ($tariff->prices as $price_item) {
            $prices[] = ['date' => Yii::$app->formatter->asDatetime($price_item->start_publish, 'php:d.m.Y'), 'price' => number_format($price_item->price, 0, '.', '')];
        }
        $tariff_data = [
            'name' => $tariff->name,
            'description' => $tariff->description,
            'start_publish' => Yii::$app->formatter->asDatetime($tariff->start_publish, 'php:d.m.Y'),
            'end_publish' => Yii::$app->formatter->asDatetime($tariff->end_publish, 'php:d.m.Y'),
            'limit_tickets' => $tariff->limit_tickets,
            'tickets_count' => $tariff->tickets_count,
            'visible' => $tariff->visible,
            'prices' => $prices,
        ];
        $ret_data = [
            'status' => 'success',
            'data' => $tariff_data,
        ];
        return $ret_data;
    }

    public function actionResultinfo()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если не аякс */
        if (!Yii::$app->request->isAjax) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка типа запроса',
            ];
        }
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        /* найти */
        $user = Yii::$app->user->identity->userAR;
        $post = Yii::$app->request->post();

        $result_record = Formsresult::findOne((int)$post['result_id']);
        if (!$result_record or ($result_record->form->ownermodel->author_id != $user->id)) {
            return [
                'status' => 'fail',
                'message' => 'Запись не найдена',
            ];
        }

        $data_list = [];
        foreach ($result_record->fields as $name => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $data_list[] = ['name' => $name, 'text' => $value];
        }
        $ret_data = [
            'status' => 'success',
            'date' => Yii::$app->formatter->asDatetime($result_record->created_at, 'php:d.m.Y'),
            'fio' => $result_record->surname . ' ' . $result_record->name . ' ' . $result_record->patronymic,
            'email' => $result_record->email,
            'phone' => $result_record->phone,
            'data_list' => $data_list,
        ];
        return $ret_data;
    }
}
