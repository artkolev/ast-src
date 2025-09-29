<div id="education_<?= $education->id; ?>" class="register_form">
    <div>
        <div class="ip_cell">
            <label class="ip_label">Учебное заведение*</label>
            <input id="name_<?= $education->id; ?>" type="text" class="input_text" name="Education[name]"
                   placeholder="Название учебного заведения" value="<?= htmlspecialchars($education->name); ?>"
                   required/>
            <div class="help-block">Введите название заведения</div>
        </div>
        <div class="ip_cell">
            <label class="ip_label">Специализация*</label>
            <input id="speciality_<?= $education->id; ?>" type="text" class="input_text" name="Education[speciality]"
                   placeholder="Ваша специализация" value="<?= htmlspecialchars($education->speciality); ?>" required/>
            <div class="help-block">Укажите специализацию</div>
        </div>
        <div class="ip_cell">
            <label class="ip_label">Уровень*</label>
            <select id="stage_id_<?= $education->id; ?>" class="pretty_select" name="Education[stage_id]">
                <?php foreach ($stages as $id => $name) { ?>
                    <option value="<?= $id; ?>" <?= ($id == $education->stage_id) ? 'selected="selected"' : ''; ?>><?= $name; ?></option>
                <?php } ?>
            </select>
            <div class="help-block">Укажите уровень образования</div>
        </div>
        <div class="ip_cell" style="position:relative;">
            <label class="ip_label">Период обучения</label>
            <div class="time_box years">
                <div class="ip_cell ipс_short">
                    <input id="study_from_<?= $education->id; ?>" type="text"
                           class="input_text ip_short maskYear datepicker-yyyy keypress" name="Education[study_from]"
                           placeholder="1990" value="<?= $education->study_from; ?>" required/>
                    <div class="help-block">Введите период обучения</div>
                </div>
                <span>&mdash;</span>
                <div class="ip_cell ipс_short">
                    <input id="study_to_<?= $education->id; ?>" type="text"
                           class="input_text ip_short maskYear datepicker-yyyy keypress" name="Education[study_to]"
                           placeholder="1994" value="<?= $education->study_to; ?>" required/>
                    <div class="help-block">Введите период обучения</div>
                </div>
            </div>
        </div>
        <div class="ip_cell">
            <input type="hidden" name="Education[by_realtime]" value="0"/>
            <input id="by_realtime_<?= $education->id; ?>" type="checkbox"
                   class="ch small" <?= ($education->by_realtime ? 'checked="checked"' : ''); ?>
                   name="Education[by_realtime]" value="1"/>
            <label>По настоящее время</label>
        </div>
        <?php if ($can_edit) { ?>
            <div class="ip_cell w100 flex flex-end">
                <button data-education="<?= $education->id; ?>" class="button-o medium lk removeEducation"
                        type="submit">Удалить
                </button>
                <button data-education="<?= $education->id; ?>" class="button-o medium blue lk applyEducation"
                        type="submit">Применить
                </button>
            </div>
        <?php } ?>
    </div>
</div>
<br><br>