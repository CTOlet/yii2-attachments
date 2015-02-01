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
	
	```php
	'modules' => [
		...
		'attachments' => [
			'class' => nemmo\attachments\Module::className(),
			'tempPath' => '@app/uploads/temp',
			'storePath' => '@app/uploads/store'
		]
		...
	]
	```

4. Attach behavior to your model (be sure that your model has "id" property)
	
	```php
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
	
5. Make sure that you have added ```'enctype' => 'multipart/form-data'``` to the ActiveForm options	

Usage
-----

1. In the `form.php` of your model add file input
	
	```php
	<?= \kartik\file\FileInput::widget([
		'name' => 'file[]',
		'id' => 'file-input',
		'options' => [
			'multiple' => true, // false if you want to allow upload a single file
		],
		'pluginOptions' => [
			'uploadUrl' => Url::toRoute('/attachments/file/upload'), // remove this if you don't want to use AJAX uploading 
			'initialPreview' => $model->isNewRecord ? [] : $model->getInitialPreview(),
			'initialPreviewConfig' => $model->isNewRecord ? [] : $model->getInitialPreviewConfig(),
			// other options
		]
	]); ?>
	```

2. Use widget to show all attachments of the model in the `view.php`
	
	```php
	<?= \nemmo\attachments\components\AttachmentsTable::widget(['model' => $model]) ?>
	```

3. (Optional) Add onclick action to your submit button that uploads all files before submitting form
	
	```php
	<?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', [
		'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
		'onclick' => "$('#file-input').fileinput('upload');"
	]) ?>
	```
	
Change log
----------

- **Feb 1, 2015** -		AJAX or basic upload.
- **Jan 30, 2015** -	Several previews of images and other files, fix of required packages. 
- **Jan 29, 2015** -	First version with basic uploading and previews.