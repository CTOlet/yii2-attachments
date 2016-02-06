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
        $controller = new Controller('test', Yii::$app);
        $response = $controller->render('attachments-input-view', [
            'model' => new Comment()
        ]);

        $this->assertContains("var fileInput = $('#file-input');", $response);
        $this->assertContains("UploadForm[file][]", $response);
        $this->assertContains('jquery.js', $response);
        $this->assertContains('fileinput.js', $response);
        $this->assertContains('fileinput.css', $response);
        $this->assertContains('kv-widgets.css', $response);
    }
}