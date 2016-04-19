<?php

namespace nemmo\attachments\components;

use nemmo\attachments\behaviors\FileBehavior;
use nemmo\attachments\ModuleTrait;
use Yii;
use yii\base\InvalidConfigException;
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
class AttachmentsTable extends Widget
{
    use ModuleTrait;

    /** @var ActiveRecord */
    public $model;

    public $tableOptions = ['class' => 'table table-striped table-bordered table-condensed'];

    public function init()
    {
        parent::init();

        if (empty($this->model)) {
            throw new InvalidConfigException("Property {model} cannot be blank");
        }

        $hasFileBehavior = false;
        foreach ($this->model->getBehaviors() as $behavior) {
            if (is_a($behavior, FileBehavior::className())) {
                $hasFileBehavior = true;
            }
        }
        if (!$hasFileBehavior) {
            throw new InvalidConfigException("The behavior {FileBehavior} has not been attached to the model.");
        }
    }

    public function run()
    {
        $confirm = Yii::t('yii', 'Are you sure you want to delete this item?');
        $js = <<<JS
        $(".delete-button").click(function(){
            var tr = this.closest('tr');
            var url = $(this).data('url');
            if (confirm("$confirm")) {
                $.ajax({
                    method: "POST",
                    url: url,
                    success: function(data) {
                        if (data) {
                            tr.remove();
                        }
                    }
                });
            }
        });
JS;
        Yii::$app->view->registerJs($js);

        return GridView::widget([
            'dataProvider' => new ArrayDataProvider(['allModels' => $this->model->getFiles()]),
            'layout' => '{items}',
            'tableOptions' => $this->tableOptions,
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
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                                '#',
                                [
                                    'class' => 'delete-button',
                                    'title' => Yii::t('yii', 'Delete'),
                                    'data-url' => Url::to(['/attachments/file/delete', 'id' => $model->id])
                                ]
                            );
                        }
                    ]
                ],
            ]
        ]);
    }
}
