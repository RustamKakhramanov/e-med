<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\widgets\ListView;

$this->title = 'Специалисты';
?>

<div class="row">
    <div class="col-md-12">
        <h1><?= $this->title; ?></h1>


        <?=
        $this->render('_search', [
            'model' => $searchModel,
            'specialities' => $specialities,
            'subdivisions' => $subdivisions
        ]);
        ?>

        <?php
//        foreach ($dataProvider->getModels() as $m) {
//            debug($m->specialities);
//            debug($m->speciality_main);
//        }
//
//        exit;
        ?>


        <div class="row mt10">
            <div class="col-xs-6">
                <div class="clearfix">
                    <h2 class="pull-left">Найдено</h2>
                    <span class="subheader pull-left"><?= human_plural_form($countFindRecord, ['специалист', 'специалиста', 'специалистов']); ?></span>
                </div>
            </div>
            <div class="col-xs-6 mt20">
                <ul class="tabs tabs-small tabs-view pull-right ml20 clearfix">
                    <li class="active"><a href="#view-table" data-toggle="tab"><span class="ico-view-table"></span></a></li>
                    <li><a href="#view-list" data-toggle="tab"><span class="ico-view-list"></span></a></li>
                </ul>

                <a href="<?= Url::toRoute('/' . Yii::$app->controller->id . '/add'); ?>" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus mr5"></i>Добавить</a>
            </div>
        </div>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="view-table">
                <div class="patients-view">
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
                                'col_name' => 'name_fio',
                                'col_spec' => 'specialities.name',
                                '' => 'subdivision_id', //todo
                                'col_sex' => 'sex',
                                'col_birthday' => 'birthday'
                            ],
                        ],
                        'emptyText' => ''
                    ])
                    ?>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane active" id="view-list">
                <div class="patients-view">

                </div>
            </div>

        </div>
    </div>
</div>