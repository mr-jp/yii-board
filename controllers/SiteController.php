<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

class SiteController extends Controller
{

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        // redirect to avalon module
        // @todo check URL and redirect accordingly
        // $this->redirect(['/avalon/site/index']);
        if (strstr($_SERVER['HTTP_HOST'], "avalon")) {
            $this->redirect(['/avalon/site/index']);            
        } elseif (strstr($_SERVER['HTTP_HOST'], "hitler")) {
            $this->redirect(['/hitler/site/index']);
        } else {
            throw new \Exception("What are you even doing here?");
        }
    }
}
