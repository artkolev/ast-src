<?php

use yii\helpers\Html;

?>
    <main class="sec content_sec">
        <div class="container wide">

            <h1><?= $model->getNameForView(); ?></h1>
            <div class="subheader"><?= $model->content; ?></div>

            <!-- <form action="" method="post" class="directions_search_box">
                <div class="search_flex noMobile1024">
                    <div class="ip_cell">
                        <input type="text" class="input_text ip_search" placeholder="Введите название мероприятия, кафедры, города, организатора" />
                        <button class="button-o button-search" type="submit">Начать поиск</button>
                    </div>
                </div>
            </form> -->
            <?php if ($directs) { ?>
                <div class="directions_box">
                    <?php foreach ($directs as $direct) { ?>
                        <div class="direction_item">
                            <div class="relative">
                                <div>
                                    <h3><?= Html::a($direct->name, $direct->getUrlPath()); ?></h3>
                                    <div class="direction_item-desc">
                                        <div><?= $direct->anons; ?></div>
                                    </div>
                                </div>
                                <?php
                                $directs_user_data = $direct->mainusers;
                                if (!empty($directs_user_data['items'])) { ?>
                                    <div class="direction_item-persons">
                                        <?php
                                        foreach ($directs_user_data['items'] as $user) {
                                            echo Html::a(Html::img($user->profile->getThumb('image', 'direct'), ['alt' => $user->profile->fullname]), $user->getUrlPath(), ['class' => 'person_item_small']);
                                        }
                                        if ($directs_user_data['count'] > 0) {
                                            echo Html::a('+' . $directs_user_data['count'], $direct->getUrlPath(), ['class' => 'person_item_small more']);
                                        }
                                        ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                </div>
            <?php } ?>
        </div>
    </main>

<?php // echo \app\modules\describeyourtask\widgets\describeyourtask\DescribeyourtaskWidget::widget();?>

    <div class="modal" id="describeyourtask_modal">
        <div class="modal_content">
            <a href="#" class="modal_close" data-fancybox-close>x</a>
            <script data-b24-form="inline/11/85px27" data-skip-moving="true">(function (w, d, u) {
                    var s = d.createElement('script');
                    s.async = true;
                    s.src = u + '?' + (Date.now() / 180000 | 0);
                    var h = d.getElementsByTagName('script')[0];
                    h.parentNode.insertBefore(s, h);
                })(window, document, 'https://crm.ast-academy.ru/upload/crm/form/loader_11_85px27.js');</script>
        </div>
        <div class="modal_overlay"></div>
    </div>

<?php
$js = <<<JS
    $('body').on('click','.describeyourtask_show', function(e){
		e.preventDefault();
		$.fancybox.open({
            src: '#describeyourtask_modal',
            type: 'inline'
        });
	});
JS;
$this->registerJs($js);
?>