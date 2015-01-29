Yii2 attachments
================
Extension for file uploading and attaching to the models

Installation
------------

1. The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

	Either run
	
	```
	php composer.phar require nemmo/yii2-attachments "*"
	```
	
	or add
	
	```
	"nemmo/yii2-attachments": "*"
	```
	
	to the require section of your `composer.json` file.

2. Apply migrations
	
	```
	php yii migrate/up --migrationPath=@vendor/nemmo/yii2-attachments/migrations
	```

3.  Add module to `config/main.php`
	
	```
	'attachments' => [
	            'class' => nemmo\attachments\Module::className(),
	            'tempPath' => '@statics/temp',
	            'storePath' => '@statics/store'
	        ]
	```

4. Attach behavior to your model (be sure that your model has "id" property)
	
	```
	public function behaviors()
	    {
	        return [
		        ...
	            'fileBehavior' => [
				'class' => \nemmo\attachments\behaviors\FileBehavior::className()
	            ]
	            ...
	        ];
	    }
	```

Usage
-----

1. In the `form.php` of your model add file input
	
	```
	<?= BootstrapFileInput::widget([
	        'name' => 'file',
	        'id' => 'file-input',
	        'options' => [
	            'multiple' => true,
	        ],
	        'clientOptions' => [
	            'uploadUrl' => Url::to('/attachments/file/upload'),
	            'maxFileCount' => 10,
	            'previewFileType' => 'file',
	            'browseClass' => 'btn btn-success',
	            'browseLabel' => 'Обзор',
	            'uploadClass' => 'btn btn-info',
	            'uploadLabel' => 'Загрузить',
	            'removeClass' => 'btn btn-danger',
	            'removeLabel' => 'Удалить',
	            'overwriteInitial' => false
	        ]
	    ]); ?>
	```

2. Use widget to show all attachments of the model in the `view.php`
	
	```
	<?= \nemmo\attachments\components\AttachmentsTable::widget(['model' => $model]) ?>
	```

3. (Optional) Add onclick action to your submit button that uploads all files before submitting form
	
	```
	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', [
	            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
	            'onclick' => "$('#file-input').fileinput('upload');"
	        ]) ?>
	    </div>
	```