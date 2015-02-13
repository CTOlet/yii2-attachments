Yii2 attachments
================
Extension for file uploading and attaching to the models

Demo
----
You can see the demo on the [krajee](http://plugins.krajee.com/file-input/demo) website

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
			'storePath' => '@app/uploads/store',
			'rules' => [ // Rules according to the FileValidator
				'mimeTypes' => 'image/png', // Only png images
				'maxSize' => 1024 * 1024 // 1 MB
			]
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
	
5. Make sure that you have added `'enctype' => 'multipart/form-data'` to the ActiveForm options	

Usage
-----

1. In the `form.php` of your model add file input
	
	```php
	<?= \nemmo\attachments\components\AttachmentsInput::widget([
		'id' => 'file-input', // Optional
		'model' => $model,
		'pluginOptions' => [ // Plugin options of the Kartik's FileInput widget 
			'maxFileCount' => 10 // Client max files
		],
		'options' => [] // Options of the Kartik's FileInput widget
	]) ?>
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

- **Feb 13, 2015** -	Added restrictions to files (see point 1 in the Usage section), now use ```AttachmentsInput``` widget on the form view	instead of ```FileInput```
- **Feb 11, 2015** -	Added preview of uploaded but not saved files and ```tableOptions``` property for widget
- **Feb 2, 2015** -		Fix: all attached files will be deleted with the model.
- **Feb 1, 2015** -		AJAX or basic upload.
- **Jan 30, 2015** -	Several previews of images and other files, fix of required packages. 
- **Jan 29, 2015** -	First version with basic uploading and previews.
