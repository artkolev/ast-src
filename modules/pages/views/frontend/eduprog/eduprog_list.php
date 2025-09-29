<?php
/*
    @descr Список программ ДПО в ЛК
    @var $model Class app\modules\pages\models\LKEduprogList; текущая страница
    @action pages/eduprog/eduproglist
*/

use app\helpers\MainHelper;
use app\modules\eduprog\models\Eduprog;
use app\modules\pages\models\LKEduprogEdit;
use app\modules\pages\models\LKEduprogViewMembers;
use app\modules\pages\models\SupportPage;
use yii\helpers\Url;

/* страница редактирования программы */
$edit_page = LKEduprogEdit::find()->where(['model' => LKEduprogEdit::class, 'visible' => 1])->one();
$edit_url = (!empty($edit_page) ? $edit_page->getUrlPath() : false);
/* страница просмотра данных о программе (стартовая страница - список слушателей) */
$view_page = LKEduprogViewMembers::find()->where(['model' => LKEduprogViewMembers::class, 'visible' => 1])->one();
$view_url = (!empty($view_page) ? $view_page->getUrlPath() : false);
// страница поддержки
$support_page = SupportPage::find()->where(['model' => SupportPage::class, 'visible' => 1])->one();

$status_class = [
        'published' => ['menu_color' => '', 'status' => 'published', 'status_color' => 'blue'],
        'planned' => ['menu_color' => '', 'status' => 'planned', 'status_color' => 'lightGray-blue'],
        'moderate' => ['menu_color' => 'orange', 'status' => 'moderation', 'status_color' => 'orange'],
        'moderate_edit' => ['menu_color' => 'middleGray', 'status' => 'moderation-fail', 'status_color' => 'middleGray'],
        'invisible' => ['menu_color' => 'lightGray-whiteText', 'status' => 'invisible', 'status_color' => 'lightGray'],
        'archive' => ['menu_color' => 'lightGray-whiteText', 'status' => 'archive', 'status_color' => 'lightGray'],
        'cancelled' => ['menu_color' => 'lightGray-whiteText', 'status' => 'cancel', 'status_color' => 'lightGray'],
        'draft' => ['menu_color' => 'lightGray-whiteText', 'status' => 'draft', 'status_color' => 'lightGray'],
];

$list_format = [
        'online' => 'Онлайн',
        'offline' => 'Офлайн',
        'hybrid' => 'Гибридное',
];

