<!-- добавить класс drag-tarif для сортировки -->
<div class="tarif-element tarif-element-mobile" data-tariff="<?= $tariff->id; ?>" data-sort="<?= $key + 1; ?>">
    <div class="tarif-element-infos">
        <div class="tarif-element-info tarif-name"><?= $tariff->name; ?></div>
        <div class="tarif-element-info tarif-date">
            до <?= Yii::$app->formatter->asDatetime(strtotime($tariff->end_publish), 'dd.MM.y'); ?></div>
        <div class="tarif-element-info tarif-price"><?= ($tariff->currentPrice === false) ? 'Продажа закрыта' : $tariff->currentPrice . ' ₽'; ?></div>
    </div>
    <div class="drag-burger"></div>
    <div data-tariff="<?= $tariff->id; ?>" class="edit_tariff edit-tarif"></div>
    <div data-tariff="<?= $tariff->id; ?>" class="remove-tarif js-remove-tarif"></div>
</div>