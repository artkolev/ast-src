<?php

namespace app\modules\pages\controllers\frontend;

use app\components\DeepController;
use app\helpers\DBHelper;
use app\helpers\MainHelper;
use app\modules\cases\models\Cases;
use app\modules\direction\models\Direction;
use app\modules\eduprog\models\Eduprog;
use app\modules\eduprogorder\models\Eduprogorder;
use app\modules\events\models\Events;
use app\modules\keywords\models\Keyword;
use app\modules\lenta\models\Blog;
use app\modules\lenta\models\Lenta;
use app\modules\lenta\models\LentaInnerExpert;
use app\modules\lenta\models\LentaInnerService;
use app\modules\lenta\models\Material;
use app\modules\lenta\models\News;
use app\modules\lenta\models\Project;
use app\modules\pages\models\AcademyCatalog;
use app\modules\pages\models\Directs;
use app\modules\pages\models\EduprogPage;
use app\modules\pages\models\Eventspage;
use app\modules\pages\models\ExpertCatalog;
use app\modules\pages\models\ExporgCatalog;
use app\modules\pages\models\FilterPage;
use app\modules\pages\models\LentaBlogpage;
use app\modules\pages\models\LentaMaterialpage;
use app\modules\pages\models\LentaNewspage;
use app\modules\pages\models\Lentapage;
use app\modules\pages\models\LentaProjectpage;
use app\modules\pages\models\Login;
use app\modules\pages\models\ServiceTypePage;
use app\modules\pages\models\TargetAudiencePage;
use app\modules\pages\models\TargetAudiencePageOld;
use app\modules\reference\models\City;
use app\modules\reference\models\Competence;
use app\modules\reference\models\Educategory;
use app\modules\reference\models\Eventsformat;
use app\modules\reference\models\Eventstag;
use app\modules\reference\models\Lentatag;
use app\modules\reference\models\Solvtask;
use app\modules\service\models\Service;
use app\modules\service_type\models\ServiceType;
use app\modules\target_audience\models\TargetAudience;
use app\modules\users\models\UserAR;
use app\modules\users\models\UserExpert;
use Yii;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Response;

