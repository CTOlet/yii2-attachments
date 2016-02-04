<?php
/**
 * Created by PhpStorm.
 * User: Alimzhan
 * Date: 1/30/2016
 * Time: 9:46 PM
 */

namespace yii\web;

use Yii;

/**
 * Turn on all error reports
 */
error_reporting(E_ALL | E_STRICT);

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

Yii::setAlias('@tests', __DIR__);

/**
 * Overwrite functions for fake uploads
 */
function is_uploaded_file($filename)
{
    return file_exists($filename);
}

function move_uploaded_file($filename, $destination)
{
    return copy($filename, $destination);
}

/**
 * Run yii web application
 */
new \yii\web\Application([
    'id' => 'unit',
    'basePath' => Yii::getAlias('@tests'),
    'vendorPath' => Yii::getAlias('@tests/../vendor'),
    'modules' => [
        'attachments' => [
            'class' => \nemmo\attachments\Module::className(),
        ]
    ],
    'components' => [
        'urlManager' => [
            'class' => \yii\web\UrlManager::className(),
            'baseUrl' => 'http://localhost',
            'scriptUrl' => '/index.php'
        ],
        'db' => [
            'class' => \yii\db\Connection::className(),
            'dsn' => 'sqlite:' . Yii::getAlias('@tests/data/db.sqlite')
        ]
    ]
]);