<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\widgets\ListView;

$this->title = 'Чеки';
?>

<div class="cashier-checks row">
    <div class="col-md-12">
        <h1><?= $this->title; ?></h1>


        <?php
        //        $this->render('_search', [
        //            'model' => $searchModel
        //        ]);
        ?>

        <div class="row mt10">
            <div class="col-xs-6">
                <div class="clearfix">
                    <h2 class="pull-left">Найдено</h2>
                    <span class="subheader pull-left"><?= human_plural_form(count($dataProvider->models), ['чек', 'чека', 'чеков']); ?></span>
                </div>
            </div>
        </div>

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
                    'col_number' => 'id',
                    'col_date' => 'created',
                    'col_summ' => 'sum',
                    'col_patient' => 'patients.name',
                    'col_user' => 'user.username',
                    'col_webkassa' => 'webkassa_id',
                ],
            ],
            'emptyText' => ''
        ])
        ?>
    </div>
</div>

<style>
    .cashier-checks .col_number {
        width: 100px;
    }

    .cashier-checks .col_date {
        width: 100px;
    }

    .cashier-checks .col_webkassa {
        width: 185px;
    }

    .cashier-checks__send {
        display: inline-block;
        /*text-decoration: underline;*/
        font-size: 12px;
        vertical-align: top;
        margin: 3px 0px 0px 3px;
    }
</style>

<script>
    $(document).ready(function () {
        $('.cashier-checks .action-print').on('click', function () {
            let id = $(this).closest('tr').attr('data-id');
            return window.open('<?=Url::to(['cashier/check-print']);?>/' + id, "_blank", "toolbar=no,top=0,left=0,width=290,height=600");
        });

        $('.cashier-checks .action-cancel').on('click', function () {
            let id = $(this).closest('tr').attr('data-id');
            openModal({
                url: '<?=Url::to(['cashier/check-cancel']);?>/' + id
            });
            return false;
        });
    });
</script>