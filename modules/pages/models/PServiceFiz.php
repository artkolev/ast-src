<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница нулевого шага подачи заявки на присоединение к Маркетплейс для Физлица. В данном случае регистрации в качестве физлица нету и выводится просто текст.
 */

namespace app\modules\pages\models;

class PServiceFiz extends Page
{
    public static $name_for_list = "Регистрация услуг как физлицо";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'service_fiz';
    public $action_id = 'pages/service/regfiz';

}
