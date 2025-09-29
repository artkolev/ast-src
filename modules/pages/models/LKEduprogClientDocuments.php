<?php
/**
 * @modelDescr Страница Личного кабинета - Просмотр документов по программе ДПО (ЛК Слушателя)
 */

namespace app\modules\pages\models;

class LKEduprogClientDocuments extends Page
{
    public static $name_for_list = "страницу Просмотра программы ДПО в ЛК Слушателя (Документы)";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'eduprog_client_documents';
    public $action_id = 'pages/eduprog/eduprog-client-documents';

}
