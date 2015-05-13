<?php

namespace nemmo\attachments\models;

use nemmo\attachments\ModuleTrait;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * This is the model class for table "attach_file".
 *
 * @property integer $id
 * @property string $name
 * @property string $model
 * @property integer $itemId
 * @property string $hash
 * @property integer $size
 * @property string $type
 * @property string $mime
 * 
 * @property string $url Alias for downloadUrl
 * @property string $downloadUrl Get the download url of the image
 * @property string $viewUrl Get the inline view url of the image
 * @property string $path Get the local file path of the image
 */
class File extends ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'attach_file';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'model', 'itemId', 'hash', 'size', 'type', 'mime'], 'required'],
            [['itemId', 'size'], 'integer'],
            [['name', 'model', 'hash', 'type', 'mime'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'model' => 'Model',
            'itemId' => 'Item ID',
            'hash' => 'Hash',
            'size' => 'Size',
            'type' => 'Type',
            'mime' => 'Mime'
        ];
    }

    /**
     * Obtains the download url of hte image
     * @return string
     */
    public function getDownloadUrl()
    {
        return Url::to(['/attachments/file/download', 'id' => $this->id]);
    }
    
    /**
     * Obtains the inline view url of hte image
     * @return string
     */
    public function getViewUrl()
    {
        return Url::to(['/attachments/file/view', 'id' => $this->id]);
    }
    
    /**
     * Support for legacy method
     * @return string
     */
    public function getUrl()
    {
        return $this->getDownloadUrl();
    }

    public function getPath()
    {
        return $this->getModule()->getFilesDirPath($this->hash) . DIRECTORY_SEPARATOR . $this->hash . '.' . $this->type;
    }
}
