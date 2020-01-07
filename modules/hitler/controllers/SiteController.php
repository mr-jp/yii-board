<?php

namespace app\modules\hitler\controllers;

use Yii;
use app\modules\hitler\models\HitlerGame;
use app\modules\hitler\models\HitlerPlayer;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\modules\hitler\helpers\RoleHelper;
use app\helpers\MyHelper;

class SiteController extends Controller
{
    
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
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
        $gameModel = new HitlerGame();
        $playerModel = new HitlerPlayer();
        $games = HitlerGame::find()->where(['started'=>'0'])->orderBy(['timestamp'=>SORT_DESC])->all();

        // temporary model for player name to create game
        $gameModelPlayer = new \yii\base\DynamicModel(['name']);
        $gameModelPlayer->addRule(['name'], 'string', ['max' => 100]);
        $gameModelPlayer->addRule(['name'], 'required');

        // create game
        if ($gameModel->load(Yii::$app->request->post()) && $gameModel->save()) {
            if ($gameModelPlayer->load(Yii::$app->request->post()) && $gameModelPlayer->validate()) {
                // Create player to join this game
                $playerModel->name = $gameModelPlayer->name;
                $playerModel->fk_game_id = $gameModel->id;

                if ($playerModel->save()) {
                    // save session for game creator
                    Yii::$app->session['gameCreator'] = $gameModel->id;

                    // redirect to wait page
                    return $this->redirect(['wait', 'gameId' => $gameModel->id, 'playerId' => $playerModel->id]);
                }
            }
        } else {
            // Clear game creator session
            Yii::$app->session['gameCreator'] = '';
        }

        // join game
        if ($playerModel->load(Yii::$app->request->post())) {
            $playerModel->setScenario('create');
            if ($playerModel->save()) {
                return $this->redirect(['wait', 'gameId' => $playerModel->fk_game_id, 'playerId' => $playerModel->id]);
            }
        }

        // render
        return $this->render('index', [
            'gameModel' => $gameModel,
            'playerModel' => $playerModel,
            'gameModelPlayer' => $gameModelPlayer,
            'games' => $games,
        ]);
    }

    /**
     * Waiting page
     * @param integer $gameId
     * @param integer $playerId
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionWait($gameId, $playerId)
    {
        $gameModel = $this->findGameModel($gameId);
        if ($gameModel == null) {
            // game no longer exists
            Yii::$app->session->setFlash('error', 'Game no longer exists!');
            return $this->redirect(['index', ['gameId'=>$gameId]]);
        }
        $playerModel = $this->findplayerModel($playerId);
        if ($playerModel == null) {
            // game no longer exists
            Yii::$app->session->setFlash('error', 'Game no longer exists!');
            $this->redirect(['index', ['playerId'=>$playerId]]);
        }
        $remainingPlayers = $gameModel->getRemainingPlayers();
        $gameCreator = Yii::$app->session['gameCreator'];

        // Start Game
        if (Yii::$app->request->post('startGame') !== null) {
            if ($gameModel->startGame()) {
                return $this->redirect(['role', 'gameId' => $gameModel->id, 'playerId' => $playerModel->id]);
            } else {
                // display errors here
                Yii::$app->session->setFlash('error', 'Not all players have joined yet!');
            }
        }

        // Join Game
        if (Yii::$app->request->post('joinGame') !== null) {
            if ($gameModel->started == '1') {
                return $this->redirect(['role', 'gameId' => $gameModel->id, 'playerId' => $playerModel->id]);
            } else {
                Yii::$app->session->setFlash('error', 'Game is not ready yet!');
            }
        }

        // Delete Game
        if (Yii::$app->request->post('cancelGame') !== null) {
            $gameModel->cancelGame();
            Yii::$app->session->setFlash('info', 'Game cancelled ...');
            return $this->redirect(['index']);
        }

        return $this->render('wait', [
            'gameModel' => $gameModel,
            'playerModel' => $playerModel,
            'remainingPlayers' => $remainingPlayers,
            'gameCreator' => $gameCreator,
        ]);
    }

    /**
     * Role page
     * @param integer $gameId
     * @param integer $playerId
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionRole($gameId, $playerId)
    {
        $gameModel = $this->findGameModel($gameId);
        if ($gameModel == null) {
            // game no longer exists
            Yii::$app->session->setFlash('error', 'Game no longer exists!');
            return $this->redirect(['index', ['gameId'=>$gameId]]);
        }
        $playerModel = $this->findplayerModel($playerId);
        if ($playerModel == null) {
            // game no longer exists
            Yii::$app->session->setFlash('error', 'Game no longer exists!');
            $this->redirect(['index', ['playerId'=>$playerId]]);
        }

        $fascists = $playerModel->getFascists($gameModel);
        $hitler = HitlerPlayer::find()->where(['role'=>'hitler', 'fk_game_id'=>$gameId])->one()->name;

        return $this->render('role', [
            'gameModel' => $gameModel,
            'playerModel' => $playerModel,
            'fascists' => $fascists,
            'hitler' => $hitler,
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['admin/index']);
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Finds the Game model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return HitlerGame the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findGameModel($id)
    {        
        return $model = HitlerGame::findOne($id);
    }

    /**
     * Finds the Player model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return HitlerPlayer the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findPlayerModel($id)
    {
        return $model = HitlerPlayer::findOne($id);
    }
}
