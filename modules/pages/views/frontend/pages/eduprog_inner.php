<?php

use app\helpers\MainHelper;
use app\modules\eduprog\models\Eduprog;
use app\modules\users\models\UserAR;
use yii\helpers\Html;
use yii\helpers\Url;

// подготовить данные для вывода в зависимости от флага предпросмотра.
if ($preview) {
    // для статуса Need_edit  - при переделке статусов заменить на "ждет доработок на первичной модерации"
    // делаем для черновика вид, как будто он уже опубликован.
    // по хорошему нужно найти способ запретить сохранение данных этого экземпляра модели, чтобы никакими побочными эффектами параметры для предпросмотра не повлияли на состояние программы в базе.
    if (in_array($model->status, [Eduprog::STATUS_NEW, Eduprog::STATUS_NEED_EDIT])) {
        $model->status = Eduprog::STATUS_PUBLIC;
        $model->registration_open = $model->canSale();
    }

    $can_buy_tickets = false;

    $moderation = $model->currentModeration;

    if (!empty($moderation)) {
        $model->name = $moderation->name;
        $model->hours = $moderation->hours;
        $model->shedule_text = $moderation->shedule_text;
        $model->address = $moderation->address;
        $model->place = $moderation->place;
        $model->rules = $moderation->rules;
        $model->content = $moderation->content;
        $model->video_title = $moderation->video_title;
        $model->structure = $moderation->structure;
        $model->learn = $moderation->learn;
        $model->cost_text = $moderation->cost_text;
        $model->suits_for = $moderation->suits_for;
        $model->block_title = $moderation->block_title;
        $model->block_text = $moderation->block_text;
        $model->works_title = $moderation->works_title;
        $model->works_text = $moderation->works_text;
        $model->format = $moderation->format;
        $model->video = $moderation->video;
        $model->lectors = $moderation->lectors;

        $category_name = $moderation->category?->name;
        $city_name = $moderation->city?->name;
        $keywords = $moderation->keywords;
        $tags_ids = [];
        $tags = [];
        if (!empty($moderation->tags)) {
            foreach ($moderation->tags as $tag_id_name) {
                if ((int)$tag_id_name > 0) {
                    $tags_ids[] = (int)$tag_id_name;
                } else {
                    $new_tag = new \app\modules\reference\models\Eventstag();
                    $new_tag->name = $tag_id_name;
                    $tags[] = $new_tag;
                }
            }
            $tags_exists = \app\modules\reference\models\Eventstag::find()->where(['IN', 'id', $tags_ids])->all();
            $tags = array_merge($tags, $tags_exists);
        }

        $report_items = [];
        foreach ($model->report as $report_item) {
            if (!in_array($report_item->id, $moderation->remove_report)) {
                $report_items[] = $report_item;
            }
        }
        $moder_report_items = $moderation->report;
        $report_items = array_merge($report_items, $moder_report_items);


    } else {
        $category_name = $model->category?->name;
        $city_name = $model->city?->name;
        $tags = $model->tags;
        $keywords = $model->keywords;
        $report_items = $model->report;
    }

    if ($moderation && $moderation->image) {
        $main_image = $moderation->getThumb('image', 'page_inner');
    } else {
        $main_image = $model->getThumb('image', 'page_inner');
    }


} else {

    $can_buy_tickets = true;

    $main_image = $model->getThumb('image', 'page_inner');
    $category_name = $model->category?->name;
    $city_name = $model->city?->name;
    $tags = $model->tags;
    $keywords = $model->keywords;
    $report_items = $model->report;

}
?>

    <main class="sec content_sec section-event-page">
        <!-- если программа завершена (soldout) - добавить класс soldout -->
        <!-- если программа академии - добавить класс academy -->
        <!-- если программа корпоративная, то не смотря на то, что регистрация на неё всегда закрыта, класс НЕ добавляем -->
        <div class="section-event-page-preview <?= (($model->statusFull == 'archive' || $model->registration_open == false) && !$model->is_corporative && !$model->is_waiting_list) ? 'soldout' : ''; ?>">
            <?= $this->render('_shields', ['mobile' => true, 'items' => $model->shieldsVisible]); ?>

            <div class="section-event-page-preview-img mobile-visible">
                <img src="<?= $main_image; ?>" alt="<?= htmlspecialchars($model->name); ?>" loading="lazy">
                <?= $model->age ? '<div class="event-side-age-limit mobile-visible">' . $model->age->name . '</div>' : ''; ?>
                <!-- если soldout - выводить лейбл, но только не для корпоративных программ -->
                <div class="soldout-label <?= (($model->registration_open == false) && !$model->is_corporative && !$model->is_waiting_list) ? 'closed' : ''; ?>"><?= (($model->registration_open == false) && !$model->is_corporative && !$model->is_waiting_list) ? 'Продажа закрыта' : 'Программа завершена'; ?></div>
            </div>

            <div class="section-event-page-preview-short">
                <?php $date_text = $model->getEduprogDateForView();
                if (!$model->is_waiting_list) {
                    if (!empty($date_text)) { ?>
                        <div class="event-page-short date">
                            <?= $date_text; ?>
                        </div>
                    <?php } ?>
                    <?php if (!empty($model->shedule_text)) { ?>
                        <div class="event-page-short time"><?= $model->shedule_text; ?></div>
                    <?php } ?>
                    <?php
                } else { ?>
                    <div class="event-page-short">
                    </div>
                    <?php
                }
                // для корпоративных программ своя кнопка. При клике должна открываться модалка с формой Битрикс24
                if ($model->is_corporative) { ?>
                    <a href="#corporative_modal" class="buy-ticket-pinned corporative_show">
                        <div class="buy-ticket-pinned-btn">Заказать для компании</div>
                    </a>
                <?php } else {
                    // и только если программа НЕ корпоративная, то проверяем формы продажи билетов
                    // в ссылку указывать якорь до блока с покупкой билетов
                    $badge = $model->getLowestPrice();
                    if ($badge['form_id'] || $model->is_waiting_list) { ?>
                        <a href="#tickets_box" class="buy-ticket-pinned anchor">
                            <div class="buy-ticket-pinned-btn">Стать участником</div>
                        </a>
                    <?php } ?>
                <?php } ?>
            </div>
            <div class="section-event-page-preview-wrapper">
                <?= $this->render('_shields', ['desktop' => true, 'items' => $model->shieldsVisible]); ?>
                <!-- десктоп обложка -->
                <div class="section-event-page-preview-img desktop-visible">
                    <img src="<?= $main_image; ?>" alt="<?= htmlspecialchars($model->name); ?>" loading="lazy">
                    <div class="soldout-label <?= (($model->registration_open == false) && !$model->is_corporative && !$model->is_waiting_list) ? 'closed' : ''; ?>"><?= (($model->registration_open == false) && !$model->is_corporative && !$model->is_waiting_list) ? 'Продажа закрыта' : 'Программа завершена'; ?></div>
                </div>

                <div class="section-event-page-preview-info">
                    <?php if ($category_name) { ?>
                        <div class="section-event-page-type"><?= $category_name; ?> (<?= $model->hours; ?> ч)</div>
                    <?php } ?>
                    <h1 class="section-event-page-title"><?= $model->getNameForView(); ?></h1>

                    <div class="blog-expert-list mobile-visible">
                        <a href="<?= $model->author->getUrlPath(); ?>" class="blog-expert-element">
                            <div class="blog-expert-element-img">
                                <img src="<?= $model->author->profile->getThumb('image', 'main'); ?>"
                                     alt="<?= $model->author->profile->halfname; ?>" loading="lazy">
                            </div>
                            <div class="blog-expert-element-info">
                                <div class="blog-expert-element-name"><?= $model->author->profile->halfname; ?></div>
                            </div>
                        </a>
                    </div>
                    <div class="section-event-page-tags">
                        <a href="<?= (!empty($eduprog_catalog) ? Url::toRoute([$eduprog_catalog?->getUrlPath(), 'formats[]' => $model->format]) : ""); ?>"
                           class="section-event-page-tag"><?= $model->formatName; ?></a>
                    </div>
                    <?php if ($model->format != Eduprog::FORMAT_ONLINE) { ?>
                        <div class="event-side-info-wrapper mobile-visible">
                            <div class="event-side-info address"><?= $model->getPrettyPlaceForView('<br>', $city_name); ?></div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="block-event-page">
            <div class="container wide">
                <?= $this->render('_social_box_lenta', ['model' => $model]); ?>
                <div class="blog-page-content">
                    <?= $this->render('_social_box_lenta_mobile', ['model' => $model, 'text' => 'Поделиться']); ?>
                    <div class="blog-page-poster-wrapper">
                        <div class="blog-right-column desktop-visible">
                            <div class="blog-expert-list desktop-visible">
                                <a href="<?= $model->author->getUrlPath(); ?>" class="blog-expert-element">
                                    <div class="blog-expert-element-img">
                                        <img src="<?= $model->author->profile->getThumb('image', 'main'); ?>"
                                             alt="<?= $model->author->profile->halfname; ?>" loading="lazy">
                                    </div>
                                    <div class="blog-expert-element-info">
                                        <div class="blog-expert-element-name"><?= $model->author->profile->halfname; ?></div>
                                    </div>
                                </a>
                            </div>
                            <?php if ($model->format != Eduprog::FORMAT_ONLINE) { ?>
                                <div class="event-side-info-wrapper desktop-visible">
                                    <div class="event-side-info address"><?= $model->getPrettyPlaceForView('<br>', $city_name); ?></div>
                                </div>
                            <?php } ?>

                            <?php if ($tags) { ?>
                                <div class="expert_item-tags desktop-visible">
                                    <?php foreach ($tags as $tag) { ?>
                                        <?= Html::a('<b class="tag-hovered">' . mb_strtolower($tag->name, 'UTF-8') . '</b><span>' . mb_strtolower($tag->name, 'UTF-8') . '</span>', (!empty($eduprog_catalog) ? Url::toRoute([$eduprog_catalog?->getUrlPath(), 'tag' => $tag->name]) : false), ['class' => 'tag', 'data-tag_id' => $tag->id, 'data-tag_name' => $tag->name]); ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?php if ($keywords) { ?>
                                <div class="expert_item-tags desktop-visible">
                                    <?php foreach ($keywords as $keyword) { ?>
                                        <?= Html::a('<b class="tag-hovered">' . mb_strtolower($keyword->name, 'UTF-8') . '</b><span>' . mb_strtolower($keyword->name, 'UTF-8') . '</span>', (!empty($eduprog_catalog) ? Url::toRoute([$eduprog_catalog?->getUrlPath(), 'keyword' => $keyword->name]) : false), ['class' => 'tag', 'data-tag_id' => $keyword->id, 'data-tag_name' => $keyword->name]); ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?= $model->age ? '<div class="event-side-age-limit desktop-visible">' . $model->age->name . '</div>' : ''; ?>

                            <!-- задать вопрос -->
                            <div class="sidebar-ask-question-block">
                                <div class="sidebar-ask-question-block-title">Остались вопросы?</div>
                                <div class="sidebar-ask-question-block-text">Получите бесплатную консультацию</div>
                                <div class="sidebar-ask-question-block-manager">
                                    <div class="sidebar-ask-question-block-manager-img">
                                        <img src="/img/expert100.png" alt="">
                                    </div>
                                    <div class="sidebar-ask-question-block-manager-info">
                                        <div class="sidebar-ask-question-block-manager-name">Евгения</div>
                                        <div class="sidebar-ask-question-block-manager-job">Менеджер по сопровождению
                                            программ ДПО
                                        </div>
                                    </div>
                                </div>
                                <a href="#ask_modal" class="button medium mb0">Задать вопрос</a>
                            </div>
                        </div>
                    </div>
                    <div class="blog-page-text-wrapper">
                        <div class="blog-page-text">
                            <div class="blog-page-steps-title">О программе</div>
                            <?= $model->content; ?>
                            <?php
                            // отсеить скрытые
                            $video = MainHelper::cleanInvisibleMultifield($model->video);
                            if (!empty($video)) {
                                if (!empty($model->video_title)) { ?>
                                    <div class="blog-page-steps-title"><?= $model->video_title; ?></div>
                                <?php } ?>
                                <?php if (count($video) == 1) {
                                    // если видео всего одно
                                    $video_data = array_pop($video);
                                    if ($video_data['link']) { ?>
                                        <div class="youtube_preview">
                                            <?php $image = $model->getThumb('videoimage', 'big', false, $video_data['image'][0]);
                                            if (empty($image) && $preview && $moderation) {
                                                $image = $moderation->getThumb('videoimage', 'big', false, $video_data['image'][0]);
                                            }
                                            if ($image) { ?>
                                                <div class="youtube_preview">
                                                    <?= MainHelper::getMultiEmbededAddress($video_data['link'], image_url: $image, image_name: $video_data['name']); ?>
                                                </div>
                                            <?php } else { ?>
                                                <div class="youtube_preview">
                                                    <?= MainHelper::getMultiEmbededAddress($video_data['link']); ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <?php $i = 1; ?>
                                    <div class="eventpage_article-info masonry-block">
                                        <?php foreach ($video as $video_data) { ?>
                                            <div class="masonry-item">
                                                <?php $image = $model->getThumb('videoimage', 'main', false, $video_data['image'][0]);
                                                if (empty($image) && $preview && $moderation) {
                                                    $image = $moderation->getThumb('videoimage', 'big', false, $video_data['image'][0]);
                                                }
                                                if ($image) { ?>
                                                    <div class="youtube_preview">
                                                        <?= MainHelper::getMultiEmbededAddress($video_data['link'], fancybox: 'video_gallery', image_url: $image, image_name: $video_data['name']); ?>
                                                    </div>
                                                <?php } else { ?>
                                                    <div class="youtube_preview">
                                                        <?= MainHelper::getMultiEmbededAddress($video_data['link'], fancybox: 'video_gallery'); ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <?php $i++;
                                        } ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                            <!-- задать вопрос -->
                            <div class="ask-question-block">
                                <div class="ask-question-block-title">Напишите нам</div>
                                <div class="ask-question-block-text">Если вы хотите узнать больше о&nbsp;программе,
                                    условиях обучения или оплаты, оставьте заявку&nbsp;—&nbsp;и мы перезвоним
                                </div>
                                <a href="#ask_modal" class="button medium mb0">Задать вопрос</a>
                            </div>
                            <?php if (!empty($model->learn)) { ?>
                                <div class="event-rules-block">
                                    <div class="event-rules-title">Чему вы научитесь?</div>
                                    <div class="event-rules-text"><?= $model->learn; ?></div>
                                </div>
                            <?php } ?>
                            <?php if (!empty($report_items)) { ?>
                                <div class="eventpage_article-info masonry-block">
                                    <?php foreach ($report_items as $key => $image) { ?>
                                        <a href="<?= $image->src; ?>" data-fancybox="gallery" class="masonry-item"><img
                                                    src="<?= $image->keeper->getThumb('report', 'main', false, $image->id); ?>"
                                                    alt="<?= str_replace('"', '&quot;', $image->name); ?>"
                                                    loading="lazy"/></a>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?php if (!empty($model->works_text)) { ?>
                                <div class="event-rules-block">
                                    <div class="event-rules-title"><?= (!empty($model->works_title) ? $model->works_title : 'Сферы применения'); ?></div>
                                    <div class="event-rules-text">
                                        <?= $model->works_text; ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (!empty($model->cost_text)) { ?>
                                <div class="service-special-block">
                                    <div class="service-special-title">Стоимость обучения</div>
                                    <div class="service-special-text"><?= $model->cost_text; ?></div>
                                </div>
                            <?php } ?>
                            <?php $structure = MainHelper::cleanInvisibleMultifield($model->structure);
                            if (!empty($structure)) { ?>
                                <div class="blog-page-steps-title">Структура программы</div>
                                <div class="accordion_box faq_box">
                                    <?php foreach ($structure as $item) { ?>
                                        <div class="accordion_item">
                                            <h5 class="accordion_title"><?= $item['name']; ?></h5>
                                            <div class="accordion_desc"><?= $item['content']; ?></div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?php $lectors = MainHelper::cleanInvisibleMultifield($model->lectors);
                            if (!empty($lectors)) { ?>
                                <div class="blog-page-steps-title">Информация о ведущих</div>
                                <div class="speakers-block">
                                    <div class="speakers-list">
                                        <?php foreach ($lectors as $item) {
                                            if (!empty($item['user_id'])) {
                                                // пробуем найти пользователя
                                                $lector = UserAR::find()->andWhere(['id' => $item['user_id']])->visible(['expert', 'exporg'])->one();
                                                if (empty($lector)) {
                                                    continue;
                                                } ?>
                                                <a href="<?= $lector->getUrlPath(); ?>" target="_blank"
                                                   class="speaker-element">
                                                    <object>
                                                        <a href="<?= $lector->getUrlPath(); ?>" target="_blank"
                                                           class="speaker-element-img">
                                                            <img src="<?= $lector->profile->getThumb('image', 'profile'); ?>"
                                                                 alt="<?= htmlspecialchars($lector->profile->halfname); ?>">
                                                        </a>
                                                        <a href="<?= $lector->getUrlPath(); ?>" target="_blank"
                                                           class="speaker-element-name"><?= $lector->profile->halfname; ?></a>
                                                        <div class="speaker-element-text"><?= $lector->profile->about_myself; ?></div>
                                                        <?php if (!empty($item['video_link'])) { ?>
                                                            <a href="<?= $item['video_link']; ?>"
                                                               class="speaker-element-video-invite-link" data-fancybox>Приглашение</a>
                                                        <?php } ?>
                                                    </object>
                                                </a>
                                            <?php } else { ?>
                                                <span class="speaker-element">
												<object>
													<?php
                                                    $foto = $model->getThumb('lectorimage', 'main', false, $item['image'][0]);
                                                    if (empty($foto) && $preview && $moderation) {
                                                        $foto = $moderation->getThumb('lectorimage', 'main', false, $item['image'][0]);
                                                    }
                                                    if (empty($foto)) {
                                                        $foto = '/files/defaults/user.jpg';
                                                    }
                                                    if ($foto) { ?>
                                                        <span class="speaker-element-img">
															<img src="<?= $foto; ?>"
                                                                 alt="<?= htmlspecialchars($item['fio']); ?>">
														</span>
                                                    <?php } ?>
													<span class="speaker-element-name"><?= $item['fio']; ?></span>
													<?php if (!empty($item['content'])) { ?>
                                                        <div class="speaker-element-text"><?= $item['content']; ?></div>
                                                    <?php } ?>
                                                    <?php if (!empty($item['video_link'])) { ?>
                                                        <a href="<?= $item['video_link']; ?>"
                                                           class="speaker-element-video-invite-link" data-fancybox>Приглашение</a>
                                                    <?php } ?>
												</object>
											</span>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (!empty($model->suits_for)) { ?>
                                <div class="service-special-block">
                                    <div class="service-special-title">Кому подойдет программа</div>
                                    <div class="service-special-text"><?= $model->suits_for; ?></div>
                                </div>
                            <?php } ?>
                            <?php if (!empty($model->block_text)) { ?>
                                <div class="service-special-block">
                                    <?php if (!empty($model->block_title)) { ?>
                                        <div class="service-special-title">Другая информация о программе</div>
                                    <?php } ?>
                                    <div class="service-special-text"><?= $model->block_text; ?></div>
                                </div>
                            <?php } ?>

                            <?php if (!empty($model->rules)) { ?>
                                <div class="event-rules-block">
                                    <div class="event-rules-title">Правила проведения и условия участия</div>
                                    <div class="event-rules-text"><?= $model->rules; ?></div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                    <?php if (!$model->is_corporative) { // перенос дат и тарифы для корпоративных программ не отображаются?>

                        <?php if ($model->getLastChangedateForFront()) {
                            $old_date = $model->getLastChangedateForFront(); ?>
                            <div class="blog-page-text-wrapper">
                                <div class="bilets-end">
                                    <div class="bilets-end-title">Время проведения программы было перенесено</div>
                                    <div class="bilets-end-text">
                                        с <?= app\helpers\MainHelper::printDateRange($old_date, 'old_date_start', 'old_date_stop'); ?>
                                        на <?= app\helpers\MainHelper::printDateRange($model, 'date_start', 'date_stop'); ?></div>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if (!empty($model->eduprogForms)) { ?>
                            <div id="tickets_box" class="blog-page-text-wrapper">
                                <?php /* если мероприятие отменено - формы не выводим. */
                                $badge = $model->getLowestPrice();
                                if ($model->status == Eduprog::STATUS_CANCELLED) { ?>
                                    <div class="bilets-end">
                                        <div class="bilets-end-title">Программа отменена</div>
                                        <div class="bilets-end-text">Для возврата заказов направьте заявку в свободной
                                            форме на <a href="mailto:help@ast-academy.ru">help@ast-academy.ru</a></div>
                                    </div>
                                    <?php
                                    /* если нет ни одного доступного тарифа */
                                } elseif ($model->is_waiting_list) { ?>
                                    <div class="bilets-end">
                                        <div class="bilets-end-title">Записаться в лист ожидания</div>
                                        <div class="bilets-end-text">Сейчас идёт набор группы с открытой датой старта.
                                            Оставьте заявку и вы в числе первых получите информацию о дате старта
                                            обучения.
                                        </div>
                                        <script data-b24-form="click/112/b8hq0m"
                                                data-skip-moving="true">(function (w, d, u) {
                                                var s = d.createElement('script');
                                                s.async = true;
                                                s.src = u + '?' + (Date.now() / 180000 | 0);
                                                var h = d.getElementsByTagName('script')[0];
                                                h.parentNode.insertBefore(s, h);
                                            })(window, document, 'https://crm.ast-academy.ru/upload/crm/form/loader_112_b8hq0m.js');</script>
                                        <button class="button medium mb5 mt10">Запись в лист ожидания</button>
                                    </div>
                                    <?php
                                    /* иначе выводим формы */
                                } elseif ($badge['form_id'] === false) { ?>
                                    <div class="bilets-end">
                                        <div class="bilets-end-title">Записаться в лист ожидания</div>
                                        <div class="bilets-end-text">На данный момент группа полностью набрана, но вы
                                            можете записаться в лист ожидания. Мы в первую очередь свяжемся с вами при
                                            формировании нового потока.
                                        </div>
                                        <script data-b24-form="click/85/je3d13"
                                                data-skip-moving="true">(function (w, d, u) {
                                                var s = d.createElement('script');
                                                s.async = true;
                                                s.src = u + '?' + (Date.now() / 180000 | 0);
                                                var h = d.getElementsByTagName('script')[0];
                                                h.parentNode.insertBefore(s, h);
                                            })(window, document, 'https://crm.ast-academy.ru/upload/crm/form/loader_85_je3d13.js');</script>
                                        <button class="button medium mb5 mt10">Запись в лист ожидания</button>
                                    </div>
                                    <?php
                                    /* иначе выводим формы */
                                } else {
                                    /* смотрим сколько форм доступно к продаже */
                                    $tariff_list = $model->tariff_list;
                                    $total_forms = [];
                                    foreach ($tariff_list as $tariff) {
                                        if (in_array($tariff->eduprogform_id, $total_forms)) {
                                            continue;
                                        }
                                        if ($model->canBuyTarif($tariff->id, 1)) {
                                            $total_forms[] = $tariff->eduprogform_id;
                                        }
                                    }
                                    /* если несколько форм */
                                    if (count($total_forms) > 1) { ?>
                                        <div class="buy-tickets-box">
                                            <h2 class="buy-tickets-title">Выберите тариф</h2>
                                            <div class="buy-tickets-tabs">
                                                <?php
                                                $active_tab = true;
                                                foreach ($model->eduprogForms as $form_eduprog) {
                                                    // если форма не входит в список отображаемых - пропускаем.
                                                    if (!in_array($form_eduprog->id, $total_forms)) {
                                                        continue;
                                                    } ?>
                                                    <div class="buy-tickets-tab tab-trigger<?= $active_tab ? ' active' : ''; ?>"
                                                         data-tab="tickets_box_<?= $form_eduprog->id; ?>"><?= $form_eduprog->name; ?></div>
                                                    <?php $active_tab = false; ?>
                                                <?php } ?>
                                            </div>
                                            <div class="tariff-tabs-content">
                                                <?php
                                                $active_tab = true;
                                                foreach ($model->eduprogForms as $form_eduprog) {
                                                    // если форма не входит в список отображаемых - пропускаем.
                                                    if (!in_array($form_eduprog->id, $total_forms)) {
                                                        continue;
                                                    } ?>
                                                    <div id="tickets_box_<?= $form_eduprog->id; ?>"
                                                         class="buy-tickets-wrapper tab-item<?= $active_tab ? ' active' : ''; ?>"
                                                         data-tab="tickets_box_<?= $form_eduprog->id; ?>">
                                                        <?= $this->render('_eduprog_tariff_form', ['form_eduprog' => $form_eduprog]); ?>
                                                    </div>
                                                    <?php $active_tab = false; ?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php
                                        /* если форма одна */
                                    } else {
                                        /* total_forms всегда будет иметь 1 значение, т.к. условием определено, что у мероприятия есть минимум 1 действующий тариф */
                                        $form_eduprog = \app\modules\eduprog\models\EduprogForm::findOne($total_forms[0]);
                                        if ($form_eduprog) { ?>
                                            <!-- задать айдишник чтобы якорь приводил сразу к этому блоку -->
                                            <div id="tickets_box_<?= $form_eduprog->id; ?>" class="buy-tickets-box">
                                                <h2 class="buy-tickets-title">Выберите тариф</h2>
                                                <div class="buy-tickets-wrapper">
                                                    <?= $this->render('_eduprog_tariff_form', ['form_eduprog' => $form_eduprog]); ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php }
                                } ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                    <?php if ($model->tags) { ?>
                        <section class="sec content_sec section-event-page3 mobile-visible">
                            <div class="container wide">
                                <div class="expert_item-tags mobile-visible">
                                    <?php foreach ($model->tags as $tag) { ?>
                                        <?= Html::a('<b class="tag-hovered">' . mb_strtolower($tag->name, 'UTF-8') . '</b><span>' . mb_strtolower($tag->name, 'UTF-8') . '</span>', (!empty($eduprog_catalog) ? $eduprog_catalog->getUrlPath() . '?tag=' . urlencode($tag->name) : false), ['class' => 'tag', 'data-tag_id' => $tag->id, 'data-tag_name' => $tag->name]); ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </section>
                    <?php } ?>
                    <?php if ($model->keywords) { ?>
                        <section class="sec content_sec section-event-page3 mobile-visible">
                            <div class="container wide">
                                <div class="expert_item-tags mobile-visible">
                                    <?php foreach ($model->keywords as $keyword) { ?>
                                        <?= Html::a('<b class="tag-hovered">' . mb_strtolower($keyword->name, 'UTF-8') . '</b><span>' . mb_strtolower($keyword->name, 'UTF-8') . '</span>', (!empty($eduprog_catalog) ? $eduprog_catalog->getUrlPath() . '?keyword=' . urlencode($keyword->name) : false), ['class' => 'tag', 'data-tag_id' => $keyword->id, 'data-tag_name' => $keyword->name]); ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </section>
                    <?php } ?>
                    <!-- задать вопрос -->
                    <div class="ask-question-block">
                        <div class="ask-question-block-title">Напишите нам</div>
                        <div class="ask-question-block-text">Если вы хотите узнать больше о программе, условиях обучения
                            или оплаты, оставьте заявку — и мы перезвоним
                        </div>
                        <a href="#ask_modal" class="button medium mb0">Задать вопрос</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- модалка б24 -->
    <div class="modal" id="ask_modal">
        <div class="modal_content">
            <a href="#" class="modal_close" data-fancybox-close>x</a>
            <script data-b24-form="inline/25/o8npqb" data-skip-moving="true">(function (w, d, u) {
                    var s = d.createElement('script');
                    s.async = true;
                    s.src = u + '?' + (Date.now() / 180000 | 0);
                    var h = d.getElementsByTagName('script')[0];
                    h.parentNode.insertBefore(s, h);
                })(window, document, 'https://crm.ast-academy.ru/upload/crm/form/loader_25_o8npqb.js');</script>
        </div>
        <div class="modal_overlay"></div>
    </div>

<?php if (!empty($closest_eduprog)) { ?>
    <section class="sec content_sec section-event-page2">
        <div class="container wide container-slider">
            <div class="section-event-page-title2 with_button">Другие программы <a
                        href="<?= $eduprog_catalog?->getUrlPath(); ?>" class="button see_all">Смотреть все</a></div>
            <div class="blog-page-4card-slider all-events-compilation owl-carousel owl-theme" data-loop="true"
                 data-autoplay="true" data-timeout="5000">
                <?php foreach ($closest_eduprog as $item) { ?>
                    <div class="blog-page-4card-slide">
                        <a href="<?= $item->getUrlPath(); ?>" class="blog-page-4card-slide-img">
                            <img src="<?= $item->getThumb('image', 'main'); ?>"
                                 alt="<?= htmlspecialchars($item->name); ?>">
                        </a>
                        <div class="blog-page-4card-slide-info">
                            <a href="<?= $item->author->getUrlPath(); ?>" class="all-events-card-author">
                                <div class="all-events-card-author-img">
                                    <img src="<?= $item->author->profile->getThumb('image', 'main'); ?>"
                                         alt="<?= $item->author->profile->halfname; ?>">
                                </div>
                                <div class="all-events-card-author-name"><?= $item->author->profile->halfname; ?></div>
                            </a>
                            <div class="all-events-card-dates"><?= $item->getEduprogDateForView(); ?></div>
                            <div class="all-events-card-type"><?= ($item->category ? $item->category->name : ''); ?></div>
                            <a href="<?= $item->getUrlPath(); ?>"
                               class="blog-page-4card-slide-title"><?= $item->name; ?></a>
                            <?php $badge = $item->getLowestPrice();
                            if ($badge['form_id']) { ?>
                                <a href="<?= $item->getUrlPath() . '#tickets_box_' . $badge['form_id']; ?>"
                                   class="all-events-card-price">
                                    <?= ($badge['low_price'] == 0) ? 'Бесплатно' : 'от ' . number_format($badge['low_price'], 0, '.', ' ') . ' ₽'; ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>
<?php if ($footer_banner) { ?>
    <section class="sec content_sec section-event-page2">
        <div class="container wide">
            <div class="blog-page-long-banner">
                <?php if (!empty($footer_banner->link)) { ?><a href="<?= $footer_banner->link; ?>"
                                                               target="_blank"><?php } ?>
                    <img src="<?= $footer_banner->getThumb('image', 'main'); ?>" alt="<?= $footer_banner->name; ?>"
                         class="visible-over650" loading="lazy">
                    <img src="<?= $footer_banner->getThumb('image_mobile', 'main'); ?>"
                         alt="<?= $footer_banner->name; ?>" class="visible-less650" loading="lazy">
                    <?php if (!empty($footer_banner->link)) { ?></a><?php } ?>
            </div>
        </div>
    </section>
<?php } ?>
<?php
// для корпоративных программ размещаем форму для заявок из Битрикс24.
if ($model->is_corporative) { ?>
    <div class="modal" id="corporative_modal">
        <div class="modal_content">
            <a href="#" class="modal_close" data-fancybox-close>x</a>
            <script data-b24-form="inline/67/a31hzs" data-skip-moving="true">(function (w, d, u) {
                    var s = d.createElement('script');
                    s.async = true;
                    s.src = u + '?' + (Date.now() / 180000 | 0);
                    var h = d.getElementsByTagName('script')[0];
                    h.parentNode.insertBefore(s, h);
                })(window, document, 'https://crm.ast-academy.ru/upload/crm/form/loader_67_a31hzs.js');</script>
        </div>
        <div class="modal_overlay"></div>
    </div>
    <?php
    $js = <<<JS
    $('body').on('click','.corporative_show, a[href="#corporative_modal"], button[href="#corporative_modal"]', function(e){
		e.preventDefault();
		$.fancybox.open({
			src: '#corporative_modal',
			type: 'inline'
		});
	});
	if(document.location.href.indexOf('#corporative_modal') >= 0) {
		$.fancybox.open({
			src: '#corporative_modal',
			type: 'inline'
		});
	}
JS;
    $this->registerJs($js);
    ?>


<?php } ?>
    <div class="modal" id="fail_order_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Оформление заказа</div>
                <p>При оформление заказа возникла ошибка.</p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>

<?php
$login_page = \app\modules\pages\models\Login::find()->where(['model' => \app\modules\pages\models\Login::class, 'visible' => 1])->one();
$login_url = $login_page ? $login_page->getUrlPath() : false;
$register_page = \app\modules\pages\models\Regfizusr::find()->where(['model' => \app\modules\pages\models\Regfizusr::class, 'visible' => 1])->one();
$register_url = $register_page ? $register_page->getUrlPath() : false;
?>

<?php
$url_order = Url::toRoute(['/pages/eduprog/checkorder']);
$command_text = 'return `${(+price).toLocaleString()} ${currency}`;';
$js = <<<JS
	$('body').on('click','.ask_modal, a[href="#ask_modal"], button[href="#ask_modal"]', function(e){
        e.preventDefault();
        $.fancybox.open({
            src: '#ask_modal',
            type: 'inline'
        });
    });


	$('.bilet_qty').change(function () {
		let tariff = $(this).closest('.buy-tickets-tariff');
		let tickets_id = $(this).closest('.buy-tickets-wrapper').attr('data-tickets-id');
		let ticket_count = parseInt($(this).val());
		let price = tariff.find('.buy-tickets-price-js').data('price');

		let total_count_hidden = tariff.find('.buy-tickets-price-count-js');
		total_count_hidden.val(price*ticket_count);

		if(ticket_count > 0) {
			tariff.addClass('choise');
		} else tariff.removeClass('choise');

		total(tickets_id);
	});

	function total(id) {
		let parent = $('.buy-tickets-wrapper[data-tickets-id="'+ id +'"]');
		let total = parent.find('.tickets-total-js');

		// считаем сумму билетов
		let totalSum = 0;
		parent.find('.buy-tickets-price-count-js').each(function () {
			totalSum = totalSum + parseInt($(this).val());
		});
		parent.find('.tickets-total-hidden-js').val(totalSum);

		// считаем кол-во билетов
		let totalCount = 0;
		parent.find('.bilet_qty').each(function () {
			totalCount = totalCount + parseInt($(this).val());
		});
		parent.find('.tickets-total-count-hidden-js').val(totalCount);

		// если результат больше 0 - делаем активной кнопку
		if(totalCount > 0 && totalSum > 0) {
			total.removeClass('disabled');
			// выводим текст в кнопку
			total.html('Купить ' + totalCount + ' за <span class="price-space-js">' + totalSum + ' ₽</span>');
		} else if(totalCount > 0 && totalSum == 0) {
			total.removeClass('disabled');
			// выводим текст в кнопку
			if(totalCount > 1) {
				total.html('Оформить');
			} else total.html('Оформить')
		} else {
			total.addClass('disabled');
			// выводим текст в кнопку
			total.html('Выберите');
		}

		// ставим пробелы в цену
		total.find('.price-space-js').text((i, text) => {
		  	const [ price, currency ] = text.split(' ');
		  	{$command_text}
		});
	}

	$('.buy-tickets-wrapper').each(function (i, e) {
		$(this).attr('data-tickets-id', i+1);
	});

	function declOfNum(n, text_forms) {  
	    n = Math.abs(n) % 100; 
	    var n1 = n % 10;
	    if (n > 10 && n < 20) { return text_forms[2]; }
	    if (n1 > 1 && n1 < 5) { return text_forms[1]; }
	    if (n1 == 1) { return text_forms[0]; }
	    return text_forms[2];
	}

JS;
$this->registerJs($js);
if ($preview) {
    $js = <<<JS
	$('.buy_tickets').click(function(e) {
		e.preventDefault();
		$('#fail_order_modal .success_box p').html('Невозможно совершить покупку в режиме предпросмотра');
	    modalPos('#fail_order_modal');
	});
JS;
} else {
    $js = <<<JS
	$('.buy_tickets').click(function(e) {
		e.preventDefault();
		let order_data = {};
		$(this).closest('.buy-tickets-wrapper').find('.bilet_qty').each(function() {
			if ($(this).val() > 0) {
				order_data[$(this).data('tariff')] = $(this).val();
			}
		});
		let param = yii.getCsrfParam();
		let token = yii.getCsrfToken();
		$.ajax({
			type: 'POST',
			url: '{$url_order}',
			processData: true,
			dataType: 'json',
			data: {order_data:order_data,param:token},
			success: function(data){
				if (data.status == 'success') {
					// в случае успеха редирект на страницу оплаты заказа
					if (data.redirect_to) {
						window.location.href = data.redirect_to;
					} else {
						$('#fail_order_modal .success_box p').html(data.message);
						modalPos('#fail_order_modal');
					}
				} else if (data.status == 'need_register') {
                    $.fancybox.open($('#need_auth'));
                } else {
                    // в случае ошибки вывести сообщение
                    $('#fail_order_modal .success_box p').html(data.message);
                    modalPos('#fail_order_modal');
                }
			}
		});
	});
JS;
}
$this->registerJs($js);
?>