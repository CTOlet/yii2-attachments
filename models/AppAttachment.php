<?php

namespace dlds\attachments\models;

use dlds\attachments\ModuleTrait;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * This is the model class for table "app_attachment".
 *
 * @property integer $id
 * @property string $name
 * @property string $model
 * @property integer $item_id
 * @property string $hash
 * @property integer $size
 * @property string $type
 * @property string $mime
 */
class AppAttachment extends ActiveRecord
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'app_attachment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'model', 'item_id', 'hash', 'size', 'type', 'mime'], 'required'],
            [['item_id', 'size'], 'integer'],
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
            'item_id' => 'Item ID',
            'hash' => 'Hash',
            'size' => 'Size',
            'type' => 'Type',
            'mime' => 'Mime'
        ];
    }

    public function getUrl()
    {
        return Url::to(['/attachments/app-attachment/download', 'id' => $this->id]);
    }
}
