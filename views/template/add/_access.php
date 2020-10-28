<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var $model app\models\Template */

?>
<div class="row">
    <div class="col-xs-6">
        <div class="temp-access-specs">
            <div class="clearfix">
                <h2 class="mt0 pull-left">Специализации<span class="subheader">0</span></h2>
                <span class="btn btn-default btn-xs ml5 pull-right js-delete-handler"><i class="fa fa-times mr5"></i>Удалить</span>
                <span class="btn btn-default btn-xs pull-right js-add-handler"><i class="fa fa-plus mr5"></i>Добавить</span>
            </div>

            <div class="rl-table">
                <div class="rl-table-header mt0">
                    <table>
                        <tr>
                            <td class="rl_col_check">
                                <div class="checkbox">
                                    <?php
                                    $uid = uniqid();
                                    echo Html::checkbox('', false, [
                                        'label' => null,
                                        'id' => $uid
                                    ])
                                    ?>
                                    <?= Html::label('&nbsp;', $uid); ?>
                                </div>
                            </td>
                            <td class="rl_col_name">Наименование</td>
                        </tr>
                    </table>
                </div>
                <div class="rl-table-rows">
                    <?php foreach ($model->templateSpecs as $row) {
                        echo $this->render('_access_row', [
                            'model' => $row
                        ]);
                    }?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-6">
        <div class="temp-access-docs">
            <div class="clearfix">
                <h2 class="mt0 pull-left">Специалисты<span class="subheader">0</span></h2>
                <span class="btn btn-default btn-xs ml5 pull-right js-delete-handler"><i class="fa fa-times mr5"></i>Удалить</span>
                <span class="btn btn-default btn-xs pull-right js-add-handler"><i class="fa fa-plus mr5"></i>Добавить</span>
            </div>
            <div class="rl-table">
                <div class="rl-table-header mt0">
                    <table>
                        <tr>
                            <td class="rl_col_check">
                                <div class="checkbox">
                                    <?php
                                    $uid = uniqid();
                                    echo Html::checkbox('', false, [
                                        'label' => null,
                                        'id' => $uid
                                    ])
                                    ?>
                                    <?= Html::label('&nbsp;', $uid); ?>
                                </div>
                            </td>
                            <td class="rl_col_name">ФИО</td>
                        </tr>
                    </table>
                </div>
                <div class="rl-table-rows">
                    <?php foreach ($model->templateDocs as $row) {
                        echo $this->render('_access_doc_row', [
                            'model' => $row
                        ]);
                    }?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.temp-access-specs .js-add-handler').on('click', function () {
            $('.temp-access-specs').addClass('block__loading');
            $.ajax({
                url: '<?=Url::to(['template/access-add-row']);?>',
                success: function (html) {
                    $('.temp-access-specs .rl-table-rows').append(html);
                    $('.temp-access-specs').removeClass('block__loading');
                }
            });
        });

        $('.temp-access-specs .rl-table-header .checkbox > input').on('change', function () {
            var v = $(this).prop('checked');
            $('.temp-access-specs .rl-table-rows .item').each(function () {
                $('.checkbox > input', $(this)).prop('checked', v);
            });
        });

        $('.temp-access-specs .js-delete-handler').on('click', function () {
            $('.temp-access-specs .rl-table-rows .item').each(function () {
                var $item = $(this);
                var $c = $('.rl_col_check .checkbox > input', $item);
                if ($c.prop('checked')) {
                    $item.remove();
                }
            });
        });

        $('.temp-access-docs .js-add-handler').on('click', function () {
            $('.temp-access-docs').addClass('block__loading');
            $.ajax({
                url: '<?=Url::to(['template/access-add-doc-row']);?>',
                success: function (html) {
                    $('.temp-access-docs .rl-table-rows').append(html);
                    $('.temp-access-docs').removeClass('block__loading');
                }
            });
        });

        $('.temp-access-docs .rl-table-header .checkbox > input').on('change', function () {
            var v = $(this).prop('checked');
            $('.temp-access-docs .rl-table-rows .item').each(function () {
                $('.checkbox > input', $(this)).prop('checked', v);
            });
        });

        $('.temp-access-docs .js-delete-handler').on('click', function () {
            $('.temp-access-docs .rl-table-rows .item').each(function () {
                var $item = $(this);
                var $c = $('.rl_col_check .checkbox > input', $item);
                if ($c.prop('checked')) {
                    $item.remove();
                }
            });
        });

    });
</script>