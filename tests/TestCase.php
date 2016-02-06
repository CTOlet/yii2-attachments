<?php
/**
 * Created by PhpStorm.
 * User: Alimzhan
 * Date: 2/6/2016
 * Time: 10:03 PM
 */

namespace tests;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\AssetManager;
use yii\web\View;

/**
 * This is the base class for all yii framework unit tests.
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        FileHelper::removeDirectory(Yii::getAlias('@tests/uploads'));
        $this->mockApplication();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        $this->destroyApplication();
    }

    protected function mockApplication($config = [], $appClass = '\yii\web\Application')
    {
        new $appClass(ArrayHelper::merge([
            'id' => 'test-app',
            'basePath' => Yii::getAlias('@tests'),
            'vendorPath' => Yii::getAlias('@tests/../vendor'),
            'modules' => [
                'attachments' => [
                    'class' => \nemmo\attachments\Module::className(),
                ]
            ],
            'components' => [
                'request' => [
                    'cookieValidationKey' => 'wefJDF8sfdsfSDefwqdxj9oq',
                    'scriptFile' => Yii::getAlias('@tests/index.php'),
                    'scriptUrl' => '/index.php',
                ],
                'db' => [
                    'class' => \yii\db\Connection::className(),
                    'dsn' => 'sqlite:' . Yii::getAlias('@tests/data/db.sqlite')
                ],
                'assetManager' => [
                    'basePath' => '@tests/assets',
                    'baseUrl' => '/',
                ]
            ]
        ], $config));
    }

    protected function destroyApplication()
    {
        Yii::$app = null;
    }

    /**
     * Creates a view for testing purposes
     *
     * @return View
     */
    protected function getView()
    {
        $view = new View();
        $view->setAssetManager(new AssetManager([
            'basePath' => '@tests/assets',
            'baseUrl' => '/',
        ]));
        return $view;
    }

    /**
     * Asserting two strings equality ignoring line endings
     *
     * @param string $expected
     * @param string $actual
     */
    public function assertEqualsWithoutLE($expected, $actual)
    {
        $expected = str_replace("\r\n", "\n", $expected);
        $actual = str_replace("\r\n", "\n", $actual);
        $this->assertEquals($expected, $actual);
    }
}