<div id="career_<?= $career->id; ?>" class="register_form">
    <div>
        <div class="ip_cell">
            <label class="ip_label">Название организации*</label>
            <input id="name_<?= $career->id; ?>" type="text" class="input_text" name="Career[name]"
                   placeholder="Название организации" value="<?= htmlspecialchars($career->name); ?>" required/>
            <div class="help-block">Введите название организации</div>
        </div>
        <div class="ip_cell">
            <label class="ip_label">Должность*</label>
            <input id="office_<?= $career->id; ?>" type="text" class="input_text" name="Career[office]"
                   placeholder="Должность" value="<?= htmlspecialchars($career->office); ?>" required/>
            <div class="help-block">Укажите должность</div>
        </div>
        <div class="ip_cell" style="position:relative;">
            <label class="ip_label">Период работы</label>
            <div class="time_box years">
                <div class="ip_cell ipс_short">
                    <input id="work_from_<?= $career->id; ?>" type="text"
                           class="input_text ip_short maskYear datepicker-yyyy keypress" name="Career[work_from]"
                           placeholder="1990" value="<?= $career->work_from; ?>" required/>
                    <div class="help-block">Введите период работы</div>
                </div>
                <span>&mdash;</span>
                <div class="ip_cell ipс_short">
                    <input id="work_to_<?= $career->id; ?>" type="text"
                           class="input_text ip_short maskYear datepicker-yyyy keypress" name="Career[work_to]"
                           placeholder="1994" value="<?= $career->work_to; ?>" required/>
                    <div class="help-block">Введите период работы</div>
                </div>
            </div>
        </div>
        <div class="ip_cell">
            <input type="hidden" name="Career[by_realtime]" value="0"/>
            <input id="by_realtime_<?= $career->id; ?>" type="checkbox"
                   class="ch small" <?= ($career->by_realtime ? 'checked="checked"' : ''); ?> name="Career[by_realtime]"
                   value="1"/>
            <label>По настоящее время</label>
        </div>
        <div class="ip_cell">
            <label class="ip_label">Достижения</label>
            <input id="achiev_<?= $career->id; ?>" type="text" class="input_text" name="Career[achiev]"
                   placeholder="Достижения" value="<?= htmlspecialchars($career->achiev); ?>"/>
            <div class="help-block">Укажите ваши достижения</div>
        </div>
        <?php if ($can_edit) { ?>
            <div class="ip_cell w100 flex flex-end">
                <button data-career="<?= $career->id; ?>" class="button-o medium lk removeCareer" type="submit">
                    Удалить
                </button>
                <button data-career="<?= $career->id; ?>" class="button-o medium blue lk applyCareer" type="submit">
                    Применить
                </button>
            </div>
        <?php } ?>
    </div>
</div>
<br><br>