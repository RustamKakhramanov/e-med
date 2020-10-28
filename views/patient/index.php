<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\widgets\ListView;

$this->title = 'Пациенты';
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
                    <span class="subheader pull-left"><?= human_plural_form($countFindRecord, ['пациент', 'пациента', 'пациентов']); ?></span>
                </div>
            </div>
            <div class="col-xs-6 mt20">
                <a href="<?= Url::toRoute('patient/add'); ?>" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus mr5"></i>Добавить</a>
            </div>
        </div>

        <div class="patients-view">
            <?=
            ListView::widget([
                'dataProvider' => $dataProvider,
                'itemOptions' => ['class' => 'item'],
                'itemView' => function ($model, $key, $index, $widget) {
            return $this->render('_table', ['model' => $model]);
        },
                'layout' => '<table class="data-table"><thead class="h-sort">{sorter}</thead>{items}<tfoot><tr><td colspan="7">{pager}</td></tr></tfoot></table>',
                'sorter' => [
                    'class' => app\components\LinkSorterTHead::className(),
                    'attributes' => [
                        'col_has_dir' => 'has_dir',
                        '' => 'name_fio',
                        'col_last_visit' => 'last_visit', //todo
                        'col_sex' => 'sex',
                        'col_birthday' => 'birthday',
                        'col_age' => 'age',
                        'col_iin' => 'iin',
                    ],
                    'attributesLinkFalse' => [
                        'last_visit' => false,
                        'dir' => false
                    ]
                ],
                'emptyText' => ''
            ])
            ?>
        </div>


    </div>
</div>
</div>