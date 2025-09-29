<?php

use app\modules\events\models\Events;
use app\modules\pages\models\PServiceIP;
use app\modules\pages\models\PServiceOOO;
use app\modules\pages\models\PServiceSelfbusy;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerJsFile('/js/jquery-ui.min.js', ['depends' => [app\assets\AppAsset::class]]);
$this->registerJsFile('/js/lk-events.js', ['depends' => [app\assets\AppAsset::class]]);
$this->registerJsFile('/js/jquery.ui.touch-punch.min.js', ['depends' => [app\assets\AppAsset::class]]);

?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <div class="lk_block">
                    <header class="lk_block_header">
                        <h1 class="lk_block_title-big"><?= $original->status == Events::STATUS_NEW ? 'Добавить мероприятие' : 'Редактирование мероприятия'; ?></h1>
                        <div class="lk_block_subtitle"><?= $model->content; ?></div>
                    </header>
                </div>
                <div class="lk-event-reg-steps">
                    <a href="<?= Url::toRoute([$model->getUrlPath(), 'id' => $original->id]); ?>"
                       class="lk-event-reg-step active">
                        <div class="lk-event-reg-step-num"><img src="/img/i_check1-white.svg" alt=""></div>
                        <div class="lk-event-reg-step-name">Описание</div>
                    </a>
                    <a href="<?= Url::toRoute([$model->getUrlPath(), 'step' => 2, 'id' => $original->id]); ?>"
                       class="lk-event-reg-step active">
                        <div class="lk-event-reg-step-num"><img src="/img/i_check1-white.svg" alt=""></div>
                        <div class="lk-event-reg-step-name">Условия</div>
                    </a>
                    <a href="<?= Url::toRoute([$model->getUrlPath(), 'step' => 3, 'id' => $original->id]); ?>"
                       class="lk-event-reg-step active">
                        <div class="lk-event-reg-step-num"><img src="/img/i_check1-white.svg" alt=""></div>
                        <div class="lk-event-reg-step-name">О мероприятии</div>
                    </a>
                    <div class="lk-event-reg-step active">
                        <div class="lk-event-reg-step-num">4</div>
                        <div class="lk-event-reg-step-name">Регистрация</div>
                    </div>
                    <a <?= ($original->status == Events::STATUS_NEW ? '' : 'href="' . Url::toRoute([$model->getUrlPath(), 'step' => 5, 'id' => $original->id]) . '" '); ?>class="lk-event-reg-step">
                        <div class="lk-event-reg-step-num">5</div>
                        <div class="lk-event-reg-step-name">Публикация</div>
                    </a>
                </div>

                <?php $form = ActiveForm::begin([
                        'id' => 'event-form',
                        'action' => '/site/ajaxValidate/',
                        'options' => ['class' => 'js-validation marked', 'autocomplete' => 'off'],
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => true,
                        'validateOnSubmit' => true,
                        'validateOnChange' => true,
                        'validateOnType' => false,
                        'validateOnBlur' => true,
                        'fieldConfig' => [
                                'options' => ['class' => 'ip_cell w100'],
                                'template' => '{label}{input}{error}{hint}',
                                'inputOptions' => ['class' => 'input_text'],
                                'labelOptions' => ['class' => 'ip_label'],
                        ],
                ]); ?>
                <?= $form->field($event_model, 'id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
                <?= $form->field($event_model, 'step', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>

                <div class="lk_block">
                    <div class="lk_content">
                        <h4 class="lk_step_title font20">Регистрация на мероприятие</h4>
                        <?php
                        $tariff_list = [
                                0 => 'Не требуется',
                                1 => 'Требуется',
                        ];
                        if (!Yii::$app->user->identity->userAR->organization->can_service) {
                            unset($tariff_list[1]);
                        }
                        ?>
                        <?= $form->field($event_model, 'need_tariff', ['template' => '{input}{error}{hint}', 'options' => ['class' => '']])->radioList(
                                $tariff_list,
                                ['item' => function ($index, $label, $name, $checked, $value) {
                                    return '<div class="ip_cell w100"><input type="radio" ' . ($checked ? 'checked="checked" ' : '') . 'name="' . $name . '" value="' . $value . '" class="ch need_formevent"><label>' . $label . '</label></div>';
                                }
                                ]
                        );
                        if (!Yii::$app->user->identity->userAR->organization->can_service) {
                            $reg_ooo_page = PServiceOOO::find()->where(['model' => PServiceOOO::class, 'visible' => 1])->one();
                            if ($reg_ooo_page) {
                                $reg_ooo_text = '<a href="' . $reg_ooo_page->getUrlPath() . '">Юрлица</a>';
                            } else {
                                $reg_ooo_text = 'Юрлица';
                            }
                            $reg_ip_page = PServiceIP::find()->where(['model' => PServiceIP::class, 'visible' => 1])->one();
                            if ($reg_ip_page) {
                                $reg_ip_text = '<a href="' . $reg_ip_page->getUrlPath() . '">Индивидуального предпринимателя</a>';
                            } else {
                                $reg_ip_text = 'Индивидуального предпринимателя';
                            }
                            $reg_selfbusy_page = PServiceSelfbusy::find()->where(['model' => PServiceSelfbusy::class, 'visible' => 1])->one();
                            if ($reg_selfbusy_page) {
                                $reg_selfbusy_text = '<a href="' . $reg_selfbusy_page->getUrlPath() . '">Самозанятого</a>';
                            } else {
                                $reg_selfbusy_text = 'Самозанятого';
                            }
                            ?>
                            <p>Чтобы создать мероприятие с возможностью регистрации, Вам необходимо зарегистрироваться
                                на маркетплейсе в качестве <?= $reg_ooo_text; ?>, <?= $reg_ip_text; ?>
                                или <?= $reg_selfbusy_text; ?></p>
                        <?php } ?>
                    </div>
                </div>

                <div class="forms_container">
                    <?php
                    $forms_count = 0;
                    if (!empty($original->eventsFormsAll)) {
                        $forms_count = count($original->eventsFormsAll);
                        foreach ($original->eventsFormsAll as $key => $ev_form) {
                            // рендерим форму
                            echo $this->render('_exist_form', ['key' => $key, 'form' => $ev_form]);
                        }
                    } ?>
                </div>

                <div class="lk_block need-reg-ch-wrap" style="display: none;">
                    <div class="lk_content">
                        <div class="ip_cell w100 more-event-forms-wrapper mb0">
                            <button class="button blue medium lk js-more-event-forms">Добавить ещё форму регистрации
                            </button>
                            <div class="more-event-forms-text">Вы можете создать ещё одну форму для другой категории
                                участников.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lk_block need-send-ch-wrap">
                    <div class="lk_content">
                        <div class="ip_cell w100 flex justify-between buttons-wrapper mb0">
                            <a href="<?= Url::toRoute([$model->getUrlPath(), 'step' => 3, 'id' => $event_model->id]); ?>"
                               class="button-o gray medium">Вернуться</a>
                            <button type="submit" class="button blue medium lk">Продолжить</button>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
        </div>
    </main>
    <div class="modal" id="fail_service_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Ошибка редактирования мероприятия</div>
                <p>При сохранении данных возникли ошибки. <?= \app\helpers\MainHelper::getHelpText() ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>

    <div class="basis_html" style="display: none;">
        <div id="form_html" data-key="<?= $forms_count; ?>">
            <div class="lk_block form-event" data-form_key="..key..">
                <div class="lk_content">
                    <h4 class="lk_step_title font20">Создать форму регистрации</h4>
                    <input class="form_id_value" type="hidden" value="temp_<?= $original->id; ?>_..key..">
                    <div class="ip_cell label-on w100">
                        <div class="flex">
                            <input type="text" class="input_text" name="LKEvent[forms][..key..][name]" value=""
                                   placeholder="Название формы" required="">
                            <label class="ip_label">Название формы*</label>
                            <div class="question_box">
                                <a href="javascript:void(0)" class="question_icon">?</a>
                                <div class="question_text">Укажите в названии, какая категория участников регистрируется
                                    через эту форму. Например, участник или спикер.
                                </div>
                            </div>
                            <div class="input-status"></div>
                        </div>
                        <div class="help-block">Укажите Название формы</div>
                    </div>
                    <h4 class="lk_step_title font20">Поля формы</h4>
                    <div class="ip_cell label-on w100">
                        <div class="flex">
                            <input type="text" class="input_text active" name="firstName"
                                   placeholder="Обязательное поле" value="Имя" required="" disabled>
                            <label class="ip_label">Обязательное поле*</label>
                            <div class="question_box">
                                <a href="javascript:void(0)" class="question_icon">?</a>
                                <div class="question_text">Обязательное поле для регистрации участника.</div>
                            </div>
                            <div class="input-status"></div>
                        </div>
                        <div class="help-block"></div>
                    </div>
                    <div class="ip_cell label-on w100">
                        <div class="flex">
                            <input type="text" class="input_text active" name="lastName" placeholder="Обязательное поле"
                                   value="Фамилия" required="" disabled>
                            <label class="ip_label">Обязательное поле*</label>
                            <div class="question_box">
                                <a href="javascript:void(0)" class="question_icon">?</a>
                                <div class="question_text">Обязательное поле для регистрации участника.</div>
                            </div>
                            <div class="input-status"></div>
                        </div>
                        <div class="help-block"></div>
                    </div>
                    <div class="ip_cell label-on w100">
                        <div class="flex">
                            <input type="text" class="input_text active" name="email" placeholder="Обязательное поле"
                                   value="Email" required="" disabled>
                            <label class="ip_label">Обязательное поле*</label>
                            <div class="question_box">
                                <a href="javascript:void(0)" class="question_icon">?</a>
                                <div class="question_text">Обязательное поле только для Плательщика. Необходимо для
                                    создания личного кабинета.
                                </div>
                            </div>
                            <div class="input-status"></div>
                        </div>
                        <div class="help-block"></div>
                    </div>
                    <div class="ip_cell label-on w100">
                        <div class="flex">
                            <input type="text" class="input_text active" name="phone" placeholder="Обязательное поле"
                                   value="Телефон" required="" disabled>
                            <label class="ip_label">Обязательное поле*</label>
                            <div class="question_box">
                                <a href="javascript:void(0)" class="question_icon">?</a>
                                <div class="question_text">Обязательное поле только для Плательщика. Необходимо для
                                    создания личного кабинета.
                                </div>
                            </div>
                            <div class="input-status"></div>
                        </div>
                        <div class="help-block"></div>
                    </div>
                    <!-- сюда добавляем созданные поля -->
                    <div class="js-added-question-list"></div>

                    <div class="ip_cell w100 mb0 add-question js-question-list" data-formkey="..key.."
                         data-fieldkey="0">
                        <button type="button" class="button blue medium lk">Добавить вопрос</button>
                        <div class="add-question-list">
                            <div class="add-question-item" data-variant="1">Короткий произвольный ответ</div>
                            <div class="add-question-item" data-variant="2">Длинный произвольный ответ</div>
                            <div class="add-question-item" data-variant="3">Выбор одного варианта</div>
                            <div class="add-question-item" data-variant="4">Выбор нескольких вариантов</div>
                        </div>
                    </div>
                    <div class="current_field"></div>
                </div>

                <div class="lk_content" <?= (Yii::$app->params['can_paid_events'] ? '' : 'style="display:none"'); ?>>
                    <h4 class="lk_step_title font20 mb10">Стоимость участия</h4>
                    <div class="lk_block_subtitle mb30">При выборе платной регистрации добавьте тариф(-ы) для
                        продолжения создания мероприятия.
                    </div>
                    <div class="ip_cell w100">
                        <input type="radio" class="ch need-pay-ch" name="LKEvent[forms][..key..][payregister]"
                               checked="checked" value="0">
                        <label>Бесплатное участие</label>
                        <div class="help-block"></div>
                    </div>
                    <?php if (Yii::$app->params['can_paid_events']) { ?>
                        <div class="ip_cell w100">
                            <input type="radio" class="ch need-pay-ch" name="LKEvent[forms][..key..][payregister]"
                                   value="1">
                            <label>Платное участие</label>
                            <div class="help-block"></div>
                        </div>
                    <?php } ?>
                </div>

                <div class="lk_content need-pay-ch-wrap" style="display: none;">
                    <h4 class="lk_step_title font20 mb20">Тарифы</h4>
                    <div class="tarif-table-wrapper">
                        <div class="tariff_place tarif-list js-tarif-list"></div>
                        <div class="ip_cell w100">
                            <button class="button blue medium lk add-tarif-js">Добавить тариф</button>
                        </div>
                    </div>
                    <div class="tariff_form_place"></div>
                </div>
                <div class="lk_content">
                    <div class="ip_cell flex flex-end w100 mb0">
                        <button class="button-o gray medium lk js-remove-event-forms">Удалить форму регистрации</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="fields_html">
            <div class="field_wrapper" data-variant="1">
                <div class="adding-question js-adding-question" data-variant="1">
                    <h4 class="lk_step_title font20">Короткий произвольный ответ</h4>
                    <p>Ответ пользователя на вопрос содержит до 255 символов</p>
                    <div class="ip_cell label-on w100">
                        <input type="hidden" name="LKEvent[forms][..key..][fields][..field..][type]" value="text">
                        <input type="hidden" name="LKEvent[forms][..key..][fields][..field..][visible]" value="1">
                        <input type="hidden" name="LKEvent[forms][..key..][fields][..field..][order]"
                               class="js-hidden-order" value="..field..">
                        <input type="text" class="input_text name_field"
                               name="LKEvent[forms][..key..][fields][..field..][name]" placeholder="Текст вопроса"
                               required="">
                        <label class="ip_label">Текст вопроса</label>
                        <div class="input-status"></div>
                        <div class="help-block"></div>
                    </div>
                    <div class="ip_cell label-on w100">
                        <input type="hidden" name="LKEvent[forms][..key..][fields][..field..][required]" value="0">
                        <input type="checkbox" class="ch" name="LKEvent[forms][..key..][fields][..field..][required]"
                               value="1">
                        <label>Ответ на вопрос обязателен</label>
                        <div class="help-block"></div>
                    </div>
                    <div class="ip_cell label-on w100 ip_cell-comment">
                        <div class="flex">
                            <input type="text" class="input_text"
                                   name="LKEvent[forms][..key..][fields][..field..][comment]"
                                   placeholder="Комментарий к вопросу (не обязательно)">
                            <label class="ip_label">Комментарий к вопросу <br class="mobile-visible">(не
                                обязательно)</label>
                            <div class="question_box">
                                <a href="javascript:void(0)" class="question_icon">?</a>
                                <div class="question_text">Краткий текст, который будет отображен в форме под текстом
                                    вопроса
                                </div>
                            </div>
                            <div class="input-status"></div>
                        </div>
                        <div class="help-block"></div>
                    </div>
                    <div class="ip_cell w100 flex mb0">
                        <button class="button-o gray small js-cancel-adding-question">Отменить</button>
                        <button class="button blue small lk js-add-question">Сохранить</button>
                    </div>
                </div>
            </div>
            <div class="field_wrapper" data-variant="2">
                <div class="adding-question js-adding-question" data-variant="2">
                    <h4 class="lk_step_title font20">Длинный произвольный ответ</h4>
                    <div class="ip_cell label-on w100">
                        <input type="hidden" name="LKEvent[forms][..key..][fields][..field..][type]" value="textarea">
                        <input type="hidden" name="LKEvent[forms][..key..][fields][..field..][visible]" value="1">
                        <input type="hidden" name="LKEvent[forms][..key..][fields][..field..][order]"
                               class="js-hidden-order" value="..field..">
                        <input type="text" class="input_text name_field"
                               name="LKEvent[forms][..key..][fields][..field..][name]" placeholder="Текст вопроса"
                               required="">
                        <label class="ip_label">Текст вопроса</label>
                        <div class="input-status"></div>
                        <div class="help-block"></div>
                    </div>
                    <div class="ip_cell label-on w100">
                        <input type="hidden" name="LKEvent[forms][..key..][fields][..field..][required]" value="0">
                        <input type="checkbox" class="ch" name="LKEvent[forms][..key..][fields][..field..][required]"
                               value="1">
                        <label>Ответ на вопрос обязателен</label>
                        <div class="help-block"></div>
                    </div>
                    <div class="ip_cell label-on w100 ip_cell-comment">
                        <div class="flex">
                            <input type="text" class="input_text"
                                   name="LKEvent[forms][..key..][fields][..field..][comment]"
                                   placeholder="Комментарий к вопросу (не обязательно)">
                            <label class="ip_label">Комментарий к вопросу <br class="mobile-visible">(не
                                обязательно)</label>
                            <div class="question_box">
                                <a href="javascript:void(0)" class="question_icon">?</a>
                                <div class="question_text">Краткий текст, который будет отображен в форме под текстом
                                    вопроса
                                </div>
                            </div>
                            <div class="input-status"></div>
                        </div>
                        <div class="help-block"></div>
                    </div>
                    <div class="ip_cell w100 flex mb0">
                        <button class="button-o gray small js-cancel-adding-question">Отменить</button>
                        <button class="button blue small lk js-add-question">Сохранить</button>
                    </div>
                </div>
            </div>
            <div class="field_wrapper" data-variant="3">
                <div class="adding-question js-adding-question" data-variant="3">
                    <h4 class="lk_step_title font20">Выбор одного варианта</h4>
                    <div class="ip_cell label-on w100">
                        <input type="hidden" name="LKEvent[forms][..key..][fields][..field..][type]" value="radio_list">
                        <input type="hidden" name="LKEvent[forms][..key..][fields][..field..][visible]" value="1">
                        <input type="hidden" name="LKEvent[forms][..key..][fields][..field..][order]"
                               class="js-hidden-order" value="..field..">
                        <input type="text" class="input_text name_field"
                               name="LKEvent[forms][..key..][fields][..field..][name]" placeholder="Текст вопроса"
                               required="">
                        <label class="ip_label">Текст вопроса</label>
                        <div class="input-status"></div>
                        <div class="help-block"></div>
                    </div>
                    <div class="ip_cell label-on w100">
                        <input type="hidden" name="LKEvent[forms][..key..][fields][..field..][required]" value="0">
                        <input type="checkbox" class="ch" name="LKEvent[forms][..key..][fields][..field..][required]"
                               value="1">
                        <label>Ответ на вопрос обязателен</label>
                        <div class="help-block"></div>
                    </div>
                    <div class="ip_cell label-on w100 ip_cell-comment">
                        <div class="flex">
                            <input type="text" class="input_text"
                                   name="LKEvent[forms][..key..][fields][..field..][comment]"
                                   placeholder="Комментарий к вопросу (не обязательно)">
                            <label class="ip_label">Комментарий к вопросу <br class="mobile-visible">(не
                                обязательно)</label>
                            <div class="question_box">
                                <a href="javascript:void(0)" class="question_icon">?</a>
                                <div class="question_text">Краткий текст, который будет отображен в форме под текстом
                                    вопроса
                                </div>
                            </div>
                            <div class="input-status"></div>
                        </div>
                        <div class="help-block"></div>
                    </div>
                    <h4 class="lk_step_title font20">Варианты ответа</h4>
                    <div class="variants-question js-variants-question" data-maxprices="5"
                         data-name="LKEvent[forms][..key..][fields][..field..][list_values][]">
                        <div class="ip_cell label-on w100 variant-question drag-input">
                            <div class="flex">
                                <input type="text" class="input_text"
                                       name="LKEvent[forms][..key..][fields][..field..][list_values][]"
                                       placeholder="Вариант ответа" data-sort="1">
                                <label class="ip_label">Вариант ответа</label>
                                <div class="remove-variant-question js-remove-variant-question"></div>
                                <div class="input-status"></div>
                                <div class="drag-burger drag-burger-variants-question"></div>
                            </div>
                        </div>
                    </div>
                    <button class="button-o gray small js-add-variant-question">Добавить вариант</button>
                    <div class="ip_cell w100 flex mb0">
                        <button class="button-o gray small js-cancel-adding-question">Отменить</button>
                        <button class="button blue small lk js-add-question">Сохранить</button>
                    </div>
                </div>
            </div>

            <div class="field_wrapper" data-variant="4">
                <div class="adding-question js-adding-question" data-variant="4">
                    <h4 class="lk_step_title font20">Выбор нескольких вариантов</h4>
                    <div class="ip_cell label-on w100">
                        <input type="hidden" name="LKEvent[forms][..key..][fields][..field..][type]"
                               value="boolean_list">
                        <input type="hidden" name="LKEvent[forms][..key..][fields][..field..][visible]" value="1">
                        <input type="hidden" name="LKEvent[forms][..key..][fields][..field..][order]"
                               class="js-hidden-order" value="..field..">
                        <input type="text" class="input_text name_field"
                               name="LKEvent[forms][..key..][fields][..field..][name]" placeholder="Текст вопроса"
                               required="">
                        <label class="ip_label">Текст вопроса</label>
                        <div class="input-status"></div>
                        <div class="help-block"></div>
                    </div>
                    <div class="ip_cell label-on w100">
                        <input type="hidden" name="LKEvent[forms][..key..][fields][..field..][required]" value="0">
                        <input type="checkbox" class="ch" name="LKEvent[forms][..key..][fields][..field..][required]"
                               value="1">
                        <label>Ответ на вопрос обязателен</label>
                        <div class="help-block"></div>
                    </div>
                    <div class="ip_cell label-on w100 ip_cell-comment">
                        <div class="flex">
                            <input type="text" class="input_text"
                                   name="LKEvent[forms][..key..][fields][..field..][comment]"
                                   placeholder="Комментарий к вопросу (не обязательно)">
                            <label class="ip_label">Комментарий к вопросу <br class="mobile-visible">(не
                                обязательно)</label>
                            <div class="question_box">
                                <a href="javascript:void(0)" class="question_icon">?</a>
                                <div class="question_text">Краткий текст, который будет отображен в форме под текстом
                                    вопроса
                                </div>
                            </div>
                            <div class="input-status"></div>
                        </div>
                        <div class="help-block"></div>
                    </div>
                    <h4 class="lk_step_title font20">Варианты ответа</h4>
                    <div class="variants-question js-variants-question" data-maxprices="5"
                         data-name="LKEvent[forms][..key..][fields][..field..][list_values][]">
                        <div class="ip_cell label-on w100 variant-question drag-input">
                            <div class="flex">
                                <input type="text" class="input_text"
                                       name="LKEvent[forms][..key..][fields][..field..][list_values][]"
                                       placeholder="Вариант ответа" data-sort="1">
                                <label class="ip_label">Вариант ответа</label>
                                <div class="remove-variant-question js-remove-variant-question"></div>
                                <div class="input-status"></div>
                                <div class="drag-burger drag-burger-variants-question"></div>
                            </div>
                        </div>
                    </div>
                    <button class="button-o gray small js-add-variant-question">Добавить вариант</button>
                    <div class="ip_cell w100 flex mb0">
                        <button class="button-o gray small js-cancel-adding-question">Отменить</button>
                        <button class="button blue small lk js-add-question">Сохранить</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="tariff_html">
            <div class="tarif-wrapper">
                <h4 class="lk_step_title font20 mb10 mt20">Наименование тарифа, цена, количество</h4>
                <p class="notice-text mb20">Данные о тарифе сохраняются только после нажатия на кнопку “Сохранить
                    тариф”</p>
                <input class="lkevent-tariff_id" name="LKEvent[tariff_id]" type="hidden" value="">
                <input class="lkevent-tariff_form_id" name="LKEvent[tariff_form_id]" type="hidden" value="">
                <div class="ip_cell w100">
                    <div class="symbols_counter_box">
                        <label class="ip_label">Название тарифа</label>
                        <input type="text" name="LKEvent[tariff_name]"
                               class="lkevent-tariff_name input_text limitedSybmbols" maxlength="90"
                               placeholder="Например, билет участника по ранней цене" required="" value="">
                        <span class="symbols_counter"></span>
                    </div>
                </div>
                <div class="ip_cell w100 mb40">
                    <div class="symbols_counter_box">
                        <label class="ip_label">Описание тарифа</label>
                        <textarea name="LKEvent[tariff_description]"
                                  class="lkevent-tariff_description input_text comment limitedSybmbols" maxlength="300"
                                  placeholder="Например, что входит в тариф"></textarea>
                        <span class="symbols_counter"></span>
                    </div>
                </div>
                <h4 class="lk_step_title">Период действия тарифа</h4>
                <div class="ip_cell ip_cell-event-date w100">
                    <div class="ip_cell datarange_ipc mr20">
                        <label class="ip_label">Начало</label>
                        <input name="LKEvent[tariff_start_publish]" type="text"
                               class="lkevent-tariff_start_publish input_text datepicker" value="<?= date("d.m.Y"); ?>"
                               placeholder="дд.мм.гг"/>
                    </div>
                    <div class="ip_cell datarange_ipc mr20">
                        <label class="ip_label">Окончание</label>
                        <input name="LKEvent[tariff_end_publish]" type="text"
                               class="lkevent-tariff_end_publish input_text datepicker"
                               value="<?= $event_model->event_date; ?>" placeholder="дд.мм.гг"/>
                    </div>
                </div>
                <h4 class="lk_step_title">Укажите сколько людей могут приобрести по данному тарифу билет</h4>
                <div class="ip_cell w49">
                    <input name="LKEvent[tariff_unlimit_ticket]" type="hidden" value="0">
                    <input name="LKEvent[tariff_unlimit_ticket]" type="checkbox"
                           class="lkevent-tariff_unlimit_ticket ch need-limit-ch" value="1">
                    <label>Не ограничено</label>
                    <div class="help-block"></div>
                </div>
                <div class="ip_cell need-limit-ch-wrap w100">
                    <div class="flex align-center">
                        <input name="LKEvent[tariff_count_ticket]" type="text"
                               class="lkevent-tariff_count_ticket input_text w49 mr20" value="" placeholder="100"
                               required="">
                        <label class="ip_label">0 - нет в наличии</label>
                    </div>
                </div>

                <h4 class="lk_step_title mb0">Стоимость</h4>
                <div class="append-price-list" data-maxprices="5" data-name1="LKEvent[tariff_prices][]"
                     data-name2="LKEvent[tariff_prices_dates][]">
                    <div class="ip_cell ip_cell-event-date append-price-element flex align-bottom w100 mb0">
                        <div class="ip_cell datarange_ipc mr20">
                            <input type="number" name="LKEvent[tariff_prices][]"
                                   class="lkevent-tariff_prices input_text" value="" placeholder="Цена, ₽"/>
                        </div>
                        <div class="ip_cell datarange_ipc mr20">
                            <label class="ip_label">Начало действия</label>
                            <input type="text" name="LKEvent[tariff_prices_dates][]"
                                   class="lkevent-tariff_prices_dates input_text disabled" readonly
                                   placeholder="С момента публикации"/>
                        </div>
                    </div>
                    <div class="ip_cell w49">
                        <input type="checkbox" class="ch js-free-price">
                        <label>Бесплатно</label>
                        <div class="help-block"></div>
                    </div>
                    <div class="append-js"></div>
                    <div class="ip_cell flex align-center w100 mb50">
                        <button class="button blue small add-price-event-js">Добавить цену</button>
                        <div class="question_box">
                            <a href="javascript:void(0)" class="question_icon">?</a>
                            <div class="question_text">
                                Вы можете изменять цену билета по мере приближения события
                            </div>
                        </div>
                    </div>
                </div>

                <h4 class="lk_step_title">Статус публикации</h4>
                <p class="gray-text">Снимите галочку, если хотите снять тариф с публикации</p>
                <div class="ip_cell w49">
                    <input name="LKEvent[tariff_visible]" type="hidden" value="0">
                    <input name="LKEvent[tariff_visible]" type="checkbox" class="lkevent-tariff_visible ch default_set"
                           value="1" checked="">
                    <label>Опубликован</label>
                    <div class="help-block"></div>
                </div>

                <div class="ip_cell w100 flex_centered mb0">
                    <button class="button-o gray medium cancel-tarif-js">Отмена</button>
                    <button class="button blue medium lk send_tariff">Сохранить тариф</button>
                </div>
            </div>
        </div>
    </div>

<?php
$url = Url::toRoute(['/pages/activities/saveevent', 'step' => 4]);
$url_tariff = Url::toRoute(['/pages/activities/savetariff']);
$url_tariffremove = Url::toRoute(['/pages/activities/removetariff']);
$url_tariffinfo = Url::toRoute(['/pages/activities/gettariff']);
$url_switchform = Url::toRoute(['/pages/activities/switchform']);

$js = <<<JS
    $('#event-form').on('beforeSubmit', function(event) {
        let formData = new FormData($('#event-form')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    // переадресация на следующий шаг
                    window.location.href = data.redirect_to;
                } else {
                    // показать модалку с ошибкой
                    $('#fail_service_modal .modal_title').html('Ошибка редактирования мероприятия');
                    $('#fail_service_modal p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
        return false;
    });
    $('#event-form').on('submit', function(e) {
        e.preventDefault();
        return false;
    });
    
    $('body').on('click', '.switch-active-js', function() {
        let form_id = $(this).data('form');
        let param = yii.getCsrfParam();
        let token = yii.getCsrfToken();
        let that = this;
        $.ajax({
            type: 'POST',
            url: '{$url_switchform}',
            processData: true,
            dataType: 'json',
            data: {form_id:form_id,param:token},
            success: function(data){
                if (data.status == 'success') {
                    // переименовать кнопку
                    if (data.visible == 1) {
                        $(that).text('Приостановить продажу билетов');
                    } else {
                        $(that).text('Возобновить продажу билетов');
                    }
                } else {
                    // в случае ошибки открыть окно с ошибкой
                    $('#fail_service_modal .modal_title').html('Продажа билетов');
                    $('#fail_service_modal .success_box p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
    });

    $('body').on('click','.send_tariff',function(e) {
        e.preventDefault();
        let formData = new FormData($('#event-form')[0]);
        let form = $(this).parents('.form-event');
        $.ajax({
            type: 'POST',
            url: '{$url_tariff}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    // удалить форму
                    // вернуть кнопку/строку и добавить новую строку
                    form.find('.cancel-tarif-js').trigger('click');
                    if (data.action == 'edit') {
                        form.find('.tarif-element[data-tariff='+data.tariff_id+']').replaceWith(data.new_tariff_html);
                    } else {
                        form.find('.tariff_place').append(data.new_tariff_html);
                    }
                } else {
                    // показать модалку с ошибкой
                    $('#fail_service_modal .modal_title').html('Ошибка редактирования тарифа');
                    $('#fail_service_modal p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
        return false;
    });

    $('body').on('click', '.js-remove-tarif', function() {
        let tariff_id = $(this).data('tariff');
        let param = yii.getCsrfParam();
        let token = yii.getCsrfToken();
        let that = this;
        $.ajax({
            type: 'POST',
            url: '{$url_tariffremove}',
            processData: true,
            dataType: 'json',
            data: {tariff_id:tariff_id,param:token},
            success: function(data){
                if (data.status == 'success') {
                    $(that).closest('.tarif-element').remove();
                } else {
                    // в случае ошибки открыть окно с ошибкой
                    $('#fail_service_modal .modal_title').html('Удаление тарифа');
                    $('#fail_service_modal .success_box p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
    });

    $('body').on('click','.edit_tariff',function() {
        // заполнить форму данными тарифа, если тариф принадлежит пользователю, иначе закрыть форму
        let tariff_id = $(this).data('tariff');
        let param = yii.getCsrfParam();
        let token = yii.getCsrfToken();
        let that = this;
        $.ajax({
            type: 'POST',
            url: '{$url_tariffinfo}',
            processData: true,
            dataType: 'json',
            data: {tariff_id:tariff_id,param:token},
            success: function(data){
                if (data.status == 'success') {

                    let form = $(that).parents('.form-event');
                    $(that).closest('.tarif-table-wrapper').hide();
                    $(that).closest('.tarif-table-wrapper').find('.tarif-element').hide();

                    let tariff_html = $('#tariff_html').html();
                    form.find('.tariff_form_place').html(tariff_html);

                    // заполняем id формы, если форма существует как запись в бд
                    let form_id = form.find('.form_id_value').val();
                    if (form_id != '') {
                        form.find('.lkevent-tariff_form_id').val(form_id);
                    }

                    // заполняем данные о тарифе
                    form.find('.lkevent-tariff_id').val(tariff_id);
                    form.find('.lkevent-tariff_name').val(data.data.name);
                    form.find('.lkevent-tariff_description').val(data.data.description);
                    form.find('.lkevent-tariff_start_publish').val(data.data.start_publish);
                    form.find('.lkevent-tariff_end_publish').val(data.data.end_publish);

                    if (data.data.limit_tickets == 0) {
                        form.find('.lkevent-tariff_unlimit_ticket:not(:checked)').trigger("click");
                    } else if (data.data.limit_tickets == 1) {
                        form.find('.lkevent-tariff_unlimit_ticket:checked').trigger("click");
                    }
                    form.find('.lkevent-tariff_count_ticket').val(data.data.tickets_count);
                    if (data.data.visible == 1) {
                        form.find('.lkevent-tariff_visible:not(:checked)').trigger("click");
                    } else if (data.data.visible == 0) {
                        form.find('.lkevent-tariff_visible:checked').trigger("click");
                    }
                    for (key in data.data.prices) {
                        if (key == 0) {
                            // первую цену просто вписываем
                            form.find('.lkevent-tariff_prices').val(data.data.prices[key].price);
                        } else {
                            // пользуемся существующим функционалом - имитируем нажатие кнопки "Добавить цену"
                            form.find('.add-price-event-js').trigger('click');
                            $(form.find('.append-js').children(':last-child').find('input.price_field')[0]).val(data.data.prices[key].price);
                            $(form.find('.append-js').children(':last-child').find('input.date_field')[0]).val(data.data.prices[key].date);
                        }
                    }
                    datePicker();
                } else {
                    // в случае ошибки открыть окно с ошибкой
                    $('#fail_service_modal .modal_title').html('Редактирование тарифа');
                    $('#fail_service_modal .success_box p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });

    });

    function datePicker() {
        $('.datepicker').not('.keypress').keypress(function(e) {
            var verified = (e.which == 8 || e.which == undefined || e.which == 0) ? null : String.fromCharCode(e.which).match();
            if (verified) {
                e.preventDefault();
            }
        });

        $('.datepicker').datepicker({
            keyboardNavigation: false,
            autoclose: true,
            todayHighlight: true,
            weekStart: 1,
            format: 'dd.mm.yyyy',
            orientation: 'bottom',
            language: 'ru'
        });

        $('.dateRange').datepair({
            dateClass: 'datepicker'
        });
    }

JS;
$this->registerJs($js);
?>