<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\Calendar\Calendar;
use yii\helpers\ArrayHelper;

$this->registerAssetBundle('app\assets\GridsterAsset');

$this->title = 'Панель супервизора';
?>

<div class="row">
    <div class="col-md-12">

        <h1>
            <?= $this->title; ?>
        </h1>

<!--        <div class="gridster">
            <ul>
                <li data-row="1" data-col="1" data-sizex="2" data-sizey="2"></li>                
            </ul>
        </div>-->

        <div style="height: 320px;border: 1px #ECEFF3 solid;width:50%; padding: 10px;">
            <?php echo $this->render('_statuses');?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
//        $('.gridster ul').gridster({
//            widget_margins: [10, 10],
//            widget_base_dimensions: [300, 300],
//            //autogrow_cols: true,
//            resize: {
//                enabled: true
//            }
//        });
    })
</script>