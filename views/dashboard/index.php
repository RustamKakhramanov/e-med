<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\ListView;
use app\helpers\Utils;

$this->title = 'Рабочий стол';
?>

<div class="dashboard-index">
    <h1><?= $this->title; ?></h1>
    <div class="dashboard-left">
        <?=
        $this->render('_search', [
            'model' => $searchModel
        ]);
        ?>
        <div class="row mt10">
            <div class="col-xs-6">
                <div class="clearfix">
                    <h2 class="pull-left">Найдено</h2>
                    <span class="subheader pull-left"><?= Utils::human_plural_form($dataProvider->query->count(), ['направление', 'направления', 'направлений']); ?></span>
                </div>
            </div>
            <div class="col-xs-6 mt20">

            </div>
        </div>

        <?=
        ListView::widget([
            'dataProvider' => $dataProvider,
            'itemOptions' => ['class' => 'item'],
            'itemView' => function ($model, $key, $index, $widget) {
                //return $this->render('_table', ['model' => $model]);
            },
            'layout' => '<table class="data-table"><thead class="h-sort">{sorter}</thead></table>',
            'sorter' => [
                'class' => app\components\LinkSorterTHead::className(),
                'attributes' => [
                    'col_number' => 'direction_item.id',
                    'col_price' => 'price.title',
                    'col_patient' => 'patients.search_field',
                    'col_doctor' => 'doctor.fio',
                    'col_created' => 'direction.created',
                    'col_paid' => 'direction_item.paid'
                ],
                'attributesLinkFalse' => [
                ]
            ],
            'emptyText' => ''
        ])
        ?>

        <div class="dashboard-left__content native-scroll">
            <?=
            ListView::widget([
                'dataProvider' => $dataProvider,
                'itemOptions' => ['class' => 'item'],
                'itemView' => function ($model, $key, $index, $widget) {
                    return $this->render('_table', ['model' => $model]);
                },
                'layout' => '<table class="data-table">{items}<tfoot><tr><td colspan="6">{pager}</td></tr></tfoot></table>',
                'emptyText' => ''
            ])
            ?>
        </div>
    </div>
    <div class="dashboard-right">
        <?= $this->render('schedule/day', [
            'scheduleData' => $scheduleData
        ]); ?>
    </div>
</div>

<style>
    .dashboard-index {
        position: relative;
        margin: 0px -20px;
        overflow: hidden;
    }

    .dashboard-index h1 {
        margin-left: 20px;
    }

    .dashboard-index .col_created {
        width: 140px;
    }

    .dashboard-index .col_paid {
        width: 120px;
    }

    .dashboard-index .col_number {
        width: 80px;
    }

    .dashboard-right {
        position: absolute;
        right: 20px;
        top: 70px;
        width: 300px;
        bottom: 0px;
        background: #fff;
        padding: 0px 0px;
        transition: all 0.5s;
    }

    .dashboard-right__max {
        z-index: 2;
        width: auto;
        left: 20px;
    }

    .dashboard-left {
        position: absolute;
        left: 0px;
        top: 70px;
        right: 320px;
        bottom: 0px;
        background: #fff;
        overflow: auto;
        padding: 0px 20px;
        transition: left 0.5s;
    }

    .dashboard-left__content {
        position: absolute;
        top: 178px;
        bottom: 0px;
        left: 20px;
        right: 20px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .dashboard-right .schedule-ctr {
        position: absolute;
        top: 107px;
        bottom: 0px;
        left: 0px;
        right: 0px;
        overflow-x: hidden;
        overflow-y: auto;
    }

    .dashboard-right .schedule-ctr .schedule-content {
        bottom: auto;
    }
</style>

<script>
    function pageHeight() {
        $('.dashboard-index').height($(window).height());
    }

    $(document).ready(function () {

        pageHeight();
        $(window).on('resize', function () {
            setTimeout(pageHeight, 100);
        });

        $('.dashboard-index .action-cancel').on('click', function () {
            openModal({
                url: '<?=Url::to(['dashboard/cancel']);?>/' + $(this).attr('data-id')
            });
        });
    });
</script>