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
        // $this->clearSession();
        $this->checkSession();
        
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
                    Yii::$app->session['gameCreator'] = true;
                    Yii::$app->session['gameId'] = $gameModel->id;
                    Yii::$app->session['playerId'] = $playerModel->id;

                    // redirect to wait page
                    return $this->redirect(['wait',]);
                }
            }
        }

        // join game
        if ($playerModel->load(Yii::$app->request->post())) {
            $playerModel->setScenario('create');
            if ($playerModel->save()) {
                // save session for game joiner
                    Yii::$app->session['gameCreator'] = false;
                    Yii::$app->session['gameId'] = $playerModel->fk_game_id;
                    Yii::$app->session['playerId'] = $playerModel->id;

                return $this->redirect(['wait']);
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
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionWait()
    {
        $gameId = Yii::$app->session['gameId'];
        $playerId = Yii::$app->session['playerId'];
        $gameCreator = Yii::$app->session['gameCreator'];

        $gameModel = $this->findGameModel($gameId);
        if ($gameModel == null) {
            // game no longer exists
            Yii::$app->session->setFlash('error', 'Game no longer exists!');
            return $this->redirect(['index']);
        }
        $playerModel = $this->findplayerModel($playerId);
        if ($playerModel == null) {
            // game no longer exists
            Yii::$app->session->setFlash('error', 'Game no longer exists!');
            $this->redirect(['index']);
        }

        $remainingPlayers = $gameModel->getRemainingPlayers();

        // Start Game
        if (Yii::$app->request->post('startGame') !== null) {
            if ($gameModel->startGame()) {
                Yii::$app->session['gameId'] = $gameModel->id;
                Yii::$app->session['playerId'] = $playerModel->id;
                Yii::$app->session['gameStarted'] = true;
                return $this->redirect(['role']);
            } else {
                // display errors here
                Yii::$app->session->setFlash('error', 'Not all players have joined yet!');
            }
        }

        // Join Game
        if (Yii::$app->request->post('joinGame') !== null) {
            if ($gameModel->started == '1') {
                Yii::$app->session['gameId'] = $gameModel->id;
                Yii::$app->session['playerId'] = $playerModel->id;
                Yii::$app->session['gameStarted'] = true;
                return $this->redirect(['role']);
            } else {
                Yii::$app->session->setFlash('error', 'Game is not ready yet!');
            }
        }

        // Delete Game
        if (Yii::$app->request->post('cancelGame') !== null) {
            $gameModel->cancelGame();
            $this->clearSession();
            Yii::$app->session->setFlash('info', 'Game cancelled ...');
            return $this->redirect(['index']);
        }

        // Exit game
        if (Yii::$app->request->post('quitGame') !== null) {
            $playerModel->quitGame();
            $this->clearSession();
            Yii::$app->session->setFlash('info', 'Quit game ...');
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
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionRole()
    {
        $gameId = Yii::$app->session['gameId'];
        $playerId = Yii::$app->session['playerId'];

        $gameModel = $this->findGameModel($gameId);
        if ($gameModel == null) {
            // game no longer exists
            Yii::$app->session->setFlash('error', 'Game no longer exists!');
            return $this->redirect(['index']);
        }
        $playerModel = $this->findplayerModel($playerId);
        if ($playerModel == null) {
            // game no longer exists
            Yii::$app->session->setFlash('error', 'Game no longer exists!');
            $this->redirect(['index']);
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
     * New Game action
     */
    public function actionQuit()
    {
        $this->clearSession();
        return $this->redirect(['index']);
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

    private function clearSession()
    {
        Yii::$app->session['gameCreator'] = '';
        Yii::$app->session['gameId'] = '';
        Yii::$app->session['playerId'] = '';
        Yii::$app->session['gameStarted'] = '';
    }

    /**
     * If session exists, redirect to either role or wait action
     */
    private function checkSession()
    {
        if(Yii::$app->session['gameId'] && Yii::$app->session['playerId']) {
            return $this->redirect([
                Yii::$app->session['gameStarted'] ? 'role' : 'wait', 
                'gameId'=>Yii::$app->session['gameId'],
                'playerId'=>Yii::$app->session['playerId']
                ]
            );
        }
    }
}
