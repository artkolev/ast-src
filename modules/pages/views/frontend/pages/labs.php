<?php

use app\modules\users\models\UserAR;
use yii\helpers\Html;

?>
<main class="sec content_sec">
    <div class="container wide">
        <h1><?= $model->getNameForView(); ?></h1>
        <div class="subheader"><?= $model->content; ?></div>
        <?php if ($labs) { ?>
            <div class="directions_box">
                <?php foreach ($labs as $item) { ?>
                    <div class="direction_item">
                        <div class="relative">
                            <div>
                                <h3><?= Html::a($item->name, $item->getUrlPath()); ?></h3>
                                <div class="direction_item-desc">
                                    <div><?= $item->anons; ?></div>
                                </div>
                            </div>
                            <?php
                            $user_data = $item->users;
                            if (!empty($user_data)) { ?>
                                <div class="direction_item-persons">
                                    <?php
                                    foreach ($user_data as $user) {
                                        if ($user['visible'] && (int)$user['user_id'] > 0) {
                                            $userar = UserAR::getUserById($user['user_id']);
                                            if (!empty($userar && !empty($userar->profile))) {
                                                echo Html::a(Html::img($userar->profile->getThumb('image', 'direct'), ['alt' => $userar->profile->fullname]), $userar->getUrlPath(), ['class' => 'person_item_small']);
                                            }
                                        } else {
                                            echo Html::a(Html::img($item->getThumb('userimage', 'main', false, $user['image'][0]), ['alt' => $user['fio']]), '#', ['class' => 'person_item_small']);
                                        }
                                    }
                                    if (count($user_data) > 4) {
                                        echo Html::a('+' . count($user_data) - 4, $item->getUrlPath(), ['class' => 'person_item_small more']);
                                    }
                                    ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

            </div>
        <?php } ?>
    </div>
</main>
