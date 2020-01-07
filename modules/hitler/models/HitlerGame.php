<?php

namespace app\modules\hitler\models;

use Yii;
use app\modules\hitler\models\HitlerPlayer;
use app\modules\hitler\helpers\RoleHelper;

/**
 * This is the model class for table "hitler_game".
 *
 * @property int $id
 * @property string $timestamp
 * @property string $started
 * @property int $players
 */
class HitlerGame extends \yii\db\ActiveRecord
{
    // Number of players determine the number of minion
    public $fascistRules = [
        5 => 2,
        6 => 2,
        7 => 3,
        8 => 3,
        9 => 4,
        10 => 4
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hitler_game';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['timestamp', 'players'], 'required'],
            [['started'], 'string'],
            [['players'], 'integer'],
            [['timestamp'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'timestamp' => 'Timestamp',
            'started' => 'Started',
            'players' => 'Number of Players',
        ];
    }

    /**
     * Get number of players joined
     * @return int
     */
    public function getPlayerCount()
    {
        return $playerCount = HitlerPlayer::find()->where(['fk_game_id' => $this->id])->count();
    }

    /**
     * Check if all players have joined
     * @return boolean True if all players have joined
     */
    private function checkNumberOfPlayers()
    {
        $playerCount = $this->getPlayerCount();

        if ($playerCount < $this->players) {
            $this->addError('players', 'Not all players have joined yet!');
        }

        return $playerCount == $this->players;
    }

    /**
     * Start the game!
     * @return [type] [description]
     */
    public function startGame()
    {
        // do a rule check
        if($this->validate() && $this->checkNumberOfPlayers()) {
            $this->assignRoles();
            $this->started = '1';
            $this->save();
            return true;
        }

        return false;
    }

    /**
     * Cancel the game
     */
    public function cancelGame()
    {
        // Delete all players in this game
        HitlerPlayer::deleteFromGame($this->id);

        // Delete the current game
        HitlerGame::delete(['id'=>$this->id]);
    }

    /**
     * Assign roles and save to database
     */
    private function assignRoles()
    {
        $roles = RoleHelper::assign($this);

        // save role and team in database for everyone
        foreach($roles as $role) {
            $player = HitlerPlayer::find()->where(['name'=>$role['name'], 'fk_game_id'=>$this->id])->one();
            $player->team = $role['team'];
            $player->role = $role['role'];
            if ($player->save() == false) {
                throw new NotFoundHttpException('Cannot save player roles ...');
            }
        }
    }

    /**
     * Get remaining players
     * @return string
     */
    public function getRemainingPlayers()
    {
        return $this->players - HitlerPlayer::find()->where(['fk_game_id'=>$this->id])->count();
    }
}
