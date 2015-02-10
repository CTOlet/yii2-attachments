<?php

namespace dlds\attachments\components;

use dlds\attachments\behaviors\AttachmentBehavior;
use dlds\attachments\ModuleTrait;
use Yii;
use yii\bootstrap\Widget;
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
class AttachmentsTable extends Widget {

    use ModuleTrait;

    /** @var ActiveRecord */
    public $model;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        if (!$this->model)
        {
            return Html::tag('div', Html::tag('b', Yii::t('yii', 'Error')) . ': ' . $this->getModule()->t('attachments', 'The model cannot be empty.'
                            ), ['class' => 'alert alert-danger']
            );
        }

        $hasAttachmentBehavior = false;
        foreach ($this->model->getBehaviors() as $behavior)
        {
            if ($behavior->className() == AttachmentBehavior::className())
            {
                $hasAttachmentBehavior = true;
            }
        }
        if (!$hasAttachmentBehavior)
        {
            return Html::tag('div', Html::tag('b', Yii::t('yii', 'Error')) . ': ' . $this->getModule()->t('attachments', 'The behavior AttachmentBehavior has not been attached to the model.'
                            ), ['class' => 'alert alert-danger']
            );
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
                            'label' => $this->getModule()->t('attachments', 'File name'),
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
                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', [
                                                '/attachments/file/delete',
                                                'id' => $model->id
                                                    ], [
                                                'title' => Yii::t('yii', 'Delete'),
                                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                                'data-method' => 'post',
                                                    ]
                                    );
                                }
                                    ]
                                ],
                            ]
                ]);
            }

        }
        