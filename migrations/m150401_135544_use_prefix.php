<?php

use yii\db\Migration;

class m150401_135544_use_prefix extends Migration
{
    public function up()
    {
        $this->renameTable('attach_file','{{%attach_file}}');
    }

    public function down()
    {
        $this->renameTable('{{%attach_file}}','attach_file');
    }
}
