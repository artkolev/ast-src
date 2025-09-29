<section class="sec content_sec search-rezult">
    <div class="container wide">
        <div class="columns_box">
            <div class="main_col">
                <h1 class="search-rezult_title"><?= $model->getNameForView(); ?></h1>
                <?= $model->content; ?>
                <form method="get" class="search-form">
                    <input type="text" name="query" value="<?= $search_text; ?>" class="search-form_input"
                           placeholder="Искать на сайте"/>
                    <button type="submit" class="button long search-form_btn">Найти</button>
                </form>
            </div>
        </div>
        <div class="container middle">
            <div class="search-rezult__empty">
                <p>Обычно запрос представляет из себя просто одно или несколько слов, например: <b>Фасилитация.</b>
                    По такому запросу будут найдены страницы, на которых встречается введенное слово.</p>
                <p>Вы можете ввести более конкретный запрос, либо задать интересующий вас вопрос на email <a
                            href="mailto:askme@ast-academy.ru">askme@ast-academy.ru</a>.</p>
            </div>
        </div>
    </div>
</section>