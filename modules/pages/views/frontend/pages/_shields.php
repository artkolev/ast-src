<?php if (count($items)) { ?>
    <?php if (count($items) > 1) { ?>
        <div class="all-events-card-stickers <?= $desktop == true ? 'desktop-visible' : ''; ?> <?= $mobile == true ? 'mobile-visible' : ''; ?>">
            <?php foreach ($items as $item) { ?>
                <div class="all-events-card-sticker"
                     <?php if (!empty($item['color'])) { ?>style="background: #<?= $item['color']; ?>;"<?php } ?> ><?= $item['text']; ?></div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <?php $item = $items[0]; ?>
        <div class="all-events-card-sticker <?= $desktop == true ? 'desktop-visible' : ''; ?> <?= $mobile == true ? 'mobile-visible' : ''; ?>"
             <?php if (!empty($item['color'])) { ?>style="background: #<?= $item['color']; ?>;"<?php } ?> ><?= $item['text']; ?></div>
    <?php } ?>
<?php } ?>