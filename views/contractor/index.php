<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\widgets\ListView;

$this->title = 'Контрагенты';
?>

<div class="row">
    <div class="col-md-12">
        <h1><?= $this->title; ?></h1>

        <div class="row mt10">
            <div class="col-xs-6">
                <div class="clearfix">
                    <h2 class="pull-left">Найдено</h2>
                    <span class="subheader pull-left"><?= human_plural_form($countFindRecord, ['запись', 'записи', 'записей']); ?></span>
                </div>
            </div>
            <div class="col-xs-6 mt20">
                <a href="<?= Url::toRoute('/' . Yii::$app->controller->id . '/add'); ?>" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus mr5"></i>Добавить</a>
            </div>
        </div>

        <div class="contractors-view">
            <?=
            ListView::widget([
                'dataProvider' => $dataProvider,
                'itemOptions' => ['class' => 'item'],
                'itemView' => function ($model, $key, $index, $widget) {
            return $this->render('_table', ['model' => $model]);
        },
                'layout' => '<table class="data-table"><thead class="h-sort">{sorter}</thead>{items}<tfoot><tr><td colspan="4">{pager}</td></tr></tfoot></table>',
                'sorter' => [
                    'class' => app\components\LinkSorterTHead::className(),
                    'attributes' => [
                        'col_name' => 'name',
                        'col_bin' => 'bin',
                        'col_phone' => 'phone',
                        'col_address' => 'address',
                    ]
                ],
                'emptyText' => ''
            ])
            ?>
        </div>
    </div>
</div>