<?php
/**
 * @var bool $can_edit
 * @var \app\modules\career\models\Career $career
 */
?>
<div class="tarif-element tarif-element-mobile" data-career="<?= $career->id; ?>">
    <div class="tarif-element-infos">
        <div class="tarif-element-info tarif-name"><?= $career->name; ?></div>
    </div>
    <?php if ($can_edit) { ?>
        <div class="edit_career edit-tarif"></div>
        <div class="remove_career remove-tarif"></div>
    <?php } ?>
</div>