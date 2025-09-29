<?php
/*
    отображение полей существующей формы при редактировании
*/
if (!empty($form)) {
    ?>
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
                                           name="LKEduprogRegform[fields][<?= $key_field; ?>][name]" placeholder=""
                                           value="<?= $field['name']; ?>" required="" disabled=""
                                           data-sort="<?= $key_field + 1; ?>" data-variant="1">
                                    <label class="ip_label">Короткий произвольный ответ (тип выбранного поля)</label>
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
                                    <input type="hidden" name="LKEduprogRegform[fields][<?= $key_field; ?>][sysname]"
                                           value="<?= $field['sysname']; ?>">
                                    <input type="hidden" name="LKEduprogRegform[fields][<?= $key_field; ?>][type]"
                                           value="text">
                                    <input type="hidden" name="LKEduprogRegform[fields][<?= $key_field; ?>][visible]"
                                           value="1">
                                    <input type="hidden" name="LKEduprogRegform[fields][<?= $key_field; ?>][order]"
                                           class="js-hidden-order" value="<?= $key_field; ?>">
                                    <input type="text" class="input_text name_field active success"
                                           name="LKEduprogRegform[fields][<?= $key_field; ?>][name]"
                                           placeholder="Текст вопроса" value="<?= $field['name']; ?>" required=""
                                           aria-invalid="false">
                                    <label class="ip_label">Текст вопроса</label>
                                    <div class="input-status"></div>
                                    <div class="help-block"></div>
                                </div>
                                <div class="ip_cell label-on w100">
                                    <input type="hidden" name="LKEduprogRegform[fields][<?= $key_field; ?>][required]"
                                           value="0">
                                    <input <?= $field['required'] == '1' ? 'checked="checked"' : ''; ?> type="checkbox"
                                                                                                        class="ch"
                                                                                                        name="LKEduprogRegform[fields][<?= $key_field; ?>][required]"
                                                                                                        value="1">
                                    <label>Ответ на вопрос обязателен</label>
                                    <div class="help-block"></div>
                                </div>
                                <div class="ip_cell label-on w100 ip_cell-comment">
                                    <div class="flex">
                                        <input type="text" class="input_text active success"
                                               name="LKEduprogRegform[fields][<?= $key_field; ?>][comment]"
                                               placeholder="Комментарий к вопросу (не обязательно)"
                                               value="<?= $field['comment']; ?>" aria-invalid="false">
                                        <label class="ip_label">Комментарий к вопросу <br class="mobile-visible">(не
                                            обязательно)</label>
                                        <div class="question_box">
                                            <a href="javascript:void(0)" class="question_icon">?</a>
                                            <div class="question_text">Краткий текст, который будет отображен в форме
                                                под текстом вопроса
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
                                           name="LKEduprogRegform[fields][<?= $key_field; ?>][name]" placeholder=""
                                           value="<?= $field['name']; ?>" required="" disabled=""
                                           data-sort="<?= $key_field + 1; ?>" data-variant="2">
                                    <label class="ip_label">Длинный произвольный ответ (тип выбранного поля)</label>
                                    <div class="edit-variant-question js-edit-question"></div>
                                    <div class="remove-variant-question js-remove-question"></div>
                                    <div class="input-status"></div>
                                    <div class="drag-burger drag-burger-question-list"></div>
                                </div>
                                <div class="help-block"></div>
                            </div>
                            <div class="adding-question js-adding-question js-adding-editing-question" data-variant="2"
                                 style="display: none;">
                                <h4 class="lk_step_title font20">Длинный произвольный ответ</h4>
                                <div class="ip_cell label-on w100">
                                    <input type="hidden" name="LKEduprogRegform[fields][<?= $key_field; ?>][sysname]"
                                           value="<?= $field['sysname']; ?>">
                                    <input type="hidden" name="LKEduprogRegform[fields][<?= $key_field; ?>][type]"
                                           value="textarea">
                                    <input type="hidden" name="LKEduprogRegform[fields][<?= $key_field; ?>][visible]"
                                           value="1">
                                    <input type="hidden" name="LKEduprogRegform[fields][<?= $key_field; ?>][order]"
                                           class="js-hidden-order" value="<?= $key_field; ?>">
                                    <input type="text" class="input_text name_field active success"
                                           name="LKEduprogRegform[fields][<?= $key_field; ?>][name]"
                                           placeholder="Текст вопроса" value="<?= $field['name']; ?>" required=""
                                           aria-invalid="false">
                                    <label class="ip_label">Текст вопроса</label>
                                    <div class="input-status"></div>
                                    <div class="help-block"></div>
                                </div>
                                <div class="ip_cell label-on w100">
                                    <input type="hidden" name="LKEduprogRegform[fields][<?= $key_field; ?>][required]"
                                           value="0">
                                    <input <?= $field['required'] == '1' ? 'checked="checked"' : ''; ?> type="checkbox"
                                                                                                        class="ch"
                                                                                                        name="LKEduprogRegform[fields][<?= $key_field; ?>][required]"
                                                                                                        value="1">
                                    <label>Ответ на вопрос обязателен</label>
                                    <div class="help-block"></div>
                                </div>
                                <div class="ip_cell label-on w100 ip_cell-comment">
                                    <div class="flex">
                                        <input type="text" class="input_text active success"
                                               name="LKEduprogRegform[fields][<?= $key_field; ?>][comment]"
                                               placeholder="Комментарий к вопросу (не обязательно)"
                                               value="<?= $field['comment']; ?>" aria-invalid="false">
                                        <label class="ip_label">Комментарий к вопросу <br class="mobile-visible">(не
                                            обязательно)</label>
                                        <div class="question_box">
                                            <a href="javascript:void(0)" class="question_icon">?</a>
                                            <div class="question_text">Краткий текст, который будет отображен в форме
                                                под текстом вопроса
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
                                           name="LKEduprogRegform[fields][<?= $key_field; ?>][name]" placeholder=""
                                           value="<?= $field['name']; ?>" required="" disabled=""
                                           data-sort="<?= $key_field + 1; ?>" data-variant="3">
                                    <label class="ip_label">Выбор одного варианта (тип выбранного поля)</label>
                                    <div class="edit-variant-question js-edit-question"></div>
                                    <div class="remove-variant-question js-remove-question"></div>
                                    <div class="input-status"></div>
                                    <div class="drag-burger drag-burger-question-list"></div>
                                </div>
                                <div class="help-block"></div>
                            </div>
                            <div class="adding-question js-adding-question js-adding-editing-question" data-variant="3"
                                 style="display: none;">
                                <h4 class="lk_step_title font20">Выбор одного варианта</h4>
                                <div class="ip_cell label-on w100">
                                    <input type="hidden" name="LKEduprogRegform[fields][<?= $key_field; ?>][sysname]"
                                           value="<?= $field['sysname']; ?>">
                                    <input type="hidden" name="LKEduprogRegform[fields][<?= $key_field; ?>][type]"
                                           value="radio_list">
                                    <input type="hidden" name="LKEduprogRegform[fields][<?= $key_field; ?>][visible]"
                                           value="1">
                                    <input type="hidden" name="LKEduprogRegform[fields][<?= $key_field; ?>][order]"
                                           class="js-hidden-order" value="<?= $key_field; ?>">
                                    <input type="text" class="input_text name_field active success"
                                           name="LKEduprogRegform[fields][<?= $key_field; ?>][name]"
                                           placeholder="Текст вопроса" value="<?= $field['name']; ?>" required=""
                                           aria-invalid="false">
                                    <label class="ip_label">Текст вопроса</label>
                                    <div class="input-status"></div>
                                    <div class="help-block"></div>
                                </div>
                                <div class="ip_cell label-on w100">
                                    <input type="hidden" name="LKEduprogRegform[fields][<?= $key_field; ?>][required]"
                                           value="0">
                                    <input <?= $field['required'] == '1' ? 'checked="checked"' : ''; ?> type="checkbox"
                                                                                                        class="ch"
                                                                                                        name="LKEduprogRegform[fields][<?= $key_field; ?>][required]"
                                                                                                        value="1">
                                    <label>Ответ на вопрос обязателен</label>
                                    <div class="help-block"></div>
                                </div>
                                <div class="ip_cell label-on w100 ip_cell-comment">
                                    <div class="flex">
                                        <input type="text" class="input_text active success"
                                               name="LKEduprogRegform[fields][<?= $key_field; ?>][comment]"
                                               placeholder="Комментарий к вопросу (не обязательно)"
                                               value="<?= $field['comment']; ?>" aria-invalid="false">
                                        <label class="ip_label">Комментарий к вопросу <br class="mobile-visible">(не
                                            обязательно)</label>
                                        <div class="question_box">
                                            <a href="javascript:void(0)" class="question_icon">?</a>
                                            <div class="question_text">Краткий текст, который будет отображен в форме
                                                под текстом вопроса
                                            </div>
                                        </div>
                                        <div class="input-status"></div>
                                    </div>
                                    <div class="help-block"></div>
                                </div>
                                <h4 class="lk_step_title font20">Варианты ответа</h4>
                                <div class="variants-question js-variants-question ui-sortable" data-maxprices="5"
                                     data-name="LKEduprogRegform[fields][<?= $key_field; ?>][list_values][]">
                                    <?php if (!empty($field['list_values'])) { ?>
                                        <?php foreach ($field['list_values'] as $key_variant => $value_name) { ?>
                                            <div class="ip_cell label-on w100 variant-question drag-input">
                                                <div class="flex">
                                                    <input type="text" class="input_text active success"
                                                           name="LKEduprogRegform[fields][<?= $key_field; ?>][list_values][]"
                                                           placeholder="Вариант ответа"
                                                           data-sort="<?= $key_variant + 1; ?>"
                                                           value="<?= $value_name; ?>" aria-invalid="false">
                                                    <label class="ip_label">Вариант ответа</label>
                                                    <div class="remove-variant-question js-remove-variant-question"></div>
                                                    <div class="input-status"></div>
                                                    <div class="drag-burger drag-burger-variants-question ui-sortable-handle"></div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                                <button class="button-o gray small js-add-variant-question">Добавить вариант</button>
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
                                           name="LKEduprogRegform[fields][<?= $key_field; ?>][name]" placeholder=""
                                           value="<?= $field['name']; ?>" required="" disabled=""
                                           data-sort="<?= $key_field + 1; ?>" data-variant="4">
                                    <label class="ip_label">Выбор нескольких вариантов (тип выбранного поля)</label>
                                    <div class="edit-variant-question js-edit-question"></div>
                                    <div class="remove-variant-question js-remove-question"></div>
                                    <div class="input-status"></div>
                                    <div class="drag-burger drag-burger-question-list"></div>
                                </div>
                                <div class="help-block"></div>
                            </div>
                            <div class="adding-question js-adding-question js-adding-editing-question" data-variant="4"
                                 style="display: none;">
                                <h4 class="lk_step_title font20">Выбор нескольких вариантов</h4>
                                <div class="ip_cell label-on w100">
                                    <input type="hidden" name="LKEduprogRegform[fields][<?= $key_field; ?>][sysname]"
                                           value="<?= $field['sysname']; ?>">
                                    <input type="hidden" name="LKEduprogRegform[fields][<?= $key_field; ?>][type]"
                                           value="boolean_list">
                                    <input type="hidden" name="LKEduprogRegform[fields][<?= $key_field; ?>][visible]"
                                           value="1">
                                    <input type="hidden" name="LKEduprogRegform[fields][<?= $key_field; ?>][order]"
                                           class="js-hidden-order" value="<?= $key_field; ?>">
                                    <input type="text" class="input_text name_field active success"
                                           name="LKEduprogRegform[fields][<?= $key_field; ?>][name]"
                                           placeholder="Текст вопроса" value="<?= $field['name']; ?>" required=""
                                           aria-invalid="false">
                                    <label class="ip_label">Текст вопроса</label>
                                    <div class="input-status"></div>
                                    <div class="help-block"></div>
                                </div>
                                <div class="ip_cell label-on w100">
                                    <input type="hidden" name="LKEduprogRegform[fields][<?= $key_field; ?>][required]"
                                           value="0">
                                    <input <?= $field['required'] == '1' ? 'checked="checked"' : ''; ?> type="checkbox"
                                                                                                        class="ch"
                                                                                                        name="LKEduprogRegform[fields][<?= $key_field; ?>][required]"
                                                                                                        value="1">
                                    <label>Ответ на вопрос обязателен</label>
                                    <div class="help-block"></div>
                                </div>
                                <div class="ip_cell label-on w100 ip_cell-comment">
                                    <div class="flex">
                                        <input type="text" class="input_text active success"
                                               name="LKEduprogRegform[fields][<?= $key_field; ?>][comment]"
                                               placeholder="Комментарий к вопросу (не обязательно)"
                                               value="<?= $field['comment']; ?>" aria-invalid="false">
                                        <label class="ip_label">Комментарий к вопросу <br class="mobile-visible">(не
                                            обязательно)</label>
                                        <div class="question_box">
                                            <a href="javascript:void(0)" class="question_icon">?</a>
                                            <div class="question_text">Краткий текст, который будет отображен в форме
                                                под текстом вопроса
                                            </div>
                                        </div>
                                        <div class="input-status"></div>
                                    </div>
                                    <div class="help-block"></div>
                                </div>
                                <h4 class="lk_step_title font20">Варианты ответа</h4>
                                <div class="variants-question js-variants-question ui-sortable" data-maxprices="5"
                                     data-name="LKEduprogRegform[fields][<?= $key_field; ?>][list_values][]">
                                    <?php if (!empty($field['list_values'])) { ?>
                                        <?php foreach ($field['list_values'] as $key_variant => $value_name) { ?>
                                            <div class="ip_cell label-on w100 variant-question drag-input">
                                                <div class="flex">
                                                    <input type="text" class="input_text active success"
                                                           name="LKEduprogRegform[fields][<?= $key_field; ?>][list_values][]"
                                                           placeholder="Вариант ответа"
                                                           data-sort="<?= $key_variant + 1; ?>"
                                                           value="<?= $value_name; ?>" aria-invalid="false">
                                                    <label class="ip_label">Вариант ответа</label>
                                                    <div class="remove-variant-question js-remove-variant-question"></div>
                                                    <div class="input-status"></div>
                                                    <div class="drag-burger drag-burger-variants-question ui-sortable-handle"></div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                                <button class="button-o gray small js-add-variant-question">Добавить вариант</button>
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
    <div class="ip_cell w100 mb0 add-question js-question-list" data-fieldkey="<?= count($form->form_fields); ?>">
        <button class="button blue medium lk">Добавить вопрос</button>
        <div class="add-question-list">
            <div class="add-question-item" data-variant="1">Короткий произвольный ответ</div>
            <div class="add-question-item" data-variant="2">Длинный произвольный ответ</div>
            <div class="add-question-item" data-variant="3">Выбор одного варианта</div>
            <div class="add-question-item" data-variant="4">Выбор нескольких вариантов</div>
        </div>
    </div>
    <div class="current_field"></div>
<?php } ?>