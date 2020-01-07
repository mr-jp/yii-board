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
                <h1>Game ID: <?= $gameModel->id ?></h1>
                <?php if ($playerModel->team == 'liberals'): ?>
                    <h2 class="text-info">You are a Liberal!</h2>
                    <p>Liberalism is a political and moral philosophy based on liberty, consent of the governed and equality before the law. Liberals espouse a wide array of views depending on their understanding of these principles, but they generally support limited government, individual rights, capitalism, democracy, secularism, gender equality, racial equality, internationalism, freedom of speech, freedom of the press and freedom of religion. Yellow is the political colour most commonly associated with liberalism. Liberalism became a distinct movement in the Age of Enlightenment, when it became popular among Western philosophers and economists. Liberalism sought to replace the norms of hereditary privilege, state religion, absolute monarchy, the divine right of kings and traditional conservatism with representative democracy and the rule of law. Liberals also ended mercantilist policies, royal monopolies and other barriers to trade, instead promoting free trade and free markets.</p>
                
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
                    <p>Fascism is a form of far-right, authoritarian ultranationalism characterized by dictatorial power, forcible suppression of opposition, and strong regimentation of society and of the economy which came to prominence in early 20th-century Europe.</p>
                    
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
