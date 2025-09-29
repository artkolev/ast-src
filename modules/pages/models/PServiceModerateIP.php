<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница успешной отправки заявки на присоединение к Маркетплейс для ИП. Страница также отображается, если пользователь отправил заявку на модерацию и пытается перейти на любой другой шаг подачи заявки
 */

namespace app\modules\pages\models;

class PServiceModerateIP extends Page
{
    public static $name_for_list = "Успешной отправки заявки (ип)";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'service_info';
    public $action_id = 'pages/service/infoip';

}
