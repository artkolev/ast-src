<?php if (!empty($items)) { ?>
    <?php
    // $first = array_shift($items);
    if (!empty($first)) {
        if (empty($image_first_mobile)) {
            $image_first_mobile = $image_first;
        }
        echo \app\modules\lenta\widgets\lenta\LentaWidget::widget(['image' => $image_first, 'image_mobile' => $image_first_mobile, 'item' => $first, 'view' => 'big']);
    }
    /*$block = array_shift($blocks);
    if($block){
        echo \app\modules\lenta\widgets\lentablock\LentaBlockWidget::widget(['block' => $block]);
    }*/
    ?>
    <?php foreach ($items as $i => $item) { ?>
        <?= \app\modules\lenta\widgets\lenta\LentaWidget::widget(['item' => $item]); ?>
        <?php if ($i % 4 == 3) { ?>
            <?php
            $block = array_shift($blocks);
            if ($block) {
                echo \app\modules\lenta\widgets\lentablock\LentaBlockWidget::widget(['block' => $block]);
            }
            ?>
        <?php } ?>
    <?php } ?>

    <?= \app\modules\lenta\widgets\ads\LentaAdsWidget::widget(['ads' => $ads]); ?>
<?php } ?>
