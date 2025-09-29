<?php
/*
    @descr Четвертый шаг создания/редактирования программы ДПО
    @var $model Class app\modules\pages\models\LKEduprogEdit; текущая страница
    @action pages/eduprog/eduprogedit
*/

use app\models\LKEduprogRegform;
use app\models\LKEduprogTariff;
use app\modules\eduprog\models\Eduprog;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->registerJsFile('/js/jquery-ui.min.js', ['depends' => [app\assets\AppAsset::class]]);
$this->registerJsFile('/js/jquery.ui.touch-punch.min.js', ['depends' => [app\assets\AppAsset::class]]);
$this->registerJsFile('/js/lk-events.js', ['depends' => [app\assets\AppAsset::class]]);
?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <div class="lk_block">
                    <header class="lk_block_header">
                        <h1 class="lk_block_title-big"><?= (empty($original) or ($original->status == Eduprog::STATUS_NEW)) ? 'Добавить программу ДПО' : 'Редактирование программы ДПО'; ?></h1>
                        <div class="lk_block_subtitle">
                            <?= $model->content; ?>
                            <br>
                            <b><?= $eduprog_model->name ?></b>
                        </div>
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
                        <div class="lk-event-reg-step-name">О программе</div>
                    </a>
                    <div class="lk-event-reg-step active">
                        <div class="lk-event-reg-step-num">4</div>
                        <div class="lk-event-reg-step-name">Регистрация<br> и тарифы</div>
                    </div>
                    <a <?= ((empty($original) or ($original->status == Eduprog::STATUS_NEW)) ? '' : 'href="' . Url::toRoute([$model->getUrlPath(), 'step' => 5, 'id' => $original->id]) . '" '); ?>class="lk-event-reg-step">
                        <div class="lk-event-reg-step-num">5</div>
                        <div class="lk-event-reg-step-name">Письмо</div>
                    </a>
                    <a <?= ((empty($original) or ($original->status == Eduprog::STATUS_NEW)) ? '' : 'href="' . Url::toRoute([$model->getUrlPath(), 'step' => 6, 'id' => $original->id]) . '" '); ?>class="lk-event-reg-step">
                        <div class="lk-event-reg-step-num">6</div>
                        <div class="lk-event-reg-step-name">Оферта<br> и публикация</div>
                    </a>
                </div>

                <?php $forms_count = 0;
                if (!empty($original->eduprogFormsAll)) {
                    $forms_count = count($original->eduprogFormsAll);
                } ?>
                <div id="regform_container">
                    <?php if ($forms_count > 0) {
                        foreach ($original->eduprogFormsAll as $form_item) {
                            echo $this->render('_registerform', ['form' => $form_item]);
                        } ?>
                    <?php } ?>
                </div>
                <div id="first_form_slot"
                     class="lk_block form-event" <?= ($forms_count != 0) ? 'style="display:none;"' : ''; ?>>
                    <div class="lk_content">
                        <h4 class="lk_step_title font20">Форма регистрации</h4>
                        <!-- элементы формы добавляются в js-form-list -->
                        <div class="drag-list js-form-list"></div>
                        <!-- скрыть если форма добавлена -->
                        <div class="ip_cell w100">
                            <button class="button blue medium lk open-form-js" data-form="new" data-name="">Добавить
                                форму
                            </button>
                        </div>
                    </div>
                </div>
                <div id="second_form_slot"
                     class="lk_block form-event" <?= ($forms_count != 1) ? 'style="display:none;"' : ''; ?>>
                    <div class="lk_content">
                        <h4 class="lk_step_title font20">Вторая форма регистрации</h4>
                        <p>Вы можете создать две разные формы регистрации для другой категории слушателей.</p>
                        <!-- элементы формы добавляются в js-form-list -->
                        <div class="drag-list js-form-list"></div>
                        <!-- скрыть если форма добавлена -->
                        <div class="ip_cell w100">
                            <button class="button blue medium lk open-form-js" data-form="new" data-name="">Добавить
                                форму
                            </button>
                        </div>
                    </div>
                </div>

                <div class="lk_block">
                    <div class="lk_content">
                        <div class="ip_cell w100 flex justify-between buttons-wrapper mb0">
                            <a href="<?= Url::toRoute([$model->getUrlPath(), 'step' => 3, 'id' => $original->id]); ?>"
                               class="button-o gray medium">Вернуться</a>
                            <a href="<?= Url::toRoute([$model->getUrlPath(), 'step' => 5, 'id' => $original->id]); ?>"
                               class="button blue medium lk">Продолжить</a>
                        </div>
                    </div>
                </div>
            </div>
            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
        </div>
    </main>


