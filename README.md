# yii2-tinymce

TinyMce widget for Yii framework.

This widget depends on TinyMCE WYSIWYG editor: https://www.tinymce.com/

## Install

``composer require --prefer-dist ereminmdev/yii2-tinymce``

## Use

```
<?= TinyMce::widget([
    'language' => Yii::$app->language,
    'clientOptions' => [
        // https://www.tinymce.com/docs/configure
        ...
    ],
    'mode' => 'full', // none, compact, basic, full, subscribe
]) ?>
```
