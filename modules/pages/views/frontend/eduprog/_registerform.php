<?php
/*
    отображение существующей формы
*/
if (!empty($form)) { ?>
    <div id="regform_<?= $form->id; ?>" class="lk_block form-event">
        <div class="lk_content">
            <h4 class="lk_step_title font20">Форма регистрации</h4>
            <!-- элементы формы добавляются в js-form-list -->
            <div class="drag-list js-form-list">
                <div class="drag-element drag-element-mobile">
                    <div class="drag-element-infos">
                        <div class="drag-element-info drag-element-name"><?= $form->name; ?></div>
                    </div>
                    <div class="<?= ($form->visible ? 'pause' : 'play'); ?>-element js-visible-form"
                         data-form="<?= $form->id; ?>"></div>
                    <div class="edit-element open-form-js" data-form="<?= $form->id; ?>"
                         data-name="<?= htmlspecialchars($form->name); ?>"></div>
                    <div class="remove-element js-remove-form" data-name="<?= htmlspecialchars($form->name); ?>"
                         data-form="<?= $form->id; ?>"></div>
                </div>
            </div>
        </div>
        <div class="lk_content">
            <h4 class="lk_step_title font20 mb20">Тарифы к форме</h4>
            <div class="tarif-table-wrapper">
                <div class="tarif-list js-tarif-list">
                    <?php if (!empty($form->tariffes)) {
                        foreach ($form->tariffes as $tariff) { ?>
                            <?= $this->render('_tariff', ['tariff' => $tariff]); ?>
                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="ip_cell w100">
                    <button class="button blue medium lk open-tarif-js" data-tariff="new" data-form="<?= $form->id; ?>">
                        Добавить тариф
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>