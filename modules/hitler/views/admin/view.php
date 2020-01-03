<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Game */

$this->title = 'Game: '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Games', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="game-view">
    <h1><?= date("Ydm - H:i", $model->timestamp) ?></h1>
    <h2>Players</h2>
    <ol>
        <?php foreach ($players as $player): ?>
            <li>
                <a href="<?= Yii::$app->urlManager->createUrl(['/avalon/admin/role','id'=>$model->id, 'playerId'=>$player->id]) ?>">
                    <?= ucfirst($player->name) ?> - <?= ucfirst($player->role) ?>
                </a>
            </li>
        <?php endforeach ?>
    </ol>
</div>
