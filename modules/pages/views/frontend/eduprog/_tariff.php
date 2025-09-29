<?php
/*
    отображение существующего тарифа
*/

if (!empty($tariff)) { ?>
    <div id="tariff_line_<?= $tariff->id; ?>" class="tarif-element tarif-element-mobile drag-tarif" data-sort="1">
        <div class="tarif-element-infos">
            <div class="tarif-element-info tarif-name"><?= $tariff->name; ?></div>
        </div>
        <div class="drag-burger drag-burger-tarif"></div>
        <div class="<?= ($tariff->visible ? 'pause' : 'play'); ?>-tarif js-visible-tarif"
             data-tariff="<?= $tariff->id; ?>"></div>
        <div class="edit-tarif open-tarif-js" data-tariff="<?= $tariff->id; ?>"
             data-form="<?= $tariff->eduprogform_id; ?>"></div>
        <div class="remove-tarif js-remove-tarif" data-tariff="<?= $tariff->id; ?>"></div>
    </div>
<?php } ?>