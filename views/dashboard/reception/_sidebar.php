<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="b-reception-sidebar-inner">
    <div class="header-ctr clearfix">
        <h2 class="pull-left">Направления</h2>
        <span class="pull-right btn btn-sm btn-default js__receprion-add-direction"><i class="fa fa-plus"></i></span>
    </div>
    <?php if (!$directions) { ?>
        <div class="mt10 mb10">Нет активных направлений</div>
    <?php } else { ?>
        <div class="rl-table">
            <div class="rl-table-header mt0">
                <table>
                    <tr>
                        <td class="rl_col_service">Услуга</td>
                        <td class="rl_col_date">Дата</td>
                    </tr>
                </table>
            </div>
            <div class="rl-table-rows">
                <?php foreach ($directions as $item) { ?>
                    <div class="item">
                        <table>
                            <tr>
                                <td class="rl_col_service">
                                    <?= $item->serviceName; ?>
                                </td>
                                <td class="rl_col_date">
                                    <?= date('d.m.Y', strtotime($item->direction->created)); ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>

<script>
    $(document).ready(function () {
        $('.js__receprion-add-direction').on('click', function () {
            openModal({
                url: '<?=Url::to(['dashboard/create-direction', 'id' => $patient_id]);?>'
            });
        });
    });
</script>