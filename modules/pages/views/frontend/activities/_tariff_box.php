<div id="tariff_<?= $tariff->id; ?>" class="lk_block">
    <main class="lk_content">
        <div>
            <div class="ip_cell w100">
                <label class="ip_label">Наименование тарифа</label>
                <input id="name_<?= $tariff->id; ?>" name="Tariff[name]" type="text" class="input_text"
                       placeholder="Название" value="<?= $tariff->name; ?>" required/>
                <div class="help-block">Введите название тарифа</div>
            </div>
            <div class="ip_cell w100">
                <label class="ip_label">Что входит в тариф</label>
                <input id="description_<?= $tariff->id; ?>" name="Tariff[description]" type="text" class="input_text"
                       value="<?= $tariff->description; ?>" placeholder="Краткое описание"/>
            </div>
            <h4 class="lk_step_title mt20">Даты, доступные для тарифа</h4>
            <label class="ip_label">Синим выделены даты на которые можно купить билет по тарифу. Снимите выделение с
                дат, на которые нельзя купить билет по тарифу.</label>

            <div class="blue_checkboxes_box">
                <?php foreach ($tariff->getDateListEnum() as $date) { ?>
                    <input name="Tariff[date_list]" type="checkbox" value="<?= $date; ?>"
                           class="blue_ch date_list" <?= (in_array($date, $tariff->date_list)) ? 'checked' : ''; ?> />
                    <label><?= $date; ?></label>
                <?php } ?>
            </div>

            <h4 class="lk_step_title mt20">Цена</h4>
            <div class="ip_cell w100">
                <label class="ip_label">Цена, руб. Укажите стоимость по которой вы сейчас продаете</label>
                <input id="price_<?= $tariff->id; ?>" name="Tariff[price]" type="text" class="input_text numbersOnly"
                       value="<?= (int)$tariff->price; ?>" required/>
                <div class="help-block">Укажите цену</div>
            </div>
            <div class="ip_cell w100">
                <label class="ip_label">Старая цена, руб. Укажите стоимость без скидки</label>
                <input id="old_price_<?= $tariff->id; ?>" name="Tariff[old_price]" type="text"
                       class="input_text numbersOnly" value="<?= (int)$tariff->old_price; ?>"/>
            </div>
            <h4 class="lk_step_title mt20">Количество мест</h4>
            <div class="ip_cell w100">
                <label class="ip_label">Укажите сколько людей могут приобрести по данному тарифу мероприятие</label>
                <input id="tickets_count_<?= $tariff->id; ?>" name="Tariff[tickets_count]" type="text"
                       class="input_text numbersOnly" value="<?= (int)$tariff->tickets_count; ?>"/>
            </div>
            <h4 class="lk_step_title mt20">Период действия</h4>
            <div class="ip_cell w100 dateRange">
                <div>
                    <div class="ip_cell datarange_ipc flex">
                        <label class="ip_label">Начало активности*</label>
                        <input id="start_publish_<?= $tariff->id; ?>" name="Tariff[start_publish]" type="text"
                               class="input_text datepicker keypress date-mask start"
                               value="<?= Yii::$app->formatter->asDatetime(strtotime($tariff->start_publish), 'dd.MM.y'); ?>"
                               placeholder="Дата начала"/>
                    </div>
                </div>
                <div>
                    <div class="ip_cell datarange_ipc flex">
                        <label class="ip_label">Окончание активности</label>
                        <input id="end_publish_<?= $tariff->id; ?>" name="Tariff[end_publish]" type="text"
                               class="input_text datepicker keypress date-mask end"
                               value="<?= Yii::$app->formatter->asDatetime(strtotime($tariff->end_publish), 'dd.MM.y'); ?>"
                               placeholder="Дата окончания"/>
                    </div>
                </div>
            </div>
            <h4 class="lk_step_title mt20">Статус публикации</h4>
            <div class="ip_cell w100">
                <label class="ip_label">Снимите галочку, если хотите снять с публикации тариф</label>
                <input type="checkbox" id="visible_<?= $tariff->id; ?>" class="ch small ch_politics"
                       name="Tariff[visible]" <?= ($tariff->visible ? 'checked="checked"' : ''); ?> value="1">
                <label class="notmark">Опубликован</label>
            </div>
            <div class="ip_cell w100 flex flex-end">
                <button data-tariff="<?= $tariff->id; ?>" class="button-o medium lk removeTariff" type="submit">
                    Удалить
                </button>
                <button data-tariff="<?= $tariff->id; ?>" class="button-o medium blue lk applyTariff" type="submit">
                    Применить
                </button>
            </div>
        </div>
    </main>
</div>