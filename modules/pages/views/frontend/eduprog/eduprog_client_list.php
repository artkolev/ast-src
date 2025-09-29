<?php
/*
    @descr Список программ ДПО в ЛК Слушателя
    @var $model Class app\modules\pages\models\LKEduprogClientList; текущая страница
    @action pages/eduprog/eduprog-client-list
*/

use app\helpers\MainHelper;
use app\modules\eduprog\models\EduprogMember;
use app\modules\pages\models\LKEduprogClientChat;
use app\modules\pages\models\LKEduprogClientContent;
use app\modules\pages\models\LKEduprogClientNews;
use app\modules\pages\models\LKEduprogClientOrder;
use yii\helpers\Url;

/* страница просмотра данных о программе (стартовая страница - новости и сообщения? если они есть. Если нету - стартовая страница - стоимость) */
$message_page = LKEduprogClientChat::find()->where(['model' => LKEduprogClientChat::class, 'visible' => 1])->one();
$news_page = LKEduprogClientNews::find()->where(['model' => LKEduprogClientNews::class, 'visible' => 1])->one();
$order_page = LKEduprogClientOrder::find()->where(['model' => LKEduprogClientOrder::class, 'visible' => 1])->one();
$content_page = LKEduprogClientContent::find()->where(['model' => LKEduprogClientContent::class, 'visible' => 1])->one();
$order_url = (!empty($order_page) ? $order_page->getUrlPath() : false);
$news_url = (!empty($news_page) ? $news_page->getUrlPath() : false);
$content_url = (!empty($content_page) ? $content_page->getUrlPath() : false);
$message_url = (!empty($message_page) ? $message_page->getUrlPath() : false);

/* страница просмотра новостей по программе */

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

                <!-- фильтр статусов события, в a href="" и option value="" ссылки на статусы -->
                <div class="lk_block desktop-visible">
                    <main class="lk_content lk_content-basic lk_switchers">
                        <a href="<?= $model->getUrlPath(); ?>" class="button lk gray">Все</a>
                        <a href="<?= Url::toRoute([$model->getUrlPath(), 'status' => 'new']); ?>" class="button lk">Активные</a>
                        <a href="<?= Url::toRoute([$model->getUrlPath(), 'status' => 'archive']); ?>"
                           class="button lk lightGray-whiteText">Архив</a>
                    </main>
                </div>
                <div class="lk_block mobile-visible">
                    <div class="lk_block_header">
                        <div class="lk_switchers-select-wrapper mb0">
                            <div class="lk_switchers-text">Статус программы</div>
                            <select class="lk_switchers-select">
                                <option value="<?= $model->getUrlPath(); ?>" <?= empty($status_filter) ? 'selected=""' : ''; ?>>
                                    Все
                                </option>
                                <option value="<?= Url::toRoute([$model->getUrlPath(), 'status' => 'new']); ?>" <?= ($status_filter == 'new') ? 'selected=""' : ''; ?>>
                                    Активные
                                </option>
                                <option value="<?= Url::toRoute([$model->getUrlPath(), 'status' => 'archive']); ?>" <?= ($status_filter == 'archive') ? 'selected=""' : ''; ?>>
                                    Архив
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- <div class="auth-notice lk_page">
                    Текст сообщения о том, что пора оплачивать новый модуль по программе. <br>
                    <a href="" class="button small">Оплатить</a>
                    <a href="#!" class="close-auth-notice"></a>
                </div> -->

                <!-- карточки программ -->
                <?php foreach ($items as $item) {
                    $member = $item->getMemberByUser(Yii::$app->user->identity->id);
                    if ($member) {

                        $structure = MainHelper::cleanInvisibleMultifield($item->structure);
                        // по умолчанию первое заполненное из контента\заказа
                        if (!empty($structure)) {
                            $view_url = $content_url ?: $order_url;
                        } else {
                            $view_url = $order_url;
                        }

                        $current_status_class = ((strtotime($item->date_stop) > strtotime(date('Y-m-d'))) && in_array($member->status, [EduprogMember::STATUS_WAITING, EduprogMember::STATUS_ACTIVE])) ? 'blue' : 'lightGray';

                        $news_notice_count = $item->countUserNewsNotice($member->user_id);
                        $message_notice_count = $item->countUserMessageNotice($member->user_id);

                        ?>
                        <div class="lk_order_item">
                            <?php
                            if (($news_notice_count + $message_notice_count) > 0) { ?>
                                <div class="lk_order_item-sticker"></div>
                            <?php } ?>
                            <a href="<?= Url::toRoute([$view_url, 'id' => $item->id]); ?>"
                               class="lk_order_more <?= $current_status_class; ?> lk_order_more-basic"><img
                                        src="/img/nav_right-white.svg" alt="Перейти"/></a>
                            <h4 class="lk-order-title"><?= $item->name; ?></h4>
                            <div class="lk-event-info-wrapper">
                                <div class="lk-event-info <?= $location_icons[$item->format]; ?>"><?= $item->formatName; ?></div>
                                <div class="lk-event-info learn"><?= $item->category ? $item->category->name : ''; ?></div>
                                <div class="lk-event-info date"><?= MainHelper::printDateRange($item, 'date_start', 'date_stop'); ?></div>
                                <div class="lk-event-info user-number"><?= $member->memberNum; ?></div>
                            </div>
                            <div class="lk-event-buttons">
                                <!-- кнопка отображается, только если есть хотя бы одна новость или сообщение -->
                                <?php if ($view_url && ($member->status == EduprogMember::STATUS_ACTIVE) && !empty($member->visibleNews)) { ?>
                                    <a href="<?= Url::toRoute([$view_url, 'id' => $item->id]); ?>"
                                       class="button-o small gray <?= ($news_notice_count > 0) ? 'have-notice' : ''; ?>">
                                        <?= ($news_notice_count > 0) ? '<span>+' . $news_notice_count . '</span>' : ''; ?>
                                        Новости
                                    </a>
                                <?php } ?>
                                <?php
                                $current_chat = $item->getChatWithUser($member->user_id, false);
                                if ($message_url && ($member->status == EduprogMember::STATUS_ACTIVE) && !empty($current_chat?->messages)) { ?>
                                    <a href="<?= Url::toRoute([$message_url, 'id' => $item->id]); ?>"
                                       class="button-o small gray <?= ($message_notice_count > 0) ? 'have-notice' : ''; ?>">
                                        <?= ($message_notice_count > 0) ? '<span>+' . $message_notice_count . '</span>' : ''; ?>
                                        Сообщения
                                    </a>
                                <?php } ?>
                                <?php if ($item->canPublish()) { ?>
                                    <a href="<?= $item->getUrlPath(); ?>" target="_blank" class="button-o small gray">Смотреть
                                        на сайте</a>
                                <?php } ?>
                            </div>
                            <div>Связь с организатором: <a
                                        href="mailto:<?= $item->contact_email; ?>"><?= $item->contact_email; ?></a>
                            </div>
                            <!-- <div>Мой номер на программе: <?= $member->memberNum; ?></div> -->
                            <a href="<?= Url::toRoute([$view_url, 'id' => $item->id]); ?>"
                               class="<?= $current_status_class; ?> lk_order_more-basic_mobile">Перейти<img
                                        src="/img/nav_right-white.svg" alt=""/></a>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>

            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
        </div>
    </main>
<?php
$js = <<<JS
    $('.lk_switchers-select').change(function() {
        document.location.href = $(this).val();
    });
JS;
$this->registerJs($js);
?>