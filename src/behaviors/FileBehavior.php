<?php
/**
 * Created by PhpStorm.
 * User: Алимжан
 * Date: 27.01.2015
 * Time: 12:24
 */

namespace nemmo\attachments\behaviors;

use nemmo\attachments\events\FileEvent;
use nemmo\attachments\models\File;
use nemmo\attachments\ModuleTrait;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\UploadedFile;

class FileBehavior extends Behavior
{
    use ModuleTrait;

    const EVENT_AFTER_ATTACH_FILES = 'afterAttachFiles';

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'saveUploads',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveUploads',
            ActiveRecord::EVENT_AFTER_DELETE => 'deleteUploads'
        ];
    }

    public function saveUploads($event)
    {
        $files = UploadedFile::getInstancesByName('UploadForm[file]');

        if (!empty($files)) {
            foreach ($files as $file) {
                if (!$file->saveAs($this->getModule()->getUserDirPath() . $file->name)) {
                    throw new \Exception(\Yii::t('yii', 'File upload failed.'));
                }
            }
        }

        $userTempDir = $this->getModule()->getUserDirPath();
        $attachedFiles = [];
        foreach (FileHelper::findFiles($userTempDir) as $file) {
            if (!($attachedFile = $this->getModule()->attachFile($file, $this->owner))) {
                throw new \Exception(\Yii::t('yii', 'File upload failed.'));
            }else{
                $attachedFiles[] = $attachedFile;
            }
        }
        if(count($attachedFiles)){
            $event = \Yii::createObject(['class' => FileEvent::class, 'files' => $attachedFiles]);
            $this->owner->trigger(self::EVENT_AFTER_ATTACH_FILES, $event);
        }
        rmdir($userTempDir);
    }

    public function deleteUploads($event)
    {
        foreach ($this->getFiles() as $file) {
            $this->getModule()->detachFile($file->id);
        }
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

        $userTempDir = $this->getModule()->getUserDirPath();
        foreach (FileHelper::findFiles($userTempDir) as $file) {
            if (substr(FileHelper::getMimeType($file), 0, 5) === 'image') {
                $initialPreview[] = Html::img(['/attachments/file/download-temp', 'filename' => basename($file)], ['class' => 'file-preview-image']);
            } else {
                $initialPreview[] = Html::beginTag('div', ['class' => 'file-preview-other']) .
                    Html::beginTag('h2') .
                    Html::tag('i', '', ['class' => 'glyphicon glyphicon-file']) .
                    Html::endTag('h2') .
                    Html::endTag('div');
            }
        }

        foreach ($this->getFiles() as $file) {
             $initialPreview[] =Url::to([$file->getUrl(true)],true);
        }

        return $initialPreview;
    }

    public function getInitialPreviewConfig()
    {
        $initialPreviewConfig = [];

        $userTempDir = $this->getModule()->getUserDirPath();
        foreach (FileHelper::findFiles($userTempDir) as $file) {
            $filename = basename($file);
            $initialPreviewConfig[] = [
                'caption' => $filename,
                'url' => Url::to(['/attachments/file/delete-temp',
                    'filename' => $filename
                ]),
            ];
        }

        foreach ($this->getFiles() as $index => $file) {
           if(str_contains($file->mime,"image"))
            {
                $initialPreviewConfig[] = [
                    'caption' => "$file->name.$file->type",
                    'url' => Url::toRoute(['/attachments/file/delete',
                    'id' => $file->id])
                ];
            }
            elseif(str_contains($file->mime,"text")){
                $initialPreviewConfig[] = [
                'type'=> "text",
                'caption' => "$file->name.$file->type",
                'url' => Url::toRoute(['/attachments/file/delete',
                'id' => $file->id])
            ];
            }
            elseif(str_contains($file->mime,"video")){
                $initialPreviewConfig[] = [
                'type'=> "video",
                'caption' => "$file->name.$file->type",
                'url' => Url::toRoute(['/attachments/file/delete',
                'id' => $file->id])
            ];
            }
            elseif(str_contains($file->type,"doc")||str_contains($file->type,"ppt")||str_contains($file->type,"xls")){
                $initialPreviewConfig[] = [
                'type'=> "office",
                'caption' => "$file->name.$file->type",
                'url' => Url::toRoute(['/attachments/file/delete',
                'id' => $file->id])
            ];
            }
            else{
                $initialPreviewConfig[] = [
                    'type'=> $file->type,
                    'caption' => "$file->name.$file->type",
                    'url' => Url::toRoute(['/attachments/file/delete',
                    'id' => $file->id])
                ];
            }
        }
        return $initialPreviewConfig;
    }
}
