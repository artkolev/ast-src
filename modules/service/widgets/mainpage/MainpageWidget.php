<?php

namespace app\modules\service\widgets\mainpage;

use app\modules\pages\models\Home;
use app\modules\pages\models\ServiceTypePage;
use app\modules\service\models\Service;
use yii\base\Widget;

class MainpageWidget extends Widget
{
    public $loop = false;
    public $autoplay = false;
    public $autoplayTimeout = 5000;
    public $services_subtitle;

    public function run()
    {
        $items = Service::findVisible()->orderBy('RAND()')->limit(6)->all();
        $service_page = ServiceTypePage::find()->where(['model' => ServiceTypePage::class, 'visible' => 1])->one();
        $home_page = Home::find()->where(['model' => Home::class, 'visible' => 1])->one();
        return $this->render('mainpage', ['items' => $items, 'service_page' => $service_page, 'services_subtitle' => $this->services_subtitle, 'loop' => $this->loop, 'autoplay' => $this->autoplay, 'autoplayTimeout' => $this->autoplayTimeout]);
    }
}
