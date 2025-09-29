<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;

$countTickets = array_sum($tickets);
?>
<section class="sec content_sec gray_bg">
    <div class="container small780">
        <h2><?= $model->getNameForView(); ?></h2>
        <?php if (Yii::$app->user->isGuest && !empty($login_page)) {
            $user = false; ?>
            <div class="auth-notice">
                Уже пользовались ast-academy.ru? <a href="<?= $login_page->getUrlPath(); ?>">Авторизуйтесь!</a>
                <a href="#!" class="close-auth-notice"></a>
            </div>
        <?php } else {
            $user = Yii::$app->user->identity->userAR;
        } ?>

        <div class="lk_block">
            <main class="lk_content lk_content_expert-service-card_basic">
                <p>Мероприятие:</p>
                <h4 class="blue"><a href="<?= $event->getUrlPath(); ?>"><?= $event->nameForView; ?></a></h4>
                <p>К оплате:</p>
                <h4 id="current-price"><?= number_format($current_summ, 0, '.', '&nbsp;'); ?> ₽</h4>
            </main>
        </div>

        <?php $form = ActiveForm::begin([
                'id' => 'order-event',
                'action' => '/site/ajaxValidate/',
                'options' => ['class' => 'marked js-validation', 'autocomplete' => 'off'],
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'validateOnSubmit' => true,
                'validateOnChange' => true,
                'validateOnType' => false,
                'validateOnBlur' => true,
                'fieldConfig' => [
                        'options' => ['class' => 'ip_cell label-on w100'],
                        'template' => '{input}{label}<div class="input-status"></div>{error}{hint}',
                        'inputOptions' => ['class' => 'input_text'],
                        'labelOptions' => ['class' => 'ip_label'],
                ],
        ]); ?>
        <?php if (empty($user->profile->name) || empty($user->profile->surname) || empty($user->profile->phone)) { ?>
            <div class="lk_block">
                <main class="lk_content lk_content_select-payment_basic">
                    <h2>Контактная информация</h2>
                    <div class="buy-ticket-detail">
                        Укажите ваши контактные данные для связи
                    </div>
                    <div style="display:none;">
                        <?= $form->field($modelform, 'eventform_id')->hiddenInput(); ?>
                    </div>

                    <div id="fiz" class="register_form request_form tab-form">
                        <div class="ip-cells">
                            <?= $form->field($modelform, 'customer_name')->textInput(['required' => 'required', 'class' => 'input_text']); ?>
                            <?= $form->field($modelform, 'customer_surname')->textInput(['required' => 'required', 'class' => 'input_text']); ?>
                            <?= $form->field($modelform, 'customer_phone')->input('tel', ['required' => 'required', 'placeholder' => '+7 (000) 000-00-00', 'class' => 'input_text phone-mask']); ?>
                        </div>
                    </div>

                </main>
            </div>
        <?php } else { ?>
            <div style="display:none;">
                <?= $form->field($modelform, 'eventform_id')->hiddenInput(); ?>
                <?= $form->field($modelform, 'customer_name')->hiddenInput(); ?>
                <?= $form->field($modelform, 'customer_surname')->hiddenInput(); ?>
                <?= $form->field($modelform, 'customer_phone')->hiddenInput(); ?>
            </div>
        <?php } ?>
        <?php
        $participant_num = 0;
        $modelform->ticket_info = ['tariff' => [], 'name' => [], 'surname' => []];
        $dop_class = '';
        if ($user) {
            $modelform->ticket_info['name'][0] = $user->profile->name;
            $modelform->ticket_info['surname'][0] = $user->profile->surname;
            $dop_class = 'active';
        }
        $event_form = $tariffs[0]->eventform;
        foreach ($tariffs as $key => $tariff) {
            $buy_for_yourself = true; ?>
            <?php for ($ticket_num = 0; $ticket_num < $tickets[$tariff->id]; $ticket_num++) {
                $participant_num++;
                if ($buy_for_yourself == true && $user) {
                    $modelform->ticket_info['name'][$participant_num - 1] = $user->profile->name;
                    $modelform->ticket_info['surname'][$participant_num - 1] = $user->profile->surname;
                }
                $modelform->ticket_info['tariff'][$participant_num - 1] = $tariff->id; ?>
                <div class="lk_block k_participant_block listener_block" data-price="<?= $tariff->currentPrice; ?>">
                    <main class="lk_content lk_content_select-payment_basic relative">
                        <?php if ($countTickets > 1) { ?>
                            <div class="remove-member js-remove-member"></div>
                        <?php } ?>
                        <h2 class="listener_part_num">Участник <?= $participant_num; ?></h2>
                        <div style="display:none;">
                            <?= $form->field($modelform, 'ticket_info[tariff][' . ($participant_num - 1) . ']')->hiddenInput(); ?>
                        </div>
                        <div class="buy-ticket-detail">
                            <p><?= $tariff->name; ?></p>
                            <div class="buy-ticket-detail-price"><?= number_format($tariff->currentPrice, 0, '.', '&nbsp;'); ?>
                                ₽
                            </div>
                        </div>
                        <div class="ip-cells">
                            <?= $form->field($modelform, 'ticket_info[name][' . ($participant_num - 1) . ']', ['template' => '{input}<label class="ip_label">Имя*</label><div class="input-status"></div>{error}{hint}'])->textInput(['placeholder' => 'Имя', 'required' => 'required', 'class' => 'input_text name_reciev ' . (($buy_for_yourself == true) ? $dop_class : '')]); ?>
                            <?= $form->field($modelform, 'ticket_info[surname][' . ($participant_num - 1) . ']', ['template' => '{input}<label class="ip_label">Фамилия*</label><div class="input-status"></div>{error}{hint}'])->textInput(['placeholder' => 'Фамилия', 'required' => 'required', 'class' => 'input_text surname_reciev ' . (($buy_for_yourself == true) ? $dop_class : '')]); ?>
                        </div>
                        <?php if (!empty($event_form) && !empty($event_form->form_fields)) {
                            foreach ($event_form->form_fields as $key => $question) {
                                if (!$question['visible']) {
                                    continue;
                                }
                                $star = $question['required'] ? '*' : '';
                                $required = $question['required'] ? 'required' : false;
                                $comment = $question['comment'] ? '<p>' . $question['comment'] . '</p>' : '';
                                switch ($question['type']) {
                                    case 'text':
                                        echo $comment;
                                        // yii выполняет encode() для всех передаваемых значений аттрибутов, поэтому, если передать уже кодированные сущности, он их кодирует второй раз. Поэтому для placeholder выполняю htmlspecialchars_decode() иначе кавычки идут как &amp;quote;
                                        echo $form->field($modelform, 'ticket_info[' . $question['sysname'] . '][' . ($participant_num - 1) . ']', ['template' => '{input}<label class="ip_label">' . $question['name'] . $star . '</label><div class="input-status"></div>{error}{hint}'])->textInput(['placeholder' => htmlspecialchars_decode($question['name'] . $star), 'required' => $required]);
                                        break;
                                    case 'textarea':
                                        echo $comment;
                                        echo $form->field($modelform, 'ticket_info[' . $question['sysname'] . '][' . ($participant_num - 1) . ']', ['template' => '{input}<label class="ip_label">' . $question['name'] . $star . '</label><div class="input-status"></div>{error}{hint}'])->textArea(['placeholder' => htmlspecialchars_decode($question['name'] . $star), 'required' => $required, 'class' => 'input_text middle']);
                                        break;
                                    case 'radio_list':
                                        echo '<p>' . $question['name'] . $star . '</p>';
                                        echo $comment;
                                        $list_values = array_combine($question["list_values"], $question["list_values"]);
                                        echo $form->field($modelform, 'ticket_info[' . $question['sysname'] . '][' . ($participant_num - 1) . ']', ['template' => '{input}{hint}{error}', 'options' => ['class' => 'ip_cell w100']])->radioList($list_values, ['item' => function ($index, $label, $name, $checked, $value) {
                                            $return = '<div class="ip_cell label-on w100"><input class="ch" type="radio" name="' . $name . '" value="' . htmlspecialchars($value) . '" /><label>' . $label . '</label></div>';
                                            return $return;
                                        }
                                        ]);
                                        break;
                                    case 'boolean_list':
                                        echo '<p>' . $question['name'] . $star . '</p>';
                                        echo $comment;
                                        $list_values = array_combine($question["list_values"], $question["list_values"]);
                                        echo $form->field($modelform, 'ticket_info[' . $question['sysname'] . '][' . ($participant_num - 1) . '][]', ['template' => '{input}{hint}{error}', 'options' => ['class' => 'ip_cell w100']])->checkboxList($list_values, ['item' => function ($index, $label, $name, $checked, $value) {
                                            $return = '<div class="ip_cell label-on w100"><input class="ch" type="checkbox" name="' . $name . '" value="' . htmlspecialchars($value) . '" /><label>' . $label . '</label></div>';
                                            return $return;
                                        }
                                        ]);
                                        break;
                                } ?>
                            <?php } ?>
                        <?php } ?>
                    </main>
                </div>
                <?php
                $buy_for_yourself = false;
            } ?>
        <?php } ?>
        <div class="lk_block">
            <main class="lk_content lk_content_select-payment_basic">
                <?= $form->field($modelform, 'agreements', ['options' => ['class' => 'ip_cell w100'], 'template' => '{input}{error}{hint}'])->widget('app\modules\formagree\widgets\formagree\FormagreeWidget'); ?>
                <button type="submit" class="button js-submit-button">
                    <?= $current_summ > 0 ? 'Перейти к оплате' : 'Продолжить'; ?>
                </button>
            </main>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</section>

