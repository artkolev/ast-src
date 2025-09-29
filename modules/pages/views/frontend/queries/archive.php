<?php
/*
    @descr Архив запросов (Запросы клиента, ЛК)

    @var $model Class app\modules\pages\models\QueriesArchive; текущая страница
    @var $items Array of app\modules\queries\models\Queries; массив запросов в архивных статусах, где пользователь либо заказчик, либо исполнитель
    @var $cur_status String текущий выбранный статус для показа (из get-параметров)

    @action pages/queries/archive
*/

use app\modules\pages\models\QueriesView;
use app\modules\queries\models\Queries;
use yii\helpers\Url;

$status_class = [
        Queries::STATUS_EXECCANCEL => ['color' => 'maroon', 'icon' => 'moderation-fail'],
        Queries::STATUS_USERCANCEL => ['color' => 'maroon', 'icon' => 'moderation-fail'],
        Queries::STATUS_DONE => ['color' => 'blue', 'icon' => 'published'],
        Queries::STATUS_CLOSE => ['color' => 'darkgreen', 'icon' => 'end-order'],
];

/* страница просмотра запроса */
$view_page = QueriesView::find()->where(['model' => QueriesView::class, 'visible' => 1])->one();
$view_url = (!empty($view_page) ? $view_page->getUrlPath() : false);
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
                        <a href="<?= $model->getUrlPath(); ?>" class="button lk gray">Все</a>
                        <a href="<?= $model->getUrlPath(); ?>?status=cancel" class="button lk maroon">Отклонено</a>
                        <a href="<?= $model->getUrlPath(); ?>?status=done" class="button lk">Согласовано</a>
                        <a href="<?= $model->getUrlPath(); ?>?status=close" class="button darkgreen lk">Закрыто</a>
                    </main>
                </div>

                <div class="lk_block mobile-visible">
                    <div class="lk_block_header">
                        <div class="lk_switchers-select-wrapper mb0">
                            <div class="lk_switchers-text">Статус запроса</div>
                            <select class="lk_switchers-select">
                                <option value="<?= $model->getUrlPath(); ?>" <?= $cur_status == '' ? 'selected' : ''; ?>>
                                    Все
                                </option>
                                <option value="<?= $model->getUrlPath(); ?>?status=cancel" <?= $cur_status == 'cancel' ? 'selected' : ''; ?>>
                                    Отклонено
                                </option>
                                <option value="<?= $model->getUrlPath(); ?>?status=done" <?= $cur_status == 'done' ? 'selected' : ''; ?>>
                                    Согласовано
                                </option>
                                <option value="<?= $model->getUrlPath(); ?>?status=close" <?= $cur_status == 'close' ? 'selected' : ''; ?>>
                                    Закрыто
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <?php if (!empty($items)) { ?>
                    <?php foreach ($items as $item) { ?>

                        <div class="lk_order_item <?= $item->hasUnreadNotifications(Yii::$app->user->identity->userAR->id) ? 'has_notifications' : ''; ?>">
                            <a href="<?= ($view_url ? Url::toRoute([$view_url, 'id' => $item->id]) : ''); ?>"
                               class="lk_order_more <?= $status_class[$item->status]['color']; ?> lk_order_more-basic"><img
                                        src="/img/nav_right-white.svg" alt=">"/></a>
                            <h4 class="lk-order-title"><?= $item->service_name; ?></h4>
                            <div class="lk-event-info-wrapper">
                                <div class="lk-event-info <?= $status_class[$item->status]['icon']; ?>"><?= $item->statusName; ?></div>
                                <div class="lk-event-info number"><?= $item->queryNum; ?></div>
                                <div class="lk-event-info date"><?= Yii::$app->formatter->asDatetime($item->created_at, 'dd.MM.y'); ?></div>
                                <div class="lk-event-info people"><?= $item->executor->profile->halfname; ?></div>
                            </div>
                            <a href="<?= ($view_url ? Url::toRoute([$view_url, 'id' => $item->id]) : ''); ?>"
                               class="<?= $status_class[$item->status]['color']; ?> lk_order_more-basic_mobile">Перейти<img
                                        src="/img/nav_right-white.svg" alt=">"/></a>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="lk_block">
                        <main class="lk_content">
                            <?php
                            $types = ['cancel' => 'отклонённых запросов', 'done' => 'согласованных запросов', 'close' => 'закрытых запросов']; ?>
                            У вас
                            нет <?= (isset($types[$cur_status]) ? $types[$cur_status] : 'завершенных запросов'); ?>.
                        </main>
                    </div>
                <?php } ?>
            </div>
            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
        </div>
    </main>
<?php
$js = <<<JS
	$('.lk_switchers-select').change(function() {
        document.location.href = $(this).val();
    });
JS;
$this->registerJs($js);
?>