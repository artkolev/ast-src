<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница просмотра и работы с заявкой в Личном кабинете. Содержит весь функционал по работе с заявкой, в зависимости от статуса и роли пользователя в заявке (клиент/испольнитель/мкс) применяются разые view.
 */

namespace app\modules\pages\models;

class QueriesView extends Page
{
    public static $name_for_list = "страницу Просмотра заявки";
    public static $count_for_list = 1;
    public $accessLevel = 'none';
    public $view = 'view';
    public $action_id = 'pages/queries/view';

}
