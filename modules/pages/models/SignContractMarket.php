<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница содержит договор для Маркетплейс, который пользователь должен подписать. Выводится если появилась новая версия договора, которую пользователь еще не подписывал
 */

namespace app\modules\pages\models;

class SignContractMarket extends Page
{
    public static $name_for_list = "страницу Подписания договора на маркетплейс";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'sign_contract';
    public $action_id = 'pages/isle/contracts';

}
