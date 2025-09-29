<?php

use app\models\OfferOrderForm;
use app\modules\pages\models\QueriesListMks;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$queries_list = QueriesListMks::find()->where(['model' => QueriesListMks::class, 'visible' => 1])->one();
$queries_url = (!empty($queries_list)) ? $queries_list->getUrlPath() : false;
?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <?php if ($queries_url) { ?>
                    <a href="<?= $queries_url; ?>" class="button-o back">Вернуться к запросам</a>
                <?php } ?>

                <div class="lk_order_item">
                    <div class="lk_order_item_info-basic">
                        <div class="grid-template-a">
                            <div><h4>Запрос №:</h4>
                                <p><?= $query->queryNum; ?></p></div>
                            <div><h4>Отправлен:</h4>
                                <p><?= Yii::$app->formatter->asDatetime($query->created_at, 'd.MM.y'); ?></p></div>
                        </div>
                        <div class="grid-template-b">
                            <div><h4>Статус:</h4>
                                <p class="green"><?= $query->statusName; ?></p></div>
                            <div><h4>Клиент:</h4>
                                <p><?= $query->user->profile->halfname; ?></p></div>
                            <div><h4>Эксперт:</h4>
                                <p><?= $query->executor->profile->halfname; ?></p></div>
                        </div>
                    </div>
                </div>
                <div class="lk_block">
                    <main class="lk_content lk_content-basic">
                        <p>Услуга:</p>
                        <h4><?= $query->service_name; ?></h4>
                        <?= !empty($query->service_descr) ? '<h4>' . $query->service_descr . '</h4>' : ''; ?>
                        <?php if (!empty($query->user_comment)) { ?>
                            <p>Комментарий:</p>
                            <h5><?= $query->user_comment; ?></h5>
                        <?php } ?>
                        <a href="#" class="button lk gray open_slidebox" data-slide_box="history">История запроса</a>
                        <a href="#" class="button lk maroon reflect_status" data-action="close"
                           data-query="<?= $query->id; ?>">Закрыть запрос</a>
                        <a href="#" class="button lk darkgreen open_slidebox" data-slide_box="offer">Предложить
                            альтернативу</a>
                    </main>
                </div>
                <div id="history" class="lk_block slide_box hidden">
                    <main class="lk_content">
                        <h1 class="lk_block_title">История запроса</h1>
                        <section class="timeline_box">
                            <?php foreach ($query->history as $event) { ?>
                                <article class="timeline-row">
                                    <div class="timeline-date"><?= Yii::$app->formatter->asDatetime($event->created_at, 'php:d.m.Y H:i'); ?></div>
                                    <div class="timeline-info">
                                        <p><?= $event->event; ?></p>
                                    </div>
                                </article>
                            <?php } ?>
                        </section>
                        <a href="#" class="button lk gray close_slidebox" data-slide_box="history">Скрыть историю</a>
                    </main>
                </div>

                <?php
                $modelform = new OfferOrderForm();
                $modelform->order_id = $query->id; ?>
                <div id="offer" class="lk_block slide_box hidden">
                    <main class="lk_content">
                        <?php $form = ActiveForm::begin([
                                'id' => 'offer-form',
                                'action' => '/site/ajaxValidate/',
                                'options' => ['class' => ''],
                                'enableAjaxValidation' => true,
                                'enableClientValidation' => true,
                                'validateOnSubmit' => true,
                                'validateOnChange' => true,
                                'validateOnType' => false,
                                'validateOnBlur' => true,
                                'fieldConfig' => [
                                        'options' => ['class' => 'ip_cell w100'],
                                        'template' => '{input}{error}{hint}',
                                        'inputOptions' => ['class' => 'input_text'],
                                ],
                        ]); ?>
                        <h1 class="lk_block_title">Предложить альтернативу клиенту</h1>
                        <p>Укажите рекомендуемого эксперта и пояснительный текст</p>
                        <?= $form->field($modelform, 'order_id')->hiddenInput(); ?>
                        <?= $form->field($modelform, 'user_id')->dropDownList([], ['class' => "pretty_select_search_user"]); ?>
                        <div class="lk_podpis left">Примечание</div>
                        <?= $form->field($modelform, 'offer_text')->textArea(['placeholder' => 'Пояснительный текст']); ?>
                        <div class="ip_cell w100">
                            <button type="submit" class="button-o medium lk">Отправить</button>
                            <a href="#" class="button-o blue medium lk close_slidebox" data-slide_box="offer">Отмена</a>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </main>
                </div>

                <?= \app\modules\message\widgets\message\MessageWidget::widget(['query' => $query]); ?>
            </div>
            <?= app\modules\users\widgets\profile\MksmenuWidget::widget(); ?>
        </div>
    </main>
    <div class="modal" id="success_queries_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Изменение запроса</div>
                <p>При изменении запроса возникла ошибка.</p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
<?php
$url = Url::toRoute(['/pages/orders/getoffered']);
$url_queries_close = Url::toRoute(['/pages/queries/closequery']);
$url_query_offer = Url::toRoute(['/pages/queries/setoffer']);
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
    $('body').on('click','.reflect_status', function(e){
        e.preventDefault();
        let query = $(this).data('query');
        let param = yii.getCsrfParam();
        let token = yii.getCsrfToken();
        let action = $(this).data('action');
        $.ajax({
            type: 'POST',
            url: '{$url_queries_close}',
            processData: true,
            dataType: 'json',
            data: {query:query,param:token,action:action},
            success: function(data){
                if (data.status == 'success') {
                    window.location.href = window.location.href;
                } else {
                    // в случае ошибки вывести сообщение
                    $('#success_queries_modal .success_box p').html(data.message);
                    modalPos('#success_queries_modal');
                }
            }
        });
    });

    $('#offer-form').on('beforeSubmit', function(event) {
        var formData = new FormData($('#offer-form')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url_query_offer}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    // в случае успеха показать модалку и обновить данные на странице подключим pjax попозже
                        // $('#success_order_modal .success_box p').html(data.message);
                        // modalPos('#success_order_modal');
                    window.location.href = window.location.href;
                } else {
                    // в случае ошибки вывести сообщение
                    $('#success_order_modal .success_box p').html(data.message);
                    modalPos('#success_order_modal');
                }
            }
        });
        return false;
    });
    $('#offer-form').on('submit', function(e){
        e.preventDefault();
        return false;
    });

JS;

$this->registerJs($js);
?>