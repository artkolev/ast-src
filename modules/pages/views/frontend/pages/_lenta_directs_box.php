<?php if (!empty($items)) { ?>
    <div class="blog-directs-list <?php if ($mobile) {
        echo 'mobile-visible';
    } ?>">
        <?php foreach ($items as $item) { ?>
            <a href="<?= $item->getUrlPath(); ?>"><?= $item->name; ?></a>
        <?php } ?>
        <div id="blog-directs-append" class="blog-directs-append"></div>
        <a href="#!" class="blog-directs-more">Ещё <span><!-- js --></span></a>
    </div>
<?php } ?>
