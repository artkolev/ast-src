<?php

use yii\helpers\Html;

?>
    <main class="sec content_sec">
        <div class="container middle">
            <h1><?= $model->getNameForView(); ?></h1>
            <div class="subheader">
                <?= $model->content; ?>
            </div>

            <form class="directions_search_box">
                <div class="search_flex noMobile1024">
                    <div class="ip_cell">
                        <input type="text" name="q" value="<?= $search_text; ?>" class="input_text ip_search"
                               placeholder="Введите название новости, кафедры, организатора"/>
                        <button class="button-o button-search" type="submit">Начать поиск</button>
                    </div>
                </div>
                <div class="search_flex">
                    <!-- <div class="custom_dropdown_box">
                        <a href="#" class="custom_dropdown-link" data-placeholder="Поиск по городу проведения"></a>
                        <div class="custom_dropdown-list checkboxes_js mScrollbarCustom simplebar">
                            <div class="custom_dropdown-row">
                                <input class="rd custom_dropdown-choice" type="radio" name="city" value="Долголетие" />
                                <label>Долголетие</label>
                            </div>
                            <div class="custom_dropdown-row">
                                <input class="rd custom_dropdown-choice" type="radio" name="city" value="Игропрактики" />
                                <label>Игропрактики</label>
                            </div>
                            <div class="custom_dropdown-row">
                                <input class="rd custom_dropdown-choice" type="radio" name="city" value="Коучинг" />
                                <label>Коучинг</label>
                            </div>
                        </div>
                    </div> -->
                    <?php if (!empty($directions)) { ?>
                        <div class="custom_dropdown_box">
                            <a href="#" class="custom_dropdown-link" data-placeholder="Поиск по кафедрам"></a>
                            <div class="custom_dropdown-list checkboxes_js mScrollbarCustom simplebar">
                                <?php foreach ($directions as $direction) { ?>
                                    <div class="custom_dropdown-row">
                                        <input class="rd custom_dropdown-choice" <?= ($direct == $direction->id) ? 'checked="checked"' : ''; ?>
                                               type="radio" name="direct" value="<?= $direction->id; ?>"/>
                                        <label><?= $direction->name; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </form>
            <?php if (!empty($items)) { ?>
                <div class="plitka_box">
                    <?php
                    foreach ($items as $key => $item) { ?>
                        <div class="plitka_item separate_image noOverflow-hover">
                            <?= Html::a(Html::img($item->getThumb('image', 'main'), ['alt' => $item->name, 'class' => 'plitka_item-img']), $item->getUrlPath()); ?>
                            <div class="plitka_item-info">
                                <h2><?= Html::a($item->name, $item->getUrlPath()); ?></h2>
                                <div><?= \Yii::$app->formatter->asDate($item->published, 'php:j F'); ?></div>
                                <?php if (!empty($item->tags)) { ?>
                                    <div class="plitka_item-tags visible-2-lines">
                                        <?php foreach ($item->tags as $key => $tag) {
                                            if ($key > 4) {
                                                continue;
                                            }
                                            echo Html::a('<b class="tag-hovered">' . $tag->name . '</b><span>' . $tag->name . '</span></a>', $model->getUrlPath() . '?tag=' . urlencode($tag->name), ['class' => 'tag']);
                                        } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <p>Новости не найдены</p>
            <?php } ?>
        </div>
    </main>
<?php
$this->registerJsFile('/js/filters.js', ['depends' => [app\assets\AppAsset::class]]);

$js = <<<JS
	$('body').on('click','.custom_dropdown-choice', function(e){
		$('form.directions_search_box').submit();
	});
JS;
$this->registerJs($js);
?>