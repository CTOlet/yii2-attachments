<?php

use yii\db\Migration;
use yii\db\Schema;

class m150127_040544_add_attachments extends Migration
{
    public function up()
    {
        $this->createTable('app_attachment', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' not null',
            'model' => Schema::TYPE_STRING . ' not null',
            'item_id' => Schema::TYPE_INTEGER . ' not null',
            'hash' => Schema::TYPE_STRING . ' not null',
            'size' => Schema::TYPE_INTEGER . ' not null',
            'type' => Schema::TYPE_STRING . ' not null',
            'mime' => Schema::TYPE_STRING . ' not null'
        ]);

        $this->createIndex('file_model', 'app_attachment', 'model');
        $this->createIndex('file_item_id', 'app_attachment', 'item_id');
    }

    public function down()
    {
        $this->dropTable('app_attachment');
    }
}
