<?php

use app\helpers\MainHelper;
use app\widgets\login\LoginWidget;

?>
    <section class="sec content_sec search-rezult">
        <div class="container wide">
            <div class="columns_box">
                <div class="main_col">
                    <h1 class="search-rezult_title"><?= $model->getNameForView(); ?></h1>
                    <?= $model->content; ?>
                    <form method="get" class="search-form">
                        <input type="text" name="query" value="<?= $search_text; ?>" class="search-form_input"
                               placeholder="Искать на сайте"/>
                        <button type="submit" class="button long search-form_btn">Найти</button>
                    </form>

                    <div class="search-rezult_info">По запросу «<?= $search_text; ?>»
                        найдено <?= ($experts_count + $services_count + $events_count + $blogs_count + $news_count + $projects_count + $materials_count + $eduprog_count); ?> <?= MainHelper::pluralForm($experts_count + $events_count + $blogs_count + $news_count + $projects_count + $materials_count, ['совпадение', 'совпадения', 'совпадений']); ?>
                        .
                    </div>
                    <ul class="search-filter tabs_nav">
                        <li><a class="search-filter__item" href="#tab-1">Эксперты <span>(<?= $experts_count; ?>)</span></a>
                        </li>
                        <li><a class="search-filter__item" href="#tab-6">Услуги
                                <span>(<?= $services_count; ?>)</span></a></li>
                        <li><a class="search-filter__item" href="#tab-2">Мероприятия
                                <span>(<?= $events_count; ?>)</span></a></li>
                        <li><a class="search-filter__item" href="#tab-8">ДПО <span>(<?= $eduprog_count; ?>)</span></a>
                        </li>
                        <li><a class="search-filter__item" href="#tab-3">Блог <span>(<?= $blogs_count; ?>)</span></a>
                        </li>
                        <li><a class="search-filter__item" href="#tab-7">Новости <span>(<?= $news_count; ?>)</span></a>
                        </li>
                        <li><a class="search-filter__item" href="#tab-4">Портфолио
                                <span>(<?= $projects_count; ?>)</span></a></li>
                        <li><a class="search-filter__item" href="#tab-5">База знаний
                                <span>(<?= $materials_count; ?>)</span></a></li>
                    </ul>
                </div>
            </div>
            <div class="tabs_container">
                <div class="tab" id="tab-1">
                    <?php if (!empty($experts)) { ?>
                        <?= $this->render('_expert_box', ['items' => $experts]); ?>
                    <?php } else { ?>
                        <div class="search-rezult__empty">
                            <p>Обычно запрос представляет из себя просто одно или несколько слов, например: <b>Фасилитация.</b>
                                По такому запросу будут найдены страницы, на которых встречается введенное слово.</p>
                            <p>Вы можете ввести более конкретный запрос, либо задать интересующий вас вопрос на email <a
                                        href="mailto:askme@ast-academy.ru">askme@ast-academy.ru</a>.</p>
                        </div>
                    <?php } ?>
                </div>
                <!-- услуги -->
                <div class="tab" id="tab-6">
                    <?php if (!empty($services)) { ?>
                        <?= $this->render('_service_types_box', ['items' => $services]); ?>
                    <?php } else { ?>
                        <div class="search-rezult__empty">
                            <p>Обычно запрос представляет из себя просто одно или несколько слов, например: <b>Фасилитация.</b>
                                По такому запросу будут найдены страницы, на которых встречается введенное слово.</p>
                            <p>Вы можете ввести более конкретный запрос, либо задать интересующий вас вопрос на email <a
                                        href="mailto:askme@ast-academy.ru">askme@ast-academy.ru</a>.</p>
                        </div>
                    <?php } ?>
                </div>
                <!-- мероприятия -->
                <div class="tab" id="tab-2">
                    <?php if (!empty($events)) { ?>
                        <div class="main_col">
                            <div class="all-events-cards">
                                <?= $this->render('_event_box', ['items' => $events]); ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="search-rezult__empty">
                            <p>Обычно запрос представляет из себя просто одно или несколько слов, например: <b>Фасилитация.</b>
                                По такому запросу будут найдены страницы, на которых встречается введенное слово.</p>
                            <p>Вы можете ввести более конкретный запрос, либо задать интересующий вас вопрос на email <a
                                        href="mailto:askme@ast-academy.ru">askme@ast-academy.ru</a>.</p>
                        </div>
                    <?php } ?>
                </div>
                <!-- ДПО -->
                <div class="tab" id="tab-8">
                    <?php if (!empty($eduprog)) { ?>
                        <div class="main_col">
                            <div id="all-eduprog-cards" class="all-events-cards">
                                <?= $this->render('_eduprog_box', ['items' => $eduprog, 'promo_items_ids' => []]); ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="search-rezult__empty">
                            <p>Обычно запрос представляет из себя просто одно или несколько слов, например: <b>Фасилитация.</b>
                                По такому запросу будут найдены страницы, на которых встречается введенное слово.</p>
                            <p>Вы можете ввести более конкретный запрос, либо задать интересующий вас вопрос на email <a
                                        href="mailto:askme@ast-academy.ru">askme@ast-academy.ru</a>.</p>
                        </div>
                    <?php } ?>
                </div>
                <!-- блоги -->
                <div class="tab" id="tab-3">
                    <?php if (!empty($blogs)) { ?>
                        <div class="blog-content">
                            <div class="blog-cards">
                                <?php foreach ($blogs as $blog) { ?>
                                    <div class="blog-card">
                                        <div class="blog-card-info-top">
                                            <?php if ($blog->author) { ?>
                                                <div class="blog-card-author-wrapper">
                                                    <a href="<?= $blog->author->getUrlPath(); ?>"
                                                       class="blog-card-author">
                                                        <div class="blog-card-author-img">
                                                            <img src="<?= $blog->author->profile->getThumb('image', 'main'); ?>"
                                                                 alt="<?= $blog->author->profile->halfname; ?>"
                                                                 loading="lazy">
                                                        </div>
                                                        <div class="blog-card-author-name"><?= $blog->author->profile->halfname; ?></div>
                                                    </a>
                                                    <!--<a href="#!" class="button-o small small2">Подписаться</a>-->
                                                </div>
                                            <?php } else { ?>
                                                <div class="blog-card-author-wrapper">
                                                    <a href="javascript:void(0);" class="blog-card-author">
                                                        <div class="blog-card-author-img">
                                                            <img src="/img/blog-page/academy.jpg"
                                                                 alt="Академия социальных технологий" loading="lazy">
                                                        </div>
                                                        <div class="blog-card-author-name">Академия социальных
                                                            технологий
                                                        </div>
                                                    </a>
                                                </div>
                                            <?php } ?>
                                            <?php /*if(!empty($blog->directionM) && !$blog->directionM->stels_direct) { ?>
                                            <a href="<?=$blog->directionM->getUrlPath()?>" class="blog-card-author-tag"><?=$blog->directionM->name?></a>
                                        <?php } */ ?>
                                        </div>
                                        <div class="blog-card-img">
                                            <a href="<?= $blog->getUrlPath(); ?>">
                                                <img src="<?= $blog->getThumb('image', 'main'); ?>"
                                                     alt="<?= str_replace('"', '&quot;', $blog->name); ?>"
                                                     loading="lazy">
                                            </a>
                                        </div>
                                        <div class="blog-card-info">
                                            <a href="<?= $blog->getUrlPath(); ?>"
                                               class="blog-card-title <?php if (!$blog->author || $blog->author->profile?->is_academy) { ?>verification<?php } ?>"><?= $blog->name; ?></a>
                                            <div class="blog-card-text-wrapper">
                                                <div class="blog-card-date"><?= \Yii::$app->formatter->asDate($blog->published, 'php:d.m.Y'); ?></div>
                                                <!--<div class="blog-card-viewed"><?= $blog->views; ?></div>-->
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="search-rezult__empty">
                            <p>Обычно запрос представляет из себя просто одно или несколько слов, например: <b>Фасилитация.</b>
                                По такому запросу будут найдены страницы, на которых встречается введенное слово.</p>
                            <p>Вы можете ввести более конкретный запрос, либо задать интересующий вас вопрос на email <a
                                        href="mailto:askme@ast-academy.ru">askme@ast-academy.ru</a>.</p>
                        </div>
                    <?php } ?>
                </div>
                <!-- проекты -->
                <div class="tab" id="tab-4">
                    <?php if (!empty($projects)) { ?>
                        <div class="blog-content">
                            <div class="blog-cards">
                                <?php foreach ($projects as $project) { ?>
                                    <div class="blog-card">
                                        <div class="blog-card-info-top">
                                            <?php if ($project->author) { ?>
                                                <div class="blog-card-author-wrapper">
                                                    <a href="<?= $project->author->getUrlPath(); ?>"
                                                       class="blog-card-author">
                                                        <div class="blog-card-author-img">
                                                            <img src="<?= $project->author->profile->getThumb('image', 'main'); ?>"
                                                                 alt="<?= $project->author->profile->halfname; ?>"
                                                                 loading="lazy">
                                                        </div>
                                                        <div class="blog-card-author-name"><?= $project->author->profile->halfname; ?></div>
                                                    </a>
                                                    <!--<a href="#!" class="button-o small small2">Подписаться</a>-->
                                                </div>
                                            <?php } else { ?>
                                                <div class="blog-card-author-wrapper">
                                                    <a href="javascript:void(0);" class="blog-card-author">
                                                        <div class="blog-card-author-img">
                                                            <img src="/img/blog-page/academy.jpg"
                                                                 alt="Академия социальных технологий" loading="lazy">
                                                        </div>
                                                        <div class="blog-card-author-name">Академия социальных
                                                            технологий
                                                        </div>
                                                    </a>
                                                </div>
                                            <?php } ?>
                                            <?php /*if(!empty($project->directionM) && !$project->directionM->stels_direct) { ?>
                                            <a href="<?=$project->directionM->getUrlPath()?>" class="blog-card-author-tag"><?=$project->directionM->name?></a>
                                        <?php } */ ?>
                                        </div>
                                        <div class="blog-card-img">
                                            <a href="<?= $project->getUrlPath(); ?>">
                                                <img src="<?= $project->getThumb('image', 'main'); ?>"
                                                     alt="<?= str_replace('"', '&quot;', $project->name); ?>"
                                                     loading="lazy">
                                            </a>
                                        </div>
                                        <div class="blog-card-info">
                                            <a href="<?= $project->getUrlPath(); ?>"
                                               class="blog-card-title <?php if (!$project->author || $project->author->profile?->is_academy) { ?>verification<?php } ?>"><?= $project->name; ?></a>
                                            <div class="blog-card-text-wrapper">
                                                <div class="blog-card-date"><?= \Yii::$app->formatter->asDate($project->published, 'php:d.m.Y'); ?></div>
                                                <!--<div class="blog-card-viewed"><?= $project->views; ?></div>-->
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="search-rezult__empty">
                            <p>Обычно запрос представляет из себя просто одно или несколько слов, например: <b>Фасилитация.</b>
                                По такому запросу будут найдены страницы, на которых встречается введенное слово.</p>
                            <p>Вы можете ввести более конкретный запрос, либо задать интересующий вас вопрос на email <a
                                        href="mailto:askme@ast-academy.ru">askme@ast-academy.ru</a>.</p>
                        </div>
                    <?php } ?>
                </div>
                <div class="tab" id="tab-5">
                    <?php if (!empty($materials)) { ?>
                        <div class="blog-content">
                            <div class="blog-cards">
                                <?php foreach ($materials as $material) { ?>
                                    <div class="blog-card">
                                        <div class="blog-card-info-top">
                                            <?php if ($material->author) { ?>
                                                <div class="blog-card-author-wrapper">
                                                    <a href="<?= $material->author->getUrlPath(); ?>"
                                                       class="blog-card-author">
                                                        <div class="blog-card-author-img">
                                                            <img src="<?= $material->author->profile->getThumb('image', 'main'); ?>"
                                                                 alt="<?= $material->author->profile->halfname; ?>"
                                                                 loading="lazy">
                                                        </div>
                                                        <div class="blog-card-author-name"><?= $material->author->profile->halfname; ?></div>
                                                    </a>
                                                    <!--<a href="#!" class="button-o small small2">Подписаться</a>-->
                                                </div>
                                            <?php } else { ?>
                                                <div class="blog-card-author-wrapper">
                                                    <a href="javascript:void(0);" class="blog-card-author">
                                                        <div class="blog-card-author-img">
                                                            <img src="/img/blog-page/academy.jpg"
                                                                 alt="Академия социальных технологий" loading="lazy">
                                                        </div>
                                                        <div class="blog-card-author-name">Академия социальных
                                                            технологий
                                                        </div>
                                                    </a>
                                                </div>
                                            <?php } ?>
                                            <?php /*if(!empty($material->directionM) && !$material->directionM->stels_direct) { ?>
                                            <a href="<?=$material->directionM->getUrlPath()?>" class="blog-card-author-tag"><?=$material->directionM->name?></a>
                                        <?php } */ ?>
                                        </div>
                                        <div class="blog-card-img">
                                            <a href="<?= $material->getUrlPath(); ?>">
                                                <img src="<?= $material->getThumb('image', 'main'); ?>"
                                                     alt="<?= str_replace('"', '&quot;', $material->name); ?>"
                                                     loading="lazy">
                                            </a>
                                        </div>
                                        <div class="blog-card-info">
                                            <a href="<?= $material->getUrlPath(); ?>"
                                               class="blog-card-title <?php if (!$material->author || $material->author->profile?->is_academy) { ?>verification<?php } ?>"><?= $material->name; ?></a>
                                            <div class="blog-card-text-wrapper">
                                                <div class="blog-card-date"><?= \Yii::$app->formatter->asDate($material->published, 'php:d.m.Y'); ?></div>
                                                <!--<div class="blog-card-viewed"><?= $material->views; ?></div>-->
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="search-rezult__empty">
                            <p>Обычно запрос представляет из себя просто одно или несколько слов, например: <b>Фасилитация.</b>
                                По такому запросу будут найдены страницы, на которых встречается введенное слово.</p>
                            <p>Вы можете ввести более конкретный запрос, либо задать интересующий вас вопрос на email <a
                                        href="mailto:askme@ast-academy.ru">askme@ast-academy.ru</a>.</p>
                        </div>
                    <?php } ?>
                </div>
                <!-- новости -->
                <div class="tab" id="tab-7">
                    <?php if (!empty($news)) { ?>
                        <div class="blog-content">
                            <div class="blog-cards">
                                <?php foreach ($news as $new) { ?>
                                    <div class="blog-card">
                                        <div class="blog-card-info-top">
                                            <?php if ($new->author) { ?>
                                                <div class="blog-card-author-wrapper">
                                                    <a href="<?= $new->author->getUrlPath(); ?>"
                                                       class="blog-card-author">
                                                        <div class="blog-card-author-img">
                                                            <img src="<?= $new->author->profile->getThumb('image', 'main'); ?>"
                                                                 alt="<?= $new->author->profile->halfname; ?>"
                                                                 loading="lazy">
                                                        </div>
                                                        <div class="blog-card-author-name"><?= $new->author->profile->halfname; ?></div>
                                                    </a>
                                                    <!--<a href="#!" class="button-o small small2">Подписаться</a>-->
                                                </div>
                                            <?php } else { ?>
                                                <div class="blog-card-author-wrapper">
                                                    <a href="javascript:void(0);" class="blog-card-author">
                                                        <div class="blog-card-author-img">
                                                            <img src="/img/blog-page/academy.jpg"
                                                                 alt="Академия социальных технологий" loading="lazy">
                                                        </div>
                                                        <div class="blog-card-author-name">Академия социальных
                                                            технологий
                                                        </div>
                                                    </a>
                                                </div>
                                            <?php } ?>
                                            <?php /*if(!empty($new->directionM) && !$new->directionM->stels_direct) { ?>
                                            <a href="<?=$new->directionM->getUrlPath()?>" class="blog-card-author-tag"><?=$new->directionM->name?></a>
                                        <?php } */ ?>
                                        </div>
                                        <div class="blog-card-img">
                                            <a href="<?= $new->getUrlPath(); ?>">
                                                <img src="<?= $new->getThumb('image', 'main'); ?>"
                                                     alt="<?= str_replace('"', '&quot;', $new->name); ?>"
                                                     loading="lazy">
                                            </a>
                                        </div>
                                        <div class="blog-card-info">
                                            <a href="<?= $new->getUrlPath(); ?>"
                                               class="blog-card-title <?php if (!$new->author || $new->author->profile?->is_academy) { ?>verification<?php } ?>"><?= $new->name; ?></a>
                                            <div class="blog-card-text-wrapper">
                                                <div class="blog-card-date"><?= \Yii::$app->formatter->asDate($new->published, 'php:d.m.Y'); ?></div>
                                                <!--<div class="blog-card-viewed"><?= $new->views; ?></div>-->
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="search-rezult__empty">
                            <p>Обычно запрос представляет из себя просто одно или несколько слов, например: <b>Фасилитация.</b>
                                По такому запросу будут найдены страницы, на которых встречается введенное слово.</p>
                            <p>Вы можете ввести более конкретный запрос, либо задать интересующий вас вопрос на email <a
                                        href="mailto:askme@ast-academy.ru">askme@ast-academy.ru</a>.</p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
<?= \app\modules\queries\widgets\queries\QueriesWidget::widget(); ?>
<?= \app\modules\pages\widgets\ordercreate\OrderCreateWidget::widget(); ?>
<?= $this->registerCssFile('/css/style-blog.css', ['depends' => [app\assets\AppAsset::class]]); ?>