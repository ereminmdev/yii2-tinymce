<?php

namespace ereminmdev\yii2\tinymce;

use yii\web\AssetBundle;

class TinyMceBaseAsset extends AssetBundle
{
    public $sourcePath = '@vendor/npm/tinymce';

    public function init()
    {
        parent::init();

        $this->js[] = YII_DEBUG ? 'tinymce.js' : 'tinymce.min.js';
    }
}
