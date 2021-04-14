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
     * {@inheritdoc}
     * @throws BadRequestHttpException
     * @throws ServerErrorHttpException
     */
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;

        foreach ($_FILES as $uploadFile) {
            $tmp_name = $uploadFile['tmp_name'];

            if (is_uploaded_file($tmp_name)) {
                $filename = $uploadFile['name'];
                $filename = ($pos = mb_strrpos($filename, '/')) !== false ? mb_substr($filename, $pos + 1) : $filename;
                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                // Verify extension
                if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    throw new BadRequestHttpException('Invalid extension.');
                }

                $filename = uniqid() . time() . '.' . $extension;
                $uploadFolder = $this->uploadFolder;

                if ($this->useSubFolder) {
                    $uploadFolder .= '/' . mb_substr($filename, 0, 2);
                }

                $baseUrl = Yii::getAlias($this->baseUrl) . '/' . $uploadFolder;
                $basePath = Yii::getAlias($this->basePath) . DIRECTORY_SEPARATOR . $uploadFolder;

                // Accept upload if there was no origin, or if it is an accepted origin
                $filepath = $basePath . DIRECTORY_SEPARATOR . $filename;
                @mkdir(dirname($filepath), 0777, true);
                move_uploaded_file($tmp_name, $filepath);

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
