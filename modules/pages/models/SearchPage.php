<?php
/**
 * @modelDescr Страница поиска по сайту
 */

namespace app\modules\pages\models;

class SearchPage extends Page
{
    public static $name_for_list = "страницу Поиска";
    public static $count_for_list = 1;
    public $view = 'search';
    public $action_id = 'pages/pages/search';

}
