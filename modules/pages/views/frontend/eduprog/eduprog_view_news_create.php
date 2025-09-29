<?php
/*
    @descr Страница Создания, редактирования, просмотра, копирования новости для программы ДПО в ЛК 
    @var $model Class app\modules\pages\models\LKEduprogViewNewsCreate; текущая страница
    @var $news_form Class app\models\LKEduprogNews; модель формы
    @var $eduprog_model Class app\modules\eduprog\models\Eduprog; модель программы ДПО, для которой создаётся/редактируется новость
    @var $original Class app\modules\eduprog\models\News; текущая новость
    @var $message_page Class app\modules\pages\models\LKEduprogViewNews; страница списка новостей

    @action pages/eduprog/eduprog-view-news-create
*/

use app\helpers\MainHelper;
use app\modules\eduprog\models\News;
use kitsunefet\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <div class="ip_cell w100">
                    <?php if ($message_page) {
                        echo Html::a('Вернуться', Url::toRoute([$message_page->getUrlPath(), 'id' => $eduprog_model->id]), ['class' => 'button-o back']); ?>
                    <?php } ?>
                </div>
                <div class="lk-block-header-no-bg">
                    <h1 class="lk_block_title-big"><?= ($news_form->id ? 'Редактировать новость' : 'Создать новость'); ?></h1>
                    <div class="lk_block_subtitle"><?= $model->content; ?></div>
                </div>
                <?php $form = ActiveForm::begin([
                        'id' => 'eduprog-news-form',
                        'action' => '/site/ajaxValidate/',
                        'options' => ['class' => 'marked'],
                        'enableAjaxValidation' => true,
                        'enableClientValidation' => true,
                        'validateOnSubmit' => true,
                        'validateOnChange' => true,
                        'validateOnType' => false,
                        'validateOnBlur' => true,
                        'fieldConfig' => [
                                'options' => ['class' => 'ip_cell w100'],
                                'template' => '{label}{input}{error}{hint}',
                                'inputOptions' => ['class' => 'input_text'],
                                'labelOptions' => ['class' => 'ip_label'],
                        ],
                ]); ?>
                <?= $form->field($news_form, 'id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>
                <?= $form->field($news_form, 'eduprog_id', ['options' => ['style' => 'display:none;']])->hiddenInput(); ?>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb15">Тема</h4>
                        <?= $form->field($news_form, 'name', ['template' => '<div class="symbols_counter_box">{input}<span class="symbols_counter"></span></div>{error}{hint}'])->textInput(['autocomplete' => 'off', 'placeholder' => "Заголовок", 'class' => 'input_text limitedSybmbols', 'maxlength' => 90]); ?>
                    </main>
                </div>
                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20 mb15">Кому отправить</h4>
                        <div class="need-show-ch-wrap">
                            <?= $form->field($news_form, 'recipient', ['options' => ['class' => 'ip_cell ip_cell-format w100 mb10'], 'template' => '{input}{error}{hint}'])->radioList(
                                    News::RECIPIENT_LIST,
                                    ['item' => function ($index, $label, $name, $checked, $value) {
                                        $return = '<div class="ip_cell w100 mb10"><input type="radio" name="' . $name . '" value="' . $value . '" data-value="' . ($value == News::RECIPIENT_SELECT) . '" ' . ($checked ? 'checked="checked"' : '') . ' class="ch need-show-ch"><label class="notmark">' . $label . '</label></div>';
                                        return $return;
                                    }
                                    ]
                            ); ?>
                            <?= $form->field($news_form, 'recipient_users', ['options' => ['class' => 'ip_cell w100 need-show', 'style' => 'display: none;'], 'template' => '<label class="ip_label">Кому:</label>{input}{error}{hint}'])->dropDownList($news_form->getMembersList($eduprog_model), ['class' => "pretty_tags_ns", 'multiple' => 'multiple', 'style' => 'width:100%']); ?>
                        </div>
                    </main>
                </div>

                <div class="lk_block">
                    <main class="lk_content">
                        <h4 class="lk_step_title font20">Текст</h4>
                        <?= $form->field($news_form, 'content', ['template' => '{input}{error}{hint}'])->widget(CKEditor::class, [
                                'editorOptions' => ElFinder::ckeditorOptions(
                                        'elfinderuser',
                                        array_merge(['editorplaceholder' => "Введите текст новости"], \app\helpers\ckeditor\CKConfig::add_charlimit(\app\helpers\ckeditor\CKConfig::DEFAULT, 2000))
                                ),
                                'containerOptions' => [
                                        'class' => 'editor_container',
                                ],
                        ]); ?>
                        <?= $form->field($news_form, 'has_tariff_button', ['template' => '{input}<label>Добавить в конце текста кнопку «Оплатить» (ведет на выбор тарифа в программе)</label>{hint}{error}', 'options' => ['class' => 'ip_cell w100 mb0']])->checkbox(['class' => 'ch'], false); ?>
                    </main>
                </div>

                <div class="lk_block">
                    <div class="lk_content">
                        <?= $form->field($news_form, 'start_publish_late', ['options' => ['class' => 'ip_cell ip_cell-format w100 mb10'], 'template' => '{input}{error}{hint}'])->radioList(
                                ['Опубликовать сейчас', 'Запланировать публикацию'],
                                ['item' => function ($index, $label, $name, $checked, $value) {
                                    $return = '<div class="ip_cell w100"><input type="radio" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked="checked"' : '') . ' class="ch ' . ($value ? 'need-delay-ch' : 'need-send-ch') . '"><label class="notmark">' . $label . '</label></div>';
                                    return $return;
                                }
                                ]
                        ); ?>
                        <div class="ip_cell ip_cell-event-date w100 mb0 need-delay-ch-wrap" style="display: none;">
                            <?= $form->field($news_form, 'start_publish_date', ['template' => '{label}{input}{error}{hint}', 'options' => ['class' => 'ip_cell datarange_ipc mr20']])->textInput(['placeholder' => "Дата начала", 'class' => 'input_text datepicker']); ?>

                            <?= $form->field($news_form, 'start_publish_time', ['template' => '{label}{input}{error}{hint}', 'options' => ['class' => 'ip_cell datarange_ipc mr20']])->input('time', ['placeholder' => "__:__", 'class' => 'input_text']); ?>
                        </div>
                    </div>
                </div>

                <div class="lk_block need-send-ch-wrap">
                    <div class="lk_content">
                        <div class="ip_cell w100 flex align-center justify-between buttons-wrapper mb0">
                            <div class="more-event-forms-text">Проверьте текст. После публикации его нельзя будет
                                редактировать.
                            </div>
                            <button type="submit" class="button blue medium lk">Опубликовать</button>
                        </div>
                    </div>
                </div>

                <div class="lk_block need-delay-ch-wrap" style="display: none;">
                    <div class="lk_content">
                        <div class="ip_cell w100 flex align-center justify-between buttons-wrapper mb0">
                            <div class="more-event-forms-text">Проверьте текст. После публикации его нельзя будет
                                редактировать.
                            </div>
                            <button type="submit" class="button blue medium lk">Запланировать</button>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
        </div>
    </main>
    <div class="modal" id="fail_service_modal">
        <div class="modal_content">
            <a href="#" class="modal_close">x</a>
            <div class="success_box">
                <div class="modal_title">Ошибка создания новости</div>
                <p>При сохранении данных возникли ошибки. <?= \app\helpers\MainHelper::getHelpText() ?></p>
                <div class="modal_buttons">
                    <a href="#" class="button small close_modal">ОК</a>
                </div>
            </div>
        </div>
        <div class="modal_overlay"></div>
    </div>

<?php
$url = Url::toRoute(['/pages/eduprog/save-news/']);
$js = <<<JS
    $('#eduprog-news-form').on('beforeSubmit', function(event){
        var formData = new FormData($('#eduprog-news-form')[0]);
        $.ajax({
            type: 'POST',
            url: '{$url}',
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    // переадресация на список новостей
                    window.location.href = data.redirect_to;
                } else {
                    // показать модалку с ошибкой
                    $('#fail_service_modal .modal_title').html('Ошибка сохранения новости');
                    $('#fail_service_modal p').html(data.message);
                    modalPos('#fail_service_modal');
                }
            }
        });
        return false;
    });
    $('#eduprog-news-form').on('submit', function(e){
        e.preventDefault();
        return false;
    });
JS;
$this->registerJs($js);
?>