<div class="modal" id="fail_order_modal">
    <div class="modal_content">
        <a href="#" class="modal_close">x</a>
        <div class="success_box">
            <div class="modal_title">Ошибка оформления заказа</div>
            <p>При сохранении данных возникли ошибки. <?= \app\helpers\MainHelper::getHelpText() ?></p>
            <div class="modal_buttons">
                <a href="#" class="button small close_modal">ОК</a>
            </div>
        </div>
    </div>
    <div class="modal_overlay"></div>
</div>

<?php
$url = Url::toRoute(['/pages/activities/orderevent']);
$js = <<<JS

    $('.hid_name').change(function(){
        let new_val = $(this).val();
        $('.k_self_buy:checked').each(function(){
            $($(this).closest('.k_participant_block').find('.name_reciev')[0]).val(new_val).trigger('change');
        });
    });
    $('.hid_surname').change(function(){
        let new_val = $(this).val();
        $('.k_self_buy:checked').each(function(){
            $($(this).closest('.k_participant_block').find('.surname_reciev')[0]).val(new_val).trigger('change');
        });
    });

    $('.k_self_buy').change(function(e) {
        let name_val = '';
        let surname_val = '';
        if($(this).is(':checked')) {
            name_val = $('.hid_name').val();
            surname_val = $('.hid_surname').val();
        }
        $($(this).closest('.k_participant_block').find('.name_reciev')[0]).val(name_val).trigger('change');
        $($(this).closest('.k_participant_block').find('.surname_reciev')[0]).val(surname_val).trigger('change');
    });

    $('.name_reciev').keypress(function(e) {
        if ($($(this).closest('.k_participant_block').find('.k_self_buy:checked')).length > 0) {
            $($(this).closest('.k_participant_block').find('.k_self_buy:checked')[0]).trigger('click');
        }
    });

    $('#order-event').on('beforeSubmit', function(event){
        $('#order-event .js-submit-button').prop('disabled', true);
        var formData = new FormData($('#order-event')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    // переадресация на страницу оплаты, либо информационную про авторизацию
                    window.location.href = data.redirect_to;
                } else {
                    // показать модалку с ошибкой
                    $('#fail_order_modal .modal_title').html('Ошибка оформления заказа');
                    $('#fail_order_modal p').html(data.message);
                    modalPos('#fail_order_modal');
                    $('#order-event .js-submit-button').prop('disabled', false);
                }
            },
			error: function() {
				$('#order-event .js-submit-button').prop('disabled', false);
			}
        });
        return false;
    });
    $('#order-event').on('submit', function(e){
        e.preventDefault();
        return false;
    });

    $('#order-event').on('beforeValidate', function(event){
        $('#order-event .js-submit-button').prop('disabled', true);
    });

    $('#order-event').on('afterValidate', function(event, messages, errorAttributes){
        if (errorAttributes.length > 0) {
            $('#order-event .js-submit-button').prop('disabled', false);
        }
    });
    
    function calculateTotalPrice() {
      let totalPrice = 0;
      const listenerBlocks = $('.listener_block');
      const blockCount = listenerBlocks.length;
  
      listenerBlocks.each(function() {
        const price = parseFloat($(this).data('price'));
        
        if (!isNaN(price)) {
          totalPrice += price;
        }
      });
    
      $('#current-price').text(totalPrice.toLocaleString('ru-RU') + ' ₽');
      
       if (blockCount === 1) {
        $('.remove-member').remove();
      }
    }
    
    $('body').on('click', '.js-remove-member', function(){
    	$(this).parents('.lk_block').remove();
        
        const elements = document.querySelectorAll('.listener_part_num');
        
        elements.forEach((element, index) => {
          element.textContent = `Участник ` + (index + 1);
        });
        
        calculateTotalPrice();
    });
JS;
$this->registerJs($js);
?>
