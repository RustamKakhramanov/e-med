<?php

use yii\helpers\Url;

$count = count($dataProvider->models) - 1;
?>

<?php foreach ($dataProvider->models as $key => $row) { ?>
    <div class="item">
        <table class="data-table">
            <tbody>
                <tr>
                    <td class="col_name">
                        <span class="row-icon <?= $row->iconClass; ?>"></span>
                        <?= $row->title; ?>
                        <div class="action-group clearfix">
                            <a href="<?= Url::toRoute('/' . Yii::$app->controller->id) . '/edit/' . $row->id; ?>" class="action action-edit"><span class="action-icon-edit"></span></a>
                            <a href="<?= Url::toRoute('/' . Yii::$app->controller->id) . '/delete/' . $row->id; ?>" class="action action-delete"><span class="action-icon-delete"></span></a>
                        </div>
                    </td>
                    <td class="col_cost">
                        <?= number_format($row->cost, 2, ', ', ' '); ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php if ($key == $count) { ?>
            <script>
                var currentPage = <?= $dataProvider->pagination->page + 1; ?>;
                var totalItems = <?= $dataProvider->pagination->totalCount; ?>;
                var lastPage = <?= ceil($dataProvider->pagination->totalCount / $dataProvider->pagination->defaultPageSize); ?>;
                
                $(function(){
                    $('.items-count-ctr').text(totalItems);
                });
            </script>
        <?php } ?>
    </div>
<?php }; ?>

