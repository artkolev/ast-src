<?php
/**
 * @modelDescr Страница авторизации на сайте
 */

namespace app\modules\pages\models;

class Login extends Page
{
    public static $name_for_list = "страницу Авторизации";

    /* представление, используемое для рендера страницы */
    public static $count_for_list = 1;

    /* экшен, используемый для обработки страницы */
    public $accessLevel = 'restricted';

    /* название, отображаемое на кнопке добавления страницы в админке */
    public $view = 'login';

    /* максимальное количество записей в таблице для этой модели */
    public $action_id = 'pages/register/login';

}
