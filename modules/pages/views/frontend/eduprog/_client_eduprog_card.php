<?php
/*
    отображение карточки программы в ЛК Клиента
*/

use app\helpers\MainHelper;

$member = $eduprog->getMemberByUser(Yii::$app->user->identity->id);
?>
<div class="section-event-page-preview">
    <!-- мобильная обложка здесь (если вдруг они разные) -->
    <div class="section-event-page-preview-img mobile-visible">
        <img src="<?= $eduprog->getThumb('image', 'main'); ?>" alt="<?= htmlspecialchars($eduprog->name); ?>"
             loading="lazy">
    </div>
    <div class="section-event-page-preview-short">
        <div class="event-page-short date">
            <?= $eduprog->getEduprogDateForView(); ?>
        </div>
        <?php if (!empty($eduprog->shedule_text)) { ?>
            <div class="event-page-short time"><?= $eduprog->shedule_text; ?></div>
        <?php } ?>
        <div class="lk-dpo-socs-wrapper">
            <div class="lk-dpo-socs-title">Связаться с организатором</div>
            <div class="lk-dpo-socs">
                <a href="mailto:<?= $eduprog->contact_email; ?>" target="_blank" data-pjax="0"
                   class="lk-dpo-soc mail"></a>
                <?php if (!empty($eduprog->contact_phone)) { ?>
                    <a href="tel:<?= MainHelper::clearPhone($eduprog->contact_phone); ?>" data-pjax="0" target="_blank"
                       class="lk-dpo-soc phone"></a>
                <?php } ?>
                <?php if (!empty($eduprog->contact_wa)) { ?>
                    <a href="https://wa.me/<?= $eduprog->contact_wa; ?>" target="_blank" data-pjax="0"
                       class="lk-dpo-soc wa"></a>
                <?php } ?>
                <?php if (!empty($eduprog->contact_telegram)) { ?>
                    <a href="<?= 'https://t.me/' . str_replace('https://t.me/', '', str_replace('@', '', $eduprog->contact_telegram)); ?>"
                       target="_blank" data-pjax="0" class="lk-dpo-soc tg"></a>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="section-event-page-preview-wrapper">
        <!-- десктоп обложка -->
        <div class="section-event-page-preview-img desktop-visible">
            <img src="<?= $eduprog->getThumb('image', 'main'); ?>" alt="<?= htmlspecialchars($eduprog->name); ?>"
                 loading="lazy">
        </div>
        <div class="section-event-page-preview-info">
            <div class="section-event-page-type"><?= $eduprog->category ? $eduprog->category->name : ''; ?>
                (<?= $eduprog->hours; ?> ч)
            </div>
            <h1 class="section-event-page-title"><?= $eduprog->name; ?></h1>
            <div class="section-event-page-type">Номер слушателя:</div>
            <div class="section-event-page-number">№ <?= $member->memberNum; ?></div>
            <div class="section-event-page-tags">
                <a class="section-event-page-tag"><?= $eduprog->formatName; ?></a>
            </div>
        </div>
    </div>
</div>
