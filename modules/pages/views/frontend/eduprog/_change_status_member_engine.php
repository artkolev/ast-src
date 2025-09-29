<?php
/**
 *   система для смены статусов у слушателей. Применяется в списке слушателей и на внутренних страницах слушателя в ЛК организатора.
 * @var $member_type string Принимает значения 'member_page' или 'member_list' от параметра зависит какой view будет отрисован для замены html на странице
 */

use app\modules\eduprog\models\EduprogMember;
use app\modules\pages\models\SupportPage;
use yii\helpers\Url;

$support_page = SupportPage::find()->where(['model' => SupportPage::class, 'visible' => 1])->one();
$support_url = $support_page ? $support_page->getUrlPath() : false;

?>
    <!-- модалки -->
    <div class="modal" id="member_active">
        <div class="modal_content modal_content-mini">
            <a href="#" class="modal_close" data-fancybox-close>x</a>
            <h2 class="modal_title modal_title-mini">
                Слушатель зачислен на обучение.
            </h2>
            <div class="modal_text modal_text-big modal_text-center">Теперь слушатель может получать новости программы и
                обмениваться сообщениями с вами на сайте Академии.
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
    <div class="modal" id="member_rejected">
        <div class="modal_content modal_content-mini">
            <a href="#" class="modal_close" data-fancybox-close>x</a>
            <h2 class="modal_title modal_title-mini">
                Слушатель не зачислен на обучение.
            </h2>
            <div class="modal_text modal_text-big modal_text-center">Академия возвращает оплату слушателю.</div>
        </div>
        <div class="modal_overlay"></div>
    </div>
    <div class="modal" id="member_expelled">
        <div class="modal_content modal_content-mini">
            <a href="#" class="modal_close" data-fancybox-close>x</a>
            <h2 class="modal_title modal_title-mini">
                Слушатель отчислен.
            </h2>
            <div class="modal_text modal_text-big modal_text-center">Теперь слушатель не может получать новости и
                обмениваться с вами сообщениями на сайте Академии. Если вы по ошибке отчислили слушателя, свяжитесь с
                Академией.
            </div>
            <?php if ($support_url) { ?>
                <div class="buttons">
                    <a href="<?= $support_url; ?>" target="_blank" class="button-o blue mini w100">Связаться с
                        Академией</a>
                </div>
            <?php } ?>
        </div>
        <div class="modal_overlay"></div>
    </div>
    <div class="modal" id="member_error">
        <div class="modal_content modal_content-mini">
            <a href="#" class="modal_close" data-fancybox-close>x</a>
            <h2 class="modal_title modal_title-mini">
                К сожалению, статус невозможно сменить
            </h2>
            <?php if ($support_url) { ?>
                <div class="buttons">
                    <a href="<?= $support_url; ?>" target="_blank" class="button-o blue mini w100">Связаться с
                        Академией</a>
                </div>
            <?php } ?>
        </div>
        <div class="modal_overlay"></div>
    </div>

    <!-- модалки с вопросами -->
    <div class="modal" id="member_ask_reject">
        <div class="modal_content modal_content-mini">
            <a href="#" class="modal_close" data-fancybox-close>x</a>
            <h2 class="modal_title modal_title-mini">
                Вы уверены, что <span id="member_ask_reject_name"></span> <br> не соответствует требованиям программы?
            </h2>
            <div class="buttons">
                <button class="button-o blue mini w100" data-fancybox-close>Отмена</button>
                <button data-close="member_ask_reject" data-status="<?= EduprogMember::STATUS_REJECTED; ?>"
                        id="reject_button" class="button-o blue mini w100 change-status-js">Да, отклонить
                </button>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>

    <div class="modal" id="member_ask_expell">
        <div class="modal_content modal_content-mini">
            <a href="#" class="modal_close" data-fancybox-close>x</a>
            <h2 class="modal_title modal_title-mini">
                Вы уверены, что хотите досрочно отчислить <br>
                слушателя <span id="member_ask_expell_name"></span>?
            </h2>
            <div class="buttons">
                <button class="button-o blue mini w100" data-fancybox-close>Отмена</button>
                <button data-close="member_ask_expell" data-status="<?= EduprogMember::STATUS_EXPELLED; ?>"
                        id="expell_button" class="button-o blue mini w100 change-status-js">Да, отчислить
                </button>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>

<?php
$url_status = Url::toRoute(['/pages/eduprog/change-member-status/']);

$status_active = EduprogMember::STATUS_ACTIVE;
$status_rejected = EduprogMember::STATUS_REJECTED;
$status_expelled = EduprogMember::STATUS_EXPELLED;

$js = <<<JS

    $('body').on('click', '.show-ask-modal-js', function(e){
        e.preventDefault();
        let member_id = $(this).data('member');
        let action = $(this).data('action');
        // let member_card = $('#member_'+member_id+' .name-field-for-modal-js').text();
        // заполнить ФИО в модалке
        $('#member_ask_'+action+'_name').html($('#member_'+member_id+' .name-field-for-modal-js').text());
        // заполнить data-аттрибут на кнопке подтверждения
        $('#'+action+'_button').data('member',member_id);
        // показать моадлку
        $.fancybox.open($('#member_ask_'+action));
    });

    $('body').on('click', '.change-status-js', function(e){
        e.preventDefault();
        let member_id = $(this).data('member');
        let status = $(this).data('status');

        // если надо закрыть модалку перед выполнением запроса
        let close_modal = $(this).data('close');
        if (close_modal !== undefined) {
            $.fancybox.close($('#'+close_modal));
        }
        // отправить аякс на изменение статуса
        $.ajax({
            type: 'GET',
            url: '{$url_status}',
            processData: true,
            dataType: 'json',
            data: {member_id:member_id, status:status, type:'{$member_type}'},
            success: function(data) {
                if (data.status == 'success') {
                    if (data.new_status == '{$status_active}') {
                        $.fancybox.open($('#member_active'));
                    }
                    if (data.new_status == '{$status_rejected}') {
                        $.fancybox.open($('#member_rejected'));
                    }
                    if (data.new_status == '{$status_expelled}') {
                        $.fancybox.open($('#member_expelled'));
                    }
                    // заменить карточку слушателя на странице
                    $('#member_'+member_id).replaceWith(data.member_card_html);
                } else {
                    $.fancybox.open($('#member_error'));
                }
            }
        });
        return false;
    });

JS;
$this->registerJs($js);
?>