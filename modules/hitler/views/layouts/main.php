<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

$title = 'Hitler Role Generator';

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title>Hitler Role Generator</title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => $title,
        'brandUrl' => Yii::$app->user->isGuest ? Yii::$app->homeUrl : Yii::$app->urlManager->createUrl("/hitler/admin/index"),
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => [
            Yii::$app->session['gameStarted'] ? (
                ['label' => 'New Game', 'url' => ['/hitler/site/quit']]
            ) : (
                ['label' => 'Home', 'url' => ['/hitler/site/index']]
            )
        ]
    ]);
    // echo Nav::widget([
    //     'options' => ['class' => 'navbar-nav navbar-right'],
    //     'items' => [

    //         Yii::$app->user->isGuest ? (
    //             ['label' => 'New Game', 'url' => ['/hitler/site/index']]
    //         ) : (
    //             ['label' => 'Admin', 'url' => ['/hitler/admin/index']]
    //         ),
    //         Yii::$app->user->isGuest ? (
    //             ['label' => 'Login', 'url' => ['/hitler/site/login']]
    //         ) : (
    //             '<li>'
    //             . Html::beginForm(['/hitler/site/logout'], 'post')
    //             . Html::submitButton(
    //                 'Logout (' . Yii::$app->user->identity->username . ')',
    //                 ['class' => 'btn btn-link logout']
    //             )
    //             . Html::endForm()
    //             . '</li>'
    //         )
    //     ],
    // ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
