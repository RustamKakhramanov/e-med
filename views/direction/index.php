<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\ListView;
use app\helpers\Utils;

$this->title = 'Направления';
?>

<div class="direction-index">
    <div class="row">
        <div class="col-md-12">
            <h1><?= $this->title; ?></h1>

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
                    <a href="<?= Url::to(['direction/master']); ?>" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus mr5"></i>Мастер</a>
                    <a href="<?= Url::to(['direction/payment']); ?>" class="btn btn-sm btn-primary pull-right mr15"><i class="fa fa-money mr5"></i>Оплата</a>
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
                            'layout' => '<table class="data-table"><thead class="h-sort">{sorter}</thead>{items}<tfoot><tr><td colspan="6">{pager}</td></tr></tfoot></table>',
                            'sorter' => [
                                'class' => app\components\LinkSorterTHead::className(),
                                'attributes' => [
                                    'col_number' => 'direction.id',
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .direction-index .col_created {
        width: 140px;
    }

    .direction-index .col_paid {
        width: 120px;
    }

    .direction-index .col_number {
        width: 80px;
    }
</style>