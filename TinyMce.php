<?php

namespace ereminmdev\yii2\tinymce;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\InputWidget;

/**
 * TinyMce widget for Yii framework.
 */
class TinyMce extends InputWidget
{
    /**
     * @var string the language to use
     */
    public $language;
    /**
     * @var array the options for the TinyMCE JS plugin.
     * Please refer to the TinyMCE JS plugin web page for possible options: https://www.tinymce.com/docs/configure/
     */
    public $clientOptions = [];
    /**
     * @var bool use compact editor mode
     */
    public $compactMode = false;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->options = ArrayHelper::merge(
            $this->options,
            ['rows' => 6]
        );

        $this->language = ($this->language !== null) ? $this->language : ((!in_array(Yii::$app->language, ['en', 'en-US'])) ? Yii::$app->language : null);

        $basePath = Yii::$app->get('urlManagerFrontend')->getBaseUrl();

        $assetBundle = TinyMceAsset::register($this->getView());
        $templatesPath = $assetBundle->baseUrl . '/templates';

        $defaultsBase = [
            'content_css' => $basePath . '/css/site-editor.css',
            'document_base_url' => Url::to('/', true),
            'valid_elements' => '*[*]',
            'convert_urls' => false,
            'browser_spellcheck' => true,
        ];

        $defaults = !$this->compactMode ? ArrayHelper::merge($defaultsBase, [
            'height' => 350,
            'fontsize_formats' => '8px 9px 10px 11px 12px 14px 18px 24px 30px 36px 48px 60px 72px 96px',
            'setup' => new JsExpression('function(editor){ editor.on("change", function(){ tinymce.triggerSave(); }); }'),
            'plugins' => [
                'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'template paste textcolor colorpicker textpattern'
            ],
            'toolbar' => 'fullscreen | insertfile undo redo | styleselect | fontselect | fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | forecolor backcolor',
            'image_advtab' => true,
            'file_picker_callback' => new JsExpression('elFinderBrowser'),
            'templates' => [
                ['title' => 'Заголовок первого уровня', 'url' => $templatesPath . '/head1.htm'],

                ['title' => 'Таблица', 'url' => $templatesPath . '/table1.htm'],
                ['title' => 'Таблица c чередование строк', 'url' => $templatesPath . '/table2.htm'],
                ['title' => 'Таблица c рамками', 'url' => $templatesPath . '/table3.htm'],
                ['title' => 'Таблица компактная', 'url' => $templatesPath . '/table4.htm'],

                ['title' => 'Фото или видео по центру', 'url' => $templatesPath . '/tpl1.htm'],
                ['title' => '2 колонки с заголовком первого уровня', 'url' => $templatesPath . '/tpl2.htm'],
                ['title' => '3 колонки с заголовком первого уровня', 'url' => $templatesPath . '/tpl3.htm'],
                ['title' => '4 колонки с заголовком первого уровня', 'url' => $templatesPath . '/tpl4.htm'],
                ['title' => 'Фото с текстом справа', 'url' => $templatesPath . '/tpl5.htm'],
                ['title' => 'Фото с текстом слева', 'url' => $templatesPath . '/tpl6.htm'],
                ['title' => '2 колонки с горизонтальными блоками', 'url' => $templatesPath . '/tpl7.htm'],

                ['title' => 'Текст 2 колонки', 'url' => $templatesPath . '/text2col.htm'],
                ['title' => 'Текст 3 колонки', 'url' => $templatesPath . '/text3col.htm'],
                ['title' => 'Текст 4 колонки', 'url' => $templatesPath . '/text4col.htm'],
                ['title' => 'Подкат', 'url' => $templatesPath . '/tackle.htm'],

                ['title' => 'Кнопка 1', 'content' => '<a class="btn btn-default" href="#" role="button">Подробнее &raquo;</a>'],
                ['title' => 'Кнопка 2', 'content' => '<a class="btn btn-primary" href="#" role="button">Подробнее &raquo;</a>'],
                ['title' => 'Кнопка 3', 'content' => '<a class="btn btn-success" href="#" role="button">Подробнее &raquo;</a>'],
                ['title' => 'Кнопка 4', 'content' => '<a class="btn btn-info" href="#" role="button">Подробнее &raquo;</a>'],
                ['title' => 'Кнопка 5', 'content' => '<a class="btn btn-warning" href="#" role="button">Подробнее &raquo;</a>'],
                ['title' => 'Кнопка 6', 'content' => '<a class="btn btn-danger" href="#" role="button">Подробнее &raquo;</a>'],
                ['title' => 'Кнопка 7', 'content' => '<a class="btn btn-link" href="#" role="button">Подробнее &raquo;</a>'],
            ],
        ]) : ArrayHelper::merge($defaultsBase, [
            'height' => 100,
            'toolbar' => false,
            'menubar' => false,
            'statusbar' => false,
            'contextmenu' => false,
            'resize' => true,
            'plugins' => ['paste'],
            'paste_as_text' => true,
        ]);

        if ($this->compactMode) {
            $this->options['class'] = '';
        }

        $this->clientOptions = ArrayHelper::merge($defaults, $this->clientOptions);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->hasModel()) {
            echo Html::activeTextarea($this->model, $this->attribute, $this->options);
        } else {
            echo Html::textarea($this->name, $this->value, $this->options);
        }
        $this->registerClientScripts();
    }

    /**
     * Registers tinyMCE js plugin
     */
    protected function registerClientScripts()
    {
        $view = $this->getView();
        $assetBundle = TinyMceAsset::register($view);

        $id = $this->options['id'];

        $this->clientOptions['selector'] = '#' . $id;
        if ($this->language !== null) {
            $this->clientOptions['language_url'] = $assetBundle->baseUrl . '/langs/' . $this->language . '.js';
        }

        $view->registerJs('tinymce.init(' . Json::encode($this->clientOptions) . ');');


        // Register ElFinder popup window
        $view->registerJs("
function elFinderBrowser(callback, value, meta) {
    tinymce.activeEditor.windowManager.open({
        file: '" . Url::toRoute(['/files/popup'], true) . "',
        title: '" . Yii::t('app', 'Files') . "',
        width: 900,
        height: 450,
        resizable: 'yes',
        inline : 'yes',
        popup_css : false,
        close_previous : 'no'
    }, {
        oninsert: function (file) {
            var url, reg, info;

            // URL normalization
            url = file.url;
            reg = /\\/[^/]+?\\/\\.\\.\\//;
            while(url.match(reg)) {
                url = url.replace(reg, '/');
            }

            // Make file info
            info = file.name;

            // Provide file and text for the link dialog
            if (meta.filetype == 'file') {
                callback(url, {text: info, title: info});
            }

            // Provide image and alt text for the image dialog
            if (meta.filetype == 'image') {
                callback(url, {alt: info});
            }

            // Provide alternative source and posted for the media dialog
            if (meta.filetype == 'media') {
                callback(url);
            }
        }
    });
    return false;
}
        ");
    }
}
