<?php

namespace ereminmdev\yii2\tinymce;

use Yii;
use yii\base\Action;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * Class ImageUploadAction
 * @package ereminmdev\yii2\tinymce
 */
class ImageUploadAction extends Action
{
    /**
     * @var string
     */
    public $uploadFolder = 'uploads';
    /**
     * @var string
     */
    public $baseUrl = '@web';
    /**
     * @var string
     */
    public $basePath = '@webroot';
    /**
     * @var bool
     */
    public $useSubFolder = true;
    /**
     * @var bool convert uploaded images to WebP format
     */
    public $convertToWebP = true;
    /**
     * @var int quality ranges from 0 (worst quality, smaller file) to 100 (best quality, biggest file)
     */
    public $convertQuality = 80;

    /**
     * {@inheritdoc}
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     */
    public function run()
    {
        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException('Invalid request method.');
        }

        Yii::$app->response->format = Response::FORMAT_RAW;

        foreach ($_FILES as $uploadFile) {
            $tmp_name = $uploadFile['tmp_name'];

            if (is_uploaded_file($tmp_name)) {
                $filename = $uploadFile['name'];
                $filename = ($pos = mb_strrpos($filename, '/')) !== false ? mb_substr($filename, $pos + 1) : $filename;
                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
                    throw new BadRequestHttpException('Invalid extension.');
                }

                $filename = sha1_file($tmp_name) . '.' . $extension;
                $uploadFolder = $this->uploadFolder;

                if ($this->useSubFolder) {
                    $uploadFolder .= '/' . mb_substr($filename, 0, 2);
                }

                $baseUrl = Yii::getAlias($this->baseUrl) . '/' . $uploadFolder;
                $basePath = Yii::getAlias($this->basePath) . DIRECTORY_SEPARATOR . $uploadFolder;

                // Accept upload if there was no origin, or if it is an accepted origin
                $filepath = $basePath . DIRECTORY_SEPARATOR . $filename;
                @mkdir(dirname($filepath), 0777, true);

                if ($this->convertToWebP && in_array($extension, ['jpg', 'jpeg', 'png'])) {
                    $im = in_array($extension, ['jpg', 'jpeg']) ? @imagecreatefromjpeg($tmp_name) : @imagecreatefrompng($tmp_name);

                    if ($im) {
                        $filename = preg_replace('/' . $extension . '$/', 'webp', $filename);
                        $filepath = preg_replace('/' . $extension . '$/', 'webp', $filepath);

                        imagepalettetotruecolor($im);
                        imagealphablending($im, true);
                        imagesavealpha($im, true);
                        imagewebp($im, $filepath, $this->convertQuality);
                        imagedestroy($im);
                    }
                } else {
                    move_uploaded_file($tmp_name, $filepath);
                }

                // Respond to the successful upload with JSON.
                // Use a location key to specify the path to the saved image resource.
                // { location : '/your/uploaded/image/file'}
                return json_encode(['location' => $baseUrl . '/' . $filename]);
            }
        }

        // Notify editor that the upload failed
        throw new ServerErrorHttpException();
    }
}
