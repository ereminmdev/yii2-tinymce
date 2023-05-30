<?php

namespace ereminmdev\yii2\tinymce;

use Yii;
use yii\base\InvalidConfigException;
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
     * @var array the options for the TinyMCE JS plugin.
     * Please refer to the TinyMCE JS plugin web page for possible options: https://www.tinymce.com/docs/configure/
     */
    public $clientOptions = [];
    /**
     * @var string one of the none, compact, basic, full, subscribe
     */
    public $mode = 'full';
    /**
     * @var string the language to use
     */
    public $language;
    /**
     * @var bool
     */
    public $useElFinder = true;
    /**
     * @var string
     */
    public $baseUrl;
    /**
     * @var string
     */
    public $assetBaseUrl;

    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        $this->options = ArrayHelper::merge($this->options, ['rows' => 6]);

        $this->language = $this->language ?? mb_substr(Yii::$app->language, 0, 2);
        $this->language = $this->language != 'en_US' ? $this->language : null;

        $this->baseUrl = $this->baseUrl ?? (Yii::$app->has('urlManagerFrontend') ? Yii::$app->urlManagerFrontend->hostInfo : Yii::$app->urlManager->hostInfo);

        $assetBundle = TinyMceAsset::register($this->getView());
        $this->assetBaseUrl = $this->assetBaseUrl ?? $this->baseUrl . $assetBundle->baseUrl;

        $templatePath = $this->assetBaseUrl . '/templates';

        $baseOptions = [
            'skin' => 'tinymce-5',
            'content_css' => $this->baseUrl . '/css/site-editor.css',
            'document_base_url' => $this->baseUrl . '/',
            'convert_urls' => false,
            'images_upload_url' => Url::toRoute(['/files/tinymce-upload'], true),
            'images_upload_credentials' => true,
            'automatic_uploads' => true,
            'autosave_ask_before_unload' => true,
            'autosave_interval' => '30s',
            'autosave_prefix' => '{path}{query}-{id}-',
            'autosave_restore_when_empty' => false,
            'autosave_retention' => '2m',
            'browser_spellcheck' => true,
            'image_advtab' => true,
            'height' => 450,
            'branding' => false,
            'promotion' => false,
            'setup' => new JsExpression('(editor) => editor.on("change", () => tinymce.activeEditor.uploadImages().then(() => tinymce.triggerSave()))'),
            'plugins' => 'preview importcss searchreplace autolink autosave save code visualblocks visualchars fullscreen image link media template table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount charmap emoticons',
            'toolbar' => 'removeformat | blocks | fontfamily | fontsize | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | emoticons | fullscreen',
        ];

        if ($this->useElFinder) {
            $baseOptions['file_picker_callback'] = new JsExpression('elFinderBrowser');
        }

        if ($this->mode == 'none') {
            $clientOptions = [];

        } elseif ($this->mode == 'compact') {
            $clientOptions = ArrayHelper::merge($baseOptions, [
                'height' => 200,
                'toolbar' => false,
                'menubar' => false,
                'statusbar' => false,
                'contextmenu' => false,
                'resize' => true,
            ]);
            $this->options['class'] = '';

        } elseif ($this->mode == 'basic') {
            $clientOptions = ArrayHelper::merge($baseOptions, [
                'toolbar' => 'fontsize bold italic forecolor backcolor bullist numlist emoji undo redo fullscreen',
                'statusbar' => false,
                'plugins' => ['autoresize'],
                'autoresize_overflow_padding' => 10,
                'autoresize_bottom_margin' => 0,
            ]);

        } elseif ($this->mode == 'subscribe') {
            $clientOptions = ArrayHelper::merge($baseOptions, [
                'schema' => 'html4',
                'doctype' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">',
                'content_css' => '',
                'convert_urls' => true,
                'relative_urls' => false,
                'remove_script_host' => false,
            ]);

        } else {
            $clientOptions = ArrayHelper::merge($baseOptions, [
                'templates' => [
                    ['title' => 'Заголовок первого уровня', 'description' => '', 'url' => $templatePath . '/head1.htm'],

                    ['title' => 'Таблица', 'description' => '', 'url' => $templatePath . '/table1.htm'],
                    ['title' => 'Таблица c чередование строк', 'description' => '', 'url' => $templatePath . '/table2.htm'],
                    ['title' => 'Таблица c рамками', 'description' => '', 'url' => $templatePath . '/table3.htm'],
                    ['title' => 'Таблица компактная', 'description' => '', 'url' => $templatePath . '/table4.htm'],

                    ['title' => 'Фото или видео по центру', 'description' => '', 'url' => $templatePath . '/tpl1.htm'],
                    ['title' => '2 колонки с заголовком первого уровня', 'description' => '', 'url' => $templatePath . '/tpl2.htm'],
                    ['title' => '3 колонки с заголовком первого уровня', 'description' => '', 'url' => $templatePath . '/tpl3.htm'],
                    ['title' => '4 колонки с заголовком первого уровня', 'description' => '', 'url' => $templatePath . '/tpl4.htm'],
                    ['title' => 'Фото с текстом справа', 'description' => '', 'url' => $templatePath . '/tpl5.htm'],
                    ['title' => 'Фото с текстом слева', 'description' => '', 'url' => $templatePath . '/tpl6.htm'],
                    ['title' => '2 колонки с горизонтальными блоками', 'description' => '', 'url' => $templatePath . '/tpl7.htm'],

                    ['title' => 'Текст 2 колонки', 'description' => '', 'url' => $templatePath . '/text2col.htm'],
                    ['title' => 'Текст 3 колонки', 'description' => '', 'url' => $templatePath . '/text3col.htm'],
                    ['title' => 'Текст 4 колонки', 'description' => '', 'url' => $templatePath . '/text4col.htm'],
                    ['title' => 'Подкат', 'description' => '', 'url' => $templatePath . '/tackle.htm'],

                    ['title' => 'Кнопка 1', 'description' => '', 'content' => '<a class="btn btn-default" href="#" role="button">Подробнее &raquo;</a>'],
                    ['title' => 'Кнопка 2', 'description' => '', 'content' => '<a class="btn btn-primary" href="#" role="button">Подробнее &raquo;</a>'],
                    ['title' => 'Кнопка 3', 'description' => '', 'content' => '<a class="btn btn-success" href="#" role="button">Подробнее &raquo;</a>'],
                    ['title' => 'Кнопка 4', 'description' => '', 'content' => '<a class="btn btn-info" href="#" role="button">Подробнее &raquo;</a>'],
                    ['title' => 'Кнопка 5', 'description' => '', 'content' => '<a class="btn btn-warning" href="#" role="button">Подробнее &raquo;</a>'],
                    ['title' => 'Кнопка 6', 'description' => '', 'content' => '<a class="btn btn-danger" href="#" role="button">Подробнее &raquo;</a>'],
                    ['title' => 'Кнопка 7', 'description' => '', 'content' => '<a class="btn btn-link" href="#" role="button">Подробнее &raquo;</a>'],
                ],
            ]);
        }

        $this->clientOptions = ArrayHelper::merge($clientOptions, $this->clientOptions);
    }

    /**
     * {@inheritdoc}
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

        $id = $this->options['id'];
        $this->clientOptions['selector'] = '#' . $id;

        if ($this->language !== null) {
            $this->clientOptions['language'] = $this->language;
            $this->clientOptions['language_url'] = $this->assetBaseUrl . '/langs/' . $this->language . '.js';
        }

        $view->registerJs('tinymce.init(' . Json::encode($this->clientOptions) . ');');

        if ($this->useElFinder) {
            $view->registerJs('
function elFinderBrowser(callback, value, meta) {
    tinymce.activeEditor.windowManager.openUrl({
        url: "' . Url::toRoute(['/files/popup']) . '",
        title: "' . Yii::t('app', 'Files') . '",
        width: 900,
        height: 450,
        resizable: "yes",
        inline : "yes",
        popup_css : false,
        close_previous : "no",
        onMessage: function (dialogApi, details) {
            const file = details.data;

            // URL normalization
            let url = file.url;
            const reg = /\\/[^/]+?\\/\\.\\.\\//;
            while(url.match(reg)) {
                url = url.replace(reg, "/");
            }

            // Make file info
            const info = file.name;

            // Provide file and text for the link dialog
            if (meta.filetype == "file") {
                callback(url, {text: info, title: info});
            }

            // Provide image and alt text for the image dialog
            if (meta.filetype == "image") {
                callback(url, {alt: info});
            }

            // Provide alternative source and posted for the media dialog
            if (meta.filetype == "media") {
                callback(url);
            }

            dialogApi.close();
        }
    });
    return false;
}
');
        }
    }
}
