<?php
/*
 *   отображение карточки программы в ЛК Эксперта
 */

use app\helpers\MainHelper;
use app\modules\pages\models\LKEduprogEdit;
use app\modules\pages\models\SupportPage;
use yii\helpers\Url;

// страница поддержки
$support_page = SupportPage::find()->where(['model' => SupportPage::class, 'visible' => 1])->one();

/* страница редактирования программы */
$edit_page = LKEduprogEdit::find()->where(['model' => LKEduprogEdit::class, 'visible' => 1])->one();
$edit_url = (!empty($edit_page) ? $edit_page->getUrlPath() : false);

$status_class = [
        'published' => 'published',
        'planned' => 'planned',
        'moderate' => 'moderation',
        'moderate_edit' => 'moderation-fail',
        'invisible' => 'invisible',
        'archive' => 'archive',
        'cancelled' => 'cancel',
        'draft' => 'draft',
];

$list_format = [
        'online' => 'Онлайн',
        'hybrid' => 'Офлайн и онлайн',
];

$price_text = 'Цена: не указано';
if (!empty($eduprog->eduprogForms)) {
    $price_text = [];
    foreach ($eduprog->eduprogForms as $eduprog_form) {
        $price_text[] = 'от ' . number_format($eduprog_form->minTariffPrice, 0, '', ' ') . ' ₽';
    }
    $price_text = array_unique($price_text);
    $price_text = implode('/', $price_text);
} ?>
    <div id="eduprog_<?= $eduprog->id; ?>" class="lk_order_item">
        <h4 class="lk-order-title"><?= $eduprog->name; ?></h4>
        <div class="lk-event-info-wrapper">
            <div class="lk-event-info <?= $status_class[$eduprog->statusFull]; ?>"><?= $eduprog->statusNameLK; ?></div>
            <div class="lk-event-info price"><?= $price_text; ?></div>
            <div class="lk-event-info <?= $eduprog->format == 'hibrid' ? 'location-gibrid' : 'location-' . $eduprog->format; ?>"><?= $eduprog->formatName; ?></div>
            <?php if ($eduprog->category) { ?>
                <div class="lk-event-info learn"><?= $eduprog->category->name; ?></div>
            <?php } else { ?>
                <div class="lk-event-info"></div>
            <?php } ?>
            <div class="lk-event-info date"><?= MainHelper::printDateRange($eduprog, 'date_start', 'date_stop'); ?></div>
        </div>
        <?php if (($eduprog->statusFull == 'moderate_edit') && !empty($eduprog->lastModeration->reason)) { ?>
            <div class="lk-event-info-text"><?= $eduprog->lastModeration->reason; ?></div>
        <?php } ?>

        <?php if ($eduprog->statusFull != 'moderate') { ?>
            <div class="lk-event-buttons">
                <?php if (in_array($eduprog->statusFull, ['published', 'cancelled']) && !empty(trim($eduprog->getUrlPath(), '/'))) { ?>
                    <a href="<?= $eduprog->getUrlPath(); ?>" class="site_open button-o small gray" target="_blank">Открыть
                        на сайте</a>
                <?php } ?>
                <?php if (in_array($eduprog->statusFull, ['published', 'moderate_edit', 'planned', 'draft', 'invisible'])) { ?>
                    <a href="<?= ($edit_url ? Url::toRoute([$edit_url, 'id' => $eduprog->id]) : ''); ?>"
                       class="button-o small gray"><?= ($eduprog->statusFull == 'moderate_edit') ? 'Внести изменения' : 'Редактировать'; ?></a>
                <?php } ?>
                <?php if (in_array($eduprog->statusFull, ['moderate_edit']) && $support_page) { ?>
                    <a target="_blank" href="<?= $support_page->getUrlPath(); ?>" class="button-o small gray">Написать в
                        поддержку</a>
                <?php } ?>
                <?php if (in_array($eduprog->statusFull, ['published', 'planned', 'moderate_edit', 'invisible']) && ((strtotime($eduprog->date_start) > strtotime(date('d.m.Y'))) or empty($eduprog->ordersAll))) { ?>
                    <a href="#" class="button-o small gray cancel_eduprog" data-origin="<?= $eduprog->id; ?>">Отменить
                        программу</a>
                <?php } ?>
                <?php if (in_array($eduprog->statusFull, ['draft', 'cancelled', 'moderate_edit', 'archive']) && empty($eduprog->ordersAll)) { ?>
                    <a data-origin="<?= $eduprog->id; ?>" href="#"
                       class="delete_eduprog button-o small gray">Удалить</a>
                <?php } ?>
                <?php if (in_array($eduprog->statusFull, ['published', 'draft', 'moderate_edit', 'planned', 'archive']) && $edit_url) { ?>
                    <a data-origin="<?= $eduprog->id; ?>" href="#" class="copy_eduprog button-o small gray">Создать
                        копию</a>
                <?php } ?>
            </div>
        <?php } ?>
    </div>

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
                        // обновить страницу, чтобы перерисовать карточку в актуальном состоянии
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
                    // программа удалена - редирект на страницу со списком программ
                    window.location.href = '{$eduprog_catalog->getUrlPath()}';
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
JS;
$this->registerJs($js);
?>