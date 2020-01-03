<?php

namespace app\modules\hitler\controllers;

use yii\web\Controller;

/**
 * Default controller for the `hitler` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $this->layout = '@app/modules/hitler/views/layouts/main.php';
        return parent::beforeAction($action);
    }
}
