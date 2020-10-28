<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\widgets\ListView;

$this->title = 'Выбор пациента';
?>

<div class="pl20 pr20 pb20" style="width: 820px;">
    <div class="row">
        <div class="col-md-12">
            <h1><?= $this->title; ?></h1>

            <?=
            $this->render('_search', [
                'model' => $searchModel
            ]);
            ?>

            <div class="row">
                <div class="col-xs-6">
                    <div class="clearfix">
                        <h2 class="pull-left">Найдено</h2>
                        <span class="subheader pull-left"><?= human_plural_form($countFindRecord, ['пациент', 'пациента', 'пациентов']); ?></span>
                    </div>
                </div>
            </div>

            <div class="patients-view quick-search-items">
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
                            '' => 'name_fio',
                            'col_sex' => 'sex',
                            'col_birthday' => 'birthday',
                            'col_age' => 'age',
                            'col_iin' => 'iin',
                        ],
                        'attributesLinkFalse' => [
                            'name_fio' => false,
                            'sex' => false,
                            'birthday' => false,
                            'age' => false,
                            'iin' => false
                        ]
                    ],
                    'emptyText' => ''
                ])
                ?>
            </div>

        </div>
    </div>
</div>
<?php
$data = [];
foreach ($dataProvider->getModels() as $model) {
    $data[$model->id] = [
        'id' => $model->id,
        'fio' => $model->fio,
        'sex' => (int) $model->sex,
        'birthday' => $model->birthdayPrint
    ];
}
?>

<script>
    var quickSearchData = <?= json_encode($data); ?>;

    $(document).ready(function () {
        $('.quick-search-items .action-pick').on('click', function () {
            patientQuickPick(quickSearchData[$(this).attr('data-id')]);
            $(this).closest('.modal-wrap').trigger('close');
            return false;
        });
    });
</script>