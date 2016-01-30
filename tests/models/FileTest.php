<?php

namespace tests\models;

use nemmo\attachments\models\File;

class FileTest extends \PHPUnit_Framework_TestCase
{
    public function testMe()
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