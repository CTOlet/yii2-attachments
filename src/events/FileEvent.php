<?php

namespace nemmo\attachments\events;

use yii\base\Event;

class FileEvent extends Event
{
    /**
     * @var nemmo\attachments\models\File[]
     */
    private $_files;

    /**
     * @return nemmo\attachments\models\File[]
     */
    public function getFiles()
    {
        return $this->_files;
    }

    /**
     * @param nemmo\attachments\models\File[] $files
     */
    public function setFiles($files)
    {
        $this->_files = $files;
    }
}
