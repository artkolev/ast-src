<section class="sec content_sec">
    <div class="container">
        <section class="register_form_success">
            <div class="ib">
                <img src="/img/envelope_big.svg" alt=""/>
                <h1><?= $model->getNameForView(); ?></h1>
                <?= $model->content; ?>
                <a href="/" class="button small">ОК, вернуться на главную</a>
            </div>
        </section>
    </div>
</section>
