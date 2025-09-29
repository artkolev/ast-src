<?php

namespace app\controllers;

use app\components\DeepController;
use app\modules\admin\components\FilestoreModel;
use app\modules\admin\components\SecureFilestoreModel;
use app\modules\pages\models\Page;
use app\modules\reference\models\City;
use kartik\mpdf\Pdf;
use Yii;
use yii\web\Response;

class SiteController extends DeepController
{
    /* ТОЛЬКО ДЛЯ ОБЩИХ ВЫЗОВОВ */

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'ajaxValidate' => [
                'class' => 'app\actions\ValidateAction',
            ],
            'ajaxSave' => [
                'class' => 'app\actions\SaveAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return false;
    }

    /* возвращает файл не показывая путь к нему */
    public function actionPrettyfile($file_id)
    {
        // $file_id = $_GET['file'];
        $file_model = FilestoreModel::findOne((int)$file_id);
        if ($file_model) {
            $file = Yii::getAlias('@webroot') . '/' . $file_model->src;
            if (file_exists($file)) {
                $tmp_var = explode('.', $file_model->src);
                $ext = end($tmp_var);
                // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
                // если этого не сделать файл будет читаться в память полностью!
                if (ob_get_level()) {
                    ob_end_clean();
                }
                // заставляем браузер показать окно сохранения файла
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header("Content-Disposition: attachment; filename=\"" . $file_model->name . '.' . $ext . "\"");
                header('Content-Length: ' . filesize($file));
                // читаем файл и отправляем его пользователю
                readfile($file);
                exit;
            }
        }
    }

    public function actionSecurefile($file_id)
    {
        $file_model = SecureFilestoreModel::findOne((int)$file_id);
        if ($file_model) {
            if (!Yii::$app->user->isGuest && (in_array(Yii::$app->user->id, $file_model->allowedUsersIds) || in_array(Yii::$app->user->identity->userAR->role, ['admin', 'mks']))) {
                $file = Yii::getAlias('@app') . '/' . $file_model->src;
                if (file_exists($file)) {
                    $tmp_var = explode('.', $file_model->src);
                    $ext = end($tmp_var);
                    // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
                    // если этого не сделать файл будет читаться в память полностью!
                    if (ob_get_level()) {
                        ob_end_clean();
                    }
                    // заставляем браузер показать окно сохранения файла
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header("Content-Disposition: attachment; filename=\"" . $file_model->name . "\"");
                    header('Content-Length: ' . filesize($file));
                    // читаем файл и отправляем его пользователю
                    readfile($file);
                    exit;
                }
            }
        }
    }

    public function actionLogintest($login)
    {
        if (!Yii::$app->user->isGuest) {
            $user = Yii::$app->user->identity->userAR;
            if ($user->role == 'admin') {
                $need_user = \app\modules\admin\models\User::findByUsername($login);
                if ($need_user) {
                    Yii::$app->user->logout();
                    Yii::$app->user->login($need_user, 3600);
                    return $this->redirect('/');
                }
            }
        }
        throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
    }

