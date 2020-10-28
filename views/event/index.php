<?php

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\ListView;

$this->title = 'События';
?>

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
                    <span class="subheader pull-left"><?= human_plural_form($countFindRecord, ['событие', 'события', 'событий']); ?></span>
                </div>
            </div>
            <div class="col-xs-6 mt20">
                <!--a href="<?= Url::toRoute('/' . Yii::$app->controller->id . '/create'); ?>" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus mr5"></i>Добавить</a-->
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
                        'layout' => '<table class="data-table"><thead class="h-sort">{sorter}</thead>{items}<tfoot><tr><td colspan="13">{pager}</td></tr></tfoot></table>',
                        'sorter' => [
                            'class' => app\components\LinkSorterTHead::className(),
                            'attributes' => [
                                'col_type' => 'type',
                                'col_date' => 'date',
                                'col_patient' => 'patient.name',
                                'col_age' => 'patients.birthday',
                                'col_doctor' => 'doctor.name',
                                'col_doctor_spec' => 'doctor_spec',
                                'col_phone' => 'patient.phone'
                            ],
                            'colspans' => [
                                'type' => 3,
                                'patient.name' => 3,
                                'doctor.name' => 2,
                                'doctor_spec' => 2,
                            ],
                            'attributesLinkFalse' => [
                                'doctor_spec' => false,
                                'patient.phone' => false
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