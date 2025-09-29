<?php

namespace app\modules\admin\controllers;

use app\modules\admin\components\DeepAdminController;
use app\modules\admin\models\LoginForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/* контроллер авторизации в админке */

class LoginController extends DeepAdminController
{
    /* шаблон авторизации в админке */
    public $layout = "@app/modules/admin/views/layouts/login";

    public function behaviors()
    {
        return [
            'access' => [
                /* страница index доступна и гостям, и авторизованным */
                /* страница logout доступна только авторизованным */
                'class' => AccessControl::class,
                'only' => ['logout', 'index'],
                'rules' => [
                    [
                        'actions' => ['index', 'validate'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],

                ],
            ],
            /* разлогиниться можно только post-запросом */
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        /* если пользователь - не гость, */
        if (!Yii::$app->user->isGuest) {
            /* редирект на главную страницу */
            return $this->redirect(['/admin']);
        }
        /* создаем модель авторизации */
        $login_model = new LoginForm();
        $login_model->action = 'default';
        /* если форма отправлена */
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            /* загружаем отправленные данные */
            if ($login_model->load(Yii::$app->request->post())) {
                if (!in_array($login_model->action, ['default', 'code'])) {
                    return [
                        'status' => 'fail',
                        'message' => 'Сценарий не определён',
                    ];
                }
                $login_model->scenario = $login_model->action;
                if ($login_model->validate()) {
                    switch ($login_model->scenario) {
                        case 'default':
                            // выдать код
                            // true - письмо было отправлено, false - таймаут отправки еще не прошел
                            $sended = $login_model->getUser()->userAR->generateNewCodeAndSend();
                            return [
                                'status' => 'success',
                                'action' => 'showCode',
                                'message' => ($sended ? 'Код был отправлен на указанный email' : 'Интервал для повторной отправки кода еще не прошел'),
                            ];
                            break;
                        case 'code':
                            // залогинить
                            if ($login_model->login()) {
                                return [
                                    'status' => 'success',
                                    'action' => 'login',
                                    'url' => '/admin/',
                                ];
                            }
                            return [
                                'status' => 'fail',
                                'message' => 'Авторизация не пройдена',
                            ];

                            break;
                    }
                } else {
                    return [
                        'status' => 'fail',
                        'message' => 'Валидация не пройдена',
                    ];
                }
            } else {
                return [
                    'status' => 'fail',
                    'message' => 'Данные переданы некорректно',
                ];
            }
        }
        /* возвращаем страницу авторизации */
        return $this->render('login', ['model' => $login_model]);
    }

    public function actionValidate()
    {
        /* если пользователь - не гость, */
        if (!Yii::$app->user->isGuest) {
            /* редирект на главную страницу */
            return $this->redirect(['/admin']);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isAjax) {
            $login_model = new LoginForm();
            if ($login_model->load(Yii::$app->request->post())) {
                if (!in_array($login_model->action, ['default', 'code'])) {
                    return false;
                }
                $login_model->scenario = $login_model->action;
                return ActiveForm::validate($login_model);
            }
        }
    }

    public function actionLogout()
    {
        /* разлогиниваемся */
        Yii::$app->user->logout();
        /* переадресация на страницу логина */
        return $this->redirect(['/admin/login']);
    }
}
