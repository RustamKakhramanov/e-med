<?php
/* @var $this \yii\web\View */

/* @var $content string */

use yii\helpers\Html;
use app\assets\AppAsset;
use yii\widgets\MaskedInputAsset;

AppAsset::register($this);
AppAsset::overrideSystemConfirm();
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body<?php if (isset($this->params['bodyClass'])) { ?> class="<?= $this->params['bodyClass']; ?>"<?php } ?>>
    <?php $this->beginBody() ?>
    <div id="wrapper">
        <div id="sidebar-wrapper">
            <?=
            $this->render(
                '@app/views/elem/sidebar.php'
            )
            ?>
        </div>

        <div id="page-content-wrapper">
            <div class="container-fluid">
                <?= $content ?>
            </div>
        </div>
    </div>
    <?php
    echo $this->render('@app/views/elem/prolong.php');
    echo $this->render('@app/views/elem/activity.php');
    echo $this->render('@app/views/elem/_flash.php');
    ?>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>