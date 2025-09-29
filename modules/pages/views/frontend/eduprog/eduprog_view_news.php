<?php
/*
    @descr Список новостей программы ДПО в ЛК
    @var $model Class app\modules\pages\models\LKEduprogViewNews; текущая страница
    @action pages/eduprog/eduprog-view-news
*/

use app\modules\eduprog\models\News;
use app\modules\pages\models\LKEduprogViewNewsCreate;
use yii\helpers\Url;

$curr_status = Yii::$app->request->get('status', '');
$create_page = LKEduprogViewNewsCreate::find()->where(['model' => LKEduprogViewNewsCreate::class, 'visible' => 1])->one();

?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <?php if (!empty($eduprog_catalog)) { ?>
                    <div class="ip_cell w100">
                        <a href="<?= $eduprog_catalog->getUrlPath(); ?>" class="button-o back">Все программы</a>
                    </div>
                <?php } ?>

                <?= $this->render('_expert_eduprog_card', ['eduprog' => $eduprog, 'eduprog_catalog' => $eduprog_catalog]); ?>
                <?= $this->render('_expert_view_submenu', ['model' => $model, 'eduprog' => $eduprog]); ?>

                <!-- фильтр статусов события, в a href="" и option value="" ссылки на статусы -->
                <div class="lk_block desktop-visible">
                    <main class="lk_content lk_content-basic lk_switchers">
                        <a href="<?= Url::toRoute([$model->getUrlPath(), 'id' => $eduprog->id]); ?>"
                           class="button lk gray">Все новости</a>
                        <a href="<?= Url::toRoute([$model->getUrlPath(), 'id' => $eduprog->id, 'status' => News::STATUS_PUBLIC]); ?>"
                           class="button lk middleGray">Опубликованные</a>
                        <a href="<?= Url::toRoute([$model->getUrlPath(), 'id' => $eduprog->id, 'status' => News::STATUS_WAITING]); ?>"
                           class="button lk orange">Запланированные</a>
                    </main>
                </div>
                <div class="lk_block mobile-visible">
                    <div class="lk_block_header">
                        <div class="lk_switchers-select-wrapper mb0">
                            <div class="lk_switchers-text">Статус события</div>
                            <select class="lk_switchers-select">
                                <option value="<?= Url::toRoute([$model->getUrlPath(), 'id' => $eduprog->id]); ?>" <?= ($curr_status == '' ? ' selected=""' : ''); ?>>
                                    Все новости
                                </option>
                                <option value="<?= Url::toRoute([$model->getUrlPath(), 'id' => $eduprog->id, 'status' => News::STATUS_PUBLIC]); ?>" <?= ($curr_status == News::STATUS_PUBLIC ? ' selected=""' : ''); ?>>
                                    Опубликованные
                                </option>
                                <option value="<?= Url::toRoute([$model->getUrlPath(), 'id' => $eduprog->id, 'status' => News::STATUS_WAITING]); ?>" <?= ($curr_status == News::STATUS_WAITING ? ' selected=""' : ''); ?>>
                                    Запланированные
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <?php if (!empty($news)) {
                    /* иконки доступные - published moderation-fail moderation planned draft archive */
                    $colors_icons = [
                            News::STATUS_PUBLIC => ['color' => 'gray', 'icon' => 'published', 'name' => 'Отправлено'],
                            News::STATUS_WAITING => ['color' => 'orange', 'icon' => 'planned', 'name' => 'Запланировано'],
                    ];
                    foreach ($news as $news_item) { ?>
                        <div id="eduprog_news_<?= $news_item->id; ?>" class="lk_order_item">
                            <?php if ($create_page) { ?>
                                <a href="<?= Url::toRoute([$create_page->getUrlPath(), 'id' => $news_item->id]); ?>"
                                   class="lk_order_more <?= $colors_icons[$news_item->status]['color']; ?> lk_order_more-basic"><img
                                            src="img/nav_right-white.svg" alt=""></a>
                            <?php } ?>
                            <h4 class="lk-order-title"><?= $news_item->name; ?></h4>
                            <div class="lk-event-info-wrapper lk-event-info-wrapper1">
                                <!-- иконки published moderation-fail moderation planned draft archive -->
                                <div class="lk-event-info <?= $colors_icons[$news_item->status]['icon']; ?>"><?= $colors_icons[$news_item->status]['name']; ?></div>
                                <div class="lk-event-info date"><?= Yii::$app->formatter->asDatetime($news_item->public_date, 'd MMMM y H:mm'); ?></div>
                            </div>
                            <div class="lk-event-buttons">
                                <?php if ($create_page) { ?>
                                    <a href="<?= Url::toRoute([$create_page->getUrlPath(), 'id' => $news_item->id]); ?>"
                                       class="button-o small gray">Редактировать</a>
                                    <a href="<?= Url::toRoute([$create_page->getUrlPath(), 'copy' => $news_item->id]); ?>"
                                       class="button-o small gray">Создать копию</a>
                                <?php } ?>
                                <a href="#" data-origin="<?= $news_item->id; ?>"
                                   class="button-o small gray remove-news-js">Удалить</a>
                            </div>
                            <?php if ($create_page) { ?>
                                <a href="<?= Url::toRoute([$create_page->getUrlPath(), 'id' => $news_item->id]); ?>"
                                   class="<?= $colors_icons[$news_item->status]['color']; ?> lk_order_more-basic_mobile">Перейти<img
                                            src="img/nav_right-white.svg" alt=""></a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <p>Вы еще не добавили новости</p>
                <?php } ?>
            </div>
            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
        </div>
    </main>

    <div class="modal" id="success_event_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Удаление новости</div>
                <p>новость успешно удалена</p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
    <div class="modal" id="fail_service_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Ошибка удаления новости</div>
                <p>При удалении новости возникли ошибки. <?= \app\helpers\MainHelper::getHelpText(); ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>

<?php
$url_delete = Url::toRoute(['/pages/eduprog/delete-news']);
$js = <<<JS
    $('.remove-news-js').on('click', function(e) {
        e.preventDefault();
        var origin = $(this).data('origin');
        $.ajax({
            type: 'GET',
            url: '{$url_delete}',
            processData: true,
            dataType: 'json',
            data: {origin:origin},
            success: function(data){
                if (data.status == 'success') {
                    // удалить мероприятие
                    $('#eduprog_news_'+origin).remove();
                    $('#success_event_modal .success_box .modal_title').html('Удаление новости');
                    $('#success_event_modal .success_box p').html(data.message);
                    modalPos('#success_event_modal');
                } else {
                    // вывести ошибку
                    $('#fail_service_modal .success_box p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
        return false;
    });

    $('.lk_switchers-select').change(function() {
        document.location.href = $(this).val();
    });
JS;
$this->registerJs($js);
?>