<?php
/**
 * @modelDescr Страница Личного кабинета - Просмотр программы ДПО раздел порядок обучения в ЛК Организатора
 */

namespace app\modules\pages\models;

class LKEduprogViewTrainingproc extends Page
{
    public static $name_for_list = "страницу Просмотра порядок обучения ДПО в ЛК (Организатор)";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'eduprog_view_trainingproc';
    public $action_id = 'pages/eduprog/eduprog-view-trainingproc';

}
