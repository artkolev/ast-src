<?php
/*
    @descr Список заказов программы ДПО в ЛК
    @var $model Class app\modules\pages\models\LKEduprogViewOrders; текущая страница
    @action pages/eduprog/eduprog-view-orders
*/

$this->registerCssFile('/css/style-moderator.css', ['depends' => [app\assets\AppAsset::class]]);
$this->registerCssFile('/css/style-blog.css', ['depends' => [app\assets\AppAsset::class]]);

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

            <div class="lk_block">
                <div class="lk_content">
                    <?php if (count($forms_list) > 0) { ?>
                        <div class="lenta-menu-noslider">
                            <?php foreach ($forms_list as $key => $form) { ?>
                                <a href="<?= Url::toRoute([$model->getUrlPath(), 'id' => $eduprog->id, 'form_id' => $form->id]); ?>"
                                   class="<?= $active_form->id == $form->id ? 'active' : '' ?>"
                                   title="<?= htmlspecialchars($form->name) ?>"><?= $form->name ?></a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <div class="flex align-center tarif-transaction-info">
                        <h4 class="lk_step_title mr30">Заказов: <?= $orders_data['count'] ?></h4>
                        <h4 class="lk_step_title">На сумму: <?= number_format($orders_data['summ'], 0, '.', ' ') ?>
                            ₽</h4>
                        <a href="<?= Url::toRoute(['/pages/eduprog/export-order-items/', 'eduprog_id' => $eduprog->id, 'form_id' => $active_form->id]); ?>"
                           class="button-o blue medium lk">Выгрузить</a>
                    </div>

                    <div class="moderator-table horizontal-scroll">
                        <div class="moderator-table-thead">
                            <div class="moderator-table-tr">
                                <!-- filtered-head - класс для кликабельного заголовка, filtered - класс для состояния отфильтрованного заголовка (синий) -->
                                <!-- классы для разной ширины колонок: table-width-small - 100px, table-width-default - 150px, table-width-middle - 210px, table-width-long - 270px
                                    если не задан никакой из этих классов, то минимальная ширина колонки - 150px, максимальная - 200px -->
                                <div class="moderator-table-head table-width-default filtered">Номер заказа</div>
                                <div class="moderator-table-head table-width-default">Позиция в заказе</div>
                                <div class="moderator-table-head table-width-middle">Плательщик</div>
                                <div class="moderator-table-head table-width-middle">E-mail плательщика</div>
                                <div class="moderator-table-head table-width-default">Дата оплаты</div>
                                <div class="moderator-table-head table-width-default">Дата возврата</div>
                                <div class="moderator-table-head table-width-default">Статус</div>
                                <div class="moderator-table-head table-width-long">Тариф</div>
                                <div class="moderator-table-head table-width-middle">Слушатель</div>
                                <div class="moderator-table-head table-width-middle">E-mail слушателя</div>
                                <div class="moderator-table-head table-width-default">Стоимость тарифа</div>
                                <div class="moderator-table-head table-width-default">Сумма заказа</div>
                            </div>
                            <?php $form = ActiveForm::begin([
                                    'id' => 'filter-form',
                                    'options' => ['class' => 'moderator-table-tr'],
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
                            <!-- Скрытые поля для корректного формирования get-параметров при аякс-фильтрации -->
                            <input type="hidden" name="id" value="<?= $eduprog->id ?>">
                            <input type="hidden" name="form_id" value="<?= $active_form->id ?>">

                            <div class="moderator-table-head-filter table-width-default">
                                <!-- Номер заказа -->

                                <?= $form->field($filter_form, 'orderNum', ['template' => '{input}<button type="button" class="clear-input-btn"></button><button class="button-search-filter" type="submit"></button>{error}{hint}', 'options' => ['class' => 'ip_cell w100 ip_cell-filter']])->textInput(['class' => 'input_text ip_search-filter', 'placeholder' => 'Номер заказа']); ?>
                            </div>
                            <div class="moderator-table-head-filter table-width-default">
                                <!-- Позиция в заказе -->
                                <?= $form->field($filter_form, 'itemNum', ['template' => '{input}<button type="button" class="clear-input-btn"></button><button class="button-search-filter" type="submit"></button>{error}{hint}', 'options' => ['class' => 'ip_cell w100 ip_cell-filter']])->textInput(['class' => 'input_text ip_search-filter', 'placeholder' => 'Позиция в заказе']); ?>
                            </div>
                            <div class="moderator-table-head-filter table-width-middle">
                                <!-- Плательщик -->
                                <?= $form->field($filter_form, 'payer_name', ['template' => '{input}<button type="button" class="clear-input-btn"></button><button class="button-search-filter" type="submit"></button>{error}{hint}', 'options' => ['class' => 'ip_cell w100 ip_cell-filter']])->textInput(['class' => 'input_text ip_search-filter', 'placeholder' => 'Плательщик']); ?>
                            </div>
                            <div class="moderator-table-head-filter table-width-middle">
                                <!-- E-mail плательщика -->
                                <?= $form->field($filter_form, 'payer_email', ['template' => '{input}<button type="button" class="clear-input-btn"></button><button class="button-search-filter" type="submit"></button>{error}{hint}', 'options' => ['class' => 'ip_cell w100 ip_cell-filter']])->textInput(['class' => 'input_text ip_search-filter', 'placeholder' => 'E-mail плательщика']); ?>
                            </div>
                            <div class="moderator-table-head-filter table-width-default">
                                <!-- Дата оплаты -->
                            </div>
                            <div class="moderator-table-head-filter table-width-default">
                                <!-- Дата возврата -->
                            </div>
                            <div class="moderator-table-head-filter table-width-default">
                                <?= $form->field($filter_form, 'status', ['template' => '<div class="custom_dropdown_box"><a href="#" class="custom_dropdown-link" data-placeholder="Статус"></a><button type="button" class="clear-dropdown-btn"></button><div class="custom_dropdown-list checkboxes_js mScrollbarCustom simplebar">{input}{error}{hint}</div></div>', 'options' => ['class' => 'ip_cell w100 ip_cell-dropdown']])->radioList(
                                        $status_list,
                                        ['item' => function ($index, $label, $name, $checked, $value) {
                                            $return = '<div class="custom_dropdown-row"><input type="radio" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked="checked"' : '') . ' class="rd custom_dropdown-choice"><label class="notmark">' . $label . '</label></div>';
                                            return $return;
                                        }
                                        ]
                                ); ?>
                            </div>
                            <div class="moderator-table-head-filter table-width-long">
                                <!-- Тариф -->
                                <?= $form->field($filter_form, 'tariff', ['template' => '<div class="custom_dropdown_box"><a href="#" class="custom_dropdown-link" data-placeholder="Тариф"></a><button type="button" class="clear-dropdown-btn"></button><div class="custom_dropdown-list checkboxes_js mScrollbarCustom simplebar">{input}{error}{hint}</div></div>', 'options' => ['class' => 'ip_cell w100 ip_cell-dropdown']])->radioList(
                                        $tariff_list,
                                        ['item' => function ($index, $label, $name, $checked, $value) {
                                            $return = '<div class="custom_dropdown-row"><input type="radio" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked="checked"' : '') . ' class="rd custom_dropdown-choice"><label class="notmark">' . $label . '</label></div>';
                                            return $return;
                                        }
                                        ]
                                ); ?>
                            </div>
                            <div class="moderator-table-head-filter table-width-middle">
                                <!-- Слушатель -->
                                <?= $form->field($filter_form, 'member_name', ['template' => '{input}<button type="button" class="clear-input-btn"></button><button class="button-search-filter" type="submit"></button>{error}{hint}', 'options' => ['class' => 'ip_cell w100 ip_cell-filter']])->textInput(['class' => 'input_text ip_search-filter', 'placeholder' => 'Слушатель']); ?>
                            </div>
                            <div class="moderator-table-head-filter table-width-middle">
                                <!-- Email Слушателя -->
                                <?= $form->field($filter_form, 'member_email', ['template' => '{input}<button type="button" class="clear-input-btn"></button><button class="button-search-filter" type="submit"></button>{error}{hint}', 'options' => ['class' => 'ip_cell w100 ip_cell-filter']])->textInput(['class' => 'input_text ip_search-filter', 'placeholder' => 'E-mail слушателя']); ?>
                            </div>
                            <div class="moderator-table-head-filter table-width-default">
                                <!-- Стоимость тарифа -->
                            </div>
                            <div class="moderator-table-head-filter table-width-default">
                                <!-- Сумма заказа -->
                            </div>
                            <?php ActiveForm::end(); ?>
                        </div>
                        <div id="orderitems_list_table" class="moderator-table-tbody">
                            <?php if (!empty($order_items)) {
                                foreach ($order_items as $order_item) {
                                    echo $this->render('_expert_orders_list_card', ['order_item' => $order_item]);
                                }
                            } else {
                                echo '<div class="table-empty-cells-text">Заказы отсутствуют</div>';
                            } ?>
                        </div>
                    </div>
                    <div class="moderator-table-pager">
                        <div id="pager_content">
                            <?= \app\widgets\pagination\LinkPager::widget(['pages' => $pages, 'container' => '#orderitems_list_table']); ?>
                        </div>
                    </div>
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
        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>

<?php
$url = $model->getUrlPath();

$js = <<<JS
    $('body').on('change','.custom_dropdown-choice', function(e) {
        $(this).closest('form').submit();
    });

    // фильтрация аяксом
    $('#filter-form').on('beforeSubmit', function(e){
    	// удалить csrf из адреса
    	$(this).find('input[name=_csrf]').attr('disabled',true);
    	let new_url = $(this).serialize(); 
        $.ajax({
            type: 'GET',
            url: '{$url}?'+new_url,
            processData: true,
            dataType: 'json',
            success: function(data) {
                if (data.status == 'success') {
                    // заменить содержимое таблицы на полученный html
                    $('#orderitems_list_table').html(data.html);
					$('#pager_content').html(data.pager);
                } else {
                    // показать модалку с ошибкой
                    $.fancybox.open($('#filter_error'));
                }
				history.pushState(null, null, '{$url}?'+new_url);
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

