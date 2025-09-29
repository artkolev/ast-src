<?php
/*
    @descr Страница выгрузки результатов из Конструктора форм (доступна пользователям, у которых есть хотя-бы одна отображаемая (visible=1) форма. Сделать форму доступной пользователю пожно в редактировании формы в админке)

    @var $model Class app\modules\pages\models\LKEventsExport; текущая страница
    @var $error_message String содержит ошибки, если в get-параметрах были переданы некорректные данные
    @var $forms_group Array имеет структуру
    [
        'events'=> Array массив данных следующей страуктуры:
            [event_id] => [
                'event' => Class app\modules\events\models\Event модель мероприятия, по которому сгруппированы формы
                'forms' => Array of app\modules\formslist\models\Formslist; массив форм принадлежащих мероприятию, доступных пользователю для выгрузки данных
            ],
        'other' => Array of app\modules\formslist\models\Formslist; массив форм без привязки к мероприятию, доступных пользователю для выгрузки данных

    @action pages/activities/events_export
*/

use yii\helpers\Url;

?>
<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">
        <div class="lk_maincol">
            <div class="lk_block mb10">
                <header class="lk_block_header">
                    <h1 class="lk_block_title-big"><?= $model->getNameForView(); ?></h1>
                    <?= $model->content; ?>
                </header>
            </div>
            <?php if (!empty($error_message)) { ?>
                <div class="lk_block">
                    <main class="lk_content">
                        <p class="red"><?= $error_message; ?></p>
                    </main>
                </div>
            <?php } ?>
            <?php
            $has_export_items = false;
            // сначала выводим формы, сгруппированные по мероприятиям
            if (!empty($forms_group['events'])) {
                $has_export_items = true;
                foreach ($forms_group['events'] as $event_id => $event_data) { ?>
                    <div class="lk_order_item">
                        <div class="lk_order_item_info">
                            <div class="lk_block_title">
                                <p><?= $event_data['event']->name; ?></p>
                            </div>
                        </div>
                        <?php if (!empty($event_data['forms'])) { ?>
                            <?php foreach ($event_data['forms'] as $fl) { ?>
                                <div>
                                    <a href="<?= Url::toRoute([$model->getUrlPath(), 'form_id' => $fl->id]); ?>"
                                       class="mt20 btn btn-primary button-o lk_button_submit btn-sm"><?= mb_strimwidth(strip_tags($fl->name), 0, 50, '...', 'UTF8'); ?></a>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                <?php }
            }
            // выводим формы, не привязанные к мероприятиям
            if (!empty($forms_group['other'])) {
                $has_export_items = true; ?>
                <div class="lk_order_item">
                    <?php foreach ($forms_group['other'] as $fl) { ?>
                        <div>
                            <a href="<?= Url::toRoute([$model->getUrlPath(), 'form_id' => $fl->id]); ?>"
                               class="mt20 btn btn-primary button-o lk_button_submit btn-sm"><?= mb_strimwidth(strip_tags($fl->name), 0, 50, '...', 'UTF8'); ?></a>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <?php if (!$has_export_items) { ?>
                <div class="lk_block">
                    <main class="lk_content">
                        Нет доступных мероприятий для выгрузки.
                    </main>
                </div>
            <?php } ?>
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>