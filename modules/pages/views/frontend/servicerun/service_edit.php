<?php

use app\models\ServiceForm;
use app\modules\service\models\Service;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var \yii\web\View $this
 * @var \app\modules\pages\models\ServiceEdit $model
 * @var ServiceForm $modelform
 * @var Service $original
 * @var int $currentStep
 */
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

                <?= $this->render('_service_step_bar', [
                        'step' => $currentStep,
                        'modelform' => $modelform,
                        'model' => $model,
                ]); ?>

                <?php $form = ActiveForm::begin([
                        'id' => 'edit-service-form',
                        'action' => '/site/ajaxValidate/',
                        'options' => ['class' => 'service-form marked'],
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

                <?= $this->render('service_step/step_' . $currentStep, [
                        'form' => $form,
                        'modelform' => $modelform,
                        'original' => $original
                ]); ?>

                <div class="lk_block">
                    <div class="lk_content">
                        <?php if ($currentStep == 1) { ?>
                            <div class="ip_cell w100 flex flex-end mb0">
                                <button type="submit" class="button blue medium lk nextBtn">Продолжить</button>
                            </div>
                        <?php } else { ?>
                            <?php $url = Url::toRoute([$model->getUrlPath(), 'step' => $currentStep - 1, 'id' => $modelform->id]); ?>
                            <div class="ip_cell w100 flex justify-between buttons-wrapper mb0">
                                <button type="button" class="button-o gray medium buttonBackStep"
                                        onclick="location.href='<?= $url; ?>'">Вернуться
                                </button>
                                <?php if ($currentStep == 6) { ?>
                                    <?php if ($original->status == Service::STATUS_DRAFT) { ?>
                                        <button type="submit" class="button-o gray medium toDraftBtn"
                                                style="margin-right: auto;">Сохранить в черновик
                                        </button>
                                    <?php } ?>
                                    <?php if ((bool)$modelform->hasDiff || $original->status == Service::STATUS_DRAFT) { ?>
                                        <button type="submit" class="button blue medium lk toModerateBtn"
                                                data-action="moder">Отправить на модерацию
                                        </button>
                                    <?php } else { ?>
                                        <button type="submit" class="button blue medium lk nextBtn">Отправить</button>
                                    <?php } ?>

                                <?php } else { ?>
                                    <button type="submit" class="button blue medium lk nextBtn">Продолжить</button>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>

            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>

            <div class="modal" id="fail_service_modal">
                <div class="modal_content">
                    <a href="#" class="modal_close">x</a>
                    <div class="success_box">
                        <div class="modal_title">Ошибка создания услуги</div>
                        <p>При создании услуги возникли ошибки.</p>
                        <div class="modal_buttons">
                            <a href="#" class="button small close_modal">ОК</a>
                        </div>
                    </div>

                </div>
                <div class="modal_overlay"></div>
            </div>
            <div class="modal-side_banner" id="moderate_service_modal_fancy">
                <div class="modal-review-content">
                    <div class="modal-side_banner-info">
                        <div class="success_box">
                            <div class="modal_title">Модерация</div>
                            <p>Услуга отправлена на модерацию, в течение пяти рабочих дней она будет
                                рассмотрена.<br><br>Если для размещения потребуется дополнительная информация, вы
                                получите уведомление от менеджера клиентского сервиса. Вам потребуется дополнить
                                карточку услуги.</p>
                            <div class="ip_cell buttons w100 mt20 mb0">
                                <a href="#" class="button blue small">Ок</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?php
$url = Url::toRoute(['/pages/servicerun/save-part-service', 'step' => $currentStep]);
$js = <<<JS
    let nextBtn = $('#edit-service-form .nextBtn'),
        toDraftBtn =   $('#edit-service-form .toDraftBtn'),
        buttonBackStep =   $('#edit-service-form .buttonBackStep'),
        toModerateBtn =   $('#edit-service-form .toModerateBtn');
    
    let url = '{$url}';
    
     toModerateBtn.on('click', function(event){
         let action = $(this).data('action');
         
         return action ? url += '&action2Moderate=1' : url;
     });
     
    $('#edit-service-form').on('beforeSubmit', function(event){
        nextBtn.prop('disabled', true);
        toDraftBtn.prop('disabled', true);
        toModerateBtn.prop('disabled', true);
        buttonBackStep.prop('disabled', true);
        
        var formData = new FormData($('#edit-service-form')[0]);
        $.ajax({
            type: 'POST',
            url: url,
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    // переадресация на второй шаг
                    if (data.show_message == 'show') {
                        $('#moderate_service_modal_fancy .ip_cell.buttons a').attr('href',data.redirect_to);
                        $.fancybox.open({
                            src: '#moderate_service_modal_fancy',
                            type: 'inline',
                            modal: true,
                            beforeClose: function() {
                                return false;
                            }
                        });
                    } else {
                        window.location.href = data.redirect_to;
                    }
                } else {
                    nextBtn.prop('disabled', false);
                    buttonBackStep.prop('disabled', false);
                    
                    // показать модалку с ошибкой
                    $('#fail_service_modal .modal_title').html('Ошибка создания услуги');
                    $('#fail_service_modal p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
        return false;
    });
    
    $('#serviceedit-form').on('submit', function(e){
        e.preventDefault();
        return false;
    });
JS;
$this->registerJs($js);
?>