<?php

use app\modules\pages\models\MessagesChat;
use yii\helpers\Url;

?>
    <main class="sec content_sec gray_bg">
        <div class="container wide lk-container">
            <div class="lk_maincol">
                <div class="lk_block">
                    <header class="lk_block_header">
                        <h1 class="lk_block_title-big"><?= $model->getNameForView(); ?></h1>
                        <?= $model->content; ?>
                    </header>
                </div>
                <form id="search_chat" class="lk_block" method="get">
                    <div class="directions_search_box">
                        <div class="search_flex">
                            <select class="pretty_select_search_user" id="find_chat" name="chat_q">
                                <?php if ($searched_user) { ?>
                                    <option value="<?= $searched_user->id; ?>"
                                            selected><?= $searched_user->profile->fullname; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </form>
                <?php if (!empty($chats)) {
                    $chat_page = MessagesChat::find()->where(['model' => MessagesChat::class, 'visible' => 1])->one();
                    foreach ($chats as $chat) {
                        $chat_url = Url::to([$chat_page->getUrlPath(), 'id' => $chat->id]);
                        $names = [];
                        foreach ($chat->member as $member) {
                            if ($member->id == Yii::$app->user->id) {
                                continue;
                            }
                            $names[] = $member->profile->fullname;
                        }
                        $start_message = $chat->messages[0]->message;
                        if (mb_strlen($start_message, 'UTF8') > 300) {
                            $start_message = mb_substr($start_message, 0, 300, 'UTF8');
                            $last_space = mb_strrpos($start_message, ' ', 0, 'UTF8');
                            if ($last_space) {
                                $start_message = mb_substr($start_message, 0, $last_space, 'UTF8') . '...';
                            }
                        }
                        ?>
                        <div class="lk_message_item <?= $chat->hasUnreadNotifications(Yii::$app->user->identity->userAR->id) ? 'has_notifications' : ''; ?>">
                            <a href="<?= $chat_url; ?>" class="lk_order_more lightgray"><i
                                        class="fa fa-angle-right"></i></a>
                            <h2 class="lk_block_title"><?= implode(', ', $names); ?></h2>
                            <div><?= $start_message; ?></div>
                            <div class="date"><?= Yii::$app->formatter->asDatetime($chat->messages[0]->created_at, 'd.MM.y'); ?></div>
                        </div>
                    <?php } ?>
                <?php } ?>

            </div>
            <?= \app\helpers\MainHelper::renderMenuWidget(); ?>
        </div>
    </main>
<?php
$url = Url::toRoute(['/pages/message/getusers', 'user' => Yii::$app->user->id]);
$js = <<<JS
    $('.pretty_select_search_user').select2({
        placeholder: 'Поиск по чатам (введите имя)',
        minimumInputLength: 4,
        language: {
            noResults: function () {
              return 'Ничего не найдено';
            },
            searching: function () {
              return 'Поиск…';
            },
            errorLoading: function () {
              return 'Результаты не могут быть загружены';
            },
            inputTooShort: function(args) { 
                return "Введите еще минимум "+ (parseInt(args.minimum) - parseInt(args.input.length))+" символ(а)";
            }
        },
        ajax: {
            delay: 300,
            url: '{$url}',
            data: function (params) {
                var queryParameters = {
                  q: params.term
                }

                return queryParameters;
            },
            processResults: function (data) {
                return {
                  results: data.items
                };
              },
            dataType: 'json'
        }
    });

    $('#find_chat').change(function(){
        $('#search_chat').submit();
    });
JS;
$this->registerJs($js);
