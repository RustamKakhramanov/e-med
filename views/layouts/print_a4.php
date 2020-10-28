<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\assets\AppAsset;

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
        <style>
            html, body {
                width: 210mm;
                height: 297mm;
            }
            @page { margin: 0; }
            body {
                padding: 10mm 10mm 10mm 20mm;
            }
        </style>
    </head>
    <body<?php if (isset($this->params['bodyClass'])) { ?> class="<?= $this->params['bodyClass']; ?>"<?php } ?>>
        <?php $this->beginBody() ?>
        <?= $content ?>
        <?php $this->endBody() ?>
        <script>
            setTimeout(function () {
                window.print();
            }, 500);
        </script>
    </body>
</html>
<?php $this->endPage() ?>