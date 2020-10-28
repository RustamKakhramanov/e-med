<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\widgets\ListView;

$this->title = 'Отчет кассира';
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
                    <span class="subheader pull-left"><?= human_plural_form($dataProvider->query->count(), ['услуга', 'услуги', 'услуг']); ?></span>
                </div>
            </div>
            <div class="col-xs-6">
                <span class="btn btn-sm btn-primary mt20 pull-right js__export-cashier-report"><i class="fa fa-download mr5"></i>Выгрузить</span>
            </div>
        </div>

        <table class="cashier-report data-table">
            <thead class="h-sort">
            <tr>
                <th class="col_number">
                    <span>Направление</span>
                </th>
                <th class="col_price">
                    <span>Услуга</span>
                </th>
                <th class="col_sum">
                    <span>Стоимость</span>
                </th>
                <th class="col_user">
                    <span>Создано</span>
                </th>
                <th class="col_doctor">
                    <span>Проведено</span>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($dataProvider->models as $item) { ?>
                <tr>
                    <td class="col_number">
                        <?= $item->numberPrint; ?>
                    </td>
                    <td class="col_price">
                        <?= $item->serviceName; ?>
                    </td>
                    <td class="col_sum">
                        <?= $item->summ; ?>
                    </td>
                    <td class="col_user">
                        <?= $item->direction->user->fio; ?>
                    </td>
                    <td class="col_doctor">
                        <?= $item->doctor_id ? $item->doctor->fio : '-'; ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .cashier-report {

    }

    .cashier-report .col_number {
        width: 85px;
    }
</style>

<script>
    $(document).ready(function(){
        $('.js__export-cashier-report').on('click', function(){
            var u = '/cashier/report-export' + window.location.search;
            window.location = u;
        });
    });
</script>