<?php

use admin\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

AppAsset::register($this);
?>
<?php $this->beginPage(); ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language; ?>">
    <head>
        <meta charset="<?= Yii::$app->charset; ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
        <?php $this->registerCsrfMetaTags(); ?>
        <title><?= Html::encode($this->title); ?></title>
        <link rel="icon" href="<?= Yii::$app->assetManager->getPublishedUrl('@admin/web'); ?>/favicon.ico">
        <?php $this->head(); ?>
    </head>
    <?php if (Yii::$app->user->can('admin')) { ?>
        <body class="c-app">
        <?php $this->beginBody(); ?>
        <div class="c-sidebar c-sidebar-dark c-sidebar-fixed c-sidebar-lg-show" id="sidebar">
            <div class="c-sidebar-brand d-lg-down-none">
                <svg class="c-sidebar-brand-full" width="118" height="46" alt="Deep Logo">
                    <use xlink:href="<?= Yii::$app->assetManager->getPublishedUrl('@admin/web'); ?>/svg/coreui.svg#full_deep"></use>
                </svg>
                <svg class="c-sidebar-brand-minimized" width="46" height="46" alt="Deep Logo">
                    <use xlink:href="<?= Yii::$app->assetManager->getPublishedUrl('@admin/web'); ?>/svg/coreui.svg#signed_deep"></use>
                </svg>
            </div>
            <?= \app\modules\adminmenu\widgets\adminmenu\AdminmenuWidget::widget(); ?>
            <button class="c-sidebar-minimizer c-class-toggler" type="button" data-target="_parent"
                    data-class="c-sidebar-minimized"></button>
        </div>

        <div class="c-wrapper c-fixed-components">
            <header class="c-header c-header-light c-header-fixed c-header-with-subheader">
                <button class="c-header-toggler c-class-toggler d-lg-none mfe-auto" type="button" data-target="#sidebar"
                        data-class="c-sidebar-show">
                    <svg class="c-icon c-icon-lg">
                        <use xlink:href="<?= Yii::$app->assetManager->getPublishedUrl('@admin/web'); ?>/svg/free.svg#cil-menu"></use>
                    </svg>
                </button>
                <a class="c-header-brand d-lg-none" href="#">
                    <svg width="118" height="46" alt="CoreUI Logo">
                        <use xlink:href="<?= Yii::$app->assetManager->getPublishedUrl('@admin/web'); ?>/svg/coreui.svg#full"></use>
                    </svg>
                </a>
                <button class="c-header-toggler c-class-toggler mfs-3 d-md-down-none" type="button"
                        data-target="#sidebar" data-class="c-sidebar-lg-show" responsive="true">
                    <svg class="c-icon c-icon-lg">
                        <use xlink:href="<?= Yii::$app->assetManager->getPublishedUrl('@admin/web'); ?>/svg/free.svg#cil-menu"></use>
                    </svg>
                </button>
                <ul class="c-header-nav d-md-down-none">
                    <li class="c-header-nav-item px-3"><a class="c-header-nav-link" href="/admin/">Панель управления</a>
                    </li>
                </ul>
                <ul class="c-header-nav ml-auto mr-4">
                    <li class="c-header-nav-item d-md-down-none mx-2">
                        <a class="c-header-nav-link"
                           href="<?= Url::to(['/admin/users/default/update/', 'id' => Yii::$app->user->id]); ?>"><?= Yii::$app->user->getIdentity()->getUserAR()->profile->name; ?>
                            (<?= Yii::$app->user->getIdentity()->getUserAR()->username; ?>)</a>
                    </li>
                    <li class="c-header-nav-item dropdown">
                        <a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true"
                           aria-expanded="false">
                            <div class="c-avatar">
                                <img class="c-avatar-img"
                                     src="/web/<?= Yii::$app->user->getIdentity()->getUserAR()->profile->image->src; ?>"
                                     alt="<?= Yii::$app->user->getIdentity()->getUserAR()->email; ?>">
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right pt-0">
                            <div class="dropdown-header bg-light py-2">
                                <strong>Аккаунт</strong>
                            </div>
                            <a class="dropdown-item"
                               href="<?= Url::to(['/admin/users/default/update/', 'id' => Yii::$app->user->id]); ?>">
                                <svg class="c-icon mr-2">
                                    <use xlink:href="<?= Yii::$app->assetManager->getPublishedUrl('@admin/web'); ?>/svg/free.svg#cil-user"></use>
                                </svg>
                                Профиль
                            </a>
                            <div class="dropdown-divider"></div>
                            <?= Html::beginForm(['/admin/login/logout'], 'post')
                            . Html::submitButton(
                                    '<svg class="c-icon mr-2">
								<use xlink:href="' . Yii::$app->assetManager->getPublishedUrl('@admin/web') . '/svg/free.svg#cil-account-logout"></use>
							</svg>
							Выход',
                                    ['class' => 'dropdown-item']
                            ) . Html::endForm(); ?>
                        </div>
                    </li>
                </ul>
                <?php if (!empty($this->params['breadcrumbs'])) { ?>
                    <div class="c-subheader px-3">
                        <?= Breadcrumbs::widget([
                                'tag' => 'ol',
                                'options' => ['class' => 'breadcrumb border-0 m-0'],
                                'itemTemplate' => '<li class="breadcrumb-item">{link}</li>',
                                'activeItemTemplate' => '<li class="breadcrumb-item active">{link}</li>',
                                'homeLink' => ['label' => 'Панель управления', 'url' => '/admin'],
                                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                        ]); ?>
                    </div>
                <?php } ?>
            </header>
            <div class="c-body">
                <main class="c-main">
                    <div class="container-fluid">
                        <div class="fade-in">
                            <?= $content; ?>
                        </div>
                        <!-- модальное окно -- заготовка -->
                        <div class="modal fade" id="info_win" tabindex="-1" role="dialog" aria-labelledby="modal_win"
                             aria-hidden="true">
                            <div class="modal-dialog modal-primary" role="document">
                                <!-- ---  modal-lg  modal-sm  modal-primary  modal-success  modal-warning  modal-danger  modal-info -->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title"></h4>
                                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <p></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button id="info_win_close" class="btn btn-success" type="button"
                                                data-dismiss="modal">Ок
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div style="display:none;">
                            <button id="show_modal_info" type="button" data-toggle="modal"
                                    data-target="#info_win"></button>
                        </div>
                    </div>
                </main>
                <footer class="c-footer">
                    <div><a href="#">DigitalMuse</a> © <?= date('Y'); ?></div>
                    <div class="ml-auto">Powered by&nbsp;<a href="#">DigitalMuse</a></div>
                </footer>
            </div>
        </div>
        <?php $this->endBody(); ?>
        </body>
    <?php } else { ?>
        <body class="c-app flex-row align-items-center">
        <?php $this->beginBody(); ?>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card-group">
                        <?= $content; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php $this->endBody(); ?>
        </body>
    <?php } ?>
    </html>
<?php $this->endPage(); ?>