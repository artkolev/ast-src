<?php
/*
    отображение цен существующего тарифа
*/

if (!empty($tariff)) {
    $prices = $tariff->prices; ?>
    <div class="append-price-list">
        <div class="ip_cell ip_cell-event-date append-price-element flex align-bottom w100 mb0">
            <?php if (is_array($prices) && (count($prices) > 0)) {
                $first_price = ['id' => $prices[0]->id, 'price' => $prices[0]->price, 'start_publish' => Yii::$app->formatter->asDatetime($prices[0]->start_publish, 'php:d.m.Y')];
            } else {
                $first_price = ['id' => '', 'price' => '', 'start_publish' => ''];
            } ?>
            <input type="hidden" name="LKEduprogTariff[prices][0][id]" value="<?= $first_price['id']; ?>"/>
            <div class="ip_cell datarange_ipc mr20">
                <input type="number" name="LKEduprogTariff[prices][0][price]" class="input_text"
                       value="<?= $first_price['price']; ?>" placeholder="Цена, ₽"/>
            </div>
            <div class="ip_cell datarange_ipc mr20">
                <label class="ip_label">Начало действия</label>
                <input type="text" name="LKEduprogTariff[prices][0][start_publish]" value="" class="input_text disabled"
                       disabled placeholder="С момента публикации"/>
            </div>
        </div>
        <div class="append-js">
            <?php if (is_array($prices) && (count($prices) > 1)) {
                for ($i = 1; $i < count($prices); $i++) { // выводим все цены, кроме первой?>
                    <div class="ip_cell ip_cell-event-date append-price-element flex align-bottom w100 mb0">
                        <input type="hidden" name="LKEduprogTariff[prices][<?= $i; ?>][id]"
                               value="<?= $prices[$i]->id; ?>"/>
                        <div class="ip_cell datarange_ipc mr20">
                            <input type="number" name="LKEduprogTariff[prices][<?= $i; ?>][price]" class="input_text"
                                   value="<?= $prices[$i]->price; ?>" placeholder="Цена, ₽"/>
                        </div>
                        <div class="ip_cell datarange_ipc mr20">
                            <label class="ip_label">Начало действия</label>
                            <input type="text" name="LKEduprogTariff[prices][<?= $i; ?>][start_publish]"
                                   class="input_text datepicker-top"
                                   value="<?= Yii::$app->formatter->asDatetime($prices[$i]->start_publish, 'php:d.m.Y'); ?>"
                                   placeholder="дд.мм.гг"/>
                            <a href="#!" class="remove-eduprog-price-js"></a>
                        </div>
                    </div>
                <?php }
            } ?>
        </div>
        <div class="ip_cell flex align-center w100 add-price-eduprog-block-js" <?= (count($prices) >= 5 ? 'style="display:none"' : ''); ?>>
            <button class="button blue small mb0 add-price-eduprog-js" data-pricecount="<?= max(1, count($prices)); ?>">
                Добавить цену
            </button>
            <div class="question_box">
                <a href="javascript:void(0)" class="question_icon">?</a>
                <div class="question_text">
                    Вы можете изменять цену билета по мере приближения события
                </div>
            </div>
        </div>
    </div>
<?php } ?>