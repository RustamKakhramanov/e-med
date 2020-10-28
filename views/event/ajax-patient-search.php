<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\widgets\ListView;
?>

<div class="patients-view">
    <table class="data-table">
        <?php
        foreach ($dataProvider->models as $model) {
            echo $this->render('ajax-patient_table', ['model' => $model]);
        }
        ?>
    </table>
</div>