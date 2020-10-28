<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\widgets\ListView;
use yii\helpers\Html;
use app\helpers\Utils;

$this->title = 'Пациенты';
?>

<div class="b-picker b-picker__patient view-outer-ctr">
    <div class="row">
        <div class="col-md-12">
            <h1><?= $this->title; ?></h1>

            <?=
            $this->render('_search', [
                    'searchModel' => $searchModel
            ]);
            ?>

            <div class="row mt10">
                <div class="col-xs-6">
                    <div class="clearfix">
                        <h2 class="pull-left">Найдено</h2>
                        <span class="subheader pull-left"><?= Utils::human_plural_form($dataProvider->query->count(), ['запись', 'записи', 'записей']); ?></span>
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
                'layout' => '<table class="data-table picker-items"><thead class="h-sort">{sorter}</thead><tbody>{items}</tbody><tfoot><tr><td colspan="5">{pager}</td></tr></tfoot></table>',
                'sorter' => [
                    'class' => app\components\LinkSorterTHead::className(),
                    'attributes' => [
                        'col_fio' => 'name_fio',
                        'col_sex' => 'sex',
                        'col_birthday' => 'birthday',
                        'col_age' => 'age',
                        'col_iin' => 'iin',
                    ]
                ],
                'emptyText' => ''
            ])
            ?>

        </div>
    </div>
</div>

<style>
    .b-picker__patient .col_sex {
        width: 70px;
    }

    .b-picker__patient .col_age {
        width: 70px;
    }

    .b-picker__patient .col_birthday {
        width: 130px;
    }

    .b-picker__patient .col_iin {
        width: 140px;
    }
</style>

<?php
$data = [];
foreach ($dataProvider->getModels() as $model) {
    $data[$model->id] = [
        'id' => $model->id,
        'name' => $model->fio
    ];
}
?>

<script>
    var pickerSearchData = <?= json_encode($data); ?>;
    var pickerTarget = '<?= Yii::$app->request->get('target'); ?>';

    $(document).ready(function () {
        $('.picker-items .action-pick').on('click', function () {
            var data = pickerSearchData[$(this).attr('data-id')];
            data.target = pickerTarget;
            $(document).trigger('patientPick.' + pickerTarget, data);
            $(this).closest('.modal-wrap').trigger('close');
            return false;
        });

        $('.b-picker .picker-items a').on('click', function () {
            var url = $(this).attr('href');
            var $modal = $(this).closest('.modal-wrap');
            if (url && url != '#') {
                $modal.trigger('updateData', {url: url}).trigger('reload');
                return false;
            }
        });

        $('.b-picker .picker-link__outer').on('click', function () {
            var url = $(this).attr('href');
            var $currentModal = $(this).closest('.modal-wrap');
            openModal({
                url: url,
                onClose: function () {
                    $currentModal.trigger('reload');
                }
            });
            return false;
        });
    });
</script>
