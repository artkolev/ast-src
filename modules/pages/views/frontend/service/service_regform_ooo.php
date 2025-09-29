<?php

use app\helpers\MainHelper;
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
                    <main class="lk_content">
                        <?php $form = ActiveForm::begin([
                                'id' => 'regservice-form',
                                'action' => '/pages/service/validate/',
                                'options' => ['class' => 'register_form marked'],
                                'enableAjaxValidation' => true,
                                'enableClientValidation' => false,
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
                        <h4 class="lk_step_title">ШАГ 1. Введите реквизиты</h4>
                        <?= $form->field($modelform, 'type', ['template' => '{input}{error}{hint}', 'options' => ['class' => '']])->hiddenInput(); ?>

                        <?= $form->field($modelform, 'organization_name', ['template' => '<div class="flex">{input}<div class="question_box"><a href="javascript:void(0)" class="question_icon">?</a><div class="question_text">Необходимо указать юридическую форму организации, Например "Общество с ограниченной ответственностью Ромашка"</div></div></div>{error}{hint}'])->textInput(['placeholder' => "Наименование организации (полностью)"]); ?>
                        <?= $form->field($modelform, 'inn')->textInput(['class' => "input_text inn-mask10"]); ?>

                        <p>Юридический адрес</p>
                        <div class="yur-addr">
                            <?= $form->field($modelform, 'ur_index', ['options' => ['class' => 'ip_cell']])->textInput(['data-addr' => 'zip', 'autocomplete' => 'off']); ?>
                            <?= $form->field($modelform, 'ur_region', ['options' => ['class' => 'ip_cell']])->textInput(['data-addr' => 'region', 'autocomplete' => 'off']); ?>
                            <?= $form->field($modelform, 'ur_city', ['options' => ['class' => 'ip_cell']])->textInput(['data-addr' => 'city', 'autocomplete' => 'off']); ?>
                            <?= $form->field($modelform, 'ur_street', ['options' => ['class' => 'ip_cell']])->textInput(['data-addr' => 'street', 'autocomplete' => 'off']); ?>
                            <div class="flex">
                                <?= $form->field($modelform, 'ur_house', ['options' => ['class' => 'ip_cell']])->textInput(['data-addr' => 'house', 'autocomplete' => 'off', 'class' => 'optionalNumber input_text']); ?>
                                <?= $form->field($modelform, 'ur_corpus', ['options' => ['class' => 'ip_cell']])->textInput(['data-addr' => 'korp', 'autocomplete' => 'off']); ?>
                                <?= $form->field($modelform, 'ur_room', ['options' => ['class' => 'ip_cell']])->textInput(['data-addr' => 'flat', 'autocomplete' => 'off', 'class' => 'optionalNumber input_text']); ?>
                            </div>
                        </div>
                        <p>Почтовый адрес</p>
                        <?= $form->field($modelform, 'match_post', ['template' => '{input}<label>Совпадает с юридическим</label>{hint}{error}', 'options' => ['class' => 'ip_cell']])->checkbox(['class' => 'ch mailing-ch'], false); ?>
                        <p class="mailing-address">
                            <span class="zip"></span>
                            <span class="region"></span>
                            <span class="city"></span>
                            <span class="street"></span>
                            <span class="house"></span>
                            <span class="korp"></span>
                            <span class="flat"></span>
                        </p>
                        <div class="mail-addr">
                            <?= $form->field($modelform, 'post_index', ['options' => ['class' => 'ip_cell'], 'labelOptions' => ['class' => 'ip_label mark-this']])->textInput(); ?>
                            <?= $form->field($modelform, 'post_region', ['options' => ['class' => 'ip_cell'], 'labelOptions' => ['class' => 'ip_label mark-this']])->textInput(); ?>
                            <?= $form->field($modelform, 'post_city', ['options' => ['class' => 'ip_cell'], 'labelOptions' => ['class' => 'ip_label mark-this']])->textInput(); ?>
                            <?= $form->field($modelform, 'post_street', ['options' => ['class' => 'ip_cell'], 'labelOptions' => ['class' => 'ip_label mark-this']])->textInput(); ?>
                            <div class="flex">
                                <?= $form->field($modelform, 'post_house', ['options' => ['class' => 'ip_cell'], 'labelOptions' => ['class' => 'ip_label mark-this']])->textInput(['class' => 'optionalNumber input_text']); ?>
                                <?= $form->field($modelform, 'post_corpus', ['options' => ['class' => 'ip_cell'], 'labelOptions' => ['class' => 'ip_label ']])->textInput(); ?>
                                <?= $form->field($modelform, 'post_room', ['options' => ['class' => 'ip_cell'], 'labelOptions' => ['class' => 'ip_label ']])->textInput(['class' => 'optionalNumber input_text']); ?>
                            </div>
                        </div>

                        <div class="mb20 pdf_container">
                            <label class="ip_label" for="regservice-docs">
                                Загрузите соглашение об аналоге собственноручной подписи. Заполняется только если Вы не
                                являетесь генеральным директором юридического лица.
                                <?php if (!empty($model->primer)) { ?><a href="<?= $model->getFile('primer'); ?>">
                                        Скачать соглашение</a><?php } ?>
                            </label><br>
                            <div class="upload_pdf_list" data-keeper_id="<?= $user->organization->id; ?>"
                                 data-maxfiles="2" data-text="Поддерживающиеся форматы PDF, ZIP, PNG, JPG, DOCX до 2 Мб"
                                 data-accept=".pdf,.zip,.png,.jpg,.docx" data-name="RegisterMarketPlace[docs][]">
                                <?php if (!empty($user->organization->docs)) { ?>
                                    <?php foreach ($user->organization->docs as $file) { ?>
                                        <div class="upload_pdf_box added">
                                            <a href="#" class="upload_pdf_link">Поддерживающиеся форматы PDF, ZIP, PNG,
                                                JPG, DOCX до 2 Мб</a>
                                            <span class="upload_pdf_result"><b><?= $file->name; ?></b><span><?= MainHelper::prettyFileSize($file->size); ?></span></span>
                                            <a href="#" class="file_pdf_remove">x</a>
                                            <input data-file_id="<?= $file->id; ?>" type="file" class="upload_pdf-input"
                                                   name="RegisterMarketPlace[docs][]"
                                                   accept=".pdf,.zip,.png,.jpg,.docx"/>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                                <?php if (count($user->organization->docs) == 0) { ?>
                                    <div class="upload_pdf_box">
                                        <a href="#" class="upload_pdf_link">Поддерживающиеся форматы PDF, ZIP, PNG, JPG,
                                            DOCX до 2 Мб</a>
                                        <span class="upload_pdf_result"></span>
                                        <a href="#" class="file_pdf_remove">x</a>
                                        <input data-file_id="" type="file" class="upload_pdf-input"
                                               name="RegisterMarketPlace[docs][]" accept=".pdf,.zip,.png,.jpg,.docx"/>
                                    </div>
                                <?php } ?>
                                <div class="upload_pdf_js"></div>
                            </div>
                            <a href="#" <?php if (count($user->organization->docs) >= 2) { ?> style="display:none;" <?php } ?>
                               class="button-o more plus addMorePdf">Добавить ещё</a>
                        </div>
                        <?= $form->field($modelform, 'bank')->textInput(); ?>
                        <?= $form->field($modelform, 'raschet_account')->textInput(['class' => "input_text rs-mask"]); ?>
                        <?= $form->field($modelform, 'kor_account')->textInput(['class' => "input_text ks-mask"]); ?>
                        <?= $form->field($modelform, 'bik')->textInput(['class' => "input_text bik-mask"]); ?>
                        <?= $form->field($modelform, 'kpp_bank')->textInput(['class' => "input_text kpp-mask"]); ?>

                        <?= $form->field($modelform, 'nds')->radioList(
                                $user->organization->getNdsList(),
                                ['item' => function ($index, $label, $name, $checked, $value) {
                                    $return = '<div class="ip_cell w100"><input type="radio" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked="checked"' : '') . ' class="ch"><label class="notmark">' . $label . '</label></div>';
                                    return $return;
                                }
                                ]
                        ); ?>
                        <?php if ($user->role == 'exporg') { ?>
                            <h4 class="lk_step_title mt20">Лицензируемые услуги</h4>
                            <div class="ip_cell w100">
                                <?= $form->field($modelform, 'license_service', ['template' => '{input}<label>Оказываю лицензируемые услуги</label>{hint}{error}', 'options' => ['class' => 'ip_cell w49']])->checkbox(['class' => 'ch license-ch'], false); ?>
                            </div>
                            <div class="mb20 pdf_container license-ch-wrap" style="display: none;">
                                <div class="upload_pdf_list upload_pdf_list_license"
                                     data-keeper_id="<?= $user->organization->id; ?>" data-maxfiles="10"
                                     data-text="Загрузите файл лицензии <br>Поддерживающиеся форматы PDF, ZIP, PNG, JPG, DOCX до 2 Мб"
                                     data-accept=".pdf,.zip,.png,.jpg,.docx" data-name="RegisterMarketPlace[license][]">
                                    <?php if (!empty($user->organization->license)) { ?>
                                        <?php foreach ($user->organization->license as $file) { ?>
                                            <div class="upload_pdf_box added">
                                                <a href="#" class="upload_pdf_link">Загрузите файл лицензии <br>Поддерживающиеся
                                                    форматы PDF, ZIP, PNG, JPG, DOCX до 2 Мб</a>
                                                <span class="upload_pdf_result"><b><?= $file->name; ?></b><span><?= MainHelper::prettyFileSize($file->size); ?></span></span>
                                                <a href="#" class="file_pdf_remove file_pdf_remove_license">x</a>
                                                <input data-file_id="<?= $file->id; ?>" type="file"
                                                       class="upload_pdf-input" name="RegisterMarketPlace[license][]"
                                                       accept=".pdf,.zip,.png,.jpg,.docx"/>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                    <?php if (count($user->organization->license) == 0) { ?>
                                        <div class="upload_pdf_box">
                                            <a href="#" class="upload_pdf_link">Загрузите файл лицензии <br>Поддерживающиеся
                                                форматы PDF, ZIP, PNG, JPG, DOCX до 2 Мб</a>
                                            <span class="upload_pdf_result"></span>
                                            <a href="#" class="file_pdf_remove file_pdf_remove_license">x</a>
                                            <input data-file_id="" type="file" class="upload_pdf-input"
                                                   name="RegisterMarketPlace[license][]"
                                                   accept=".pdf,.zip,.png,.jpg,.docx"/>
                                        </div>
                                    <?php } ?>
                                    <div class="upload_pdf_js_license"></div>
                                </div>
                                <a href="#" <?php if (count($user->organization->license) >= 10) { ?> style="display:none;" <?php } ?>
                                   class="button-o more plus addMorePdf addMorePdf_license">Добавить ещё</a>
                            </div>
                        <?php } ?>

                        <?php // echo $form->field($modelform, 'comment',['template'=>'{label}{input}<span class="symbols_counter">500 символов</span>{error}{hint}','options'=>['class'=>'ip_cell w100 symbols_counter_box']])->textArea(['placeholder'=>'Укажите особую информацию','class'=>"input_text limitedSybmbols",'maxlength'=>500]);?>
                        <?= $form->field($modelform, 'agreements', ['template' => '{input}{error}{hint}', 'options' => ['class' => 'ip_cell w100']])->widget('app\modules\formagree\widgets\formagree\FormagreeWidget'); ?>
                        <div class="ip_cell w100 flex flex-end">
                            <button type="submit" class="button-o blue lk_button">Продолжить</button>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </main>
                </div>
            </div>
            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
        </div>
    </main>
    <div class="modal" id="fail_service_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Ошибка регистрации Юрлица</div>
                <div id="error_msg"><p>При сохранении данных возникли
                        ошибки. <?= \app\helpers\MainHelper::getHelpText() ?></p></div>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>

        </div>
        <div class="modal_overlay"></div>
    </div>
<?php
$url = Url::toRoute(['/pages/service/saveooo']);
$url_files_rem = Url::toRoute(['/pages/service/removeooofiles']);
$url_files_rem_license = Url::toRoute(['/pages/service/removeooofileslicense']);
$js = <<<JS
    $(document).on('click', '#regservice-form .file_pdf_remove', function () {
        let file_id = $($(this).parent().find('.upload_pdf-input')[0]).attr('data-file_id');
        let keeper_id = $('#regservice-form .upload_pdf_list').attr('data-keeper_id');
        if(!$(this).hasClass('.file_pdf_remove_license')) {
            $.ajax({
                type: 'POST',
                url: '{$url_files_rem}',
                processData: true,
                dataType: 'json',
                data: {id:file_id, keeper_id:keeper_id},
                success: function(data) {
                    if (data.status == 'success') {
                        // ничего не делаем - все делает скрипт в main.js
                        // надеюсь этот скрипт учитывает что файл мог не удалиться? 
                    } else if (data.status == 'fail') {
                        //modalPos('#fail_service_modal');
                    }

                }
            });
        }  
        return false;
    });

    $(document).on('click', '#regservice-form .file_pdf_remove_license', function () {
        let file_id = $($(this).parent().find('.upload_pdf-input')[0]).attr('data-file_id');
        let keeper_id = $('#regservice-form .upload_pdf_list_license').attr('data-keeper_id');
        $.ajax({
            type: 'POST',
            url: '{$url_files_rem_license}',
            processData: true,
            dataType: 'json',
            data: {id:file_id, keeper_id:keeper_id},
            success: function(data) {
                if (data.status == 'success') {
                    // ничего не делаем - все делает скрипт в main.js
                    // надеюсь этот скрипт учитывает что файл мог не удалиться? 
                } else if (data.status == 'fail') {
                    //modalPos('#fail_service_modal');
                }

            }
        });
        return false;
    });


    $('#regservice-form').on('beforeSubmit', function(event){
        var formData = new FormData($('#regservice-form')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    // переадресация на страницу успешной регистрации Эксперта
                    window.location.href = data.redirect_to;
                } else {
                    // показать модалку с ошибкой
                    $('#error_msg').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
        return false;
    });
    $('#regservice-form').on('submit', function(e){
        e.preventDefault();
        return false;
    });
JS;
$this->registerJs($js);
?>