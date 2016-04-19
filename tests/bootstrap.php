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

$_SERVER['REQUEST_URI'] = 'http://localhost/index.php';