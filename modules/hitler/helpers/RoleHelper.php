<?php

namespace app\modules\hitler\helpers;

use app\modules\hitler\models\HitlerPlayer;
use app\modules\hitler\models\HitlerGame;

class RoleHelper
{
    /**
     * Assign roles to the game model
     * @param  HitlerGame $gameModel
     * @return array
     */
    public static function assign($gameModel)
    {
        $players = [];
        $playerModel = HitlerPlayer::find()->where(['fk_game_id' => $gameModel->id])->all();
        $players = array_map(function($value) {
            return $value->name;
        }, $playerModel);

        // randomize player order
        shuffle($players);

        // assign the fascists
        $fascists = [];
        $numberOfPlayers = sizeof($players);
        $numberOfFascists = $gameModel->fascistRules[$numberOfPlayers];
        for($i = 0; $i < $numberOfFascists; $i++) {
            $fascists[] = array_pop($players);
        }

        // assign the liberals (remaining players are liberals)
        $liberals = $players;

        // assign hitler
        $assignedPlayers = [];
        $assignedPlayers[] = [
            'name' => array_pop($fascists),
            'team' => 'fascists',
            'role' => 'hitler'
        ];

        // assign remaining fascists
        for ($i = 0; $i < sizeof($fascists); $i++) {
            $assignedPlayers[] = [
                'name' => $fascists[$i],
                'team' => 'fascists',
                'role' => 'fascist'
            ];
        }

        // assign remaining liberals
        for ($i = 0; $i < sizeof($liberals); $i++) {
            $assignedPlayers[] = [
                'name' => $liberals[$i],
                'team' => 'liberals',
                'role' => 'liberal'
            ];
        }

        return $assignedPlayers;
    }
}
