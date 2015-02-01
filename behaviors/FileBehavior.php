<?php
/**
 * Created by PhpStorm.
 * User: Алимжан
 * Date: 27.01.2015
 * Time: 12:24
 */

namespace nemmo\attachments\behaviors;

use nemmo\attachments\models\File;
use nemmo\attachments\ModuleTrait;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\UploadedFile;

class FileBehavior extends \yii\base\Behavior
{
    use ModuleTrait;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'saveUploads',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveUploads'
        ];
    }

    /**
     * @param $filePath string
     * @return bool|File
     * @throws \Exception
     */
    public function attachFile($filePath)
    {
        if (!$this->owner->id) {
            throw new \Exception('Owner must have id when you attach file');
        }

        if (!file_exists($filePath)) {
            throw new \Exception('File not exist :' . $filePath);
        }

        $fileHash = md5(microtime(true) . $filePath);
        $fileType = pathinfo($filePath, PATHINFO_EXTENSION);
        $newFileName = $fileHash . '.' . $fileType;
        $fileDirPath = $this->getModule()->getFilesDirPath($fileHash);

        $newFilePath = $fileDirPath . DIRECTORY_SEPARATOR . $newFileName;

        copy($filePath, $newFilePath);

        if (!file_exists($filePath)) {
            throw new \Exception('Cannot copy file! ' . $filePath . ' to ' . $newFilePath);
        }

        $file = new File();

        $file->name = pathinfo($filePath, PATHINFO_FILENAME);
        $file->model = $this->getModule()->getShortClass($this->owner);
        $file->itemId = $this->owner->id;
        $file->hash = $fileHash;
        $file->size = filesize($filePath);
        $file->type = $fileType;
        $file->mime = FileHelper::getMimeType($filePath);

        if ($file->save()) {
            unlink($filePath);
            return $file;
        } else {
            if (count($file->getErrors()) > 0) {

                $ar = array_shift($file->getErrors());

                unlink($newFilePath);
                throw new \Exception(array_shift($ar));
            }
            return false;
        }
    }

    public function saveUploads($event)
    {
        $files = UploadedFile::getInstancesByName('file');

        if (!empty($files)) {
            foreach ($files as $file) {
                if (!$file->saveAs($this->getModule()->getUserDirPath() . DIRECTORY_SEPARATOR . $file->name)) {
                    throw new \Exception('Cannot attach file');
                }
            }
        }

        $userTempDir = $this->getModule()->getUserDirPath();
        foreach (FileHelper::findFiles($userTempDir) as $file) {
            if (!$this->attachFile($file)) {
                throw new \Exception('Cannot attach file');
            }
        }
        rmdir($userTempDir);
    }

    /**
     * @return File[]
     * @throws \Exception
     */
    public function getFiles()
    {
        $fileQuery = File::find()
            ->where([
                'itemId' => $this->owner->id,
                'model' => $this->getModule()->getShortClass($this->owner)
            ]);
        $fileQuery->orderBy(['id' => SORT_ASC]);

        return $fileQuery->all();
    }

    public function getInitialPreview()
    {
        $initialPreview = [];

        foreach ($this->getFiles() as $file) {
            if (substr($file->mime, 0, 5) === 'image') {
                $initialPreview[] = Html::img($file->getUrl(), ['class' => 'file-preview-image']);
            } else {
                $initialPreview[] = Html::beginTag('div', ['class' => 'file-preview-other']) .
                    Html::beginTag('h2') .
                    Html::tag('i', '', ['class' => 'glyphicon glyphicon-file']) .
                    Html::endTag('h2') .
                    Html::endTag('div');
            }
        }

        return $initialPreview;
    }

    public function getInitialPreviewConfig()
    {
        $initialPreviewConfig = [];

        foreach ($this->getFiles() as $index => $file) {
            $initialPreviewConfig[] = [
                'caption' => "$file->name.$file->type",
                'url' => Url::toRoute(['/attachments/file/delete',
                    'id' => $file->id
                ]),
            ];
        }

        return $initialPreviewConfig;
    }
}