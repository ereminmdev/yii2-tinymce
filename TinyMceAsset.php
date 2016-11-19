<?php

namespace ereminmdev\yii2\tinymce;

use yii\web\AssetBundle;

class TinyMceAsset extends AssetBundle
{
    public $sourcePath = '@vendor/ereminmdev/yii2-tinymce/assets';

    public $depends = [
        'ereminmdev\yii2\tinymce\TinyMceBaseAsset'
    ];
}
