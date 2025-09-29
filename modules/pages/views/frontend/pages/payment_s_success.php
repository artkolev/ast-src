<main class="sec content_sec">
    <div class="container">
        <section class="shrinked_section">
            <h1><?= $model->getNameForView(); ?></h1>
            <?= $model->content; ?>
            <?php
            // параметры из GET
            // 508 - номер платежа в системе paykeeper (не номер заказа)
            // ["payment_id"]=> string(3) "508" ["clientid"]=> string(31) "Имя Пользователя" ["result"]=> string(7) "success"
            ?>
        </section>
    </div>
</main>