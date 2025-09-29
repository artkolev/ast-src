<?php
// список заказов (МКС)
use app\modules\order\models\Order;
use app\modules\pages\models\OrdersView;
use app\modules\pages\models\SelectPayment;
use app\modules\payment_system\models\PaymentSystem;
use yii\helpers\Url;

$payment_page = SelectPayment::find()->where(['model' => SelectPayment::class, 'visible' => 1])->one();
$colors = [
        Order::STATUS_PAYED => 'blue',
        Order::STATUS_INWORK => 'yellow',
        Order::STATUS_EXECCANCEL => 'red',
        Order::STATUS_USERCANCEL => 'red',
        Order::STATUS_DISCUS => 'maroon',
        Order::STATUS_OFFER => 'darkgreen',

];

$view_page = OrdersView::find()->where(['model' => OrdersView::class, 'visible' => 1])->one();
$view_url = (!empty($view_page) ? $view_page->getUrlPath() : false);
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
                <div class="lk_block desktop-visible">
                    <main class="lk_content lk_content-basic lk_switchers">
                        <a href="<?= $model->getUrlPath(); ?>?status=discus" class="button lk maroon">Контроль качества
                            (<?= $discus_count; ?>)</a>
                        <a href="<?= $model->getUrlPath(); ?>?status=expiried_new" class="button lk">Необработанные
                            (<?= $expiried_new_count; ?>)</a>
                        <a href="<?= $model->getUrlPath(); ?>?status=offers" class="button lk darkgreen">Ждут
                            предложений (<?= $offers_count; ?>)</a>
                        <a href="<?= $model->getUrlPath(); ?>?status=canceled" class="button lk red">Отклоненные
                            (<?= $canceled_count; ?>)</a>
                        <a href="<?= $model->getUrlPath(); ?>?status=expiried" class="button yellow lk">Просроченые
                            (<?= $expiried_count; ?>)</a>
                        <a href="<?= $model->getUrlPath(); ?>" class="button lk gray">Все (<?= $orders_count; ?>)</a>
                    </main>
                </div>
                <div class="lk_block mobile-visible">
                    <div class="lk_block_header">
                        <div class="lk_switchers-select-wrapper mb0">
                            <div class="lk_switchers-text">Статус заказа</div>
                            <select class="lk_switchers-select">
                                <option value="<?= $model->getUrlPath(); ?>" <?= $_SERVER['REQUEST_URI'] == $model->getUrlPath() ? 'selected' : ''; ?>>
                                    Все (<?= $orders_count; ?>)
                                </option>
                                <option value="<?= $model->getUrlPath(); ?>?status=discus" <?= $_SERVER['REQUEST_URI'] == $model->getUrlPath() . '?status=discus' ? 'selected' : ''; ?>>
                                    Контроль качества (<?= $discus_count; ?>)
                                </option>
                                <option value="<?= $model->getUrlPath(); ?>?status=expiried_new" <?= $_SERVER['REQUEST_URI'] == $model->getUrlPath() . '?status=expiried_new' ? 'selected' : ''; ?>>
                                    Необработанные (<?= $expiried_new_count; ?>)
                                </option>
                                <option value="<?= $model->getUrlPath(); ?>?status=offers" <?= $_SERVER['REQUEST_URI'] == $model->getUrlPath() . '?status=offers' ? 'selected' : ''; ?>>
                                    Ждут предложений (<?= $offers_count; ?>)
                                </option>
                                <option value="<?= $model->getUrlPath(); ?>?status=canceled" <?= $_SERVER['REQUEST_URI'] == $model->getUrlPath() . '?status=canceled' ? 'selected' : ''; ?>>
                                    Отклоненные (<?= $canceled_count; ?>)
                                </option>
                                <option value="<?= $model->getUrlPath(); ?>?status=expiried" <?= $_SERVER['REQUEST_URI'] == $model->getUrlPath() . '?status=expiried' ? 'selected' : ''; ?>>
                                    Просроченые (<?= $expiried_count; ?>)
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <?php if (!empty($items)) { ?>
                    <?php foreach ($items as $item) { ?>
                        <div class="lk_order_item <?= $item->hasUnreadNotifications(Yii::$app->user->identity->userAR->id) ? 'has_notifications' : ''; ?>">
                            <a href="<?= ($view_url ? Url::toRoute([$view_url, 'id' => $item->id]) : ''); ?>"
                               class="lk_order_more <?= $colors[$item->status]; ?> lk_order_more-basic"><img
                                        src="/img/nav_right-white.svg" alt=">"/></a>
                            <div class="lk_order_item_info-basic">
                                <div class="grid-template-a">
                                    <div>
                                        <h4>Заказ №:</h4>
                                        <p><?= $item->orderNum; ?></p>
                                    </div>
                                    <?php if (!empty($item->execute_start)) { ?>
                                        <div>
                                            <h4>В работе с:</h4>
                                            <p><?= Yii::$app->formatter->asDatetime($item->execute_start, 'dd.MM.y'); ?></p>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($item->execute_before)) { ?>
                                        <div>
                                            <h4>Завершение заказа:</h4>
                                            <p><?= Yii::$app->formatter->asDatetime($item->execute_before, 'dd.MM.y'); ?></p>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="grid-template-b">
                                    <div>
                                        <h4>Статус:</h4>
                                        <p class="<?= $colors[$item->status]; ?>"><?= $item->statusName; ?></p>
                                    </div>
                                    <div>
                                        <h4>Эксперт:</h4>
                                        <p><?= $item->executor->profile->halfname; ?></p>
                                    </div>
                                    <div>
                                        <h4>Стоимость:</h4>
                                        <p><?= number_format($item->price, 0, '', '&nbsp;'); ?> руб.</p>
                                    </div>
                                </div>
                                <div class="grid-template-c">
                                    <div>
                                        <h4>Услуга:</h4>
                                        <p><?= $item->service_name; ?></p>
                                    </div>
                                    <?php if ($item->status == Order::STATUS_NEW) {
                                        $payment_url = (!empty($payment_page)) ? Url::toRoute([$payment_page->getUrlPath(), 'category' => PaymentSystem::USEDIN_SERVICES, 'id' => $item->id]) : false;
                                        if ($payment_url) { ?>
                                            <div><a href="<?= $payment_url; ?>"
                                                    class="button-o lk blue mb0">Оплатить</a></div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                            <a href="<?= ($view_url ? Url::toRoute([$view_url, 'id' => $item->id]) : ''); ?>"
                               class="<?= $colors[$item->status]; ?> lk_order_more-basic_mobile">Перейти<img
                                        src="/img/nav_right-white.svg" alt=">"/></a>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="lk_block">
                        <main class="lk_content">
                            <?php $get = Yii::$app->request->get();
                            $types = ['expiried_new' => 'необработанных заказов', 'expiried' => 'просроченных заказов', 'canceled' => 'отклоненных заказов', 'discus' => 'заказов в статусе "Контроль качества"', 'offers' => 'заказов, ожидающих предложений']; ?>
                            У вас
                            нет <?= (isset($types[$get['status']]) ? $types[$get['status']] : 'текущих заказов'); ?>.
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