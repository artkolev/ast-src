<?php
/**
 * @var bool $can_edit
 * @var \app\modules\education\models\Education $education
 */
?>
<div class="tarif-element tarif-element-mobile" data-education="<?= $education->id; ?>">
    <div class="tarif-element-infos">
        <div class="tarif-element-info tarif-name"><?= $education->name; ?></div>
    </div>
    <?php if ($can_edit) { ?>
        <div class="edit_education edit-tarif"></div>
        <div class="remove_education remove-tarif"></div>
    <?php } ?>
</div>