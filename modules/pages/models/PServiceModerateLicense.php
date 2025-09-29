<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница успешной отправки заявки на оказание лицензируемых услуг. Страница также отображается, если пользователь отправил заявку на модерацию и пытается перейти на страницу подачи заявки
 */

namespace app\modules\pages\models;

class PServiceModerateLicense extends Page
{
    public static $name_for_list = "О модерации заявки на Лицензируемые услуги (ДПО)";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'service_info';
    public $action_id = 'pages/service/infolicense';

}
