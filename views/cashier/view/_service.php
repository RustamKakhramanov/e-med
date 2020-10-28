<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\helpers\Utils;

$uid = uniqid();
?>

<div class="item item-<?= $uid; ?>" data-summ="<?= ($item->cost * $item->count); ?>">
    <table>
        <tr>
            <td class="rl_col_check">
                <div class="checkbox">
                    <?=
                    Html::checkbox('services[' . $item->id . ']', false, [
                        'label' => null,
                        'id' => $uid,
                        'value' => $item->cost * $item->count
                    ])
                    ?>
                    <?= Html::label('&nbsp;', $uid); ?>
                </div>
            </td>
            <td class="rl_col_date">
                <?= date('d.m.Y', strtotime($item->direction->created)); ?>
            </td>
            <td class="rl_col_direction">
                <?= $item->direction->numberPrint; ?>
            </td>
            <td class="rl_col_service">
                <?= $item->price->title; ?>
            </td>
            <td class="rl_col_user">
                <?= $item->direction->user->username; ?>
            </td>
            <td class="rl_col_summ">
                <?= Utils::nformat($item->cost * $item->count); ?>
            </td>
        </tr>
    </table>

    <script>
        $(document).ready(function () {
            function <?= 'init_' . $uid; ?>() {
                var keys = ['spec_id'];
                $.each(keys, function (k, keyName) {
                    var n = 'access-spec-<?= $uid; ?>-' + keyName;
                    $form.yiiActiveForm('add', {
                        id: n,
                        name: n,
                        container: '.field-' + n,
                        input: '#' + n,
                        error: '.help-block',
                        enableAjaxValidation: true
                    });
                });
            }

            var $form = $('.item-<?= $uid; ?>').closest('form');
            if ($form.data('yiiActiveForm')) {
                <?= 'init_' . $uid; ?>();
            } else {
                $form.on('afterInit', function () {
                    <?= 'init_' . $uid; ?>();
                });
            }
        });
    </script>
</div>