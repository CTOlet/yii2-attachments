<?php
/**
 * Created by PhpStorm.
 * User: Alimzhan
 * Date: 2/2/2016
 * Time: 8:58 PM
 */

namespace tests\models;

use nemmo\attachments\behaviors\FileBehavior;
use yii\db\ActiveRecord;

/**
 * Class Comment
 * @property string text
 */
class Comment extends ActiveRecord
{
    /**
     * @inheritDoc
     */
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            ['text', 'required'],
            ['text', 'string']
        ];
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            FileBehavior::className()
        ];
    }
}