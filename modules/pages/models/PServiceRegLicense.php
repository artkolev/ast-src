<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница подачи заявки на оказание лицензируемых услуг, публикацию программ ДПО.
 */

namespace app\modules\pages\models;

class PServiceRegLicense extends Page
{
    public static $name_for_list = "Форма регистрации на оказание лицензированых услуг";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'service_regform_license';
    public $action_id = 'pages/service/regformlicense';

}
