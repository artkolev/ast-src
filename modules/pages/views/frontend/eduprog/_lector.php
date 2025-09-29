<?php
/*
    отображение эксперта в форме добавления преподавателя к программе ДПО
*/
if (!empty($item)) {
    ?>
    <div class="search-speaker-result-element">
        <div class="search-speaker-result-element-img">
            <img src="<?= $item->profile->getThumb('image', 'main'); ?>" alt="<?= $item->profile->halfname; ?>">
        </div>
        <div class="search-speaker-result-element-info">
            <div class="search-speaker-result-element-name"><?= $item->profile->halfname; ?></div>
            <div class="search-speaker-result-element-text"><?= $item->profile->about_myself; ?></div>
            <a href="<?= $item->getUrlPath(); ?>" class="search-speaker-result-element-link" target="_blank">Открыть на
                сайте</a>
        </div>
    </div>
<?php } ?>