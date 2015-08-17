<?php

use yii\db\Migration;
use yii\db\Schema;

class m150127_040544_add_attachments extends Migration
{
    use \nemmo\attachments\ModuleTrait;

    public function up()
    {
        $this->createTable($this->getModule()->tableName, [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' not null',
            'model' => Schema::TYPE_STRING . ' not null',
            'itemId' => Schema::TYPE_INTEGER . ' not null',
            'hash' => Schema::TYPE_STRING . ' not null',
            'size' => Schema::TYPE_INTEGER . ' not null',
            'type' => Schema::TYPE_STRING . ' not null',
            'mime' => Schema::TYPE_STRING . ' not null'
        ]);

        $this->createIndex('file_model', $this->getModule()->tableName, 'model');
        $this->createIndex('file_item_id', $this->getModule()->tableName, 'itemId');
    }

    public function down()
    {
        $this->dropTable($this->getModule()->tableName);
    }
}
