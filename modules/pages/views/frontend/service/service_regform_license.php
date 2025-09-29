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
                                'action' => '/pages/service/validate-license/',
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
                        <h4 class="lk_step_title mt20">Лицензируемые услуги</h4>
                        <div class="ip_cell w100">
                            <?= $form->field($modelform, 'license_service', ['template' => '{input}<label>Оказываю лицензируемые услуги</label>{hint}{error}', 'options' => ['class' => 'ip_cell w100']])->checkbox(['class' => 'ch license-ch'], false); ?>
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
                                            <input data-file_id="<?= $file->id; ?>" type="file" class="upload_pdf-input"
                                                   name="RegisterMarketPlace[license][]"
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

                        <?php if (!empty($contract_eduprog)) { ?>
                            <?= $form->field($modelform, 'agree_license_contract', ['template' => '{input}<label>' . $contract_eduprog->text_label . '</label>{hint}{error}', 'options' => ['class' => 'ip_cell w100']])->checkbox(['class' => 'ch'], false); ?>
                        <?php } ?>
                        <?= $form->field($modelform, 'dpo_agreements', ['template' => '{input}{error}{hint}', 'options' => ['class' => 'ip_cell w100']])->widget('app\modules\formagree\widgets\formagree\FormagreeWidget'); ?>

                        <div class="ip_cell w100 flex flex-end">
                            <button type="submit" class="button-o blue lk_button">Принять условия и подписать</button>
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

$url = Url::toRoute(['/pages/service/savelicense']);
$url_files_rem_license = Url::toRoute(['/pages/service/removeooofileslicense']);
$js = <<<JS
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