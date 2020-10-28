<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\widgets\ListView;

$this->title = 'Пользователи';
?>

<div class="row">
    <div class="col-md-12">
        <h1><?= $this->title; ?></h1>

        <div class="row mt10">
            <div class="col-xs-6">
                <div class="clearfix">
                    <h2 class="pull-left">Найдено</h2>
                    <span class="subheader pull-left"><?= human_plural_form($countFindRecord, ['пользователь', 'пользователя', 'пользователей']); ?></span>
                </div>
            </div>
            <div class="col-xs-6 mt20">
                <a href="<?= Url::toRoute('/' . Yii::$app->controller->id . '/add'); ?>" class="btn btn-sm btn-primary pull-right"><i class="fa fa-plus mr5"></i>Добавить</a>
            </div>
        </div>

        <div class="contracts-view">
            <?=
            ListView::widget([
                'dataProvider' => $dataProvider,
                'itemOptions' => ['class' => 'item'],
                'itemView' => function ($model, $key, $index, $widget) {
            return $this->render('_table', ['model' => $model]);
        },
                'layout' => '<table class="data-table"><thead class="h-sort">{sorter}</thead>{items}</table>',
                'sorter' => [
                    'class' => app\components\LinkSorterTHead::className(),
                    'attributes' => [
                        'col_name' => 'username',
                        'col_fio' => 'fio',
                        'col_created' => 'created_at',
                        'col_updated' => 'updated_at',
                        'col_role' => 'role_id'
                    ],
//                    'attributesLinkFalse' => [
//                        'username' => false
//                    ]
                ],
                'emptyText' => ''
            ])
            ?>
        </div>
    </div>
</div>