    public function actionTestpdf()
    {
        $post = Yii::$app->request->post();
        if (!empty($post['htmlline'])) {
            // $content = $this->renderPartial('testpdf');
            $content = $post['htmlline'];
            $pdf = new Pdf([
                // set to use core fonts only
                'mode' => Pdf::MODE_UTF8,
                // A4 paper format
                'format' => Pdf::FORMAT_A4,
                // portrait orientation
                'orientation' => Pdf::ORIENT_PORTRAIT,
                // stream to browser inline
                'destination' => Pdf::DEST_DOWNLOAD,
                'filename' => 'test.pdf',
                // your html content input
                'content' => $content,
                // format content from your own css file if needed or use the
                // enhanced bootstrap css built by Krajee for mPDF formatting
                // 'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
                // any css to be embedded if required
                'cssInline' => $post['cssline'], // '.pageframe {width: 681px;height: 993px;background: url(/img/pdf-certificate/cert_frame.jpg);}.header td {padding: 35px;padding-bottom: 0;}.header_info {width: 180px;color: #6c81be;font-size: 13px;}.main {padding-top: 50px;padding-left: 105px;height: 500px;}.h1 {margin-top: 0;margin-bottom: 23px;color: #af8c4b;font-size: 49px;line-height: 120%;font-weight: 400;}.footer {padding-top: 20px;padding-left: 105px;background: url(/img/pdf-certificate/cert_pechat.jpg) no-repeat right bottom;width: 538px;}.h2 {margin-top: 0;margin-bottom: 21px;font-size: 36px;color: #6c81be;font-weight: 200;}.text {width: 492px;font-size: 18px;color: #000;line-height: 170%;padding-top: 30px;}.footer_text {width: 400px;color: #000;font-size: 15px;margin-bottom: 37px;}.podpis {width: 400px;color: #6c81be;font-size: 12px;}.podpis_text {height: 60px;margin-right: 9px;}.podpis img {vertical-align: middle;}',
                // set mPDF properties on the fly
                'options' => ['title' => 'Заголовок'],
                // call mPDF methods on the fly
                // 'methods' => [
                //    'SetHeader' => ['<img src="' . \app\helpers\MainHelper::get_template_base_url() . '/img/logo.jpg" class="logo" />'],
                //    'SetFooter' => ['<div class="centered">Страница {PAGENO}</div>'],
                // ]
                // 'marginLeft' => 0,
                // 'marginRight' => 0,
                // 'marginTop' => 0,
                // 'marginBottom' => 0,
                // 'marginHeader' => 0,
                // 'marginFooter' => 0,
            ]);
            return $pdf->render();
        }
        return $this->render('index');
    }

    public function actionApplycity($city_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $city = City::find()->where(['id' => $city_id, 'visible' => 1])->one();
        if ($city) {
            $this->setCity($city->id);
            return ['status' => 'success', 'message' => 'Выбран город ' . $city->name, 'city_name' => $city->name];
        }
        return ['status' => 'fail', 'message' => 'Указанный город не найден'];

    }

    public function actionSitemap()
    {
        Yii::$app->response->format = Response::FORMAT_XML;
        Yii::$app->response->formatters = [
            'xml' => [
                'class' => \yii\web\XmlResponseFormatter::class,
                'itemTag' => 'url',
                'rootTag' => 'urlset',
            ],
        ];
        $pages = Page::findForSitemap()->all();
        $urls = [];
        $used_models = [];
        foreach ($pages as $page) {
            $path = $page->getUrlPath();
            if (trim($path, '/') == '') {
                $path = '/';
            }
            $urls[] = [
                'loc' => trim(\app\helpers\MainHelper::get_template_base_url(), '/') . $path,
                'changefreq' => 'monthly',
                'priority' => '0.5',
                'lastmod' => Yii::$app->formatter->asDatetime($page->updated_at, 'php:Y-m-d\TH:i:s'),
            ];
            if ($page->start_module != '') {
                // копаемся в подключенном модуле
                if (isset(Yii::$app->modules['admin']['modules'][$page->start_module])) {
                    // модуль есть в конфиге
                    // подгружаем модуль
                    $module_ex = new Yii::$app->modules['admin']['modules'][$page->start_module]['class']($page->start_module);
                    if (isset($module_ex->params['models_for_sitemap']) && !empty($module_ex->params['models_for_sitemap']) and is_array($module_ex->params['models_for_sitemap'])) {
                        // все ок, берем модель и ищем
                        foreach ($module_ex->params['models_for_sitemap'] as $model_class) {
                            // исключаем использованные модели
                            if (!in_array($model_class, $used_models)) {
                                array_push($used_models, $model_class);
                                $items = $model_class::findForSitemap()->all();
                                foreach ($items as $item) {
                                    $urls[] = [
                                        'loc' => trim(\app\helpers\MainHelper::get_template_base_url(), '/') . $item->getUrlPath(),
                                        'changefreq' => 'monthly',
                                        'priority' => '0.5',
                                        'lastmod' => Yii::$app->formatter->asDatetime($item->updated_at, 'php:Y-m-d\TH:i:s'),
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }
        return $urls;
    }
}
