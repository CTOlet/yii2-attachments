<?php
namespace nemmo\attachments\migrations;

use yii\db\Migration;

/**
 * Class m180327_050508_add_fields_create_date_etc
 */
class m180327_050508_add_fields_create_date_etc extends Migration
{
    use \nemmo\attachments\ModuleTrait;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn($this->getModule()->tableName, 'create_by', $this->integer());
        $this->addColumn($this->getModule()->tableName, 'create_date', $this->timestamp()->defaultValue(null));

        $this->createIndex('file_create_by', $this->getModule()->tableName, 'create_by');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn($this->getModule()->tableName, 'create_by');
        $this->dropColumn($this->getModule()->tableName, 'create_date');
    }
}
