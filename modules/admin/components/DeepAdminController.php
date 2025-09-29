<?php

declare(strict_types = 1);

namespace app\modules\admin\components;

use app\modules\users\models\UserDirection;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;

use yii\web\Response;
use yii\widgets\ActiveForm;

/* основной контроллер админки. Остальные контроллеры админки должны наследоваться от него. */

class DeepAdminController extends Controller
{
    /* класс модели, с которым производим основные операции. Для каждого контроллера определяется отдельно. */
    public $modelClass;

    /* основной шаблон админки */
    public $layout = "@app/modules/admin/views/layouts/admin";

    /* заголовок страницы */
    public $title = 'Модуль';

    public function behaviors()
    {
        return [
            /* страница login доступна только гостям */
            /* страница logout доступна только авторизованным */
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'login'],
                'rules' => [
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],

                ],
            ],
            /* остальные экшены доступны только администратору */
            [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
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

    public function beforeAction($action)
    {
        /* заполнение хлебных крошек в админке */
        Yii::$app->view->params['breadcrumbs'] = [];
        /* если текущий модуль не основной */
        if ($action->controller->module->id != 'admin') {
            /* добавляем ссылку на текущий модуль */
            Yii::$app->view->params['breadcrumbs'][] = [
                'label' => $action->controller->module->params['name'],
                'url' => '/admin/'.$action->controller->module->id.'/',
            ];
        }

        return parent::beforeAction($action);
    }

    /* функция возвращает список кнопок на странице (перед грид-вью) */
    public function getButtons()
    {
        return [
            'add_page' => [
                'class' => 'success',
                'name' => 'Создать',
                'url' => Url::toRoute(['/admin/'.$this->module->id.'/'.$this->id.'/create']),
            ],
        ];
    }

    /* основные CRUD действия определены здесь. В остальных контроллерах производится только настройка. */
    /* страница вывода списка записей */
    public function actionIndex()
    {
        /* дописываем в хлебные крошки текущее действие */
        Yii::$app->view->params["breadcrumbs"][] = ['label' => $this->title];
        // запоминаем странцу на которой находимся
        Yii::$app->session->set('page', ArrayHelper::getValue(\Yii::$app->request->get(), 'page', null));
        /* создаем и загружаем поисковую модель */
        $search_model = new $this->modelClass(['forceInit' => true]);
        $search_model->load(Yii::$app->request->queryParams);

        /* отдаем страницу */
        /* проверяем, если у контроллера есть собственный view - то отдаем его. Иначе - отдаем общий. */
        if (file_exists(Yii::getAlias('@app/modules/'.$this->module->id.'/views/backend/'.$this->id.'/index.php'))) {
            $view = 'index';
        } else {
            $view = '@app/modules/admin/backend_views/index';
        }
        return $this->render($view, ['search_model' => $search_model]);
    }

    /* страница создания новой записи */
    public function actionCreate()
    {
        /* дописываем в хлебные крошки текущее действие */
        Yii::$app->view->params["breadcrumbs"][] = ['label' => $this->title];
        /* создаем и загружаем текущую модель GET-параметрами */
        $model = new $this->modelClass(['forceInit' => true]);
        $model->load(\Yii::$app->request->get());
        /* ajax validation */
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post())) {
                return ActiveForm::validate($model);
            }
        }
        /* если форма была отправлена */
        if (Yii::$app->request->isPost) {
            /* догружаем данными из POST-запроса */
            $model->load(\Yii::$app->request->post());
            /* если запись сохранилась */
            if ($model->save()) {
                /* если была нажата кнопка "Сохранить" */
                if (Yii::$app->request->post()['action'] == "save") {
                    /* смотрим GET-параметры */
                    $get_params = \Yii::$app->request->get();
                    /* если в GET-параметрах был "return_url" */
                    if (isset($get_params['return_url'])) {
                        /* переходим по адресу возврата */
                        $ret_url = parse_url($get_params['return_url']);
                        $params = [];
                        if (!empty($ret_url['query'])) {
                            parse_str($ret_url['query'], $params);
                        }
                        $params[0] = $ret_url['path'];
                        $params['#'] = $get_params['anchor'];

                        return $this->redirect(Url::toRoute($params));
                    }
                    /* если задана текущая страница - возвращаемся на неё */
                    $cur_page = (Yii::$app->session->get('page') ? Yii::$app->session->get('page') : null);
                    /* если у модели указан parent_id - перейти в соответствующий раздел */
                    if ($model->hasAttribute('parent_id') && ($model->parent_id > 0)) {
                        /* переходим в категорию модели */
                        return $this->redirect(Url::toRoute([
                            '/admin/'.$this->module->id.'/'.$this->id.'/index/',
                            'parent_id' => $model->parent_id,
                            'page' => $cur_page,
                        ]));
                    }

                    /* иначе возвращаемся на страницу со списком записей */

                    return $this->redirect(Url::toRoute([
                        '/admin/'.$this->module->id.'/'.$this->id.'/index/',
                        'page' => $cur_page,
                    ]));
                }
                /* если была нажата кнопка "Продолжить" */
                if (Yii::$app->request->post()['action'] == "continue") {
                    /* создаем и загружаем новую модель GET-параметрами */
                    $model = new $this->modelClass(['forceInit' => true]);
                    $model->load(\Yii::$app->request->get());
                }
            }
        }
        /* отдаем страницу */
        /* проверяем, если у контроллера есть собственный view - то отдаем его. Иначе - отдаем общий. */
        if (file_exists(Yii::getAlias('@app/modules/'.$this->module->id.'/views/backend/'.$this->id.'/create.php'))) {
            $view = 'create';
        } else {
            $view = '@app/modules/admin/backend_views/create';
        }
        return $this->render($view, ['model' => $model]);
    }

    /* страница редактирования записи */
    public function actionUpdate($id, $checkVis = false)
    {
        /* дописываем в хлебные крошки текущее действие */
        Yii::$app->view->params["breadcrumbs"][] = ['label' => $this->title];
        /* находим запись по id */
        $model = $this->modelClass::findOne((int) $id);
        /* если запись не найдена - возвращаем ошибку */
        if (empty($model)) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        /* если нужно отмечать как просмотренное */
        if ($checkVis) {
            $model->visible = 1;
            $model->updateAttributes(['visible' => 1]);
        }

        /* ajax validation */
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post())) {
                return ActiveForm::validate($model);
            }
        }
        /* если форма была отправлена */
        if (Yii::$app->request->isPost) {
            /* загружаем данные из POST-запроса */
            $model->load(\Yii::$app->request->post());
            /* если запись сохранилась */
            if ($model->save()) {
                /* если была нажата кнопка "Сохранить" */
                if (Yii::$app->request->post()['action'] == "save") {
                    /* смотрим GET-параметры */
                    $get_params = \Yii::$app->request->get();
                    /* если в GET-параметрах был "return_url" */
                    if (isset($get_params['return_url'])) {
                        /* переходим по адресу возврата */
                        $ret_url = parse_url($get_params['return_url']);
                        $params = [];
                        if (!empty($ret_url['query'])) {
                            parse_str($ret_url['query'], $params);
                        }
                        $params[0] = $ret_url['path'];
                        $params['#'] = $get_params['anchor'];

                        return $this->redirect(Url::toRoute($params));
                    }
                    /* если задана текущая страница - возвращаемся на неё */
                    $cur_page = (Yii::$app->session->get('page') ? Yii::$app->session->get('page') : null);
                    /* если у модели указан parent_id - перейти в соответствующий раздел */
                    if ($model->hasAttribute('parent_id') && ($model->parent_id > 0)) {
                        /* переходим в категорию модели */
                        return $this->redirect(Url::toRoute([
                            '/admin/'.$this->module->id.'/'.$this->id.'/index/',
                            'parent_id' => $model->parent_id,
                            'page' => $cur_page,
                        ]));
                    }
                    /* иначе возвращаемся на страницу со списком записей */
                    return $this->redirect(Url::toRoute([
                        '/admin/'.$this->module->id.'/'.$this->id.'/index/',
                        'page' => $cur_page,
                    ]));
                }
            }
        }
        /* отдаем страницу */
        /* проверяем, если у контроллера есть собственный view - то отдаем его. Иначе - отдаем общий. */
        if (file_exists(Yii::getAlias('@app/modules/'.$this->module->id.'/views/backend/'.$this->id.'/update.php'))) {
            $view = 'update';
        } else {
            $view = '@app/modules/admin/backend_views/update';
        }
        return $this->render($view, ['model' => $model]);
    }

    /* страница просмотра записи */
    public function actionView($id, $checkVis = false)
    {
        /* дописываем в хлебные крошки текущее действие */
        Yii::$app->view->params["breadcrumbs"][] = ['label' => $this->title];
        /* находим запись по id */
        $model = $this->modelClass::findOne((int) $id);
        /* если запись не найдена - возвращаем ошибку */
        if (empty($model)) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        /* если нужно отмечать как просмотренное */
        if ($checkVis) {
            $model->visible = 1;
            $model->updateAttributes(['visible' => 1]);
        }

        /* отдаем страницу */
        /* проверяем, если у контроллера есть собственный view - то отдаем его. Иначе - отдаем общий. */
        if (file_exists(Yii::getAlias('@app/modules/'.$this->module->id.'/views/backend/'.$this->id.'/view.php'))) {
            $view = 'view';
        } else {
            $view = '@app/modules/admin/backend_views/view';
        }
        return $this->render($view, ['model' => $model]);
    }

    /* страница удаления записи */
    public function actionDelete($id)
    {
        /* дописываем в хлебные крошки текущее действие */
        Yii::$app->view->params["breadcrumbs"][] = ['label' => $this->title];
        /* находим запись по id */
        $model = $this->modelClass::findOne((int) $id);
        /* если запись не найдена - возвращаем ошибку */
        if (empty($model)) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        /* инициализируем массив ошибок */
        $errors = [];
        /* инициализируем id родителя */
        $parent_id = null;
        /* если пришли по POST-запросу */
        if (Yii::$app->request->isPost) {
            /* если есть иерархическая структура - запоминаем родителя */
            if ($model->hasAttribute('parent_id')) {
                $parent_id = $model->parent_id;
            }
            /* если запись не удалилась */
            if (!$model->delete()) {
                /* записать ошибки в формате одноуровневого массива */
                $errors[] = "Невозможно удалить запись";
                foreach ($model->errors as $field_error) {
                    $errors[] = implode("<br>", $field_error);
                }
            }
        } else {
            /* если пришли по ссылке (не POST-запрос) - допистаь ошибку */
            $errors[] = $this->title." несанкционировано.";
        }
        /* если ошибок не возникло */
        if (empty($errors)) {
            /* если задана текущая страница - возвращаемся на неё */
            $cur_page = (Yii::$app->session->get('page') ? Yii::$app->session->get('page') : null);

            /* переходим на страницу списка элементов */

            return $this->redirect(Url::toRoute([
                '/admin/'.$this->module->id.'/'.$this->id.'/index/',
                'parent_id' => $parent_id,
                'page' => $cur_page,
            ]));
        }
        /* если возникли ошибки - отдаем страницу с описанием */
        /* проверяем, если у контроллера есть собственный view - то отдаем его. Иначе - отдаем общий. */
        if (file_exists(Yii::getAlias('@app/modules/'.$this->module->id.'/views/backend/'.$this->id.'/delete.php'))) {
            $view = 'delete';
        } else {
            $view = '@app/modules/admin/backend_views/delete';
        }
        return $this->render($view, ['model' => $model, 'errors' => $errors]);

    }

    /* страница копирования записи */
    public function actionDuplicate($id)
    {
        /* дописываем в хлебные крошки текущее действие */
        Yii::$app->view->params["breadcrumbs"][] = ['label' => $this->title];
        /* находим запись по id */
        $model = $this->modelClass::findOne((int) $id);
        /* если запись не найдена - возвращаем ошибку */
        if (empty($model)) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        /* инициализируем массив ошибок */
        $errors = [];

        /* если пришли по POST-запросу */
        if (Yii::$app->request->isPost) {
            /* если запись не удалилась */
            $new_record = $model->duplicate();
            if (!$new_record) {
                /* записать ошибки в формате одноуровневого массива */
                $errors[] = "Невозможно скопировать запись";
            }
        } else {
            /* если пришли по ссылке (не POST-запрос) - допистаь ошибку */
            $errors[] = $this->title." несанкционировано.";
        }
        /* если ошибок не возникло */
        if (empty($errors)) {
            /* если задана текущая страница - возвращаемся на неё */
            $cur_page = (Yii::$app->session->get('page') ? Yii::$app->session->get('page') : null);

            /* переходим на страницу списка элементов */

            return $this->redirect($new_record);
        }
        /* если возникли ошибки - отдаем страницу с описанием */
        /* проверяем, если у контроллера есть собственный view - то отдаем его. Иначе - отдаем общий. */
        if (file_exists(Yii::getAlias('@app/modules/'.$this->module->id.'/views/backend/'.$this->id.'/copy.php'))) {
            $view = 'copy';
        } else {
            $view = '@app/modules/admin/backend_views/copy';
        }
        return $this->render($view, ['model' => $model, 'errors' => $errors]);

    }

    /* страница редактирования поля в таблице GridView */
    public function actionUpdategrid($model)
    {
        $post = Yii::$app->request->post();
        $id = unserialize(base64_decode($post['pk']));
        $model = trim($model, '/');
        $record = $model::findOne((int) $id);
        if ($record) {
            $field = $post['name'];
            $value = $post['value'];
            $record->{$field} = $value;
            if ($record->validate()) {
                $record->updateAttributes([$field => $value]);

                return true;
            }
            $errors = [];
            foreach ($record->errors as $field_error) {
                $errors[] = implode("<br>", $field_error);
            }
            throw new \yii\web\HttpException(406, implode('<br>', $errors));

        } else {
            throw new \yii\web\HttpException(406, 'Запись не найдена');
        }
    }

    /* страница редактирования поля в таблице GridView */
    public function actionUpdatebool()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $get = Yii::$app->request->get();
        if (isset($get['model']) && isset($get['pk']) && isset($get['attribute'])) {
            $model = $get['model']::findOne((int) $get['pk']);
            if ($model) {
                $attribute = $get['attribute'];
                $model->{$attribute} = (!$model->{$attribute});
                if ($model->save()) {
                    return ['status' => 'success', 'new_state' => $model->{$attribute}];
                }
                return ['status' => 'fail', 'message' => 'Невозможно измениь значение'];

            }
            return ['status' => 'fail', 'message' => 'Не найдена запись'];

        }
        return ['status' => 'fail', 'message' => 'Не указаны обязательные параметры'];

        /*$id = unserialize(base64_decode($_POST['pk']));
        $model = trim($model,'/');
        $record = $model::findOne((int)$id);
        if ($record) {
            $field = $_POST['name'];
            $value = $_POST['value'];
            $record->$field = $value;
            if ($record->validate()) {
                $record->updateAttributes([$field => $value]);
                return true;
            } else {
                $errors = [];
                foreach ($record->errors as $field_error) {
                    $errors[] = implode("<br>",$field_error);
                }
                throw new \yii\web\HttpException(406, implode('<br>',$errors));
            }
        } else {
            throw new \yii\web\HttpException(406, 'Запись не найдена');
        }*/

    }

    /* функция возвращает список кнопок массового действия с записями грида */
    /*
    * name - Название кнопки
    * class - html-класс кнопки
    *   Доступные классы:
    *       btn-primary, btn-secondary, btn-success, btn-warning, btn-danger, btn-info, btn-light, btn-dark, btn-link
    *   Для одиночной кнопки:
    *       action - выполняемое действие
    *       params - дополнительный параметр для действия
    *   Для множественной кнопки
    *       list - массив одиночных кнопок
    *           Для одиночных кнопок доступны параметры:
    *               name
    *               action
    *               params
    */

    public function getBulks()
    {
        /* список ссылок для выпадющей кнопки */
        $status_list = [
            ['name' => 'Активировать', 'action' => 'setvisible', 'params' => '1'],
            ['name' => 'Скрыть', 'action' => 'setvisible', 'params' => '0'],
        ];
        $buttons = [
            /* выпадающая кнопка */
            ['name' => 'Отображение', 'list' => $status_list, 'class' => "btn-success"],
            /* одиночная кнопка */
            ['name' => 'Удалить', 'action' => 'remove', 'params' => '', 'class' => "btn-danger"],
        ];

        return $buttons;
    }

    /* Экшен массовых действий */
    /* Экшен вызывается от имени требуемого контроллера, за счет чего заполняются нужные параметры, такие как класс модели. */
    public function actionBulk()
    {
        /* обнуляем список ошибок */
        $errors = [];
        /* если POST-запрос, то продолжаем */
        if (Yii::$app->request->isPost) {
            /* получаем POST-параметры */
            $post = \Yii::$app->request->post();
            /* если передан массив идентификаторов записей */
            if (!empty($post['ids'])) {
                /* выбираем переданное действие из опианных */
                switch ($post['action']) {
                    /* установка активноти элементов */
                    case "setvisible":
                        /* перебираем все полученные идентификаторы */
                        foreach ($post['ids'] as $item_id) {
                            /* если запись с переданным id найдена по классу, заданному для текущего действия текущего контроллера, то продолжаем */
                            if ($model = $this->modelClass::findOne((int) $item_id)) {
                                /* устанавливаем активность в соответствии с переданным параметром */
                                $model->visible = (int) $post['params'];
                                /* если сохранить модель невозможно */
                                if (!$model->save()) {
                                    /* записать ошибки в формате одноуровневого массива */
                                    $errors[] = "При изменении ".$model->getNameForView()." возникли ошибки: ";
                                    foreach ($model->errors as $field_error) {
                                        $errors[] = implode("<br>", $field_error);
                                    }
                                }
                            } else {
                                /* если запись не найдена - добавляем ошибку */
                                $errors[] = "Запись с ID=".$item_id." не найдена.";
                            }
                        }
                        break;
                        /* копирование записей */
                    case "duplicate":
                        /* перебираем все полученные идентификаторы */
                        foreach ($post['ids'] as $item_id) {
                            /* если запись с переданным id найдена по классу, заданному для текущего действия текущего контроллера, то продолжаем */
                            if ($model = $this->modelClass::findOne((int) $item_id)) {
                                /* загружаем модель спомощью заданного класса */
                                if ($model->hasAttribute('model')) {
                                    $className = $model->model;
                                    $model = $className::findOne($model->id);
                                }
                                /* если удалить запись невозможно */
                                if (!$model->duplicate()) {
                                    /* записать ошибки в формате одноуровневого массива */
                                    $errors[] = "Невозможно скопировать ".$model->getNameForView();
                                }
                            } else {
                                /* если запись не найдена - добавляем ошибку */
                                $errors[] = "Запись с ID=".$item_id." не найдена.";
                            }
                        }
                        break;

                        /* удаление записей */
                    case "remove":
                        /* перебираем все полученные идентификаторы */
                        foreach ($post['ids'] as $item_id) {
                            /* если запись с переданным id найдена по классу, заданному для текущего действия текущего контроллера, то продолжаем */
                            if ($model = $this->modelClass::findOne((int) $item_id)) {
                                /* загружаем модель спомощью заданного класса */
                                if ($model->hasAttribute('model')) {
                                    $className = $model->model;
                                    $model = $className::findOne($model->id);
                                }
                                /* если удалить запись невозможно */
                                if (!$model->delete()) {
                                    /* записать ошибки в формате одноуровневого массива */
                                    $errors[] = "Невозможно удалить ".$model->getNameForView();
                                    foreach ($model->errors as $field_error) {
                                        $errors[] = implode("<br>", $field_error);
                                    }
                                }
                            } else {
                                /* если запись не найдена - добавляем ошибку */
                                $errors[] = "Запись с ID=".$item_id." не найдена.";
                            }
                        }
                        break;

                    default:
                        /* если действие не описано - добавляем ошибку */
                        $errors[] = "Действие, которое вы пытаетесь совершить не описано.";
                }
            } else {
                /* если массив идентификаторов записей пуст - добавляем ошибку */
                $errors[] = "Список записей для обработки пуст.";
            }
        } else {
            /* если это НЕ POST-запрос - добавляем ошибку */
            $errors[] = "Действия несанкционированы.";
        }
        if (empty($errors)) {
            /* если ошибок не было - возвращаем результат */
            $result = [
                'status' => 'success',
                'message' => 'Действия успешно выполнены',
                'title' => $this->module->params['name'],
            ];
        } else {
            /* если были ошибки - возвращаем результат */
            $result = ['status' => 'fail', 'errors' => $errors, 'title' => $this->module->params['name']];
        }

        /* возвращаем результат */

        return json_encode($result);
    }

    /* Экшен для удаления файла */
    public function actionDeletefile()
    {
        /* задаем заголовок страницы */
        $this->title = 'Удаление файла';
        /* задаем имя класса основной модели */
        $this->modelClass = '\app\modules\admin\components\FilestoreModel';
        /* инициализация массива ошибок */
        $errors = [];
        /* если запрос на удаление файла - POST */
        if (Yii::$app->request->isPost) {
            /* получаем параметры запроса */
            $post = \Yii::$app->request->post();
            /* если передаен идентификатор файла */
            if (!empty($post['id'])) {
                /* выбираем действие из описанных */
                switch ($post['action']) {
                    /* удаление файла */
                    case "remove":
                        /* если запись с переданным id найдена по классу, заданному для текущего действия текущего контроллера, то продолжаем */
                        if ($model = $this->modelClass::findOne((int) $post['id'])) {
                            /* если файл нельзя удалить */
                            if (!$model->delete()) {
                                /* записать ошибки в формате одноуровневого массива */
                                $errors[] = "Невозможно удалить ".$model->name;
                                foreach ($model->errors as $field_error) {
                                    $errors[] = implode("<br>", $field_error);
                                }
                            }
                        } else {
                            /* если запись не найдена - добавляем ошибку */
                            $errors[] = "Запись с ID=".$post['id']." не найдена.";
                        }
                        break;
                    case "edit":
                        $this->title = 'Редактирование файла';
                        /* если запись с переданным id найдена по классу, заданному для текущего действия текущего контроллера, то продолжаем */
                        if ($model = $this->modelClass::findOne((int) $post['id'])) {
                            /* если файл нельзя отредактировать */
                            if (!$model->updateAttributes([$post['field'] => $post['value']])) {
                                /* записать ошибки в формате одноуровневого массива */
                                $errors[] = "Невозможно изменить ".$model->name;
                                foreach ($model->errors as $field_error) {
                                    $errors[] = implode("<br>", $field_error);
                                }
                            }
                        } else {
                            /* если запись не найдена - добавляем ошибку */
                            $errors[] = "Запись с ID=".$post['id']." не найдена.";
                        }
                        break;
                    default:
                        /* если действие не описано - добавляем ошибку */
                        $errors[] = "Действие, которое вы пытаетесь совершить не описано.";
                }
            } else {
                /* если не передан идентификатор файла - добавляем ошибку */
                $errors[] = "Список записей для обработки пуст.";
            }
        } else {
            /* если запрос на удаление файла не POST - добавляем ошибку */
            $errors[] = "Действия несанкционированы.";
        }
        if (empty($errors)) {
            /* если ошибок не было - возвращаем результат */
            $result = ['status' => 'success', 'message' => 'Действия успешно выполнены', 'title' => 'Файлы'];
        } else {
            /* если были ошибки - возвращаем результат */
            $result = ['status' => 'fail', 'errors' => $errors, 'title' => 'Файлы'];
        }

        /* возвращаем результат */

        return json_encode($result);
    }

    /* Экшен для отправки уведомлений */
    public function actionSendmessage()
    {
        /* задаем заголовок страницы */
        $this->title = 'Отправка уведомления';
        /* задаем имя класса основной модели */
        $this->modelClass = '\app\modules\users\models\UserAR';
        /* инициализация массива ошибок */
        $errors = [];
        /* если запрос на отправку уведомления - POST */
        if (Yii::$app->request->isPost) {
            /* получаем параметры запроса */
            $post = \Yii::$app->request->post();
            /* если передаен идентификатор пользователя */
            $message = 'Уведомление об активации аккаунта отправлено';
            if (!empty($post['id']) || !empty($post['user_direction_id'])) {
                /* выбираем тип из описанных */
                switch ($post['type']) {
                    case "first_login_letter":
                        /* если запись с переданным id найдена то продолжаем */
                        if ($model = $this->modelClass::findOne((int) $post['id'])) {
                            /* если пользователь активирован */
                            if (empty($model->self_registered) and empty($model->last_login)) {
                                $model->sendFirstLoginEmail();
                                $message = 'Письмо со ссылкой на первый вход отправлено';
                            } else {
                                $errors[] = "Отправить письмо со ссылкой на первый вход можно только для пользователя зарегистрированного вручную и ни разу не логинившегося на сайте";
                            }
                        } else {
                            /* если запись не найдена - добавляем ошибку */
                            $errors[] = "Пользователь с ID=".$post['id']." не найден.";
                        }
                        break;
                    case "activate_letter":
                        if ($model = $this->modelClass::findOne((int) $post['id'])) {
                            /* если пользователь активирован */
                            if ($model->self_registered and ($model->status == \app\modules\users\models\UserAR::STATUS_INACTIVE)) {
                                if ($model->sendActivateEmail()) {
                                    $message = 'Письмо со ссылкой на активацию аккаунта отправлено';
                                } else {
                                    $errors[] = "Отправить письмо со ссылкой на активацию можно не чаще чем раз в 3 часа.";
                                }
                            } else {
                                $errors[] = "Отправить письмо со ссылкой на активацию можно только для пользователя зарегистрированного самостоятельно и не активировавшего свой аккаунт";
                            }
                        } else {
                            /* если запись не найдена - добавляем ошибку */
                            $errors[] = "Пользователь с ID=".$post['id']." не найден.";
                        }
                        break;
                    case "generate_sert":
                        $this->modelClass = UserDirection::class;

                        /* если запись с переданным id найдена, то продолжаем */
                        if ($model = $this->modelClass::findOne($post['user_direction_id'])) {
                            /* если пользователь активирован */
                            $sert_num = $model->generateSert();
                            if ($sert_num) {
                                // $model->sendFirstLoginEmail();
                                $message = 'Для пользователя сгенерирован номер сертификата '.$sert_num;
                            } else {
                                $errors[] = "Невозможно сгенерировать сертификат";
                            }
                        } else {
                            /* если запись не найдена - добавляем ошибку */
                            $errors[] = "Связка пользователя и кафедры с ID=".$post['user_direction_id']." не найден.";
                        }
                        break;
                    case "regenerate_sert":
                        $this->modelClass = UserDirection::class;

                        /* если запись с переданным id найдена, то продолжаем */
                        if ($model = $this->modelClass::findOne($post['user_direction_id'])) {
                            /* если пользователь активирован */
                            $sert_num = $model->generateSert(true);
                            if ($sert_num) {
                                // $model->sendFirstLoginEmail();
                                $message = 'Для пользователя сгенерирован номер сертификата '.$sert_num;
                            } else {
                                $errors[] = "Невозможно сгенерировать сертификат";
                            }
                        } else {
                            /* если запись не найдена - добавляем ошибку */
                            $errors[] = "Связка пользователя и кафедры с ID=".$post['user_direction_id']." не найдена.";
                        }
                        break;
                    default:
                        /* если действие не описано - добавляем ошибку */
                        $errors[] = "Действие, которое вы пытаетесь совершить не описано.";
                }
            } else {
                /* если не передан идентификатор файла - добавляем ошибку */
                $errors[] = "Список записей для обработки пуст.";
            }
        } else {
            /* если запрос на удаление файла не POST - добавляем ошибку */
            $errors[] = "Действия несанкционированы.";
        }
        if (empty($errors)) {
            /* если ошибок не было - возвращаем результат */
            $result = ['status' => 'success', 'message' => $message, 'title' => 'Уведомление'];
        } else {
            /* если были ошибки - возвращаем результат */
            $result = ['status' => 'fail', 'errors' => $errors, 'title' => 'Уведомление'];
        }

        /* возвращаем результат */

        return json_encode($result);
    }
}
