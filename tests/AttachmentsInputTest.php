<?php
/**
 * Created by PhpStorm.
 * User: Alimzhan
 * Date: 2/6/2016
 * Time: 10:18 PM
 */

namespace tests;

use nemmo\attachments\components\AttachmentsInput;
use tests\models\Comment;
use Yii;
use yii\web\Controller;

class AttachmentsInputTest extends TestCase
{
    public function testEmptyConfig()
    {
        $this->setExpectedException('\yii\base\InvalidConfigException');
        AttachmentsInput::widget();
    }

    public function testDefaultConfig()
    {
        Yii::$app->controller = new Controller('test', Yii::$app);
        $response = Yii::$app->controller->render('attachments-input-view', [
            'model' => new Comment()
        ]);

        $this->assertContains("var fileInput = $('#file-input');", $response);
        $this->assertContains("UploadForm[file][]", $response);
        $this->assertContains('jquery.js', $response);
        $this->assertContains('fileinput.js', $response);
        $this->assertContains('fileinput.css', $response);
        $this->assertContains('kv-widgets.css', $response);
    }

    public function testDefaultConfigOldModel()
    {
        $comment = new Comment();
        $comment->text = 'test';
        $this->generateFiles(['png', 'jpg', 'txt']);
        $comment->save();

        Yii::$app->controller = new Controller('test', Yii::$app);
        $response = Yii::$app->controller->render('attachments-input-view', [
            'model' => $comment
        ]);

        $this->assertContains("var fileInput = $('#file-input');", $response);
        $this->assertContains("UploadForm[file][]", $response);
        $this->assertContains('jquery.js', $response);
        $this->assertContains('fileinput.js', $response);
        $this->assertContains('fileinput.css', $response);
        $this->assertContains('kv-widgets.css', $response);
        $this->assertContains('file-preview-image', $response);
        $this->assertContains('file-preview-other', $response);
        $this->assertContains('attachments%2Ffile%2Fdelete', $response);
    }
}