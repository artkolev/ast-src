<?php
/*
    @descr Список услуг (ЛК)

    @var $model Class app\modules\pages\models\ServiceList; текущая страница
    @var $items Array of app\modules\service\models\Service; массив услуг пользователя
    @var $curr_status String текущий выбранный статус для показа (из get-параметров)

    @action pages/servicerun/list
*/

use app\modules\pages\models\ServiceAdd;
use app\modules\pages\models\ServiceAddIndi;
use app\modules\pages\models\ServiceEdit;
use app\modules\pages\models\SupportPage;
use app\modules\service\models\Service;
use yii\helpers\Url;

/**
 * @var string $curr_status
 * @var Service[] $items
 */

// страница редактирования услуги
$service_edit = ServiceEdit::find()->where(['model' => ServiceEdit::class, 'visible' => 1])->one();
$service_new_typical = ServiceAdd::find()->where(['model' => ServiceAdd::class, 'visible' => 1])->one();
$service_new_indi = ServiceAddIndi::find()->where(['model' => ServiceAddIndi::class, 'visible' => 1])->one();

// страница поддержки
$support_page = SupportPage::find()->where(['model' => SupportPage::class, 'visible' => 1])->one();

?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <div class="lk_block">
                    <header class="lk_block_header">
                        <h1 class="lk_block_title-big mb0"><?= $model->getNameForView(); ?></h1>
                        <?php if (!empty($model->content)) { ?>
                            <div class="mt20"><?= $model->content; ?></div>
                        <?php } ?>
                    </header>
                </div>
                <div class="lk_block desktop-visible">
                    <main class="lk_content lk_content-basic lk_switchers">
                        <a href="<?= Url::toRoute([$model->getUrlPath()]); ?>" class="button lk gray">Все</a>
                        <a href="<?= Url::toRoute([$model->getUrlPath(), 'status' => 'active']); ?>" class="button lk">Опубликовано</a>
                        <a href="<?= Url::toRoute([$model->getUrlPath(), 'status' => 'moderation']); ?>"
                           class="button lk orange">На модерации</a>
                        <a href="<?= Url::toRoute([$model->getUrlPath(), 'status' => 'draft']); ?>"
                           class="button lk lightGray">Черновики</a>
                        <a href="<?= Url::toRoute([$model->getUrlPath(), 'status' => 'need_edit']); ?>"
                           class="button lk middleGray">Внести изменения</a>
                        <a href="<?= Url::toRoute([$model->getUrlPath(), 'status' => 'not_active']); ?>"
                           class="button lk lightGray-blue">Снято с продажи</a>
                    </main>
                </div>
                <div class="lk_block mobile-visible">
                    <div class="lk_block_header">
                        <div class="lk_switchers-select-wrapper mb0">
                            <div class="lk_switchers-text">Статус услуги</div>
                            <select class="lk_switchers-select">
                                <option value="<?= Url::toRoute([$model->getUrlPath()]); ?>" <?= ($curr_status == '' ? 'selected="selected"' : ''); ?>>
                                    Все
                                </option>
                                <option value="<?= Url::toRoute([$model->getUrlPath(), 'status' => 'active']); ?>" <?= ($curr_status == 'active' ? 'selected="selected"' : ''); ?>>
                                    Опубликовано
                                </option>
                                <option value="<?= Url::toRoute([$model->getUrlPath(), 'status' => 'moderation']); ?>" <?= ($curr_status == 'moderation' ? 'selected="selected"' : ''); ?>>
                                    На модерации
                                </option>
                                <option value="<?= Url::toRoute([$model->getUrlPath(), 'status' => 'draft']); ?>" <?= ($curr_status == 'draft' ? 'selected="selected"' : ''); ?>>
                                    Черновики
                                </option>
                                <option value="<?= Url::toRoute([$model->getUrlPath(), 'status' => 'need_edit']); ?>" <?= ($curr_status == 'need_edit' ? 'selected="selected"' : ''); ?>>
                                    Внести изменения
                                </option>
                                <option value="<?= Url::toRoute([$model->getUrlPath(), 'status' => 'not_active']); ?>" <?= ($curr_status == 'not_active' ? 'selected="selected"' : ''); ?>>
                                    Снято с продажи
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <?php if (!empty($items)) {
                    $status_names = [
                            'withdrawn' => 'Снято с продажи',
                    ];
                    $colors = [
                            'published' => 'blue',
                            'withdrawn' => 'lightGray-blue',
                            'draft' => 'lightGray',
                            'moderation' => '',
                            'moderation-fail' => 'lightGray',
                    ];

                    foreach ($items as $item) {
                        switch ($item->kind) {
                            case Service::KIND_ONLINE:
                                $place = $item->kindName . ', ' . $item->platform;
                                $place_icon = 'location-online';
                                break;
                            case Service::KIND_OFFLINE:
                                $place = $item->kindName . ', ' . $item->city->name . ', ' . $item->place;
                                $place_icon = 'location-offline';
                                break;
                            case Service::KIND_HYBRID:
                                $place = $item->kindName;
                                $place_icon = 'location-gibrid';
                                break;
                        }

                        switch ($item->status) {
                            case Service::STATUS_FIRST_MODERATE:
                            case Service::STATUS_WAIT_MODERATE:
                                $statusClass = 'moderation';
                                break;
                            case Service::STATUS_WAIT_EDIT_FIRST_MODERATE:
                            case Service::STATUS_WAIT_EDIT_MODERATE:
                                $statusClass = 'moderation-fail';
                                break;
                            case Service::STATUS_PUBLIC:
                                $statusClass = $item->visible ? 'published' : 'withdrawn';
                                break;
                            case Service::STATUS_ARCHIVE:
                                $statusClass = 'archive';
                                break;
                            default:
                                $statusClass = 'draft';
                                break;
                        }

                        $service_edit_url = $service_edit;
                        if ($item->status == Service::STATUS_DRAFT) {
                            $service_edit_url = $item->type == Service::TYPE_TYPICAL ? $service_new_typical : $service_new_indi;
                        }

                        $statusName = $statusClass == 'withdrawn' ? $status_names[$statusClass] : Service::STATUS_NAMES[$item->status];
                        ?>
                        <div id="service_<?= $item->id; ?>" class="lk_order_item">
                            <?php if (!in_array($item->status, Service::NOT_EDIT_STATUS)) { ?>
                                <a href="<?= Url::to([$service_edit_url->getUrlPath(), 'id' => $item->id]); ?>"
                                   class="colored_link lk_order_more <?= $colors[$statusClass]; ?> lk_order_more-basic"><img
                                            src="/img/nav_right-white.svg" alt=""/></a>
                            <?php } ?>
                            <h4 class="lk-order-title"><?= $item->name; ?></h4>

                            <div class="lk-event-info-wrapper">
                                <div class="lk-event-info service_name <?= $statusClass; ?>"><?= $statusName; ?></div>
                                <div class="lk-event-info price">
                                    <?php if ($item->type == Service::TYPE_TYPICAL) { ?>
                                        <?= number_format($item->price, 0, '.', '&nbsp;'); ?> ₽
                                    <?php } else { ?>
                                        По запросу
                                    <?php } ?>
                                </div>
                                <div class="lk-event-info <?= $place_icon; ?>"><?= $place; ?></div>
                                <?php if ($item->serviceType) { ?>
                                    <div class="lk-event-info learn"><?= $item->serviceType->name; ?></div>
                                <?php } ?>
                            </div>
                            <?php if (in_array($item->status, [Service::STATUS_WAIT_EDIT_FIRST_MODERATE, Service::STATUS_WAIT_EDIT_MODERATE]) && !empty($item->currentModeration->reason)) { ?>
                                <div class="lk-event-info-text"><?= $item->currentModeration->reason; ?></div>
                            <?php } ?>
                            <?php if (!in_array($item->status, Service::NOT_EDIT_STATUS)) { ?>
                                <div class="lk-event-buttons">
                                    <a target="_blank"
                                       href="<?= $item->getUrlPath(); ?>" <?= ($statusClass == 'published' ? '' : 'style="display: none;"'); ?>
                                       class="site_url button-o small">Смотреть на сайте</a>
                                    <a href="<?= Url::to([$service_edit_url->getUrlPath(), 'id' => $item->id]); ?>"
                                       class="button-o small">Редактировать</a>
                                    <?php if (in_array($statusClass, ['published', 'withdrawn'])) { ?>
                                        <a href="#" class="button-o small switch" data-switch="visible"
                                           data-service="<?= $item->id; ?>"><?= ($item->visible ? 'Снять с продажи' : 'Вернуть в продажу'); ?></a>
                                    <?php } ?>
                                    <?php if (in_array($item->status, [Service::STATUS_WAIT_EDIT_MODERATE, Service::STATUS_WAIT_EDIT_FIRST_MODERATE]) && $support_page) { ?>
                                        <a target="_blank" href="<?= $support_page->getUrlPath(); ?>"
                                           class="button-o small">Написать в поддержку</a>
                                    <?php } ?>
                                    <a href="#" class="button-o small confirm-delete" data-switch="archive"
                                       data-service="<?= $item->id; ?>">Удалить</a>
                                </div>
                            <?php } ?>

                            <!--ссылка для мобилок-->
                            <?php if (!in_array($item->status, Service::NOT_EDIT_STATUS)) { ?>
                                <a href="<?= Url::to([$service_edit_url->getUrlPath(), 'id' => $item->id]); ?>"
                                   class="colored_link <?= $colors[$statusClass]; ?> lk_order_more-basic_mobile">Перейти<img
                                            src="/img/nav_right-white.svg" alt=""/></a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="lk_block">
                        <main class="lk_content">
                            <?php
                            $types = ['active' => 'опубликованных услуг', 'not_active' => 'снятых с продажи услуг', 'moderation' => 'услуг на модерации', 'need_edit' => 'услуг, требующих корректировки']; ?>
                            У вас нет <?= (isset($types[$curr_status]) ? $types[$curr_status] : 'услуг'); ?>.
                        </main>
                    </div>
                <?php } ?>

            </div>
            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
        </div>
    </main>
    <div class="modal" id="fail_service_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Ошибка обновления услуги</div>
                <p>При изменении статуса услуги возникли ошибки. <?= \app\helpers\MainHelper::getHelpText(); ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
    <div class="modal" id="confirm_delete_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Услуга будет удалена</div>
                <div class="modal_buttons">
                    <a href="#" class="button small service-delete">Подтверждаю</a>
                    <a href="#" class="button small close_modal">Отмена</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
<?php
$url = Url::toRoute(['/pages/servicerun/switchfield']);
$js = <<<JS
    $('.lk-event-buttons .switch').on('click', function(e) {
        e.preventDefault();
        var attribute = $(this).data('switch');
        var item = $(this).data('service');
        sendSwitch(attribute, item);
        
        return false;
    });

    $('#confirm_delete_modal .service-delete').on('click', function(e) {
        e.preventDefault();
        var attribute = $(this).data('switch');
        var item = $(this).data('service');
        
        closeModal('#confirm_delete_modal');
        sendSwitch(attribute, item);
        
        return false;
    });
    
    $('.lk-event-buttons .confirm-delete').on('click', function(e) {
        e.preventDefault();
        
        let deleteButton = $('#confirm_delete_modal .service-delete');
      
        deleteButton.data('switch', $(this).data('switch'));
        deleteButton.data('service', $(this).data('service'));

        modalPos('#confirm_delete_modal');
        return false;
    });

    function sendSwitch(attribute, item)
    {
        $.ajax({
            type: 'GET',
            url: '{$url}',
            processData: true,
            dataType: 'json',
            data: {attribute:attribute,id:item},
            success: function(data){
                if (data.status == 'success') {
                    // если attribute == archive, то удалить запись
                    if (attribute == 'archive') {
                        $('#service_'+item).remove();
                    }
                    // если аттрибут == visible то сменить класс и переименовать кнопку
                    if (attribute == 'visible') {
                        let serviceName = $('#service_'+item+' .service_name');
                        
                        if (data.visible == 1) {
                            // активировать
                            $('#service_'+item+' a.colored_link').each(function(){
                                $(this).removeClass('lightGray-blue').addClass('blue');
                            });
                            $('#service_'+item+' .switch[data-switch=visible]').html('Снять с продажи');
                            $('#service_'+item+' .site_url').css('display','inline-block');
                            
                            serviceName.text('Опубликована');
                            serviceName.removeClass('withdrawn');
                            serviceName.addClass('published');
                        } else {
                            // деактивировать
                            $('#service_'+item+' a.colored_link').each(function(){
                                $(this).removeClass('blue').addClass('lightGray-blue');
                            });
                            $('#service_'+item+' .switch[data-switch=visible]').html('Вернуть в продажу');
                            $('#service_'+item+' .site_url').css('display','none');
                           
                            serviceName.text('Снято с продажи');
                            serviceName.removeClass('published');
                            serviceName.addClass('withdrawn');
                        }
                    }
                } else {
                    // вывести ошибку
                    $('#fail_service_modal .success_box p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
    }

    $('.lk_switchers-select').change(function() {
        document.location.href = $(this).val();
    });
JS;
$this->registerJs($js);
?>