$location_icons = ['online' => 'location-online', 'offline' => 'location-offline', 'hybrid' => 'location-gibrid'];
?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <div class="lk_block">
                    <header class="lk_block_header">
                        <h1 class="lk_block_title-big mb0"><?= $model->getNameForView(); ?></h1>
                        <?php if (!empty($model->content)) { ?>
                            <div class="mt20"><?= $model->content; ?></div>
                        <?php } ?>
                    </header>
                </div>
                <div class="lk_block desktop-visible">
                    <main class="lk_content lk_content-basic lk_switchers">
                        <a href="<?= $model->getUrlPath(); ?>" class="button lk gray">Все</a>
                        <?php foreach (Eduprog::getStatusListLK() as $key => $value) { ?>
                            <a href="<?= $model->getUrlPath(); ?>?status=<?= $key; ?>"
                               class="button lk <?= $status_class[$key]['menu_color']; ?>"><?= $value; ?></a>
                        <?php } ?>
                    </main>
                </div>

                <div class="lk_block mobile-visible">
                    <div class="lk_block_header">
                        <div class="lk_switchers-select-wrapper mb0">
                            <div class="lk_switchers-text">Статус программы</div>
                            <select class="lk_switchers-select">
                                <option value="<?= $model->getUrlPath(); ?>" <?= ($curr_status == '' ? ' selected=""' : ''); ?>>
                                    Все
                                </option>
                                <?php foreach (Eduprog::getStatusListLK() as $key => $value) { ?>
                                    <option value="<?= $model->getUrlPath(); ?>?status=<?= $key; ?>" <?= ($curr_status == $key ? ' selected=""' : ''); ?>><?= $value; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php if (!empty($items)) { ?>
                    <?php foreach ($items as $item) {
                        $price_text = 'Цена: не указано';
                        if (!empty($item->eduprogForms)) {
                            $price_text = [];
                            foreach ($item->eduprogForms as $eduprog_form) {
                                $price_text[] = 'от ' . number_format($eduprog_form->minTariffPrice, 0, '', ' ') . ' ₽';
                            }
                            $price_text = array_unique($price_text);
                            $price_text = implode('/', $price_text);
                        } ?>
                        <div id="eduprog_<?= $item->id; ?>" class="lk_order_item">

                            <?php if ($item->countOrganizatorMessageNotice()) { ?>
                                <div class="lk_order_item-sticker"></div>
                            <?php } ?>
                            <?php if ($item->statusFull != 'moderate') { ?>
                                <a href="<?= ($view_url ? Url::toRoute([$view_url, 'id' => $item->id]) : ''); ?>"
                                   class="lk_order_more <?= $status_class[$item->statusFull]['status_color']; ?> lk_order_more-basic"><img
                                            src="/img/nav_right-white.svg" alt=""/></a>
                            <?php } ?>
                            <h4 class="lk-order-title"><?= $item->name; ?></h4>
                            <div class="lk-event-info-wrapper">
                                <div class="lk-event-info <?= $status_class[$item->statusFull]['status']; ?>"><?= $item->statusNameLK; ?></div>
                                <div class="lk-event-info price"><?= $price_text; ?></div>
                                <div class="lk-event-info <?= (!empty($location_icons[$item->format]) ? $location_icons[$item->format] : 'location-gibrid'); ?>"><?= (!empty($list_format[$item->format]) ? $list_format[$item->format] : 'Формат программы: не указан'); ?></div>
                                <?php if ($item->category) { ?>
                                    <div class="lk-event-info learn"><?= $item->category->name; ?></div>
                                <?php } else { ?>
                                    <div class="lk-event-info"></div>
                                <?php } ?>
                                <div class="lk-event-info date"><?= MainHelper::printDateRange($item, 'date_start', 'date_stop'); ?></div>
                            </div>
                            <?php if (($item->statusFull == 'moderate_edit') && !empty($item->lastModeration->reason)) { ?>
                                <div class="lk-event-info-text"><?= $item->lastModeration->reason; ?></div>
                            <?php } ?>

                            <?php if ($item->statusFull != 'moderate') { ?>
                                <div class="lk-event-buttons">
                                    <?php if (in_array($item->statusFull, ['published', 'cancelled']) && (!empty(trim($item->getUrlPath(), '/')))) { ?>
                                        <a href="<?= $item->getUrlPath(); ?>" class="site_open button-o small"
                                           target="_blank">Открыть на сайте</a>
                                    <?php } ?>
                                    <?php if (in_array($item->statusFull, ['published', 'moderate_edit', 'planned', 'draft', 'invisible'])) { ?>
                                        <a href="<?= ($edit_url ? Url::toRoute([$edit_url, 'id' => $item->id]) : ''); ?>"
                                           class="button-o small"><?= ($item->statusFull == 'moderate_edit') ? 'Внести изменения' : 'Редактировать'; ?></a>
                                    <?php } ?>
                                    <?php if (in_array($item->statusFull, ['moderate_edit']) && $support_page) { ?>
                                        <a target="_blank" href="<?= $support_page->getUrlPath(); ?>"
                                           class="button-o small">Написать в поддержку</a>
                                    <?php } ?>
                                    <?php if (in_array($item->statusFull, ['published', 'planned', 'moderate_edit', 'invisible']) && ((strtotime($item->date_start) > strtotime(date('d.m.Y'))) or empty($item->ordersAll))) { ?>
                                        <a href="#" class="button-o small cancel_eduprog"
                                           data-origin="<?= $item->id; ?>">Отменить программу</a>
                                    <?php } ?>
                                    <?php if (in_array($item->statusFull, ['draft', 'cancelled', 'moderate_edit', 'archive']) && empty($item->ordersAll)) { ?>
                                        <a data-origin="<?= $item->id; ?>" href="#"
                                           class="delete_eduprog button-o small">Удалить</a>
                                    <?php } ?>
                                    <?php if (in_array($item->statusFull, ['published', 'draft', 'moderate_edit', 'planned', 'archive']) && $edit_url) { ?>
                                        <a data-origin="<?= $item->id; ?>" href="#" class="copy_eduprog button-o small">Создать
                                            копию</a>
                                    <?php } ?>
                                </div>
                                <a href="<?= ($view_url ? Url::toRoute([$view_url, 'id' => $item->id]) : ''); ?>"
                                   class="<?= $status_class[$item->statusFull]['status_color']; ?> lk_order_more-basic_mobile">Перейти<img
                                            src="/img/nav_right-white.svg" alt=""/></a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="lk_block">
                        <main class="lk_content">
                            Программы не найдены.
                        </main>
                    </div>
                <?php } ?>
            </div>
            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
        </div>
    </main>
    <div class="modal" id="fail_service_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Ошибка обновления программы</div>
                <p>При изменении статуса программы возникли ошибки. <?= \app\helpers\MainHelper::getHelpText(); ?></p>
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
                <div class="modal_title">Удаление программы</div>
                <p>Программа успешно удалена</p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>

<?php
$url_copy = Url::toRoute(['/pages/eduprog/copyeduprog']);
$url_delete = Url::toRoute(['/pages/eduprog/deleteeduprog']);
$url_cancel = Url::toRoute(['/pages/eduprog/cancel-eduprog']);
$js = <<<JS
    $('.lk-event-buttons .cancel_eduprog').on('click', function(e) {
        e.preventDefault();
        var origin = $(this).data('origin');
        $.ajax({
            type: 'GET',
            url: '{$url_cancel}',
            processData: true,
            dataType: 'json',
            data: {origin:origin},
            success: function(data){
                if (data.status == 'success') {
                    if (data.redirect_to) {
                        window.location.href = data.redirect_to;
                    } else {
                        // удалить из списка, пока не прийдет vue.js или не перепишу отрисовку в отдельный view
                        $('#eduprog_'+origin).remove();
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
    $('.lk-event-buttons .delete_eduprog').on('click', function(e) {
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
                    $('#eduprog_'+origin).remove();
                    $('#success_event_modal .success_box .modal_title').html('Удаление программы');
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
    $('.lk-event-buttons .copy_eduprog').on('click', function(e) {
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
    $('.lk_switchers-select').change(function() {
        document.location.href = $(this).val();
    });
JS;
$this->registerJs($js);
?>