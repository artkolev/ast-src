<?php

use app\modules\pages\models\QueriesView;
use app\modules\queries\models\Queries;
use yii\helpers\Url;

$colors = [
        Queries::STATUS_NEW => 'maroon',
        Queries::STATUS_EXECCANCEL => 'orange',
        Queries::STATUS_OFFER => 'green',
];

$textcolors = [
        Queries::STATUS_NEW => 'maroon',
        Queries::STATUS_AGREEMENT => 'orange',
        Queries::STATUS_OFFER => 'darkgreen',
];

$view_page = QueriesView::find()->where(['model' => QueriesView::class, 'visible' => 1])->one();
$view_url = (!empty($view_page) ? $view_page->getUrlPath() : false);
?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <div class="lk_block mb10">
                    <header class="lk_block_header">
                        <h1 class="lk_block_title-big"><?= $model->getNameForView(); ?></h1>
                        <?= $model->content; ?>
                    </header>
                </div>
                <div class="lk_block desktop-visible">
                    <main class="lk_content lk_content-basic">
                        <a href="<?= $model->getUrlPath(); ?>?status=offer" class="button lk darkgreen">Ждут предложений
                            (<?= $offer_count; ?>)</a>
                        <a href="<?= $model->getUrlPath(); ?>?status=expired" class="button lk maroon">Необработанные
                            (<?= $expired_count; ?>)</a>
                        <a href="<?= $model->getUrlPath(); ?>?status=canceled" class="button lk orange">Отмененные
                            экспертом (<?= $canceled_count; ?>)</a>
                        <a href="<?= $model->getUrlPath(); ?>" class="button lk gray">Все (<?= $queries_count; ?>)</a>
                    </main>
                </div>
                <div class="lk_block mobile-visible">
                    <div class="lk_block_header">
                        <div class="lk_switchers-select-wrapper mb0">
                            <div class="lk_switchers-text">Статус запроса</div>
                            <select class="lk_switchers-select">
                                <option value="<?= $model->getUrlPath(); ?>" <?= $_SERVER['REQUEST_URI'] == $model->getUrlPath() ? 'selected' : ''; ?>>
                                    Все (<?= $queries_count; ?>)
                                </option>
                                <option value="<?= $model->getUrlPath(); ?>?status=offer" <?= $_SERVER['REQUEST_URI'] == $model->getUrlPath() . '?status=offer' ? 'selected' : ''; ?>>
                                    Ждут предложений (<?= $offer_count; ?>)
                                </option>
                                <option value="<?= $model->getUrlPath(); ?>?status=expired" <?= $_SERVER['REQUEST_URI'] == $model->getUrlPath() . '?status=expired' ? 'selected' : ''; ?>>
                                    Необработанные (<?= $expired_count; ?>)
                                </option>
                                <option value="<?= $model->getUrlPath(); ?>?status=canceled" <?= $_SERVER['REQUEST_URI'] == $model->getUrlPath() . '?status=canceled' ? 'selected' : ''; ?>>
                                    Отмененные экспертом (<?= $canceled_count; ?>)
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <?php if (!empty($items)) { ?>
                    <?php foreach ($items as $item) { ?>
                        <div class="lk_order_item <?= $item->hasUnreadNotifications(Yii::$app->user->identity->userAR->id) ? 'has_notifications' : ''; ?>">
                            <a href="<?= ($view_url ? Url::toRoute([$view_url, 'id' => $item->id]) : ''); ?>"
                               class="lk_order_more <?= $colors[$item->status]; ?> lk_order_more-basic"><img
                                        src="/img/nav_right-white.svg" alt=">"/></a>
                            <div class="lk_order_item_info-basic">
                                <div class="grid-template-a">
                                    <div><h4>Запрос №:</h4>
                                        <p><?= $item->queryNum; ?></p></div>
                                    <div><h4>Отправлен:</h4>
                                        <p><?= Yii::$app->formatter->asDatetime($item->created_at, 'd.MM.y'); ?></p>
                                    </div>
                                </div>
                                <div class="grid-template-b">
                                    <div><h4>Статус:</h4>
                                        <p class="<?= $textcolors[$item->status]; ?>"><?= $item->statusName; ?></p>
                                    </div>
                                    <div><h4>Эксперт:</h4>
                                        <p><?= $item->executor->profile->halfname; ?></p></div>
                                </div>
                                <div class="grid-template-c">
                                    <div><h4>Услуга:</h4>
                                        <p><?= $item->service_name; ?></p></div>
                                </div>
                            </div>
                            <a href="<?= ($view_url ? Url::toRoute([$view_url, 'id' => $item->id]) : ''); ?>"
                               class="<?= $colors[$item->status]; ?> lk_order_more-basic_mobile">Перейти<img
                                        src="/img/nav_right-white.svg" alt=">"/></a>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="lk_block">
                        <main class="lk_content">
                            <?php $get = Yii::$app->request->get();
                            $types = ['expired' => 'необработанных запросов', 'canceled' => 'отмененных запросов', 'offer' => 'запросов, ожидающих предложений']; ?>
                            У вас
                            нет <?= (isset($types[$get['status']]) ? $types[$get['status']] : 'текущих запросов'); ?>.
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