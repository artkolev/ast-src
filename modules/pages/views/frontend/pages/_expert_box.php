<?php

use app\helpers\MainHelper;
use app\modules\pages\models\ExpertCatalog;
use yii\helpers\Html;

$catalog = ExpertCatalog::find()->where(['model' => ExpertCatalog::class, 'visible' => 1])->one();

/* используется только для страниц каталога. Если надо вы другом месте - см. фильтры по специализациям. */
?>
<div class="experts_box">
    <?php if (!empty($items)) {
        foreach ($items as $user) { ?>
            <div class="expert_item">
                <div class="expert_item-img_box">
                    <?= Html::a(Html::img($user->profile->getThumb('image', 'prev'), ['alt' => $user->profile->fullname]), $user->getUrlPath(), ['class' => 'expert_item-img']); ?>
                    <?= app\widgets\shield\ShieldWidget::widget(['user' => $user]); ?>
                </div>
                <div class="expert_item-info">
                    <h5><a href="<?= $user->getUrlPath(); ?>"><?= $user->profile->halfname; ?></a></h5>
                    <?php /* <div class="expert_item-city"><?=$user->profile->city->name?></div> */ ?>
                    <?php /*if (!is_null($user->directionM) && !$user->directionM->stels_direct) { ?>
                        <div class="expert_item-direction"><?=$user->directionM->name?></div>
                    <?php }*/ ?>
                    <div class="expert_item-desc">
                        <a href="<?= $user->getUrlPath(); ?>"><?= $user->profile->about_myself; ?></a>
                    </div>
                    <div class="expert_item-tags">
                        <?php foreach ($user->competence as $key => $competence) {
                            $class = 'tag set_filter';
                            if ($key > 1) {
                                $class = 'tag set_filter hide_mobile';
                            }
                            if ($key > 5) {
                                $class = 'tag set_filter hide_mobile hide';
                            }
                            echo Html::a('<b class="tag-hovered">' . MainHelper::mb_ucfirst(mb_strtolower($competence->name, 'UTF-8')) . '</b><span>' . MainHelper::mb_ucfirst(mb_strtolower($competence->name, 'UTF-8')) . '</span></a>', $catalog->getUrlPath() . '?competence[]=' . $competence->id, ['class' => $class, 'data-tagid' => $competence->id, 'data-tagname' => $competence->name]);
                        } ?>
                        <a href="<?= $user->getUrlPath(); ?>"
                           class="tag more taghref"><span>Ещё +<u><!-- js --></u></span></a>
                    </div>
                    <div class="expert_item-buttons">
                        <!-- <a href="#" class="button-o small academ_connect" data-academ="<?= $user->id; ?>">Связаться</a> -->
                        <!-- <a href="<?= $user->getUrlPath(); ?>" class="button more">Подробнее</a> -->
                        <a href="<?= $user->getUrlPath(); ?>" class="button-o small">Подробнее</a>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
    <!-- <a href="" class="wide_banner text_white" style="background-image: url(img/wide_banner.jpg);">
        <div class="wide_banner-info">
            <h2>Точки на временной оси</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            <div class="wide_banner-undertext">
                <span>Сант-Петербург, БКЗ Октябрьский</span> <span>20 мая, 19:00</span>
            </div>
        </div>
    </a> -->
</div>
<!-- <div class="paginate_box">
	<div class="pg_prev"><a href="">← Предыдущая страница</a></div>
	<ul class="paginate">
		<li><a href="">1</a></li>
		<li class="pg_current"><a href="">2</a></li>
		<li><a href="">3</a></li>
		<li><a href="">4</a></li>
		<li class="pg_space">...</li>
		<li><a href="">12</a></li>
	</ul>
	<div class="pg_next"><a href="">Следующая страница →</a></div>
</div> -->