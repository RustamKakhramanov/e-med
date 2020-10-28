<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\widgets\ListView;

$this->title = 'Отчет по направлениям за ';
?>

<div class="row">
    <div class="col-md-12">

        <div class="clearfix">
            <h1 class="pull-left">
                <?= $this->title; ?>
            </h1>
            <div class="input-group input-datepicker pull-left" style="margin: 26px 0px 0px 14px;width: 133px;">
                <input type="text" class="form-control datepicker" value="<?= $date; ?>"/>
                <div class="dropdown-menu dropdown-menu-right">
                    <div class="input-datepicker-ui"></div>
                </div>
                <span class="input-group-addon dropdown-handler" data-toggle="dropdown"></span>
            </div>
        </div>

        <?php if (!count($data)) { ?>
            <i class="fa fa-warning mr5"></i>За день нет направлений
        <?php } else { ?>

            <table class="data-table report-paid-table">
                <thead>
                    <tr>
                        <th><span>Специалист/пациент</span></th>
                        <th class="col-count text-right"><span>Кол-во услуг</span></th>
                        <th class="col-sum text-right"><span>На сумму</span></th>
                        <th class="col-sum text-right"><span>Оплачено</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $patientIds = [];
                    foreach ($data as $key => $item) {
                        ?>
                        <tr class="tr-doctor">
                            <td class="col-name">
                                <?= $item['doctor']; ?>
                            </td>
                            <td class="text-right">
                                <?= $item['count']; ?>
                            </td>
                            <td class="text-right">
                                <?= $item['sum']['total']; ?>
                            </td>
                            <td class="text-right">
                                <?= $item['sum']['paid']; ?>
                            </td>
                        </tr>
                        <?php $counter = 1; ?>
                        <?php
                        foreach ($item['patients'] as $subKey => $subItem) {
                            if (!in_array($subKey, $patientIds)) {
                                $patientIds[] = $subKey;
                            }
                            ?>
                            <tr class="tr-patient">
                                <td class="col-name">
                                    <?= $counter . '.'; ?>
                                    <?= $subItem['patient']; ?>
                                </td>
                                <td class="text-right">
                                    <?= $subItem['count']; ?>
                                </td>
                                <td class="text-right">
                                    <?= $subItem['sum']['total']; ?>
                                </td>
                                <td class="text-right">
                                    <?php if ($subItem['sum']['paid'] != $subItem['sum']['total']) { ?>
                                        <span class="text-danger">
                                            <?= $subItem['sum']['paid']; ?>
                                        </span>
                                    <?php } else { ?>
                                        <span class="text-success">
                                            <?= $subItem['sum']['paid']; ?>
                                        </span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php $counter++; ?>
                        <?php } ?>
                    <?php } ?>
                </tbody>

                <tfoot>
                    <tr>
                        <td class="text-right">
                            <div class="clearfix pl10">
                                <span class="pull-left">Пациентов: <?= count($patientIds); ?></span>
                                <span class="pull-right"><strong>Итого:</strong></span>
                            </div>
                        </td>
                        <td class="text-right"><?= $final['count']; ?></td>
                        <td class="text-right"><?= $final['total']; ?></td>
                        <td class="text-right"><?= $final['paid']; ?></td>
                    </tr>
                </tfoot>
            </table>

        <?php } ?>
    </div>
</div>

<style>
    .report-paid-table {

    }

    .report-paid-table .col-sum {
        width: 110px;
    }

    .report-paid-table .col-count {
        width: 140px;
    }

    .report-paid-table .tr-doctor {
        font-weight: 700;
    }

    .report-paid-table .tr-doctor .col-name {
        padding-left: 10px;
    }

    .report-paid-table .tr-patient td {
        padding: 6px 10px;
    }

    .report-paid-table .tr-patient:hover td {
        background: #F8F6E7;
    }

    .report-paid-table .tr-patient .col-name {
        padding-left: 10px;
    }
</style>

<script>
    $(document).ready(function () {
        $('.input-datepicker-ui').datepicker('destroy').datepicker({
            dateFormat: 'yy-mm-dd',
            prevText: '&larr;',
            nextText: '&rarr;',
            showOtherMonths: true,
            onSelect: function (date) {
                var $parent = $(this).closest('.input-datepicker');
                $('input', $parent).val(date);
                $('.dropdown-handler', $parent).dropdown('toggle');

                window.location = '/report/day?date=' + date;
            }
        });

        $('.input-datepicker .dropdown-menu').on('click', function (e) {
            e.stopPropagation();
        });
    });
</script>