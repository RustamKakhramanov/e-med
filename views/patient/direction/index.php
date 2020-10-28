<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use app\helpers\Utils;
use yii\widgets\ListView;

$this->title = 'Направления ' . $patient->fio;

?>

<div class="row">
    <div class="col-md-12">
        <h1><?= $patient->fio; ?></h1>
        <ul class="tabs tabs-large clearfix">
            <li class=""><a href="<?= Url::to(['patient/edit', 'id' => $patient->id]); ?>"><i
                            class="fa fa-user mr5"></i>Профиль</a></li>
            <li class="active"><a href="<?= Url::to(['patient/direction', 'id' => $patient->id]); ?>"><i
                            class="fa fa-indent mr5"></i>Направления</a></li>
        </ul>

        <div class="patient-direction mt20">
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
                    <a href="<?= Url::to(['patient/direction-add', 'id' => $patient->id]); ?>"
                       class="btn btn-sm btn-primary pull-right"><i
                                class="fa fa-plus mr5"></i>Добавить</a>
                </div>
            </div>

            <?=
            ListView::widget([
                'dataProvider' => $dataProvider,
                'itemOptions' => ['class' => 'item'],
                'itemView' => function ($model, $key, $index, $widget) {
                    return $this->render('_table', ['model' => $model]);
                },
                'layout' => '<table class="data-table"><thead class="h-sort">{sorter}</thead>{items}<tfoot><tr><td colspan="5">{pager}</td></tr></tfoot></table>',
                'sorter' => [
                    'class' => app\components\LinkSorterTHead::className(),
                    'attributes' => [
                        'col_number' => 'direction.id',
                        'col_price' => 'price.title',
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

<style>
    .patient-direction .col_created {
        width: 140px;
    }

    .patient-direction .col_paid {
        width: 120px;
    }

    .patient-direction .col_number {
        width: 80px;
    }
</style>

<script>
    $(document).ready(function () {
        $('.dashboard-index .action-cancel').on('click', function () {
            openModal({
                url: '<?=Url::to(['patient/direction-cancel']);?>/' + $(this).attr('data-id')
            });
        });
    });
</script>