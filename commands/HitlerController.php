<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\modules\hitler\models\HitlerGame;
use app\modules\hitler\models\HitlerPlayer;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HitlerController extends Controller
{
    /**
     * This command welcomes you.
     * @return int Exit code
     */
    public function actionIndex()
    {
        echo 'Welcome!';
        echo "\n";
        return ExitCode::OK;
    }

    /**
     * Create a game
     * @param string $players Number of players
     * @return int Exit code
     */
    public function actionCreate($players = 5)
    {
        // Check if players are less than 5
        if ($players < 5) {
            echo "You must have at least 5 players!\n";
            var_dump($model->errors);
            return ExitCode::USAGE;
        }

        // Create new game
        $model = new HitlerGame();
        $model->players = $players;
        $model->timestamp = (string)time();
        $model->started = "0";
        
        if ($model->save()) {
            // Create player to join this game
            $model2 = new HitlerPlayer();
            $model2->name = 'Jason';
            $model2->fk_game_id = $model->id;
            if($model2->save() == false) {
                echo "Error creating player";
                var_dump($model2->errors);
                return ExitCode::SOFTWARE;
            } else {
                // Set older open games to closed
                HitlerGame::updateAll(['started'=>'1'], "id != {$model->id}");
            }

            echo "Game {$model->id} started with {$players} players\n";
        } else {
            echo "Error creating game\n";
            var_dump($model->errors);
            return ExitCode::SOFTWARE;
        }

        // Everything went ok
        return ExitCode::OK;
    }

    /**
     * Add a player to the most recent game
     * @param string $name Player Name
     * @return int Exit code
     */
    public function actionAdd($name = 'Bob')
    {
        // Check if any available game at the moment
        $gameModel = HitlerGame::find()->where(['started' => '0'])->orderBy('id DESC')->one();
        if ($gameModel === null) {
            echo "Game full or no available game at the moment!\n";
            return ExitCode::SOFTWARE;
        }

        // Get number of players for this game
        $playerCount = (int)$gameModel->getPlayerCount();
        if ($playerCount >= $gameModel->players) {
            echo "Game full or no available game at the moment!\n";
            return ExitCode::SOFTWARE;
        }

        // Create a player and join this game
        $playerModel = new HitlerPlayer();
        $playerModel->setScenario('create');
        $playerModel->name = $name;
        $playerModel->fk_game_id = $gameModel->id;

        if ($playerModel->save()) {
            $playerCount++;
            echo "{$name} added to game {$gameModel->id} ({$playerCount}/{$gameModel->players} players)\n";
        } else {
            echo "Error creating player\n";
            var_dump($playerModel->errors);
            return ExitCode::SOFTWARE;
        }

        // Everything went ok
        return ExitCode::OK;
    }

    /**
     * This will start the game and assign roles
     * @return int Exit code
     */
    public function actionStart()
    {
        // Check if any available game at the moment
        $gameModel = HitlerGame::find()->where(['started' => '0'])->orderBy('id DESC')->one();
        if ($gameModel === null) {
            echo "Game full or no available game at the moment!\n";
            return ExitCode::SOFTWARE;
        }

        // Only allow the game to start if the number of players have been met
        $playerCount = (int)$gameModel->getPlayerCount();
        if ($playerCount < $gameModel->players) {
            echo "Not all players have joined yet!\n";
            return ExitCode::SOFTWARE;
        }

        // Start the game!
        if ($gameModel->startGame() === false) {
            echo "Error creating the game!\n";
            return ExitCode::SOFTWARE;
        }

        // Show player and roles
        echo "Game started successfully\n\n";
        $this->displayPlayerRoles($gameModel->id);
        
        // Everything went ok
        return ExitCode::OK;
    }

    /**
     * Delete a game
     * @param string $gameId Game ID to delete
     * @return int Exit code
     */
    public function actionDelete($gameId = 0)
    {
        HitlerGame::deleteAll('id = :gameId', ['gameId'=>$gameId]);
        HitlerPlayer::deleteAll('fk_game_id = :gameId', ['gameId'=>$gameId]);

        echo "Game {$gameId} and all it's players deleted\n";

        // Everything went ok
        return ExitCode::OK;
    }

    /**
     * Function to display roles
     */
    private function displayPlayerRoles($gameId)
    {
        $players = HitlerPlayer::find()->where(['fk_game_id'=>$gameId])->all();
        foreach($players as $player) {
            echo "{$player->role} \t\t\t\t {$player->name}";
            echo "\n";
        }
    }
}
