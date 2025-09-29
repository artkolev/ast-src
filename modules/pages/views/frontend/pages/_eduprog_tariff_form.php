<?php

use app\helpers\MainHelper;

const TICKET_COUNT_LIMIT = 1000;

/**
 * @var $form_eduprog instance of \app\modules\eduprog\models\EduprogForm
 */
$single_tariff = (count($form_eduprog->publicTariffes) == 1);
$tariff_total_summ = 0;
foreach ($form_eduprog->publicTariffes as $tariff) {
    $ticket_count = min($tariff->remainTickets, TICKET_COUNT_LIMIT);
    if ($tariff->min_to_buy > $ticket_count) {
        $ticket_count = 0;
    }
    $later_prices = $tariff->getLaterPrices();
    $later_prices_data = [];
    if (!empty($later_prices)) {
        foreach ($later_prices as $price_item) {
            $later_prices_data[] = 'c ' . Yii::$app->formatter->asDate($price_item->start_publish, 'php:j F') . ' — ' . number_format($price_item->price, 0, '.', '&nbsp;') . ' ₽';
        }
    }
    if ($single_tariff) {
        $tariff_total_summ = $tariff->currentPrice;
    }
    ?>
    <div class="buy-tickets-tariff <?= $single_tariff ? 'choise' : ''; ?>">
        <div class="buy-tickets-info">
            <?php if ($tariff->limit_tickets && ($ticket_count < TICKET_COUNT_LIMIT) && ($ticket_count > 0)) { ?>
                <div class="buy-tickets-label">
                    Осталось <?= $ticket_count; ?> <?= MainHelper::pluralForm($ticket_count, ['место', 'места', 'мест']); ?></div>
            <?php } ?>
            <div class="buy-tickets-name"><?= $tariff->name; ?></div>
            <?php if (!empty($tariff->description)) { ?>
                <div class="buy-tickets-text"><?= nl2br($tariff->description); ?></div>
            <?php } ?>
            <?php if ($tariff->min_to_buy > 1) { ?>
                <div class="buy-tickets-text">Минимальное количество мест в заказе - <?= $tariff->min_to_buy; ?></div>
            <?php } ?>
        </div>
        <div class="buy-tickets-data">
            <div class="buy-tickets-price-info">
                <span class="buy-tickets-price buy-tickets-price-js"
                      data-price="<?= number_format($tariff->currentPrice, 0, '.', ''); ?>"><?= ($tariff->currentPrice > 0) ? number_format($tariff->currentPrice, 0, '.', '&nbsp;') . ' ₽' : 'Бесплатно'; ?></span>
                <?php if ($ticket_count < 1) { ?>
                    <span class="buy-tickets-other-price no-bilet-text">Места закончились</span>
                <?php } ?>
                <?php if (!empty($later_prices_data)) { ?>
                    <span class="buy-tickets-other-price"><?= implode('<br>', $later_prices_data); ?></span>
                <?php } ?>
            </div>
            <div class="buy-tickets-count-info <?= ($ticket_count < 1) ? 'disabled' : ''; ?>">
                <?php if ($tariff->min_to_buy > 1) { ?>
                    <div class="buy-tickets-count-text">от <?= $tariff->min_to_buy; ?> шт.</div>
                <?php } ?>
                <div class="input-count-group">
                    <button type="button" class="btn-number btn-minus" data-type="minus" data-field="count" disabled>−
                    </button>
                    <!-- задать атрибут  если минимальное кол-во покупаемых билетов больше одного -->
                    <input type="text" <?= ($tariff->min_to_buy > 1) ? 'min-count="' . $tariff->min_to_buy . '"' : ''; ?>
                           data-tariff="<?= $tariff->id; ?>" name="count" class="input-number number bilet_qty"
                           value="<?= $single_tariff ? '1' : '0'; ?>" min="<?= $single_tariff ? '1' : '0'; ?>"
                           max="<?= $ticket_count; ?>" readonly onkeyup="this.value = this.value.replace(/[^\d]/g,'');">
                    <button type="button" class="btn-number btn-plus" data-type="plus" data-field="count">+</button>
                </div>
            </div>
            <input type="hidden" class="buy-tickets-price-count-js"
                   value="<?= ($single_tariff ? number_format($tariff->currentPrice, 0, '.', '') : 0); ?>">
        </div>
    </div>
<?php } ?>

<div class="buy-tickets-footer">
    <input type="hidden" name="total-count" class="tickets-total-count-hidden-js"
           value="<?= $single_tariff ? '1' : '0'; ?>">
    <input type="hidden" name="total" class="tickets-total-hidden-js"
           value="<?= number_format($tariff_total_summ, 0, '.', ''); ?>">
    <button class="button blue tickets-total-js buy_tickets <?= $single_tariff ? '' : 'disabled'; ?>">
        <?= ($single_tariff ? ($tariff_total_summ > 0 ? 'Купить 1 за ' . number_format($tariff_total_summ, 0, '.', '&nbsp;') . ' ₽' : 'Оформить') : 'Выберите тариф'); ?>
    </button>
</div>