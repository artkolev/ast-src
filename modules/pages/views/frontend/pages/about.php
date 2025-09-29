<?php if (!empty($model->about_slider)) {
    $visible_about_slider = [];
    foreach ($model->about_slider as $slider_item) {
        if ($slider_item['visible'] != 1) continue;
        $visible_about_slider[] = $slider_item;
    }
    if (count($visible_about_slider) > 1) { ?>
        <section class="sec slider_sec">
            <div class="homeslider owl-carousel dotsNav arrowsNav" data-autoplay="true" data-autoswap="5000">
                <?php foreach ($visible_about_slider as $item) { ?>
                    <div class="item"
                         style="background-image: url(<?= $model->getThumb('sliderimage', 'main', false, $item['image'][0]); ?>)">
                        <div class="homeslider_infobox">
                            <h2><?= $item['name']; ?></h2>
                            <p><?= $item['content']; ?></p>
                            <?php if (!empty($item['button_name']) or !empty($item['button_name2'])) { ?>
                                <div class="btns-inner">
                                    <?php if ($item['button_name']) { ?>
                                        <a href="<?= $item['button_link']; ?>"
                                           class="button <?= $item['button_class'] ?>"><?= $item['button_name'] ?></a>
                                    <?php } ?>
                                    <?php if ($item['button_name2']) { ?>
                                        &nbsp;<a href="<?= $item['button_link2']; ?>"
                                                 class="button-o <?= $item['button_class2']; ?>"><?= $item['button_name2']; ?></a>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </section>
    <?php } else {
        $item = $visible_about_slider[0]; ?>
        <section class="sec no_slider_sec"
                 style="background-image: url(<?= $model->getThumb('sliderimage', 'main', false, $item['image'][0]); ?>?>)">
            <div class="container">
                <div>
                    <h2><?= $item['name']; ?></h2>
                    <p><?= $item['content']; ?></p>
                    <?php if (!empty($item['button_name']) or !empty($item['button_name2'])) { ?>
                        <div class="btns-inner">
                            <?php if ($item['button_name']) { ?>
                                <a href="<?= $item['button_link']; ?>"
                                   class="button <?= $item['button_class'] ?>"><?= $item['button_name'] ?></a>
                            <?php } ?>
                            <?php if ($item['button_name2']) { ?>
                                &nbsp;<a href="<?= $item['button_link2']; ?>"
                                         class="button-o <?= $item['button_class2']; ?>"><?= $item['button_name2']; ?></a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>
    <?php } ?>
<?php } ?>
<?php if (!empty($model->about_preims)) { ?>
    <section class="sec content_sec">
        <div class="container wide">
            <div class="prs_box">
                <?php foreach ($model->about_preims as $item) {
                    if ($item['visible'] != 1) continue; ?>
                    <article class="text_icon pr_item">
                        <span class="icon"><i class="fa fa-check"></i></span>
                        <div><?= $item['name']; ?></div>
                    </article>
                <?php } ?>
            </div>
        </div>
    </section>
<?php } ?>
<?php if (!empty($model->content)) { ?>
    <section class="sec content_sec">
        <div class="container">
            <?= $model->content; ?>
        </div>
    </section>
<?php } ?>
<?php if (!empty($model->about_perk)) { ?>
    <section class="sec content_sec gray_bg">
        <div class="container middle">
            <h1 class="centered mb40"><?= (!empty($model->academy_title) ? $model->academy_title : 'Академия - экспертам'); ?></h1>
            <div class="perks_box">
                <?php foreach ($model->about_perk as $item) {
                    if ($item['visible'] != 1) continue; ?>
                    <article class="perks_item">
                        <div class="perk_text"><?= $item['name']; ?></div>
                        <div class="perk_img"><img
                                    src="<?= $model->getThumb('perkimage', 'main', false, $item['image'][0]); ?>"
                                    alt="<?= str_replace('"', '&quot;', $item['name']); ?>"/></div>
                    </article>
                <?php } ?>
            </div>
            <?php if (!empty($model->presentation)) { ?>
                <div class="buttons_box centered">
                    <a href="<?= $model->getFile('presentation'); ?>" class="button">Скачать презентацию</a>
                </div>
            <?php } ?>
        </div>
    </section>
<?php } ?>
<?php if (!empty($model->about_howto)) { ?>
    <section id="how_to_expert" class="sec content_sec">
        <div class="container middle">
            <h2 class="centered">Как стать экспертом Академии</h2>
            <p class="subheader centered">Решение о приеме эксперта в Академию принимают сами члены профессионального
                сообщества, поэтому им надо познакомиться с вами.</p>
            <div class="howto_box">
                <?php foreach ($model->about_howto as $item) {
                    if ($item['visible'] != 1) continue; ?>
                    <article class="howto_item">
                        <div class="howto_img"><img
                                    src="<?= $model->getThumb('howtoimage', 'main', false, $item['image'][0]); ?>"
                                    alt="<?= str_replace('"', '&quot;', $item['name']); ?>"/></div>
                        <div class="howto_text">
                            <span class="highlight"><?= $item['name']; ?></span> <?= $item['description']; ?>
                        </div>
                    </article>
                <?php } ?>
            </div>
            <div class="buttons_box centered">
                <a href="/register/" class="button">Стать экспертом</a>
            </div>
        </div>
    </section>
<?php } ?>

<section class="sec content_sec">
    <div class="container">
        <div class="ask_titles">
            <h2 class="centered">Если у вас есть вопросы, мы всегда готовы на них ответить</h2>
            <p class="subheader centered">Напишите нам по адресу: <a href="mailto:askme@ast-academy.ru">askme@ast-academy.ru</a>
            </p>
        </div>
    </div>
</section>
