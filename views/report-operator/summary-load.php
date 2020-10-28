<table class="data-table mt10">
    <thead>
        <tr>
            <th class="col_time" style="width: 200px;"><span>Оператор</span></th>
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
            foreach ($users as $key => $user) {
            ?>
            <tr class="period-tr">
                <td class="col_time"><?= $user['name']; ?></td>
                <td class="text-right col_calls"><?= $user['calls']; ?></td>
                <td class="text-right col_abandoned"><?= $user['abandoned']; ?></td>
                <td class="text-right col_completed"><?= $user['completed']; ?></td>
                <td class="text-right col_outgoing"><?= $user['outgoing_abandon']; ?></td>
                <td class="text-right col_outgoing"><?= $user['outgoing_callme']; ?></td>
                <td class="text-right"><?= gmdate('i:s', $user['waiting']); ?></td>                
                <td class="text-right"><?= gmdate('i:s', $user['longest']); ?></td>
            </tr>
        <?php } ?>
    </tbody>
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