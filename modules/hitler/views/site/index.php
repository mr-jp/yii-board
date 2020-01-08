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
                <h1>Join Game</h1>
                <?php if(empty($games)): ?>
                    <p>No open games ...</p>
                <?php else:?>
                    <?php $joinForm = ActiveForm::begin(); ?>
                        <?= $joinForm->field($playerModel, 'name')->textInput([
                            'tabindex'=>'1',
                            'autofocus'=>'1'
                            ]); ?>
                        <?= $joinForm->field($playerModel, 'fk_game_id')->dropDownList(ArrayHelper::map($games, 'id', 'id')) ?>

                    <div class="form-group">
                        <?= Html::submitButton('Join Game', ['class' => 'btn btn-success', 'tabindex'=>'2']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>

                <?php endif ?>
            </div>
            <div class="col-lg-6">
                <h1>Create Game</h1>
                <?php $createForm = ActiveForm::begin(); ?>
                <?= $createForm->field($gameModelPlayer, 'name')->label('Your Name'); ?>
                <?= $createForm->field($gameModel, 'players')->dropDownList([
                    '5' => 5,
                    '6' => 6,
                    '7' => 7,
                    '8' => 8,
                    '9' => 9,
                    '10' => 10
                ]) ?>
                <?php echo $createForm->field($gameModel, 'timestamp')->hiddenInput(['value'=> time()])->label(false); ?>
                <?php echo $createForm->field($gameModel, 'started')->hiddenInput(['value'=> 0])->label(false); ?>

                <div class="form-group">
                    <?= Html::submitButton('New Game', ['class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>

        <div class="row">
        </div>

    </div>
</div>