class PagesController extends DeepController
{
    public function actionIndex($model)
    {
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model]);
    }

    public function actionPage($model)
    {
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model]);
    }

    public function actionAbout($model)
    {
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model]);
    }

    public function actionAboutUs($model)
    {
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model]);
    }

    public function actionIndividuals($model)
    {
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model]);
    }

    /* страницы каталогов пользователей */
    public function actionAll($model)
    {
        return $this->actionUsersCatalog($model, []);
    }

    public function actionUsersCatalog($model, $roles)
    {
        // задать заголовок страницы
        $this->setMeta($model);
        // итоговые параметры для поиска/фильтрации
        $terms = [
            // решаемые задачи
            'task' => [],
            // специализации
            'competence' => [],
            // группы услуг
            'servgroup' => [],
            // кафедры
            'directs' => [],
            // формат оказания услуг
            'service_type' => [],
            // цена услуг
            'price' => [],
            // город
            'city' => [],
            // текстовый поиск
            'search' => '',
        ];
        // запрос на выборку пользователей
        $experts = UserExpert::find();
        $experts
            ->leftJoin('user_direction', 'user_direction.user_id=user.id')
            ->andFilterWhere(['user_direction.role' => $roles])
            ->visible(['expert', 'exporg']);

        // параметры (поиск/фильтрация)
        $get = Yii::$app->request->get();
        // если пришли по фильтрам/поиску
        if (!empty($get)) {
            // список пользователей, которых требуется добавить в выборку
            $users_ids = [];
            // Заполняем параметры для поиска по пользователям
            // перебираем полученные параметры-массивы
            foreach ($get as $key => $value) {
                // если не входит в обрабатываемый список параметров, или не массив - пропускаем
                if ((!in_array($key, ['task', 'competence', 'servgroup', 'directs', 'city', 'service_type'])) or !is_array($value)) {
                    continue;
                }
                // убрать пустые значение в массиве
                $value = array_diff($value, ['']);
                // если пустой массив - пропускаем
                if (empty($value)) {
                    continue;
                }
                $terms[$key] = $value;
            }
            // поиск по стоимости услуг
            if (!empty($get['price_from'])) {
                $terms['price']['from'] = (int)$get['price_from'];
            }
            if (!empty($get['price_to'])) {
                $terms['price']['to'] = (int)$get['price_to'];
            }
            // текстовый поиск
            if (!empty($get['query'])) {
                $terms['search'] = htmlspecialchars($get['query']);
            }

            // ключевые слова
            $keywordName = (isset($get['keyword']) ? $get['keyword'] : false);
            if ($keywordName) {
                $keyword = Keyword::find()->where(['name' => $keywordName])->one();
                if ($keyword) {
                    $experts->leftJoin('models_keywords', 'models_keywords.entity_id = user.id');
                    $experts->andWhere(['models_keywords.keyword_id' => $keyword->id]);
                    $experts->andWhere(['models_keywords.entity_model' => UserAR::class]);
                }
            }

            // ищем идентификаторы пользователей - владельцев специализаций, подходящих по фильтрам
            if (!empty($terms['competence'])) {

                $user_ids_competence = (new \yii\db\Query())->select(['DISTINCT(`user_id`)'])->from('user_ref_competence')->where(['IN', 'competence_id', $terms['competence']])->column();
                /* если итоговый массив еще не проинициализирован - то просто приравниваем его к найденному массиву, иначе находим пересечение множеств (заменяет оператор AND в запросе к базе) */
                $users_ids = (empty($users_ids)) ? $user_ids_competence : array_intersect($users_ids, $user_ids_competence);
            }

            // ищем идентификаторы пользователей - владельцев решаемых задач, подходящих по фильтрам
            if (!empty($terms['task'])) {
                $user_ids_solvtask = (new \yii\db\Query())->select(['DISTINCT(`user_id`)'])->from('user_ref_solvtask')->where(['IN', 'solvtask_id', $terms['task']])->column();
                /* если итоговый массив еще не проинициализирован - то просто приравниваем его к найденному массиву, иначе находим пересечение множеств (заменяет оператор AND в запросе к базе) */
                $users_ids = (empty($users_ids)) ? $user_ids_solvtask : array_intersect($users_ids, $user_ids_solvtask);
            }

            // ищем идентификаторы пользователей - по заданным кафедрам (основным и дополнительным)
            if (!empty($terms['directs'])) {
                $user_ids_direction = (new \yii\db\Query())->select(['DISTINCT(`user_id`)'])->from('user_direction')->where(['IN', 'direction_id', $terms['directs']])->andFilterWhere(['role' => $roles])->andWhere(['visible' => 1])->column();
                /* если итоговый массив еще не проинициализирован - то просто приравниваем его к найденному массиву, иначе находим пересечение множеств (заменяет оператор AND в запросе к базе) */
                $users_ids = (empty($users_ids)) ? $user_ids_direction : array_intersect($users_ids, $user_ids_direction);
            }

            // ищем по услугам по фильтрам (по типам услуг, формату оказания, стоимости)
            if (!empty($terms['servgroup']) or !empty($terms['price']) or !empty($terms['service_type'])) {
                $services_query = Service::findVisible()->select('DISTINCT (user_id)');
                if (!empty($terms['servgroup'])) {
                    $services_query->andWhere(['IN', 'type_id', $terms['servgroup']]);
                }
                if (!empty($terms['price'])) {
                    $criteria_price = ['AND', ['type' => 0]];
                    if (!empty($terms['price']['from'])) {
                        $criteria_price[] = ['>=', 'price', (int)$terms['price']['from']];
                    }
                    if (!empty($terms['price']['to'])) {
                        $criteria_price[] = ['<=', 'price', (int)$terms['price']['to']];
                    }
                    $services_query->andWhere($criteria_price);
                }
                if (!empty($terms['service_type'])) {
                    $services_query->andWhere(['IN', 'kind', $terms['service_type']]);
                }
                $user_ids_services = $services_query->asArray()->column();
                /* если итоговый массив еще не проинициализирован - то просто приравниваем его к найденному массиву, иначе находим пересечение множеств (заменяет оператор AND в запросе к базе) */
                $users_ids = (empty($users_ids)) ? $user_ids_services : array_intersect($users_ids, $user_ids_services);
            }

            // если есть ограничения из фильтров - добавляем в запрос
            if (!empty($users_ids)) {
                $experts->andWhere(['IN', 'user.id', $users_ids]);
            }

            // таблица профиля тяжелая, поэтому не будем подключать её несколько раз.
            $has_joined_profile = false;
            // ищем по городу
            if (!empty($terms['city'])) {
                if (!$has_joined_profile) {
                    $has_joined_profile = true;
                    $experts->leftJoin('profile', 'profile.user_id = user.id');
                }
                $experts->andWhere(['IN', 'profile.city_id', $terms['city']]);
            }
            // ищем пользователей по поисковому запросу
            if (!empty($terms['search'])) {
                // специализации из поиска
                $competence_ids = ArrayHelper::map(Competence::find()->where(['LIKE', 'name', $terms['search']])->andWhere(['visible' => 1])->all(), 'id', 'id');

                $user_ids_search_competence = (new \yii\db\Query())->select(['DISTINCT(`user_id`)'])->from('user_ref_competence')->where(['IN', 'competence_id', $competence_ids])->column();

                // услуги из поиска
                $user_ids_search_services = Service::findVisible()->select('DISTINCT (user_id)')->leftJoin('service_type', 'service_type.id = service.type_id')->andWhere(['OR',
                    ['LIKE', 'service.name', $terms['search']],
                    ['LIKE', 'service.description', $terms['search']],
                    ['LIKE', 'service_type.name', $terms['search']],
                ])->asArray()->column();
                // объединяем найденные группы пользователей (заменяет оператор OR в поисковом запросе)
                $user_ids_search = array_keys(array_flip($user_ids_search_competence) + array_flip($user_ids_search_services));

                // добавляем в поисковый запрос
                if (!$has_joined_profile) {
                    $has_joined_profile = true;
                    $experts->leftJoin('profile', 'profile.user_id = user.id');
                }
                // общие условия для всех ролей
                $or_condition = [
                    ['IN', 'user.id', $user_ids_search],
                    ['LIKE', 'profile.about_myself', $terms['search']],
                ];
                // для экспертов и академиков ищем по ФИ
                if (in_array('expert', $roles) or in_array('academ', $roles) or $roles == []) {
                    $or_condition[] = ['LIKE', 'CONCAT(`profile`.`surname`," ",`profile`.`name`)', $terms['search']];
                    $or_condition[] = ['LIKE', 'CONCAT(`profile`.`name`," ",`profile`.`surname`)', $terms['search']];
                }
                // для эо ищем по названию организации
                if (in_array('exporg', $roles) or $roles == []) {
                    $experts->leftJoin('organization', 'organization.user_id = user.id');
                    $or_condition[] = ['LIKE', 'organization.organization_name', $terms['search']];
                }
                array_unshift($or_condition, 'OR');
                $experts->andWhere($or_condition);
            }
        }

        // пагинация
        $countQuery = clone $experts;
        $count_experts = $countQuery->count();

        $pageparams = Yii::$app->request->get();
        unset($pageparams['model']);

        $pages = new Pagination([
            'totalCount' => $count_experts,
            'defaultPageSize' => 25,
            'route' => $model->getUrlPath(),
            'params' => $pageparams,
        ]);

        $experts = $experts->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        shuffle($experts);

        // если ajax-запрос
        if (Yii::$app->request->isAjax) {
            // задаем формат ответа
            Yii::$app->response->format = Response::FORMAT_JSON;
            // инициализируем массив ответа
            $data = ['status' => 'fail'];
            // если пользователи найдены
            if (!empty($experts)) {
                // рендерим
                $data['count'] = 'Показать ' . $count_experts . ' ' . MainHelper::pluralForm($count_experts, ['предложение', 'предложения', 'предложений']);
                $data['html'] = $this->renderPartial('_expert_box', ['items' => $experts]);
                // говорим, что все ОК
                $data['status'] = 'success';
            }
            $data['pager'] = \app\widgets\pagination\LinkPager::widget(['pages' => $pages, 'is_ajax' => true]);
            // возвращаем ответ
            return $data;
        }
        // получаем список кафедр
        $directions = Direction::find()
            ->andWhere(['visible' => 1, 'stels_direct' => 0])
            ->andWhere([
                'IN', 'id', DBHelper::getAffectedByUsersIds('user_direction', 'direction_id', $roles),
            ])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        // получаем список решаемых задач
        $selected_tasks = Solvtask::find()->where(['visible' => 1])->andWhere(['IN', 'id', $terms['task']])->orderBy(['name' => SORT_ASC])->all();

        // получаем список специализаций и компетенций
        $selected_competences = Competence::find()->where(['visible' => 1])->andWhere(['IN', 'id', $terms['competence']])->orderBy(['name' => SORT_ASC])->all();

        // получаем список городов
        $cities_short = City::find()->where(['visible' => 1])->andWhere(['IN', 'id', DBHelper::getAffectedByUsersIds('profile', 'city_id', $roles)])->andWhere(['OR', ['big_city' => 1], ['IN', 'id', $terms['city']]])->orderBy(['big_city' => SORT_DESC, 'name' => SORT_ASC])->all();

        // получаем список типов услуг
        $selected_servgroups = ServiceType::find()->where(['visible' => 1])->andWhere(['IN', 'id', $terms['servgroup']])->orderBy(['name' => SORT_ASC])->all();

        $filter_page = FilterPage::find()->where(['model' => FilterPage::class, 'visible' => 1])->one();

        $filter_roles = implode(',', $roles);

        // рендерим страницу
        return $this->render($model->view, [
            'model' => $model,
            'items' => $experts,
            'directions' => $directions,
            'tasks' => $selected_tasks,
            'competences' => $selected_competences,
            'servgroups' => $selected_servgroups,
            'terms' => $terms,
            'cities_short' => $cities_short,
            'filter_page' => $filter_page,
            'filter_roles' => $filter_roles,
            'pages' => $pages,
        ]);
    }

    public function actionAcademy($model)
    {
        return $this->actionUsersCatalog($model, ['academ']);
    }

    public function actionExpert($model)
    {
        return $this->actionUsersCatalog($model, ['expert']);
    }

    public function actionExporg($model)
    {
        return $this->actionUsersCatalog($model, ['exporg']);
    }

    public function actionUser($model)
    {
        /** @var UserAR $model */
        $this->setMeta($model);

        $pageSizeCards = 6;
        $pageSizeLandscape = 3;

        // услуги пользователя
        $userServices = Service::findVisible()->andWhere(['user_id' => $model->id]);

        // разбивка на страницы
        // услуги
        [$services, $servicesPages] = $this->expertPagePaginations($model, $userServices, 'services', $pageSizeLandscape);
        // мероприятия
        [$events, $eventsPages] = $this->expertPagePaginations($model, $model->getEventsCatalog(), 'events', $pageSizeCards);
        // образовательные программы
        [$eduprogs, $eduprogsPages] = $this->expertPagePaginations($model, Eduprog::findVisible()->andWhere(['author_id' => $model->id])->orderBy(['registration_open' => SORT_DESC, 'date_stop' => SORT_ASC]), 'eduprogs', $pageSizeLandscape);
        // блоги
        [$blogs, $blogsPages] = $this->expertPagePaginations($model, $model->getBlogsCatalog(), 'blogs', $pageSizeCards);
        // База знаний
        [$materials, $materialsPages] = $this->expertPagePaginations($model, $model->getMaterialsCatalog(), 'materials', $pageSizeCards);
        // Новости
        [$news, $newsPages] = $this->expertPagePaginations($model, $model->getNewsCatalog(), 'news', $pageSizeCards);
        // Портфолио
        [$portfolio, $portfolioPages] = $this->expertPagePaginations($model, $model->getProjectsCatalog(), 'portfolio', $pageSizeCards);

        // если ajax-запрос
        if (Yii::$app->request->isAjax) {
            // задаем формат ответа
            Yii::$app->response->format = Response::FORMAT_JSON;
            // получаем идентификатор блока
            $pathId = Yii::$app->request->get('pathId');
            // инициализируем массив ответа
            $ajaxData = ['status' => 'fail'];
            // проверяем входит ли наш идентификатор в массив возможных идентификаторов
            if (in_array($pathId, ['blogs', 'materials', 'news', 'portfolio', 'events', 'services', 'eduprogs'])) {
                $elements = ${$pathId};
                if (!empty($elements)) {
                    $template = in_array($pathId, ['blogs', 'materials', 'news', 'portfolio']) ? 'catalog' : $pathId;
                    // рендерим
                    $ajaxData['html'] = $this->renderPartial('user/_' . $template . '_item', ['items' => $elements]);
                    // говорим, что все ОК
                    $ajaxData['status'] = 'success';
                    // генерируем кнопку
                    $pagination = ${$pathId . 'Pages'};
                    $pages = $pagination->getLinks();
                    $ajaxData['pager'] = '<a href="' . $pages['next'] . '" data-block="' . $pathId . '" class="button long w100 mt20 expert_show_more_btn">Загрузить еще</a>';
                    // убираем кнопку если страницы кончились
                    if (!isset($pages['next'])) {
                        $ajaxData['pager'] = null;
                    }
                }
            }

            return $ajaxData;
        }

        $catalog = $model->role == 'exporg' ?
            ExporgCatalog::find()->where(['model' => ExporgCatalog::class, 'visible' => 1])->one() :
            ExpertCatalog::find()->where(['model' => ExpertCatalog::class, 'visible' => 1])->one();

        return $this->render(
            'expert_exporg',
            [
                'user' => $model,
                'events' => $events,
                'eventsPages' => $eventsPages,
                'services' => $services,
                'servicesPages' => $servicesPages,
                'eduprogs' => $eduprogs,
                'eduprogsPages' => $eduprogsPages,
                'blogs' => $blogs,
                'blogsPages' => $blogsPages,
                'materials' => $materials,
                'materialsPages' => $materialsPages,
                'news' => $news,
                'newsPages' => $newsPages,
                'portfolio' => $portfolio,
                'portfolioPages' => $portfolioPages,
                'catalog' => $catalog,
                'userRole' => $model->role,
            ]
        );


    }

    private function expertPagePaginations($model, ActiveQuery $elementsQuery, string $ident, int $pageSize = 6): array
    {
        // пагинация
        $countQuery = clone $elementsQuery;
        $countElements = $countQuery->count();

        $pageparams = Yii::$app->request->get();
        unset($pageparams['model'], $pageparams['pathId']);

        $pages = new Pagination([
            'totalCount' => $countElements,
            'defaultPageSize' => $pageSize,
            'route' => $model->getUrlPath(),
            'params' => $pageparams,
            'pageParam' => $ident,
        ]);

        $elements = $elementsQuery->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return [$elements, $pages];
    }

    public function actionDirects($model)
    {
        $this->setMeta($model);
        $directs = Direction::find()->where(['visible' => 1, 'stels_direct' => 0])->orderBy(['name' => SORT_ASC])->all();
        return $this->render($model->view, ['model' => $model, 'directs' => $directs]);
    }

    public function actionDirection($model)
    {
        $this->setMeta($model);
        $parent = Directs::find()->where(['start_module' => 'direction', 'visible' => '1'])->one();
        $academ_catalog = AcademyCatalog::find()->where(['model' => AcademyCatalog::class, 'visible' => 1])->one();
        $expert_catalog = ExpertCatalog::find()->where(['model' => ExpertCatalog::class, 'visible' => 1])->one();
        $exporg_catalog = ExporgCatalog::find()->where(['model' => ExporgCatalog::class, 'visible' => 1])->one();

        $events_catalog = Eventspage::find()->where(['model' => Eventspage::class, 'visible' => 1])->one();
        // мероприятия, принадлежащие направлению
        $events_actual = Events::findVisibleForCatalog()
            ->leftJoin('eventsform', 'eventsform.event_id = events.id')
            ->leftJoin('tariff', 'tariff.event_form_id = eventsform.id')
            // актуальный тариф
            ->andWhere(['tariff.visible' => 1])->andWhere('(tariff.start_publish IS NULL) OR (tariff.start_publish < NOW())')->andWhere('(tariff.end_publish IS NULL) OR (tariff.end_publish > NOW())')
            // мероприятие с регистрацией
            ->andWhere(['events.need_tariff' => 1])
            // есть доступные билеты
            ->andWhere(['>', 'tariff.remainTickets', 0])
            // форма тарифа платная и активная
            ->andWhere(['eventsform.visible' => 1]) // / попросили отображть не только платные, 'eventsform.payregister' => 1])
            // не является отмененным
            ->andWhere(['<>', 'events.status', Events::STATUS_CANCELLED])
            ->andWhere(['direction_id' => $model->id, 'show_in_dir' => 1])->orderBy(['event_date' => SORT_ASC])->limit(16)->all();

        // завершившиеся мероприятия
        $events_old = Events::findVisible()
            ->andWhere('events.event_date_end < CURDATE()')
            ->andWhere(['direction_id' => $model->id, 'show_in_dir' => 1])
            ->andWhere(['<>', 'events.status', Events::STATUS_CANCELLED])
            ->orderBy(['event_date' => SORT_ASC])
            ->limit(16 - count($events_actual))
            ->all();
        $events = array_merge($events_actual, $events_old);

        $blog_catalog = LentaBlogpage::find()->where(['model' => LentaBlogpage::class, 'visible' => 1])->one();
        $blogs_with_experts_ids = ArrayHelper::map(
            LentaInnerExpert::find()->leftJoin('user', 'user.id = lentainnerexpert.expert_id')->leftJoin('user_direction', 'user.id = user_direction.user_id')
                ->where(['OR',
                    ['user_direction.direction_id' => $model->id],
                    ['user_direction.main_direction' => $model->id],
                ])
                ->andWhere(['lentainnerexpert.visible' => 1])
                ->andWhere(['user.status' => UserAR::STATUS_ACTIVE])
                ->andWhere(['user.visible' => 1])
                ->all(),
            'page_id',
            'page_id'
        );
        $blogs = Blog::findVisible()->andWhere(
            (['OR',
                ['direction_id' => $model->id, 'show_in_dir' => 1],
                ['IN', 'lenta.id', $blogs_with_experts_ids],
            ])
        )->published()->orderBy(['created_at' => SORT_DESC])->limit(16)->all();

        $project_catalog = LentaProjectpage::find()->where(['model' => LentaProjectpage::class, 'visible' => 1])->one();
        $projects = Project::findVisible()->andWhere(['direction_id' => $model->id, 'show_in_dir' => 1])->andWhere('(start_publish IS NULL) OR (start_publish < NOW())')->andWhere('(end_publish IS NULL) OR (end_publish > NOW())')->orderBy(['published' => SORT_DESC])->limit(16)->all();

        $material_catalog = LentaMaterialpage::find()->where(['model' => LentaMaterialpage::class, 'visible' => 1])->one();
        $materials = Material::findVisible()->andWhere(['direction_id' => $model->id, 'show_in_dir' => 1])->andWhere('(start_publish IS NULL) OR (start_publish < NOW())')->andWhere('(end_publish IS NULL) OR (end_publish > NOW())')->orderBy(['published' => SORT_DESC])->limit(16)->all();

        $service_page = ServiceTypePage::find()->where(['model' => ServiceTypePage::class, 'visible' => 1])->one();
        $services_experts_ids = ArrayHelper::map(
            UserAR::find()->leftJoin('user_direction', 'user.id = user_direction.user_id')
                ->where(['OR',
                    ['user_direction.direction_id' => $model->id],
                    ['user_direction.main_direction' => $model->id],
                ])
                ->visible()
                ->all(),
            'id',
            'id'
        );
        $services = Service::findVisible()->andFilterWhere(['IN', 'service.user_id', $services_experts_ids])->orderBy(new Expression('rand()'))->limit(16)->all();

        return $this->render($model->view, [
            'model' => $model,
            'parent' => $parent,
            'academ_catalog' => $academ_catalog,
            'expert_catalog' => $expert_catalog,
            'exporg_catalog' => $exporg_catalog,
            'events_catalog' => $events_catalog,
            'blog_catalog' => $blog_catalog,
            'project_catalog' => $project_catalog,
            'material_catalog' => $material_catalog,
            'events' => $events,
            'blogs' => $blogs,
            'projects' => $projects,
            'materials' => $materials,
            'service_page' => $service_page,
            'services' => $services,
        ]);
    }

    public function actionEvents($model)
    {
        // устанавливаем мете-данные для страницы из модели мероприятия
        $this->setMeta($model);
        // параметры запроса (для фильтрации)
        $get = Yii::$app->request->get();
        $search_query = trim(Yii::$app->request->get('q'));

        // при выборе Мероприятия Академии + Открыта регистрация скрывать завершённые (задача 9470)
        if (Yii::$app->request->get('is_ast', 0) && Yii::$app->request->get('registration_open', 0)) {
            $items_query = Events::findVisibleForCatalog();
        } else {
            $items_query = Events::findVisible();
        }

        // заполнение/обнуление фильтров
        // теги
        $tag = (isset($get['tag']) ? $get['tag'] : false);
        // флаги о джойне таблиц
        $has_tag_table = false;
        $has_direction_table = false;

        if ($tag) {
            $events_tag = Eventstag::find()->where(['name' => $tag])->one();
            if ($events_tag) {
                $items_query->leftJoin('events_ref_eventstag', 'events_ref_eventstag.events_id = events.id');
                $items_query->andWhere(['events_ref_eventstag.eventstag_id' => $events_tag->id]);
                $has_tag_table = true;
            }
        }

        // ключевые слова
        $keywordName = (isset($get['keyword']) ? $get['keyword'] : false);
        if ($keywordName) {
            $keyword = Keyword::find()->where(['name' => $keywordName])->one();
            if ($keyword) {
                $items_query->leftJoin('models_keywords', 'models_keywords.entity_id = events.id');
                $items_query->andWhere(['models_keywords.keyword_id' => $keyword->id]);
                $items_query->andWhere(['models_keywords.entity_model' => Events::class]);
            }
        }

        if (!empty($search_query)) {
            if (!$has_tag_table) {
                $items_query->leftJoin('events_ref_eventstag', 'events_ref_eventstag.events_id = events.id');
                $has_tag_table = true;
            }
            if (!$has_direction_table) {
                $items_query->leftJoin('events_ref_direction', 'events_ref_direction.events_id = events.id');
                $has_direction_table = true;
            }

            $tags_search = ArrayHelper::map(Eventstag::find()->where(['LIKE', 'name', $search_query])->all(), 'id', 'id');
            $direct_search = ArrayHelper::map(Direction::find()->where(['LIKE', 'name', $search_query])->all(), 'id', 'id');
            $city_search = ArrayHelper::map(City::find()->where(['LIKE', 'name', $search_query])->all(), 'id', 'id');
            $eventsformat_search = ArrayHelper::map(Eventsformat::find()->where(['LIKE', 'name', $search_query])->all(), 'id', 'id');

            $query = UserAR::find();
            $roles = ['expert'];
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id');
            $query->leftJoin('profile', 'profile.user_id = user.id');
            $query->andWhere(['IN', 'auth_assignment.item_name', $roles]);
            $query->andWhere(['status' => UserAR::STATUS_ACTIVE]);
            $query->andWhere(['OR',
                ['LIKE', 'CONCAT(`profile`.`surname`," ",`profile`.`name`)', $search_query],
                ['LIKE', 'CONCAT(`profile`.`name`," ",`profile`.`surname`)', $search_query],
            ]);
            $users_search = ArrayHelper::map($query->all(), 'id', 'id');

            $query = UserAR::find();
            $roles = ['exporg'];
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id');
            $query->leftJoin('profile', 'profile.user_id = user.id');
            $query->andWhere(['IN', 'auth_assignment.item_name', $roles]);
            $query->andWhere(['status' => UserAR::STATUS_ACTIVE]);
            $query->andWhere(['LIKE', 'profile.organization_name', $search_query]);
            $orgs_search = ArrayHelper::map($query->all(), 'id', 'id');

            $users_search = array_merge($users_search, $orgs_search);

            $items_query->andWhere(['OR',
                ['LIKE', 'events.name', $search_query],
                ['LIKE', 'events.content', $search_query],
                ['LIKE', 'events.place', $search_query],
                ['IN', 'events_ref_eventstag.eventstag_id', $tags_search],
                ['IN', 'events.direction_id', $direct_search],
                ['IN', 'events.format_id', $eventsformat_search],
                ['IN', 'events_ref_direction.direction_id', $direct_search],
                ['IN', 'events.author_id', $users_search],
                ['IN', 'events.city_id', $city_search],
            ]);
        }
        if (!empty($get)) {
            // мероприятия академии
            $terms['is_ast'] = 0;
            if (!empty($get['is_ast'])) {
                $terms['is_ast'] = intval($get['is_ast']);
            }
            // регистрация открыта
            $terms['registration_open'] = 1;
            if ($get['registration_open'] == '0') {
                $terms['registration_open'] = 0;
            }
            // поиск по кафедрам
            $terms['directs'] = [];
            if (!empty($get['directs']) && is_array($get['directs'])) {
                $terms['directs'] = $get['directs'];
            }
            // поиск по видам
            $terms['eventsformats'] = [];
            if (!empty($get['eventsformats']) && is_array($get['eventsformats'])) {
                $terms['eventsformats'] = $get['eventsformats'];
            }
            // поиск по форматам мероприятия
            $terms['event_format'] = [];
            if (!empty($get['event_format']) && is_array($get['event_format'])) {
                $trans = [Events::TYPE_HYBRID => 2, Events::TYPE_ONLINE => 1, Events::TYPE_OFFLINE => 0];
                foreach ($get['event_format'] as $stype) {
                    if (isset($trans[$stype])) {
                        $terms['event_format'][] = $trans[$stype];
                    }
                }
            }
            // поиск по стоимости
            if (!empty($get['price'])) {
                $terms['price'] = $get['price'];
            }
            // кастомная дата
            if (!empty($get['event_xdate'])) {
                $terms['event_xdate'] = $get['event_xdate'];
            }
            // поиск по дате
            if (!empty($get['date_from'])) {
                $terms['date']['from'] = $get['date_from'];
            }
            if (!empty($get['date_to'])) {
                $terms['date']['to'] = $get['date_to'];
            }
            // поиск по городу
            $terms['city'] = [];
            if (!empty($get['city']) && is_array($get['city'])) {
                $terms['city'] = $get['city'];
            }
        }
        // необходимые таблицы
        $items_query->leftJoin('eventsform', 'eventsform.event_id = events.id');
        $items_query->leftJoin('tariff', 'tariff.event_form_id = eventsform.id');
        $items_query->leftJoin('tariff_price', 'tariff_price.tariff_id = tariff.id');
        if (!empty($terms['price']) || !empty($terms['event_xdate'])) {
            $items_query
                ->andWhere(['eventsform.visible' => 1])
                ->andWhere(['tariff.visible' => 1])
                ->andWhere('(tariff.start_publish IS NULL) OR (tariff.start_publish < NOW())')
                ->andWhere('(tariff.end_publish IS NULL) OR (tariff.end_publish > NOW())')
                ->andWhere('(tariff_price.start_publish IS NULL) OR (tariff_price.start_publish < NOW())')
                ->andWhere('(tariff_price.end_publish IS NULL) OR (tariff_price.end_publish > NOW())');
        }

        // только мероприятия академии
        if ($terms['is_ast'] == 1) {
            $items_query->leftJoin('profile', 'profile.user_id = events.author_id');
            $items_query->andWhere(['OR',
                ['events.author_id' => 0],
                ['profile.is_academy' => 1],
            ]);
        }
        // только с открытой регистрацией
        if ($terms['registration_open'] == 1) {
            $items_query
                // актуальный тариф
                ->andWhere(['tariff.visible' => 1])
                ->andWhere('(tariff.start_publish IS NULL) OR (tariff.start_publish < NOW())')
                ->andWhere('(tariff.end_publish IS NULL) OR (tariff.end_publish > NOW())')
                // актуальный прайс у тарифа
                ->andWhere(['<=', 'tariff_price.start_publish', new \yii\db\Expression('CURDATE()')])
                ->andWhere(['>=', 'tariff_price.end_publish', new \yii\db\Expression('CURDATE()')])
                // мероприятие не завершено
                ->andWhere('(events.event_date_end IS NULL) OR (events.event_date_end >= CURDATE())')
                // мероприятие с регистрацией
                ->andWhere(['events.need_tariff' => 1])
                ->andWhere(['OR',
                    // бесплатная регистрация
                    ['tariff.free' => 1],
                    // форма тарифа платная и активная
                    ['eventsform.visible' => 1, 'eventsform.payregister' => 1],
                ])
                // есть доступные билеты
                ->andWhere(['>', 'tariff.remainTickets', 0])
                // не является отмененным
                ->andWhere(['<>', 'events.status', Events::STATUS_CANCELLED]);
        }
        // ищем по заданным кафедрам
        if (!empty($terms['directs'])) {
            $items_query->andWhere(['IN', 'events.direction_id', $terms['directs']]);
        }
        // ищем по видам
        if (!empty($terms['eventsformats'])) {
            $items_query->leftJoin('ref_eventsformat', 'ref_eventsformat.id = events.format_id');
            $items_query->andWhere(['IN', 'ref_eventsformat.id', $terms['eventsformats']]);
        }
        // ищем по форматам мероприятия
        if (!empty($terms['event_format'])) {
            $trans = [2 => Events::TYPE_HYBRID, 1 => Events::TYPE_ONLINE, 0 => Events::TYPE_OFFLINE];
            foreach ($terms['event_format'] as $stype) {
                if (isset($trans[$stype])) {
                    $kinds[] = $trans[$stype];
                }
            }
            $items_query->andWhere(['events.type' => $kinds]);
        }
        // ищем по стоимости
        $price_filter_priority = false;
        if (!empty($terms['price'])) {
            switch ($terms['price']) {
                case '5000+':
                    $items_query->andWhere(['OR',
                        ['tariff.free' => 1],
                        ['>=', 'tariff_price.price', 5000],
                    ]);
                    $price_filter_priority = true;
                    break;
                case '5000':
                    $items_query->andWhere(['OR',
                        ['tariff.free' => 1],
                        ['<=', 'tariff_price.price', 5000],
                    ]);
                    break;
                case '1000':
                    $items_query->andWhere(['OR',
                        ['tariff.free' => 1],
                        ['<=', 'tariff_price.price', 1000],
                    ]);
                    break;
                case 'free':
                    $items_query->andWhere(['tariff.free' => 1]);
                    break;
                default:
                    break;
            }
        }
        // ищем по датам
        if (!empty($terms['event_xdate'])) {
            switch ($terms['event_xdate']) {
                case 'weekend':
                    $items_query->andWhere(['AND',
                        ['>=', 'event_date_end', date('Y-m-d', strtotime('Saturday this week'))],
                        ['<=', 'event_date', date('Y-m-d', strtotime('Sunday this week'))],
                    ]);
                    break;
                case 'tomorrow':
                    $items_query->andWhere(['AND',
                        ['>=', 'event_date_end', date('Y-m-d', strtotime('+1 day'))],
                        ['<=', 'event_date', date('Y-m-d', strtotime('+1 day'))],
                    ]);
                    break;
                case 'today':
                    $items_query->andWhere(['AND',
                        ['>=', 'event_date_end', date('Y-m-d', strtotime('now'))],
                        ['<=', 'event_date', date('Y-m-d', strtotime('now'))],
                    ]);
                    break;
                default:
                    break;
            }
        } // ищем по датам
        elseif (!empty($terms['date'])) {
            if (!empty($terms['date']['from'])) {
                $items_query->andFilterWhere(['>=', 'events.event_date_end', date('Y-m-d', strtotime($terms['date']['from']))]);
            }
            if (!empty($terms['date']['to'])) {
                $items_query->andFilterWhere(['<=', 'events.event_date', date('Y-m-d', strtotime($terms['date']['to']))]);
            }
        }
        // ищем по городу
        if (!empty($terms['city'])) {
            $items_query->andWhere(['IN', 'events.city_id', $terms['city']]);
        }

        // промо мероприятия
        $promo_items_ids = $model->promo_events_ids;
        if (!empty($model->promo_events_ids)) {
            $items_query->addOrderBy([new Expression('FIELD (events.id, ' . implode(',', $promo_items_ids) . ') DESC')]);
        }
        if ($price_filter_priority) {

            $items_query->addOrderBy([new Expression('FIELD (tariff.free, 0) DESC')]);
        }
        $items_query->addOrderBy([
            '(events.event_date >= CURDATE() OR events.event_date_end >= CURDATE())' => SORT_DESC,
            'IF(events.event_date >= CURDATE() OR events.event_date_end >= CURDATE(), events.event_date, NULL)' => SORT_ASC,
            'IF(events.event_date < CURDATE() AND events.event_date_end < CURDATE(), events.event_date_end, NULL)' => SORT_DESC,
        ]);

        $items_query->distinct();

        // пагинация
        $countQuery = clone $items_query;
        $count_events = $countQuery->count();

        $pageparams = Yii::$app->request->get();
        unset($pageparams['model']);

        $pages = new Pagination([
            'totalCount' => $count_events,
            'defaultPageSize' => 24,
            'route' => $model->getUrlPath(),
            'params' => $pageparams,
        ]);

        $items = $items_query->offset($pages->offset)
            ->with(['author', 'format'])
            ->limit($pages->limit)
            ->all();

        // если ajax-запрос
        if (Yii::$app->request->isAjax) {
            // задаем формат ответа
            Yii::$app->response->format = Response::FORMAT_JSON;
            // инициализируем массив ответа
            $data = ['status' => 'fail'];
            // если пользователи найдены
            if (!empty($items)) {
                // рендерим
                $data['count'] = 'Показать ' . $count_events . ' ' . MainHelper::pluralForm($count_events, ['предложение', 'предложения', 'предложений']);
                $data['html'] = $this->renderPartial('_event_box', ['items' => $items, 'promo_items_ids' => $promo_items_ids, 'model' => $model]);
                // говорим, что все ОК
                $data['status'] = 'success';
            }
            $data['pager'] = \app\widgets\pagination\LinkPager::widget(['pages' => $pages, 'is_ajax' => true, 'container' => '#all-events-cards']);
            // возвращаем ответ
            return $data;
        }

        $directions = Direction::find()->where(['visible' => 1, 'stels_direct' => 0])->orderBy(['name' => SORT_ASC])->all();

        $event_formats_ids = array_unique(Events::findVisibleForCatalog()->select('events.format_id')->asArray()->column());
        $eventsformats = Eventsformat::find()->where(['visible' => 1])->andWhere(['IN', 'ref_eventsformat.id', $event_formats_ids])->orderBy(['name' => SORT_ASC])->all();

        $event_cities_ids = Events::findVisibleForCatalog()->select('events.city_id')->distinct()->asArray()->column();
        // получаем список городов
        $cities_short = City::find()->where(['visible' => 1])->andWhere(['IN', 'id', $event_cities_ids])->orderBy(['big_city' => SORT_DESC, 'name' => SORT_ASC])->all();

        return $this->render(
            $model->view,
            [
                'model' => $model,
                'items' => $items,
                'promo_items_ids' => $promo_items_ids,
                'eventsformats' => $eventsformats,
                'directions' => $directions,
                'cities_short' => $cities_short,
                'terms' => $terms,
                'search_text' => htmlspecialchars($search_query),
                'pages' => $pages,
            ]
        );

    }

    public function actionEventsinner($model)
    {
        $this->setMeta($model);
        /* проверяем видимость по ролям */
        if (Yii::$app->user->isGuest) {
            /* гость приравнивается к физлицу */
            if (!$model->vis_fiz) {
                $login_page = Login::find()->where(['model' => Login::class, 'visible' => 1])->one();
                if ($login_page) {
                    /* воспользуемся системой редиректа после логина для возврата к мероприятию */
                    Yii::$app->session->remove('createorder_after_login');
                    Yii::$app->session->set('redirect_after_login', $model->getUrlPath());
                    $this->redirect(Url::toRoute($login_page->getUrlPath()));
                } else {
                    // если страница не найдена - вернуть 404
                    throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
                }
            }
        } else {
            $role = Yii::$app->user->identity->userAR->role;
            $has_access = false;
            switch ($role) {
                /* Эксперт */
                case 'expert':
                    $has_access = $model->vis_expert;
                    break;
                /* Экспертная организация */
                case 'exporg':
                    $has_access = $model->vis_exporg;
                    break;
                /* Юрлицо */
                case 'urusr':
                    $has_access = $model->vis_ur;
                    break;
                /* Физлицо */
                case 'fizusr':
                    $has_access = $model->vis_fiz;
                    break;
                /* Для админа и МКС нет ограничений на области видимости */
                case 'admin':
                case 'mks':
                    $has_access = true;
                    break;
                /* Для остальных область видимости как у Физлица */
                default:
                    $has_access = $model->vis_fiz;
                    break;
            }
            if (!$has_access) {
                throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
            }
        }

        $this->layout = '@app/views/layouts/eventpage';
        $events_catalog = Eventspage::find()->where(['model' => Eventspage::class, 'visible' => 1])->one();

        /* кнопка в шапке сайта */
        $button = [
            'name' => 'Все мероприятия',
            'link' => ($events_catalog ? $events_catalog->getUrlPath() : false)
        ];
        $this->getView()->params['button_head'] = $button;

        /*
        В блоке выводятся ближайшие мероприятия, у которых есть билеты к продаже. Ближайшие мероприятия выбираются с датой завершения позже текущего дня и в порядке возрастания даты завершения. В блоке выводить 8 мероприятий.
        */
        $closest_events = Events::findVisibleForCatalog()
            ->leftJoin('eventsform', 'eventsform.event_id = events.id')
            ->leftJoin('tariff', 'tariff.event_form_id = eventsform.id')
            // актуальный тариф
            ->andWhere(['tariff.visible' => 1])->andWhere('(tariff.start_publish IS NULL) OR (tariff.start_publish < NOW())')->andWhere('(tariff.end_publish IS NULL) OR (tariff.end_publish > NOW())')
            // мероприятие с регистрацией
            ->andWhere(['events.need_tariff' => 1])
            // есть доступные билеты
            ->andWhere(['>', 'tariff.remainTickets', 0])
            // не содержит текущее мероприятие
            ->andWhere(['!=', 'events.id', $model->id])
            // не отображать отменённые
            ->andWhere(['<>', 'events.status', Events::STATUS_CANCELLED])
            // форма тарифа платная и активная
            ->andWhere(['eventsform.visible' => 1, 'eventsform.payregister' => 1])->distinct()->orderBy('events.event_date_end ASC')->limit(8)->all();

        $footer_banner = \app\modules\pages_helper\models\LentaPageAds::findVisible()->andWhere(['page_id' => $events_catalog->id])->orderBy(new Expression('rand()'))->one();

        return $this->render($model->view, [
            'model' => $model,
            'events_catalog' => $events_catalog,
            'closest_events' => $closest_events,
            'footer_banner' => $footer_banner
        ]);
    }

    public function actionSearch($model)
    {
        $this->setMeta($model);
        $get = Yii::$app->request->get();
        $search_text = htmlentities(trim($get['query']));
        if (!empty($search_text)) {
            // эксперты
            $roles = ['expert', 'exporg'];

            $tags_search = ArrayHelper::map(Competence::find()->where(['LIKE', 'name', $search_text])->andWhere(['visible' => 1])->all(), 'id', 'id');

            $service_search = ArrayHelper::map(Service::findVisible()->leftJoin('service_type', 'service_type.id = service.type_id')->where(
                ['OR',
                    ['LIKE', 'service.name', $search_text],
                    ['LIKE', 'service.description', $search_text],
                    ['LIKE', 'service_type.name', $search_text],
                ]
            )->andWhere(['service.visible' => 1])->all(), 'user_id', 'user_id');

            $service_search = array_unique($service_search);

            $experts_query = UserAR::find()
                ->leftJoin('profile', 'profile.user_id = user.id')
                ->leftJoin('user_ref_competence', 'user_ref_competence.user_id = user.id')
                ->where(
                    ['or',
                        // ['LIKE','LCASE(CONCAT(`profile`.`surname`," ",`profile`.`name`," ",`profile`.`patronymic`))',$search_text],
                        ['LIKE', 'LCASE(CONCAT(`profile`.`surname`," ",`profile`.`name`))', $search_text],
                        ['LIKE', 'LCASE(CONCAT(`profile`.`name`," ",`profile`.`surname`))', $search_text],
                        ['LIKE', 'profile.organization_name', $search_text],
                        ['LIKE', 'profile.about_myself', $search_text],
                        ['IN', 'user_ref_competence.competence_id', $tags_search],
                        ['IN', 'user.id', $service_search],
                    ]
                )
                ->visible($roles)
                ->orderBy(['profile.surname' => 'asc', 'profile.name' => 'asc']);
            $experts = $experts_query->all();
            $experts_count = count($experts);

            // мероприятия
            $events_query = Events::findVisible();
            $events_query->leftJoin('events_ref_eventstag', 'events_ref_eventstag.events_id = events.id');
            $events_query->leftJoin('events_ref_direction', 'events_ref_direction.events_id = events.id');

            $tags_search = ArrayHelper::map(Eventstag::find()->where(['LIKE', 'name', $search_text])->all(), 'id', 'id');
            $direct_search = ArrayHelper::map(Direction::find()->where(['LIKE', 'name', $search_text])->all(), 'id', 'id');
            $city_search = ArrayHelper::map(City::find()->where(['LIKE', 'name', $search_text])->all(), 'id', 'id');

            $query = UserAR::find();
            $roles = ['expert'];
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id');
            $query->leftJoin('profile', 'profile.user_id = user.id');
            $query->andWhere(['IN', 'auth_assignment.item_name', $roles]);
            $query->andWhere(['status' => UserAR::STATUS_ACTIVE]);
            $query->andWhere(['OR',
                ['LIKE', 'CONCAT(`profile`.`surname`," ",`profile`.`name`)', $search_text],
                ['LIKE', 'CONCAT(`profile`.`name`," ",`profile`.`surname`)', $search_text],
            ]);
            $users_search = ArrayHelper::map($query->all(), 'id', 'id');

            $query = UserAR::find();
            $roles = ['exporg'];
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id');
            $query->leftJoin('profile', 'profile.user_id = user.id');
            $query->andWhere(['IN', 'auth_assignment.item_name', $roles]);
            $query->andWhere(['status' => UserAR::STATUS_ACTIVE]);
            $query->andWhere(['LIKE', 'profile.organization_name', $search_text]);
            $orgs_search = ArrayHelper::map($query->all(), 'id', 'id');

            $users_search = array_merge($users_search, $orgs_search);

            $events_query->andWhere(['OR',
                ['LIKE', 'events.name', $search_text],
                ['LIKE', 'events.subtitle', $search_text],
                ['LIKE', 'events.content', $search_text],
                ['LIKE', 'events.place', $search_text],
                ['IN', 'events_ref_eventstag.eventstag_id', $tags_search],
                ['IN', 'events.direction_id', $direct_search],
                ['IN', 'events_ref_direction.direction_id', $direct_search],
                ['IN', 'events.author_id', $users_search],
                ['IN', 'events.city_id', $city_search],
            ]);
            $events_query->orderBy(['event_date' => SORT_DESC]);
            $events_query->distinct();

            $events_count = $events_query->count();
            $events = $events_query->limit(30)->all();

            // блоги
            // демо-версия - ищем по названию
            $blogs_query = Blog::findVisible();
            $blogs_query->leftJoin('lenta_ref_lentatag', 'lenta_ref_lentatag.lenta_id = lenta.id');
            $blogs_query->leftJoin('lenta_ref_direction', 'lenta_ref_direction.lenta_id = lenta.id');

            $tags_search = ArrayHelper::map(Lentatag::find()->where(['LIKE', 'name', $search_text])->all(), 'id', 'id');
            $direct_search = ArrayHelper::map(Direction::find()->where(['LIKE', 'name', $search_text])->all(), 'id', 'id');

            $query = UserAR::find();
            $roles = ['expert'];
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id');
            $query->leftJoin('profile', 'profile.user_id = user.id');
            $query->andWhere(['IN', 'auth_assignment.item_name', $roles]);
            $query->andWhere(['status' => UserAR::STATUS_ACTIVE]);
            $query->andWhere(['OR',
                ['LIKE', 'CONCAT(`profile`.`surname`," ",`profile`.`name`)', $search_text],
                ['LIKE', 'CONCAT(`profile`.`name`," ",`profile`.`surname`)', $search_text],
            ]);

            $users_search = ArrayHelper::map($query->all(), 'id', 'id');

            $query = UserAR::find();
            $roles = ['exporg'];
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id');
            $query->leftJoin('profile', 'profile.user_id = user.id');
            $query->andWhere(['IN', 'auth_assignment.item_name', $roles]);
            $query->andWhere(['status' => UserAR::STATUS_ACTIVE]);
            $query->andWhere(['LIKE', 'profile.organization_name', $search_text]);
            $orgs_search = ArrayHelper::map($query->all(), 'id', 'id');

            $users_search = array_merge($users_search, $orgs_search);

            $blogs_query->andWhere(['OR',
                ['LIKE', 'lenta.name', $search_text],
                ['LIKE', 'lenta.subtitle', $search_text],
                ['LIKE', 'lenta.content', $search_text],
                ['IN', 'lenta_ref_lentatag.lentatag_id', $tags_search],
                ['IN', 'lenta.direction_id', $direct_search],
                ['IN', 'lenta_ref_direction.direction_id', $direct_search],
                ['IN', 'lenta.author_id', $users_search],
            ]);
            $blogs_query->orderBy(['published' => SORT_DESC]);
            $blogs_query->distinct();
            $blogs_count = $blogs_query->count();
            $blogs = $blogs_query->limit(30)->all();

            // новости
            $news_query = News::findVisible();
            $news_query->leftJoin('lenta_ref_lentatag', 'lenta_ref_lentatag.lenta_id = lenta.id');
            $news_query->leftJoin('lenta_ref_direction', 'lenta_ref_direction.lenta_id = lenta.id');

            $tags_search = ArrayHelper::map(Lentatag::find()->where(['LIKE', 'name', $search_text])->all(), 'id', 'id');
            $direct_search = ArrayHelper::map(Direction::find()->where(['LIKE', 'name', $search_text])->all(), 'id', 'id');

            $query = UserAR::find();
            $roles = ['expert'];
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id');
            $query->leftJoin('profile', 'profile.user_id = user.id');
            $query->andWhere(['IN', 'auth_assignment.item_name', $roles]);
            $query->andWhere(['status' => UserAR::STATUS_ACTIVE]);
            $query->andWhere(['OR',
                ['LIKE', 'CONCAT(`profile`.`surname`," ",`profile`.`name`)', $search_text],
                ['LIKE', 'CONCAT(`profile`.`name`," ",`profile`.`surname`)', $search_text],
            ]);

            $users_search = ArrayHelper::map($query->all(), 'id', 'id');

            $query = UserAR::find();
            $roles = ['exporg'];
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id');
            $query->leftJoin('profile', 'profile.user_id = user.id');
            $query->andWhere(['IN', 'auth_assignment.item_name', $roles]);
            $query->andWhere(['status' => UserAR::STATUS_ACTIVE]);
            $query->andWhere(['LIKE', 'profile.organization_name', $search_text]);
            $orgs_search = ArrayHelper::map($query->all(), 'id', 'id');

            $users_search = array_merge($users_search, $orgs_search);

            $news_query->andWhere(['OR',
                ['LIKE', 'lenta.name', $search_text],
                ['LIKE', 'lenta.subtitle', $search_text],
                ['LIKE', 'lenta.content', $search_text],
                ['IN', 'lenta_ref_lentatag.lentatag_id', $tags_search],
                ['IN', 'lenta.direction_id', $direct_search],
                ['IN', 'lenta_ref_direction.direction_id', $direct_search],
                ['IN', 'lenta.author_id', $users_search],
            ]);
            $news_query->orderBy(['published' => SORT_DESC]);
            $news_query->distinct();
            $news_count = $news_query->count();
            $news = $news_query->limit(30)->all();

            // проекты
            // демо-версия - ищем по названию
            $projects_query = Project::findVisible();
            $projects_query->leftJoin('lenta_ref_lentatag', 'lenta_ref_lentatag.lenta_id = lenta.id');
            $projects_query->leftJoin('lenta_ref_direction', 'lenta_ref_direction.lenta_id = lenta.id');

            $tags_search = ArrayHelper::map(Lentatag::find()->where(['LIKE', 'name', $search_text])->all(), 'id', 'id');
            $direct_search = ArrayHelper::map(Direction::find()->where(['LIKE', 'name', $search_text])->all(), 'id', 'id');

            $query = UserAR::find();
            $roles = ['expert'];
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id');
            $query->leftJoin('profile', 'profile.user_id = user.id');
            $query->andWhere(['IN', 'auth_assignment.item_name', $roles]);
            $query->andWhere(['status' => UserAR::STATUS_ACTIVE]);
            $query->andWhere(['OR',
                ['LIKE', 'CONCAT(`profile`.`surname`," ",`profile`.`name`)', $search_text],
                ['LIKE', 'CONCAT(`profile`.`name`," ",`profile`.`surname`)', $search_text],
            ]);
            $users_search = ArrayHelper::map($query->all(), 'id', 'id');

            $query = UserAR::find();
            $roles = ['exporg'];
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id');
            $query->leftJoin('profile', 'profile.user_id = user.id');
            $query->andWhere(['IN', 'auth_assignment.item_name', $roles]);
            $query->andWhere(['status' => UserAR::STATUS_ACTIVE]);
            $query->andWhere(['LIKE', 'profile.organization_name', $search_text]);
            $orgs_search = ArrayHelper::map($query->all(), 'id', 'id');

            $users_search = array_merge($users_search, $orgs_search);

            $projects_query->andWhere(['OR',
                ['LIKE', 'lenta.name', $search_text],
                ['LIKE', 'lenta.subtitle', $search_text],
                ['LIKE', 'lenta.content', $search_text],
                ['IN', 'lenta_ref_lentatag.lentatag_id', $tags_search],
                ['IN', 'lenta.direction_id', $direct_search],
                ['IN', 'lenta_ref_direction.direction_id', $direct_search],
                ['IN', 'lenta.author_id', $users_search],
            ]);

            $projects_query->orderBy(['published' => SORT_DESC]);
            $projects_query->distinct();

            $projects_count = $projects_query->count();
            $projects = $projects_query->limit(30)->all();

            // материалы
            // демо-версия - ищем по названию
            $material_query = Material::findVisible();
            $material_query->leftJoin('lenta_ref_lentatag', 'lenta_ref_lentatag.lenta_id = lenta.id');
            $material_query->leftJoin('lenta_ref_direction', 'lenta_ref_direction.lenta_id = lenta.id');

            $tags_search = ArrayHelper::map(Lentatag::find()->where(['LIKE', 'name', $search_text])->all(), 'id', 'id');
            $direct_search = ArrayHelper::map(Direction::find()->where(['LIKE', 'name', $search_text])->all(), 'id', 'id');

            $query = UserAR::find();
            $roles = ['expert'];
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id');
            $query->leftJoin('profile', 'profile.user_id = user.id');
            $query->andWhere(['IN', 'auth_assignment.item_name', $roles]);
            $query->andWhere(['status' => UserAR::STATUS_ACTIVE]);
            $query->andWhere(['OR',
                ['LIKE', 'CONCAT(`profile`.`surname`," ",`profile`.`name`)', $search_text],
                ['LIKE', 'CONCAT(`profile`.`name`," ",`profile`.`surname`)', $search_text],
            ]);
            $users_search = ArrayHelper::map($query->all(), 'id', 'id');

            $query = UserAR::find();
            $roles = ['exporg'];
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id');
            $query->leftJoin('profile', 'profile.user_id = user.id');
            $query->andWhere(['IN', 'auth_assignment.item_name', $roles]);
            $query->andWhere(['status' => UserAR::STATUS_ACTIVE]);
            $query->andWhere(['LIKE', 'profile.organization_name', $search_text]);
            $orgs_search = ArrayHelper::map($query->all(), 'id', 'id');

            $users_search = array_merge($users_search, $orgs_search);

            $material_query->andWhere(['OR',
                ['LIKE', 'lenta.name', $search_text],
                ['LIKE', 'lenta.subtitle', $search_text],
                ['LIKE', 'lenta.content', $search_text],
                ['IN', 'lenta_ref_lentatag.lentatag_id', $tags_search],
                ['IN', 'lenta.direction_id', $direct_search],
                ['IN', 'lenta_ref_direction.direction_id', $direct_search],
                ['IN', 'lenta.author_id', $users_search],
            ]);

            $material_query->orderBy(['published' => SORT_DESC]);
            $material_query->distinct();

            $materials_count = $material_query->count();
            $materials = $material_query->limit(30)->all();

            // услуги
            $services_query = Service::findVisible();

            $query = UserAR::find()->visible(['expert']);
            $query->leftJoin('profile', 'profile.user_id = user.id');
            $query->leftJoin('organization', 'organization.user_id = user.id');
            $query->andWhere(['organization.can_service' => 1]);
            $query->andWhere(['OR',
                ['LIKE', 'CONCAT(`profile`.`surname`," ",`profile`.`name`)', $search_text],
                ['LIKE', 'CONCAT(`profile`.`name`," ",`profile`.`surname`)', $search_text],
            ]);
            $users_search = ArrayHelper::map($query->all(), 'id', 'id');

            $query = UserAR::find()->visible(['exporg']);
            $query->leftJoin('profile', 'profile.user_id = user.id');
            $query->leftJoin('organization', 'organization.user_id = user.id');
            $query->andWhere(['organization.can_service' => 1]);
            $query->andWhere(['LIKE', 'profile.organization_name', $search_text]);
            $orgs_search = ArrayHelper::map($query->all(), 'id', 'id');

            $users_search = array_merge($users_search, $orgs_search);

            $services_query->andWhere(['IN', 'service.status', Service::CATALOG_VISIBLE_STATUSES]);

            $services_query->andWhere(['OR',
                ['LIKE', 'service.name', $search_text],
                ['LIKE', 'service.place', $search_text],
                ['LIKE', 'service.platform', $search_text],
                ['LIKE', 'service.description', $search_text],
                ['LIKE', 'service.short_description', $search_text],
                ['LIKE', 'service.target_descr', $search_text],
                ['LIKE', 'service.task_descr', $search_text],
                ['LIKE', 'service.special_descr', $search_text],
                ['LIKE', 'service.price_descr', $search_text],
                ['IN', 'service.user_id', $users_search],
            ]);

            $services_query->distinct();

            $services_count = $services_query->count();
            $services = $services_query->limit(30)->all();

            // текстовые страницы
            // демо-версия - ищем по названию и тексту
            /*$pages = Page::find()->where(['visible'=>1,'model'=>Page::class])
            ->andWhere(['OR',['LIKE','name',$search_text],['LIKE','content',$search_text]]);
            $pages_count = $pages->count();
            $pages = $pages->limit(30)->all();*/

            // дпо
            $eduprog_query = Eduprog::findVisible();

            $query = UserAR::find();
            $roles = ['expert'];
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id');
            $query->leftJoin('profile', 'profile.user_id = user.id');
            $query->andWhere(['IN', 'auth_assignment.item_name', $roles]);
            $query->andWhere(['status' => UserAR::STATUS_ACTIVE]);
            $query->andWhere(['OR',
                ['LIKE', 'CONCAT(`profile`.`surname`," ",`profile`.`name`)', $search_text],
                ['LIKE', 'CONCAT(`profile`.`name`," ",`profile`.`surname`)', $search_text],
            ]);
            $users_search = ArrayHelper::map($query->all(), 'id', 'id');

            $query = UserAR::find();
            $roles = ['exporg'];
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id');
            $query->leftJoin('profile', 'profile.user_id = user.id');
            $query->andWhere(['IN', 'auth_assignment.item_name', $roles]);
            $query->andWhere(['status' => UserAR::STATUS_ACTIVE]);
            $query->andWhere(['LIKE', 'profile.organization_name', $search_text]);
            $orgs_search = ArrayHelper::map($query->all(), 'id', 'id');

            $users_search = array_merge($users_search, $orgs_search);

            $eduprog_query->andWhere(['OR',
                ['LIKE', 'eduprog.name', $search_text],
                ['LIKE', 'eduprog.content', $search_text],
                ['LIKE', 'eduprog.rules', $search_text],
                ['LIKE', 'eduprog.learn', $search_text],
                ['LIKE', 'eduprog.cost_text', $search_text],
                ['LIKE', 'eduprog.suits_for', $search_text],
                ['LIKE', 'eduprog.block_text', $search_text],
                ['LIKE', 'eduprog.works_text', $search_text],
                ['IN', 'eduprog.author_id', $users_search],
            ]);

            $eduprog_query->orderBy(['start_publish' => SORT_DESC])->distinct();

            $eduprog_count = $eduprog_query->count();
            $eduprog = $eduprog_query->limit(30)->all();

            $events_catalog = Eventspage::find()->where(['model' => Eventspage::class, 'visible' => 1])->one();
            $project_catalog = LentaProjectpage::find()->where(['model' => LentaProjectpage::class, 'visible' => 1])->one();
            $eduprog_catalog = EduprogPage::find()->where(['model' => EduprogPage::class, 'visible' => 1])->one();

            return $this->render($model->view, [
                'model' => $model,
                'search_text' => htmlspecialchars($search_text),
                'experts_count' => $experts_count,
                'experts' => $experts,
                'blogs_count' => $blogs_count,
                'blogs' => $blogs,
                'news_count' => $news_count,
                'news' => $news,
                'events_count' => $events_count,
                'events' => $events,
                'events_catalog' => $events_catalog,
                'projects_count' => $projects_count,
                'projects' => $projects,
                'project_catalog' => $project_catalog,
                'materials_count' => $materials_count,
                'materials' => $materials,
                'services' => $services,
                'services_count' => $services_count,
                'eduprog' => $eduprog,
                'eduprog_count' => $eduprog_count,
                'eduprog_catalog' => $eduprog_catalog,
                /*'pages_count' => $pages_count,
                'pages' => $pages,*/
            ]);
        }
        return $this->render($model->view . '_empty', [
            'model' => $model,
            'search_text' => htmlspecialchars($search_text)
        ]);

    }

    public function actionFilter($model)
    {
        // задать заголовок страницы
        $this->setMeta($model);
        $get = Yii::$app->request->get();
        $terms = [];
        $roles = ['academ', 'expert', 'exporg'];
        // если пришли по фильтрам
        if (!empty($get)) {
            // текстовый поиск
            $terms['search'] = '';
            if (!empty($get['query'])) {
                $terms['search'] = htmlspecialchars($get['query']);
            }
            // поиск по Решаемым задачам и по Специализациям
            $terms['task'] = [];
            if (!empty($get['task']) && is_array($get['task'])) {
                $terms['task'] = array_merge($terms['task'], $get['task']);
            }
            $terms['spec'] = [];
            if (!empty($get['competence']) && is_array($get['competence'])) {
                $terms['spec'] = array_merge($terms['spec'], $get['competence']);
            }
            // поиск по группам услуг
            $terms['servgroup'] = [];
            if (!empty($get['servgroup']) && is_array($get['servgroup'])) {
                $terms['servgroup'] = $get['servgroup'];
            }
            // поиск по кафедрам
            $terms['directs'] = [];
            if (!empty($get['directs']) && is_array($get['directs'])) {
                $terms['directs'] = $get['directs'];
            }
            // поиск по типам услуг
            $terms['service_type'] = [];
            if (!empty($get['service_type']) && is_array($get['service_type'])) {
                $trans = ['hybrid' => 2, 'online' => 1, 'offline' => 0];
                foreach ($get['service_type'] as $stype) {
                    if (isset($trans[$stype])) {
                        $terms['service_type'][] = $trans[$stype];
                    }
                }
            }
            // поиск по стоимости услуг
            if (!empty($get['price_from'])) {
                $terms['price']['from'] = (int)$get['price_from'];
            }
            if (!empty($get['price_to'])) {
                $terms['price']['to'] = (int)$get['price_to'];
            }
            // поиск по городу
            $terms['city'] = [];
            if (!empty($get['city']) && is_array($get['city'])) {
                $terms['city'] = $get['city'];
            }
            // поиск по полу
            $terms['gender'] = [];
            if (!empty($get['gender']) && is_array($get['gender'])) {
                $trans = ['male' => 0, 'female' => 1];
                foreach ($get['gender'] as $stype) {
                    if (isset($trans[$stype])) {
                        $terms['gender'][] = $trans[$stype];
                    }
                }
            }
            // поиск по возрасту
            if (!empty($get['age_from'])) {
                $terms['age']['from'] = date('Y-m-d', time() - (int)$get['age_from'] * 365 * 24 * 60 * 60);
                $terms['age_orig']['from'] = (int)$get['age_from'];
            }
            if (!empty($get['age_to'])) {
                $terms['age']['to'] = date('Y-m-d', time() - (int)$get['age_to'] * 365 * 24 * 60 * 60);
                $terms['age_orig']['to'] = (int)$get['age_to'];
            }
        }

        // получаем список кафедр
        $directions = Direction::find()
            ->andWhere(['visible' => 1, 'stels_direct' => 0])
            ->andWhere([
                'IN', 'id', DBHelper::getAffectedByUsersIds('user_direction', 'direction_id', $roles),
            ])
            ->orderBy(['name' => SORT_ASC])
            ->all();
        // получаем список решаемых задач
        $selected_tasks = Solvtask::find()->where(['visible' => 1])->andWhere(['IN', 'id', $terms['task']])->andWhere(['IN', 'id', DBHelper::getAffectedByUsersIds('user_ref_solvtask', 'solvtask_id', $roles)])->orderBy(['name' => SORT_ASC])->all();
        // получаем список специализаций и компетенций
        $selected_competences = Competence::find()->where(['visible' => 1])->andWhere(['IN', 'id', $terms['spec']])->andWhere(['IN', 'id', DBHelper::getAffectedByUsersIds('user_ref_competence', 'competence_id', $roles)])->orderBy(['name' => SORT_ASC])->all();
        // получаем короткий список городов
        $cities_short = City::find()->where(['visible' => 1])->andWhere(['IN', 'id', $terms['city']])->orderBy(['big_city' => SORT_DESC, 'name' => SORT_ASC])->all();
        // получаем список типов услуг
        $selected_servgroups = ServiceType::find()->where(['visible' => 1])->andWhere(['IN', 'id', $terms['servgroup']])->orderBy(['name' => SORT_ASC])->all();
        $servgroups = ServiceType::find()->where(['visible' => 1])->orderBy(['name' => SORT_ASC])->all();

        // популярные решаемые задачи
        $tasks_pop = Solvtask::find()->where(['visible' => 1, 'popular' => 1])->andWhere(['IN', 'id', DBHelper::getAffectedByUsersIds('user_ref_solvtask', 'solvtask_id', $roles)])->orderBy(['name' => SORT_ASC])->all();
        // популярные компетенции
        $competence_pop = Competence::find()->where(['visible' => 1, 'popular' => 1])->andWhere(['IN', 'id', DBHelper::getAffectedByUsersIds('user_ref_competence', 'competence_id', $roles)])->orderBy(['name' => SORT_ASC])->all();
        // популярные типы услуг
        //        var_dump(1);
        //        exit();
        $servgroups_pop = ServiceType::find()->where(['visible' => 1, 'popular' => 1])->andWhere(['IN', 'id', DBHelper::getAffectedByUsersIds('service', 'type_id', $roles)])->orderBy(['name' => SORT_ASC])->all();
        // популярные == крупные города
        $cities_pop = City::find()->where(['visible' => 1, 'big_city' => 1])->orderBy(['name' => SORT_ASC])->all();
        // страница со всеми академиками
        $academic_page = AcademyCatalog::find()->where(['model' => AcademyCatalog::class, 'visible' => 1])->one();
        // рендерим страницу
        return $this->render($model->view, ['model' => $model, 'terms' => $terms, 'directions' => $directions, 'tasks' => $selected_tasks, 'competences' => $selected_competences, 'tasks_pop' => $tasks_pop, 'competence_pop' => $competence_pop, 'cities_short' => $cities_short, 'cities_pop' => $cities_pop, 'servgroups' => $selected_servgroups, 'all_servgroups' => $servgroups, 'servgroups_pop' => $servgroups_pop, 'academic_page' => $academic_page]);
    }

    public function actionService($model)
    {
        $this->setMeta($model);
        $target_audience_catalog = TargetAudiencePage::find()->where(['model' => TargetAudiencePage::class, 'visible' => 1])->one();
        return $this->redirect($target_audience_catalog->getUrlPath());
    }

    public function actionServiceinner($model)
    {
        $this->setMeta($model);
        $this->layout = '@app/views/layouts/eventpage';
        $target_audience_catalog = TargetAudiencePage::find()->where(['model' => TargetAudiencePage::class, 'visible' => 1])->one();
        $service_catalog = ServiceTypePage::find()->where(['model' => ServiceTypePage::class, 'visible' => 1])->one();

        /* кнопка в шапке сайта */
        $button = [
            'name' => 'Все услуги Экспертов Академии',
            'link' => ($target_audience_catalog ? $target_audience_catalog->getUrlPath() : false)
        ];
        $this->getView()->params['button_head'] = $button;

        $more_services = Service::findVisible()
            ->andWhere(['!=', 'service.id', $model->id])
            ->leftJoin('service_ref_competence', 'service_ref_competence.service_id = service.id')
            ->leftJoin('service_ref_solvtask', 'service_ref_solvtask.service_id = service.id')
            ->andWhere(['OR',
                ['IN', 'service_ref_competence.competence_id', ArrayHelper::map($model->competence, 'id', 'id')],
                ['IN', 'service_ref_solvtask.solvtask_id', ArrayHelper::map($model->solvtask, 'id', 'id')],
            ])
            ->orderBy(new Expression('rand()'))
            ->limit(6)->all();

        $footer_banner = \app\modules\pages_helper\models\LentaPageAds::findVisible()->andWhere(['page_id' => $service_catalog->id])->orderBy(new Expression('rand()'))->one();

        return $this->render($model->view, ['model' => $model, 'target_audience_catalog' => $target_audience_catalog, 'service_catalog' => $service_catalog, 'more_services' => $more_services, 'footer_banner' => $footer_banner]);
    }

    public function actionTargetAudienceOld($model)
    {
        $this->setMeta($model);
        $items = TargetAudience::find()->where(['visible' => 1])->orderBy(['name' => SORT_ASC])->all();
        $target_audience_catalog = TargetAudiencePageOld::find()->where(['model' => TargetAudiencePageOld::class, 'visible' => 1])->one();
        $random_type = ServiceType::find()->where(['visible' => 1])->orderBy(new Expression('rand()'))->one();
        return $this->render($model->view, ['model' => $model, 'target_audience' => $items, 'target_audience_catalog' => $target_audience_catalog, 'random_type' => $random_type]);
    }

    public function actionTargetAudience($model)
    {
        $this->setMeta($model);
        $service_type_page = ServiceTypePage::find()->where(['model' => ServiceTypePage::class, 'visible' => 1])->one();
        $roles = ['expert'];

        $rows = 8;
        $cols = 4;

        $experts_has_service_ids = Service::findVisible()->select('user_id')->distinct()->asArray()->column();
        $random_experts = UserAR::find()
            ->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id')
            ->leftJoin('profile', 'profile.user_id = user.id')
            ->leftJoin('file_store', 'file_store.keeper_id = profile.id')
            ->andWhere(['file_store.keeper_class' => 'app\modules\users\models\Profile'])
            ->andWhere(['file_store.keeper_field' => 'image'])
            ->andWhere(['>', 'file_store.id', 0])
            ->andWhere(['IN', 'auth_assignment.item_name', $roles])
            ->andWhere(['status' => UserAR::STATUS_ACTIVE])
            ->andWhere(['user.visible' => true])
            ->andWhere(['IN', 'user.id', $experts_has_service_ids])
            ->orderBy(new Expression('rand()'))
            ->limit($rows * $cols)
            ->all();

        $pop = $model->getPopularTypes();
        return $this->render($model->view, ['model' => $model, 'pop' => $pop, 'random_experts' => $random_experts, 'service_type_page' => $service_type_page, 'rows' => $rows, 'cols' => $cols]);
    }

    public function actionTargetAudienceinner($model)
    {
        $this->setMeta($model);
        $target_audience_catalog = TargetAudiencePage::find()->where(['model' => TargetAudiencePage::class, 'visible' => 1])->one();
        $target_audience_list = TargetAudience::find()->where(['visible' => 1])->orderBy(['name' => SORT_ASC])->all();
        return $this->render($model->view, ['model' => $model, 'target_audience_catalog' => $target_audience_catalog, 'target_audience_list' => $target_audience_list,]);
    }

    // TargetAudiencePageProblem => $problem

    public function actionPaymentSuccess($model)
    {
        $this->setMeta($model);
        $order_id = Yii::$app->request->get('order', null);
        $order_type = Yii::$app->request->get('type', null);
        $user_id = Yii::$app->user->isGuest ? null : Yii::$app->user->identity->id;
        $message = '';
        if (!empty($order_id) && !empty($order_type)) {
            // найти заказ, в зависимости от типа
            // проверить, что заказ принадлежит пользователю
            switch ($order_type) {
                case 'dpo':
                    $order = Eduprogorder::findOne((int)$order_id);
                    if ($order && ($order->user_id == $user_id) && $order->is_payed) {
                        // найти текст для отображения на странице
                        $message = $order->eduprog?->success_text;
                        if (empty($message)) {
                            // достаем из настроек стандартный текст
                            $message = \app\modules\settings\models\Settings::getInfo('success_order_eduprog');
                        }
                    }
                    // если заказ не найден или с ним что-то не так, то страница отображается без индивидуального текста
                    break;
                // default:
                // для остальных типов пока нет инструкций
            }
        }
        return $this->render($model->view, ['model' => $model, 'message' => $message]);
    }

    public function actionServiceType($model)
    {
        // $this->setMeta($model);
        // $target_audience_catalog = TargetAudiencePage::find()->where(['model'=>TargetAudiencePage::class,'visible'=>1])->one();
        // return $this->redirect($target_audience_catalog->getUrlPath());
        $get = Yii::$app->request->get();
        if (!empty(intval($get['problem_id']))) {
            $problem = \app\modules\target_audience\models\TargetAudiencePageProblem::find()->where(['id' => intval($get['problem_id'])])->one();
            if (!empty($problem)) {
                return $this->actionServiceTypeinner($model, $problem);
            }
        }
        return $this->actionServiceTypeinner($model, new \app\modules\target_audience\models\TargetAudiencePageProblem());
    }

    public function actionServiceTypeinner($model, $problem = false)
    {
        $this->setMeta($model);
        $get = Yii::$app->request->get();
        if ($problem && !empty($problem->page_query)) {
            parse_str($problem->page_query, $get);
        }
        $terms = [];

        $sort_field = 'name';
        $sort_val = SORT_ASC;

        $target_audience_catalog = TargetAudiencePage::find()->where(['model' => TargetAudiencePage::class, 'visible' => 1])->one();
        $service_type_page = ServiceTypePage::find()->where(['model' => ServiceTypePage::class, 'visible' => 1])->one();

        $audience_list = TargetAudience::find()->where(['visible' => 1])
            ->leftJoin('service_ref_target_audience', 'service_ref_target_audience.target_audience_id = target_audience.id')
            ->orderBy(['name' => SORT_ASC])->distinct()->all();

        $service_types = ServiceType::find()->where(['visible' => 1])->orderBy(['order' => SORT_ASC, 'name' => SORT_ASC])->distinct()->all();

        $directs = Direction::find()->where(['visible' => 1, 'stels_direct' => 0])->orderBy(['name' => SORT_ASC])->distinct()->all();

        $service_cities_ids = Service::findVisible()->select('city_id')->distinct()->asArray()->column();
        $cities_pop = City::find()->where(['visible' => 1, 'big_city' => 1])->andWhere(['IN', 'id', $service_cities_ids])->orderBy(['name' => SORT_ASC])->all();

        if (!empty($get)) {
            if (!empty($get['sort'] && $get['sort'] == 0)) {
                $sort_val = SORT_DESC;
            }

            // текстовый поиск
            $terms['search'] = '';
            if (!empty($get['query'])) {
                $terms['search'] = trim($get['query']);
            }

            $terms['service_types'] = [];
            if (!empty($get['service_types']) && is_array($get['service_types'])) {
                $terms['service_types'] = $get['service_types'];
            }

            $terms['directs'] = [];
            if (!empty($get['directs']) && is_array($get['directs'])) {
                $terms['directs'] = $get['directs'];
            }

            $terms['target_audience'] = [];
            if (!empty($get['target_audience']) && is_array($get['target_audience'])) {
                $terms['target_audience'] = array_merge($terms['target_audience'], $get['target_audience']);
            }

            // поиск по формату
            $terms['service_kind'] = [];
            if (!empty($get['service_kind']) && is_array($get['service_kind'])) {
                $trans = ['hybrid' => 2, 'online' => 1, 'offline' => 0];
                foreach ($get['service_kind'] as $stype) {
                    if (isset($trans[$stype])) {
                        $terms['service_kind'][] = $trans[$stype];
                    }
                }
            }
            // поиск по стоимости услуг
            if (!empty($get['price_from'])) {
                $terms['price']['from'] = (int)$get['price_from'];
            }
            if (!empty($get['price_to'])) {
                $terms['price']['to'] = (int)$get['price_to'];
            }
            // поиск по городу
            $terms['city'] = [];
            if (!empty($get['city']) && is_array($get['city'])) {
                $terms['city'] = $get['city'];
            }

        }

        $items_query = Service::findVisible()->visibleByRole(true);
        $items_query
            ->leftJoin('service_type', 'service_type.id = service.type_id')
            ->leftJoin('service_ref_target_audience', 'service_ref_target_audience.service_id = service.id');

        if (!empty($terms['search'])) {
            $service_search = ArrayHelper::map(Service::findVisible()->leftJoin('service_type', 'service_type.id = service.type_id')->andWhere(
                ['OR',
                    ['LIKE', 'service.name', $terms['search']],
                    ['LIKE', 'service.description', $terms['search']],
                    ['LIKE', 'service_type.name', $terms['search']],
                ]
            )->andWhere(['service.visible' => 1])->all(), 'id', 'id');

            $service_search = array_unique($service_search);
            $items_query->andWhere(['IN', 'service.id', $service_search]);

        }

        if (!empty($terms['service_types'])) {
            $items_query->andWhere(['IN', 'service.type_id', $terms['service_types']]);
        }

        if (!empty($terms['directs'])) {
            $items_query->andWhere(['IN', 'service.direction_id', $terms['directs']]);
        }

        //
        if (!empty($terms['target_audience'])) {
            $items_query->andWhere(['IN', 'service_ref_target_audience.target_audience_id', $terms['target_audience']]);
        }

        if (!empty($terms['competence'])) {
            $items_query->leftJoin('service_ref_competence', 'service_ref_competence.service_id = service.id')
                ->andWhere(['IN', 'service_ref_competence.competence_id', $terms['competence']]);
        }

        // ищем по форматам
        if (!empty($terms['service_kind'])) {
            $items_query->andWhere(['IN', 'service.kind', $terms['service_kind']]);
        }
        // ищем по стоимости
        if (!empty($terms['price'])) {
            if (!empty($terms['price']['from'])) {
                $items_query->andFilterWhere(['>=', 'service.price', $terms['price']['from']]);
            }
            if (!empty($terms['price']['to'])) {
                $items_query->andFilterWhere(['<=', 'service.price', $terms['price']['to']]);
            }
        }

        // ключевые слова
        $keywordName = (isset($get['keyword']) ? $get['keyword'] : false);
        if ($keywordName) {
            $keyword = Keyword::find()->where(['name' => $keywordName])->one();
            if ($keyword) {
                $items_query->leftJoin('models_keywords', 'models_keywords.entity_id = service.id');
                $items_query->andWhere(['models_keywords.keyword_id' => $keyword->id]);
                $items_query->andWhere(['models_keywords.entity_model' => Service::class]);
            }
        }

        // решаемые задачи
        $solvtaskName = (isset($get['solvtask']) ? $get['solvtask'] : false);
        if ($solvtaskName) {
            $solvtask = Solvtask::find()->where(['name' => $solvtaskName])->one();
            if ($solvtask) {
                $items_query->leftJoin('service_ref_solvtask', 'service_ref_solvtask.service_id = service.id');
                $items_query->andWhere(['service_ref_solvtask.solvtask_id' => $solvtask->id]);
            }
        }

        // специализации
        $competenceName = (isset($get['competence']) ? $get['competence'] : false);
        if ($competenceName) {
            $competence = Competence::find()->where(['name' => $competenceName])->one();
            if ($competence) {
                $items_query->leftJoin('service_ref_competence', 'service_ref_competence.service_id = service.id');
                $items_query->andWhere(['service_ref_competence.competence_id' => $competence->id]);
            }
        }

        // ищем по городу
        if (!empty($terms['city'])) {
            $items_query->andWhere(['IN', 'service.city_id', $terms['city']]);
        }

        // случайная выдача
        if (!empty($get['random'] && $get['random'] == 1)) {
            $items_query->orderBy(new Expression('rand()'))->distinct();
        } else {
            $items_query->orderBy([$sort_field => $sort_val])->distinct();
        }

        $countQuery = clone $items_query;
        $count_items = $countQuery->count();

        $pageparams = Yii::$app->request->get();
        unset($pageparams['model']);

        $pages = new Pagination([
            'totalCount' => $count_items,
            'defaultPageSize' => 8,
            'route' => $problem->getUrlPath(),
            'params' => $pageparams,
        ]);

        $items = $items_query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        if (Yii::$app->request->isAjax) {
            // задаем формат ответа
            Yii::$app->response->format = Response::FORMAT_JSON;
            // инициализируем массив ответа
            $data = ['status' => 'fail'];
            // если пользователи найдены
            if (!empty($items)) {
                // рендерим
                $data['count'] = 'Показать ' . $count_items . ' ' . MainHelper::pluralForm($count_items, ['предложение', 'предложения', 'предложений']);
                $data['html'] = $this->renderPartial('_service_types_box', ['items' => $items, 'model' => $model]);
                // говорим, что все ОК
                $data['status'] = 'success';
            }
            $data['pager'] = \app\widgets\pagination\LinkPager::widget(['pages' => $pages, 'is_ajax' => true, 'container' => '#services_expert_items']);
            // возвращаем ответ
            return $data;
        }

        $cities_short = City::find()->where(['visible' => 1])->andWhere(['IN', 'id', $terms['city']])->orderBy(['big_city' => SORT_DESC, 'name' => SORT_ASC])->all();
        $selected_target_audience = ServiceType::find()->where(['visible' => 1])->andWhere(['IN', 'id', $terms['target_audience']])->orderBy(['name' => SORT_ASC])->all();

        return $this->render($problem->view, [
            'model' => $problem,
            'terms' => $terms,
            'target_audience_catalog' => $target_audience_catalog,
            'service_type_page' => $service_type_page,
            'audience_list' => $audience_list,
            'service_types' => $service_types,
            'cities_pop' => $cities_pop,
            'directs' => $directs,
            'cities_short' => $cities_short,
            'selected_target_audience' => $selected_target_audience,
            'items' => $items,
            'pages' => $pages,
        ]);
    }

    public function actionPaymentFail($model)
    {
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model]);
    }

    public function actionAfisha($model)
    {
        // на текущий момент шаблона этой страницы нет - редиректим на главную
        $this->setMeta($model);
        return $this->redirect('/');
        // return $this->render($model->view,['model'=>$model]);
    }

    public function actionNone($model)
    {
        // для всех каталогов, у которых нет страницы со списком элементов - редиректим на главную
        return $this->redirect('/');
    }

    public function actionAfishaview($model)
    {
        // выдать pdf на просмотр
        /*$file = Yii::getAlias('@webroot').'/'.$model->afisha->src;
        if (file_exists($file)) {
            header('Content-Type: application/pdf');
            if (ob_get_level()) {
              ob_end_clean();
            }
            readfile($file);
        } else {
            // если файл не найден вернуть 404
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }*/

        // выдать страницу с рендером iframe
        $this->setMeta($model);
        $this->layout = '@app/views/layouts/empty';
        return $this->render($model->view, ['model' => $model]);
    }

    public function actionBusiness($model)
    {
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model]);
    }

    public function actionHrs($model)
    {
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model]);
    }

    public function actionAbout_expert($model)
    {
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model]);
    }

    public function actionBlog($model)
    {
        $items_model = Blog::class;
        $mode = 'blog';
        return $this->actionLenta($model, $items_model, $mode);
    }

    public function actionLenta($model, $items_model = null, $mode = null)
    {
        if (empty($items_model)) {
            $items_model = Lenta::class;
            $mode = 'lenta';
        }

        $this->setMeta($model);
        $get = Yii::$app->request->get();

        // аякс для верхнего списка промо записей
        $promo_lenta_limit = 7;
        $promo_lenta_show_more = false;
        $promo_lenta = $model->getPromo_lenta(true);
        // номер страницы
        if (isset($get['promo_lenta_page'])) {

            $promo_lenta->offset(intval((intval($get['promo_lenta_page']) - 1) * $promo_lenta_limit));
            $promo_lenta_count = count($promo_lenta->all());
            $promo_lenta->limit($promo_lenta_limit);
            $promo_lenta = $promo_lenta->all();

            if (Yii::$app->request->isAjax) {
                // задаем формат ответа
                Yii::$app->response->format = Response::FORMAT_JSON;
                // инициализируем массив ответа
                $data = ['status' => 'fail'];
                if (!empty($promo_lenta)) {
                    $data['count'] = $promo_lenta_count;
                    $data['show_more'] = ($promo_lenta_count > $promo_lenta_limit) ? true : false;
                    $data['html'] = $this->renderPartial('_lenta_promo_box', ['items' => $promo_lenta, 'model' => $model]);
                    $data['status'] = 'success';
                }
                return $data;
            }
        } // показываем первые 7 на странице при закгрузке без аякса
        else {
            if ($promo_lenta) {
                $promo_lenta_show_more = $promo_lenta->count() > $promo_lenta_limit ? true : false;
                $promo_lenta = $promo_lenta->limit(7)->all();
            }
        }

        $items_query = $items_model::findVisible();
        $terms = [];
        // поиск по кафедрам
        $terms['directs'] = [];
        if (!empty($get['directs']) && is_array($get['directs'])) {
            $terms['directs'] = $get['directs'];
        }

        $tag = (isset($get['tag']) ? $get['tag'] : false);

        $has_tag_table = false;
        $has_direction_table = false;

        if ($tag) {
            $material_tag = Lentatag::find()->where(['name' => $tag])->one();
            if ($material_tag) {
                if (!$has_tag_table) {
                    $items_query->leftJoin('lenta_ref_lentatag', 'lenta_ref_lentatag.lenta_id = lenta.id');
                    $has_tag_table = true;
                }
                $items_query->andWhere(['lenta_ref_lentatag.lentatag_id' => $material_tag->id]);
            }
        }

        // ключевые слова
        $keywordName = (isset($get['keyword']) ? $get['keyword'] : false);
        if ($keywordName) {
            $keyword = Keyword::find()->where(['name' => $keywordName])->one();
            if ($keyword) {
                $items_query->leftJoin('models_keywords', 'models_keywords.entity_id = lenta.id');
                $items_query->andWhere(['models_keywords.keyword_id' => $keyword->id]);
                $items_query->andWhere(['IN', 'models_keywords.entity_model', [Lenta::class, Material::class, News::class, Project::class, Blog::class]]);
            }
        }

        // ищем по заданным кафедрам
        if (!empty($terms['directs'])) {
            if (!$has_direction_table) {
                $items_query->leftJoin('lenta_ref_direction', 'lenta_ref_direction.lenta_id = lenta.id');
                $has_direction_table = true;
            }
            $items_query->andWhere(['OR',
                ['lenta.direction_id' => $terms['directs']],
                ['IN', 'lenta_ref_direction.direction_id', $terms['directs']],
            ]);
        }

        $search_query = htmlentities(trim(Yii::$app->request->get('q')));
        if (!empty($search_query)) {
            if (!$has_tag_table) {
                $items_query->leftJoin('lenta_ref_lentatag', 'lenta_ref_lentatag.lenta_id = lenta.id');
                $has_tag_table = true;
            }
            if (!$has_direction_table) {
                $items_query->leftJoin('lenta_ref_direction', 'lenta_ref_direction.lenta_id = lenta.id');
                $has_direction_table = true;
            }

            $tags_search = ArrayHelper::map(Lentatag::find()->where(['LIKE', 'name', $search_query])->all(), 'id', 'id');
            $direct_search = ArrayHelper::map(Direction::find()->where(['LIKE', 'name', $search_query])->all(), 'id', 'id');

            $query = UserAR::find();
            $roles = ['expert'];
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id');
            $query->leftJoin('profile', 'profile.user_id = user.id');
            $query->andWhere(['IN', 'auth_assignment.item_name', $roles]);
            $query->andWhere(['status' => UserAR::STATUS_ACTIVE]);
            $query->andWhere(['OR',
                ['LIKE', 'CONCAT(`profile`.`surname`," ",`profile`.`name`)', $search_query],
                ['LIKE', 'CONCAT(`profile`.`name`," ",`profile`.`surname`)', $search_query],
            ]);
            $users_search = ArrayHelper::map($query->all(), 'id', 'id');

            $query = UserAR::find();
            $roles = ['exporg'];
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id');
            $query->leftJoin('profile', 'profile.user_id = user.id');
            $query->andWhere(['IN', 'auth_assignment.item_name', $roles]);
            $query->andWhere(['status' => UserAR::STATUS_ACTIVE]);
            $query->andWhere(['LIKE', 'profile.organization_name', $search_query]);
            $orgs_search = ArrayHelper::map($query->all(), 'id', 'id');

            $users_search = array_merge($users_search, $orgs_search);

            $items_query->andWhere(['OR',
                ['LIKE', 'lenta.name', $search_query],
                ['LIKE', 'lenta.content', $search_query],
                ['IN', 'lenta_ref_lentatag.lentatag_id', $tags_search],
                ['IN', 'lenta.direction_id', $direct_search],
                ['IN', 'lenta_ref_direction.direction_id', $direct_search],
                ['IN', 'lenta.author_id', $users_search],
            ]);
        }

        $author_query = intval(trim(Yii::$app->request->get('author_id')));
        if (!empty($author_query) || trim(Yii::$app->request->get('author_id') === '0')) {
            $items_query->andWhere(['lenta.author_id' => $author_query]);
        }

        $lenta_directions_ids = array_unique($items_model::findVisibleForCatalog()->select('lenta.direction_id')->asArray()->column());
        $directions = Direction::find()->where(['visible' => 1, 'stels_direct' => 0])->andWhere(['IN', 'direction.id', $lenta_directions_ids])->orderBy(['name' => SORT_ASC])->all();
        $items_query->andWhere('(start_publish IS NULL) OR (start_publish < NOW())')->andWhere('(end_publish IS NULL) OR (end_publish > NOW())')->distinct()->orderBy(['published' => SORT_DESC]);
        $countQuery = clone $items_query;
        $count_items = $countQuery->count();

        $blocks_query = \app\modules\pages_helper\models\LentaPageBlock::find()->where(['page_id' => $model->id])->andWhere(['visible' => 1])->distinct()->orderBy(['order' => SORT_ASC]);
        $blocks_countQuery = clone $blocks_query;
        $blocks_count_items = $blocks_countQuery->count();

        $pageparams = Yii::$app->request->get();
        unset($pageparams['model']);

        $pages = new Pagination([
            'totalCount' => $count_items,
            'defaultPageSize' => 24,
            'route' => $model->getUrlPath(),
            'params' => $pageparams,
        ]);

        $blocks_pages = new Pagination([
            'totalCount' => $blocks_count_items,
            'defaultPageSize' => 5,
            'route' => $model->getUrlPath(),
            'params' => $pageparams,
        ]);

        $page_num = Yii::$app->request->get('page');

        $first = $image_first = $image_first_mobile = null;

        if (empty($page_num) || strval($page_num) == '1') {
            $image_first = $model->getThumb('image_first', 'main');
            $image_first_mobile = $model->getThumb('image_first_mobile', 'main');
            $first = $model->getFirst_lenta();
        }

        $items = $items_query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        $blocks = $blocks_query->offset($blocks_pages->offset)
            ->limit($blocks_pages->limit)
            ->all();

        // если ajax-запрос
        if (Yii::$app->request->isAjax) {
            // задаем формат ответа
            Yii::$app->response->format = Response::FORMAT_JSON;
            // инициализируем массив ответа
            $data = ['status' => 'fail'];
            // если пользователи найдены
            if (!empty($items)) {
                // рендерим
                $data['count'] = 'Показать ' . $count_items . ' ' . MainHelper::pluralForm($count_items, ['предложение', 'предложения', 'предложений']);
                $data['html'] = $this->renderPartial('_lenta_box', ['image_first' => $image_first, 'image_first_mobile' => $image_first_mobile, 'first' => $first, 'pages' => $pages, 'blocks' => $blocks, 'items' => $items, 'model' => $model]);
                // говорим, что все ОК
                $data['status'] = 'success';
            }
            $data['pager'] = \app\widgets\pagination\LinkPager::widget(['pages' => $pages, 'is_ajax' => true, 'container' => '#lenta_content']);
            // возвращаем ответ
            return $data;
        }

        $ads = \app\modules\pages_helper\models\LentaPageAds::findVisible()->andWhere(['page_id' => $model->id])->orderBy(new Expression('rand()'))->limit(1)->all();

        return $this->render($model->view, ['image_first' => $image_first, 'image_first_mobile' => $image_first_mobile, 'first' => $first, 'ads' => $ads, 'promo_lenta' => $promo_lenta, 'promo_lenta_show_more' => $promo_lenta_show_more, 'items_model' => $items_model, 'model' => $model, 'mode' => $mode, 'pages' => $pages, 'blocks' => $blocks, 'items' => $items, 'directions' => $directions, 'terms' => $terms, 'search_text' => htmlspecialchars($search_query)]);
    }

    public function actionProject($model)
    {
        $items_model = Project::class;
        $mode = 'project';
        return $this->actionLenta($model, $items_model, $mode);
    }

    public function actionMaterial($model)
    {
        $items_model = Material::class;
        $mode = 'material';
        return $this->actionLenta($model, $items_model, $mode);
    }

    public function actionNews($model)
    {
        $items_model = News::class;
        $mode = 'news';
        return $this->actionLenta($model, $items_model, $mode);
    }

    public function actionProjectinner($model)
    {
        return $this->actionLentainner($model);
    }

    public function actionLentainner($model)
    {
        $this->setMeta($model);
        $session = Yii::$app->session;

        $views_array = $session->get('views', []);
        if (!in_array($model->id, $views_array)) {
            array_push($views_array, $model->id);
            $session->set('views', $views_array);
            $model->updateCounters(['views' => 1, 'trending_score' => 1]);
        }

        $lenta_url = Lentapage::find()->where(['model' => Lentapage::class, 'visible' => 1])->one()?->getUrlPath();
        $return_title = 'Вернуться';
        switch ($model->lentatype) {
            case Blog::LENTATYPE:
                $catalog = LentaBlogpage::find()->where(['model' => LentaBlogpage::class, 'visible' => 1])->one();
                $same_type = Blog::findVisible()->andWhere(['not in', 'lenta.id', [$model->id]])->orderBy(new Expression('rand()'))->limit(20)->all();
                $more_items = Lenta::findVisible()->andWhere(['not in', 'lenta.id', [$model->id]])->orderBy(new Expression('rand()'))->limit(20)->all();
                $same_type_title = 'Еще в Блоге';
                $same_type_url = $catalog->getUrlPath();
                $return_title = 'Блог';
                break;
            case Material::LENTATYPE:
                // не индексируем материалы
                $this->getView()->registerMetaTag(['name' => 'robots', 'content' => 'noindex'], 'robots');

                $catalog = LentaMaterialpage::find()->where(['model' => LentaMaterialpage::class, 'visible' => 1])->one();
                $same_type = Material::findVisible()->andWhere(['not in', 'lenta.id', [$model->id]])->andWhere(['author_id' => $model->author_id])->orderBy(new Expression('rand()'))->limit(20)->all();
                $more_items = Lenta::findVisible()->andWhere(['not in', 'lenta.id', [$model->id]])->orderBy(new Expression('rand()'))->limit(20)->all();
                $same_type_title = 'Другие материалы эксперта';
                $same_type_url = $model->author ? $model->author->getUrlPath() . '#tab-lenta' : '';
                $return_title = 'База знаний';
                break;
            case Project::LENTATYPE:
                $catalog = LentaProjectpage::find()->where(['model' => LentaProjectpage::class, 'visible' => 1])->one();
                $same_type = Project::findVisible()->andWhere(['not in', 'lenta.id', [$model->id]])->andWhere(['author_id' => $model->author_id])->orderBy(new Expression('rand()'))->limit(20)->all();
                $more_items = Lenta::findVisible()->andWhere(['not in', 'lenta.id', [$model->id]])->orderBy(new Expression('rand()'))->limit(20)->all();
                $same_type_title = 'Другие проекты эксперта';
                $same_type_url = $model->author ? $model->author->getUrlPath() . '#tab-lenta' : '';
                $return_title = 'Портфолио';
                break;
            case News::LENTATYPE:
                $catalog = LentaNewspage::find()->where(['model' => LentaNewspage::class, 'visible' => 1])->one();
                $same_type = News::findVisible()->andWhere(['not in', 'lenta.id', [$model->id]])->orderBy(new Expression('rand()'))->limit(20)->all();
                $more_items = Lenta::findVisible()->andWhere(['not in', 'lenta.id', [$model->id]])->orderBy(new Expression('rand()'))->limit(20)->all();
                $same_type_title = 'Еще в Новостях';
                $same_type_url = $catalog->getUrlPath();
                $return_title = 'Новости';
                break;
            default:
                $catalog = Lentapage::find()->where(['model' => Lentapage::class, 'visible' => 1])->one();
                $same_type = Lenta::findVisible()->andWhere(['not in', 'lenta.id', [$model->id]])->orderBy(new Expression('rand()'))->limit(20)->all();
                $more_items = Lenta::findVisible()->andWhere(['not in', 'lenta.id', [$model->id]])->orderBy(new Expression('rand()'))->limit(20)->all();
                $same_type_title = 'Еще в Ленте';
                $same_type_url = $catalog->getUrlPath();
                $return_title = 'Вернуться';
                break;
        }
        $return_url = $catalog->getUrlPath();

        $content = $model->content;
        // подставляем подсказки в контент
        $regexp = '/&?n?b?s?p?;?<span class="lenta_tooltip" data-tooltip="([^"]+)">([^<]+)<\/span>&?n?b?s?p?;?/u';
        $content = preg_replace($regexp, '<div class="tooltip-wrapper"><a href="javascript:void(0);" class="tooltip-trigger">$2</a><div class="tooltip">$1</div>', $content);

        $side_array = [];
        $side_users_with_tags = '';

        // не показываем автора в блоге
        if ($model->lentatype != Blog::LENTATYPE) {
            $side_author = $model->author;
        } else {
            $side_author = false;
        }
        // добавляем автора
        if ($side_author) {
            $side_users_with_tags .= \app\modules\lenta\widgets\lentainner\LentaInnerWidget::widget(['items' => [$side_author], 'view' => 'authors_side', 'params' => ['title' => 'Автор',]]);
        }
        $side_experts = $model->expertsInner;
        // добавляем экспертов в боковой виджет
        if (!empty($side_experts)) {
            $side_users_with_tags .= \app\modules\lenta\widgets\lentainner\LentaInnerWidget::widget(['items' => $side_experts, 'view' => 'experts_side']);
        }
        $side_editors = $model->editors;
        // добавляем редакторов в боковой виджет
        if (!empty($side_editors)) {
            $side_users_with_tags .= \app\modules\lenta\widgets\lentainner\LentaInnerWidget::widget(['items' => $side_editors, 'view' => 'editors_side', 'params' => ['title' => 'Редактор' . (count($side_editors) > 1 ? 'ы' : '') . ' блога',]]);
        }
        // теги приклеиваем к редакторам
        if (!empty($model->visibleTags)) {
            $side_users_with_tags .= '<div class="expert_item-tags">';
            foreach ($model->visibleTags as $item) {
                $side_users_with_tags .= '<a class="tag" href="' . $return_url . '?tag=' . urlencode($item->name) . '" data-tagid="' . $item->id . '" data-tagname="' . $item->name . '"><b class="tag-hovered">' . $item->name . '</b><span>' . $item->name . '</span></a>';
            }
            $side_users_with_tags .= '</div>';
        }
        // ключевые слова приклеиваем к редакторам
        if (!empty($model->keywords)) {
            $side_users_with_tags .= '<div class="expert_item-tags">';
            foreach ($model->keywords as $item) {
                $side_users_with_tags .= '<a class="tag" href="' . $return_url . '?keyword=' . urlencode($item->name) . '" data-tagid="' . $item->id . '" data-tagname="' . $item->name . '"><b class="tag-hovered">' . $item->name . '</b><span>' . $item->name . '</span></a>';
            }
            $side_users_with_tags .= '</div>';
        }

        // проверяем есть ли в контенте материалы, вставленные через автокомплит
        $regexp = '/&?n?b?s?p?;?<span class="lenta_(gallery|quote|stage|events|service)" data-target="(\d+)">([a-zA-ZабвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ\s\d&;:\-_]+)<\/span>&?n?b?s?p?;?/u';

        $matches = [];
        preg_match_all($regexp, $content, $matches, PREG_SET_ORDER);

        // текстовые блоки контента, разделенные вставленными из автокомплита блоками
        $content_texts = preg_split($regexp, $content);
        if (empty($content_texts) || $content_texts == false) {
            array_push($content_texts, $content);
        }

        // баннер на странице
        // приклеивается к чиплам если нет дополнительного бокового контента
        $banners_ids = ArrayHelper::map($model->pagebanner, 'id', 'id');
        // случайный баннер из выбранный внутри записи ленты
        $banner_content = \app\modules\banner\widgets\banner\PageBannerWidget::widget(['random_from_ids' => $banners_ids, 'forced_desktop' => true]);
        if (!empty($banners_ids) && !empty($banner_content)) {
            if (count($content_texts) > 1) {
                array_push($side_array, $banner_content);
            } else {
                $side_users_with_tags .= $banner_content;
            }
        }
        // else{
        //    //случайный баннер с включенным отображением в текущем типе записи ленты
        //    $banner_content = \app\modules\banner\widgets\banner\PageBannerWidget::widget(['inner_page'=>$model->lentatype,'id'=>$model->id]);
        //    if(!empty($banner_content)){
        //        if (count($content_texts)>1){
        //            array_push($side_array, $banner_content);
        //        }
        //        else{
        //            $side_users_with_tags .= $banner_content;
        //        }
        //    }
        // }

        if (count($content_texts) > 1 && !$model->hide_side_same_tags) {
            // берем теги статьи
            $tags_ids = ArrayHelper::map($model->visibleTags, 'id', 'id');
            if ($model->lentatype == News::LENTATYPE) {
                // только для каталога новостей - ближайшие новости с тем же тегом или ближайшие по времени
                $news_same_count = 3;
                $same_tags = Lenta::findVisible()
                    ->andWhere(['lentatype' => $model->lentatype])
                    ->leftJoin('lenta_ref_lentatag', 'lenta_ref_lentatag.lenta_id = lenta.id')
                    ->andWhere(['in', 'lentatag_id', $tags_ids])
                    ->andWhere(['not in', 'lenta.id', [$model->id]])
                    ->orderBy(['published' => SORT_DESC])
                    ->limit($news_same_count)->all();
                // если с этими тегами нет - выводим ближайшие по времени
                $more = $news_same_count - count($same_tags);
                if ($more > 0) {
                    $more_items = Lenta::findVisible()
                        ->andWhere(['lentatype' => $model->lentatype])
                        ->andWhere(['not in', 'lenta.id', [$model->id]])
                        ->andWhere(['not in', 'lenta.id', array_keys(ArrayHelper::map($same_tags, 'id', 'id'))])
                        ->orderBy(['published' => SORT_DESC])
                        ->limit($more)->all();
                    $same_tags = array_merge($same_tags, $more_items);
                }
                if (!empty($same_tags)) {
                    array_push($side_array, \app\modules\lenta\widgets\lentainner\LentaInnerWidget::widget(['items' => $same_tags, 'view' => 'readmore1']));
                }
            } else {
                // для всех остальных - проверяем есть ли статьи с такими тегами
                $same_tags = Lenta::findVisible()
                    ->andWhere(['lentatype' => $model->lentatype])
                    ->leftJoin('lenta_ref_lentatag', 'lenta_ref_lentatag.lenta_id = lenta.id')
                    ->andWhere(['in', 'lentatag_id', $tags_ids])
                    ->andWhere(['not in', 'lenta.id', [$model->id]])
                    ->orderBy(new Expression('rand()'))
                    ->limit(5)->all();

                // добавляем статьи для блоков с чередующимися лейаутами и добавляем виджеты в массив для вывода
                $same_tags_slice1 = [];
                if (!is_null($element = array_shift($same_tags))) {
                    array_push($same_tags_slice1, $element);
                }
                if (!is_null($element = array_shift($same_tags))) {
                    array_push($same_tags_slice1, $element);
                }
                if (!empty($same_tags_slice1)) {
                    array_push($side_array, \app\modules\lenta\widgets\lentainner\LentaInnerWidget::widget(['items' => $same_tags_slice1, 'view' => 'readmore1']));
                }
                $same_tags_slice2 = [];
                if (!is_null($element = array_shift($same_tags))) {
                    array_push($same_tags_slice2, $element);
                }
                if (!empty($same_tags_slice2)) {
                    array_push($side_array, \app\modules\lenta\widgets\lentainner\LentaInnerWidget::widget(['items' => $same_tags_slice2, 'view' => 'readmore2']));
                }
                $same_tags_slice3 = [];
                if (!is_null($element = array_shift($same_tags))) {
                    array_push($same_tags_slice3, $element);
                }
                if (!is_null($element = array_shift($same_tags))) {
                    array_push($same_tags_slice3, $element);
                }
                if (!empty($same_tags_slice3)) {
                    array_push($side_array, \app\modules\lenta\widgets\lentainner\LentaInnerWidget::widget(['items' => $same_tags_slice3, 'view' => 'readmore1']));
                }
            }

        }

        if (count($content_texts) > 1 && !$model->hide_side_service) {
            // боковой виджет с сервисом
            $side_service = LentaInnerService::getServiceBlock($model->id, true, 0);
            if (!empty($side_service)) {
                array_push($side_array, $side_service);
            }
        }

        // подсчет отступов между боковыми блоками для их равномерного распределения
        if (count($side_array)) {
            $side_offset = intval(count($content_texts) / (count($side_array) + 1));
            if ($side_offset < 1) {
                $side_offset = 1;
            }
        } else {
            $side_offset = 1;
        }

        // заполняем контент блоками из автокомплита и боковыми блоками
        $content = '';
        $mobile_banner_showed = false;
        foreach ($content_texts as $i => $text) {
            $content .= '<div class="blog-page-text-wrapper"><div class="blog-page-text">';
            $content .= $text;
            $content .= '</div>';
            // равномрно распределяем боковые блоки, оставляем отступ после редакторов
            if (($i + 1) % $side_offset == 0 && (empty($side_users_with_tags) || $i != 0)) {
                // в мобильной версии элементы скрыты классом desktop-visible
                $content .= '<div class="blog-right-column desktop-visible">';
                $content .= empty($side_array) ? '' : array_shift($side_array);
                $content .= '</div>';
            }
            $content .= '</div>';
            $match = empty($matches) ? null : array_shift($matches);
            if ($match) {
                $content .= '<div class="blog-page-text-wrapper">';
                $block_type = $match[1];
                $block_id = $match[2];
                switch ($block_type) {
                    case 'gallery':
                        $block = \app\modules\lenta\models\LentaInnerGallery::find()->where(['visible' => 1])->andWhere(['id' => $block_id])->andWhere(['page_id' => $model->id])->one();
                        $content .= \app\modules\lenta\widgets\lentainner\LentaInnerWidget::widget(['items' => $block, 'view' => 'gallery']);
                        break;
                    case 'quote':
                        $block = \app\modules\lenta\models\LentaInnerQuote::find()->where(['visible' => 1])->andWhere(['id' => $block_id])->andWhere(['page_id' => $model->id])->one();
                        $content .= \app\modules\lenta\widgets\lentainner\LentaInnerWidget::widget(['items' => $block, 'view' => 'quote', 'layout' => $block->layout]);
                        break;
                    case 'stage':
                        $block = \app\modules\lenta\models\LentaInnerStage::find()->where(['visible' => 1])->andWhere(['id' => $block_id])->andWhere(['page_id' => $model->id])->one();
                        $content .= \app\modules\lenta\widgets\lentainner\LentaInnerWidget::widget(['items' => $block->elements, 'view' => 'stage', 'layout' => $block->layout]);
                        break;
                    case 'events':
                        $block = \app\modules\lenta\models\LentaInnerEvents::findVisible($model->id)->andWhere(['lentainnerevents.id' => $block_id])->one();
                        // if(!empty($block)){
                        $content .= \app\modules\lenta\widgets\lentainner\LentaInnerWidget::widget(['items' => $block->elements, 'view' => 'events', 'show_empty' => true]);
                        // }
                        break;
                    case 'service':
                        $block = LentaInnerService::getServiceBlock($model->id, false, $block_id);
                        if (!empty($block)) {
                            $content .= $block;
                        }
                        break;
                }
                $content .= '</div>';
                // добавляем мобильный баннер после первого блока если он есть
                $banner_content = \app\modules\banner\widgets\banner\PageBannerWidget::widget(['random_from_ids' => $banners_ids, 'forced_mobile' => true]);
                if (!$mobile_banner_showed && !empty($banners_ids) && !empty($banner_content) && count($content_texts) > 1) {
                    $mobile_banner_showed = true;
                    $content .= $banner_content;
                }
            }
            // добавляем мобильный баннер в конце статьи если нет блоков внутри статьи
            $banner_content = \app\modules\banner\widgets\banner\PageBannerWidget::widget(['random_from_ids' => $banners_ids, 'forced_mobile' => true]);
            if (!$mobile_banner_showed && !empty($banners_ids) && !empty($banner_content) && count($content_texts) < 2) {
                $mobile_banner_showed = true;
                $content .= $banner_content;
            }
        }

        $ads = \app\modules\pages_helper\models\LentaPageAds::findVisible()->andWhere(['page_id' => $catalog->id])->orderBy(new Expression('rand()'))->limit(1)->all();
        $category_tags_ids = ArrayHelper::map($catalog->pageTags, 'id', 'id');
        $category_tags = LentaTag::find()->where(['IN', 'id', $category_tags_ids])->orderBy(new Expression('rand()'))->limit(20)->all();
        $lentaslider = \app\modules\lenta\models\LentaInnerSlider::findVisible($model->id)->all();
        return $this->render($model->view, ['lentaslider' => $lentaslider, 'return_url' => $return_url, 'return_title' => $return_title, 'same_type_title' => $same_type_title, 'same_type_url' => $same_type_url, 'lenta_url' => $lenta_url, 'content' => $content, 'side_users_with_tags' => $side_users_with_tags, 'model' => $model, 'catalog' => $catalog, 'ads' => $ads, 'same_type' => $same_type, 'more_items' => $more_items, 'category_tags' => $category_tags]);
    }

    public function actionBloginner($model)
    {
        return $this->actionLentainner($model);
    }

    public function actionMaterialinner($model)
    {
        return $this->actionLentainner($model);
    }

    public function actionNewsinner($model)
    {
        return $this->actionLentainner($model);
    }

    public function actionEduprog($model)
    {
        // выключатель ДПО
        if (!Yii::$app->params['enable_dpo']) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        // устанавливаем мете-данные для страницы из модели мероприятия
        $this->setMeta($model);

        // параметры запроса (для фильтрации)
        $get = Yii::$app->request->get();

        // базовые ограничения на показ зависят от того - будет ли фильтрация по дате.
        if (!empty($get) && (!empty($get['start_mounth']))) {
            // без учета даты проведения мероприятия
            $items_query = Eduprog::findVisible();
        } else {
            // отображает только текущие и будущие мероприятия (и корпоративные)
            $items_query = Eduprog::findVisibleForCatalog();
        }

        $prices_list = [
            '20000' => 'до 20 000 ₽',
            '50000' => 'до 50 000 ₽',
            '100000' => 'до 100 000 ₽',
            '150000' => 'до 150 000 ₽',
            '200000' => 'до 200 000 ₽',
            '250000' => 'до 250 000 ₽',
            '300000' => 'до 300 000 ₽',
            'over' => '300 000+  ₽',
        ];

        $duration_list = [
            '2' => 'до 2 дней',
            '31' => 'до 1 месяца',
            '92' => 'до 3 месяцев',
            '183' => 'до 6 месяцев',
            '365' => 'до 12 месяцев',
            '548' => 'до 18 месяцев',
            '730' => 'до 24 месяцев',
            'over' => '24 месяца +',
        ];

        $hours_list = [
            '16' => 'до 16 часов',
            '72' => 'до 72 часов',
            '100' => 'до 100 часов',
            '250' => 'до 250 часов',
            '600' => 'до 600 часов',
            '1000' => 'до 1 000 часов',
            'over' => '1 000+ часов',
        ];

        $eduprog_directions_ids = array_unique(Eduprog::findVisibleForCatalog()->select('eduprog.direction_id')->asArray()->column());
        $directions_list = ArrayHelper::map(Direction::find()->where(['visible' => 1, 'stels_direct' => 0])->andWhere(['IN', 'direction.id', $eduprog_directions_ids])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');

        $format_list = Eduprog::getFormatList();

        $eduprog_category_ids = array_unique(Eduprog::findVisibleForCatalog()->select('eduprog.category_id')->asArray()->column());
        $category_list = ArrayHelper::map(Educategory::find()->where(['visible' => 1])->andWhere(['IN', 'id', $eduprog_category_ids])->orderBy(['name' => SORT_ASC])->all(), 'id', 'name');

        $eduprog_cities_ids = array_unique(Eduprog::findVisibleForCatalog()->select('eduprog.city_id')->asArray()->column());
        // получаем список городов
        $cities_list = ArrayHelper::map(City::find()->where(['visible' => 1])->andWhere(['IN', 'id', $eduprog_cities_ids])->orderBy(['big_city' => SORT_DESC, 'name' => SORT_ASC])->all(), 'id', 'name');

        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!//
        $promo_items_ids = [];
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!//

        // заполнение/обнуление фильтров
        // теги
        $tag = (isset($get['tag']) ? $get['tag'] : false);
        // флаги о джойне таблиц
        $has_tag_table = false;

        if ($tag) {
            $events_tag = Eventstag::find()->where(['name' => $tag])->one();
            if ($events_tag) {
                $items_query->leftJoin('eduprog_ref_eventstag', 'eduprog_ref_eventstag.eduprog_id = eduprog.id');
                $items_query->andWhere(['eduprog_ref_eventstag.eventstag_id' => $events_tag->id]);
                $has_tag_table = true;
            }
        }
        // ключевые слова
        $keywordName = (isset($get['keyword']) ? $get['keyword'] : false);
        if ($keywordName) {
            $keyword = Keyword::find()->where(['name' => $keywordName])->one();
            if ($keyword) {
                $items_query->leftJoin('models_keywords', 'models_keywords.entity_id = eduprog.id');
                $items_query->andWhere(['models_keywords.keyword_id' => $keyword->id]);
                $items_query->andWhere(['models_keywords.entity_model' => Eduprog::class]);
            }
        }
        $terms = Yii::$app->request->get();
        $order_sets = false;

        if (isset($terms['q']) && !empty($terms['q'])) {

            if (!$has_tag_table) {
                $items_query->leftJoin('eduprog_ref_eventstag', 'eduprog_ref_eventstag.eduprog_id = eduprog.id');
                $has_tag_table = true;
            }

            $tags_search = ArrayHelper::map(Eventstag::find()->where(['LIKE', 'name', $terms['q']])->all(), 'id', 'id');
            $direct_search = ArrayHelper::map(Direction::find()->where(['LIKE', 'name', $terms['q']])->all(), 'id', 'id');
            $city_search = ArrayHelper::map(City::find()->where(['LIKE', 'name', $terms['q']])->all(), 'id', 'id');
            $category_search = ArrayHelper::map(Educategory::find()->where(['LIKE', 'name', $terms['q']])->all(), 'id', 'id');

            $query = UserAR::find();
            $roles = ['expert'];
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id');
            $query->leftJoin('profile', 'profile.user_id = user.id');
            $query->andWhere(['IN', 'auth_assignment.item_name', $roles]);
            $query->andWhere(['status' => UserAR::STATUS_ACTIVE]);
            $query->andWhere(['OR',
                ['LIKE', 'CONCAT(`profile`.`surname`," ",`profile`.`name`)', $terms['q']],
                ['LIKE', 'CONCAT(`profile`.`name`," ",`profile`.`surname`)', $terms['q']],
            ]);
            $users_search = ArrayHelper::map($query->all(), 'id', 'id');

            $query = UserAR::find();
            $roles = ['exporg'];
            $query->leftJoin('auth_assignment', 'auth_assignment.user_id = user.id');
            $query->leftJoin('profile', 'profile.user_id = user.id');
            $query->andWhere(['IN', 'auth_assignment.item_name', $roles]);
            $query->andWhere(['status' => UserAR::STATUS_ACTIVE]);
            $query->andWhere(['LIKE', 'profile.organization_name', $terms['q']]);
            $orgs_search = ArrayHelper::map($query->all(), 'id', 'id');

            $users_search = array_merge($users_search, $orgs_search);

            $items_query->andWhere(['OR',
                ['LIKE', 'eduprog.name', $terms['q']],
                ['LIKE', 'eduprog.content', $terms['q']],
                ['LIKE', 'eduprog.suits_for', $terms['q']],
                ['LIKE', 'eduprog.learn', $terms['q']],
                ['IN', 'eduprog_ref_eventstag.eventstag_id', $tags_search],
                ['IN', 'eduprog.direction_id', $direct_search],
                ['IN', 'eduprog.category_id', $category_search],
                ['IN', 'eduprog.author_id', $users_search],
                ['IN', 'eduprog.city_id', $city_search],
            ]);
        } else {
            $terms['q'] = '';
        }

        // старт программы
        if (isset($terms['start_mounth']) && !empty($terms['start_mounth']) && is_array($terms['start_mounth'])) {
            // у корпоративных программ нет даты начала, но они должны попадать в выборку
            $items_query->andWhere(['OR',
                ['IN', 'MONTH(`eduprog`.`date_start`)', $terms['start_mounth']],
                ['eduprog.is_corporative' => 1],
                ['eduprog.is_waiting_list' => 1],
            ]);
            // при выводе программ корпоративные должны отображаться последними
            $items_query->orderBy(['is_corporative' => SORT_ASC, 'is_waiting_list' => SORT_ASC, 'eduprog.date_start' => SORT_ASC]);
            $order_sets = true;
        } else {
            $terms['start_mounth'] = [];
        }

        // если отмечена галочка registration_open - то показываем И корпоративные, И с открытой регистрацией с датой завершения
        if (isset($get['registration_open']) && $get['registration_open'] == '1' && $get['is_corporative'] == '0') {
            $items_query->joinWith('tariff');
            $items_query->andWhere(['NOT', ['eduprog.date_stop_sale' => null]]);
            $items_query->andWhere(['>', 'eduprog.date_stop_sale', date('Y-m-d', time())]);
            $items_query->andWhere(['OR',
                ['registration_open' => $get['registration_open']],
                ['is_corporative' => 1],
                ['is_waiting_list' => 1],
            ]);
            $items_query->andWhere(['>', 'remainTickets', 0]);
            // если отмечена галочка registration_open - то показываем И корпоративные, И с открытой регистрацией
        } elseif (isset($get['is_corporative']) && $get['is_corporative'] == '1' && $get['registration_open'] == '1') {
            $items_query->andWhere(['OR',
                ['registration_open' => $get['registration_open']],
                ['is_corporative' => 1]
            ]);
            // иначе, если отмечена галочка is_corporative, отображаем только корпоративные.
        } elseif (isset($get['is_corporative']) && $get['is_corporative'] == '1') {
            $items_query->andWhere(['is_corporative' => $get['is_corporative']]);
        }

        // цена
        if (isset($terms['price']) && !empty($terms['price'])) {
            // у корпоративных программ нет тарифов, но даже при фильтрации по ценам они должны попадать в выборку.
            $items_query->leftJoin('eduprog_forms', 'eduprog_forms.eduprog_id = eduprog.id');
            $items_query->leftJoin('eduprog_tariff', 'eduprog_tariff.eduprogform_id = eduprog_forms.id');
            $items_query->leftJoin('eduprog_price', 'eduprog_price.tariff_id = eduprog_tariff.id');
            if ($terms['price'] == 'over') {
                $price_condition = ['>', 'eduprog_price.price', 300000];
            } else {
                $price_condition = ['<=', 'eduprog_price.price', $terms['price']];
            }

            $items_query->andWhere(['OR',
                ['eduprog.is_corporative' => 1],
                ['eduprog.is_waiting_list' => 1],
                ['AND',
                    ['eduprog_forms.visible' => 1],
                    ['eduprog_tariff.visible' => 1],
                    ['>', 'eduprog_tariff.remainTickets', 0],
                    'eduprog_tariff.start_publish < CURDATE() AND eduprog_tariff.end_publish > CURDATE()',
                    'eduprog_price.start_publish < CURDATE() AND eduprog_price.end_publish > CURDATE()',
                    $price_condition
                ]
            ]);
        } else {
            $terms['price'] = '';
        }

        // формат проведения
        if (isset($terms['formats']) && !empty($terms['formats']) && is_array($terms['formats'])) {
            $items_query->andWhere(['IN', 'eduprog.format', $terms['formats']]);
        } else {
            $terms['formats'] = [];
        }

        // вид программы
        if (isset($terms['category']) && !empty($terms['category']) && is_array($terms['category'])) {
            $items_query->andWhere(['IN', 'eduprog.category_id', $terms['category']]);
        } else {
            $terms['category'] = [];
        }

        // продолжительность
        if (isset($terms['duration']) && !empty($terms['duration'])) {
            // у корпоративных программ нет даты начала и даты завершения, но при фильтрации они должны попадать в выборку
            if ($terms['duration'] == 'over') {
                $items_query->andWhere(['OR',
                    ['eduprog.is_corporative' => 1],
                    ['eduprog.is_waiting_list' => 1],
                    ['>', 'DATEDIFF(`eduprog`.`date_stop`,`eduprog`.`date_start`)', '730']
                ]);
            } else {
                $items_query->andWhere(['OR',
                    ['eduprog.is_corporative' => 1],
                    ['eduprog.is_waiting_list' => 1],
                    ['<=', 'DATEDIFF(`eduprog`.`date_stop`,`eduprog`.`date_start`)', (int)$terms['duration']],
                ]);
            }
        } else {
            $terms['duration'] = '';
        }

        // продолжительность
        if (isset($terms['hours']) && !empty($terms['hours'])) {
            if ($terms['hours'] == 'over') {
                $items_query->andWhere(['>', 'eduprog.hours', 1000]);
            } else {
                $items_query->andWhere(['<=', 'eduprog.hours', $terms['hours']]);
            }
        } else {
            $terms['hours'] = '';
        }

        // кафедры
        if (isset($terms['directions']) && !empty($terms['directions']) && is_array($terms['directions'])) {
            $items_query->andWhere(['IN', 'eduprog.direction_id', $terms['directions']]);
        } else {
            $terms['directions'] = [];
        }

        // города
        if (isset($terms['city']) && !empty($terms['city']) && is_array($terms['city'])) {
            $items_query->andWhere(['IN', 'eduprog.city_id', $terms['city']]);
        } else {
            $terms['city'] = [];
        }


        /*

        //промо мероприятия
        $promo_items_ids = $model->promo_events_ids;
        if(!empty($model->promo_events_ids)){
            $items_query->addOrderBy([new \yii\db\Expression('FIELD (events.id, ' . implode(',', $promo_items_ids) . ') DESC')]);
        }
        if($price_filter_priority){
            $items_query->addOrderBy([new \yii\db\Expression('FIELD (tariff.free, 0) DESC')]);
        }
        */

        $items_query->distinct();

        if (!$order_sets) {
            // корпоративные программы должны выводиться в конце.
            // 1) сортируем по признаку registration_open (программы с открытой регистрацией выводятся первыми)
            // 2) среди программ с открытой регистрацией корпоративных программ не будет, т.к. на корпоративные программы регистрация всегда закрыта
            // 3) среди программ с закрытой регистрацией сортируем по признаку is_corporative (корпоративные программы в конце)
            // 4) и уже среди всех сформированных групп программ сортируем по дате окончания программы:
            $items_query->orderBy(['registration_open' => SORT_DESC, 'is_corporative' => SORT_ASC, 'is_waiting_list' => SORT_ASC, 'date_start' => SORT_ASC]);
        }
        // пагинация
        $countQuery = clone $items_query;
        $count_eduprog = $countQuery->count();

        $pageparams = Yii::$app->request->get();
        unset($pageparams['model']);

        $pages = new Pagination([
            'totalCount' => $count_eduprog,
            'defaultPageSize' => 25,
            'route' => $model->getUrlPath(),
            'params' => $pageparams,
        ]);

        $items = $items_query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        // если ajax-запрос
        if (Yii::$app->request->isAjax) {
            // задаем формат ответа
            Yii::$app->response->format = Response::FORMAT_JSON;
            // инициализируем массив ответа
            $data = ['status' => 'fail'];
            // если пользователи найдены
            if (!empty($items)) {
                // рендерим
                $data['count'] = 'Показать ' . $count_eduprog . ' ' . MainHelper::pluralForm($count_eduprog, ['предложение', 'предложения', 'предложений']);
                $data['html'] = $this->renderPartial('_eduprog_box', ['items' => $items, 'model' => $model, 'promo_items_ids' => $promo_items_ids]);
                // говорим, что все ОК
                $data['status'] = 'success';
            }
            $data['pager'] = \app\widgets\pagination\LinkPager::widget(['pages' => $pages, 'is_ajax' => true, 'container' => '#all-eduprog-cards']);
            // возвращаем ответ
            return $data;
        }

        return $this->render(
            $model->view,
            [
                'model' => $model,
                'items' => $items,
                'prices_list' => $prices_list,
                'duration_list' => $duration_list,
                'hours_list' => $hours_list,
                'directions_list' => $directions_list,
                'format_list' => $format_list,
                'category_list' => $category_list,
                'cities_list' => $cities_list,
                'promo_items_ids' => $promo_items_ids,
                'terms' => $terms,
                'pages' => $pages,
            ]
        );
    }

    public function actionEduproginner($model, $preview = false)
    {
        // выключатель ДПО
        if (!Yii::$app->params['enable_dpo']) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }

        if ($preview) {
            // проверить доступность предпросмотра для пользователя. Просмотреть может только админ или автор программы ДПО
            if (Yii::$app->user->isGuest) {
                $preview = false;
            }
            $user = Yii::$app->user->identity->userAR;
            if (!in_array($user->role, ['admin', 'expert', 'exporg'])) {
                $preview = false;
            }

            if (($user->role != 'admin') and ($user->id != $model->author_id)) {
                $preview = false;
            }
        }

        $this->setMeta($model);
        $this->layout = '@app/views/layouts/eventpage';
        $eduprog_catalog = EduprogPage::find()->where(['model' => EduprogPage::class, 'visible' => 1])->one();

        /* кнопка в шапке сайта */
        $button = [
            'name' => 'Каталог образовательных программ',
            'link' => ($eduprog_catalog ? $eduprog_catalog->getUrlPath() : false)
        ];
        $this->getView()->params['button_head'] = $button;

        /*
        В блоке выводятся ближайшие программы, у которых есть билеты к продаже. Ближайшие мероприятия выбираются с датой завершения позже текущего дня и в порядке возрастания даты завершения. В блоке выводить 8 программ.
        */
        $closest_eduprog = Eduprog::findVisibleForCatalog()
            // мероприятие с регистрацией
            ->andWhere(['eduprog.registration_open' => 1])
            // не содержит текущее мероприятие
            ->andWhere(['!=', 'eduprog.id', $model->id])
            // форма тарифа платная и активная
            ->orderBy('eduprog.date_stop ASC')->limit(8)->all();

        $footer_banner = \app\modules\pages_helper\models\LentaPageAds::findVisible()->andWhere(['page_id' => $eduprog_catalog->id])->orderBy(new Expression('rand()'))->one();

        return $this->render($model->view, [
            'model' => $model,
            'preview' => $preview,
            'eduprog_catalog' => $eduprog_catalog,
            'closest_eduprog' => $closest_eduprog,
            'footer_banner' => $footer_banner,
        ]);
    }

    public function actionEducontractinner($model)
    {
        // выключатель ДПО
        if (!Yii::$app->params['enable_dpo']) {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
        if (!$model->eduprog->canPublish()) {
            throw new \yii\web\NotFoundHttpException('Договор не актуален');
        }
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model]);
    }

    public function actionLabs($model)
    {
        $this->setMeta($model);
        $labs = \app\modules\labs\models\Labs::find()->where(['visible' => 1])->orderBy(['order' => SORT_ASC, 'name' => SORT_ASC])->all();
        return $this->render($model->view, ['model' => $model, 'labs' => $labs]);
    }

    public function actionLabsinner($model)
    {
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model]);
    }

    public function actionCases($model)
    {
        $this->setMeta($model);
        $cases = Cases::find()->where(['visible' => 1])->orderBy(['order' => SORT_ASC, 'name' => SORT_ASC])->all();
        return $this->render($model->view, ['model' => $model, 'cases' => $cases]);
    }

    public function actionCasesinner($model)
    {
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model]);
    }

    public function actionAdventCalendar($model)
    {
        $this->layout = '@app/views/layouts/new-years';
        $this->setMeta($model);
        return $this->render($model->view, ['model' => $model]);
    }

    /** В начале списка выводит активные мероприятия */
    private function canSaleEventsFirst(array $userEvents): array
    {
        $openEvents = [];
        /** @var Events[] $userEvents */
        foreach ($userEvents as $n => $event) {
            if ($event->canSale() && !$event->hasNotTicketsForSale() && strtotime($event->event_date) > strtotime(date('Y-m-d'))) {
                $openEvents[] = $event;
                unset($userEvents[$n]);
            }
        }

        return array_merge($openEvents, $userEvents);
    }
}
