# yii2-tinymce

TinyMce widget for Yii framework.

This widget depend on TinyMCE WYSIWYG editor: https://www.tinymce.com/

## Install

``composer require ereminmdev/yii2-tinymce``

## Use

```
<?= TinyMce::widget([
    'language' => Yii::$app->language,
    'clientOptions' => [
        ...
    ],
    'compactMode' => false,
]) ?>
```

## Client options

https://www.tinymce.com/docs/configure/
