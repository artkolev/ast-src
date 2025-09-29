<?php
/*
    @descr Список учасников программы ДПО в ЛК (главная страница просмотра программы)
    @var $model Class app\modules\pages\models\LKEduprogViewMembers; текущая страница
    @action pages/eduprog/eduprog-view-members
*/

use app\helpers\MainHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">
        <div class="lk_maincol">
            <?php if (!empty($eduprog_catalog)) { ?>
                <div class="ip_cell w100">
                    <a href="<?= $eduprog_catalog->getUrlPath(); ?>" class="button-o back">Все программы</a>
                </div>
            <?php } ?>

            <?= $this->render('_expert_eduprog_card', ['eduprog' => $eduprog, 'eduprog_catalog' => $eduprog_catalog]); ?>
            <?= $this->render('_expert_view_submenu', ['model' => $model, 'eduprog' => $eduprog]); ?>

            <div class="lk-block-no-bg">
                <div class="flex align-center tarif-transaction-info">
                    <h4 class="lk_step_title mr30">Слушателей: <?= array_sum($total_info) ?></h4>
                    <h4 class="lk_step_title mr30">Обучаются: <?= (int)$total_info['active'] ?></h4>
                    <h4 class="lk_step_title"> Ожидают подтверждения: <?= (int)$total_info['waiting'] ?></h4>
                    <a href="<?= Url::toRoute(['/pages/eduprog/export-members/', 'eduprog_id' => $eduprog->id]); ?>"
                       class="button-o blue medium lk">Выгрузить в Excel</a>
                </div>
                <?php $form = ActiveForm::begin([
                        'id' => 'filter-form',
                    // 'action' => '/site/ajaxValidate/',
                        'options' => ['class' => 'filters_row'],
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => true,
                        'validateOnSubmit' => true,
                        'validateOnChange' => false,
                        'validateOnType' => false,
                        'validateOnBlur' => false,
                        'fieldConfig' => [
                                'options' => ['class' => 'ip_cell w100'],
                                'template' => '{label}{input}{error}{hint}',
                                'inputOptions' => ['class' => 'input_text'],
                                'labelOptions' => ['class' => 'ip_label'],
                        ],
                ]); ?>
                <?= $form->field($filter_form, 'name', ['template' => '{input}<button type="button" class="clear-input-btn"></button><button class="button-search-filter" type="submit"></button>{error}{hint}', 'options' => ['class' => 'ip_cell ip_cell-filter']])->textInput(['class' => 'input_text ip_search-filter', 'placeholder' => 'ФИО, Номер']); ?>

                <?= $form->field($filter_form, 'email', ['template' => '{input}<button type="button" class="clear-input-btn"></button><button class="button-search-filter" type="submit"></button>{error}{hint}', 'options' => ['class' => 'ip_cell ip_cell-filter']])->textInput(['class' => 'input_text ip_search-filter', 'placeholder' => 'Email']); ?>

                <?= $form->field($filter_form, 'tariff', ['template' => '<div class="custom_dropdown_box"><a href="#" class="custom_dropdown-link" data-placeholder="Тариф"></a><button type="button" class="clear-dropdown-btn"></button><div class="custom_dropdown-list checkboxes_js mScrollbarCustom simplebar">{input}{error}{hint}</div></div>', 'options' => ['class' => 'ip_cell ip_cell-dropdown']])->radioList(
                        $tariff_list,
                        ['item' => function ($index, $label, $name, $checked, $value) {
                            $return = '<div class="custom_dropdown-row"><input type="radio" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked="checked"' : '') . ' class="rd custom_dropdown-choice"><label class="notmark">' . $label . '</label></div>';
                            return $return;
                        }
                        ]
                ); ?>

                <?= $form->field($filter_form, 'status', ['template' => '<div class="custom_dropdown_box"><a href="#" class="custom_dropdown-link" data-placeholder="Статус"></a><button type="button" class="clear-dropdown-btn"></button><div class="custom_dropdown-list checkboxes_js mScrollbarCustom simplebar">{input}{error}{hint}</div></div>' . $filter_form->getQuestion('status'), 'options' => ['class' => 'ip_cell flex ip_cell-status']])->radioList(
                        $status_list,
                        ['item' => function ($index, $label, $name, $checked, $value) {
                            $return = '<div class="custom_dropdown-row"><input type="radio" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked="checked"' : '') . ' class="rd custom_dropdown-choice"><label class="notmark">' . $label . '</label></div>';
                            return $return;
                        }
                        ]
                ); ?>
                <?php ActiveForm::end(); ?>
                <?php if (!empty($members)) { ?>
                    <div class="tarif-table-wrapper">
                        <table id="members_list_table" class="table tarif-table participants-table">
                            <?php foreach ($members as $member) {
                                echo $this->render('_expert_members_list_card', ['member' => $member, 'member_url' => $member_url]);
                            } ?>
                        </table>
                        <!-- <div class="ip_cell w100">
                            <button class="button long w100 w100 show-more-tarif-js">Показать больше</button>
                        </div> -->
                    </div>
                <?php } else { ?>
                    <div class="tarif-table-wrapper">
                        <table id="members_list_table" class="table tarif-table participants-table">
                            <tr>
                                <td colspan="4"> Слушатели еще не зарегистрировались
                            </tr>
                        </table>
                    </div>
                <?php } ?>

                <?= $this->render('_change_status_member_engine', ['member_type' => 'member_list']); ?>

                <div class="modal" id="filter_error">
                    <div class="modal_content modal_content-mini">
                        <a href="#" class="modal_close" data-fancybox-close>x</a>
                        <h2 class="modal_title modal_title-mini">
                            Во время фильтрации возникла ошибка
                        </h2>
                        <div class="modal_text modal_text-big modal_text-center">Обновите страницу и попробуйте еще
                            раз
                        </div>
                    </div>
                    <div class="modal_overlay"></div>
                </div>
            </div>
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>

<?php
$js = <<<JS
    $('body').on('change','.custom_dropdown-choice', function(e) {
        $(this).closest('form').submit();
    });

    // фильтрация аяксом
    $('#filter-form').on('beforeSubmit', function(event){
        var formData = new FormData($('#filter-form')[0]);
        $.ajax({
            type: 'POST',
            url: window.location.href,
            contentType: false,
            processData: false,
            dataType: 'json',
            data: formData,
            success: function(data) {
                if (data.status == 'success') {
                    // заменить содержимое таблицы на полученный html
                    $('#members_list_table').html(data.html_data);
                } else {
                    // показать модалку с ошибкой
                    $.fancybox.open($('#filter_error'));
                }
            }
        });
        return false;
    });
    $('#filter-form').on('submit', function(e){
        e.preventDefault();
        return false;
    });
JS;
$this->registerJs($js);
?>

