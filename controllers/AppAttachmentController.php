<?php

namespace dlds\attachments\controllers;

use dlds\attachments\models\AppAttachment;
use dlds\attachments\ModuleTrait;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\UploadedFile;

class AppAttachmentController extends Controller {

    use ModuleTrait;

    public function actionUpload()
    {
        $file = UploadedFile::getInstancesByName('file')[0];

        if ($file->saveAs($this->getModule()->getUserDirPath() . DIRECTORY_SEPARATOR . $file->name))
        {
            return json_encode(['uploadedFile' => $file->name]);
        }
        else
        {
            throw new \Exception(\Yii::t('yii', 'File upload failed.'));
        }
    }

    public function actionDownload($id)
    {
        $file = AppAttachment::findOne(['id' => $id]);
        $filePath = $this->getModule()->getFilesDirPath($file->hash) . DIRECTORY_SEPARATOR . $file->hash . '.' . $file->type;

        return \Yii::$app->response->sendFile($filePath, "$file->name.$file->type");
    }

    public function actionDelete($id)
    {
        $this->getModule()->detachFile($id);

        if (\Yii::$app->request->isAjax)
        {
            return json_encode([]);
        }
        else
        {
            return $this->redirect(Url::previous());
        }
    }

}
