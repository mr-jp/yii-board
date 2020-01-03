<?php

namespace app\modules\avalon;

/**
 * avalon module definition class
 */
class Module extends \yii\base\Module
{
    private $_assetsUrl;

    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\avalon\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public function getAssetsUrl()
    {
        if ($this->_assetsUrl === null)
            $this->_assetsUrl = Yii::$app()->getAssetManager()->publish(__DIR__ . '/assets', false, -1, true);
        return $this->_assetsUrl;
    }
}
