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
                    </header>
                    <main class="lk_content">
                        <?php $form = ActiveForm::begin([
                                'id' => 'regservice-form',
                                'action' => '/pages/service/validate-contract/',
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
                        <h4 class="lk_step_title">ШАГ 2. Заключение договора</h4>
                        <?php if (!empty($contract)) { ?>
                            <?= $form->field($modelform, 'agree_contract', ['template' => '{input}<label>' . $contract->text_label . '</label>{hint}{error}', 'options' => ['class' => 'ip_cell w100']])->checkbox(['class' => 'ch'], false); ?>
                            <?php if (!empty($contract_eduprog)) { ?>
                                <?= $form->field($modelform, 'agree_license_contract', ['template' => '{input}<label>' . $contract_eduprog->text_label . '</label>{hint}{error}', 'options' => ['class' => 'ip_cell w100']])->checkbox(['class' => 'ch'], false); ?>
                            <?php } ?>
                            <?= $form->field($modelform, 'dpo_agreements', ['template' => '{input}{error}{hint}', 'options' => ['class' => 'ip_cell w100']])->widget('app\modules\formagree\widgets\formagree\FormagreeWidget'); ?>
                            <div class="ip_cell w100 flex flex-end">
                                <?php if (!empty($form_page)) { ?>
                                    <a href="<?= $form_page->getUrlPath(); ?>" class="button-o lk_button">Назад</a>
                                <?php } ?>
                                <button type="submit" class="button-o lk_button">Принять условия и подписать</a>
                            </div>
                        <?php } else { ?>
                            <div class="ip_cell w100">
                                <p>Договор для выбранного типа регистрации разрабатывается. Попробуйте вернуться
                                    позже.</p>
                            </div>
                        <?php } ?>
                        <?php ActiveForm::end(); ?>
                    </main>
                </div>
            </div>
            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
        </div>
    </main>
    <div class="modal" id="fail_dogovor_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Подписание договора</div>
                <p>Подписание договора невозможно, обртитесь к администратору</p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
<?php
$url = Url::toRoute(['/pages/service/signcontract/']);
$js = <<<JS
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
                    modalPos('#fail_dogovor_modal');
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