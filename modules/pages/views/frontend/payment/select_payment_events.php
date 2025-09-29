<?php

use app\modules\payment\models\Payment;
use yii\widgets\ActiveForm;

$user = Yii::$app->user->identity->userAR;
$role = $user->role;
$type_val = ($role == 'urusr' || $role == 'exporg') ? Payment::CONTRAGENT_ORGANIZATION : Payment::CONTRAGENT_INDIVIDUAL;
$normal_price = number_format($order->price, 0, '.', ' ');
$comission_price = number_format(round($order->price * 1.1, 2), 0, '.', ' ');
?>
<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">
        <div class="lk_maincol">

            <div class="lk_block">
                <main class="lk_content lk_content_expert-service-card_basic">
                    <p>Мероприятие:</p>
                    <h4 class="blue"><a href="<?= $order->event->getUrlPath(); ?>"><?= $order->event->name; ?></a></h4>

                    <?php if (!empty($order->items_group)) { ?>
                        <?php foreach ($order->items_group as $name => $count) { ?>
                            <p>Тариф:</p>
                            <h4><?= $name; ?></h4>

                            <p>Количество билетов:</p>
                            <h4><?= $count; ?></h4>
                        <?php } ?>
                    <?php } ?>

                    <div class="lk-total-info">
                        <div class="lk-total-info-text">
                            <p>Стоимость:</p>
                            <h4><?= number_format($order->price, 0, '.', '&nbsp;'); ?> ₽</h4>
                        </div>
                        <?php /* пока нет промокодов
                        <div class="lk-total-info-text">
                            <p>Скидка по промокоду:</p>
                            <h4>10 %</h4>
                        </div>
                        <div class="lk-total-info-text">
                            <p>ИТОГО К ОПЛАТЕ:</p>
                            <h4>4&nbsp;500 ₽</h4>
                        </div>
                        */ ?>
                    </div>
                    <?php /* пока нет промокодов
                    <div class="ip_cell promocode-input">
                        <label class="ip_label">Ввести промокод</label>
                        <div class="flex">
                            <input type="text" class="input_text mr10" placeholder="">
                            <button class="button-o lk small">Применить</button>
                        </div>
                    </div>
                    */ ?>
                </main>
            </div>

            <!-- BLOCK-->
            <div class="lk_block">
                <main class="lk_content">
                    <h4 class="lk-order-title"><?= $model->getNameForView(); ?></h4>
                    <?= $model->content; ?>

                    <?php $form = ActiveForm::begin([
                            'id' => 'paystart-form',
                            'options' => ['class' => ''],
                            'enableAjaxValidation' => false,
                            'enableClientValidation' => false,
                            'fieldConfig' => [
                                    'options' => ['class' => 'ip_cell w100'],
                                    'template' => '{input}{error}{hint}',
                                    'inputOptions' => ['class' => 'button-o checkbox'],
                            ],
                    ]); ?>

                    <?= $form->field($modelform, 'type', ['options' => ['class' => 'ip_cell i-f-column']])->radioList(
                            $modelform->getTypeList(),
                            ['item' => function ($index, $label, $name, $checked, $value) use ($modelform) {
                                $return = '<input ' . ($checked ? 'checked="checked"' : '') . ' type="radio" name="' . $name . '" value="' . $value . '" class="button-o checkbox tab-payment-trigger" data-prodamus="' . $modelform->isProdamusPayment($value) . '" data-tab="billpay_' . $modelform->isBillPayment($value) . '"><label>' . $label . '</label>';
                                return $return;
                            }
                            ]
                    ); ?>

                    <div class="tabs-content">
                        <div class="payer-info-block tab-payment-item" data-tab="billpay_1">
                            <div class="payer-info-title-wrapper">
                                <div class="payer-info-title">Сведения о плательщике</div>
                                <a href="#payer-info" class="button-o payer-btn js-payer-btn">Изменить</a>
                            </div>
                            <div class="payer-info js-payment-fiz-wrapper"
                                 style="<?= ($role == 'urusr' || $role == 'exporg') ? 'display: none;' : 'display: block;'; ?>">
                                <div class="payer-info-subtitle">ФИО</div>
                                <div class="payer-info-text js-payment-fio-text"><?= $user->profile->fullname; ?></div>
                            </div>
                            <div class="js-payment-yur-wrapper"
                                 style="<?= ($role == 'urusr' || $role == 'exporg') ? 'display: block;' : 'display: none;'; ?>">
                                <div class="payer-info">
                                    <div class="payer-info-subtitle">Название организации</div>
                                    <div class="payer-info-text js-payment-organization-text"><?= $user->profile->fullname; ?></div>
                                </div>
                                <div class="payer-info">
                                    <div class="payer-info-subtitle">ИНН организации</div>
                                    <div class="payer-info-text js-payment-inn-text"><?= ($role == 'urusr' || $role == 'exporg') ? $user->organization->inn : ''; ?></div>
                                </div>
                            </div>
                            <div class="payer-info">
                                <div class="payer-info-subtitle">Телефон</div>
                                <div class="payer-info-text js-payment-phone-text"><?= $user->profile->phone; ?></div>
                            </div>
                            <div class="payer-info">
                                <div class="payer-info-subtitle">E-mail</div>
                                <div class="payer-info-text js-payment-email-text"><?= $user->email; ?></div>
                            </div>
                        </div>
                    </div>

                    <?= $form->field($modelform, 'agreements', ['options' => ['class' => 'ip_cell w100 contract']])->widget('app\modules\formagree\widgets\formagree\FormagreeWidget'); ?>

                    <?= $form->field($modelform, 'contragent_inn', ['options' => ['class' => 'hidden']])->hiddenInput(['readonly' => true, 'class' => 'js-payment-contragent-inn-input', 'value' => ($role == 'urusr' || $role == 'exporg') ? $user->organization->inn : '']); ?>
                    <?= $form->field($modelform, 'contragent_name', ['options' => ['class' => 'hidden']])->hiddenInput(['readonly' => true, 'class' => 'js-payment-contragent-name-input', 'value' => $user->profile->fullname]); ?>
                    <?= $form->field($modelform, 'contragent_phone', ['options' => ['class' => 'hidden']])->hiddenInput(['readonly' => true, 'class' => 'js-payment-contragent-phone-input', 'value' => $user->profile->phone]); ?>
                    <?= $form->field($modelform, 'contragent_email', ['options' => ['class' => 'hidden']])->hiddenInput(['readonly' => true, 'class' => 'js-payment-contragent-email-input', 'value' => $user->email]); ?>
                    <?= $form->field($modelform, 'contragent_type', ['options' => ['class' => 'hidden']])->hiddenInput(['readonly' => true, 'class' => 'js-payment-contragent-type-input', 'value' => $type_val]); ?>
                    <?= $form->field($modelform, 'contragent_edo', ['options' => ['class' => 'hidden']])->hiddenInput(['readonly' => true, 'class' => 'js-payment-contragent-edo-input', 'value' => $edo_val]); ?>

                    <div class="total-btn-wrapper">
                        <button id="paybutton" type="submit" class="button">ОПЛАТИТЬ</button>
                        <div class="total-btn-info">
                            <div class="total-btn-info-price">Итого <?= $normal_price; ?> ₽ <span
                                        class="hidden total-btn-info-price-discount"><?= $normal_price; ?> ₽</span>
                            </div>
                            <div class="hidden total-btn-info-price-discount-percent">комиссия 10%</div>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </main>
                <!-- модалка -->
                <div class="modal" id="payer-info">
                    <div class="modal_content">
                        <a href="#" class="modal_close" data-fancybox-close>x</a>
                        <div class="modal_title">Сведения о плательщике</div>
                        <div class="lenta-menu">
                            <a href="#!"
                               class="tab tab-trigger <?= ($role == 'urusr' || $role == 'exporg') ? '' : 'active'; ?>"
                               data-tab="Физлицо">Физлицо</a>
                            <a href="#!"
                               class="tab tab-trigger <?= ($role == 'urusr' || $role == 'exporg') ? 'active' : ''; ?>"
                               data-tab="Юрлицо">Юрлицо</a>
                        </div>
                        <div class="tabs-content">
                            <div class="tab-item <?= ($role == 'urusr' || $role == 'exporg') ? '' : 'active'; ?>"
                                 data-tab="Физлицо">
                                <form>
                                    <div class="ip_cell w100">
                                        <label class="ip_label">Фамилия Имя Отчество</label>
                                        <input type="text" class="input_text js-payment-fio" placeholder=""
                                               value="<?= ($role == 'urusr' || $role == 'exporg') ? '' : $user->profile->fullname; ?>"
                                               required="">
                                    </div>
                                    <div class="ip_cell w100">
                                        <label class="ip_label">Телефон</label>
                                        <input type="text" class="input_text phone-mask js-payment-phone" placeholder=""
                                               value="<?= $user->profile->phone; ?>">
                                    </div>
                                    <div class="ip_cell w100">
                                        <label class="ip_label">E-mail</label>
                                        <input type="text" class="input_text js-payment-email" placeholder=""
                                               value="<?= $user->email; ?>">
                                    </div>
                                    <div class="ip_cell w100 mb0">
                                        <button class="button blue big w100 js-payment-card-save disabled">Продолжить
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-item <?= ($role == 'urusr' || $role == 'exporg') ? 'active' : ''; ?>"
                                 data-tab="Юрлицо">
                                <form>
                                    <div class="ip_cell w100">
                                        <label class="ip_label">Название организации</label>
                                        <input type="text" class="input_text js-payment-organization" placeholder=""
                                               required=""
                                               value="<?= ($role == 'urusr' || $role == 'exporg') ? $user->profile->fullname : ''; ?>">
                                    </div>
                                    <div class="ip_cell w100">
                                        <label class="ip_label">ИНН</label>
                                        <input type="text" class="input_text inn-mask-auto js-payment-inn"
                                               placeholder="" required=""
                                               value="<?= ($role == 'urusr' || $role == 'exporg') ? $user->organization->inn : ''; ?>">
                                    </div>
                                    <div class="ip_cell w100">
                                        <label class="ip_label">Телефон</label>
                                        <input type="text" class="input_text js-payment-phone" placeholder=""
                                               value="<?= $user->profile->phone; ?>">
                                    </div>
                                    <div class="ip_cell w100">
                                        <label class="ip_label">E-mail</label>
                                        <input type="text" class="input_text js-payment-email" placeholder=""
                                               value="<?= $user->email; ?>">
                                    </div>
                                    <div class="ip_cell w100">
                                        <input type="checkbox" class="ch js-payment-edo" name="have-edo" value="1">
                                        <label>Используем ЭДО</label>
                                    </div>
                                    <div class="ip_cell w100 mb0">
                                        <button class="button blue big w100 js-payment-schet-save disabled">Продолжить
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal_overlay"></div>
                </div>
            </div>
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>
<?php
$this->registerCssFile('/css/style-blog.css', ['depends' => [app\assets\AppAsset::class]]);
$org_val = Payment::CONTRAGENT_ORGANIZATION;
$fiz_val = Payment::CONTRAGENT_INDIVIDUAL;
$js = <<<JS

    $('.tab-payment-trigger').click(function(){
        let tab = $(this).data('tab');
        let prodamus = $(this).data('prodamus');
        $('.tab-payment').removeClass('active');
        $(this).addClass('active');
        $('.tab-payment-item').removeClass('active');
        $('.tab-payment-item[data-tab="'+ tab +'"]').addClass('active');
        if(tab == 'billpay_1') {
            if($('.js-payment-fiz-wrapper').is(':visible')) {
                $('.js-payment-contragent-type-input').val('{$fiz_val}');
            } else if($('.js-payment-yur-wrapper').is(':visible')) {
                $('.js-payment-contragent-type-input').val('{$org_val}');
            } else $('.js-payment-contragent-type-input').val('{$type_val}');
            $('#paybutton').text('ВЫСТАВИТЬ СЧЕТ');
        } else {
            $('.js-payment-contragent-inn-input, .js-payment-contragent-name-input, .js-payment-contragent-type-input, .js-payment-contragent-phone-input, .js-payment-contragent-email-input').val('');
            $('.js-payment-contragent-edo-input').prop('checked', false);
            $('#paybutton').text('ОПЛАТИТЬ');
        }
        if(prodamus == '1'){
            $('.total-btn-info-price').get(0).childNodes[0].nodeValue='Итого {$comission_price} ₽ ';
            $('.total-btn-info-price-discount').show();
            $('.total-btn-info-price-discount-percent').show();
        }
        else{
            $('.total-btn-info-price').get(0).childNodes[0].nodeValue='Итого {$normal_price} ₽ ';
            $('.total-btn-info-price-discount').hide();
            $('.total-btn-info-price-discount-percent').hide();
        }
    });

    $('body').on('keyup','.js-payment-fio, [data-tab="Физлицо"] .js-payment-phone, [data-tab="Физлицо"] .js-payment-email', function(e){
        let fio = $(this).closest('form').find('.js-payment-fio');
        let phone = $(this).closest('form').find('.js-payment-phone');
        let email = $(this).closest('form').find('.js-payment-email');
        if(fio.val() != '') {
            $(this).closest('form').find('button').removeClass('disabled');
        } else {
            $(this).closest('form').find('button').addClass('disabled');
        }
    });

    $('body').on('keyup','.js-payment-organization, .js-payment-inn, [data-tab="Юрлицо"] .js-payment-phone, [data-tab="Юрлицо"] .js-payment-email', function(e){
        let organization = $(this).closest('form').find('.js-payment-organization');
        let inn = $(this).closest('form').find('.js-payment-inn');
        let phone = $(this).closest('form').find('.js-payment-phone');
        let email = $(this).closest('form').find('.js-payment-email');
        if(($(organization).val() != '') && ($(inn).val() != '')) {
            $(this).closest('form').find('button').removeClass('disabled');
        } else {
            $(this).closest('form').find('button').addClass('disabled');
        }
    });

    $('body').on('click','.js-payment-card-save', function(e){
        e.preventDefault();
        let fio = $(this).closest('form').find('.js-payment-fio');
        let phone = $(this).closest('form').find('.js-payment-phone');
        let email = $(this).closest('form').find('.js-payment-email');
        if(fio.val() != '') {
            $('.js-payment-fio-text').html(fio.val());
            $('.js-payment-contragent-name-input').val(fio.val());
        }
        if(phone.val() != '') {
            $('.js-payment-phone-text').html(phone.val());
            $('.js-payment-contragent-phone-input').val(phone.val());
        } else {
            $('.js-payment-phone-text').html('');
            $('.js-payment-contragent-phone-input').val('');
        }
        if(email.val() != '') {
            $('.js-payment-email-text').html(email.val());
            $('.js-payment-contragent-email-input').val(email.val());
        } else {
            $('.js-payment-email-text').html('');
            $('.js-payment-contragent-email-input').val('');
        }
        $('.js-payment-contragent-inn-input').val('');
        $('.js-payment-contragent-type-input').val('individual');
        $('.js-payer-btn').html('Изменить');
        $('.js-payment-fiz-wrapper').show();
        $('.js-payment-yur-wrapper').hide();
        $.fancybox.close();
        //$(this).closest('form')[0].reset();
    });

    $('body').on('click','.js-payment-schet-save', function(e){
        e.preventDefault();
        let organization = $(this).closest('form').find('.js-payment-organization');
        let inn = $(this).closest('form').find('.js-payment-inn');
        let phone = $(this).closest('form').find('.js-payment-phone');
        let email = $(this).closest('form').find('.js-payment-email');
        let edo = $(this).closest('form').find('.js-payment-edo');
        if(organization.val() != '') {
            $('.js-payment-organization-text').html(organization.val());
            $('.js-payment-contragent-name-input').val(organization.val());
        }
        if(inn.val() != '') {
            $('.js-payment-inn-text').html(inn.val());
            $('.js-payment-contragent-inn-input').val(inn.val());
        }
        if(phone.val() != '') {
            $('.js-payment-phone-text').html(phone.val());
            $('.js-payment-contragent-phone-input').val(phone.val());
        } else {
            $('.js-payment-phone-text').html('');
            $('.js-payment-contragent-phone-input').val('');
        }
        if(email.val() != '') {
            $('.js-payment-email-text').html(email.val());
            $('.js-payment-contragent-email-input').val(email.val());
        } else {
            $('.js-payment-email-text').html('');
            $('.js-payment-contragent-email-input').val('');
        }
        if(edo.prop('checked')) {
            $('.js-payment-contragent-edo-input').val(edo.val());
        } else {
            $('.js-payment-contragent-edo-input').val('');
        }
        $('.js-payment-contragent-type-input').val('organization');
        $('.js-payer-btn').html('Изменить');
        $('.js-payment-fiz-wrapper').hide();
        $('.js-payment-yur-wrapper').show();
        $.fancybox.close();
        //$(this).closest('form')[0].reset();
    });

    // модалка
    $('body').on('click','.payer-btn, a[href="#payer-info"], button[href="#payer-info"]', function(e){
        e.preventDefault();
        $.fancybox.open({
            src: '#payer-info',
            type: 'inline'
        });
    });

    $('body').on('click', '#paystart-form button', function() {
        let ths = $(this);
        ths.prop('disabled', true);
        ths.closest('form').submit();
		setTimeout(function() {
			ths.prop('disabled', false);
		}, 3000);
    });

JS;
$this->registerJs($js);
?>
