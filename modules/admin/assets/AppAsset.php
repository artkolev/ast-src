<?php

namespace admin\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $basePath = '@admin/web';
    public $sourcePath = '@admin/web';
    public $css = [
        'css/style.css',
        'select2/css/select2.min.css',
        'colorpicker/css/colorpicker.css',
        'fancybox/jquery.fancybox.min.css',
        'css/deep.css',
        'css/docs.css',
    ];
    public $js = [
        'js/dist/coreui.bundle.min.js',
        'js/jquery.maskedinput.min.js',
        'js/clipboard.min.js',
        'js/dist/coreui-utils.js',
        'select2/js/select2.full.js',
        'colorpicker/js/colorpicker.js',
        'fancybox/jquery.fancybox.min.js',
        'js/deep.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\jui\JuiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
