<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\ListView;
use app\helpers\Utils;

$this->title = 'Осмотры';
?>

<div class="reception-index">
    <div class="row">
        <div class="col-md-12">
            <h1><?= $this->title; ?></h1>

            <div class="row mt10">
                <div class="col-xs-6">
                    <div class="clearfix">
                        <h2 class="pull-left">Найдено</h2>
                        <span class="subheader pull-left"><?= Utils::human_plural_form($dataProvider->query->count(), ['осмотр', 'осмотра', 'осмотров']); ?></span>
                    </div>
                </div>
                <div class="col-xs-6 mt20">

                </div>
            </div>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="view-table">
                    <div class="events-view">
                        <?=
                        ListView::widget([
                            'dataProvider' => $dataProvider,
                            'itemOptions' => ['class' => 'item'],
                            'itemView' => function ($model, $key, $index, $widget) {
                                return $this->render('_table', ['model' => $model]);
                            },
                            'layout' => '<table class="data-table"><thead class="h-sort">{sorter}</thead>{items}<tfoot><tr><td colspan="3">{pager}</td></tr></tfoot></table>',
                            'sorter' => [
                                'class' => app\components\LinkSorterTHead::className(),
                                'attributes' => [
                                    'col_number' => 'direction_id',
                                    'col_created' => 'created',
                                    'col_patient' => 'patients.search_field'
                                ],
                                'attributesLinkFalse' => [
                                ]
                            ],
                            'emptyText' => ''
                        ])
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .reception-index .col_number {
        width: 150px;
    }

    .reception-index .col_created {
        width: 150px;
    }
</style>

<script>
    $(document).ready(function(){
        $('.js-action-print').on('click', function(){
            return window.open($(this).attr('href'), "_blank", "toolbar=no,top=0,left=0,width=600,height=600");
        });
    });
</script>