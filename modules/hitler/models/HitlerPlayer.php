<?php

namespace app\modules\hitler\models;

use app\helpers\MyHelper;
use Yii;

/**
 * This is the model class for table "hitler_player".
 *
 * @property int $id
 * @property string $name
 * @property string|null $team
 * @property string|null $role
 * @property int $fk_game_id
 */
class HitlerPlayer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hitler_player';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'fk_game_id'], 'required'],
            [['fk_game_id'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['team', 'role'], 'string', 'max' => 50],
            ['name', 'checkDuplicateName', 'on' => 'create']    // only check this on 'create' scenario
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Your Name',
            'team' => 'Team',
            'role' => 'Role',
            'fk_game_id' => 'Game ID',
        ];
    }

    public function checkDuplicateName($attribute, $params)
    {
        $count = HitlerPlayer::find()->where(['name'=>$this->name, 'fk_game_id'=>$this->fk_game_id])->count();
        if ($count > 0) {
            $this->addError('name', "{$this->name} is already in the game!");
        }
    }

    /**
     * Delete all players from this game
     * @param  int $gameId
     */
    public static function deleteFromGame($gameId)
    {
        HitlerPlayer::deleteAll('fk_game_id = :gameId', ['gameId'=>$gameId]);
    }

    /**
     * Get the fascists
     * @param HitlerGame $gameModel
     * @return array
     */
    public function getFascists($gameModel)
    {
        $sql = 'SELECT * FROM `hitler_player` WHERE `fk_game_id` = :gameId AND `id` != :playerId AND `team` = "fascists" AND `role` != "hitler"';
        return HitlerPlayer::findBySql($sql, [':gameId'=>$gameModel->id, ':playerId'=>$this->id])->all();
    }
}
