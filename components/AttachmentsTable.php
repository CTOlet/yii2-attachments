<?php

namespace nemmo\attachments\components;

use common\modules\attachments\behaviors\FileBehavior;
use Yii;
use yii\data\ArrayDataProvider;
use yii\db\ActiveRecord;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Created by PhpStorm.
 * User: Алимжан
 * Date: 28.01.2015
 * Time: 19:10
 */
class AttachmentsTable extends \yii\bootstrap\Widget
{
    /** @var  ActiveRecord */
    public $model;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        if (!$this->model) {
            return '<div class="alert alert-danger"><b>Error</b>: The model is empty</div>';
        }

        $hasFileBehavior = false;
        foreach ($this->model->getBehaviors() as $behavior) {
            if ($behavior->className() == FileBehavior::className()) {
                $hasFileBehavior = true;
            }
        }
        if (!$hasFileBehavior) {
            return '<div class="alert alert-danger"><b>Error</b>: The FileBehavior has not been attached to the model</div>';
        }

        Url::remember(Url::current());
        return GridView::widget([
            'dataProvider' => new ArrayDataProvider(['allModels' => $this->model->getFiles()]),
            'layout' => '{items}',
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn'
                ],
                [
                    'label' => 'Название файла',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Html::a("$model->name.$model->type", $model->getUrl());
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{delete}',
                    'buttons' => [
                        'delete' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                                [
                                    '/attachments/file/delete',
                                    'id' => $model->id
                                ],
                                [
                                    'title' => Yii::t('yii', 'Удалить'),
                                    'data-confirm' => Yii::t('yii', 'Вы уверены, что хотите удалить сотрудника?'),
                                    'data-method' => 'post',
                                    'data-pjax' => '0',
                                ]
                            );
                        }
                    ]
                ],
            ]
        ]);
    }
}