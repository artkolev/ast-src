<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <div class="lk_block">
                    <header class="lk_block_header">
                        <h1 class="lk_block_title-big"><?= $model->getNameForView(); ?></h1>
                        <?= $model->content; ?>
                    </header>
                </div>
                <?php $form = ActiveForm::begin([
                        'id' => 'message-form',
                        'action' => '/site/ajaxValidate/',
                        'options' => ['class' => ''],
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => true,
                        'validateOnSubmit' => true,
                        'validateOnChange' => true,
                        'validateOnType' => false,
                        'validateOnBlur' => true,
                        'fieldConfig' => [
                                'options' => ['class' => 'ip_cell'],
                                'template' => '{input}{error}{hint}',
                                'inputOptions' => ['class' => ''],
                        ],
                ]); ?>

                <div class="directions_search_box">
                    <div class="search_flex">
                        <?= $form->field($modelform, 'user_id')->dropDownList([], ['class' => "pretty_select_search_user"]); ?>
                    </div>
                </div>
                <div class="mesage_form_wrapper hidden">
                    <div id="user_info" class="lk_order_msg-block">
                        <header class="lk_order_msg-header">
                            <div class="lk_order_msg-avatar">
                                <img src="" alt="">
                            </div>
                            <div>
                                <h4 class="lk_order_msg-name"></h4>
                                <div class="date"></div>
                            </div>
                        </header>
                        <article class="lk_order_msg-text">
                            <p>Основная кафедра - </p>
                        </article>
                    </div>

                    <div class="lk_block">
                        <main class="lk_content">
                            <div class="chat_answer_box">
                                <?= $form->field($modelform, 'message', ['options' => ['class' => 'ip_cell w100']])->textarea(['class' => "input_text chat_answer", 'placeholder' => 'Написать сообщение']); ?>
                                <div class="chat_answer_buttons">
                                    <button class="button medium lk">Отправить</button>
                                </div>
                            </div>
                        </main>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
        </div>
    </main>
    <div class="modal" id="success_message_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Отправка сообщения</div>
                <p>Отправка сообщения невозможна. <?= \app\helpers\MainHelper::getHelpText(); ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
<?php
$url = Url::toRoute(['/pages/message/getusers']);
$url_user = Url::toRoute(['/pages/message/getuserinfo']);
$url_create = Url::toRoute(['/pages/message/newmessage']);
$js = <<<JS
    $('.pretty_select_search_user').select2({
        placeholder: 'Поиск по участникам АСТ (введите имя)',
        minimumInputLength: 4,
        language: {
            noResults: function () {
              return 'Ничего не найдено';
            },
            searching: function () {
              return 'Поиск…';
            },
            errorLoading: function () {
              return 'Результаты не могут быть загружены';
            },
            inputTooShort: function(args) { 
                return "Введите еще минимум "+ (parseInt(args.minimum) - parseInt(args.input.length))+" символ(а)";
            }
        },
        ajax: {
            delay: 300,
            url: '{$url}',
            data: function (params) {
                var queryParameters = {
                  q: params.term
                }

                return queryParameters;
            },
            processResults: function (data) {
                return {
                  results: data.items
                };
              },
            dataType: 'json'
        }
    });

    $('#messagenew-user_id').change(function(){
        var value = $(this).val();
        $.ajax({
            type: 'GET',
            url: '{$url_user}',
            processData: true,
            dataType: 'json',
            data: {id:value},
            success: function(data){
                if (data.status == 'success') {
                    // вывести блок с инфой о пользователе
                    $('#user_info .lk_order_msg-avatar img').attr('src',data.info.image);
                    $('#user_info .lk_order_msg-avatar img').attr('alt',data.info.name);
                    $('#user_info .lk_order_msg-name').html(data.info.name);
                    $('#user_info .date').html(data.info.role);
                    $('#user_info .lk_order_msg-text p').html('Основная кафедра - '+data.info.main_direction);
                    $('.mesage_form_wrapper').removeClass('hidden');
                } else {
                    $('.mesage_form_wrapper').addClass('hidden');
                }
            }
        });
    });

    $('#message-form').on('beforeSubmit', function(event) {
        var formData = new FormData($('#message-form')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url_create}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    // переход к чату с пользователем
                    window.location.href = data.redirect_to;
                } else {
                    // показать модалку с ошибкой
                    if (data.message.length > 0) {
                        $('#success_message_modal .success_box p').html(data.message);
                    }
                    modalPos('#success_message_modal');
                }
            }
        });
        return false;
    });
    $('#message-form').on('submit', function(e){
        e.preventDefault();
        return false;
    });


JS;
$this->registerJs($js);
