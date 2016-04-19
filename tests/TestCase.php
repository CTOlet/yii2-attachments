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
use yii\web\UploadedFile;
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
        Yii::$app->db->createCommand()->truncateTable('attach_file')->execute();
        Yii::$app->db->createCommand()->truncateTable('comment')->execute();
        Yii::$app->db->createCommand()->truncateTable('sqlite_sequence')->execute();
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
//                'urlManager' => [
//                    'class' => \yii\web\UrlManager::className(),
//                    'baseUrl' => 'http://localhost',
//                    'scriptUrl' => '/index.php'
//                ],
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

    public function generateFiles($types)
    {
        $_FILES = [];
        UploadedFile::reset();

        foreach ($types as $index => $type) {
            $file = "file.$type";
            $path = Yii::getAlias("@tests/files/$file");
            $_FILES["UploadForm[file][$index]"] = [
                'name' => $file,
                'type' => mime_content_type($path),
                'size' => filesize($path),
                'tmp_name' => $path,
                'error' => 0
            ];
        }
    }

    public function checkFilesExist($types)
    {
        foreach ($types as $type) {
            $filePath = $this->getTempDirPath() . "/file.$type";
            $this->assertFileExists($filePath);
        }
    }

    public function checkFilesNotExist($types)
    {
        foreach ($types as $type) {
            $filePath = $this->getTempDirPath() . "/file.$type";
            $this->assertFileNotExists($filePath);
        }
    }

    public function getTempDirPath()
    {
        return Yii::getAlias('@tests/uploads/temp/' . Yii::$app->session->id);
    }
}