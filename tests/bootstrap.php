<?php
/**
 * Created by PhpStorm.
 * User: Alimzhan
 * Date: 1/30/2016
 * Time: 9:46 PM
 */
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

Yii::setAlias('@tests', __DIR__);

new \yii\console\Application([
    'id' => 'unit',
    'basePath' => __DIR__,
    'vendorPath' => __DIR__ . '/../vendor',
    'modules' => [
        'attachments' => \nemmo\attachments\Module::className()
    ],
    'components' => [
        'db' => [
            'class' => \yii\db\Connection::className(),
            'dsn' => 'sqlite:' . __DIR__ . '/data/db.sqlite'
        ]
    ]
]);