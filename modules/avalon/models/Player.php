<?php

namespace app\modules\avalon\models;

use Yii;

/**
 * This is the model class for table "player".
 *
 * @property int $id
 * @property string $name
 * @property string $team
 * @property string $role
 * @property int $fk_game_id
 */
class Player extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'player';
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
            'name' => 'Your name',
            'team' => 'Team',
            'role' => 'Role',
            'fk_game_id' => 'Fk Game ID',
        ];
    }

    public function checkDuplicateName($attribute, $params)
    {
        $count = Player::find()->where(['name'=>$this->name, 'fk_game_id'=>$this->fk_game_id])->count();
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
        Player::deleteAll('fk_game_id = :gameId', ['gameId'=>$gameId]);
    }
}
