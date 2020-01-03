<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
/* @var $this yii\web\View */

$this->title = 'Avalon Role Generator';
?>
<div class="site-index">

    <div class="body-content">

        <div class="row">
            <div class="col-lg-6">
                <h2>Game List</h2>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '{items}',
                    'columns' => [
                        [
                            'attribute'=>'timestamp',
                            'label' => 'Timestamp',
                            'content'=>function($data){
                                return date("Ydm - H:i", $data->timestamp);
                            }
                        ],
                        [
                            'attribute'=>'started',
                            'label' => 'Status',
                            'content'=>function($data){
                                return $data->started == 0 ? 'Open' : 'Closed';
                            }
                        ],
                        // ['class' => 'yii\grid\ActionColumn','template' => '{view} {delete}'],
                        ['class' => 'yii\grid\ActionColumn','template' => '{view} {delete}'],
                    ],
                ]); ?>
            </div>
        </div>

        <?php if(!Yii::$app->user->isGuest): ?>
        <div class="row">
            <div class="col-lg-6">
                <h2>Create New Game</h2>

                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'players')->dropDownList([
                    '5' => 5,
                    '6' => 6,
                    '7' => 7,
                    '8' => 8,
                    '9' => 9,
                    '10' => 10
                ]) ?>
                <?= $form->field($model, 'percival')->checkbox(['uncheck' => 0, 'value' => 1]); ?>
                <?= $form->field($model, 'mordred')->checkbox(['uncheck' => 0, 'value' => 1]); ?>
                <?= $form->field($model, 'morgana')->checkbox(['uncheck' => 0, 'value' => 1]); ?>
                <?= $form->field($model, 'oberon')->checkbox(['uncheck' => 0, 'value' => 1]); ?>

                <?php echo $form->field($model, 'timestamp')->hiddenInput(['value'=> time()])->label(false); ?>
                <?php echo $form->field($model, 'started')->hiddenInput(['value'=> 0])->label(false); ?>

                <div class="form-group">
                    <?= Html::submitButton('New Game', ['class' => 'btn btn-success']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <?php endif ?>
    </div>
</div>
