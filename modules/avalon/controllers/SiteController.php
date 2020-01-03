<?php

namespace app\modules\avalon\controllers;

use Yii;
use app\modules\avalon\models\Game;
use app\modules\avalon\models\Player;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\modules\avalon\models\ContactForm;
use app\modules\avalon\helpers\RoleHelper;

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
        $this->layout = '@app/modules/avalon/views/layouts/main.php';
        return parent::beforeAction($action);
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $playerModel = new Player();
        $gameModel = Game::find()->where(['started' => '0'])->orderBy('id DESC')->one();

        // don't display anything if no game at the moment
        if ($gameModel === null) {
            return $this->render('index', [
                'gameAvailable' => false,
            ]);
        }

        $playerCount = (int)$gameModel->getPlayerCount();
        $gameAvailable = $this->checkAvailableGame($gameModel, $playerCount);

        if ($playerModel->load(Yii::$app->request->post())) {

            // check if max number of players reached
            if ($playerCount >= $gameModel->players) {
                return $this->redirect(['index']);
            }

            // redirect if player saves
            if ($playerModel->save()) {
                return $this->redirect(['wait', 'id' => $playerModel->id]);
            }
        }

        return $this->render('index', [
            'gameModel' => $gameModel,
            'playerModel' => $playerModel,
            'playerCount' => $playerCount,
            'gameAvailable' => $gameAvailable,
        ]);
    }

    /**
     * Waiting page
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionWait($id)
    {
        $model = $this->findPlayerModel($id);
        $gameModel = Game::find()->where(['id'=>$model->fk_game_id])->one();

        if (Yii::$app->request->post()) {
            if ($gameModel->isReady() === false) {
                $model->addError('name', 'Game is not yet ready!');
            } else {
                // save player id into session (so user cannot enter role id in URL in the role page)
                Yii::$app->session['playerId'] = $model->id;
                return $this->redirect(['role', 'id' => $model->fk_game_id]);
            }


        }

        return $this->render('@app/modules/avalon/views/common/wait.php', [
            'model' => $model,
            'gameModel' => $gameModel
        ]);
    }

    /**
     * Role page
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionRole($id)
    {
        if (isset($_GET['playerId'])) {
            throw new NotFoundHttpException('Nice try!');
        }

        $playerId = Yii::$app->session['playerId'];
        $gameModel = $this->findGameModel($id);
        $playerModel = $this->findPlayerModel($playerId);
        extract(RoleHelper::findRoles($gameModel));

        return $this->render('@app/modules/avalon/views/common/role.php', [
            'gameModel' => $gameModel,
            'playerModel' => $playerModel,
            'minions' => $minions,
            'servants' => $servants,
            'merlin' => $merlin,
            'morgana' => $morgana,
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
     * @return Game the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findGameModel($id)
    {
        if (($model = Game::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Finds the Player model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Player the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findPlayerModel($id)
    {
        if (($model = Player::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Check if game is available
     * @param  Game $gameModel
     * @param  int $playerCount
     * @return boolean
     */
    protected function checkAvailableGame($gameModel, $playerCount) {
        if ($gameModel === null) {
            return false;
        }

        if ($playerCount >= $gameModel->players) {
            return false;
        }

        return true;
    }
}
