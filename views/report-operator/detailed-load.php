<?php
$periods = $data['periods'];
?>

<table class="data-table mt10">
    <thead>
        <tr>
            <th class="col_time" style="width: 200px;"><span>Дата<br/>Время</span></th>
            <th class="text-right"><span>Количество<br/>звонков</span></th>
            <th class="text-right"><span>Количество<br/>пропущенных</span></th>
            <th class="text-right"><span>Количество<br/>принятых</span></th>
            <th class="text-right"><span>Исходящие<br/>(пропущенные)</span></th>
            <th class="text-right"><span>Исходящие<br/>(с сайта)</span></th>
            <th class="text-right"><span>Среднее время<br/>разговора (сек)</span></th>
            <th class="text-right"><span>Максимальное<br/>разговор (сек)</span></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $total = [
            'calls' => 0,
            'abandoned' => 0,
            'completed' => 0,
            'waiting_count' => 0,
            'waiting' => 0,
            'longest' => 0,
            'outgoing_abandon' => 0,
            'outgoing_callme' => 0
        ];
        foreach ($periods as $pKey => $period) {
            $total['calls'] += $period['calls'];
            $total['abandoned'] += $period['abandoned'];
            $total['completed'] += $period['completed'];
            $total['outgoing_abandon'] += $period['outgoing_abandon'];
            $total['outgoing_callme'] += $period['outgoing_callme'];

            if ($period['sess']) {
                $total['waiting_count'] ++;
                $total['waiting'] += $period['waiting'];
            }

            if ($period['longest'] > $total['longest']) {
                $total['longest'] = $period['longest'];
            }
            ?>
            <tr class="period-tr <?= (!count($period['sess'])) ? 'no-sess' : 'has-sess'; ?>">
                <td class="col_time">
                    <?= date('d.m.Y', strtotime($pKey)); ?>
                    <?php if ($period['duration']) { ?>
                        <span class="day-duration"><?= $period['duration']; ?></span>
                    <?php } ?>
                </td>
                <td class="text-right col_calls"><?= $period['calls']; ?></td>
                <td class="text-right col_abandoned"><?= $period['abandoned']; ?></td>
                <td class="text-right col_completed"><?= $period['completed']; ?></td>
                <td class="text-right col_outgoing"><?= $period['outgoing_abandon']; ?></td>
                <td class="text-right col_outgoing"><?= $period['outgoing_callme']; ?></td>
                <td class="text-right"><?= gmdate('i:s', $period['waiting']); ?></td>                
                <td class="text-right"><?= gmdate('i:s', $period['longest']); ?></td>
            </tr>
            <?php if ($period['sess']) { ?>
                <tr class="period-sess-tr hidden">
                    <td colspan="8" class="pt0 pb0">
                        <table class="data-table">
                            <?php
                            $counter = 0;
                            foreach ($period['sess'] as $sess) {
                                $counter++;
                                ?>
                                <tr>
                                    <td class="col_time">
                                        <span class="sess-time"><?= date('H:i', strtotime($sess['start'])); ?> &ndash; <?= date('H:i', strtotime($sess['end'])); ?></span>
                                        <span class="day-duration"><?= $sess['duration']; ?></span>

                                    </td>
                                    <td class="text-right col_calls"><?= $sess['calls']; ?></td>
                                    <td class="text-right col_abandoned"><?= $sess['abandoned']; ?></td>
                                    <td class="text-right col_completed"><?= $sess['completed']; ?></td>
                                    <td class="text-right col_outgoing"><?= $sess['outgoing_abandon']; ?></td>
                                    <td class="text-right col_outgoing"><?= $sess['outgoing_callme']; ?></td>
                                    <td class="text-right"><?= gmdate('i:s', $sess['waiting']); ?></td>                
                                    <td class="text-right"><?= gmdate('i:s', $sess['longest']); ?></td>
                                </tr>
                            <?php } ?>
                        </table>
                    </td>
                </tr>
                <tr></tr>
            <?php } ?>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <td class="text-right"><strong><?= $data['monthDuration']; ?></strong></td>
            <td class="text-right"><strong><?= $total['calls']; ?></strong></td>
            <td class="text-right"><strong><?= $total['abandoned']; ?></strong></td>
            <td class="text-right"><strong><?= $total['completed']; ?></strong></td>
            <td class="text-right"><strong><?= $total['outgoing_abandon']; ?></strong></td>
            <td class="text-right"><strong><?= $total['outgoing_callme']; ?></strong></td>
            <td class="text-right"><strong><?= $total['waiting_count'] ? gmdate('i:s', floor($total['waiting'] / $total['waiting_count'])) : ''; ?></strong></td>
            <td class="text-right"><strong><?= gmdate('i:s', $total['longest']); ?></strong></td>
        </tr>
    </tfoot>
</table>

<style>
    .col_calls {
        color: #FDCC35 !important;
    }
    .col_abandoned {
        color: #FC4526 !important;
    }
    .col_completed {
        color: #62ACE6 !important;
    }
    .col_outgoing {
        color: #008C70 !important;
    }

    .no-sess td {
        color: #ccc !important;
    }

    .day-duration {
        color: #416586;
        display: inline-block;
        font-size: 12px;
        vertical-align: top;
        margin: 2px 0px 0px 5px;
    }

    .has-sess > td {
        cursor: pointer;
    }

    .period-sess-tr .data-table {
        width: 100%;
        table-layout: fixed;        
    }

    .period-sess-tr .data-table td {
        background: #F9F6E8 !important;    
        padding: 5px 10px;
    }

    .period-sess-tr .data-table .col_time {
        width: 200px;
        font-size: 13px;
        padding-left: 10px;
    }

    .period-sess-tr .data-table .day-duration {
        margin-top: 0px;
    }

    .period-sess-tr .data-table .sess-time {
        font-size: 12px;
    }

</style>

<script>

    $(document).ready(function () {
        $('.period-tr.has-sess').on('click', function () {
            $(this).next('tr').toggleClass('hidden');
        });
    });
</script>