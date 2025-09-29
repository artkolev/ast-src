<?php

use app\modules\events\models\Events;
use app\modules\pages\models\LKEventsEdit;
use app\modules\pages\models\LKEventsList;
use app\modules\pages\models\SupportPage;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerCssFile('/css/style-blog.css', ['depends' => [app\assets\AppAsset::class]]);

$parent_page = LKEventsList::find()->where(['visible' => 1, 'model' => LKEventsList::class])->one();

/* страница редактирования мероприятия */
$edit_page = LKEventsEdit::find()->where(['model' => LKEventsEdit::class, 'visible' => 1])->one();
$edit_url = (!empty($edit_page) ? $edit_page->getUrlPath() : false);

// страница поддержки
$support_page = SupportPage::find()->where(['model' => SupportPage::class, 'visible' => 1])->one();

$status_class = [
        'published' => ['menu_color' => '', 'status' => 'published', 'status_color' => 'blue'],
        'planned' => ['menu_color' => '', 'status' => 'planned', 'status_color' => 'lightGray-blue'],
        'moderate' => ['menu_color' => 'orange', 'status' => 'moderation', 'status_color' => 'orange'],
        'moderate_edit' => ['menu_color' => 'middleGray', 'status' => 'moderation-fail', 'status_color' => 'middleGray'],
        'invisible' => ['menu_color' => 'lightGray-whiteText', 'status' => 'invisible', 'status_color' => 'lightGray'],
        'archive' => ['menu_color' => 'lightGray-whiteText', 'status' => 'archive', 'status_color' => 'lightGray'],
        'cancelled' => ['menu_color' => 'lightGray-whiteText', 'status' => 'cancelled', 'status_color' => 'lightGray'],
        'draft' => ['menu_color' => 'lightGray-whiteText', 'status' => 'draft', 'status_color' => 'lightGray'],
];

$location_icons = [Events::TYPE_ONLINE => 'location-online', Events::TYPE_OFFLINE => 'location-offline', Events::TYPE_HYBRID => 'location-gibrid'];

$list_type = [
        Events::TYPE_ONLINE => 'Онлайн',
        Events::TYPE_OFFLINE => 'Офлайн',
        Events::TYPE_HYBRID => 'Гибридное',
];

$price_text = 'Цена: не указано';
if ($event->need_tariff) {
    if (!empty($event->eventsForms)) {
        $price_text = [];
        foreach ($event->eventsForms as $event_form) {
            $price_text[] = ($event_form->payregister ? 'от ' . number_format($event_form->minTariffPrice, 0, '', ' ') . ' ₽' : 'Бесплатно');
        }
        $price_text = array_unique($price_text);
        $price_text = implode('/', $price_text);
    }
} else {
    $price_text = 'Регистрация не требуется';
}

