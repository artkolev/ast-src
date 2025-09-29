<?php

namespace app\modules\pages\controllers\frontend;

use app\components\SocketServer;
use app\models\MessageNew;
use app\modules\admin\components\SecureFilestoreModel;
use app\modules\message\models\Chat;
use app\modules\message\models\Message;
use app\modules\order\models\Order;
use app\modules\pages\models\Login;
use app\modules\pages\models\MessagesChat;
use app\modules\pages\models\MessagesIndex;
use app\modules\pages\models\ProfileIndex;
use app\modules\queries\models\Queries;
use app\modules\users\models\UserAR;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\UploadedFile;

class MessageController extends LKController
{
    /* страница с документами */
    public function actionIndex($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        $get = Yii::$app->request->get();
        $searched_user = false;
        if (!empty($get['chat_q'])) {
            $searched_user = UserAR::find()->where(['id' => $get['chat_q'], 'status' => UserAR::STATUS_ACTIVE])->one();
            $my_chats = Chat::find()->leftJoin('chat_ref_user', 'chat_ref_user.chat_id = chat.id')->leftJoin('chat_ref_user search', 'search.chat_id = chat.id')->where(['status' => Chat::STATUS_OPEN, 'chat_ref_user.user_id' => $user->id, 'search.user_id' => (int)$get['chat_q']])->all();
        } else {
            $my_chats = Chat::find()->leftJoin('chat_ref_user', 'chat_ref_user.chat_id = chat.id')->where(['status' => Chat::STATUS_OPEN, 'chat_ref_user.user_id' => $user->id])->all();
        }
        $chats = [];
        foreach ($my_chats as $chat) {
            $chats[$chat->messages[0]->created_at] = $chat;
        }
        krsort($chats);
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'chats' => $chats, 'searched_user' => $searched_user]);

    }

    /* страница с документами */
    public function actionCreate($model)
    {
        /* === временно скрыто задача #3557 === */
        $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
        return $this->redirect($login_page->getUrlPath());
        /* ================================== */

        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }

        $modelform = new MessageNew();
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model, 'modelform' => $modelform]);

    }

    /* страница с чатом */
    public function actionChat($model)
    {
        /* редирект на логин, если неавторизован */
        if (Yii::$app->user->isGuest) {
            $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
            return $this->redirect($login_page->getUrlPath());
        }
        $user = Yii::$app->user->identity->userAR;

        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        /* страница доступна только участникам АСТ */
        if (!in_array($role, ['expert', 'exporg'])) {
            $profile_page = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
            return $this->redirect($profile_page->getUrlPath());
        }
        /* найти чат */
        $get = Yii::$app->request->get();
        $chat = Chat::find()->leftJoin('chat_ref_user', 'chat_ref_user.chat_id = chat.id')->where(['chat.id' => (int)$get['id'], 'chat_ref_user.user_id' => $user->id])->andWhere(['<', 'chat.status', Chat::STATUS_DELETED])->one();

        if ($chat) {
            $this->setMeta($model);
            return $this->render($model->view, ['model' => $model, 'chat' => $chat]);
        }
        // Выбранный чат недоступен - редирект на список чатов
        $message_index = MessagesIndex::find()->where(['model' => MessagesIndex::class, 'visible' => 1])->one();
        return $this->redirect($message_index->getUrlPath());


    }

    /* страница с документами */
    public function actionGetusers()
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

            $profiles = UserAR::find()->leftJoin('profile', 'profile.user_id = user.id')->where(
                ['or',
                    ['LIKE', 'LCASE(CONCAT(`profile`.`surname`," ",`profile`.`name`," ",`profile`.`patronymic`))', $q],
                    ['LIKE', 'profile.organization_name', $q]
                ]
            )->andWhere(['user.status' => UserAR::STATUS_ACTIVE])->andWhere(['!=', 'user.id', $user->id]);
            if (!empty($get['user'])) {
                // поиск по существующим чатам
                // найти все мои чаты и составить список собеседников
                $my_chats = Chat::find()->leftJoin('chat_ref_user', 'chat_ref_user.chat_id = chat.id')->where(['status' => Chat::STATUS_OPEN, 'chat_ref_user.user_id' => $user->id])->all();
                $my_recipients = [];
                foreach ($my_chats as $chat) {
                    $my_recipients = array_merge($my_recipients, ArrayHelper::map($chat->member, 'id', 'id'));
                }
                $my_recipients = array_unique($my_recipients);
                // отфильтьровать по вхождению id в список собеседников
                $profiles->andWhere(['IN', 'user.id', $my_recipients]);
            }
            $profiles = $profiles->all();
            $items = [];
            foreach ($profiles as $item) {
                $items[] = ['id' => $item->profile->user_id, 'text' => $item->profile->getFullname()];
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

    /* страница с документами */
    public function actionGetuserinfo()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->user->isGuest) {
            return ['status' => 'fail', 'message' => 'Вы не авторизованы'];
        }

        if (Yii::$app->request->isAjax) {
            // обрабатываем только ajax-запросы
            $get = Yii::$app->request->get();
            /** @var UserAR $user */
            $user = UserAR::find()->where(['id' => $get['id'], 'status' => UserAR::STATUS_ACTIVE])->one();
            if ($user && in_array($user->role, ['expert', 'exporg'])) {
                $info = [
                    'image' => $user->profile->getThumb('image', 'main'),
                    'name' => $user->profile->fullname . ((Yii::$app->user->id == $user->id) ? ' (это Вы)' : ''),
                    'role' => $user->roleName,
                    'main_direction' => ($user->directionM) ? $user->directionM->name : 'Не задана',
                ];
                return [
                    'status' => 'success',
                    'info' => $info,
                    'message' => 'Пользователь найден',
                ];
            }
            return [
                'status' => 'fail',
                'message' => 'Пользователь не найден',
            ];

        }
        return [
            'status' => 'fail',
        ];

    }

    /* отправка сообщения */
    public function actionNewmessage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        $user = Yii::$app->user->identity->userAR;
        /* сохранить сообщение */
        $modelform = new MessageNew();
        if (Yii::$app->request->isAjax && $modelform->sanitize(Yii::$app->request->post()) && $modelform->validate()) {

            $fileInstances = UploadedFile::getInstances($modelform, 'files_loader');
            foreach ($fileInstances as $key => $fileInstance) {
                if ($key == 3) {
                    return [
                        'status' => 'fail',
                        'message' => 'Превышено максимальное количество файлов.',
                    ];
                }
                // проверить размер и расширение
                if (!in_array($fileInstance->type, ["image/png", "image/jpeg", "application/pdf", "image/svg", "image/svg+xml", "image/webp", "application/xml", "text/xml", "image/tiff", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/vnd.openxmlformats-officedocument.presentationml.presentation", "application/vnd.ms-powerpoint", "application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"])) {
                    return [
                        'status' => 'fail',
                        'message' => 'Такой формат файлов не поддерживается.',
                    ];
                }
                if ($fileInstance->size > 5 * 1024 * 1024) {
                    return [
                        'status' => 'fail',
                        'message' => 'Максимальный размер файла - 5Мб.',
                    ];
                }
            }

            if (!empty($modelform->id)) {
                // редактирование сообщений не реализовано
                // найти сообщение по id и если оно принадлежит текущему пользователю - изменить текст сообщения
                // вернуть success
            } else {
                if (!empty($modelform->chat_id)) {
                    // если пользователь mks и его еще нет в чате - добавить
                    if ($user->role == 'mks') {
                        $chat = Chat::findOne($modelform->chat_id);
                        $chat->addMember($user->id);
                    }
                    // найти указанный чат и если текущий пользователь числится среди участников чата - добавить сообщение в чат
                    $needed_chat = Chat::find()->leftJoin('chat_ref_user', 'chat_ref_user.chat_id = chat.id')->where(['chat.id' => $modelform->chat_id, 'chat_ref_user.user_id' => $user->id])->andWhere(['<', 'chat.status', Chat::STATUS_DELETED])->one();
                    if ($needed_chat) {
                        $newmessage = new Message();
                        $newmessage->chat_id = $needed_chat->id;
                        $newmessage->user_id = $user->id;
                        $newmessage->message = $modelform->message;
                        $newmessage->status = Message::STATUS_NORMAL;
                        $newmessage->edited = 0;
                        $newmessage->visible = 1;
                        $newmessage->files_loader = $modelform->files_loader;

                        if ($newmessage->save()) {

                            if (!empty($fileInstances)) {
                                foreach ($fileInstances as $key => $fileInstance) {
                                    $new_file_model = new SecureFilestoreModel();
                                    $new_file_model->isMain = ($key == 0);
                                    $new_file_model->file_path = 'secure_media/chat_' . $newmessage->chat->id . '/';
                                    $new_file_model->order = $key;
                                    $new_file_model->keeper_id = $newmessage->id;
                                    $new_file_model->keeper_class = Message::class;
                                    $new_file_model->keeper_field = 'files';
                                    $new_file_model->new_name = 'chat_' . $newmessage->chat->id . '_' . time() . rand(10, 99);
                                    $new_file_model->file_loader = $fileInstance;
                                    $new_file_model->description = '';
                                    $res = $newmessage->link('files', $new_file_model);
                                    if (!$res) {
                                        foreach ($newmessage->chat->member as $member) {
                                            $new_file_model->allowToUser($member->id);
                                        }
                                    }
                                }
                            }

                            // создать уведомление для участников чата
                            foreach ($newmessage->chat->member as $member) {
                                if ($user->id != $member->id) {
                                    $newmessage->createNotification($member->id);
                                }
                                $smsg = [
                                    'event' => 'send',
                                    'backend_type' => 'Message',
                                    'user' => $member->id,
                                    'message' => $newmessage->id
                                ];
                                try {
                                    SocketServer::notify_message($smsg);
                                } catch (\Exception $e) {
                                    // просто идем дальше
                                }
                            }

                            // вернуть success
                            return [
                                'status' => 'success',
                                'text' => $newmessage->message,
                                'date' => Yii::$app->formatter->asDatetime($newmessage->created_at, 'd.MM.y, H:mm'),
                                'message' => 'Сообщение отправлено',
                            ];
                        } else {
                            if ($newmessage->hasErrors()) {
                                return $newmessage->getErrors();
                            }
                            return [
                                'status' => 'fail',
                                'message' => 'Невозможно создать сообщение. ' . \app\helpers\MainHelper::getHelpText(),
                            ];

                        }
                    } else {
                        return [
                            'status' => 'fail',
                            'message' => 'Невозможно создать сообщение, чат недоступен',
                        ];
                    }
                    // вернуть success
                } else {
                    if (!empty($modelform->user_id)) {
                        $recipient = UserAR::find()->where(['id' => $modelform->user_id, 'status' => UserAR::STATUS_ACTIVE])->one();
                        if ($recipient) {
                            // найти чат, участниками которого являются только текущий пользователь и выбранный
                            $needed_chat = Chat::find()->leftJoin('chat_ref_user sender', 'sender.chat_id = chat.id')->leftJoin('chat_ref_user recipient', 'recipient.chat_id = chat.id')->where(['status' => Chat::STATUS_OPEN, 'type' => Chat::TYPE_SIMPLE, 'sender.user_id' => $user->id, 'recipient.user_id' => $recipient->id])->one();
                            // если чат существует - дописать в него сообщение
                            if (!$needed_chat) {
                                // если такого чата нет - создать.
                                $needed_chat = new Chat();
                                $needed_chat->name = 'Индивидуальный чат между ' . $user->profile->fullname . ' и ' . $recipient->profile->fullname;
                                $needed_chat->status = Chat::STATUS_OPEN;
                                $needed_chat->type = Chat::TYPE_SIMPLE;
                                $needed_chat->visible = 1;
                                $members = [$user->id, $recipient->id];
                                $needed_chat->member = array_unique($members);
                                if (!$needed_chat->save()) {
                                    return [
                                        'status' => 'fail',
                                        'message' => 'Невозможно создать новый диалог. ' . \app\helpers\MainHelper::getHelpText(),
                                    ];
                                }
                            }
                            // создать сообщение
                            $newmessage = new Message();
                            $newmessage->chat_id = $needed_chat->id;
                            $newmessage->user_id = $user->id;
                            $newmessage->message = $modelform->message;
                            $newmessage->status = Message::STATUS_NORMAL;
                            $newmessage->edited = 0;
                            $newmessage->visible = 1;
                            $newmessage->files_loader = $modelform->files_loader;

                            if ($newmessage->save()) {

                                if (!empty($fileInstances)) {
                                    foreach ($fileInstances as $key => $fileInstance) {
                                        $new_file_model = new SecureFilestoreModel();
                                        $new_file_model->isMain = ($key == 0);
                                        $new_file_model->file_path = 'secure_media/chat_' . $newmessage->chat->id . '/';
                                        $new_file_model->order = $key;
                                        $new_file_model->keeper_id = $newmessage->id;
                                        $new_file_model->keeper_class = Message::class;
                                        $new_file_model->keeper_field = 'files';
                                        $new_file_model->new_name = 'chat_' . $newmessage->chat->id . '_' . time() . rand(10, 99);
                                        $new_file_model->file_loader = $fileInstance;
                                        $new_file_model->description = '';
                                        $res = $newmessage->link('files', $new_file_model);
                                        if (!$res) {
                                            foreach ($newmessage->chat->member as $member) {
                                                $new_file_model->allowToUser($member->id);
                                            }
                                        }
                                    }
                                }

                                // создать уведомление для участников чата
                                foreach ($newmessage->chat->member as $member) {
                                    if ($user->id != $member->id) {
                                        $newmessage->createNotification($member->id);
                                    }
                                    $smsg = [
                                        'event' => 'send',
                                        'backend_type' => 'Message',
                                        'user' => $member->id,
                                        'message' => $newmessage->id
                                    ];
                                    try {
                                        SocketServer::notify_message($smsg);
                                    } catch (\Exception $e) {
                                        // просто идем дальше
                                    }
                                }

                                // вернуть sucess и ссылку на чат
                                $chat_page = MessagesChat::find()->where(['model' => MessagesChat::class, 'visible' => 1])->one();
                                $chat_url = Url::to([$chat_page->getUrlPath(), 'id' => $needed_chat->id]);
                                return [
                                    'status' => 'success',
                                    'redirect_to' => $chat_url,
                                    'message' => 'Сообщение отправлено',
                                ];
                            }
                            if ($newmessage->hasErrors()) {
                                return $newmessage->getErrors();
                            }
                            return [
                                'status' => 'fail',
                                'message' => 'Невозможно создать сообщение. ' . \app\helpers\MainHelper::getHelpText(),
                            ];


                        }
                        return [
                            'status' => 'fail',
                            'message' => 'Выбранный пользователь на текущий момент недоступен',
                        ];

                    }
                    // невозможно сохранить сообщение - неясен получатель.
                    return [
                        'status' => 'fail',
                        'message' => 'Получатель сообщения не определен',
                    ];

                }
            }
        }
        return [
            'status' => 'fail',
        ];
    }

    /* отправка первого сообщения от МКС */
    public function actionStartmkschat()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        /* ошибка, если неавторизован */
        if (Yii::$app->user->isGuest) {
            return [
                'status' => 'fail',
                'message' => 'Ошибка авторизации',
            ];
        }
        $user = Yii::$app->user->identity->userAR;
        if ($user->role != 'mks') {
            return [
                'status' => 'fail',
                'message' => 'Вы не можете начать чат с МКС',
            ];
        }
        /* вывести в настройки и разделить для заказов и заявок */
        $start_message = 'Добрый день! Чем могу помочь?';

        // найти запрос/заказ для которого начинаем чат

        $post = Yii::$app->request->post();
        if (Yii::$app->request->isAjax && isset($post['instance']) && isset($post['type']) && isset($post['query'])) {
            switch ($post['instance']) {
                case 'Queries':
                    $query = Queries::findOne((int)$post['query']);
                    break;
                case 'Order':
                    $query = Order::findOne((int)$post['query']);
                    break;
            }
            // сообщения в запросах/заказах неплохо бы реализовать интерфейсом, чтоб 100% не было различий
            if ($query) {
                if (in_array($post['type'], [Chat::MODER_CHAT_EXPERT, Chat::MODER_CHAT_CLIENT])) {
                    $query->addMessage($start_message, $user->id, $post['type']);
                    return [
                        'status' => 'success',
                        'message' => 'Чат создан',
                    ];
                }
                return [
                    'status' => 'fail',
                    'message' => 'Некорректный тип чата',
                ];

            }
            return [
                'status' => 'fail',
                'message' => 'Невозможно добавить сообщение.',
            ];


        }
        return [
            'status' => 'fail',
        ];
    }
}
