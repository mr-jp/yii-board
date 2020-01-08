<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use app\modules\hitler\assets\ModuleAssetBundle;
/* @var $this yii\web\View */

\yii\web\YiiAsset::register($this);
$imageBaseUrl = ModuleAssetBundle::register($this)->baseUrl.'/images';

$this->title = 'Hitler Role Generator';
?>
<div class="site-index">

    <div class="body-content">

        <div class="row">
            <div class="col-lg-6">
                <h1>Game ID: <?= $gameModel->id ?></h1>
                <?php if ($playerModel->team == 'liberals'): ?>
                    <h2 class="text-info">You are a Liberal!</h2>
                    <p><?= Html::img($imageBaseUrl.'/'.$playerModel->team.'.jpg', ['class'=>'role-img']) ?></p>
                    <button class="btn btn-info" data-toggle="collapse" data-target="#role">Click to hide player notes ...</button>                    

                    <div id="role" class="collapse in">
                        <h3>Player Notes ...</h3>
                        <ul>
                            <li>You don't know who your friends are</li>
                            <li>Trust no one</li>
                        </ul>
                    </div>
                
                <?php else: ?>
                    <h2 class="text-danger">You are a Fascist!</h2>
                    <p><?= Html::img($imageBaseUrl.'/'.$playerModel->team.'.jpg', ['class'=>'role-img']) ?></p>
                    <button class="btn btn-danger" data-toggle="collapse" data-target="#role">Click to hide player notes ...</button>             

                    <div id="role" class="collapse in">
                        <h3>Player Notes:</h3>

                        <?php if ($playerModel->role=='hitler'): ?>
                            <p><strong><span class="text-danger">You are Hitler!</span></strong></p>
                            <?php else: ?>
                            <p><strong><span class="text-danger"><?= $hitler ?> is Hitler!</span></strong></p>
                        <?php endif ?>

                        <?php if ($gameModel->players < 7 || $playerModel->role !== 'hitler'): ?>
                            <?php if(!empty($fascists)): ?>
                            <p>The other fascist(s) are:</p>
                            <ul>
                                <?php foreach($fascists as $fascist): ?>
                                    <li><?= $fascist->name ?></li>
                                <?php endforeach ?>
                            </ul>
                            <?php endif ?>
                        <?php endif ?>
                        
                    </div>

                <?php endif ?>
            </div>
        </div>
    </div>
</div>
