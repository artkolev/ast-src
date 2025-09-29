<?php

use app\modules\pages\models\Eventspage;
use app\modules\pages\models\LentaMaterialpage;
use app\modules\pages\models\LentaNewspage;
use app\modules\users\models\UserAR;
use yii\helpers\Html;

$material_catalog = LentaMaterialpage::find()->where(['model' => LentaMaterialpage::class, 'visible' => 1])->one();
$news_catalog = LentaNewspage::find()->where(['model' => LentaNewspage::class, 'visible' => 1])->one();
$events_catalog = Eventspage::find()->where(['model' => Eventspage::class, 'visible' => 1])->one();
$news = $model->newsObjects;
$materials = $model->materialsObjects;
$events = $model->eventsObjects;
$quotes = $model->quotesObjects;
$targets = $model->targetsObjects;
$tasks = $model->tasksObjects;
?>
    <main class="sec content_sec sec-bg-pic">
        <div class="pic-bg">
            <img src="<?= $model->getThumb('back_image', 'main'); ?>" alt="">
        </div>
        <div class="container wide">

            <h1><?= $model->name; ?></h1>
            <div class="subheader"><?= $model->description; ?></div>
            <div class="labs-top-wrapper">
                <?php if (!empty($model->direction)) { ?>
                    <div class="expert_item-tags">
                        <?php foreach ($model->direction as $item) { ?>
                            <a class="tag" href="<?= $item->getUrlPath(); ?>"><b
                                        class="tag-hovered"><?= $item->name; ?></b><span><?= $item->name; ?></span></a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </main>

<?php if ((!empty($model->content))
        or (!empty($model->users))
        or (!empty($events))
        or (!empty($news))
        or (!empty($materials))
        or ((!empty($model->join_us_title) && !empty($model->join_us_text)) || (!empty($model->join_us_button_name) && !empty($model->join_us_button_link)) || (!empty($model->join_us_button2_name) && !empty($model->join_us_button2_link)) || (!empty($model->join_us_button3_name) && !empty($model->join_us_button3_link)) || (!empty($model->join_us_button4_name) && !empty($model->join_us_button4_link)))
) { ?>
    <main class="sec nav-wrap">
        <div class="container wide">

            <ul class="nav">
                <?php if (!empty($model->content)) { ?>
                    <li><a class="anchor" href="#about">О лаборатории</a></li>
                <?php } ?>
                <?php if (!empty($model->users)) { ?>
                    <li><a class="anchor" href="#users">Участники лаборатории</a></li>
                <?php } ?>
                <?php if (!empty($news)) { ?>
                    <li><a class="anchor" href="#news">Новости</a></li>
                <?php } ?>
                <?php if (!empty($events)) { ?>
                    <li><a class="anchor" href="#events">Мероприятия</a></li>
                <?php } ?>
                <?php if (!empty($materials)) { ?>
                    <li><a class="anchor" href="#materials">База знаний</a></li>
                <?php } ?>
                <?php if ((!empty($model->join_us_button_name) && !empty($model->join_us_button_link)) || (!empty($model->join_us_button2_name) && !empty($model->join_us_button2_link)) || (!empty($model->join_us_button3_name) && !empty($model->join_us_button3_link)) || (!empty($model->join_us_button4_name) && !empty($model->join_us_button4_link))) { ?>
                    <li><a class="anchor" href="#join">Присоединиться к лаборатории</a></li>
                <?php } ?>
            </ul>
        </div>
    </main>
<?php } ?>

<?php if (!empty($model->video_title)) { ?>
    <section id="about" class="sec">
        <div class="container wide page-promo">
            <?php if (!empty($model->video_link) or !empty($model->video_image)) { ?>
                <div class="index_video-area ver-3">
                    <div class="index_video-right">
                        <?php if (!empty($model->video_link)) { ?>
                            <div class="youtube_preview mb0">
                                <a href="<?= $model->video_link; ?>" data-fancybox class="youtube_link">
                                    <?= Html::img($model->getThumb('video_image', 'main'), ['alt' => $model->video_title, 'loading' => 'lazy']); ?>
                                </a>
                            </div>
                        <?php } elseif (!empty($model->video_image)) { ?>
                            <?= Html::img($model->getThumb('video_image', 'main'), ['alt' => $model->video_title, 'loading' => 'lazy']); ?>
                        <?php } ?>
                    </div>
                    <article class="index_video-left">
                        <h2><?= $model->video_title; ?></h2>
                        <?= $model->video_descr; ?>
                    </article>
                </div>
            <?php } ?>
        </div>
    </section>
<?php } ?>

<?php if (!empty($model->content)) { ?>
    <section class="sec content_txt">
        <div class="container wide">

            <div class="txt_expand">
                <p class="text-content"><?= $model->content; ?></p>
                <button class="button-open arr_down">Развернуть</button>
            </div>

        </div>
    </section>
<?php } ?>

<?php if (!empty($targets) || !empty($tasks)) { ?>
    <section class="sec section-page section-goals-and-problems">
        <div class="container wide">
            <div class="goals-and-problems-list">
                <?php if (!empty($targets)) { ?>
                    <div class="goals-and-problems-element blue">
                        <div class="goals-and-problems-element-title">Цели лаборатории</div>
                        <div class="goals-and-problems-element-list">
                            <?php foreach ($targets as $item) { ?>
                                <div class="goals-and-problems-element-item"><?= $item['name']; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if (!empty($tasks)) { ?>
                    <div class="goals-and-problems-element orange">
                        <div class="goals-and-problems-element-title">Задачи лаборатории</div>
                        <div class="goals-and-problems-element-list">
                            <?php foreach ($tasks as $item) { ?>
                                <div class="goals-and-problems-element-item"><?= $item['name']; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>

<?php if (!empty($quotes)) { ?>
    <section class="sec section-page teachers">
        <div class="container wide">
            <div class="teachers_slider owl-carousel dotsNavTeachers" data-autoplay="0">
                <?php foreach ($quotes as $item) { ?>
                    <?php if ($item['visible'] && (int)$item['user_id'] > 0) { ?>
                        <?php $user = UserAR::getUserById($item['user_id']); ?>
                        <?php if (!empty($user)) { ?>
                            <div class="item">
                                <div class="teacher_slide">
                                    <div class="teacher_slide-info">
                                        <div class="teacher_slide-img">
                                            <?= Html::a(Html::img($user->profile->getThumb('image', 'profile'), ['alt' => $user->profile->fullname]), $user->getUrlPath()); ?>
                                        </div>
                                        <div class="teacher_slide-name">
                                            <a href="<?= $user->getUrlPath(); ?>"
                                               target="_blank"><?= $user->profile->halfname; ?></a>
                                        </div>
                                        <div class="teacher_slide-post"><?= $item['content']; ?></div>
                                    </div>
                                    <blockquote class="teacher_slide-text">
                                        <p><?= $item['description']; ?></p>
                                    </blockquote>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <?php if ($item['visible']) { ?>
                            <div class="item">
                                <div class="teacher_slide">
                                    <div class="teacher_slide-info">
                                        <div class="teacher_slide-img"><img
                                                    src="<?= $model->getThumb('quoteimage', 'main', false, $item['image'][0]); ?>"
                                                    alt=""></div>
                                        <div class="teacher_slide-name"><?= $item['fio']; ?></div>
                                        <div class="teacher_slide-post"><?= $item['content']; ?></div>
                                    </div>
                                    <blockquote class="teacher_slide-text">
                                        <p><?= $item['description']; ?></p>
                                    </blockquote>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </div>
    </section>
<?php } ?>

<?php if (!empty($model->users)) { ?>
    <section id="users" class="sec section-page section-experts-academy gray-bg">
        <div class="container wide">
            <h3>Участники Лаборатории</h3>
            <div class="subheader">
                Команда профессионалов-единомышленников, которая придумывает и реализует мероприятия и проекты для
                развития Лаборатории и ее ключевой темы
            </div>
            <div class="experts-academy-list default-slider-4 owl-carousel owl-theme">
                <?php foreach ($model->users as $i => $item) { ?>
                    <?php if ($item['visible'] && (int)$item['user_id'] > 0) { ?>
                        <?php $user = UserAR::getUserById($item['user_id']); ?>
                        <?php if (!empty($user)) { ?>
                            <div href="#speaker_<?= $i; ?>" class="experts-academy-element" data-fancybox>
                                <div class="expert_item">
                                    <div class="expert_item-img_box">
                                        <?= Html::a(Html::img($user->profile->getThumb('image', 'profile'), ['alt' => $user->profile->fullname]), $user->getUrlPath(), ['class' => 'expert_item-img']); ?>
                                        <?php if (!empty($item['content'])) { ?>
                                            <span class="expert_rang no-rang blue">
											<span><?= $item['content']; ?></span>
										</span>
                                        <?php } else { ?>
                                            <?= app\widgets\shield\ShieldWidget::widget(['user' => $user]); ?>
                                        <?php } ?>
                                    </div>
                                    <div class="expert_item-info">
                                        <h4>
                                            <a href="<?= $user->getUrlPath(); ?>"><?= $user->profile->getHalfname('<br>'); ?></a>
                                        </h4>
                                        <?php if (!is_null($user->directionM) && !$user->directionM->stels_direct) { ?>
                                            <div class="expert_item-direction"><?= $user->directionM->name; ?></div>
                                        <?php } ?>
                                        <?php if (!empty($item['description'])) { ?>
                                            <div class="expert_item-desc">
                                                <a href="#!"><?= mb_substr(strip_tags($item['description']), 0, 150, 'UTF-8'); ?></a>
                                            </div>
                                        <?php } else { ?>
                                            <div class="expert_item-desc">
                                                <a href="<?= $user->getUrlPath(); ?>"><?= mb_substr(strip_tags($user->profile->about_myself), 0, 150, 'UTF-8'); ?> </a>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <?php if ($item['visible']) { ?>
                            <div href="#speaker_<?= $i; ?>" class="experts-academy-element" data-fancybox>
                                <div class="expert_item">
                                    <div class="expert_item-img_box">
                                        <a href="#!" class="expert_item-img">
                                            <img src="<?= $model->getThumb('userimage', 'main', false, $item['image'][0]); ?>"
                                                 alt="<?= $item['fio']; ?>">
                                        </a>
                                        <?php if (!empty($item['content'])) { ?>
                                            <span class="expert_rang no-rang blue">
											<span><?= $item['content']; ?></span>
										</span>
                                        <?php } ?>
                                    </div>
                                    <div class="expert_item-info">
                                        <h5><a href="#!"><?= $item['fio']; ?></a></h5>
                                        <?php if (!empty($item['description'])) { ?>
                                            <div class="expert_item-desc">
                                                <a href="#!"><?= mb_substr(strip_tags($item['description']), 0, 150, 'UTF-8'); ?></a>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </section>
    <?php foreach ($model->users as $i => $item) { ?>
        <?php if ($item['visible'] && (int)$item['user_id'] > 0) { ?>
            <?php $user = UserAR::getUserById($item['user_id']); ?>
            <?php if (!empty($user)) { ?>
                <div id="speaker_<?= $i; ?>" class="modal modal-speaker">
                    <div class="modal_content">
                        <a href="#" class="modal_close" data-fancybox-close>x</a>
                        <div class="modal-speaker-wrapper">
                            <div class="modal-speaker-img">
                                <?= Html::a(Html::img($user->profile->getThumb('image', 'profile'), ['alt' => $user->profile->fullname]), $user->getUrlPath(), ['class' => 'expert_item-img']); ?>
                            </div>
                            <div class="modal-speaker-info">
                                <a href="<?= $user->getUrlPath(); ?>" class="modal-speaker-name"
                                   target="_blank"><?= $user->profile->getHalfname('<br>'); ?></a>
                                <?php if (!is_null($user->directionM) && !$user->directionM->stels_direct) { ?>
                                    <div class="modal-speaker-status"><?= $user->directionM->name; ?></div>
                                <?php } ?>
                            </div>
                        </div>
                        <?php if (!empty($item['description'])) { ?>
                            <div class="modal-speaker-text"><?= $item['description']; ?></div>
                        <?php } else { ?>
                            <div class="modal-speaker-text"><?= $user->profile->about_myself; ?></div>
                        <?php } ?>
                        <a class="modal-speaker-link-main-site" target="_blank" href="<?= $user->getUrlPath(); ?>">Подробнее
                            об эксперте</a>
                    </div>
                    <div class="modal_overlay"></div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <?php if ($item['visible']) { ?>
                <div id="speaker_<?= $i; ?>" class="modal modal-speaker">
                    <div class="modal_content">
                        <a href="#" class="modal_close" data-fancybox-close>x</a>
                        <div class="modal-speaker-wrapper">
                            <div class="modal-speaker-img">
                                <img src="<?= $model->getThumb('userimage', 'main', false, $item['image'][0]); ?>"
                                     alt="<?= $item['fio']; ?>">
                            </div>
                            <div class="modal-speaker-info">
                                <a href="#!" class="modal-speaker-name"><?= $item['fio']; ?></a>
                                <div class="modal-speaker-status"><?= $item['content']; ?></div>
                            </div>
                        </div>
                        <div class="modal-speaker-text"><?= $item['description']; ?></div>
                    </div>
                    <div class="modal_overlay"></div>
                </div>
            <?php } ?>
        <?php } ?>
    <?php } ?>
<?php } ?>

<?php if (!empty($events)) { ?>
    <section class="sec content_sec section-event-page2 white-bg" id="events">
        <div class="container wide container-slider">
            <h2 class="with_button">Мероприятия <a href="<?= $events_catalog->getUrlPath(['labs[]' => $model->id]); ?>"
                                                   class="button see_all">Смотреть все</a></h2>
            <div class="blog-page-4card-slider all-events-compilation owl-carousel owl-theme" data-loop="true"
                 data-autoplay="true" data-timeout="5000">
                <?php foreach ($events as $i => $item) { ?>
                    <?php
                    $eventHolder = $item->eventCardStatusText(); ?>
                    <div class="blog-page-4card-slide <?= $eventHolder->holder ? 'soldout' : false; ?>">
                        <a href="<?= $item->getUrlPath(); ?>" class="blog-page-4card-slide-img">
                            <img src="<?= $item->getThumb('image', 'main'); ?>"
                                 alt="<?= str_replace('"', '&quot;', $item->name); ?>">
                            <?php if ($eventHolder->holder) { ?>
                                <!-- если soldout - выводить лейбл, если билетов нет, добавить класс closed -->
                                <div class="soldout-label closed"><?= $eventHolder->holder_text; ?></div>
                            <?php } ?>
                        </a>
                        <div class="blog-page-4card-slide-info">
                            <?php $author = $item->getAuthorForView(); ?>
                            <a href="<?= $author['link']; ?>" class="all-events-card-author">
                                <div class="all-events-card-author-img">
                                    <img src="<?= $author['image']; ?>" alt="<?= $author['name']; ?>">
                                </div>
                                <div class="all-events-card-author-name"><?= $author['name']; ?></div>
                            </a>
                            <div class="all-events-card-dates"><?= $item->getEventDateForView(); ?></div>
                            <div class="all-events-card-type"><?= ($item->format ? $item->format->name : ''); ?></div>
                            <a href="<?= $item->getUrlPath(); ?>"
                               class="blog-page-4card-slide-title"><?= $item->name; ?></a>
                            <?php if (!$eventHolder->holder) { ?>
                                <?php [$price, $form_id] = $item->priceBadge; ?>
                                <?php if ($price) { ?>
                                    <a href="<?= $item->getUrlPath() . '#tickets_box_' . $form_id; ?>"
                                       class="all-events-card-price"><?= $price; ?></a>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>

<?php if (!empty($materials)) { ?>
    <section class="sec content_sec section-blog-page2" id="materials">
        <div class="container wide container-slider">
            <h2 class="with_button">База знаний лаборатории <a
                        href="<?= $material_catalog->getUrlPath(['labs' => $model->id]); ?>" class="button see_all">Смотреть
                    все</a></h2>
            <div class="blog-page-4card-slider owl-carousel owl-theme" data-loop="true" data-autoplay="true"
                 data-timeout="5000">
                <?php foreach ($materials as $i => $item) { ?>
                    <a href="<?= $item->getUrlPath(); ?>" class="blog-page-4card-slide">
                        <div class="blog-page-4card-slide-img">
                            <img src="<?= $item->getThumb('image', 'main'); ?>"
                                 alt="<?= str_replace('"', '&quot;', $item->name); ?>">
                        </div>
                        <div class="blog-page-4card-slide-info">
                            <div class="blog-page-4card-slide-title"><?= $item->name; ?></div>
                            <div class="blog-page-4card-slide-text-wrapper">
                                <div class="blog-page-4card-slide-date"><?= \Yii::$app->formatter->asDate($item->published, 'php:d.m.Y'); ?></div>
                                <!-- <div class="blog-page-4card-slide-viewed"><?= $item->views; ?></div> -->
                            </div>
                        </div>
                    </a>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>

<?php if (!empty($news)) { ?>
    <section class="sec content_sec section-blog-page2" id="news">
        <div class="container wide container-slider">
            <h2 class="with_button">Новости <a href="<?= $news_catalog->getUrlPath(['labs' => $model->id]); ?>"
                                               class="button see_all">Смотреть все</a></h2>
            <div class="blog-page-4card-slider owl-carousel owl-theme" data-loop="true" data-autoplay="true"
                 data-timeout="5000">
                <?php foreach ($news as $i => $item) { ?>
                    <a href="<?= $news[$i]->getUrlPath(); ?>" class="blog-page-4card-slide">
                        <div class="blog-page-4card-slide-img">
                            <img src="<?= $news[$i]->getThumb('image', 'main'); ?>"
                                 alt="<?= str_replace('"', '&quot;', $news[$i]->name); ?>">
                        </div>
                        <div class="blog-page-4card-slide-info">
                            <div class="blog-page-4card-slide-title"><?= $news[$i]->name; ?></div>
                            <div class="blog-page-4card-slide-text-wrapper">
                                <div class="blog-page-4card-slide-date"><?= \Yii::$app->formatter->asDate($news[$i]->published, 'php:d.m.Y'); ?></div>
                                <!-- <div class="blog-page-4card-slide-viewed"><?= $news[$i]->views; ?></div> -->
                            </div>
                        </div>
                    </a>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>

<?php if ((!empty($model->join_us_title) && !empty($model->join_us_text)) || (!empty($model->join_us_button_name) && !empty($model->join_us_button_link)) || (!empty($model->join_us_button2_name) && !empty($model->join_us_button2_link)) || (!empty($model->join_us_button3_name) && !empty($model->join_us_button3_link)) || (!empty($model->join_us_button4_name) && !empty($model->join_us_button4_link))) { ?>
    <section id="join" class="sec section-page section-personal-consultation section-partnership gray-bg">
        <div class="container wide">
            <div class="join_us_box join_us_box-v3">
                <span class="join_us_bg join_us_bg-v3"></span>
                <div class="join_us_box-title"><?= $model->join_us_title; ?></div>
                <p><?= $model->join_us_text; ?></p>
                <div class="join_us_button">
                    <?php if (!empty($model->join_us_button_name) && !empty($model->join_us_button_link)) { ?><a
                        href="<?= $model->join_us_button_link; ?>"
                        class="button white"><?= $model->join_us_button_name; ?></a><?php } ?>
                    <?php if (!empty($model->join_us_button2_name) && !empty($model->join_us_button2_link)) { ?><a
                        href="<?= $model->join_us_button2_link; ?>"
                        class="button white"><?= $model->join_us_button2_name; ?></a><?php } ?>
                    <?php if (!empty($model->join_us_button3_name) && !empty($model->join_us_button3_link)) { ?><a
                        href="<?= $model->join_us_button3_link; ?>"
                        class="button white"><?= $model->join_us_button3_name; ?></a><?php } ?>
                    <?php if (!empty($model->join_us_button4_name) && !empty($model->join_us_button4_link)) { ?><a
                        href="<?= $model->join_us_button4_link; ?>"
                        class="button white"><?= $model->join_us_button4_name; ?></a><?php } ?>
                </div>
            </div>
        </div>
    </section>
<?php } ?>

<?php
$this->registerJsFile('/js/main-blog.js', ['depends' => [app\assets\AppAsset::class]]);
$this->registerCssFile('/css/style-blog.css', ['depends' => [app\assets\AppAsset::class]]);
?>