<?php
$regform_model = new LKEduprogRegform();
$regform_model->eduprog_id = $original->id;
?>
    <div class="modal" id="new_form_modal">
        <div class="modal_content">
            <a href="#" class="modal_close" data-fancybox-close>x</a>
            <?php $form = ActiveForm::begin([
                    'id' => 'eduprog-regform',
                    'action' => '/site/ajaxValidate/',
                    'options' => ['class' => 'marked form-event'],
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
            <?= $form->field($regform_model, 'id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
            <?= $form->field($regform_model, 'eduprog_id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
            <h4 class="lk_step_title font20 centered">Добавить форму</h4>

            <?= $form->field($regform_model, 'name', ['options' => ['class' => 'ip_cell label-on w100'], 'template' => '<div class="flex">{input}{label}' . $regform_model->getQuestion('name') . '<div class="input-status"></div></div>{error}{hint}'])->textInput(['placeholder' => "Название формы"]); ?>

            <h4 class="lk_step_title">Обязательные поля</h4>
            <div class="ip_cells">
                <div class="ip_cell label-on">
                    <div class="flex">
                        <input type="text" class="input_text active disabled"
                               placeholder="Обязательное поле для регистрации" value="Фамилия" required="" readonly>
                        <label class="ip_label">Обязательное поле для регистрации*</label>
                        <div class="input-status"></div>
                    </div>
                    <div class="help-block"></div>
                </div>
                <div class="ip_cell label-on">
                    <div class="flex">
                        <input type="text" class="input_text active disabled"
                               placeholder="Обязательное поле для регистрации" value="Имя" required="" readonly>
                        <label class="ip_label">Обязательное поле для регистрации*</label>
                        <div class="input-status"></div>
                    </div>
                    <div class="help-block"></div>
                </div>
            </div>
            <div class="ip_cells">
                <div class="ip_cell label-on ">
                    <div class="flex">
                        <input type="text" class="input_text active disabled"
                               placeholder="Обязательное поле для регистрации" value="Отчество" required="" readonly>
                        <label class="ip_label">Обязательное поле для регистрации*</label>
                        <div class="input-status"></div>
                    </div>
                    <div class="help-block"></div>
                </div>
                <div class="ip_cell label-on">
                    <div class="flex">
                        <input type="text" class="input_text active disabled"
                               placeholder="Обязательное поле для регистрации" value="Email" required="" readonly>
                        <label class="ip_label">Обязательное поле для регистрации*</label>
                        <div class="input-status"></div>
                    </div>
                    <div class="help-block"></div>
                </div>
            </div>
            <!-- сюда добавляем созданные поля -->
            <div id="fields_container"></div>

            <div class="ip_cell w100 flex_centered mb0">
                <button class="button-o gray medium" data-fancybox-close>Отмена</button>
                <button type="submit" class="button blue medium lk">Сохранить</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="modal_overlay"></div>
    </div>

<?php
/* поля формы не должны быть заполнены ничем, кроме id программы, чтобы form.reset() работал корректно */
$tariff_model = new LKEduprogTariff();
$tariff_model->eduprog_id = $original->id;
?>

    <div class="modal" id="new_tariff_modal">
        <div class="modal_content">
            <a href="#" class="modal_close" data-fancybox-close>x</a>
            <?php $form = ActiveForm::begin([
                    'id' => 'eduprog-tariffform',
                    'action' => '/site/ajaxValidate/',
                    'options' => ['class' => 'marked tarif-wrapper'],
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
            <?= $form->field($tariff_model, 'id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
            <?= $form->field($tariff_model, 'eduprog_id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
            <?= $form->field($tariff_model, 'eduprogform_id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>

            <h4 class="lk_step_title font20 mb10">Наименование тарифа, цена, количество</h4>
            <p class="notice-text mb20">Данные о тарифе сохраняются только после нажатия на кнопку “Сохранить тариф”</p>

            <?= $form->field($tariff_model, 'name', ['options' => ['class' => 'ip_cell w100'], 'template' => '<div class="symbols_counter_box">{label}{input}<span class="symbols_counter"></span></div>{error}{hint}'])->textInput(['class' => 'input_text limitedSybmbols', 'placeholder' => "Например, билет слушателя по ранней цене", 'maxlength' => '90']); ?>

            <?= $form->field($tariff_model, 'description', ['options' => ['class' => 'ip_cell w100 mb40'], 'template' => '<div class="symbols_counter_box">{label}{input}<span class="symbols_counter"></span></div>{error}{hint}'])->textArea(['class' => 'input_text comment limitedSybmbols', 'placeholder' => "Например, что входит в тариф", 'maxlength' => '300']); ?>

            <h4 class="lk_step_title">Период действия тарифа</h4>
            <div class="ip_cell ip_cell-event-date w100">

                <?= $form->field($tariff_model, 'start_publish', ['options' => ['class' => 'ip_cell datarange_ipc mr20']])->textInput(['class' => 'input_text datepicker', 'placeholder' => "дд.мм.гг"]); ?>

                <?= $form->field($tariff_model, 'end_publish', ['options' => ['class' => 'ip_cell datarange_ipc mr20']])->textInput(['class' => 'input_text datepicker', 'placeholder' => "дд.мм.гг"]); ?>
            </div>

            <h4 class="lk_step_title">Укажите сколько людей могут приобрести по данному тарифу участие в программе</h4>

            <?= $form->field($tariff_model, 'unlimit_tickets', ['options' => ['class' => 'ip_cell w49'], 'template' => '{input}<label>Не ограничено</label>{hint}{error}'])->checkbox(['class' => 'ch need-limit-ch'], false); ?>

            <?= $form->field($tariff_model, 'tickets_count', ['options' => ['class' => 'ip_cell need-limit-ch-wrap w100'], 'template' => '<div class="flex align-center">{input}<label class="ip_label">0 - нет вналичии</label></div>{error}{hint}'])->textInput(['class' => 'input_text w49 mr20', 'placeholder' => "100"]); ?>

            <h4 class="lk_step_title mt20 mb0">Цена</h4>
            <?= $form->field($tariff_model, 'prices', ['options' => ['style' => 'display:none;'], 'template' => '{input}{error}{hint}'])->hiddenInput(); ?>
            <div id="price_block"></div>
            <div class="ip_cell w100 flex_centered mb0">
                <button class="button blue medium lk">Сохранить тариф</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="modal_overlay"></div>
    </div>


    <div class="modal" id="fail_service_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Ошибка создания программы</div>
                <p>При сохранении данных возникли ошибки. <?= \app\helpers\MainHelper::getHelpText() ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>

    <!-- модалка удаления формы -->
    <div class="modal" id="remove_form_modal">
        <div class="modal_content">
            <a href="#" class="modal_close" data-fancybox-close>x</a>
            <h4 class="lk_step_title font20 centered">
                Восстановить форму после удаления невозможно. Вы точно хотите удалить форму <br>
                «<span id="delete_form_name"></span>»?
            </h4>
            <div class="modal_buttons">
                <a href="#" class="button w100 eduprog_delete_form" data-formdelete="">Да, удалить</a>
            </div>

        </div>
        <div class="modal_overlay"></div>
    </div>

    <div id="fields_html" style="display: none;">
        <div class="field_wrapper" data-variant="1">
            <div class="adding-question js-adding-question" data-variant="1">
                <h4 class="lk_step_title font20">Короткий произвольный ответ</h4>
                <p>Ответ пользователя на вопрос содержит до 255 символов</p>
                <div class="ip_cell label-on w100">
                    <input type="hidden" name="LKEduprogRegform[fields][..field..][type]" value="text">
                    <input type="hidden" name="LKEduprogRegform[fields][..field..][visible]" value="1">
                    <input type="hidden" name="LKEduprogRegform[fields][..field..][order]" class="js-hidden-order"
                           value="..field..">
                    <input type="text" class="input_text name_field" name="LKEduprogRegform[fields][..field..][name]"
                           placeholder="Текст вопроса" required="">
                    <label class="ip_label">Текст вопроса</label>
                    <div class="input-status"></div>
                    <div class="help-block"></div>
                </div>
                <div class="ip_cell label-on w100">
                    <input type="hidden" name="LKEduprogRegform[fields][..field..][required]" value="0">
                    <input type="checkbox" class="ch" name="LKEduprogRegform[fields][..field..][required]" value="1">
                    <label>Ответ на вопрос обязателен</label>
                    <div class="help-block"></div>
                </div>
                <div class="ip_cell label-on w100 ip_cell-comment">
                    <div class="flex">
                        <input type="text" class="input_text" name="LKEduprogRegform[fields][..field..][comment]"
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
                    <input type="hidden" name="LKEduprogRegform[fields][..field..][type]" value="textarea">
                    <input type="hidden" name="LKEduprogRegform[fields][..field..][visible]" value="1">
                    <input type="hidden" name="LKEduprogRegform[fields][..field..][order]" class="js-hidden-order"
                           value="..field..">
                    <input type="text" class="input_text name_field" name="LKEduprogRegform[fields][..field..][name]"
                           placeholder="Текст вопроса" required="">
                    <label class="ip_label">Текст вопроса</label>
                    <div class="input-status"></div>
                    <div class="help-block"></div>
                </div>
                <div class="ip_cell label-on w100">
                    <input type="hidden" name="LKEduprogRegform[fields][..field..][required]" value="0">
                    <input type="checkbox" class="ch" name="LKEduprogRegform[fields][..field..][required]" value="1">
                    <label>Ответ на вопрос обязателен</label>
                    <div class="help-block"></div>
                </div>
                <div class="ip_cell label-on w100 ip_cell-comment">
                    <div class="flex">
                        <input type="text" class="input_text" name="LKEduprogRegform[fields][..field..][comment]"
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
                    <input type="hidden" name="LKEduprogRegform[fields][..field..][type]" value="radio_list">
                    <input type="hidden" name="LKEduprogRegform[fields][..field..][visible]" value="1">
                    <input type="hidden" name="LKEduprogRegform[fields][..field..][order]" class="js-hidden-order"
                           value="..field..">
                    <input type="text" class="input_text name_field" name="LKEduprogRegform[fields][..field..][name]"
                           placeholder="Текст вопроса" required="">
                    <label class="ip_label">Текст вопроса</label>
                    <div class="input-status"></div>
                    <div class="help-block"></div>
                </div>
                <div class="ip_cell label-on w100">
                    <input type="hidden" name="LKEduprogRegform[fields][..field..][required]" value="0">
                    <input type="checkbox" class="ch" name="LKEduprogRegform[fields][..field..][required]" value="1">
                    <label>Ответ на вопрос обязателен</label>
                    <div class="help-block"></div>
                </div>
                <div class="ip_cell label-on w100 ip_cell-comment">
                    <div class="flex">
                        <input type="text" class="input_text" name="LKEduprogRegform[fields][..field..][comment]"
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
                     data-name="LKEduprogRegform[fields][..field..][list_values][]">
                    <div class="ip_cell label-on w100 variant-question drag-input">
                        <div class="flex">
                            <input type="text" class="input_text"
                                   name="LKEduprogRegform[fields][..field..][list_values][]"
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
                    <input type="hidden" name="LKEduprogRegform[fields][..field..][type]" value="boolean_list">
                    <input type="hidden" name="LKEduprogRegform[fields][..field..][visible]" value="1">
                    <input type="hidden" name="LKEduprogRegform[fields][..field..][order]" class="js-hidden-order"
                           value="..field..">
                    <input type="text" class="input_text name_field" name="LKEduprogRegform[fields][..field..][name]"
                           placeholder="Текст вопроса" required="">
                    <label class="ip_label">Текст вопроса</label>
                    <div class="input-status"></div>
                    <div class="help-block"></div>
                </div>
                <div class="ip_cell label-on w100">
                    <input type="hidden" name="LKEduprogRegform[fields][..field..][required]" value="0">
                    <input type="checkbox" class="ch" name="LKEduprogRegform[fields][..field..][required]" value="1">
                    <label>Ответ на вопрос обязателен</label>
                    <div class="help-block"></div>
                </div>
                <div class="ip_cell label-on w100 ip_cell-comment">
                    <div class="flex">
                        <input type="text" class="input_text" name="LKEduprogRegform[fields][..field..][comment]"
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
                     data-name="LKEduprogRegform[fields][..field..][list_values][]">
                    <div class="ip_cell label-on w100 variant-question drag-input">
                        <div class="flex">
                            <input type="text" class="input_text"
                                   name="LKEduprogRegform[fields][..field..][list_values][]"
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

        <div id="fields_container_html">
            <div class="js-added-question-list"></div>
            <div class="ip_cell w100 mb0 add-question js-question-list" data-fieldkey="0">
                <button class="button blue medium lk">Добавить вопрос</button>
                <div class="add-question-list">
                    <div class="add-question-item" data-variant="1">Короткий произвольный ответ</div>
                    <div class="add-question-item" data-variant="2">Длинный произвольный ответ</div>
                    <div class="add-question-item" data-variant="3">Выбор одного варианта</div>
                    <div class="add-question-item" data-variant="4">Выбор нескольких вариантов</div>
                </div>
            </div>
            <div class="current_field"></div>
        </div>

        <div id="empty_prices_html">
            <div class="append-price-list">
                <div class="ip_cell ip_cell-event-date append-price-element flex align-bottom w100 mb0">
                    <input type="hidden" name="LKEduprogTariff[prices][0][id]" value=""/>
                    <div class="ip_cell datarange_ipc mr20">
                        <input type="number" name="LKEduprogTariff[prices][0][price]" class="input_text"
                               placeholder="Цена, ₽"/>
                    </div>
                    <div class="ip_cell datarange_ipc mr20">
                        <label class="ip_label">Начало действия</label>
                        <input type="text" name="LKEduprogTariff[prices][0][start_publish]" value=""
                               class="input_text disabled" disabled placeholder="С момента публикации"/>
                    </div>
                </div>
                <div class="append-js"></div>
                <div class="ip_cell flex align-center w100 add-price-eduprog-block-js">
                    <button class="button blue small mb0 add-price-eduprog-js" data-pricecount="1">Добавить цену
                    </button>
                    <div class="question_box">
                        <a href="javascript:void(0)" class="question_icon">?</a>
                        <div class="question_text">
                            Вы можете изменять цену билета по мере приближения события
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="add_price_html">
            <div class="ip_cell ip_cell-event-date append-price-element flex align-bottom w100 mb0">
                <input type="hidden" name="LKEduprogTariff[prices][..price..][id]" value=""/>
                <div class="ip_cell datarange_ipc mr20">
                    <input type="number" name="LKEduprogTariff[prices][..price..][price]" class="input_text"
                           placeholder="Цена, ₽"/>
                </div>
                <div class="ip_cell datarange_ipc mr20">
                    <label class="ip_label">Начало действия</label>
                    <input type="text" name="LKEduprogTariff[prices][..price..][start_publish]"
                           class="input_text datepicker-top" placeholder="дд.мм.гг"/>
                    <a href="#!" class="remove-eduprog-price-js"></a>
                </div>
            </div>
        </div>

    </div>

<?php
$url = Url::toRoute(['/pages/eduprog/save-regform/']);
$url_visible_change = Url::toRoute(['/pages/eduprog/switch-visible-form/']);
$url_delete_form = Url::toRoute(['/pages/eduprog/remove-form/']);
$url_fields_html = Url::toRoute(['/pages/eduprog/fields-html/']);

$url_tariff = Url::toRoute(['/pages/eduprog/save-tariff/']);
$url_visible_tariff_change = Url::toRoute(['/pages/eduprog/switch-visible-tariff/']);
$url_delete_tariff = Url::toRoute(['/pages/eduprog/remove-tariff/']);
$url_fields_tariff = Url::toRoute(['/pages/eduprog/fields-tariff/']);

$js = <<<JS
    $('body').on('click','.open-form-js', function(e) {
        e.preventDefault();
        let form_id = $(this).data('form');
        let name = $(this).data('name');
        // очистить форму
        $('#lkeduprogregform-eduprog_id').val('{$original->id}');
        $('#lkeduprogregform-name').val(name);
        if (form_id == 'new') {
            $('#lkeduprogregform-id').val('');
            $('#fields_container').html($('#fields_container_html').html());
            $.fancybox.open($('#new_form_modal'));
        } else {
            $('#lkeduprogregform-id').val(form_id);
            // запросить html полей формы
            $.ajax({
                type: 'GET',
                url: '{$url_fields_html}',
                processData: true,
                dataType: 'json',
                data: {form_id:form_id},
                success: function(data){
                    if (data.status == 'success') {
                        $('#fields_container').html(data.html);
                        $.fancybox.open($('#new_form_modal'));
                    } else {
                        $('#fail_service_modal .modal_title').html('Ошибка редактирования формы');
                        $('#fail_service_modal p').html(data.message);
                        modalPos('#fail_service_modal');
                    }
                }
            });
        }
    });

    $('body').on('click','.js-remove-form', function() {
        // заполнить данные формы
        let formname = $(this).data('name');
        let formid = $(this).data('form');
        $('#delete_form_name').html(formname);
        $('#remove_form_modal .eduprog_delete_form').attr('data-formdelete', formid);
        $.fancybox.open($('#remove_form_modal'));
    });

    $('#eduprog-regform').on('beforeSubmit', function(event) {
        var formData = new FormData($('#eduprog-regform')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    if (data.action == 'append') {
                        $(data.selector).append(data.html_form);
                    }
                    if (data.action == 'replace') {
                        console.log('replace');
                        $(data.selector).replaceWith(data.html_form);
                    }
                    if (data.total_forms == 0) {
                        $('#first_form_slot').css('display','block');
                        $('#second_form_slot').css('display','none');
                    } else if (data.total_forms == 1) {
                        $('#first_form_slot').css('display','none');
                        $('#second_form_slot').css('display','block');
                    } else {
                        $('#first_form_slot').css('display','none');
                        $('#second_form_slot').css('display','none');
                    }
                    $.fancybox.close();
                } else {
                    // показать модалку с ошибкой
                    $.fancybox.close();
                    $('#fail_service_modal .modal_title').html('Ошибка создания формы');
                    $('#fail_service_modal p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
        return false;
    });
    $('#eduprog-regform').on('submit', function(e){
        e.preventDefault();
        return false;
    });

    // остановить, запустить продажи по форме
    $('body').on('click', '.js-visible-form', function(e){
        e.preventDefault();
        let form_id = $(this).data('form');
        let that = this;
        $.ajax({
            type: 'GET',
            url: '{$url_visible_change}',
            processData: true,
            dataType: 'json',
            data: {form_id:form_id},
            success: function(data){
                if (data.status == 'success') {
                    // сменить класс у элемента
                    if (data.visible) {
                        $(that).removeClass('play-element').addClass('pause-element');
                    } else {
                        $(that).removeClass('pause-element').addClass('play-element');
                    }
                } else {
                    $('#fail_service_modal .modal_title').html('Ошибка изменения формы');
                    $('#fail_service_modal p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
    });

    // удалить форму
    $('body').on('click', '.eduprog_delete_form', function(e){
        e.preventDefault();
        let form_id = $(this).attr('data-formdelete');
        $.ajax({
            type: 'GET',
            url: '{$url_delete_form}',
            processData: true,
            dataType: 'json',
            data: {form_id:form_id},
            success: function(data){
                // закрыть модалку
                $.fancybox.close();
                if (data.status == 'success') {
                    // удалить блок с формой
                    $('#regform_'+form_id).remove();
                    if (data.total_forms == 0) {
                        $('#first_form_slot').css('display','block');
                        $('#second_form_slot').css('display','none');
                    } else if (data.total_forms == 1) {
                        $('#first_form_slot').css('display','none');
                        $('#second_form_slot').css('display','block');
                    } else {
                        $('#first_form_slot').css('display','none');
                        $('#second_form_slot').css('display','none');
                    }
                } else {
                    // показать сообщение об ошибке
                    $('#fail_service_modal .modal_title').html('Ошибка удаления формы');
                    $('#fail_service_modal p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
    });


    
    //////////////////////////// ТАРИФЫ ////////////////////////////



    $('body').on('click','.open-tarif-js', function(e) {
        e.preventDefault();
        let tariff_id = $(this).data('tariff');
        let form_id = $(this).data('form');
        $('#eduprog-tariffform').trigger("reset");

        // показать блок с количеством билетов
        $('.tarif-wrapper').find('.need-limit-ch-wrap').show();

        if (tariff_id == 'new') {
            $('#lkeduprogtariff-id').val('');
            $('#lkeduprogtariff-eduprogform_id').val(form_id);
            // обновить блок с ценами
            $('#price_block').html($('#empty_prices_html').html());
            $.fancybox.open($('#new_tariff_modal'));
        } else {
            $('#lkeduprogtariff-id').val(tariff_id);
            $('#lkeduprogtariff-eduprogform_id').val(form_id);
            // запросить данные о тарифе
            $.ajax({
                type: 'GET',
                url: '{$url_fields_tariff}',
                processData: true,
                dataType: 'json',
                data: {tariff_id:tariff_id},
                success: function(data) {
                    if (data.status == 'success') {
                        // заполнить поля формы
                        for (key in data.fields) {
                            if (key == 'unlimit_tickets') {
                                // для чекбоксов отдельная обработка
                                if (data.fields[key] == 0) {
                                    $('#lkeduprogtariff-'+key+':checked').trigger("click");
                                } else if (data.fields[key] == 1) {
                                    $('#lkeduprogtariff-'+key+':not(:checked)').trigger("click");
                                }
                            } else {
                                $('#lkeduprogtariff-'+key).val(data.fields[key]);
                            }
                        }

                        // заполнить блок с ценами
                        $('#price_block').html(data.html);
                        // подключить календари
                        datePicker();
                        // показать форму
                        $.fancybox.open($('#new_tariff_modal'));
                    } else {
                        $('#fail_service_modal .modal_title').html('Ошибка редактирования тарифа');
                        $('#fail_service_modal p').html(data.message);
                        modalPos('#fail_service_modal');
                    }
                }
            });
        }
    });

    $('#eduprog-tariffform').on('beforeSubmit', function(event) {
        var formData = new FormData($('#eduprog-tariffform')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url_tariff}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    if (data.action == 'append') {
                        $(data.selector).append(data.html_tariff);
                    }
                    if (data.action == 'replace') {
                        $(data.selector).replaceWith(data.html_tariff);
                    }
                    $.fancybox.close();
                } else {
                    // показать модалку с ошибкой
                    $.fancybox.close();
                    $('#fail_service_modal .modal_title').html('Ошибка создания формы');
                    $('#fail_service_modal p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
        return false;
    });
    $('#eduprog-tariffform').on('submit', function(e){
        e.preventDefault();
        return false;
    });

    // остановить, запустить продажи по тарифу
    $('body').on('click', '.js-visible-tarif', function(e){
        e.preventDefault();
        let tariff_id = $(this).data('tariff');
        let that = this;
        $.ajax({
            type: 'GET',
            url: '{$url_visible_tariff_change}',
            processData: true,
            dataType: 'json',
            data: {tariff_id:tariff_id},
            success: function(data){
                if (data.status == 'success') {
                    // сменить класс у элемента
                    if (data.visible) {
                        $(that).removeClass('play-tarif').addClass('pause-tarif');
                    } else {
                        $(that).removeClass('pause-tarif').addClass('play-tarif');
                    }
                } else {
                    $('#fail_service_modal .modal_title').html('Ошибка изменения тарифа');
                    $('#fail_service_modal p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
    });

    // удалить тариф
    $('body').on('click', '.js-remove-tarif', function(e){
        e.preventDefault();
        let tariff_id = $(this).data('tariff');
        $.ajax({
            type: 'GET',
            url: '{$url_delete_tariff}',
            processData: true,
            dataType: 'json',
            data: {tariff_id:tariff_id},
            success: function(data){
                if (data.status == 'success') {
                    // удалить блок с тарифом
                    $('#tariff_line_'+tariff_id).remove();
                } else {
                    // показать сообщение об ошибке
                    $('#fail_service_modal .modal_title').html('Ошибка удаления тарифа');
                    $('#fail_service_modal p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
    });

    // добавить цену
    $('body').on('click','.add-price-eduprog-js', function(e) {
        e.preventDefault();
        let iter = parseInt($(this).attr('data-pricecount'));
        let price_html = $('#add_price_html').html();
        // заменить порядковый номер цены
        price_html = price_html.replaceAll('..price..',iter);
        $('#price_block .append-js').append(price_html);
        iter = iter+1;
        $(this).attr('data-pricecount', iter);
        if ($(this).closest('.append-price-list').find('.append-js').children().length >= 4) {
            // если элементов 4 и больше, то скрыть кнопку добавления цены
            $($(this).closest('.append-price-list').find('.add-price-eduprog-block-js')[0]).hide();
        }
        datePicker();
    });

     $('body').on('click', '.remove-eduprog-price-js', function(e) {
        e.preventDefault();
        let container = $(this).closest('.append-js');
        let button = $(this).closest('.append-price-list').find('.add-price-eduprog-block-js');
        $(this).closest('.append-price-element').remove();
        if (container.children().length < 4) {
            // если элементов меньше 4-х, то показать кнопку добавления цены
            button.show();
        }
        return false;
    });

    function datePicker() {
        $('.datepicker, .datepicker-top').not('.keypress').keypress(function(e) {
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

        $('.datepicker-top').datepicker({
            keyboardNavigation: false,
            autoclose: true,
            todayHighlight: true,
            weekStart: 1,
            maxViewMode: 2,
            format: 'dd.mm.yyyy',
            orientation: 'top',
            language: 'ru'
        });

        $('.dateRange').datepair({
            dateClass: 'datepicker'
        });
    }



JS;
$this->registerJs($js);
?>