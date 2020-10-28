<?php if ($items) { ?>
    <div class="rl-table">
        <div class="rl-table-rows">
            <?php foreach ($items as $item) {
                $uid = uniqid();
                ?>
                <div class="item item-<?= $uid; ?>" data-id="<?= $item->id; ?>">
                    <table>
                        <tr>
                            <td class="rl_col_date">
                                <?= date('d.m.Y', strtotime($item->direction->created)); ?>
                            </td>
                            <td class="rl_col_service">
                                <div class="history-item-title" title="<?= $item->price->title; ?>">
                                    <?= $item->price->title; ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            <?php } ?>
        </div>
    </div>
<?php } else { ?>
    <div class="master-history-empty"><i class="fa fa-warning"></i><br/>Пациент не имеет направлений</div>
<?php } ?>
