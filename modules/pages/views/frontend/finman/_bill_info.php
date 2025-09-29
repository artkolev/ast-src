<?php

use app\modules\payment\models\Payment;
use app\modules\payment_system\models\BillAnoSystem;
use app\modules\payment_system\models\BillSystem;
use app\modules\payment_system\models\ProdamusAnoSystem;
use app\modules\payment_system\models\ProdamusSystem;

?>
<?php if ($bill) { ?>
    <div class="finance-manager-info-block">
        <div class="finance-manager-info">
            <div class="finance-manager-info-name">Счет</div>
            <?php if (in_array($bill->payment_system, [BillSystem::class, BillAnoSystem::class])) { ?>
                <div class="finance-manager-info-text"><a
                            href="<?= $bill->getPdfLink(); ?>"><?= $bill->payment_id; ?></a></div>
            <?php } elseif (in_array($bill->payment_system, [ProdamusSystem::class, ProdamusAnoSystem::class])) { ?>
                <div class="finance-manager-info-text"><?= $bill->payment_id; ?></div>
            <?php } ?>
        </div>
        <div class="finance-manager-info">
            <div class="finance-manager-info-name">Сумма:</div>
            <div class="finance-manager-info-text email"><?= number_format($bill->summ, 2, '.', ''); ?></div>
        </div>
        <div class="finance-manager-info">
            <div class="finance-manager-info-name">Дата выставления счета</div>
            <div class="finance-manager-info-text"><?= empty($bill->created_at) ? '' : Yii::$app->formatter->asDatetime($bill->created_at, 'php:d.m.Y'); ?></div>
        </div>
        <div class="finance-manager-info">
            <div class="finance-manager-info-name">Статус</div>
            <div class="finance-manager-info-text"><?= $bill->statusName; ?></div>
        </div>
        <div class="finance-manager-info">
            <div class="finance-manager-info-name">Email плательщика:</div>
            <div class="finance-manager-info-text email"><?= $bill->contragent_email; ?></div>
        </div>
        <?php if (!empty($bill->payment_date)) { ?>
            <div class="finance-manager-info">
                <div class="finance-manager-info-name">Дата оплаты</div>
                <div class="finance-manager-info-text"><?= Yii::$app->formatter->asDatetime($bill->payment_date, 'php:d.m.Y'); ?></div>
            </div>
        <?php } ?>
        <?php if (!empty($bill->refund_date)) { ?>
            <div class="finance-manager-info">
                <div class="finance-manager-info-name">Дата возврата средств</div>
                <div class="finance-manager-info-text"><?= Yii::$app->formatter->asDatetime($bill->refund_date, 'php:d.m.Y'); ?></div>
            </div>
        <?php } ?>
        <?php if ($bill->status == Payment::STATUS_NEW) { ?>
            <div>
                <a id="open_bill_pay_form" href="#" data-bill="<?= $bill->id; ?>"
                   data-postpay="<?= $bill->is_postpay; ?>" class="button">
                    <?php if ($bill->is_postpay) { ?>
                        Ввести дату оплаты и изменить статус на “Постоплата проведена”
                    <?php } else { ?>
                        Ввести дату оплаты и изменить статус на оплачен
                    <?php } ?>
                </a>
            </div>
            <?php if (!$bill->is_postpay) { ?>
                <div>
                    <a id="open_bill_postpay_form" href="#" data-bill="<?= $bill->id; ?>"
                       data-payment="<?= $bill->payment_id; ?>" class="button">Отметить счет на ПОСТОПЛАТУ</a>
                </div>
            <?php } else { ?>
                <div>
                    <a id="choose_bill_pay_button" href="#" data-bill="<?= $bill->id; ?>" class="button">Снять признак
                        Постоплата</a>
                </div>
            <?php } ?>
        <?php } ?>
        <?php if ($bill->status == Payment::STATUS_ACCEPTED) { ?>
            <?php if (in_array($bill->payment_system, [BillSystem::class, BillAnoSystem::class])) { ?>
                <div>
                    <a id="open_bill_date_form" href="#" data-bill="<?= $bill->id; ?>"
                       data-paydate="<?= $bill->payment_date ? Yii::$app->formatter->asDatetime($bill->payment_date, 'php:d.m.Y') : ''; ?>"
                       class="button">Изменить дату оплаты</a>
                </div>
                <div>
                    <a id="open_bill_status_form" href="#" data-bill="<?= $bill->id; ?>"
                       data-postpay="<?= $bill->is_postpay; ?>" class="button">Изменить статус</a>
                </div>
            <?php } elseif (in_array($bill->payment_system, [ProdamusSystem::class, ProdamusAnoSystem::class])) { ?>
                <div>
                    <a id="open_bill_status_form" href="#" data-bill="<?= $bill->id; ?>"
                       data-postpay="<?= $bill->is_postpay; ?>" class="button">Изменить статус</a>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
<?php } ?>