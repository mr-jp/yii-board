<?php

namespace app\modules\avalon\helpers;

use app\modules\avalon\models\Player;

class RoleHelper
{
    /**
     * Assign roles to the game model
     * @param  Game $gameModel
     * @return array
     */
    public static function assign($gameModel)
    {
        $players = [];
        $playerModel = Player::find()->where(['fk_game_id' => $gameModel->id])->all();
        $players = array_map(function($value) {
            return $value->name;
        }, $playerModel);

        // randomize player order
        shuffle($players);

        // assign the minions
        $minions = [];
        $numberOfPlayers = sizeof($players);
        $numberOfMinions = $gameModel->minionRules[$numberOfPlayers];
        for($i = 0; $i < $numberOfMinions; $i++) {
            $minions[] = array_pop($players);
        }

        // assign the servants (remaining players are servants)
        $servants = $players;

        // do a check to make sure special characters don't outnumber the minion
        $specialMinionCount = 0;
        if ($gameModel->mordred == '1') $specialMinionCount++;
        if ($gameModel->morgana == '1') $specialMinionCount++;
        if ($gameModel->mordred == '1') $specialMinionCount++;
        if ($specialMinionCount > $numberOfMinions) {
            throw new \Exception('Number of special characters out number minion count!');
        }

        // assign assassin
        $assignedPlayers[] = [
            'name' => array_pop($minions),
            'team' => 'minions',
            'role' => 'assassin'
        ];

        if ($gameModel->mordred == '1') {
            $assignedPlayers[] = [
                'name' => array_pop($minions),
                'team' => 'minions',
                'role' => 'mordred'
            ];
        }

        if ($gameModel->morgana == '1') {
            $assignedPlayers[] = [
                'name' => array_pop($minions),
                'team' => 'minions',
                'role' => 'morgana'
            ];
        }

        if ($gameModel->oberon == '1') {
            $assignedPlayers[] = [
                'name' => array_pop($minions),
                'team' => 'minions',
                'role' => 'oberon'
            ];
        }

        // assign remaining minions
        for ($i = 0; $i < sizeof($minions); $i++) {
            $assignedPlayers[] = [
                'name' => $minions[$i],
                'team' => 'minions',
                'role' => 'minion'
            ];
        }

        // assign merlin
        $assignedPlayers[] = [
            'name' => array_pop($servants),
            'team' => 'servants',
            'role' => 'merlin'
        ];

        if ($gameModel->percival == '1') {
            $assignedPlayers[] = [
                'name' => array_pop($servants),
                'team' => 'servants',
                'role' => 'percival'
            ];
        }

        // assign remaining servants
        for ($i = 0; $i < sizeof($servants); $i++) {
            $assignedPlayers[] = [
                'name' => $servants[$i],
                'team' => 'servants',
                'role' => 'servant'
            ];
        }

        return $assignedPlayers;
    }

    /**
     * Return roles for a game
     * @param  Game $gameModel
     * @return array
     */
    public static function findRoles($gameModel)
    {
        $minions = Player::find()->where(['fk_game_id'=>$gameModel->id, 'team'=>'minions'])->all();
        $servants = Player::find()->where(['fk_game_id'=>$gameModel->id, 'team'=>'servants'])->all();
        $merlin = Player::find()->where(['fk_game_id'=>$gameModel->id, 'role'=>'merlin'])->one();
        $percival = Player::find()->where(['fk_game_id'=>$gameModel->id, 'role'=>'percival'])->one();
        $mordred = Player::find()->where(['fk_game_id'=>$gameModel->id, 'role'=>'mordred'])->one();
        $morgana = Player::find()->where(['fk_game_id'=>$gameModel->id, 'role'=>'morgana'])->one();
        $oberon = Player::find()->where(['fk_game_id'=>$gameModel->id, 'role'=>'oberon'])->one();
        return compact('minions', 'servants', 'merlin', 'percival', 'mordred', 'morgana', 'oberon');
    }
}
