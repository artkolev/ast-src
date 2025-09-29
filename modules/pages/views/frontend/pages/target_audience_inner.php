<main class="sec content_sec">
    <div class="container wide">
        <h1 class="services-block-title"><a href="<?= $model->getUrlPath(); ?>"
                                            class="services-back-button"></a><?= $model->name; ?></h1>
        <div class="subheader"></div>

        <div class="services-subcategory-list">
            <?php foreach ($model->serviceTypes as $service_type) { ?>
                <a href="<?= $service_type->getUrlPath(); ?>" class="services-subcategory">
                    <div class="services-subcategory-bg visible-over650"
                         style="background: url(<?= $service_type->getThumb('image', 'main'); ?>) center no-repeat #F8F8F8; background-size: cover;"></div>
                    <div class="services-subcategory-bg visible-less650"
                         style="background: url(<?= $service_type->getThumb('image_mobile', 'main'); ?>) center no-repeat #F8F8F8; background-size: cover;"></div>
                    <div class="services-subcategory-name"><?= $service_type->name; ?></div>
                </a>
            <?php } ?>
        </div>
    </div>

    <div class="sec services-category-list-block visible-over650">
        <div class="container wide">
            <h2>Категории</h2>
            <div class="services-category-list">
                <?php foreach ($target_audience_list as $ta) { ?>
                    <?php if ($ta->url) { ?>
                        <a href="<?= $ta->getUrlPath(); ?>"
                           class="services-category <?= ($model->id == $ta->id) ? ' active ' : ''; ?>"><?= $ta->name; ?></a>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>

    <?php if (!empty($services = $model->getRandomServices(20))) { ?>
        <div class="sec services-kind-block services-slider-block">
            <div class="container wide">
                <h2>Вас могут заинтересовать</h2>
            </div>
            <div class="container services-slider-container">
                <div class="services-square-slider services-expert-slider owl-carousel owl-theme">
                    <?php foreach ($services as $service) { ?>
                        <div class="services-expert-slide">
                            <a href="<?= $service->getUrlPath(); ?>">
                                <?php if ($service->user) { ?>
                                    <div class="services-expert-slide-img">
                                        <img src="<?= $service->user->profile->getThumb('image', 'prev'); ?>" alt="">
                                    </div>
                                    <div class="services-expert-slide-name"><?= $service->user->profile->getHalfname('<br>'); ?></div>
                                <?php } ?>
                                <div class="services-expert-slide-service"><?= $service->name; ?></div>
                            </a>
                        </div>
                    <?php } ?>
                    <!-- <div class="services-square-slide-fake"></div>
                    <div class="services-square-slide-fake"></div> -->
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="sec services-seo-block">
        <div class="container wide">
            <?= $model->content; ?>
        </div>
    </div>
</main>