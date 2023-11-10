<?php

namespace ereminmdev\yii2\tinymce;

use yii\web\AssetBundle;

class TinyMceBaseAsset extends AssetBundle
{
    public $sourcePath = '@vendor/tinymce/tinymce';

    public $js = [
        YII_DEBUG ? 'tinymce.js' : 'tinymce.min.js',
    ];

    public $publishOptions = [
        //'except' => ['/composer.json', '/bower.json', '/package.json'],
        'only' => ['*.js', '*.css'],
    ];
}
