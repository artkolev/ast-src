<?php
/**
 * @modelDescr Страница Личного Кабинета - Страница доступна только Экспертным организациям и содержит перечень участников ЭО и форму для их редактирования
 */

namespace app\modules\pages\models;

class ProfileMembers extends Page
{
    public static $name_for_list = "Участники организации";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'profile_members';
    public $action_id = 'pages/profile/members';

}
