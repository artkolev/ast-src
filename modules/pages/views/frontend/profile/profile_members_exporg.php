<?php

use app\helpers\MainHelper;
use app\modules\pages\models\ProfileMembers;
use app\modules\users\models\UserExporg;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var ProfileMembers $model
 * @var UserExporg $user
 */
$this->registerJsFile('/js/jquery-ui.min.js', ['depends' => [app\assets\AppAsset::class]]);
$this->registerJsFile('/js/jquery.ui.touch-punch.min.js', ['depends' => [app\assets\AppAsset::class]]);
$this->registerJsFile('/js/lk-events.js', ['depends' => [app\assets\AppAsset::class]]);
?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <div class="lk_block">
                    <main class="lk_content">
                        <h1 class="lk_block_title-big mb30"><?= $model->getNameForView(); ?></h1>
                        <?php if (!empty($user->profileExtended)) { ?>
                            <?php $form = ActiveForm::begin([
                                    "id" => "members-form",
                                    "action" => "/site/ajaxValidate/",
                                    "options" => ["class" => "marked"],
                                    "enableAjaxValidation" => false,
                                    "enableClientValidation" => true,
                                    "validateOnSubmit" => true,
                                    "validateOnChange" => true,
                                    "validateOnType" => false,
                                    "validateOnBlur" => true,
                                    "fieldConfig" => [
                                            "options" => ["class" => "ip_cell w100"],
                                            "template" => "{label}{input}{error}{hint}",
                                            "inputOptions" => ["class" => "input_text"],
                                            "labelOptions" => ["class" => "ip_label"],
                                    ],
                            ]); ?>
                            <div id="members_list" class="drag-list js-speaker-list js-dragndrop-list">
                                <?php
                                $key_max_members = 0;
                                foreach (MainHelper::cleanInvisibleMultifield($user->profileExtended->members) as $key => $member) {
                                    if ($key_max_members < $key) {
                                        $key_max_members = $key;
                                    }
                                    $member = (object)$member;
                                    $member->id = $key;
                                    ?>
                                    <div id="member_<?= $member->id; ?>"
                                         class="drag-element drag-element-mobile js-dragndrop-element"
                                         data-sort="<?= $key; ?>">
                                        <?php
                                        echo $form->field($user->profileExtended, 'members[' . $member->id . '][order]', ['template' => '{input}', 'options' => ['style' => 'display:none;']])->hiddenInput(['class' => 'order_field']);
                                        echo $form->field($user->profileExtended, 'members[' . $member->id . '][visible]', ['template' => '{input}', 'options' => ['style' => 'display:none;']])->hiddenInput(['class' => 'visible_field']);
                                        ?>
                                        <div class="drag-element-infos">
                                            <div id="member_name_text_<?= $member->id; ?>"
                                                 class="drag-element-info drag-element-name"><?= $member->name; ?></div>
                                        </div>
                                        <div class="drag-burger drag-burger-element"></div>
                                        <div class="edit-element js-edit-member"
                                             data-fancyelement="#member_modal_<?= $member->id; ?>"></div>
                                        <div class="remove-element js-remove-member"></div>
                                        <div style="display:none;">
                                            <div class="modal" id="member_modal_<?= $member->id; ?>">
                                                <div class="modal_content">
                                                    <a href="#" class="modal_close" data-fancybox-close>x</a>
                                                    <div class="modal_title">Добавить участника организации</div>
                                                    <?php echo $form->field($user->profileExtended, 'members[' . $member->id . '][name]', ['template' => '<label class="ip_label">Фамилия Имя Отчество</label> {input}{error}{hint}', 'options' => ['class' => 'ip_cell w100']])->textInput(['class' => 'transname input_text', 'data-nametext' => '#member_name_text_' . $member->id, 'placeholder' => '']); ?>
                                                    <div class="ip_cell w100 mb0">
                                                        <button class="button blue big w100" data-fancybox-close>
                                                            Сохранить
                                                        </button>
                                                    </div>
                                                    <div class="modal_overlay"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>

                            <div class="ip_cell w100">
                                <button data-field="member" data-cur_key="<?= $key_max_members; ?>"
                                        class="button blue medium lk add-member mb30">Добавить участника
                                </button>
                                <button class="button-o lk_button_submit" type="submit">Сохранить</button>
                            </div>

                            <?php
                            ob_start();
                            $empty_key = "change_me";

                            /** Шаблон для добавления участника */
                            ?>
                            <div id="member_<?= $empty_key; ?>"
                                 class="drag-element drag-element-mobile js-dragndrop-element"
                                 data-sort="<?= $empty_key; ?>">
                                <?php
                                echo $form->field($user->profileExtended, 'members[' . $empty_key . '][order]', ['template' => '{input}', 'options' => ['style' => 'display:none;']])->hiddenInput(['class' => 'order_field', 'value' => $empty_key]);
                                echo $form->field($user->profileExtended, 'members[' . $empty_key . '][visible]', ['template' => '{input}', 'options' => ['style' => 'display:none;']])->hiddenInput(['class' => 'visible_field', 'value' => 1]);
                                ?>
                                <div class="drag-element-infos">
                                    <div id="member_name_text_<?= $empty_key; ?>"
                                         class="drag-element-info drag-element-name"></div>
                                </div>
                                <div class="drag-burger drag-burger-element"></div>
                                <div class="edit-element js-edit-member"
                                     data-fancyelement="#member_modal_<?= $empty_key; ?>"></div>
                                <div class="remove-element js-remove-member"></div>
                                <div style="display:none;">
                                    <div class="modal" id="member_modal_<?= $empty_key; ?>">
                                        <div class="modal_content">
                                            <a href="#" class="modal_close" data-fancybox-close>x</a>
                                            <div class="modal_title">Добавить участника организации</div>
                                            <?php echo $form->field($user->profileExtended, 'members[' . $empty_key . '][name]', ['template' => '<label class="ip_label">Фамилия Имя Отчество</label> {input}{error}{hint}', 'options' => ['class' => 'ip_cell w100']])->textInput(['class' => 'transname input_text', 'data-nametext' => '#member_name_text_' . $empty_key, 'placeholder' => '', 'value' => '']); ?>
                                            <div class="ip_cell w100 mb0">
                                                <button class="button blue big w100" data-fancybox-close>Сохранить
                                                </button>
                                            </div>
                                            <div class="modal_overlay"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $cleanHtmlMemberBlock = ob_get_clean();
                            $this->registerJsVar(
                                    "member_newitem",
                                    $cleanHtmlMemberBlock,
                                    $position = yii\web\View::POS_HEAD
                            );
                            ?>
                            <?php ActiveForm::end(); ?>
                        <?php } ?>
                    </main>
                </div>
            </div>
            <?= \app\modules\users\widgets\profile\ExporgmenuWidget::widget(); ?>
        </div>
    </main>

    <div class="modal" id="fail_profile_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Ошибка сохранения данных</div>
                <p><?= \app\helpers\MainHelper::getHelpText(); ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
    <div class="modal" id="success_profile_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Изменение участников организации</div>
                <p>Данные об участнике организации успешно изменены</p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
<?php
$url = Url::toRoute(["/pages/profile/savemember"]);
$js = <<<JS
    $('body').on('click', '.js-edit-member', function(e) {
        e.preventDefault();
         $.fancybox.open({
            src: $(this).data('fancyelement'),
            type: 'inline',
            parentEl: "#members-form",
        });
    });

    $('body').on('click','.js-remove-member', function(e) {
        $(this).closest('.drag-element').remove();
    });
    
    $('body').on('keyup change paste','.transname', function(e){
        $($(this).data('nametext')).html($(this).val());
    });
    
    $('body').on('click','.add-member', function(e){
        e.preventDefault();
        let field_name = $(this).data('field');
        let new_html = window[field_name + '_newitem'];
        let cur_key = parseInt($(this).attr('data-cur_key'));
        cur_key = cur_key + 1;
        new_html = new_html.replace(/change_me/g, cur_key);
        $('#members_list').append(new_html);
        $(this).attr('data-cur_key',cur_key);
        
        $.fancybox.open({
            src: '#member_modal_'+ cur_key,
            type: 'inline',
            parentEl: "#members-form",
        });
    });
    
    let xhr;
    
    $('#members-form').on('submit', function(e){
        e.preventDefault();
        if(xhr){
            // Не доаускает повторной отправки формы, если запрос уже ушёл в эфир.
            return false; 
        }
        let formData = new FormData($('#members-form').get(0));
        xhr = $.ajax({
            url: '{$url}',
            type: 'POST',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: (data, status, jqXHR) => {
                if(data.status === 'fail'){
                    $('#fail_profile_modal .success_box p').html(data.message);
                    modalPos('#fail_profile_modal');                    
                } else if (data.status === 'success') {
                    $('#success_profile_modal .success_box p').html(data.message);
                    modalPos('#success_profile_modal');
                    setTimeout(function () {
                        closeModal('#success_profile_modal');
                    }, 2000);
                }   
            },
            complete: (jqXHR, status) => {
                xhr = null;
            } 
        });
    });
JS;
$this->registerJs($js);
?>