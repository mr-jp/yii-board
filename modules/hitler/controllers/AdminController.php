<?php

namespace app\modules\hitler\controllers;

use Yii;
use app\modules\hitler\models\HitlerGame;
use app\modules\hitler\models\HitlerPlayer;
use yii\data\ActiveDataProvider;
use yii\data\Sort;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
// use app\modules\hitler\helpers\RoleHelper;

class AdminController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'view', 'wait', 'role', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'wait', 'role', 'delete'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $this->layout = '@app/modules/hitler/views/layouts/main.php';
        return parent::beforeAction($action);
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new HitlerGame();

        $dataProvider = new ActiveDataProvider([
            'query' => HitlerGame::find(),
            'sort'=> ['defaultOrder' => ['timestamp'=>SORT_DESC]]
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            // Create player to join this game
            $model2 = new HitlerPlayer();
            $model2->name = 'Jason';
            $model2->fk_game_id = $model->id;
            if($model2->save() == false) {
                $message='Error creating new player ...';
                throw new HttpException(403, $message);
            }

            // Set older open games to closed
            HitlerGame::updateAll(['started'=>'1'], "id != {$model->id}");

            return $this->redirect(['wait', 'id' => $model->id, 'playerId' => $model2->id]);
        }

        return $this->render('index', [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single HitlerGame model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {}

    /**
     * Waiting page
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionWait($id, $playerId)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->post()) {
            // start game
            if ($model->startGame()) {
                // redirect to role page
                return $this->redirect(['role', 'id' => $model->id, 'playerId' => $playerId]);
            }

        }

        return $this->render('@app/modules/hitler/views/common/wait.php', [
            'model' => $model,
            'gameModel' => $model
        ]);
    }

    /**
     * Role page
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionRole($id, $playerId)
    {}

    /**
     * Deletes an existing HitlerGame model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {}

    /**
     * Finds the HitlerGame model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return HitlerGame the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = HitlerGame::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the Plaer model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Player the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findPlayerModel($id)
    {}
}
