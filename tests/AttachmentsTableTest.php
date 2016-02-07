<?php
/**
 * Created by PhpStorm.
 * User: Alimzhan
 * Date: 2/6/2016
 * Time: 10:18 PM
 */

namespace tests;

use nemmo\attachments\components\AttachmentsTable;
use tests\models\Comment;
use Yii;
use yii\db\ActiveRecord;
use yii\web\Controller;

class AttachmentsTableTest extends TestCase
{
    public function testEmptyConfig()
    {
        $this->setExpectedException('\yii\base\InvalidConfigException');
        AttachmentsTable::widget();
    }

    public function testEmptyBehaviorConfig()
    {
        $this->setExpectedException('\yii\base\InvalidConfigException');
        $model = new ActiveRecord();
        AttachmentsTable::widget(['model' => $model]);
    }

    public function testDefaultConfig()
    {
        $comment = new Comment();
        $comment->text = 'test';

        $types = ['png', 'txt', 'jpg'];
        $this->generateFiles($types);
        Yii::$app->runAction('attachments/file/upload');

        $comment->save();

        Yii::$app->controller = new Controller('test', Yii::$app, ['action' => 'test']);
        $response = Yii::$app->controller->render('attachments-table-view', [
            'model' => $comment
        ]);

        $this->assertContains('table table-striped table-bordered table-condensed', $response);
        $this->assertContains("yii.gridView.js", $response);
        $this->assertContains('jquery.js', $response);
        $this->assertContains('yii.js', $response);
        $this->assertContains('yiiGridView', $response);
        $this->assertContains('file.png', $response);
        $this->assertContains('file.txt', $response);
        $this->assertContains('file.jpg', $response);
    }
}