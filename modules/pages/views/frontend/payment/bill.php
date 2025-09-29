<main class="sec content_sec gray_bg">
    <div class="container wide lk-container">

        <div class="lk_maincol">
            <?php /*
            <div class="ip_cell w100">
                <a href="/profile/orders/" class="button-o back">Вернуться к заказам</a>
            </div>
            */ ?>

            <!-- успешно сформированный счет -->
            <div class="lk_block">
                <main class="lk_content">
                    <h4 class="lk-order-title">Счёт на оплату успешно сформирован</h4>
                    <p>
                        Счёт будет загружен на ваш компьютер. <br>
                        Если загрузка не началась автоматически, пожалуйста, <a href="<?= $bill->getPdfLink(); ?>">нажмите
                            здесь</a>
                    </p>
                </main>
            </div>

        </div>
        <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
    </div>
</main>
<script type="text/javascript">
    setTimeout('document.location.href="<?= $bill->getPdfLink(); ?>";', 1000);
</script>