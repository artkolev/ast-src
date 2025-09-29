<?php

use app\modules\pages\models\PServiceRegIP;
use yii\helpers\Url;

$reg_page = PServiceRegIP::find()->where(['model' => PServiceRegIP::class, 'visible' => 1])->one();
?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <div class="lk_block">
                    <header class="lk_block_header">
                        <h1 class="lk_block_title-big"><?= $model->getNameForView(); ?></h1>
                    </header>
                    <main class="lk_content">
                        <?= $model->content; ?>
                        <br>
                        ВАЖНО: если Вы не являетесь лично ИП, а представляете интересы другого ИП, то Вам необходимо
                        получить заверенное подписью индивидуального предпринимателя Соглашение об аналоге
                        собственноручной подписи. <?php if (!empty($reg_page->primer)) { ?><a
                            href="<?= $reg_page->getFile('primer'); ?>">Ссылка на скачивание.</a><?php } ?>
                        <br>
                        <?php if ($reg_page) { ?>
                            <div class="ip_cell w100">
                                <a href="<?= $reg_page->getUrlPath(); ?>" class="button-o lk_button_submit">Перейти к
                                    регистрации</a>
                            </div>
                        <?php } else { ?>
                            <div class="ip_cell w100">
                                <a href="#" class="button-o open_wait lk_button_submit" data-fancybox>Перейти к
                                    регистрации</a>
                            </div>
                        <?php } ?>
                        <?php if ($query_form) { ?>
                            <div class="ip_cell w100">
                                <a href="#" class="button-o lk_button_submit removeMyQuery">Удалить мою заявку</a>
                            </div>
                        <?php } ?>
                    </main>
                </div>
            </div>
            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
        </div>
    </main>
    <div class="modal" id="delete_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Удаление запроса</div>
                <p>Удаление запроса на публикацию услуг невозможно. <?= \app\helpers\MainHelper::getHelpText() ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>

        </div>
        <div class="modal_overlay"></div>
    </div>
    <div class="modal" id="wait_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Регистрация на оказание услуг</div>
                <p>Маркетплейс сейчас работает в тестовом режиме. Как только этот раздел станет доступен, мы пригласим
                    вас к его использованию.</p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>

        </div>
        <div class="modal_overlay"></div>
    </div>
<?php
$url = Url::toRoute(['/pages/service/deletequery']);
$js = <<<JS
    $('.open_wait').click(function(e){
        e.preventDefault();
        ym(67214377,'reachGoal','register_ip');
        modalPos('#wait_modal');
        return false;
    });
    $('.removeMyQuery').on('click', function(e) {
        e.preventDefault();
        $.ajax({
            type: 'GET',
            url: '{$url}',
            processData: true,
            dataType: 'json',
            data: {action:'remove'},
            success: function(data){
                if (data.status == 'success') {
                    // перезагрузить страницу
                    window.location.href = window.location.href;
                } else {
                    // показать модалку с ошибкой
                    modalPos('#delete_modal');
                }
            }
        });
        return false;
    });
JS;
$this->registerJs($js);
?>