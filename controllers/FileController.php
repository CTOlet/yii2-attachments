<?php

namespace nemmo\attachments\controllers;

use nemmo\attachments\models\File;
use nemmo\attachments\models\UploadForm;
use nemmo\attachments\ModuleTrait;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\UploadedFile;

class FileController extends Controller
{
    use ModuleTrait;

    public function actionUpload()
    {
        $model = new UploadForm();
        $model->file = UploadedFile::getInstances($model, 'file');

        if ($model->rules()[0]['maxFiles'] == 1) {
            $model->file = UploadedFile::getInstances($model, 'file')[0];
        }

        if ($model->file && $model->validate()) {
            $result['uploadedFiles'] = [];
            if (is_array($model->file)) {
                foreach ($model->file as $file) {
                    $path = $this->getModule()->getUserDirPath() . DIRECTORY_SEPARATOR . $file->name;
                    $file->saveAs($path);
                    $result['uploadedFiles'][] = $file->name;
                }
            } else {
                $path = $this->getModule()->getUserDirPath() . DIRECTORY_SEPARATOR . $model->file->name;
                $model->file->saveAs($path);
            }
            return json_encode($result);
        } else {
            return json_encode([
                'error' => $model->errors['file']
            ]);
        }
    }

    /**
     * Download action for the file
     * @param integer $id ID of the image to fetch
     * @param integer $size Optional maximum size of the image. Requires yii2-easy-thumbnail-image-helper
     * @param bool $crop Optional cropping. Requires yii2-easy-thumbnail-image-helper
     * @return static Image data
     */
    public function actionDownload($id, $size = NULL, $crop = FALSE)
    {
        if (!is_null($size) && $this->getModule()->checkResizeRequirements() == FALSE)
            throw new \Exception("You need himiklab/yii2-easy-thumbnail-image-helper for image resize features");
        
        $file = File::findOne(['id' => $id]);
        $filePath = $this->getModule()->getFilesDirPath($file->hash) . DIRECTORY_SEPARATOR . $file->hash . '.' . $file->type;
        
        // Resize if requested
        if (!is_null($size) && $file->isImage) {
            $filePath = \himiklab\thumbnail\EasyThumbnailImage::thumbnailFile(
                    $filePath, 
                    $size, 
                    $size, 
                    $crop ? \himiklab\thumbnail\EasyThumbnailImage::THUMBNAIL_OUTBOUND : \himiklab\thumbnail\EasyThumbnailImage::THUMBNAIL_INSET);
        }

        return \Yii::$app->response->sendFile($filePath, "$file->name.$file->type");
    }
    
    public function actionDelete($id)
    {
        $this->getModule()->detachFile($id);

        if (\Yii::$app->request->isAjax) {
            return json_encode([]);
        } else {
            return $this->redirect(Url::previous());
        }
    }

    public function actionDownloadTemp($filename)
    {
        $filePath = $this->getModule()->getUserDirPath() . DIRECTORY_SEPARATOR . $filename;

        return \Yii::$app->response->sendFile($filePath, $filename);
    }

    public function actionDeleteTemp($filename)
    {
        $userTempDir = $this->getModule()->getUserDirPath();
        $filePath = $userTempDir . DIRECTORY_SEPARATOR . $filename;
        unlink($filePath);
        if (!sizeof(FileHelper::findFiles($userTempDir))) {
            rmdir($userTempDir);
        }

        if (\Yii::$app->request->isAjax) {
            return json_encode([]);
        } else {
            return $this->redirect(Url::previous());
        }
    }
}
