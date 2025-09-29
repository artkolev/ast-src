<?php

use app\helpers\MainHelper;
use app\modules\pages\models\ProfileCafedra;
use app\modules\pages\models\ProfileIndex;
use app\modules\settings\models\SettingsText;
use kitsunefet\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var \yii\web\View $this
 * @var bool $can_edit
 */
$user = Yii::$app->user->identity->userAR;
$profile_index = ProfileIndex::find()->where(['model' => ProfileIndex::class, 'visible' => 1])->one();
$profile_cafedra = ProfileCafedra::find()->where(['model' => ProfileCafedra::class, 'visible' => 1])->one();
$user = Yii::$app->user->identity->userAR;
?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <?php if ($profile_index) { ?>
                    <a href="<?= $profile_index->getUrlPath(); ?>" class="button-o back">Вернуться к личному
                        кабинету</a>
                <?php } ?>
                <div class="lk_block">
                    <header class="lk_block_header">
                        <h1 class="lk_block_title-big"><?= $model->getNameForView(); ?></h1>
                        <?= $model->content; ?>
                    </header>
                </div>
                <?php $form = ActiveForm::begin([
                        'id' => 'anketa-form',
                        'action' => '/site/ajaxValidate/',
                        'options' => ['class' => 'marked', 'autocomplete' => 'off'],
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => true,
                        'validateOnSubmit' => true,
                        'validateOnChange' => true,
                        'validateOnType' => false,
                        'validateOnBlur' => true,
                        'fieldConfig' => [
                                'options' => ['class' => 'ip_cell w100'],
                                'template' => '{label}{input}{error}{hint}',
                                'inputOptions' => ['class' => 'input_text save_profile'],
                                'labelOptions' => ['class' => 'ip_label'],
                        ],
                ]); ?>
                <div class="lk_block">
                    <?php if (!empty($model->profarea_title) or !empty($model->profarea_text)) { ?>
                        <header class="lk_block_header">
                            <h1 class="lk_block_title">Личные данные</h1>
                        </header>
                    <?php } ?>
                    <main class="lk_content">
                        <div class="ip_cell w100 ">
                            <?= $form->field($modelform, 'surname')->textInput(['placeholder' => 'Фамилия*']); ?>
                        </div>
                        <div class="ip_cell w100 ">
                            <?= $form->field($modelform, 'name')->textInput(['placeholder' => 'Имя*']); ?>
                        </div>
                        <div class="ip_cell w100 ">
                            <?= $form->field($modelform, 'patronymic')->textInput(['placeholder' => 'Отчество']); ?>
                        </div>
                    </main>
                </div>
                <div class="lk_block">
                    <?php if (!empty($model->profarea_title) or !empty($model->profarea_text)) { ?>
                        <header class="lk_block_header">
                            <?= $model->profarea_title ? '<h1 class="lk_block_title">' . $model->profarea_title . '</h1>' : ''; ?>
                            <?= $model->profarea_text ? '<p>' . $model->profarea_text . '</p>' : ''; ?>
                        </header>
                    <?php } ?>
                    <main class="lk_content">
                        <?php if ($user->directionM) { ?>
                            <div class="ip_cell w100 ">
                                <label class="ip_label">Основная кафедра</label>
                                <h4 class="ip_cell mb0">Кафедра "<?= $user->directionM->name; ?>"</h4>
                            </div>
                        <?php } ?>
                        <a href="<?= $profile_cafedra->getUrlPath(); ?>" class="button-o lk_button_submit">Изменить
                            кафедру</a><br><br>
                        <div class="ip_cell w100 ">
                            <?= $form->field($modelform, 'extra_direct')->textArea(); ?>
                        </div>

                        <?= $form->field($modelform, 'competence')->dropDownList($modelform->getCompetenceList() + ArrayHelper::map($user->competence, 'id', 'name'), ['class' => "pretty_tags_ns save_profile", 'multiple' => 'multiple']); ?>
                        <?= $form->field($modelform, 'solvtask')->dropDownList($modelform->getSolvtaskList() + ArrayHelper::map($user->solvtask, 'id', 'name'), ['class' => "pretty_tags_ns save_profile", 'multiple' => 'multiple']); ?>
                    </main>
                </div>
                <div class="lk_block">
                    <?php if (!empty($model->main_title) or !empty($model->main_text)) { ?>
                        <header class="lk_block_header">
                            <?= $model->main_title ? '<h1 class="lk_block_title">' . $model->main_title . '</h1>' : ''; ?>
                            <?= $model->main_text ? '<p>' . $model->main_text . '</p>' : ''; ?>
                        </header>
                    <?php } ?>
                    <main class="lk_content">
                        <?= $form->field($modelform, 'city_id')->dropDownList($modelform->getCityList(), ['class' => "pretty_select save_profile"]); ?>
                        <?= $form->field($modelform, 'sex')->dropDownList($modelform->getSexList(), ['prompt' => 'Не указан', 'class' => "pretty_select save_profile"]); ?>
                        <?= $form->field($modelform, 'birthday')->textInput(['class' => "input_text ip_medium datepicker keypress date-mask save_profile"]); ?>
                        <h3>Ссылки</h3>
                        <?= $form->field($modelform, 'url_to_site', ['template' => '<div class="flex">{input}' . $modelform->getQuestion('url_to_site') . '</div>{error}{hint}'])->input('text', ['placeholder' => "Адрес сайта"]); ?>
                        <?= $form->field($modelform, 'url_to_vk', ['options' => ['class' => 'ip_cell social_url'], 'template' => '<span class="in_circle"><i class="fa fa-vk"></i></span><div class="flex">{input}' . $modelform->getQuestion('url_to_vk') . '</div>{error}{hint}'])->input('text', ['placeholder' => "Ссылка на страницу в социальной сети"]); ?>
                        <?= $form->field($modelform, 'url_to_dzen', ['options' => ['class' => 'ip_cell social_url'], 'template' => '<span class="in_circle"><i class="fa fa-zen"></i></span><div class="flex">{input}' . $modelform->getQuestion('url_to_dzen') . '</div>{error}{hint}'])->input('text', ['placeholder' => "Ссылка на страницу в социальной сети"]); ?>
                        <?= $form->field($modelform, 'url_to_twitter', ['options' => ['class' => 'ip_cell social_url'], 'template' => '<span class="in_circle"><i class="fa fa-twitter"></i></span><div class="flex">{input}' . $modelform->getQuestion('url_to_twitter') . '</div>{error}{hint}'])->input('text', ['placeholder' => "Ссылка на страницу в социальной сети"]); ?>
                        <?= $form->field($modelform, 'url_to_youtube', ['options' => ['class' => 'ip_cell social_url'], 'template' => '<span class="in_circle"><i class="fa fa-youtube-play"></i></span><div class="flex">{input}' . $modelform->getQuestion('url_to_youtube') . '</div>{error}{hint}'])->input('text', ['placeholder' => "Ссылка на страницу в социальной сети"]); ?>
                        <?= $form->field($modelform, 'url_to_telegram', ['options' => ['class' => 'ip_cell social_url'], 'template' => '<span class="in_circle"><i class="fa fa-send"></i></span><div class="flex">{input}' . $modelform->getQuestion('url_to_telegram') . '</div>{error}{hint}'])->input('text', ['placeholder' => "Ссылка на страницу в социальной сети"]); ?>
                        <?= $form->field($modelform, 'extra_links')->textArea(); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <?php if (!empty($model->education_title) or !empty($model->education_text)) { ?>
                        <header class="lk_block_header">
                            <?= $model->education_title ? '<h1 class="lk_block_title">' . $model->education_title . '</h1>' : ''; ?>
                            <?= $model->education_text ? '<p>' . $model->education_text . '</p>' : ''; ?>
                        </header>
                    <?php } ?>
                    <main class="lk_content">
                        <div class="existing_educat">
                            <?php foreach ($user->educationFront as $education) {
                                echo $this->render('_education_element', ['education' => $education, 'can_edit' => $can_edit]);
                            } ?>
                        </div>
                        <?php if ($can_edit) { ?>
                            <div class="ip_cell w100">
                                <button class="button blue medium lk add_education">Добавить образование</button>
                            </div>
                        <?php } ?>
                        <?= $form->field($modelform, 'education_check', ['template' => '{input}{error}{hint}'])->textInput(['style' => 'display:none']); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <?php if (!empty($model->career_title) or !empty($model->career_text)) { ?>
                        <header class="lk_block_header">
                            <?= $model->career_title ? '<h1 class="lk_block_title">' . $model->career_title . '</h1>' : ''; ?>
                            <?= $model->career_text ? '<p>' . $model->career_text . '</p>' : ''; ?>
                        </header>
                    <?php } ?>
                    <main class="lk_content">
                        <div class="existing_career">
                            <?php foreach ($user->careerFront as $career) {
                                echo $this->render('_career_element', ['career' => $career, 'can_edit' => $can_edit]);
                            } ?>
                        </div>
                        <?php if ($can_edit) { ?>
                            <div class="ip_cell w100">
                                <button class="button blue medium lk add_career">Добавить место работы</button>
                            </div>
                        <?php } ?>
                        <?= $form->field($modelform, 'career_check', ['template' => '{input}{error}{hint}'])->textInput(['style' => 'display:none']); ?>
                    </main>
                </div>
                <div class="lk_block">
                    <?php if (!empty($model->history_title) or !empty($model->history_text)) { ?>
                        <header class="lk_block_header">
                            <?= $model->history_title ? '<h1 class="lk_block_title">' . $model->history_title . '</h1>' : ''; ?>
                            <?= $model->history_text ? '<p>' . $model->history_text . '</p>' : ''; ?>
                        </header>
                    <?php } ?>
                    <main class="lk_content">
                        <?= $form->field($modelform, 'about_myself')->textArea(['placeholder' => "Например, специалист по личностному развитию, доктор экономических наук"]); ?>
                        <?= $form->field($modelform, 'history')->widget(CKEditor::class, [
                                'editorOptions' => ElFinder::ckeditorOptions(
                                        'elfinderuser',
                                        array_merge(['editorplaceholder' => 'Моя история'], \app\helpers\ckeditor\CKConfig::DEFAULT)
                                ),
                                'options' => ['class' => 'input_text'],
                                'containerOptions' => [
                                        'class' => 'editor_container',
                                ],
                        ]); ?>
                        <?= $form->field($modelform, 'video')->input('url', ['placeholder' => "Ссылка на видеопрезентацию"]); ?>
                    </main>
                </div>
                <div class="lk_block">
                    <?php if (!empty($model->docs_title) or !empty($model->docs_text)) { ?>
                        <header class="lk_block_header">
                            <?= $model->docs_title ? '<h1 class="lk_block_title">' . $model->docs_title . '</h1>' : ''; ?>
                            <?= $model->docs_text ? '<p>' . $model->docs_text . '</p>' : ''; ?>
                        </header>
                    <?php } ?>
                    <main class="lk_content">
                        <div class="mb20 pdf_container">
                            <div class="upload_pdf_list" data-maxfiles="20"
                                 data-text="Поддерживающиеся форматы PDF, ZIP, PNG, JPG, XLS, XLSX до 2 Мб"
                                 data-accept=".pdf,.zip,.png,.jpg,.xls,.xlsx" data-name="ProfileAnketa[requirements][]">
                                <div class="upload_pdf_js">
                                    <?php if (!empty($user->profile->requirements)) { ?>
                                        <?php foreach ($user->profile->requirements as $file) { ?>
                                            <div class="upload_pdf_box added">
                                                <a href="#" class="upload_pdf_link">Поддерживающиеся форматы PDF, ZIP,
                                                    PNG, JPG, XLS, XLSX до 2 Мб</a>
                                                <span class="upload_pdf_result"><b><?= $file->name; ?></b><span><?= MainHelper::prettyFileSize($file->size); ?></span></span>
                                                <a href="#" class="file_pdf_remove">x</a>
                                                <input data-file_id="<?= $file->id; ?>" type="file"
                                                       class="upload_pdf-input" name="ProfileAnketa[requirements][]"
                                                       accept=".pdf,.zip,.png,.jpg,.jpeg,.xls,.xlsx"/>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                    <?php if (count($user->profile->requirements) == 0) { ?>
                                        <div class="upload_pdf_box">
                                            <a href="#" class="upload_pdf_link">Поддерживающиеся форматы PDF, ZIP, PNG,
                                                JPG, XLS, XLSX до 2 Мб</a>
                                            <span class="upload_pdf_result"></span>
                                            <a href="#" class="file_pdf_remove">x</a>
                                            <input data-file_id="" type="file" class="upload_pdf-input"
                                                   name="ProfileAnketa[requirements][]"
                                                   accept=".pdf,.zip,.png,.jpg,.jpeg,.xls,.xlsx"/>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <a href="#" <?php if (count($user->profile->requirements) >= 20) { ?> style="display:none;" <?php } ?>
                               class="button-o more plus addMorePdf">Добавить ещё</a>
                            <?= $form->field($modelform, 'requirements_check', ['template' => '{input}{error}{hint}'])->textInput(['style' => 'display:none']); ?>
                        </div>
                        <?= $form->field($modelform, 'require_link')->textArea(['placeholder' => "Ссылка на видео"]); ?>
                    </main>
                </div>
                <?php if ($can_edit) { ?>
                    <div class="lk_block">
                        <main class="lk_content">
                            <div class="ip_cell w100">
                                <button type="submit" class="button-o lk_button_submit font-size16">Отправить на
                                    модерацию
                                </button>
                            </div>
                            <div class="ip_cell w100 lk_podpis  font-size14">
                                После успешного прохождения модерации анкета будет направлена на рассмотрение Высшего
                                экспертного совета кафедры (общий срок рассмотрения от 7 рабочих дней)
                            </div>
                        </main>
                    </div>
                <?php } else { ?>
                    <div class="lk_block">
                        <main class="lk_content">
                            <div class="ip_cell w100">
                                <a class="button-o lk_button_submit">Ваша анкета на модерации. Ожидайте решение о
                                    вступлении в Академию</a>
                            </div>

                            <div class="ip_cell w100 lk_podpis  font-size14">
                                После успешного прохождения модерации анкета будет направлена на рассмотрение Высшего
                                экспертного совета кафедры (общий срок рассмотрения от 7 рабочих дней)
                            </div>
                        </main>
                    </div>
                <?php } ?>
                <?php ActiveForm::end(); ?>
            </div>
            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
        </div>
    </main>
    <div class="modal" id="moderate_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Отправить анкету на модерацию?</div>
                <?= SettingsText::getInfo('join_academy_expert'); ?>
                <div class="modal_buttons">
                    <a href="#" class="button small go_expert_moder">Да, отправить анкету на модерацию.</a>
                    <a href="#" class="button small close_modal">Нет, продолжить редактировать анкету.</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
    <div class="modal" id="moderate_status">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Модерация анкеты</div>
                <p>Ваша анкета отправлена на модерацию. Ожидайте результата.</p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">Ок</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
    <div class="modal" id="education_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <?php
            $educationModel = new \app\models\ProfileEducation();
            $form = ActiveForm::begin([
                    'id' => 'education-form',
                    'action' => '/site/ajaxValidate/',
                    'options' => ['class' => 'education_form'],
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => false,
                    'validateOnSubmit' => true,
                    'validateOnChange' => true,
                    'validateOnType' => false,
                    'validateOnBlur' => true,
                    'fieldConfig' => [
                            'template' => '{input}{error}{hint}',
                            'inputOptions' => ['class' => 'input_text'],
                    ],
            ]); ?>
            <div class="modal_title">Образование</div>
            <input type="hidden" id="educationId" name="id">

            <div class="ip_cell w100">
                <label class="ip_label">Учебное заведение*</label>
                <?= $form->field($educationModel, 'name')->textInput(['placeholder' => "Название учебного заведения*"]); ?>
            </div>

            <div class="ip_cell w100">
                <label class="ip_label">Специализация*</label>
                <?= $form->field($educationModel, 'speciality')->textInput(['placeholder' => "Ваша специализация*"]); ?>
            </div>

            <div class="ip_cell w100">
                <label class="ip_label">Уровень*</label>
                <?= $form->field($educationModel, 'stage_id')->dropDownList($educationModel->getStageList(), ['class' => "pretty_select"]); ?>
            </div>

            <div class="ip_cell w100">
                <label class="ip_label">Период обучения</label>
                <div class="time_box years">
                    <?= $form->field($educationModel, 'study_from', ['options' => ['class' => 'ip_cell ipс_short'], 'template' => '{input}{error}{hint}'])->textInput(['placeholder' => "1990", 'class' => 'input_text ip_short maskYear datepicker-yyyy keypress']); ?>
                    <span>&mdash;</span>
                    <?= $form->field($educationModel, 'study_to', ['options' => ['class' => 'ip_cell ipс_short'], 'template' => '{input}{error}{hint}'])->textInput(['placeholder' => "1994", 'class' => 'input_text ip_short maskYear datepicker-yyyy keypress']); ?>
                </div>
            </div>

            <div class="ip_cell w100">
                <?= $form->field($educationModel, 'by_realtime', ['template' => '{input}<label>По настоящее время</label>{hint}{error}', 'options' => ['class' => 'ip_cell w49']])->checkbox(['class' => 'ch'], false); ?>
            </div>

            <div class="ip_cell w100">
                <button class="button-o lk_button_submit" type="submit">Сохранить</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="modal_overlay"></div>
    </div>
    <div class="modal" id="career_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <?php
            $educationModel = new \app\models\ProfileCareer();
            $form = ActiveForm::begin([
                    'id' => 'career-form',
                    'action' => '/site/ajaxValidate/',
                    'options' => ['class' => 'career_form'],
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => false,
                    'validateOnSubmit' => true,
                    'validateOnChange' => true,
                    'validateOnType' => false,
                    'validateOnBlur' => true,
                    'fieldConfig' => [
                            'template' => '{input}{error}{hint}',
                            'inputOptions' => ['class' => 'input_text'],
                    ],
            ]); ?>
            <div class="modal_title">Место работы</div>
            <input type="hidden" id="careerId" name="id">
            <div class="ip_cell w100">
                <label class="ip_label">Название организации*</label>
                <?= $form->field($career_form, 'name')->textInput(['placeholder' => "Название организации*"]); ?>
            </div>
            <div class="ip_cell w100">
                <label class="ip_label">Должность*</label>
                <?= $form->field($career_form, 'office')->textInput(['placeholder' => "Должность*"]); ?>
            </div>
            <div class="ip_cell w100">
                <label class="ip_label">Период работы</label>
                <div class="time_box years">
                    <?= $form->field($career_form, 'work_from', ['options' => ['class' => 'ip_cell ipс_short'], 'template' => '{input}{error}{hint}'])->textInput(['placeholder' => "1990", 'class' => 'input_text ip_short maskYear datepicker-yyyy keypress']); ?>
                    <span>&mdash;</span>
                    <?= $form->field($career_form, 'work_to', ['options' => ['class' => 'ip_cell ipс_short'], 'template' => '{input}{error}{hint}'])->textInput(['placeholder' => "1994", 'class' => 'input_text ip_short maskYear datepicker-yyyy keypress']); ?>
                </div>
            </div>
            <div class="ip_cell w100">
                <?= $form->field($career_form, 'by_realtime', ['template' => '{input}<label>По настоящее время</label>{hint}{error}', 'options' => ['class' => 'ip_cell w49']])->checkbox(['class' => 'ch'], false); ?>
            </div>
            <div class="ip_cell w100">
                <label class="ip_label">Достижения</label>
                <?= $form->field($career_form, 'achiev')->textInput(['placeholder' => "Достижения"]); ?>
            </div>
            <div class="ip_cell w100">
                <button class="button-o lk_button_submit" type="submit">Сохранить</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="modal_overlay"></div>
    </div>
    <div class="modal" id="fail_save_anketa">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Изменение анкеты</div>
                <p>Возникли ошибки при сохранении данных</p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
    <div class="modal" id="success_profile_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Изменение анкеты</div>
                <p>Успешно сохранено</p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>
<?php
if (!$can_edit) {
    $js = <<<JS
        $('#anketa-form input').attr('disabled','disabled');
        $('#anketa-form select').attr('disabled','disabled');
        $('#anketa-form textarea').attr('disabled','disabled');

        $('.file_pdf_remove').css('display','none');
        $('.addMorePdf').css('display','none');
JS;
    $this->registerJs($js);
}
$url = Url::toRoute(['/pages/profile/saveanketa']);
$url_moder = Url::toRoute(['/pages/profile/moderateme']);

$url_files = Url::toRoute(['/pages/profile/saveanketafiles']);
$url_files_rem = Url::toRoute(['/pages/profile/removeanketafiles']);

$url_get_edu = Url::toRoute(['/pages/profile/geteducation']);
$url_save_edu = Url::toRoute(['/pages/profile/saveeducation']);
$url_remove_edu = Url::toRoute(['/pages/profile/removeeducation']);

$url_get_career = Url::toRoute(['/pages/profile/getcareer']);
$url_save_career = Url::toRoute(['/pages/profile/savecareer']);
$url_remove_career = Url::toRoute(['/pages/profile/removecareer']);

$js = <<<JS
	
	function save_field(attribute, value) {
        let param = yii.getCsrfParam();
        let token = yii.getCsrfToken();
        $.ajax({
            type: 'POST',
            url: '{$url}',
            processData: true,
            dataType: 'json',
            data: {param:token,attribute:attribute,value:value},
            success: function(data) {
                if (data.status == 'success') {
                    // надо бы как-нибудь отметить, что сохранено
                } else if (data.status == 'fail') {
                    $('#fail_save_anketa .success_box p').html(data.message);
                    modalPos('#fail_save_anketa');
                }

            }
        });

	}

    // сохранение данных при редактировании
    $('body').on('change','.save_profile',function(e) {
        save_field($(this).attr('id'), $(this).val());
    });

    CKEDITOR.instances['profileanketa-history'].on('blur', function() { 
    	$('#anketa-form').yiiActiveForm('validateAttribute', 'profileanketa-history');
	    save_field('profileanketa-history', $('#profileanketa-history').val());
	});

	$('#anketa-form').yiiActiveForm('remove', 'profileanketa-history');
	$('#anketa-form').yiiActiveForm('add', {
        'id': 'profileanketa-history',
        'container': '.field-profileanketa-history',
        'input': '#profileanketa-history',
        'error': '.help-block',
        'enableAjaxValidation':true,
        'validateOnChange': false
        // 'validateOnBlur': true,
    });

    // отправка на модерацию
    $('#anketa-form').on('beforeSubmit', function(e) {
        e.preventDefault();
        modalPos('#moderate_modal');
        return false;
    });

    $('.go_expert_moder').click(function(e){
        e.preventDefault();
        closeModal('#moderate_modal');
        $.ajax({
            type: 'GET',
            url: '{$url_moder}',
            processData: true,
            dataType: 'json',
            data: {action:'moderate'},
            success: function(data) {
                if (data.status == 'success') {
                    $('#moderate_status .success_box p').html(data.message);
                    modalPos('#moderate_status');
                    setTimeout(function () {
                        location.reload(); // перезагрузить страницу.
                    }, 5000);
        } else {
                    $('#moderate_status .success_box p').html(data.message);
                    modalPos('#moderate_status');
        }
        }
        });
        return false;
    });

    // прикрепление файлов 
    $(document).on('change', '#anketa-form .upload_pdf-input', function () {
        let files = $(this)[0].files[0];
        let file_id = $(this).attr('data-file_id');
        let that = this;
        if (typeof files == 'undefined') return;
        var data = new FormData();
        data.append('files', files);
        data.append('file_id', file_id);

            $.ajax({
                type: 'POST',
            url: '{$url_files}',
            data: data,
            cache: false,
                dataType: 'json',
            processData : false,
            contentType : false, 
                success: function(data){
                    if (data.status == 'success') {
                    // задать file_id для поля
                    $(that).attr('data-file_id',data.file_id);
                } else {
                    // вывести ошибку и обнулить загруженный файл
                    // показать модалку с ошибкой
                    $(that).val('');
                    $(that).closest('.upload_pdf_box').removeClass('added');
                    $(that).parent().find('.upload_pdf_result').html('');

                        $('#success_profile_modal .success_box p').html(data.message);
                        modalPos('#success_profile_modal');
                    }
                }
            });
    });
    // удаление файлов
    $(document).on('click', '#anketa-form .file_pdf_remove', function () {
        let file_id = $($(this).parent().find('.upload_pdf-input')[0]).attr('data-file_id');
        $.ajax({
            type: 'POST',
            url: '{$url_files_rem}',
            processData: true,
            dataType: 'json',
            data: {id:file_id},
            success: function(data){
                if (data.status == 'success') {
                    // ничего не делаем - все делает скрипт в main.js
                } else if (data.status == 'fail') {
                    // выводим ошибку
                    //$('#success_profile_modal .success_box p').html(data.message);
                    //modalPos('#success_profile_modal');
                }

            }
        });
        return false;
    });

    //образование
    $('body').on('click','.add_education', function(e){
		e.preventDefault();
		modalPos('#education_modal');
		$("#education-form")[0].reset();
        $('#educationId').val('')
	});
    $('#education-form').on('submit', function(e){
    	e.preventDefault();
    	return false;
    });
    $('#education-form').on('beforeSubmit', function(event){
        let formData = new FormData($('#education-form')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url_save_edu}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    closeModal('#education_modal');
                    $("#education-form")[0].reset();
                    
                    if ($('[data-education="' + data.data.id + '"]').length > 0) {
                        $('[data-education="' + data.data.id + '"]').replaceWith(data.new_educat_html);
                    } else {
                    $('.existing_educat').append(data.new_educat_html);
                            }
                } else if (data.status == 'fail') {
                    $('#fail_save_anketa .success_box p').html(data.message);
                    modalPos('#fail_save_anketa');
                }
                $('#anketa-form').yiiActiveForm('validateAttribute', 'profileanketa-education_check');
                        },
            error: function(data) {
                $('#fail_save_anketa .success_box p').html('Произошла ошибка при отправке данных');
                modalPos('#fail_save_anketa');
                }
        });
        return false;
    });
    $('body').on('click','.edit_education', function(e) {
        let id = $(e.currentTarget).parent().data('education');
            $.ajax({
            type: 'GET',
            url: '{$url_get_edu}',
                dataType: 'json',
            data: {id: id},
                success: function(data){
                    if (data.status == 'success') {
                    let modal = $('#education_modal');
                    
                    for (var attr in data.data) {
                        if (attr == 'by_realtime') {
                            if (data.data[attr] == '1') {
                                $('#profileeducation-' + attr, modal).prop('checked', true);
                    }
                        } else {
                            $('#profileeducation-' + attr, modal).val(data.data[attr])
                        }
                    }
                    $('#profileeducation-stage_id').trigger('change');
                    $('#educationId').val(id)

		            modalPos('#education_modal');
                } else if (data.status == 'fail') {
                    $('#fail_save_anketa .success_box p').html(data.message);
                    modalPos('#fail_save_anketa');
                }
            },
            error: function(data) {
                $('#fail_save_anketa .success_box p').html('Произошла ошибка при отправке данных');
                modalPos('#fail_save_anketa');
        }
    });
	});
    $('body').on('click','.remove_education', function(e) {
        let id = $(e.currentTarget).parent().data('education');
        $.ajax({
            type: 'POST',
            url: '{$url_remove_edu}',
            dataType: 'json',
            data: {id: id},
            success: function(data){
                if (data.status == 'success') {
                    $('[data-education="' + id + '"]').remove();
                } else if (data.status == 'fail') {
                    $('#fail_save_anketa .success_box p').html(data.message);
                    modalPos('#fail_save_anketa');
                }
                $('#anketa-form').yiiActiveForm('validateAttribute', 'profileanketa-education_check');
            },
            error: function(data) {
                $('#fail_save_anketa .success_box p').html('Произошла ошибка при отправке данных');
                modalPos('#fail_save_anketa');
            }
        });
    });

    //место работы
    $('body').on('click','.add_career', function(e){
		e.preventDefault();
		modalPos('#career_modal');
		$("#career-form")[0].reset();
        $('#careerId').val('')
	});
    $('#career-form').on('submit', function(e){
    	e.preventDefault();
    	return false;
    });
    $('#career-form').on('beforeSubmit', function(event){
        let formData = new FormData($('#career-form')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url_save_career}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    closeModal('#career_modal');
                    $("#career-form")[0].reset();
                    
                    if ($('[data-career="' + data.data.id + '"]').length > 0) {
                        $('[data-career="' + data.data.id + '"]').replaceWith(data.new_career_html);
                    } else {
                    $('.existing_career').append(data.new_career_html);
                }
                } else if (data.status == 'fail') {
                    $('#fail_save_anketa .success_box p').html(data.message);
                    modalPos('#fail_save_anketa');
            }
                $('#anketa-form').yiiActiveForm('validateAttribute', 'profileanketa-career_check');
            },
            error: function(data) {
                $('#fail_save_anketa .success_box p').html('Произошла ошибка при отправке данных');
                modalPos('#fail_save_anketa');
            }
        });
        return false;
    });
    $('body').on('click','.edit_career', function(e) {
        let id = $(e.currentTarget).parent().data('career');
        $.ajax({
            type: 'GET',
            url: '{$url_get_career}',
            dataType: 'json',
            data: {id: id},
            success: function(data) {
                if (data.status == 'success') {
                    let modal = $('#career_modal');

                    for (var attr in data.data) {
                        if (attr == 'by_realtime') {
                            if (data.data[attr] == '1') {
                                $('#profilecareer-' + attr, modal).prop('checked', true);
            }
                } else {
                            $('#profilecareer-' + attr, modal).val(data.data[attr])
                        }
                    }
                    $('#careerId', modal).val(id)

		            modalPos('#career_modal');
                } else if (data.status == 'fail') {
                    $('#fail_save_anketa .success_box p').html(data.message);
                    modalPos('#fail_save_anketa');
                }
            },
            error: function(data) {
                $('#fail_save_anketa .success_box p').html('Произошла ошибка при отправке данных');
                modalPos('#fail_save_anketa');
            }
        });
    });
    $('body').on('click','.remove_career', function(e) {
        let id = $(e.currentTarget).parent().data('career');
        $.ajax({
            type: 'POST',
            url: '{$url_remove_career}',
            dataType: 'json',
            data: {id: id},
            success: function(data) {
                if (data.status == 'success') {
                    $('[data-career="' + id + '"]').remove();
                } else if (data.status == 'fail') {
                    $('#fail_save_anketa .success_box p').html(data.message);
                    modalPos('#fail_save_anketa');
                }
                $('#anketa-form').yiiActiveForm('validateAttribute', 'profileanketa-career_check');
            },
            error: function(data) {
                $('#fail_save_anketa .success_box p').html('Произошла ошибка при отправке данных');
                modalPos('#fail_save_anketa');
            }
        });
    });
JS;
$this->registerJs($js);
?>