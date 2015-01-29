<?php

namespace nemmo\attachments\controllers;

use nemmo\attachments\models\File;
use nemmo\attachments\ModuleTrait;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\UploadedFile;

class FileController extends Controller
{
    use ModuleTrait;

    public function actionUpload()
    {
        $file = UploadedFile::getInstanceByName('file');

        if ($file->saveAs($this->getModule()->getUserDirPath() . DIRECTORY_SEPARATOR . $file->name)) {
            return json_encode(['uploadedFile' => $file->name]);
        } else {
            throw new \Exception('Cannot upload the file');
        }
    }

    public function actionDownload($id)
    {
        $file = File::findOne(['id' => $id]);
        $filePath = $this->getModule()->getFilesDirPath($file->hash) . DIRECTORY_SEPARATOR . $file->hash . '.' . $file->type;

        return \Yii::$app->response->sendFile($filePath, "$file->name.$file->type");
    }

    public function actionDelete($id)
    {
        $file = File::findOne(['id' => $id]);
        $filePath = $this->getModule()->getFilesDirPath($file->hash) . DIRECTORY_SEPARATOR . $file->hash . '.' . $file->type;

        unlink($filePath);
        $file->delete();

        return $this->redirect(Url::previous());
    }
}
