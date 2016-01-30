<?php

namespace nemmo\attachments;

/**
 * Created by PhpStorm.
 * User: Алимжан
 * Date: 27.01.2015
 * Time: 12:32
 */

trait ModuleTrait
{
    /**
     * @var null|Module
     */
    private $_module = null;

    /**
     * @return null|Module
     * @throws \Exception
     */
    protected function getModule()
    {
        if ($this->_module == null) {
            $this->_module = \Yii::$app->getModule('attachments');
        }

        if (!$this->_module) {
            throw new \Exception("Yii2 attachment module not found, may be you didn't add it to your config?");
        }

        return $this->_module;
    }
}