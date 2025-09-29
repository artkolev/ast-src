<?php

use app\modules\eduprog\models\Eduprog;
use app\modules\events\models\Events;
use app\modules\pages\models\ExpertCatalog;
use app\modules\pages\models\ExporgCatalog;
use app\modules\service\models\Service;
use app\modules\users\models\UserAR;
use app\modules\users\models\UserDirection;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var UserAR $user
 * @var Events[] $events
 * @var array $eventsPages
 * @var Service[] $services
 * @var array $servicesPages
 * @var Eduprog[] $eduprogs
 * @var array $eduprogsPages
 * @var \app\modules\lenta\models\Blog[] $blogs
 * @var array $blogsPages
 * @var \app\modules\lenta\models\Material[] $materials
 * @var array $materialsPages
 * @var \app\modules\lenta\models\News[] $news
 * @var array $newsPages
 * @var \app\modules\lenta\models\Project[] $portfolio
 * @var array $portfolioPages
 * @var string $userRole
 * @var ExporgCatalog|ExpertCatalog $catalog
 */


?>

    <section class="sec section-page section-expert-page gray-bg">
        <div class="container wide">
            <div class="expert-page-wrapper">
                <aside class="expert-page-aside">
                    <div class="expert-page-menu">
                        <div class="expert-page-menu-list">
                            <?php if (!empty($user)) { ?>
                                <a href="#profile" class="expert-page-menu-link anchor">Профиль</a>
                            <?php } ?>
                            <?php if (!empty($events)) { ?>
                                <a href="#events" class="expert-page-menu-link anchor">Мероприятия</a>
                            <?php } ?>
                            <?php if (!empty($services)) { ?>
                                <a href="#services" class="expert-page-menu-link anchor">Услуги</a>
                            <?php } ?>
                            <?php if (!empty($eduprogs) && $userRole == 'exporg') { ?>
                                <a href="#eduprogs" class="expert-page-menu-link anchor">Образовательные программы</a>
                            <?php } ?>
                            <?php if (!empty($blogs)) { ?>
                                <a href="#blogs" class="expert-page-menu-link anchor">Блог</a>
                            <?php } ?>
                            <?php if (!empty($materials)) { ?>
                                <a href="#materials" class="expert-page-menu-link anchor">База знаний</a>
                            <?php } ?>
                            <?php if (!empty($news)) { ?>
                                <a href="#news" class="expert-page-menu-link anchor">Новости</a>
                            <?php } ?>
                            <?php if (!empty($portfolio)) { ?>
                                <a href="#portfolio" class="expert-page-menu-link anchor">Портфолио</a>
                            <?php } ?>
                        </div>
                    </div>
                </aside>
                <div class="expert-page-content">
                    <!--Профиль-->
                    <?php if (!empty($user)) { ?>
                        <div class="expert-page-block">
                            <div class="expert-page-block-wrapper expert-page-block-expert">
                                <div class="expert-page-info-block">
                                    <div class="expert-page-img-wrapper">
                                        <div class="expert-page-img">
                                            <?= Html::img($user->profile->getThumb('image', 'profile'), ['alt' => $user->profile->fullname]); ?>
                                        </div>
                                        <?php if ($user->profile->honorablesovet != 1) { ?>
                                            <a href="#" class="button blue expert-btn academ_connect"
                                               data-academ="<?= $user->id; ?>">Связаться</a>
                                        <?php } ?>
                                    </div>
                                    <div class="expert-page-info">
                                        <h1 class="expert-page-name"><?= $user->getNameForView(); ?></h1>
                                        <div class="expert-page-location"><?= mb_convert_case($user->profile->city->name, MB_CASE_TITLE, 'UTF-8'); ?></div>

                                        <div class="expert-page-medals">
                                            <?php if ($user->expertTitles) { ?>
                                                <?php foreach ($user->expertTitles as $role => $expertTitle) { ?>
                                                    <div class="expert-page-medal">
                                                        <div class="expert-page-medal-img">
                                                            <img src="<?= UserDirection::roleImage($role); ?>"
                                                                 alt="<?= UserDirection::getRoleList()[$role]; ?>">
                                                        </div>
                                                        <div class="expert-page-medal-tooltip">
                                                            <?php foreach ($expertTitle as $item) { ?>
                                                                <div class="expert-page-medal-tooltip-text"><?= UserDirection::getRoleList()[$role]; ?></div>
                                                                <a href="<?= $item->url; ?>"
                                                                   class="expert-page-medal-tooltip-link"><?= $item->name; ?></a>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>

                                            <?php if ($userRole == 'exporg' && (bool)$user->organization->license_service && !empty($user->organization->about)) { ?>
                                                <div class="expert-page-medal">
                                                    <div class="expert-page-medal-img">
                                                        <img src="/img/medal-learn.svg"
                                                             alt="<?= UserDirection::getRoleList()[$userRole]; ?>">
                                                    </div>
                                                    <div class="expert-page-medal-tooltip">
                                                        <div class="expert-page-medal-tooltip-text">
                                                            <?= $user->organization->about; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="expert-page-text"><?= $user->profile->about_myself; ?></div>
                                <a href="#profile" class="expert-page-details-link anchor">Подробнее об опыте и
                                    компетенциях</a>
                            </div>
                        </div>
                        <div id="profile" class="expert-page-block">
                            <h3 class="expert-page-block-title">Профиль</h3>
                            <div class="expert-page-block-content">
                                <?php if ($user->expertTitles) { ?>
                                    <?php foreach ($user->expertTitles as $role => $expertTitle) { ?>
                                        <div class="expert-page-block-wrapper">
                                            <div class="expert-page-medal-big">
                                                <div class="expert-page-medal-big-img">
                                                    <img src="<?= UserDirection::roleImage($role); ?>"
                                                         alt="<?= UserDirection::getRoleList()[$role]; ?>">
                                                </div>
                                                <div class="expert-page-medal-big-info">
                                                    <?php foreach ($expertTitle as $item) { ?>
                                                        <div class="expert-page-medal-big-text"><?= UserDirection::getRoleList()[$role]; ?></div>
                                                        <a href="<?= $item->url; ?>"
                                                           class="expert-page-medal-big-link"><?= $item->name; ?></a>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>

                                <?php if (!empty($user->profile->history)) { ?>
                                    <div class="expert-page-block-wrapper">
                                        <div class="expert-page-text-title"><?= !empty($userRole == 'exporg') ? 'Об организации' : 'Личная история'; ?></div>
                                        <div class="expert-page-text"><?= $user->profile->history; ?></div>
                                    </div>
                                <?php } ?>
                                <?php if ($userRole == 'exporg' && (bool)$user->organization->license_service && !empty($user->organization->about)) { ?>
                                    <div class="expert-page-block-wrapper">
                                        <div class="expert-page-text-title">Сведения об образовательной организации
                                        </div>
                                        <div class="expert-page-text">
                                            <?= $user->organization->about; ?>
                                        </div>
                                    </div>
                                <?php } ?>

                                <!--Карьера-->
                                <?php if ($userRole != 'exporg') { ?>
                                    <?php if (!empty($user->careerFront)) { ?>
                                        <div class="expert-page-block-wrapper">
                                            <div class="expert-page-text-title">Карьера</div>
                                            <?php foreach ($user->careerFront as $career) { ?>
                                                <div class="expert-page-dates">
                                                    <?php
                                                    if ($career->by_realtime) {
                                                        $career->work_to = 'По настоящее время';
                                                    }
                                                    if (!empty($career->work_from) or !empty($career->work_to)) {
                                                        $array = [$career->work_from, $career->work_to];
                                                        $array = array_diff($array, ['']); ?>
                                                        <div class="expert-page-dates-date"><?= implode(' &mdash; ', $array); ?></div>
                                                    <?php } ?>
                                                    <div class="expert-page-dates-title"><?= $career->name; ?></div>
                                                    <div class="expert-page-dates-name"><?= $career->office; ?></div>
                                                    <?php if (!empty($career->achiev)) { ?>
                                                        <div class="expert-page-dates-list">
                                                            <div class="expert-page-dates-element"><?= $career->achiev; ?></div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                    <!--Образование-->
                                    <?php if (!empty($user->educationFront)) { ?>
                                        <div class="expert-page-block-wrapper">
                                            <div class="expert-page-text-title">Образование</div>
                                            <?php foreach ($user->educationFront as $education) { ?>
                                                <div class="expert-page-dates">
                                                    <?php
                                                    if ($education->by_realtime) {
                                                        $education->study_to = 'По настоящее время';
                                                    }
                                                    if (!empty($education->study_from) or !empty($education->study_to)) {
                                                        $array = [$education->study_from, $education->study_to];
                                                        $array = array_diff($array, ['']); ?>
                                                        <div class="expert-page-dates-date"><?= implode(' &mdash; ', $array); ?></div>
                                                    <?php } ?>
                                                    <div class="expert-page-dates-title"><?= $education->name; ?></div>
                                                    <div class="expert-page-dates-name"><?= $education->speciality; ?></div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                                <!--Ключевые слова-->
                                <?php if (!empty($user->keywords)) { ?>
                                    <div class="expert-page-block-wrapper">
                                        <div class="expert-page-text-title">Направления</div>
                                        <div class="expert_item-tags">
                                            <?php foreach ($user->keywords as $key => $keyword) { ?>
                                                <a class="tag"
                                                   href="<?= $catalog->getUrlPath() . '?keyword=' . urlencode($keyword->name); ?>"
                                                   data-tagid="<?= $key; ?>"
                                                   data-tagname="<?= $keyword->name; ?>" <?= ($key > 10) ? 'style="display: none;"' : ''; ?> >
                                                    <b class="tag-hovered"><?= $keyword->name; ?>
                                                    </b><span><?= $keyword->name; ?></span>
                                                </a>
                                                <?php if ($key == 10) { ?>
                                                    <?php $count = count($user->keywords); ?>
                                                    <?php if ($key < $count) { ?>
                                                        <a class="tag js-more-tags" href="#"><b class="tag-hovered">Еще
                                                                +<?= $count - $key; ?></b><span>Еще +<?= $count - $key; ?></span></a>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <!--Решаемые задачи-->
                                <?php if (!empty($user->solvtask)) { ?>
                                    <div class="expert-page-block-wrapper">
                                        <div class="expert-page-text-title">Решаемые задачи</div>
                                        <div class="expert_item-tags">
                                            <?php foreach ($user->solvtask as $key => $solvtask) { ?>
                                                <a class="tag"
                                                   href="<?= $catalog->getUrlPath() . '?task[]=' . $solvtask->id; ?>"
                                                   data-tagid="<?= $key; ?>"
                                                   data-tagname="<?= $solvtask->name; ?>" <?= ($key > 10) ? 'style="display: none;"' : ''; ?> >
                                                    <b class="tag-hovered"><?= $solvtask->name; ?>
                                                    </b><span><?= $solvtask->name; ?></span>
                                                </a>
                                                <?php if ($key == 10) { ?>
                                                    <?php $count = count($user->solvtask); ?>
                                                    <?php if ($key < $count) { ?>
                                                        <a class="tag js-more-tags" href="#"><b class="tag-hovered">Еще
                                                                +<?= $count - $key; ?></b><span>Еще +<?= $count - $key; ?></span></a>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <!--Специализации-->
                                <?php if (!empty($user->competence)) { ?>
                                    <div class="expert-page-block-wrapper">
                                        <div class="expert-page-text-title">Специализации</div>
                                        <div class="expert_item-tags">
                                            <?php foreach ($user->competence as $key => $competence) { ?>
                                                <a class="tag"
                                                   href="<?= $catalog->getUrlPath() . '?competence[]=' . $competence->id; ?>"
                                                   data-tagid="<?= $key; ?>"
                                                   data-tagname="<?= $competence->name; ?>" <?= ($key > 10) ? 'style="display: none;"' : ''; ?> >
                                                    <b class="tag-hovered"><?= $competence->name; ?>
                                                    </b><span><?= $competence->name; ?></span>
                                                </a>
                                                <?php if ($key == 10) { ?>
                                                    <?php $count = count($user->competence); ?>
                                                    <?php if ($key < $count) { ?>
                                                        <a class="tag js-more-tags" href="#"><b class="tag-hovered">Еще
                                                                +<?= $count - $key; ?></b><span>Еще +<?= $count - $key; ?></span></a>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <!--Мероприятия-->
                    <?php if (!empty($events)) { ?>
                        <?php $eventsLinks = $eventsPages->getLinks(); ?>
                        <div id="events" class="expert-page-block">
                            <h3 class="expert-page-block-title">Мероприятия</h3>
                            <div class="expert-page-block-content">
                                <div id="events-cards-load" class="expert-page-three-cards">
                                    <?= $this->render('user/_events_item', [
                                            'items' => $events
                                    ]); ?>
                                </div>
                                <div id="events-cards-pager">
                                    <?php if (isset($eventsLinks['next'])) { ?>
                                        <a href="<?= $eventsLinks['next']; ?>" data-block="events"
                                           class="button long w100 mt20 expert_show_more_btn">Загрузить еще</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>


                    <!--Услуги-->
                    <?php if (!empty($services)) { ?>
                        <?php $servicesLinks = $servicesPages->getLinks(); ?>
                        <div id="services" class="expert-page-block">
                            <h3 class="expert-page-block-title">Услуги</h3>
                            <div class="expert-page-block-content">
                                <div class="all-services-list">
                                    <div id="services-cards-load" class="all-services-cards">
                                        <?= $this->render('user/_services_item', [
                                                'items' => $services
                                        ]); ?>
                                    </div>
                                </div>
                                <div id="services-cards-pager">
                                    <?php if (isset($servicesLinks['next'])) { ?>
                                        <a href="<?= $servicesLinks['next']; ?>" data-block="services"
                                           class="button long w100 mt20 expert_show_more_btn">Загрузить еще</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!--Образовательные программы-->
                    <?php if (!empty($eduprogs)) { ?>
                        <?php $eduprogsLinks = $eduprogsPages->getLinks(); ?>
                        <div id="eduprogs" class="expert-page-block">
                            <h3 class="expert-page-block-title">Образовательные программы</h3>
                            <div class="expert-page-block-content">
                                <div id="eduprogs-cards-load" class="expert-page-three-cards">
                                    <?= $this->render('user/_eduprogs_item', [
                                            'items' => $eduprogs
                                    ]); ?>
                                </div>
                                <div id="eduprogs-cards-pager">
                                    <?php if (isset($eduprogsLinks['next'])) { ?>
                                        <a href="<?= $eduprogsLinks['next']; ?>" data-block="eduprogs"
                                           class="button long w100 mt20 expert_show_more_btn">Загрузить еще</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!--Блог-->
                    <?php if (!empty($blogs)) { ?>
                        <?php $blogsLinks = $eduprogsPages->getLinks(); ?>
                        <div id="blogs" class="expert-page-block">
                            <h3 class="expert-page-block-title">Блог</h3>
                            <div class="expert-page-block-content">
                                <div id="blogs-cards-load" class="expert-page-three-cards">
                                    <?= $this->render('user/_catalog_item', [
                                            'items' => $blogs
                                    ]); ?>
                                </div>
                                <div id="blogs-cards-pager">
                                    <?php if (isset($blogsLinks['next'])) { ?>
                                        <a href="<?= $blogsLinks['next']; ?>" data-block="blogs"
                                           class="button long w100 mt20 expert_show_more_btn">Загрузить еще</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!--База знаний-->
                    <?php if (!empty($materials)) { ?>
                        <?php $materialsLinks = $materialsPages->getLinks(); ?>
                        <div id="materials" class="expert-page-block">
                            <h3 class="expert-page-block-title">База знаний</h3>
                            <div class="expert-page-block-content">
                                <div id="materials-cards-load" class="expert-page-three-cards">
                                    <?= $this->render('user/_catalog_item', [
                                            'items' => $materials
                                    ]); ?>
                                </div>
                                <div id="materials-cards-pager">
                                    <?php if (isset($materialsLinks['next'])) { ?>
                                        <a href="<?= $materialsLinks['next']; ?>" data-block="materials"
                                           class="button long w100 mt20 expert_show_more_btn">Загрузить еще</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!--Новости-->
                    <?php if (!empty($news)) { ?>
                        <?php $newsLinks = $newsPages->getLinks(); ?>
                        <div id="news" class="expert-page-block">
                            <h3 class="expert-page-block-title">Новости</h3>
                            <div class="expert-page-block-content">
                                <div id="news-cards-load" class="expert-page-three-cards">
                                    <?= $this->render('user/_catalog_item', [
                                            'items' => $news
                                    ]); ?>
                                </div>
                                <div id="news-cards-pager">
                                    <?php if (isset($newsLinks['next'])) { ?>
                                        <a href="<?= $newsLinks['next']; ?>" data-block="news"
                                           class="button long w100 mt20 expert_show_more_btn">Загрузить еще</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!--Портфолио-->
                    <?php if (!empty($portfolio)) { ?>
                        <?php $portfolioLinks = $portfolioPages->getLinks(); ?>
                        <div id="portfolio" class="expert-page-block">
                            <h3 class="expert-page-block-title">Портфолио</h3>
                            <div class="expert-page-block-content">
                                <div id="portfolio-cards-load" class="expert-page-three-cards">
                                    <?= $this->render('user/_catalog_item', [
                                            'items' => $portfolio
                                    ]); ?>
                                </div>
                                <div id="portfolio-cards-pager">
                                    <?php if (isset($portfolioLinks['next'])) { ?>
                                        <a href="<?= $portfolioLinks['next']; ?>" data-block="portfolio"
                                           class="button long w100 mt20 expert_show_more_btn">Загрузить еще</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="modal" id="fail_expert_modal">
            <div class="modal_content">
                <a href="#" class="modal_close">x</a>
                <div class="success_box">
                    <div class="modal_title">Ошибка получения данных</div>
                    <p>К сожалению, по вашему запросу ничего не найдено</p>
                    <div class="modal_buttons">
                        <a href="#" class="button small close_modal">ОК</a>
                    </div>
                </div>

            </div>
            <div class="modal_overlay"></div>
        </div>
    </section>


<?= \app\modules\banner\widgets\banner\PageBannerWidget::widget(['inner_page' => 'expert', 'id' => $user->id, 'mobile' => '1']); ?>

<?= \app\modules\queries\widgets\queries\QueriesWidget::widget(); ?>
<?= \app\modules\feedacadem\widgets\feedacadem\FeedacademWidget::widget(); ?>
<?= \app\modules\pages\widgets\ordercreate\OrderCreateWidget::widget(); ?>
<?php
$this->registerCssFile('/css/style-blog.css', ['depends' => [app\assets\AppAsset::class]]);

$js = <<<JS
	$('body').on('click','.academ_connect', function(e){
		e.preventDefault();
		$('#feedacadem-academ').val($(this).data('academ'));
		modalPos('#feedacadem_modal');
	});

	$('body').on('click','.expert_show_more_btn',function(e){
		e.preventDefault();
		let new_url = $(this).attr('href'),
			block = $(this).attr('data-block'),
			cardsLoad = $('#' + block + '-cards-load'),
			cardsPager = $('#' + block + '-cards-pager');
        
			$.ajax({
				type: 'GET',
				url: new_url,
				data: {pathId: block},
				processData: true,
				dataType: 'json',
				success: function(data) {
					if (data.status == 'success') {
						cardsLoad.append(data.html);
						cardsPager.html(data.pager);
					} else {
                        cardsPager.hide();
                        
						// показать модалку с ошибкой
						modalPos('#fail_expert_modal');
					}
				}
			});
		return false;

	});

	$('body').on('click', '.js-more-tags', function(e) {
        e.preventDefault();
        $(this).hide().siblings('.tag').show();
	});
JS;
$this->registerJs($js);
?>