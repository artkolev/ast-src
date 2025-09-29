<?php
/*
    @descr Мои услуги в Работе (Заказы Эксперта, ЛК)

    @var $model Class app\modules\pages\models\OrdersListIncom; текущая страница
    @var $items Array of app\modules\order\models\Order; массив заказов Эксперта
    @var $cur_status String текущий выбранный статус для показа (из get-параметров)
    @var $count_discus_orders Int количество заказов в статусе Контроль качества

    @action pages/orders/incoming
*/

use app\modules\order\models\Order;
use app\modules\pages\models\OrdersView;
use yii\helpers\Url;

$status_class = [
        Order::STATUS_PAYED => ['color' => 'blue', 'icon' => 'published'],
        Order::STATUS_INWORK => ['color' => 'orange', 'icon' => 'inwork'],
        Order::STATUS_EXECCANCEL => ['color' => 'lightGray', 'icon' => 'cancel'],
        Order::STATUS_USERCANCEL => ['color' => 'lightGray', 'icon' => 'cancel'],
        Order::STATUS_EXECDONE => ['color' => 'darkgreen', 'icon' => 'end-order'],
        Order::STATUS_DISCUS => ['color' => 'lightGray', 'icon' => 'control'],
];

/* страница просмотра заказа */
$view_page = OrdersView::find()->where(['model' => OrdersView::class, 'visible' => 1])->one();
$view_url = (!empty($view_page) ? $view_page->getUrlPath() : false);
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
                        <a href="<?= $model->getUrlPath(); ?>?status=payed" class="button lk">Новые</a>
                        <a href="<?= $model->getUrlPath(); ?>?status=inwork" class="button lk orange">В работе</a>
                        <a href="<?= $model->getUrlPath(); ?>?status=ready" class="button lk darkgreen">Закрытие
                            заказа</a>
                        <a href="<?= $model->getUrlPath(); ?>?status=cancel" class="button lk lightGray-whiteText">Отмена</a>
                        <?php if ($count_discus_orders > 0) { ?>
                            <a href="<?= $model->getUrlPath(); ?>?status=discus" class="button lk lightGray-whiteText">Контроль
                                качества</a>
                        <?php } ?>
                    </main>
                </div>

                <div class="lk_block mobile-visible">
                    <div class="lk_block_header">
                        <div class="lk_switchers-select-wrapper mb0">
                            <div class="lk_switchers-text">Статус заказа</div>
                            <select class="lk_switchers-select">
                                <option value="<?= $model->getUrlPath(); ?>" <?= $cur_status == '' ? 'selected' : ''; ?>>
                                    Все
                                </option>
                                <option value="<?= $model->getUrlPath(); ?>?status=payed" <?= $cur_status == 'payed' ? 'selected' : ''; ?>>
                                    Новые
                                </option>
                                <option value="<?= $model->getUrlPath(); ?>?status=inwork" <?= $cur_status == 'inwork' ? 'selected' : ''; ?>>
                                    В работе
                                </option>
                                <option value="<?= $model->getUrlPath(); ?>?status=ready" <?= $cur_status == 'ready' ? 'selected' : ''; ?>>
                                    Закрытие заказа
                                </option>
                                <option value="<?= $model->getUrlPath(); ?>?status=cancel" <?= $cur_status == 'cancel' ? 'selected' : ''; ?>>
                                    Отмена
                                </option>
                                <?php if ($count_discus_orders > 0) { ?>
                                    <option value="<?= $model->getUrlPath(); ?>?status=discus" <?= $cur_status == 'discus' ? 'selected' : ''; ?>>
                                        Контроль качества
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php if (!empty($items)) { ?>
                    <?php foreach ($items as $item) { ?>
                        <div class="lk_order_item <?= $item->hasUnreadNotifications(Yii::$app->user->identity->userAR->id) ? 'has_notifications' : ''; ?>">
                            <a href="<?= ($view_url ? Url::toRoute([$view_url, 'id' => $item->id]) : ''); ?>"
                               class="lk_order_more <?= $status_class[$item->status]['color']; ?> lk_order_more-basic"><img
                                        src="/img/nav_right-white.svg" alt=""/></a>
                            <h4 class="lk-order-title"><?= $item->service_name; ?></h4>
                            <div class="lk-event-info-wrapper lk-event-info-wrapper6">
                                <div class="lk-event-info <?= $status_class[$item->status]['icon']; ?>"><?= $item->statusName; ?></div>
                                <div class="lk-event-info number"><?= $item->orderNum; ?></div>
                                <?php if (!empty($item->service) && !empty($item->service->serviceType)) { ?>
                                    <div class="lk-event-info learn">
                                        <?= $item->service->serviceType->name; ?>
                                    </div>
                                <?php } else { ?>
                                    <div class="lk-event-info"></div>
                                <?php } ?>
                                <div class="lk-event-info price"><?= number_format($item->price, 0, '', '&nbsp;'); ?>
                                    ₽
                                </div>
                                <?php
                                $date_expired = false;
                                $dates = [];
                                if (!empty($item->execute_start)) {
                                    $dates[] = Yii::$app->formatter->asDatetime($item->execute_start, 'dd.MM.y');
                                }
                                if (!empty($item->execute_before)) {
                                    $dates[] = Yii::$app->formatter->asDatetime($item->execute_before, 'dd.MM.y');
                                    $date_expired = (strtotime($item->execute_before) <= time());
                                }
                                ?>
                                <?php if (!empty($dates)) { ?>
                                    <div class="lk-event-info date <?= $date_expired ? 'red' : ''; ?>"><?= implode(' - ', $dates); ?></div>
                                <?php } else { ?>
                                    <div class="lk-event-info"></div>
                                <?php } ?>
                                <div class="lk-event-info people"><?= $item->user->profile->halfname; ?></div>
                            </div>
                            <a href="<?= ($view_url ? Url::toRoute([$view_url, 'id' => $item->id]) : ''); ?>"
                               class="<?= $status_class[$item->status]['color']; ?> lk_order_more-basic_mobile">Перейти<img
                                        src="/img/nav_right-white.svg" alt=">"/></a>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="lk_block">
                        <main class="lk_content">
                            <?php
                            $types = ['payed' => 'заказов на стадии Оплачен', 'inwork' => 'заказов в работе', 'ready' => 'закрытых заказов', 'cancel' => 'отмененных заказов', 'discus' => 'заказов на стадии Контроль качества']; ?>
                            У вас нет <?= (isset($types[$cur_status]) ? $types[$cur_status] : 'текущих заказов'); ?>.
                        </main>
                    </div>
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