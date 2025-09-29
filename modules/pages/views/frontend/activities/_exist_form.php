<div class="lk_block form-event" data-form_key="<?= $key; ?>">
    <div class="lk_content">
        <h4 class="lk_step_title font20">Создать форму регистрации</h4>
        <input class="form_id_value" type="hidden" name="LKEvent[forms][<?= $key; ?>][id]" value="<?= $form->id; ?>">
        <div class="ip_cell label-on w100">
            <div class="flex">
                <input type="text" class="input_text active success" name="LKEvent[forms][<?= $key; ?>][name]"
                       value="<?= $form->name; ?>" placeholder="Название формы" required="">
                <label class="ip_label">Название формы*</label>
                <div class="question_box">
                    <a href="javascript:void(0)" class="question_icon">?</a>
                    <div class="question_text">Укажите в названии, какая категория участников регистрируется через эту
                        форму. Например, участник или спикер.
                    </div>
                </div>
                <div class="input-status"></div>
            </div>
            <div class="help-block">Укажите Название формы</div>
        </div>
        <h4 class="lk_step_title font20">Поля формы</h4>
        <div class="ip_cell label-on w100">
            <div class="flex">
                <input type="text" class="input_text active" name="firstName" placeholder="Обязательное поле"
                       value="Имя" required="" disabled>
                <label class="ip_label">Обязательное поле*</label>
                <div class="question_box">
                    <a href="javascript:void(0)" class="question_icon">?</a>
                    <div class="question_text">Обязательное поле для регистрации участника.</div>
                </div>
                <div class="input-status"></div>
            </div>
            <div class="help-block"></div>
        </div>
        <div class="ip_cell label-on w100">
            <div class="flex">
                <input type="text" class="input_text active" name="lastName" placeholder="Обязательное поле"
                       value="Фамилия" required="" disabled>
                <label class="ip_label">Обязательное поле*</label>
                <div class="question_box">
                    <a href="javascript:void(0)" class="question_icon">?</a>
                    <div class="question_text">Обязательное поле для регистрации участника.</div>
                </div>
                <div class="input-status"></div>
            </div>
            <div class="help-block"></div>
        </div>
        <div class="ip_cell label-on w100">
            <div class="flex">
                <input type="text" class="input_text active" name="email" placeholder="Обязательное поле" value="Email"
                       required="" disabled>
                <label class="ip_label">Обязательное поле*</label>
                <div class="question_box">
                    <a href="javascript:void(0)" class="question_icon">?</a>
                    <div class="question_text">Обязательное поле только для Плательщика. Необходимо для создания личного
                        кабинета.
                    </div>
                </div>
                <div class="input-status"></div>
            </div>
            <div class="help-block"></div>
        </div>
        <div class="ip_cell label-on w100">
            <div class="flex">
                <input type="text" class="input_text active" name="phone" placeholder="Обязательное поле"
                       value="Телефон" required="" disabled>
                <label class="ip_label">Обязательное поле*</label>
                <div class="question_box">
                    <a href="javascript:void(0)" class="question_icon">?</a>
                    <div class="question_text">Обязательное поле только для Плательщика. Необходимо для создания личного
                        кабинета.
                    </div>
                </div>
                <div class="input-status"></div>
            </div>
            <div class="help-block"></div>
        </div>

        <!-- сюда добавляем созданные поля -->
        <div class="js-added-question-list">
            <?php if (!empty($form->form_fields)) {
                foreach ($form->form_fields as $key_field => $field) {
                    switch ($field['type']) {
                        case 'text':
                            ?>
                            <div class="added-question-wrapper" data-question="<?= $key_field + 1; ?>">
                                <div class="ip_cell ip_cell-mobile label-on w100 drag-input"
                                     data-question-input="<?= $key_field + 1; ?>">
                                    <div class="flex">
                                        <input type="text" class="input_text active disabled"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][name]"
                                               placeholder="" value="<?= htmlspecialchars($field['name']); ?>"
                                               required="" disabled="" data-sort="<?= $key_field + 1; ?>"
                                               data-variant="1">
                                        <label class="ip_label">Короткий произвольный ответ (тип выбранного
                                            поля)</label>
                                        <div class="edit-variant-question js-edit-question"></div>
                                        <div class="remove-variant-question js-remove-question"></div>
                                        <div class="input-status"></div>
                                        <div class="drag-burger drag-burger-question-list"></div>
                                    </div>
                                    <div class="help-block"></div>
                                </div>
                                <div class="adding-question js-adding-question js-adding-editing-question"
                                     style="display: none;">
                                    <h4 class="lk_step_title font20">Короткий произвольный ответ</h4>
                                    <p>Ответ пользователя на вопрос содержит до 255 символов</p>
                                    <div class="ip_cell label-on w100">
                                        <input type="hidden"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][sysname]"
                                               value="<?= $field['sysname']; ?>">
                                        <input type="hidden"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][type]"
                                               value="text">
                                        <input type="hidden"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][visible]"
                                               value="1">
                                        <input type="hidden"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][order]"
                                               class="js-hidden-order" value="<?= $key_field; ?>">
                                        <input type="text" class="input_text name_field active success"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][name]"
                                               placeholder="Текст вопроса"
                                               value="<?= htmlspecialchars($field['name']); ?>" required=""
                                               aria-invalid="false">
                                        <label class="ip_label">Текст вопроса</label>
                                        <div class="input-status"></div>
                                        <div class="help-block"></div>
                                    </div>
                                    <div class="ip_cell label-on w100">
                                        <input type="hidden"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][required]"
                                               value="0">
                                        <input <?= $field['required'] == '1' ? 'checked="checked"' : ''; ?>
                                                type="checkbox" class="ch"
                                                name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][required]"
                                                value="1">
                                        <label>Ответ на вопрос обязателен</label>
                                        <div class="help-block"></div>
                                    </div>
                                    <div class="ip_cell label-on w100 ip_cell-comment">
                                        <div class="flex">
                                            <input type="text" class="input_text active success"
                                                   name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][comment]"
                                                   placeholder="Комментарий к вопросу (не обязательно)"
                                                   value="<?= htmlspecialchars($field['comment']); ?>"
                                                   aria-invalid="false">
                                            <label class="ip_label">Комментарий к вопросу <br class="mobile-visible">(не
                                                обязательно)</label>
                                            <div class="question_box">
                                                <a href="javascript:void(0)" class="question_icon">?</a>
                                                <div class="question_text">Краткий текст, который будет отображен в
                                                    форме под текстом вопроса
                                                </div>
                                            </div>
                                            <div class="input-status"></div>
                                        </div>
                                        <div class="help-block"></div>
                                    </div>
                                    <div class="ip_cell w100 flex mb0">
                                        <button class="button-o gray small js-cancel-adding-question">Отменить</button>
                                        <button class="button blue small lk js-add-question js-add-edit-question">
                                            Сохранить
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php
                            break;
                        case 'textarea':
                            ?>
                            <div class="added-question-wrapper" data-question="<?= $key_field + 1; ?>">
                                <div class="ip_cell ip_cell-mobile label-on w100 drag-input"
                                     data-question-input="<?= $key_field + 1; ?>">
                                    <div class="flex">
                                        <input type="text" class="input_text active disabled"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][name]"
                                               placeholder="" value="<?= htmlspecialchars($field['name']); ?>"
                                               required="" disabled="" data-sort="<?= $key_field + 1; ?>"
                                               data-variant="2">
                                        <label class="ip_label">Длинный произвольный ответ (тип выбранного поля)</label>
                                        <div class="edit-variant-question js-edit-question"></div>
                                        <div class="remove-variant-question js-remove-question"></div>
                                        <div class="input-status"></div>
                                        <div class="drag-burger drag-burger-question-list"></div>
                                    </div>
                                    <div class="help-block"></div>
                                </div>
                                <div class="adding-question js-adding-question js-adding-editing-question"
                                     data-variant="2" style="display: none;">
                                    <h4 class="lk_step_title font20">Длинный произвольный ответ</h4>
                                    <div class="ip_cell label-on w100">
                                        <input type="hidden"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][sysname]"
                                               value="<?= $field['sysname']; ?>">
                                        <input type="hidden"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][type]"
                                               value="textarea">
                                        <input type="hidden"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][visible]"
                                               value="1">
                                        <input type="hidden"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][order]"
                                               class="js-hidden-order" value="<?= $key_field; ?>">
                                        <input type="text" class="input_text name_field active success"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][name]"
                                               placeholder="Текст вопроса"
                                               value="<?= htmlspecialchars($field['name']); ?>" required=""
                                               aria-invalid="false">
                                        <label class="ip_label">Текст вопроса</label>
                                        <div class="input-status"></div>
                                        <div class="help-block"></div>
                                    </div>
                                    <div class="ip_cell label-on w100">
                                        <input type="hidden"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][required]"
                                               value="0">
                                        <input <?= $field['required'] == '1' ? 'checked="checked"' : ''; ?>
                                                type="checkbox" class="ch"
                                                name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][required]"
                                                value="1">
                                        <label>Ответ на вопрос обязателен</label>
                                        <div class="help-block"></div>
                                    </div>
                                    <div class="ip_cell label-on w100 ip_cell-comment">
                                        <div class="flex">
                                            <input type="text" class="input_text active success"
                                                   name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][comment]"
                                                   placeholder="Комментарий к вопросу (не обязательно)"
                                                   value="<?= htmlspecialchars($field['comment']); ?>"
                                                   aria-invalid="false">
                                            <label class="ip_label">Комментарий к вопросу <br class="mobile-visible">(не
                                                обязательно)</label>
                                            <div class="question_box">
                                                <a href="javascript:void(0)" class="question_icon">?</a>
                                                <div class="question_text">Краткий текст, который будет отображен в
                                                    форме под текстом вопроса
                                                </div>
                                            </div>
                                            <div class="input-status"></div>
                                        </div>
                                        <div class="help-block"></div>
                                    </div>
                                    <div class="ip_cell w100 flex mb0">
                                        <button class="button-o gray small js-cancel-adding-question">Отменить</button>
                                        <button class="button blue small lk js-add-question js-add-edit-question">
                                            Сохранить
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php
                            break;
                        case 'radio_list':
                            ?>
                            <div class="added-question-wrapper" data-question="<?= $key_field + 1; ?>">
                                <div class="ip_cell ip_cell-mobile label-on w100 drag-input"
                                     data-question-input="<?= $key_field + 1; ?>">
                                    <div class="flex">
                                        <input type="text" class="input_text active disabled"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][name]"
                                               placeholder="" value="<?= htmlspecialchars($field['name']); ?>"
                                               required="" disabled="" data-sort="<?= $key_field + 1; ?>"
                                               data-variant="3">
                                        <label class="ip_label">Выбор одного варианта (тип выбранного поля)</label>
                                        <div class="edit-variant-question js-edit-question"></div>
                                        <div class="remove-variant-question js-remove-question"></div>
                                        <div class="input-status"></div>
                                        <div class="drag-burger drag-burger-question-list"></div>
                                    </div>
                                    <div class="help-block"></div>
                                </div>
                                <div class="adding-question js-adding-question js-adding-editing-question"
                                     data-variant="3" style="display: none;">
                                    <h4 class="lk_step_title font20">Выбор одного варианта</h4>
                                    <div class="ip_cell label-on w100">
                                        <input type="hidden"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][sysname]"
                                               value="<?= $field['sysname']; ?>">
                                        <input type="hidden"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][type]"
                                               value="radio_list">
                                        <input type="hidden"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][visible]"
                                               value="1">
                                        <input type="hidden"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][order]"
                                               class="js-hidden-order" value="<?= $key_field; ?>">
                                        <input type="text" class="input_text name_field active success"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][name]"
                                               placeholder="Текст вопроса"
                                               value="<?= htmlspecialchars($field['name']); ?>" required=""
                                               aria-invalid="false">
                                        <label class="ip_label">Текст вопроса</label>
                                        <div class="input-status"></div>
                                        <div class="help-block"></div>
                                    </div>
                                    <div class="ip_cell label-on w100">
                                        <input type="hidden"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][required]"
                                               value="0">
                                        <input <?= $field['required'] == '1' ? 'checked="checked"' : ''; ?>
                                                type="checkbox" class="ch"
                                                name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][required]"
                                                value="1">
                                        <label>Ответ на вопрос обязателен</label>
                                        <div class="help-block"></div>
                                    </div>
                                    <div class="ip_cell label-on w100 ip_cell-comment">
                                        <div class="flex">
                                            <input type="text" class="input_text active success"
                                                   name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][comment]"
                                                   placeholder="Комментарий к вопросу (не обязательно)"
                                                   value="<?= htmlspecialchars($field['comment']); ?>"
                                                   aria-invalid="false">
                                            <label class="ip_label">Комментарий к вопросу <br class="mobile-visible">(не
                                                обязательно)</label>
                                            <div class="question_box">
                                                <a href="javascript:void(0)" class="question_icon">?</a>
                                                <div class="question_text">Краткий текст, который будет отображен в
                                                    форме под текстом вопроса
                                                </div>
                                            </div>
                                            <div class="input-status"></div>
                                        </div>
                                        <div class="help-block"></div>
                                    </div>
                                    <h4 class="lk_step_title font20">Варианты ответа</h4>
                                    <div class="variants-question js-variants-question ui-sortable" data-maxprices="5"
                                         data-name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][list_values][]">
                                        <?php if (!empty($field['list_values'])) { ?>
                                            <?php foreach ($field['list_values'] as $key_variant => $value_name) { ?>
                                                <div class="ip_cell label-on w100 variant-question drag-input">
                                                    <div class="flex">
                                                        <input type="text" class="input_text active success"
                                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][list_values][]"
                                                               placeholder="Вариант ответа"
                                                               data-sort="<?= $key_variant + 1; ?>"
                                                               value="<?= htmlspecialchars($value_name); ?>"
                                                               aria-invalid="false">
                                                        <label class="ip_label">Вариант ответа</label>
                                                        <div class="remove-variant-question js-remove-variant-question"></div>
                                                        <div class="input-status"></div>
                                                        <div class="drag-burger drag-burger-variants-question ui-sortable-handle"></div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                    <button class="button-o gray small js-add-variant-question">Добавить вариант
                                    </button>
                                    <div class="ip_cell w100 flex mb0">
                                        <button class="button-o gray small js-cancel-adding-question">Отменить</button>
                                        <button class="button blue small lk js-add-question js-add-edit-question">
                                            Сохранить
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php
                            break;
                        case 'boolean_list':
                            ?>
                            <div class="added-question-wrapper" data-question="<?= $key_field + 1; ?>">
                                <div class="ip_cell ip_cell-mobile label-on w100 drag-input"
                                     data-question-input="<?= $key_field + 1; ?>">
                                    <div class="flex">
                                        <input type="text" class="input_text active disabled"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][name]"
                                               placeholder="" value="<?= htmlspecialchars($field['name']); ?>"
                                               required="" disabled="" data-sort="<?= $key_field + 1; ?>"
                                               data-variant="4">
                                        <label class="ip_label">Выбор нескольких вариантов (тип выбранного поля)</label>
                                        <div class="edit-variant-question js-edit-question"></div>
                                        <div class="remove-variant-question js-remove-question"></div>
                                        <div class="input-status"></div>
                                        <div class="drag-burger drag-burger-question-list"></div>
                                    </div>
                                    <div class="help-block"></div>
                                </div>
                                <div class="adding-question js-adding-question js-adding-editing-question"
                                     data-variant="4" style="display: none;">
                                    <h4 class="lk_step_title font20">Выбор нескольких вариантов</h4>
                                    <div class="ip_cell label-on w100">
                                        <input type="hidden"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][sysname]"
                                               value="<?= $field['sysname']; ?>">
                                        <input type="hidden"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][type]"
                                               value="boolean_list">
                                        <input type="hidden"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][visible]"
                                               value="1">
                                        <input type="hidden"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][order]"
                                               class="js-hidden-order" value="<?= $key_field; ?>">
                                        <input type="text" class="input_text name_field active success"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][name]"
                                               placeholder="Текст вопроса"
                                               value="<?= htmlspecialchars($field['name']); ?>" required=""
                                               aria-invalid="false">
                                        <label class="ip_label">Текст вопроса</label>
                                        <div class="input-status"></div>
                                        <div class="help-block"></div>
                                    </div>
                                    <div class="ip_cell label-on w100">
                                        <input type="hidden"
                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][required]"
                                               value="0">
                                        <input <?= $field['required'] == '1' ? 'checked="checked"' : ''; ?>
                                                type="checkbox" class="ch"
                                                name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][required]"
                                                value="1">
                                        <label>Ответ на вопрос обязателен</label>
                                        <div class="help-block"></div>
                                    </div>
                                    <div class="ip_cell label-on w100 ip_cell-comment">
                                        <div class="flex">
                                            <input type="text" class="input_text active success"
                                                   name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][comment]"
                                                   placeholder="Комментарий к вопросу (не обязательно)"
                                                   value="<?= htmlspecialchars($field['comment']); ?>"
                                                   aria-invalid="false">
                                            <label class="ip_label">Комментарий к вопросу <br class="mobile-visible">(не
                                                обязательно)</label>
                                            <div class="question_box">
                                                <a href="javascript:void(0)" class="question_icon">?</a>
                                                <div class="question_text">Краткий текст, который будет отображен в
                                                    форме под текстом вопроса
                                                </div>
                                            </div>
                                            <div class="input-status"></div>
                                        </div>
                                        <div class="help-block"></div>
                                    </div>
                                    <h4 class="lk_step_title font20">Варианты ответа</h4>
                                    <div class="variants-question js-variants-question ui-sortable" data-maxprices="5"
                                         data-name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][list_values][]">
                                        <?php if (!empty($field['list_values'])) { ?>
                                            <?php foreach ($field['list_values'] as $key_variant => $value_name) { ?>
                                                <div class="ip_cell label-on w100 variant-question drag-input">
                                                    <div class="flex">
                                                        <input type="text" class="input_text active success"
                                                               name="LKEvent[forms][<?= $key; ?>][fields][<?= $key_field; ?>][list_values][]"
                                                               placeholder="Вариант ответа"
                                                               data-sort="<?= $key_variant + 1; ?>"
                                                               value="<?= htmlspecialchars($value_name); ?>"
                                                               aria-invalid="false">
                                                        <label class="ip_label">Вариант ответа</label>
                                                        <div class="remove-variant-question js-remove-variant-question"></div>
                                                        <div class="input-status"></div>
                                                        <div class="drag-burger drag-burger-variants-question ui-sortable-handle"></div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                    <button class="button-o gray small js-add-variant-question">Добавить вариант
                                    </button>
                                    <div class="ip_cell w100 flex mb0">
                                        <button class="button-o gray small js-cancel-adding-question">Отменить</button>
                                        <button class="button blue small lk js-add-question js-add-edit-question">
                                            Сохранить
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php
                            break;
                    } ?>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="ip_cell w100 mb0 add-question js-question-list" data-formkey="<?= $key; ?>"
             data-fieldkey="<?= count($form->form_fields); ?>">
            <button type="button" class="button blue medium lk">Добавить вопрос</button>
            <div class="add-question-list">
                <div class="add-question-item" data-variant="1">Короткий произвольный ответ</div>
                <div class="add-question-item" data-variant="2">Длинный произвольный ответ</div>
                <div class="add-question-item" data-variant="3">Выбор одного варианта</div>
                <div class="add-question-item" data-variant="4">Выбор нескольких вариантов</div>
            </div>
        </div>
        <div class="current_field"></div>
    </div>

    <div class="lk_content" <?= (Yii::$app->params['can_paid_events'] ? '' : 'style="display:none"'); ?>>
        <h4 class="lk_step_title font20 mb10">Стоимость участия</h4>
        <div class="lk_block_subtitle mb30">При выборе платной регистрации добавьте тариф(-ы) для продолжения создания
            мероприятия.
        </div>
        <div class="ip_cell w100">
            <input type="radio" class="ch need-pay-ch"
                   name="LKEvent[forms][<?= $key; ?>][payregister]" <?= $form->payregister == 1 ? '' : 'checked="checked"'; ?>
                   value="0">
            <label>Бесплатное участие</label>
            <div class="help-block"></div>
        </div>
        <?php if (Yii::$app->params['can_paid_events']) { ?>
            <div class="ip_cell w100">
                <input type="radio" class="ch need-pay-ch"
                       name="LKEvent[forms][<?= $key; ?>][payregister]" <?= $form->payregister == 1 ? 'checked="checked"' : ''; ?>
                       value="1">
                <label>Платное участие</label>
                <div class="help-block"></div>
            </div>
        <?php } ?>
    </div>
    <div class="lk_content need-pay-ch-wrap" style="display: none;">
        <h4 class="lk_step_title font20 mb20">Тарифы</h4>
        <div class="tarif-table-wrapper">
            <div class="tariff_place tarif-list js-tarif-list">
                <?php if (!empty($form->tariffes)) { ?>
                    <?php foreach ($form->tariffes as $tarif_key => $tariff) {
                        if ($tariff->free) {
                            continue;
                        }
                        echo $this->render('_tariff_line', ['tariff' => $tariff, 'key' => $tarif_key]); ?>
                    <?php } ?>
                <?php } ?>
            </div>
            <div class="ip_cell w100">
                <button class="button blue medium lk add-tarif-js">Добавить тариф</button>
            </div>
        </div>
        <div class="tariff_form_place"></div>
    </div>
    <div class="lk_content">
        <div class="ip_cell w100 flex justify-between buttons-wrapper mb0">
            <div class="ip_cell flex align-center w100 mb0">
                <button class="button-o gray medium lk switch-active-js"
                        data-form="<?= $form->id; ?>"><?= $form->visible ? 'Приостановить' : 'Возобновить'; ?> продажу
                    билетов
                </button>
                <div class="question_box">
                    <a href="javascript:void(0)" class="question_icon">?</a>
                    <div class="question_text">
                        По клику тарифы в форме будут скрыты и появится надпись "Продажа билетов приостановлена. Все
                        билеты проданы".
                    </div>
                </div>
            </div>
            <div class="ip_cell flex flex-end w100 mb0">
                <button class="button-o gray medium lk js-remove-event-forms">Удалить форму регистрации</button>
            </div>
        </div>
    </div>
</div>