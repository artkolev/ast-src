<?php

use app\modules\direction\models\Direction;
use app\modules\feedacadem\models\Feedacadem;
use app\modules\subscribe\models\Subscribe;
use app\modules\writeus\models\Writeus;
use app\modules\writeus_pr\models\WriteusPr;

?>
<div class="card">
    <div class="card-header">Панель управления</div>
    <div class="card-body">
        <div class="row">
            <div class="col-sm-6 col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-header"><h3><a href="/admin/direction/">Кафедры</a></h3></div>
                    <div class="card-body"><a href="/admin/direction/"><?= Direction::find()->count(); ?> кафедр</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-header"><h3>Участники АСТ</h3></div>
                    <div class="card-body">
                        <a href="/admin/users/expert/">Эксперты</a><br>
                        <a href="/admin/users/exporg/">Экспертные организации</a><br>
                        <a href="/admin/users/fizusr/">Физические лица</a><br>
                        <a href="/admin/users/urusr/">Юридические лица</a><br>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-header"><h3>Каталоги деятельности</h3></div>
                    <div class="card-body">
                        <a href="/admin/events/">Мероприятия</a><br>
                        <a href="/admin/lenta/">Лента</a><br>
                        <a href="/admin/lenta/blog/">Блоги</a><br>
                        <a href="/admin/lenta/news/">Новости</a><br>
                        <a href="/admin/lenta/project/">Портфолио</a><br>
                        <a href="/admin/lenta/material/">База знаний</a><br>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-header"><h3><a href="/admin/subscribe/">Подписка на рассылку</a></h3></div>
                    <div class="card-body"><a href="/admin/subscribe/">Количество
                            заявок: <?= Subscribe::find()->count(); ?></a></div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-header"><h3><a href="/admin/feedacadem/">Связаться с экспертом</a></h3></div>
                    <div class="card-body"><a href="/admin/feedacadem/">Количество
                            заявок: <?= Feedacadem::find()->count(); ?></a></div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-header"><h3><a href="/admin/writeus/">Напишите нам</a></h3></div>
                    <div class="card-body"><a href="/admin/writeus/">Количество
                            заявок: <?= Writeus::find()->count(); ?></a></div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-header"><h3><a href="/admin/writeus/">Связаться с PR-службой</a></h3></div>
                    <div class="card-body"><a href="/admin/writeus/">Количество
                            заявок: <?= WriteusPr::find()->count(); ?></a></div>
                </div>
            </div>
        </div>
    </div>
</div>