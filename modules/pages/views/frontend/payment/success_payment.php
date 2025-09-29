<?php

use app\modules\pages\models\LKEventsTickets;
use app\modules\pages\models\OrdersList;

if ($type == 'order') {
    $order_list = OrdersList::find()->where(['model' => OrdersList::class, 'visible' => 1])->one();
    $ret_url = (!empty($order_list)) ? $order_list->getUrlPath() : false;
    $entity = 'заказов';
} elseif ($type == 'event') {
    $tickets_list = LKEventsTickets::find()->where(['model' => LKEventsTickets::class, 'visible' => 1])->one();
    $ret_url = (!empty($tickets_list)) ? $tickets_list->getUrlPath() : false;
    $entity = 'билетов';
}

?>
<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">
        <div class="lk_maincol">
            <div class="lk_block">
                <main class="lk_content">
                    <section class="register_form_success">
                        <div class="ib">
                            <img src="/img/envelope_big.svg" alt=""/>
                            <h1><?= $model->getNameForView(); ?></h1>
                            <?= $model->content; ?>
                            <?php if ($ret_url) { ?>
                                <a href="<?= $ret_url; ?>" class="button small">Вернуться к списку <?= $entity; ?></a>
                            <?php } ?>
                        </div>
                    </section>
                </main>
            </div>
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>