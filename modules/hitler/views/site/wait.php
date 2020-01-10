<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */

$this->title = 'Hitler Role Generator';
?>
<div class="site-index">

    <div class="body-content">

        <div class="row">
            <div class="col-lg-6">
                <h1>Waiting for other players ...</h1>
                <?php $form = ActiveForm::begin(); ?>
                    <div class="form-group">
                        <h2>Game ID: <?= $gameModel->id ?></h2>
                        
                        <?php if ($gameCreator): ?>
                            <?php if ($remainingPlayers !== 0): ?>
                                <p>Waiting for <?= $gameModel->getRemainingPlayers() ?> more player(s)</p>
                            <?php else: ?>
                                <p>Game is ready to start ...</p>
                            <?php endif ?>
                            <?= 
                                Html::submitButton('Start Game', [
                                'name'=>'startGame',
                                'class' => 'btn btn-success',
                                // 'disabled' => $remainingPlayers !== 0
                            ]);
                            ?>
                            <?= Html::submitButton('Cancel Game', ['name'=>'cancelGame', 'class' => 'btn btn-danger']) ?>
                        <?php else: ?>
                            <?php if ($remainingPlayers !== 0): ?>
                                <p>Waiting for <?= $gameModel->getRemainingPlayers() ?> more player(s)</p>
                            <?php else: ?>
                                <p>Please tell the game creator to start the game ...</p>
                            <?php endif ?>
                            <?= Html::submitButton('Join Game', ['name'=>'joinGame', 'class' => 'btn btn-success']) ?>
                            <?= Html::submitButton('Quit Game', ['name'=>'quitGame', 'class' => 'btn btn-danger']) ?>
                        <?php endif ?>

                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
