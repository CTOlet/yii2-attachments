<?php
/**
 * Created by PhpStorm.
 * User: Alimzhan
 * Date: 2/2/2016
 * Time: 9:45 PM
 */

namespace tests;

use nemmo\attachments\Module;
use Yii;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class FileControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        FileHelper::removeDirectory(Yii::getAlias('@tests/uploads'));
        UploadedFile::reset();
    }

    /**
     * Upload action test
     */
    public function testUpload1()
    {
        $types = ['png', 'txt', 'jpg'];
        $this->generateFiles($types);
        $output = Yii::$app->runAction('attachments/file/upload');
        $this->assertArrayHasKey('uploadedFiles', $output);
        $this->assertTrue(in_array('file.png', $output['uploadedFiles']));
        $this->checkFilesExist($types);
    }

    public function testUpload2()
    {
        $types = ['png', 'txt', 'jpg', 'zip'];
        $this->generateFiles($types);
        $output = Yii::$app->runAction('attachments/file/upload');
        $this->assertArrayHasKey('error', $output);
    }

    public function testUpload3()
    {
        Yii::$app->setModule('attachments', [
            'class' => Module::className(),
            'rules' => [
                'maxFiles' => 1
            ]
        ]);
        $types = ['png', 'zip'];
        $this->generateFiles($types);
        $output = Yii::$app->runAction('attachments/file/upload');
        $this->assertArrayHasKey('error', $output);
    }

    public function testUpload4()
    {
        Yii::$app->setModule('attachments', [
            'class' => Module::className(),
            'rules' => [
                'maxFiles' => 1
            ]
        ]);
        $types = ['png'];
        $this->generateFiles($types);
        $output = Yii::$app->runAction('attachments/file/upload');
        $this->assertArrayHasKey('uploadedFiles', $output);
        $this->assertTrue(in_array('file.png', $output['uploadedFiles']));
        $this->checkFilesExist($types);
    }

    public function testUpload5()
    {
        Yii::$app->setModule('attachments', [
            'class' => Module::className(),
            'rules' => [
                'maxFiles' => 3,
                'mimeTypes' => ['image/png', 'image/jpeg']
            ]
        ]);
        $types = ['png', 'jpg', 'zip'];
        $this->generateFiles($types);
        $output = Yii::$app->runAction('attachments/file/upload');
        var_dump($output);
        $this->assertArrayHasKey('error', $output);
    }

    public function generateFiles($types)
    {
        $_FILES = [];
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
            $filePath = Yii::getAlias('@tests/uploads/temp/' . Yii::$app->session->id . '/file.' . $type);
            $this->assertTrue(file_exists($filePath));
        }
    }
}
