<?php

namespace dlds\attachments;

use dlds\attachments\models\AppAttachment;
use yii\helpers\FileHelper;
use yii\i18n\PhpMessageSource;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'dlds\attachments\controllers';

    public $storePath = '@app/uploads/store';

    public $tempPath = '@app/uploads/temp';

    public function init()
    {
        parent::init();

        // custom initialization code goes here
        if (!$this->storePath or !$this->tempPath) {
            throw new \Exception('Setup storePath and tempPath in module properties');
        }

        $this->defaultRoute = 'file';
        $this->registerTranslations();
    }

    public function registerTranslations()
    {
        \Yii::$app->i18n->translations['dlds/*'] = [
            'class' => PhpMessageSource::className(),
            'sourceLanguage' => 'en-US',
            'basePath' => '@vendor/dlds/yii2-attachments/messages',
            'fileMap' => [
                'dlds/attachments' => 'attachments.php'
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        return \Yii::t('dlds/' . $category, $message, $params, $language);
    }

    public function getStorePath()
    {
        return \Yii::getAlias($this->storePath);
    }

    public function getTempPath()
    {
        return \Yii::getAlias($this->tempPath);
    }

    /**
     * @param $fileHash
     * @return string
     */
    public function getFilesDirPath($fileHash)
    {
        $path = $this->getStorePath() . DIRECTORY_SEPARATOR . $this->getSubDirs($fileHash);

        FileHelper::createDirectory($path);

        return $path;
    }

    public function getSubDirs($fileHash, $depth = 3)
    {
        $depth = min($depth, 9);
        $path = '';

        for ($i = 0; $i < $depth; $i++) {
            $folder = substr($fileHash, $i * 3, 2);
            $path .= $folder;
            if ($i != $depth - 1) $path .= DIRECTORY_SEPARATOR;
        }

        return $path;
    }

    public function getUserDirPath()
    {
        \Yii::$app->session->open();

        $userDirPath = $this->getTempPath() . DIRECTORY_SEPARATOR . \Yii::$app->session->id;
        FileHelper::createDirectory($userDirPath);

        \Yii::$app->session->close();

        return $userDirPath;
    }

    public function getShortClass($obj)
    {
        $className = get_class($obj);
        if (preg_match('@\\\\([\w]+)$@', $className, $matches)) {
            $className = $matches[1];
        }
        return $className;
    }

    /**
     * @param $filePath string
     * @param $owner
     * @return bool|AppAttachment
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function attachFile($filePath, $owner)
    {
        if (!$owner->id) {
            throw new \Exception('Owner must have id when you attach file');
        }

        if (!file_exists($filePath)) {
            throw new \Exception('File not exist :' . $filePath);
        }

        $fileHash = md5(microtime(true) . $filePath);
        $fileType = pathinfo($filePath, PATHINFO_EXTENSION);
        $newFileName = $fileHash . '.' . $fileType;
        $fileDirPath = $this->getFilesDirPath($fileHash);

        $newFilePath = $fileDirPath . DIRECTORY_SEPARATOR . $newFileName;

        copy($filePath, $newFilePath);

        if (!file_exists($filePath)) {
            throw new \Exception('Cannot copy file! ' . $filePath . ' to ' . $newFilePath);
        }

        $file = new AppAttachment();

        $file->name = pathinfo($filePath, PATHINFO_FILENAME);
        $file->model = $this->getShortClass($owner);
        $file->item_id = $owner->id;
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

    public function detachFile($id)
    {
        $file = AppAttachment::findOne(['id' => $id]);
        $filePath = $this->getFilesDirPath($file->hash) . DIRECTORY_SEPARATOR . $file->hash . '.' . $file->type;
        unlink($filePath);

        $file->delete();
    }
}
