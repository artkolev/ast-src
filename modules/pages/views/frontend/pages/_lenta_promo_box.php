<?php if (!empty($items)) { ?>
    <?php foreach ($items as $item) { ?>
        <a href="<?= $item->getUrlPath(); ?>" class="lenta-actual"><?= $item->name; ?>
            <!--<span class="lenta-actual-viewed"><?= $item->views; ?></span>--></a>
    <?php } ?>
<?php } ?>
