<?php

use app\helpers\Utils;

/** @var $model \app\models\Check */
/** @var $cashbox \app\models\Cashbox */
$cashbox = $model->shift->cashbox;
?>

<table cellpadding="0" cellspacing="0" border="0"
       style="border-width:0px;empty-cells:show;width:271px;position:relative;">
    <tbody>
    <tr>
        <td style="width:0px;height:0px;"></td>
        <td style="height:0px;width:2px;"></td>
        <td style="height:0px;width:22px;"></td>
        <td style="height:0px;width:95px;"></td>
        <td style="height:0px;width:5px;"></td>
        <td style="height:0px;width:6px;"></td>
        <td style="height:0px;width:3px;"></td>
        <td style="height:0px;width:32px;"></td>
        <td style="height:0px;width:3px;"></td>
        <td style="height:0px;width:6px;"></td>
        <td style="height:0px;width:16px;"></td>
        <td style="height:0px;width:78px;"></td>
        <td style="height:0px;width:1px;"></td>
        <td style="height:0px;width:2px;"></td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:16px;"></td>
        <td colspan="12"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:bold;font-style:normal;width:269px;height:16px;line-height:14px;text-align:center;vertical-align:top;">
            <nobr>ДУБЛИКАТ</nobr>
        </td>
        <td></td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:17px;"></td>
        <td colspan="12"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:269px;height:17px;line-height:14px;text-align:center;vertical-align:top;">
            <nobr><?= $cashbox->org_name; ?></nobr>
        </td>
        <td></td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:16px;"></td>
        <td colspan="12"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:269px;height:16px;line-height:14px;text-align:center;vertical-align:top;">
            <nobr>БИН&nbsp;<?= $cashbox->bin; ?></nobr>
        </td>
        <td></td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:13px;"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:4px;"></td>
        <td colspan="12"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom:#000000 1px dashed;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:269px;height:3px;">
        </td>
        <td></td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:4px;"></td>
        <td colspan="12"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:269px;height:4px;">
        </td>
        <td></td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:16px;"></td>
        <td colspan="6"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;padding-right:1px;width:132px;height:16px;line-height:14px;text-align:center;vertical-align:middle;">
            <nobr><?= $cashbox->name; ?></nobr>
        </td>
        <td colspan="6"
            style="color:#000000;background-color:transparent;border-left:#000000 1px solid;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;padding-left:1px;width:134px;height:16px;line-height:14px;text-align:center;vertical-align:middle;">
            <nobr>Смена&nbsp;<?= $model->webkassaData['ShiftNumber']; ?></nobr>
        </td>
        <td></td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:16px;"></td>
        <td colspan="12"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:269px;height:16px;line-height:14px;text-align:center;vertical-align:top;">
            <nobr>Порядковый&nbsp;номер&nbsp;чека&nbsp;<?= $model->webkassaData['CheckOrderNumber']; ?></nobr>
        </td>
        <td></td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:16px;"></td>
        <td colspan="6"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:133px;height:16px;line-height:14px;text-align:left;vertical-align:top;">
            <nobr>Чек&nbsp;№<?= $model->webkassaData['CheckNumber']; ?></nobr>
        </td>
        <td colspan="6"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:136px;height:16px;line-height:14px;text-align:right;vertical-align:top;">
            <nobr><?= $model->user->fio; ?></nobr>
        </td>
        <td></td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:17px;"></td>
        <td colspan="12"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:269px;height:17px;line-height:14px;text-align:left;vertical-align:top;">
            <nobr><?= (!$model->back_id) ? 'ПРОДАЖА' : 'ВОЗВРАТ ПРОДАЖИ'; ?></nobr>
        </td>
        <td></td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:3px;"></td>
        <td colspan="12"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom:#000000 1px dashed;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:269px;height:2px;">
        </td>
        <td></td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:4px;"></td>
        <td colspan="12"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:269px;height:4px;">
        </td>
        <td></td>
    </tr>

    <!-- items loop -->
    <?php foreach ($model->checkItems as $key => $item) { ?>
        <tr style="vertical-align:top;">
            <td style="width:0px;"></td>
            <td colspan="2"
                style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:24px;line-height:14px;text-align:left;vertical-align:top;">
                <nobr><?= $key + 1; ?>.</nobr>
            </td>
            <td colspan="10"
                style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;padding-left:1px;width:244px;line-height:14px;text-align:left;vertical-align:top;">
                <?= $item->directionItem->price->title; ?>
            </td>
            <td></td>
        </tr>
        <tr style="vertical-align:top;">
            <td style="width:0px;height:17px;"></td>
            <td colspan="2"
                style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:24px;height:17px;">
            </td>
            <td colspan="5"
                style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;padding-left:1px;width:140px;height:17px;line-height:14px;text-align:left;vertical-align:top;">
                <nobr><?= $item->directionItem->count; ?>
                    &nbsp;x&nbsp;<?= Utils::ncost($item->directionItem->cost); ?></nobr>
            </td>
            <td colspan="4"
                style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:103px;height:17px;line-height:14px;text-align:right;vertical-align:top;">
                <nobr><?= Utils::ncost($item->directionItem->count * $item->directionItem->cost); ?></nobr>
            </td>
            <td style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:1px;height:17px;">
            </td>
            <td></td>
        </tr>
        <tr style="vertical-align:top;">
            <td style="width:0px;height:17px;"></td>
            <td colspan="2"
                style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:24px;height:17px;">
            </td>
            <td colspan="2"
                style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom:#000000 1px dotted;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;padding-left:1px;width:99px;height:16px;line-height:14px;text-align:left;vertical-align:top;">
                <nobr>Стоимость</nobr>
            </td>
            <td colspan="8"
                style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom:#000000 1px dotted;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:145px;height:16px;line-height:14px;text-align:right;vertical-align:top;">
                <nobr><?= Utils::ncost($item->directionItem->count * $item->directionItem->cost); ?></nobr>
            </td>
            <td></td>
        </tr>
        <tr style="vertical-align:top;">
            <td style="width:0px;height:6px;"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    <?php } ?>
    <!-- /items loop -->


    <tr style="vertical-align:top;">
        <td style="width:0px;height:7px;"></td>
        <td></td>
        <td colspan="12"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom:#000000 1px dashed;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:269px;height:6px;">
        </td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:6px;"></td>
        <td></td>
        <td colspan="12"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:269px;height:6px;">
        </td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:10px;"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>

    <!-- payments -->
    <tr style="vertical-align:top;">
        <td style="width:0px;height:16px;"></td>
        <td></td>
        <td colspan="2"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:118px;height:16px;line-height:14px;text-align:right;vertical-align:top;">
            <nobr>Наличные:</nobr>
        </td>
        <td colspan="5"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:48px;height:16px;">
        </td>
        <td colspan="5"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:103px;height:16px;line-height:14px;text-align:right;vertical-align:top;">
            <nobr><?= Utils::ncost($model->payment_cash); ?></nobr>
        </td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:16px;"></td>
        <td></td>
        <td colspan="2"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:118px;height:16px;line-height:14px;text-align:right;vertical-align:top;">
            <nobr>Банковская&nbsp;карта:</nobr>
        </td>
        <td colspan="5"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:48px;height:16px;">
        </td>
        <td colspan="5"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:103px;height:16px;line-height:14px;text-align:right;vertical-align:top;">
            <nobr><?= Utils::ncost($model->payment_card); ?></nobr>
        </td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:17px;"></td>
        <td></td>
        <td colspan="2"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:bold;font-style:normal;width:117px;height:17px;line-height:14px;text-align:right;vertical-align:top;">
            <nobr>ИТОГО:</nobr>
        </td>
        <td colspan="6"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:bold;font-style:normal;width:55px;height:17px;">
        </td>
        <td colspan="4"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:bold;font-style:normal;width:97px;height:17px;line-height:14px;text-align:right;vertical-align:top;">
            <nobr><?= Utils::ncost($model->payment_card + $model->payment_cash); ?></nobr>
        </td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:16px;"></td>
        <td></td>
        <td colspan="2"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:117px;height:16px;line-height:14px;text-align:right;vertical-align:top;">
            <nobr>в&nbsp;т.ч.&nbsp;НДС:</nobr>
        </td>
        <td colspan="6"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:55px;height:16px;">
        </td>
        <td colspan="4"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:97px;height:16px;line-height:14px;text-align:right;vertical-align:top;">
            <nobr><?= Utils::ncost($model->nds); ?></nobr>
        </td>
    </tr>
    <!-- /payments -->
    <tr style="vertical-align:top;">
        <td style="width:0px;height:13px;"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:4px;"></td>
        <td></td>
        <td colspan="12"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom:#000000 1px dashed;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:269px;height:3px;">
        </td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:4px;"></td>
        <td></td>
        <td colspan="12"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:269px;height:4px;">
        </td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:16px;"></td>
        <td></td>
        <td colspan="4"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:128px;height:16px;line-height:14px;text-align:left;vertical-align:top;">
            <nobr>Фискальный&nbsp;признак:</nobr>
        </td>
        <td colspan="8"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:141px;height:16px;line-height:14px;text-align:left;vertical-align:top;">
            <nobr><?= $model->webkassa_id; ?></nobr>
        </td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:16px;"></td>
        <td></td>
        <td colspan="12"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:269px;height:16px;line-height:14px;text-align:left;vertical-align:top;">
            <nobr>Время:&nbsp;<?= $model->webkassaData['DateTime']; ?></nobr>
        </td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:30px;"></td>
        <td></td>
        <td colspan="12"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:269px;height:30px;line-height:14px;text-align:left;vertical-align:top;">
            <nobr>Оператор&nbsp;фискальных&nbsp;данных:&nbsp;АО</nobr>
            <br>
            <nobr><?= $cashbox->operator_name; ?></nobr>
        </td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:31px;"></td>
        <td></td>
        <td colspan="12"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:269px;height:31px;line-height:14px;text-align:left;vertical-align:top;">
            <nobr>Для&nbsp;проверки&nbsp;чека&nbsp;зайдите&nbsp;на&nbsp;сайт</nobr>
            <br>
            <nobr>consumer.oofd.kz</nobr>
        </td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:3px;"></td>
        <td></td>
        <td colspan="12"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom:#000000 1px dashed;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:269px;height:2px;">
        </td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:4px;"></td>
        <td></td>
        <td colspan="12"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:269px;height:4px;">
        </td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:2px;"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:15px;"></td>
        <td></td>
        <td colspan="8"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Lucida Console;font-size:12px;font-weight:bold;font-style:normal;width:172px;height:15px;line-height:12px;text-align:right;vertical-align:top;">
            <nobr>ФИСКАЛЬНЫЙ&nbsp;ЧЕК</nobr>
        </td>
        <td style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:16px;height:15px;">
        </td>
        <td colspan="3"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Lucida Console;font-size:12px;font-weight:bold;font-style:italic;width:81px;height:15px;line-height:12px;text-align:left;vertical-align:top;">
            <nobr>ФП</nobr>
        </td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:13px;"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:16px;"></td>
        <td></td>
        <td colspan="12"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:269px;height:16px;line-height:14px;text-align:center;vertical-align:top;">
            <nobr>ИНК:&nbsp;<?= $cashbox->kkt; ?></nobr>
        </td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:16px;"></td>
        <td></td>
        <td colspan="12"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:269px;height:16px;line-height:14px;text-align:center;vertical-align:top;">
            <nobr>РНК:&nbsp;<?= $cashbox->rnk; ?></nobr>
        </td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:16px;"></td>
        <td></td>
        <td colspan="12"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:269px;height:16px;line-height:14px;text-align:center;vertical-align:top;">
            <nobr>ЗНК:&nbsp;<?= $cashbox->webkassa_id; ?></nobr>
        </td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:17px;"></td>
        <td></td>
        <td colspan="12"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:269px;height:17px;line-height:14px;text-align:center;vertical-align:top;">
            <nobr>WEBKASSA.KZ</nobr>
        </td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:13px;"></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr style="vertical-align:top;">
        <td style="width:0px;height:16px;"></td>
        <td></td>
        <td colspan="12"
            style="color:#000000;background-color:transparent;border-left-style:none;border-top-style:none;border-right-style:none;border-bottom-style:none;font-family:Consolas;font-size:12px;font-weight:normal;font-style:normal;width:269px;height:16px;line-height:14px;text-align:center;vertical-align:top;">
            <nobr>***&nbsp;СПАСИБО&nbsp;ЗА&nbsp;ПОКУПКУ&nbsp;***</nobr>
        </td>
    </tr>
    </tbody>
</table>

<style>
    @page {
        margin: 0;
        padding: 0;
        size: auto;
        width: 271px;
    }
</style>