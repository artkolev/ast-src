<?php
/*
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\service\models\Service;
?>
<main class="sec content_sec">
    <div class="container middle">
        <h1><?=$model->getNameForView()?></h1>
        <div class="subheader">
            <?=$model->content;?>
        </div>
        <?php if (!empty($items)) { ?>
            <section class="articles_box">
                <?php foreach ($items as $key => $service) { ?>
                    <article id="service_<?=$service->id?>" class="service_item article_wide_item">
                        <div class="article_wide-info">
                            <h3><a href="<?=$service->getUrlPath()?>"><?=$service->name?></a></h3>
                            <p><?=$service->description?></p>
                            <?php if ($service->kind == Service::KIND_ONLINE || $service->kind == Service::KIND_HYBRID) { ?>
                                <p>Услуга проводится в <?=$service->platform?></p>
                            <?php } ?>
                            <?php if ($service->kind == Service::KIND_OFFLINE || $service->kind == Service::KIND_HYBRID) { ?>
                                <p>Услуга проводится в городе <?=$service->city->name?></p>
                            <?php } ?>
                        </div>
                        <div class="article_wide-details">
                            <?php if ($service->type == Service::TYPE_TYPICAL) { ?>
                                <div class="service_item-price"><?=$service->price?> руб.</div>
                                <?php if (!Yii::$app->user->isGuest) { ?>
                                    <a href="#" data-service="<?=$service->id?>" class="button send_order orderCreate">Оплатить</a>
                                <?php } ?>
                            <?php } ?>
                            <?php if ($service->type == Service::TYPE_CUSTOM) { ?>
                                <div class="service_item-price no-price">Узнать стоимость</div>
                                <?php if (!Yii::$app->user->isGuest) { ?>
                                    <a href="#" data-service="<?=$service->id?>" class="button send_order queryCreate">Запросить</a>
                                <?php } ?>
                            <?php } ?>
                        </div>
                        <?php if ($service->user) { ?>
                            <a href="<?=$service->user->getUrlPath()?>" class="author_box">
                                <div class="author_img">
                                    <img src="<?=$service->user->profile->getThumb('image','main')?>" alt="<?=$service->user->profile->halfname?>" />
                                </div>
                                <span>
                                    <?=$service->user->profile->getHalfname('<br>');?>
                                </span>
                            </a>
                        <?php } ?>
                    </article>
                <?php } ?>
            </section>
        <?php } else { ?>
            <p>Услуги не найдены</p>
        <?php } ?>
    </div>
</main>
<div class="modal" id="fail_order_modal">
    <div class="modal_content">
        <a href="#" class="modal_close">x</a>
        <div class="success_box">
            <div class="modal_title">Создание заказа</div>
            <p>При создании заказа возникла ошибка.</p>
            <div class="modal_buttons">
                <a href="#" class="button small close_modal">ОК</a>
            </div>
        </div>

    </div>
    <div class="modal_overlay"></div>
</div>
<?=\app\modules\queries\widgets\queries\QueriesWidget::widget();?>
<?php
$url_order = Url::toRoute(['/pages/orders/create']);
$js = <<<JS
    $('body').on('click','.custom_dropdown-choice', function(e){
        $('form.directions_search_box').submit();
    });
    $('body').on('click','.orderCreate', function(e){
        e.preventDefault();
        var service = $(this).data('service');
        var param = yii.getCsrfParam();
        var token = yii.getCsrfToken();
        $.ajax({
            type: 'POST',
            url: '{$url_order}',
            processData: true,
            dataType: 'json',
            data: {service:service,param:token},
            success: function(data){
                if (data.status == 'success') {
                    // в случае успеха редирект на страницу оплаты заказа
                    if (data.redirect_to) {
                        window.location.href = data.redirect_to;
                    } else {
                        $('#fail_order_modal .success_box p').html(data.message);
                        modalPos('#fail_order_modal');
                    }
                } else {
                    // в случае ошибки вывести сообщение
                    $('#fail_order_modal .success_box p').html(data.message);
                    modalPos('#fail_order_modal');
                }
            }
        });
    });
JS;
$this->registerJs($js);
*/
