<?php

use yii\db\Migration;
use yii\db\Schema;

class m150127_040544_add_attachments extends Migration
{
    public function up()
    {
        $this->createTable('attach_file', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' not null',
            'model' => Schema::TYPE_STRING . ' not null',
            'itemId' => Schema::TYPE_INTEGER . ' not null',
            'hash' => Schema::TYPE_STRING . ' not null',
            'size' => Schema::TYPE_INTEGER . ' not null',
            'type' => Schema::TYPE_STRING . ' not null'
        ]);

        $this->createIndex('file_model', 'attach_file', 'model');
        $this->createIndex('file_item_id', 'attach_file', 'itemId');
    }

    public function down()
    {
        $this->dropTable('attach_file');
    }
}
