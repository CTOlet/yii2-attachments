<?php
/**
 * Created by PhpStorm.
 * User: Alimzhan
 * Date: 2/2/2016
 * Time: 9:45 PM
 */

namespace tests;

use nemmo\attachments\models\File;

class FileTest extends TestCase
{
    public function testValidate()
    {
        $file = new File();

        $this->assertFalse($file->validate());
        $this->assertArrayHasKey('name', $file->errors);
        $this->assertArrayHasKey('model', $file->errors);
        $this->assertArrayHasKey('itemId', $file->errors);
        $this->assertArrayHasKey('hash', $file->errors);
        $this->assertArrayHasKey('size', $file->errors);
        $this->assertArrayHasKey('type', $file->errors);
        $this->assertArrayHasKey('mime', $file->errors);
    }
}
