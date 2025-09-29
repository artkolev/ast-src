<?php
$this->registerJsFile("https://securepay.tinkoff.ru/html/payForm/js/tinkoff_v2.js", ['depends' => [app\assets\AppAsset::class]]);
?>
    <main class="sec content_sec">
        <div class="container">
            <section class="shrinked_section">
                <h1><?= $model->getNameForView(); ?></h1>
                <?= $model->content; ?>
                <form id="tinkoff_form" name="TinkoffPayForm">
                    <input id="tinkoff_terminalkey" class="input_text" name="terminalkey" type="hidden"
                           value="<?= $model->terminalkey; ?>"/>
                    <input id="tinkoff_frame" class="input_text" name="frame" type="hidden"
                           value="<?= $model->is_frame ? 'true' : 'false'; ?>"/>
                    <input id="tinkoff_language" class="input_text" name="language" type="hidden" value="ru"/>
                    <input id="tinkoff_amount" class="input_text" name="amount" type="hidden"
                           value="<?= $model->price; ?>" required=""/>
                    <input id="tinkoff_order" class="input_text" name="order" type="hidden"
                           value="<?= $model->url; ?>_"/>
                    <input id="tinkoff_description" class="input_text" name="description"
                           value="<?= str_replace('"', "'", strip_tags($model->description)); ?>" type="hidden"/>
                    <input id="tinkoff_receipt" class="input_text" type="hidden" name="receipt" value="">

                    <div class="ip_cell w100">
                        <label class="ip_label" for="tinkoff_name">ФИО</label>
                        <input id="tinkoff_name" type="text" class="input_text" name="name" required>
                    </div>
                    <div class="ip_cell w100">
                        <label class="ip_label" for="tinkoff_email">E-mail</label>
                        <input id="tinkoff_email" type="email" class="input_text" name="email" required>
                    </div>
                    <div class="ip_cell w100">
                        <label class="ip_label" for="tinkoff_phone">Контактный телефон</label>
                        <input id="tinkoff_phone" type="tel" class="input_text" name="phone" required>
                    </div>
                    <div class="ip_cell w100 contact_buttons">
                        <button type="submit" class="button-o blue big">Оплатить</button>
                    </div>
                </form>
                <?= $model->content_after; ?>
            </section>
        </div>
    </main>
<?php
$rand_str = time() . rand(10, 99);
$js = <<<JS
	$('#tinkoff_order').val($('#tinkoff_order').val()+'{$rand_str}');

	$('#tinkoff_form').on('submit', function(e){
		e.preventDefault();
		let form = document.getElementById("tinkoff_form");
        let name = $('#tinkoff_description').val() || "Оплата";
        let amount = $('#tinkoff_amount').val();
        let email = $('#tinkoff_email').val();
        let phone = $('#tinkoff_phone').val();

        if (amount && email && phone) {
            $('#tinkoff_receipt').val(JSON.stringify({
                "Email": email,
                "Phone": phone,
                "EmailCompany": "{$model->email}",
                "Taxation": "{$model->taxation}",
                "Items": [
                    {
                        "Name": name,
                        "Price": amount + '00',
                        "Quantity": 1.00,
                        "Amount": amount + '00',
                        "PaymentMethod": "{$model->payment_method}",
                        "PaymentObject": "{$model->payment_object}",
                        "Tax": "{$model->tax}"
                    }
                ]
            }));
            pay(form);
        } else alert("Не все обязательные поля заполнены");
        return false;
	});
JS;
$this->registerJs($js);