?>

    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <?php if ($parent_page) { ?>
                    <div class="ip_cell w100">
                        <a href="<?= $parent_page->getUrlPath(); ?>" class="button-o back">Мероприятия</a>
                    </div>
                <?php } ?>
                <div class="lk_order_item">
                    <h4 class="lk-order-title"><?= $event->name; ?></h4>
                    <div class="lk-event-info-wrapper">
                        <div class="lk-event-info <?= $status_class[$event->statusFull]['status']; ?>"><?= $event->statusNameLK; ?></div>
                        <div class="lk-event-info price"><?= $price_text; ?></div>
                        <div class="lk-event-info <?= (!empty($location_icons[$event->type]) ? $location_icons[$event->type] : 'location-gibrid'); ?>"><?= (!empty($event->prettyPlace) ? $event->prettyPlace : (!empty($list_type[$event->type]) ? $list_type[$event->type] : 'Формат мероприятия: не указано')); ?></div>
                        <?php if ($event->format) { ?>
                            <div class="lk-event-info learn"><?= $event->format->name; ?></div>
                        <?php } else { ?>
                            <div class="lk-event-info"></div>
                        <?php } ?>
                        <div class="lk-event-info date"><?= app\helpers\MainHelper::printDateRange($event); ?></div>
                    </div>
                    <?php if ($event->statusFull != 'moderate') { ?>
                        <div class="lk-event-buttons">
                            <?php if (in_array($event->statusFull, ['published', 'cancelled'])) { ?>
                                <a href="<?= $event->getUrlPath(); ?>" class="site_open button-o small" target="_blank">Открыть
                                    на сайте</a>
                            <?php } ?>
                            <?php if (in_array($event->statusFull, ['published', 'moderate_edit', 'planned', 'draft', 'invisible'])) { ?>
                                <a href="<?= ($edit_url ? Url::toRoute([$edit_url, 'id' => $event->id]) : ''); ?>"
                                   class="button-o small"><?= ($event->statusFull == 'moderate_edit') ? 'Внести изменения' : 'Редактировать'; ?></a>
                            <?php } ?>
                            <?php /* функционал "Снять с публикации" пока отключаем
                        if (in_array($event->statusFull,['published','planned','invisible']) && empty($event->ordersAll)) { ?>
                            <a href="#" class="button-o small switch" data-switch="visible" data-event="<?=$event->id?>"><?=($event->statusFull == 'invisible')?'Опубликовать':'Снять с публикации'?></a>
                        <?php } */ ?>
                            <?php if (in_array($event->statusFull, ['moderate_edit']) && $support_page) { ?>
                                <a target="_blank" href="<?= $support_page->getUrlPath(); ?>" class="button-o small">Написать
                                    в поддержку</a>
                            <?php } ?>
                            <?php if (in_array($event->statusFull, ['archive']) && $edit_url) { ?>
                                <a data-origin="<?= $event->id; ?>" href="#" class="button-o small copy_event">Опубликовать
                                    повторно</a>
                            <?php } ?>
                            <?php if (in_array($event->statusFull, ['published', 'planned', 'moderate_edit', 'invisible']) && (strtotime($event->event_date) > time() or empty($event->ordersAll))) { ?>
                                <a href="#" class="button-o small cancell_event" data-origin="<?= $event->id; ?>">Отменить
                                    мероприятие</a>
                            <?php } ?>
                            <?php if (in_array($event->statusFull, ['draft', 'cancelled', 'moderate_edit']) && empty($event->ordersAll)) { ?>
                                <a data-origin="<?= $event->id; ?>" href="#"
                                   class="delete_event button-o small">Удалить</a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
                <?php if (!empty($event->eventsForms)) { ?>
                <div class="lk_block">
                    <div class="lk_content">
                        <?php if (count($event_forms_list) > 0) { ?>
                            <div class="lenta-menu-noslider">
                                <?php foreach ($event_forms_list as $key => $form) { ?>
                                    <a href="<?= Url::toRoute([$model->getUrlPath(), 'id' => $event->id, 'event_form_id' => $form->id]); ?>"
                                       class="<?= $event_active_form->id == $form->id ? 'active' : ''; ?>"
                                       title="<?= htmlspecialchars($form->name); ?>"><?= $form->name; ?></a>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <div class="flex align-center tarif-transaction-info">
                            <h4 class="lk_step_title mr30">Заказов: <?= $event_orders_data['count']; ?></h4>
                            <h4 class="lk_step_title">На
                                сумму: <?= number_format($event_orders_data['summ'], 0, '.', ' '); ?> ₽</h4>
                            <a href="<?= Url::toRoute(['/pages/activities/exporttickets', 'event_id' => $event->id, 'form_id' => $event_active_form->id]); ?>"
                               class="button-o blue medium lk">Выгрузить в Excel</a>
                        </div>

                        <div class="tarif-table-wrapper">
                            <table class="table tarif-table transaction-table">
                                <tr>
                                    <th class="desktop-visible">Дата</th>
                                    <th class="desktop-visible">№ Заказа</th>
                                    <th class="desktop-visible">Статус</th>
                                    <th class="desktop-visible">Клиент</th>
                                    <th class="desktop-visible">Телефон</th>
                                    <th class="mobile-visible">Клиент</th>
                                    <th class="centered">Сумма</th>
                                </tr>
                                <?php $form = ActiveForm::begin([
                                        'id' => 'event-filter-form',
                                        'options' => ['class' => 'moderator-table-tr'],
                                        'enableAjaxValidation' => false,
                                        'enableClientValidation' => true,
                                        'validateOnSubmit' => true,
                                        'validateOnChange' => false,
                                        'validateOnType' => false,
                                        'validateOnBlur' => false,
                                        'fieldConfig' => [
                                                'options' => ['class' => 'ip_cell w100'],
                                                'template' => '{label}{input}{error}{hint}',
                                                'inputOptions' => ['class' => 'input_text'],
                                                'labelOptions' => ['class' => 'ip_label'],
                                        ],
                                ]); ?>
                                <input type="hidden" name="id" value="<?= $event->id; ?>">
                                <input type="hidden" name="form_id" value="<?= $event_active_form->id; ?>">
                                <?php ActiveForm::end(); ?>
                                <div id="event_orderitems_list_table" class="moderator-table-tbody"></div>
                                <?php foreach ($event_order_items as $event_order_item) { ?>
                                    <?php echo $this->render('_event_orders_list_card', ['event_order_item' => $event_order_item]); ?>
                                <?php } ?>
                        </div>
                        </table>
                        <div id="event_pager_content">
                            <?= \app\widgets\pagination\LinkPager::widget(['pages' => $event_pages, 'container' => '#event_orderitems_list_table']); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-tarif-transaction" id="transaction">
                <div class="modal-review-content">
                    <div class="lk_block_title-big">Информация об операции</div>
                    <div class="tarif-transaction-list">
                        <div class="tarif-transaction-element">
                            <div class="tarif-transaction-name">Дата операции</div>
                            <div id="tariff_date_modal" class="tarif-transaction-text"></div>
                        </div>
                        <div class="tarif-transaction-element">
                            <div class="tarif-transaction-name">Заказ №</div>
                            <div id="tariff_number_modal" class="tarif-transaction-text"></div>
                        </div>
                        <div class="tarif-transaction-element">
                            <div class="tarif-transaction-name">Участник</div>
                            <div id="tariff_user_modal" class="tarif-transaction-text"></div>
                        </div>
                        <div class="tarif-transaction-element">
                            <div class="tarif-transaction-name">Телефон</div>
                            <div id="tariff_phone_modal" class="tarif-transaction-text"></div>
                        </div>
                        <div id="tariff_list_modal_container"></div>
                    </div>
                    <div class="ip_cell w100 mb0">
                        <button class="button blue small mb0" data-fancybox-close>Ок</button>
                    </div>
                </div>
            </div>
            <?php } ?>
            <?php if (!empty($forms_list)) { ?>
            <div class="lk_block">
                <div class="lk_content">
                    <?php if (count($forms_list) > 0) { ?>
                        <div class="lenta-menu-noslider">
                            <?php foreach ($forms_list as $key => $form) { ?>
                                <a href="<?= Url::toRoute([$model->getUrlPath(), 'id' => $event->id, 'form_id' => $form->id]); ?>"
                                   class="<?= $active_form->id == $form->id ? 'active' : ''; ?>"
                                   title="<?= htmlspecialchars($form->name); ?>"><?= $form->name; ?></a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <div class="flex align-center tarif-transaction-info">
                        <h4 class="lk_step_title font20 mr30">Записей: <?= $orders_data['count']; ?></h4>
                        <a href="<?= Url::toRoute(['/pages/activities/exportform', 'event_id' => $event->id, 'form_id' => $active_form->id]); ?>"
                           class="button-o blue medium lk">Выгрузить в Excel</a>
                    </div>
                    <div class="tarif-table-wrapper">
                        <table class="table tarif-table transaction-table">
                            <tr>
                                <th class="desktop-visible">Дата</th>
                                <th class="desktop-visible">ФИО</th>
                                <th class="desktop-visible">E-mail</th>
                                <th class="mobile-visible">Информация</th>
                                <th class="centered">Телефон</th>
                            </tr>
                            <?php $form = ActiveForm::begin([
                                    'id' => 'filter-form',
                                    'options' => ['class' => 'moderator-table-tr'],
                                    'enableAjaxValidation' => false,
                                    'enableClientValidation' => true,
                                    'validateOnSubmit' => true,
                                    'validateOnChange' => false,
                                    'validateOnType' => false,
                                    'validateOnBlur' => false,
                                    'fieldConfig' => [
                                            'options' => ['class' => 'ip_cell w100'],
                                            'template' => '{label}{input}{error}{hint}',
                                            'inputOptions' => ['class' => 'input_text'],
                                            'labelOptions' => ['class' => 'ip_label'],
                                    ],
                            ]); ?>
                            <input type="hidden" name="id" value="<?= $event->id; ?>">
                            <input type="hidden" name="form_id" value="<?= $active_form->id; ?>">
                            <?php ActiveForm::end(); ?>
                            <div id="orderitems_list_table" class="moderator-table-tbody"></div>
                            <?php foreach ($order_items as $order_item) { ?>
                                <?php echo $this->render('_orders_list_card', ['order_item' => $order_item]); ?>
                            <?php } ?>
                    </div>
                    </table>

                    <div id="pager_content">
                        <?= \app\widgets\pagination\LinkPager::widget(['pages' => $pages, 'container' => '#orderitems_list_table']); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-tarif-transaction" id="formresult">
            <div class="modal-review-content">
                <div class="lk_block_title-big">Информация об участнике</div>
                <div class="tarif-transaction-list">
                    <div class="tarif-transaction-element">
                        <div class="tarif-transaction-name">Дата регистрации</div>
                        <div id="result_date_modal"
                             class="tarif-transaction-text"><?= Yii::$app->formatter->asDatetime($record->created_at, 'php:d.m.Y'); ?></div>
                    </div>
                    <div class="tarif-transaction-element">
                        <div class="tarif-transaction-name">ФИО</div>
                        <div id="result_fio_modal"
                             class="tarif-transaction-text"><?= $record->surname . ' ' . $record->surname . ' ' . $record->patronymic; ?></div>
                    </div>
                    <div class="tarif-transaction-element">
                        <div class="tarif-transaction-name">E-mail</div>
                        <div id="result_email_modal" class="tarif-transaction-text"><?= $record->email; ?></div>
                    </div>
                    <div class="tarif-transaction-element">
                        <div class="tarif-transaction-name">Телефон</div>
                        <div id="result_phone_modal" class="tarif-transaction-text"><?= $record->phone; ?></div>
                    </div>
                    <div id="result_list_modal_container"></div>
                </div>
                <div class="ip_cell w100 mb0">
                    <button class="button blue small mb0" data-fancybox-close>Ок</button>
                </div>
            </div>
        </div>
        <?php } ?>
        <div class="modal" id="filter_error">
            <div class="modal_content modal_content-mini">
                <a href="#" class="modal_close" data-fancybox-close>x</a>
                <h2 class="modal_title modal_title-mini">
                    Во время фильтрации возникла ошибка
                </h2>
                <div class="modal_text modal_text-big modal_text-center">Обновите страницу и попробуйте еще раз</div>
            </div>
            <div class="modal_overlay"></div>
        </div>
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
        </div>
    </main>
    <div class="modal" id="fail_order_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Информация о заказе</div>
                <p>Невозможно получить данные. <?= \app\helpers\MainHelper::getHelpText(); ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
    <div class="modal" id="fail_result_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Информация о записе</div>
                <p>Невозможно получить данные. <?= \app\helpers\MainHelper::getHelpText(); ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
    <div class="modal" id="success_event_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Удаление мероприятия</div>
                <p>Мероприятие успешно удалено</p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
    <div class="modal" id="fail_service_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Ошибка обновления мероприятия</div>
                <p>При изменении статуса мероприятия возникли ошибки. <?= \app\helpers\MainHelper::getHelpText(); ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
<?php
$url_info = Url::toRoute(['/pages/activities/tariffinfo']);
$url_result = Url::toRoute(['/pages/activities/resultinfo']);
$url_copy = Url::toRoute(['/pages/activities/copyevent']);
$url = Url::toRoute(['/pages/activities/switchfield']);
$url_delete = Url::toRoute(['/pages/activities/deleteevent']);
$url_cancell = Url::toRoute(['/pages/activities/cancellevent']);

$js = <<<JS
    $('.lk-event-buttons .copy_event').on('click', function(e) {
        e.preventDefault();
        var origin = $(this).data('origin');
        $.ajax({
            type: 'GET',
            url: '{$url_copy}',
            processData: true,
            dataType: 'json',
            data: {origin:origin},
            success: function(data){
                if (data.status == 'success') {
                    // если аттрибут == visible
                    if (data.redirect_to) {
                        window.location.href = data.redirect_to;
                    }
                } else {
                    // вывести ошибку
                    $('#fail_service_modal .success_box p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
        return false;
    });
    $('.lk-event-buttons .delete_event').on('click', function(e) {
        e.preventDefault();
        var origin = $(this).data('origin');
        $.ajax({
            type: 'GET',
            url: '{$url_delete}',
            processData: true,
            dataType: 'json',
            data: {origin:origin},
            success: function(data){
                if (data.status == 'success') {
                    // удалить мероприятие
                    $('#event_'+origin).remove();
                    $('#success_event_modal .success_box .modal_title').html('Удаление мероприятия');
                    $('#success_event_modal .success_box p').html(data.message);
                    modalPos('#success_event_modal');
                } else {
                    // вывести ошибку
                    $('#fail_service_modal .success_box p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
        return false;
    });
    $('.lk-event-buttons .cancell_event').on('click', function(e) {
        e.preventDefault();
        var origin = $(this).data('origin');
        $.ajax({
            type: 'GET',
            url: '{$url_cancell}',
            processData: true,
            dataType: 'json',
            data: {origin:origin},
            success: function(data){
                if (data.status == 'success') {
                    if (data.redirect_to) {
                        window.location.href = data.redirect_to;
                    } else {
                        window.location.href = window.location.href;
                    }
                } else {
                    // вывести ошибку
                    $('#fail_service_modal .success_box p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
        return false;
    });
    $('.lk-event-buttons .switch').on('click', function(e) {
        e.preventDefault();
        var attribute = $(this).data('switch');
        var item = $(this).data('event');
        $.ajax({
            type: 'GET',
            url: '{$url}',
            processData: true,
            dataType: 'json',
            data: {attribute:attribute,id:item},
            success: function(data){
                if (data.status == 'success') {
                    // если аттрибут == visible
                    if (attribute == 'visible') {
                        // деактивировать - скрыть кнопки, перекрасить
                        $('.lk_order_item .lk_order_more').removeClass('blue').removeClass('orange').addClass('lightblue');
                        $('.lk_order_item .lk_order_more-basic_mobile').removeClass('realblue').removeClass('orange').addClass('blue');
                        $('.lk_order_item .site_open').css('display','none');
                        $('.lk_order_item .switch_public').css('display','none');
                    }
                } else {
                    // вывести ошибку
                    $('#fail_service_modal .success_box p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
        return false;
    });

    $('.open_info').click(function(e) {
        e.preventDefault();
        let ticket_id = $(this).data('ticket');
        let param = yii.getCsrfParam();
        let token = yii.getCsrfToken();
        $.ajax({
            type: 'POST',
            url: '{$url_info}',
            processData: true,
            dataType: 'json',
            data: {ticket_id:ticket_id,param:token},
            success: function(data){
                if (data.status == 'success') {
                    // заполнить модалку
                    $('#tariff_date_modal').html(data.date);
                    $('#tariff_number_modal').html(data.number);
                    $('#tariff_user_modal').html(data.fio);
                    $('#tariff_phone_modal').html(data.phone);
                    let inner_html = '';

                    for (key in data.tariff_list) {
                        inner_html = inner_html + '<div class="tarif-transaction-element"><div class="tarif-transaction-name">Тариф</div><div class="tarif-transaction-text">'+data.tariff_list[key].name+'</div></div><div class="tarif-transaction-element"><div class="tarif-transaction-name">Куплено билетов</div><div class="tarif-transaction-text">'+data.tariff_list[key].count+'</div></div>';
                    }
                    $('#tariff_list_modal_container').html(inner_html);
                    // показать модалку
                    $.fancybox.open($('#transaction'));
                } else {
                    // в случае ошибки вывести сообщение
                    $('#fail_order_modal .success_box p').html(data.message);
                    modalPos('#fail_order_modal');
                }
            }
        });
    });

    $('.open_result').click(function(e) {
        e.preventDefault();
        let result_id = $(this).data('result');
        let param = yii.getCsrfParam();
        let token = yii.getCsrfToken();
        $.ajax({
            type: 'POST',
            url: '{$url_result}',
            processData: true,
            dataType: 'json',
            data: {result_id:result_id,param:token},
            success: function(data){
                if (data.status == 'success') {
                    // заполнить модалку
                    $('#result_date_modal').html(data.date);
                    $('#result_fio_modal').html(data.fio);
                    $('#result_email_modal').html(data.email);
                    $('#result_phone_modal').html(data.phone);

                    let inner_html = '';

                    for (key in data.data_list) {
                        inner_html = inner_html + '<div class="tarif-transaction-element"><div class="tarif-transaction-name">'+data.data_list[key].name+'</div><div class="tarif-transaction-text">'+data.data_list[key].text+'</div></div>';
                    }
                    $('#result_list_modal_container').html(inner_html);
                    // показать модалку
                    $.fancybox.open($('#formresult'));
                } else {
                    // в случае ошибки вывести сообщение
                    $('#fail_result_modal .success_box p').html(data.message);
                    modalPos('#fail_result_modal');
                }
            }
        });
    });
    $('#filter-form').on('beforeSubmit', function(e){
    	$(this).find('input[name=_csrf]').attr('disabled',true);
    	let new_url = $(this).serialize(); 
        $.ajax({
            type: 'GET',
            url: '{$url}?'+new_url,
            processData: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    // заменить содержимое таблицы на полученный html
                    $('#event_orderitems_list_table').html(data.html);
					$('#event_pager_content').html(data.pager);
                } else {
                    // показать модалку с ошибкой
                    $.fancybox.open($('#filter_error'));
                }
				history.pushState(null, null, '{$url}?'+new_url);
            }
        });
        return false;
    });

    $('#filter-form').on('submit', function(e){
        e.preventDefault();
        return false;
    });
    $('#event-filter-form').on('beforeSubmit', function(e){
    	$(this).find('input[name=_csrf]').attr('disabled',true);
    	let new_url = $(this).serialize(); 
        $.ajax({
            type: 'GET',
            url: '{$url}?'+new_url,
            processData: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    // заменить содержимое таблицы на полученный html
                    $('#orderitems_list_table').html(data.html);
					$('#pager_content').html(data.pager);
                } else {
                    // показать модалку с ошибкой
                    $.fancybox.open($('#filter_error'));
                }
				history.pushState(null, null, '{$url}?'+new_url);
            }
        });
        return false;
    });

    $('#event-filter-form').on('submit', function(e){
        e.preventDefault();
        return false;
    });
JS;
$this->registerJs($js);
?>