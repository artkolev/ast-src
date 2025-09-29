<?php
/**
 * @modelDescr Страница Личного Кабинета - Содержит форму для запроса сертификата из Конструктора форм, Сертификаты Академии по кафедрам, Сертификаты из Конструктора форм, Сертификаты по Тарифам билетов и Файлы, загруженные в профиль пользователя в поле Сертификаты
 */

namespace app\modules\pages\models;

class SertificateDocs extends Page
{
    public static $name_for_list = "Сертификаты пользователей";
    public static $count_for_list = 1;
    public $accessLevel = 'restricted';
    public $view = 'documents_sertificate';
    public $action_id = 'pages/documents/sertificate';

}
