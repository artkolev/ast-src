<main class="sec services-banner">
    <div class="services-banner-img visible-over650"
         style="background: linear-gradient(0deg, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url(<?= $model->getThumb('image', 'main'); ?>) center no-repeat; background-size: cover;"></div>
    <div class="services-banner-img visible-less650"
         style="background: linear-gradient(0deg, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url(<?= $model->getThumb('image_mobile', 'main'); ?>) center no-repeat; background-size: cover;"></div>
    <div class="container wide">
        <h1><?= $model->getNameForView(); ?></h1>
        <?php if (!empty($model->subtitle)) { ?>
            <p class="subheader"><?= $model->subtitle; ?></p>
        <?php } ?>
    </div>
</main>

<main class="sec content_sec">
    <?php
    $counter = 0;
    foreach ($target_audience as $ta) {
        $inc = false; ?>
        <?php if (!empty($service_types = $ta->serviceTypes)) { ?>
            <?php if ($counter != 3) {
                $inc = true; ?>
                <div class="sec services-kind-block services-slider-block">
                    <div class="container wide">
                        <h2><a href="<?= $ta->getUrlPath(); ?>"><?= $ta->name; ?></a></h2>
                    </div>
                    <div class="container services-slider-container">
                        <div class="services-square-slider owl-carousel owl-theme">
                            <?php foreach ($service_types as $service_type) { ?>
                                <a href="<?= $service_type->getUrlPath(); ?>" class="services-subcategory-slide">
                                    <div class="services-subcategory-bg visible-over650"
                                         style="background: url(<?= $service_type->getThumb('image', 'main'); ?>) center no-repeat #F8F8F8; background-size: cover;"></div>
                                    <div class="services-subcategory-bg visible-less650"
                                         style="background: url(<?= $service_type->getThumb('image_mobile', 'main'); ?>) center no-repeat #F8F8F8; background-size: cover;"></div>
                                    <div class="services-subcategory-slide-name"><?= $service_type->name; ?></div>
                                </a>
                            <?php } ?>
                            <!-- <div class="services-square-slide-fake"></div>
                            <div class="services-square-slide-fake"></div> -->
                        </div>
                    </div>
                </div>
            <?php } elseif ($counter == 3) {
                $inc = true; ?>
                <div class="sec services-kind-block services-slider-block">
                    <div class="container wide">
                        <h2><a href="<?= $ta->getUrlPath(); ?>"><?= $ta->name; ?></a></h2>
                    </div>
                    <div class="container services-slider-container">
                        <div class="services-triple-slider owl-carousel owl-theme">
                            <?php foreach ($service_types as $service_type) { ?>
                                <a href="<?= $service_type->getUrlPath(); ?>" class="services-triple-slide">
                                    <div class="services-triple-slide-bg visible-over650"
                                         style="background: url(<?= $service_type->getThumb('image', 'main'); ?>) center no-repeat #F8F8F8; background-size: cover;"></div>
                                    <div class="services-triple-slide-bg visible-less650"
                                         style="background: url(<?= $service_type->getThumb('image_mobile', 'main'); ?>) center no-repeat #F8F8F8; background-size: cover;"></div>
                                    <div class="services-triple-slide-name"><?= $service_type->name; ?></div>
                                    <div class="services-triple-slide-text"><?= $service_type->subtitle; ?></div>
                                </a>
                            <?php } ?>
                            <!-- <div class="services-triple-slide-fake"></div>
                            <div class="services-triple-slide-fake"></div> -->
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>

        <?php if (!empty($target_audience)) { ?>
            <?php if ($counter == 1) { ?>
                <div class="sec services-kind-block services-slider-block">
                    <div class="container wide">
                        <h2>Категории</h2>
                    </div>
                    <div class="container services-slider-container">
                        <div class="services-square-slider owl-carousel owl-theme">
                            <?php foreach ($target_audience as $ta) { ?>
                                <a href="<?= $ta->getUrlPath(); ?>" class="services-square-slide">
                                    <div class="services-square-slide-bg visible-over650"
                                         style="background: url(<?= $ta->getThumb('image', 'main'); ?>) center no-repeat #F8F8F8; background-size: cover;"></div>
                                    <div class="services-square-slide-bg visible-less650"
                                         style="background: url(<?= $ta->getThumb('image_mobile', 'main'); ?>) center no-repeat #F8F8F8; background-size: cover;"></div>
                                    <div class="services-square-slide-name"><?= $ta->name; ?></div>
                                </a>
                            <?php } ?>
                            <!-- <div class="services-square-slide-fake"></div>
                            <div class="services-square-slide-fake"></div> -->
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>

        <?php if (!empty($services = $random_type->getRandomServices(20))) { ?>
            <?php if ($counter == 2) { ?>
                <div class="sec services-kind-block services-slider-block">
                    <div class="container wide">
                        <h2><?= $random_type->name; ?></h2>
                    </div>
                    <div class="container services-slider-container">
                        <div class="services-square-slider services-expert-slider owl-carousel owl-theme">
                            <?php foreach ($services as $service) { ?>
                                <div class="services-expert-slide">
                                    <a href="<?= $service->getUrlPath(); ?>">
                                        <?php if ($service->user) { ?>
                                            <div class="services-expert-slide-img">
                                                <img src="<?= $service->user->profile->getThumb('image', 'prev'); ?>"
                                                     alt="">
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
        <?php } ?>
        <?php
        if ($inc) {
            $counter++;
        };
    } ?>

    <?php if (!empty($target_audience_catalog->getPopular())) { ?>
        <div class="sec services-kind-block services-slider-block">
            <div class="container wide">
                <h2>Популярные виды услуг</h2>
            </div>
            <div class="container services-slider-container">
                <div class="services-square-slider owl-carousel owl-theme">
                    <?php foreach ($target_audience_catalog->getPopular() as $service_type) { ?>
                        <a href="<?= $service_type->getUrlPath(); ?>" class="services-subcategory-slide">
                            <div class="services-subcategory-bg visible-over650"
                                 style="background: url(<?= $service_type->getThumb('image', 'main'); ?>) center no-repeat #F8F8F8; background-size: cover;"></div>
                            <div class="services-subcategory-bg visible-less650"
                                 style="background: url(<?= $service_type->getThumb('image_mobile', 'main'); ?>) center no-repeat #F8F8F8; background-size: cover;"></div>
                            <div class="services-subcategory-slide-name"><?= $service_type->name; ?></div>
                        </a>
                    <?php } ?>
                    <!-- <div class="services-square-slide-fake"></div>
                    <div class="services-square-slide-fake"></div> -->
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="sec services-seo-block">
        <div class="container wide">
            <?= $model->content_seo; ?>
        </div>
    </div>
</main>