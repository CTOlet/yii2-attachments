<?php

namespace nemmo\attachments\models;

use nemmo\attachments\ModuleTrait;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Created by PhpStorm.
 * User: Алимжан
 * Date: 13.02.2015
 * Time: 21:07
 */
class UploadForm extends Model
{
    use ModuleTrait;

    /**
     * @var UploadedFile|Null file attribute
     */
    public $file;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            array_replace([['file'], 'file'], $this->getModule()->rules)
        ];
    }
}