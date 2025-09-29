<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница нулевого шага подачи заявки на присоединение к Маркетплейс для Юрлица. Текст на странице + Кнопки Подать заявку / Удалить заявку
 */

namespace app\modules\pages\models;

class PServiceOOO extends Page
{
    public static $name_for_list = "Регистрация услуг как Юрлицо";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'service_regindex_ooo';
    public $action_id = 'pages/service/regindexooo';

}
