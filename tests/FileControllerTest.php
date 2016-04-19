<?php
/**
 * Created by PhpStorm.
 * User: Alimzhan
 * Date: 2/2/2016
 * Time: 9:45 PM
 */

namespace tests;

use nemmo\attachments\Module;
use tests\models\Comment;
use Yii;
use yii\web\Response;

class FileControllerTest extends TestCase
{
    /**
     * Upload action test
     */
    public function testPreUpload1()
    {
        $types = ['png', 'txt', 'jpg'];
        $this->generateFiles($types);
        $response = Yii::$app->runAction('attachments/file/upload');
        $this->assertArrayHasKey('uploadedFiles', $response);
        $this->assertTrue(in_array('file.png', $response['uploadedFiles']));
        $this->checkFilesExist($types);
        foreach ($types as $type) {
            /** @var Response $response */
            $response = Yii::$app->runAction('attachments/file/download-temp', ['filename' => "file.$type"]);
            ob_start();
            $response->send();
            $actual = ob_get_clean();
            $response->clear();
            $expected = file_get_contents(Yii::getAlias("@tests/files/file.$type"));
            $this->assertEquals($expected, $actual);

            $response = Yii::$app->runAction('attachments/file/delete-temp', ['filename' => "file.$type"]);
            $this->assertEquals($response, []);
        }
        $this->checkFilesNotExist($types);
        $this->assertFileNotExists($this->getTempDirPath());

        $comment = new Comment();
        $comment->text = 'test';
        $this->assertTrue($comment->save());

        $file = $comment->files[0];
        /** @var Response $response */
        $response = Yii::$app->runAction('attachments/file/download', ['id' => $file->id]);
        ob_start();
        $response->send();
        $actual = ob_get_clean();
        $response->clear();
        $expected = file_get_contents(Yii::getAlias("@tests/files/file.{$file->type}"));
        $this->assertEquals($expected, $actual);
        $this->assertFileExists($file->path);
        $response = Yii::$app->runAction('attachments/file/delete', ['id' => -1]);
        $this->assertEquals($response, false);
        $response = Yii::$app->runAction('attachments/file/delete', ['id' => $file->id]);
        $this->assertEquals($response, true);
        $this->assertFileNotExists($file->path);

        $this->assertNotSame(false, $comment->delete());
    }

    public function testPreUpload2()
    {
        $types = ['png', 'txt', 'jpg', 'zip'];
        $this->generateFiles($types);
        $response = Yii::$app->runAction('attachments/file/upload');
        $this->assertArrayHasKey('error', $response);
        $errorMessage = 'You can upload at most 3 files.';
        $this->assertTrue(in_array($errorMessage, $response['error']));
    }

    public function testPreUpload3()
    {
        Yii::$app->setModule('attachments', [
            'class' => Module::className(),
            'rules' => [
                'maxFiles' => 1
            ]
        ]);
        $types = ['png', 'zip'];
        $this->generateFiles($types);
        $response = Yii::$app->runAction('attachments/file/upload');
        $this->assertArrayHasKey('error', $response);
        $errorMessage = 'Please upload a file.';
        $this->assertTrue(in_array($errorMessage, $response['error']));
    }

    public function testPreUpload4()
    {
        Yii::$app->setModule('attachments', [
            'class' => Module::className(),
            'rules' => [
                'maxFiles' => 1
            ]
        ]);
        $types = ['png'];
        $this->generateFiles($types);
        $response = Yii::$app->runAction('attachments/file/upload');
        $this->assertArrayHasKey('uploadedFiles', $response);
        $this->assertTrue(in_array('file.png', $response['uploadedFiles']));
        $this->checkFilesExist($types);
    }

    public function testPreUpload5()
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
        $response = Yii::$app->runAction('attachments/file/upload');
        $this->assertArrayHasKey('error', $response);
        $errorMessage = 'Only files with these MIME types are allowed: image/png, image/jpeg.';
        $this->assertTrue(in_array($errorMessage, $response['error']));
    }
}
