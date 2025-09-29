<?php
/**
 * @modelDescr Страница Личного кабинета - Просмотр программы ДПО раздел порядок обучения
 */

namespace app\modules\pages\models;

class LKEduprogViewTrainingprocCreate extends Page
{
    public static $name_for_list = "страницу Создания/редактирования порядок обучения ДПО в ЛК (Организатор)";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'eduprog_view_trainingproc_create';
    public $action_id = 'pages/eduprog/eduprog-view-trainingproc-create';

}
