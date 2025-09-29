<?php
/*
    @descr Мои запросы в Работе (Запросы Эксперта, ЛК)

    @var $model Class app\modules\pages\models\QueriesListIncom; текущая страница
    @var $items Array of app\modules\queries\models\Queries; массив запросов Эксперта
    @var $cur_status String текущий выбранный статус для показа (из get-параметров)

    @action pages/queries/incoming
*/

use app\modules\pages\models\QueriesView;
use app\modules\queries\models\Queries;
use yii\helpers\Url;

$status_class = [
        Queries::STATUS_NEW => ['color' => 'blue', 'icon' => 'published'],
        Queries::STATUS_AGREEMENT => ['color' => 'darkgreen', 'icon' => 'query_inwork'],
];

/* страница просмотре запроса */
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
                        <a href="<?= $model->getUrlPath(); ?>?status=new" class="button lk">Новые запросы</a>
                        <a href="<?= $model->getUrlPath(); ?>?status=agreement" class="button lk darkgreen">Обсуждение
                            условий</a>
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
                                <option value="<?= $model->getUrlPath(); ?>?status=new" <?= $cur_status == 'new' ? 'selected' : ''; ?>>
                                    Новые запросы
                                </option>
                                <option value="<?= $model->getUrlPath(); ?>?status=agreement" <?= $cur_status == 'agreement' ? 'selected' : ''; ?>>
                                    Обсуждение условий
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
                                <div class="lk-event-info people"><?= $item->user->profile->halfname; ?></div>
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
                            $types = ['new' => 'новых запросов', 'agreement' => 'запросов на этапе обсуждения условий']; ?>
                            У вас нет <?= (isset($types[$cur_status]) ? $types[$cur_status] : 'текущих запросов'); ?>.
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