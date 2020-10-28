<div class="graph-type-control no-print">
    <span class="btn btn-sm btn-default" data-type="line">Линии</span>
    <span class="btn btn-sm btn-default" data-type="column">Колонки</span>
    <span class="btn btn-sm btn-default" data-type="area">Области</span>
</div>

<div class="chart mt10">
    <div id="graph-1" style="height: 320px;border: 1px #ECEFF3 solid;"></div>
</div>

<table class="data-table mt10">
    <thead>
        <tr>
            <th class="col_time" style="width: 145px;"><span>Время<br/>Дата</span></th>
            <th class="text-right"><span>Количество<br/>звонков</span></th>
            <th class="text-right"><span>Количество<br/>пропущенных</span></th>
            <th class="text-right"><span>Количество<br/>принятых</span></th>
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
            'waiting' => 0,
            'longest' => 0
        ];
        foreach ($periods as $period) {
            $total['calls'] += $period['data']['calls'];
            $total['abandoned'] += $period['data']['abandoned'];
            $total['completed'] += $period['data']['completed'];
            $total['waiting'] += $period['data']['waiting'];
            if ($period['data']['longest'] > $total['longest']) {
                $total['longest'] = $period['data']['longest'];
            }
            ?>
            <tr>
                <td class="col_time"><?= date('d.m.Y H:i', strtotime($period['time'])); ?></td>
                <td class="text-right col_calls"><?= $period['data']['calls']; ?></td>
                <td class="text-right col_abandoned"><?= $period['data']['abandoned']; ?></td>
                <td class="text-right col_completed"><?= $period['data']['completed']; ?></td>
                <td class="text-right"><?= gmdate('i:s', $period['data']['waiting']);?></td>                
                <td class="text-right"><?= gmdate('i:s', $period['data']['longest']);?></td>
            </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <td class="text-right"></td>
            <td class="text-right"><strong><?= $total['calls']; ?></strong></td>
            <td class="text-right"><strong><?= $total['abandoned']; ?></strong></td>
            <td class="text-right"><strong><?= $total['completed']; ?></strong></td>
            <td class="text-right"><strong><?= gmdate('i:s', floor($total['waiting'] / count($periods)));?></strong></td>
            <td class="text-right"><strong><?= gmdate('i:s', $total['longest']);?></strong></td>
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
</style>


<?php
$graphData = [];

foreach ($periods as $period) {
    $graphData[] = [
        'time' => $period['time'],
        'calls' => $period['data']['calls'],
        'abandoned' => $period['data']['abandoned'],
        'completed' => $period['data']['completed']
    ];
}
?>

<script>

    var chartsList = {};
    var group_type = <?= $searchModel->group_type; ?>;

    function chart1(data) {
        var chart = new AmCharts.AmSerialChart();

        chart.addListener('drawn', function (event) {
            $('a[title="JavaScript charts"]').hide();
        });

        chart.dataProvider = data;
        chart.validateData();
        chart.categoryField = "time";
        chart.creditsPosition = 'top-right';

        if (!data.length) {
            chart.addLabel(0, '50%', 'Данные отсутствуют', 'center');
        }

        // AXES X
        var categoryAxis = chart.categoryAxis;
        categoryAxis.gridAlpha = 0.07;
        categoryAxis.axisColor = "#DADADA";
        categoryAxis.startOnAxis = true;
        categoryAxis.labelFunction = function (valueText, serialDataItem, categoryAxis) {
            var m = moment(serialDataItem.category);
            if (group_type == 0) {
                return m.format('HH:mm');
            }
            if (group_type == 1) {
                return m.format('DD.MM.YYYY');
            }
        };
        // AXES Y
        var valueAxis = new AmCharts.ValueAxis();
        valueAxis.axisColor = "#DADADA";
        valueAxis.gridAlpha = 0.07;
        valueAxis.integersOnly = true;
        chart.addValueAxis(valueAxis);

        // first graph
        var graph = new AmCharts.AmGraph();
        graph.type = "line";
        graph.title = "Все звонки";
        graph.valueField = "calls";
        graph.bullet = "circle";
        graph.bulletSize = 6;
        graph.lineAlpha = 1;
        graph.lineColor = "#FDCC35";
        graph.fillAlphas = 0;
        chart.addGraph(graph);

        // third graph
        graph = new AmCharts.AmGraph();
        graph.type = "line";
        graph.title = "Отвеченные звонки";
        graph.valueField = "completed";
        graph.bullet = "circle";
        graph.bulletSize = 6;
        graph.lineAlpha = 1;
        graph.lineColor = "#62ACE6";
        graph.fillAlphas = 0;
        chart.addGraph(graph);

        // second graph
        graph = new AmCharts.AmGraph();
        graph.type = "line";
        graph.title = "Пропущенные звонки";
        graph.valueField = "abandoned";
        graph.bullet = "circle";
        graph.bulletSize = 6;
        graph.lineAlpha = 1;
        graph.lineColor = "#FC4526";
        graph.fillAlphas = 0;
        chart.addGraph(graph);

        // LEGEND
        var legend = new AmCharts.AmLegend();
        legend.align = "left";
        legend.equalWidths = false;
        chart.addLegend(legend);

        // CURSOR
        var chartCursor = new AmCharts.ChartCursor();
        chartCursor.cursorColor = '#416586';
        chartCursor.zoomable = false;
        chart.addChartCursor(chartCursor);

        // WRITE
        chartsList['graph-1'] = chart;
        changeType(chartsList['graph-1'], chartType);
        chart.write('graph-1');
    }


    function changeType(chart, type) {
        if (type == 'column') {
            chart.valueAxes[0].stackType = '3d';
            chart.categoryAxis.startOnAxis = false;
            $.each(chart.graphs, function (k, v) {
                chart.graphs[k].type = 'column';
                chart.graphs[k].lineAlpha = 0;
                chart.graphs[k].fillAlphas = 1;
            });
        }

        if (type == 'line') {
            chart.valueAxes[0].stackType = 'none';
            chart.categoryAxis.startOnAxis = true;
            $.each(chart.graphs, function (k, v) {
                chart.graphs[k].type = 'line';
                chart.graphs[k].lineAlpha = 1;
                chart.graphs[k].fillAlphas = 0;
            });
        }

        if (type == 'area') {
            chart.valueAxes[0].stackType = 'none';
            chart.categoryAxis.startOnAxis = true;
            $.each(chart.graphs, function (k, v) {
                chart.graphs[k].type = 'smoothedLine';
                chart.graphs[k].lineAlpha = 0;
                chart.graphs[k].fillAlphas = 1;
            });
        }

        chart.validateNow();
    }

    $(document).ready(function () {
        chart1(<?= json_encode($graphData); ?>);

        $('.graph-type-control .btn').each(function () {
            if ($(this).attr('data-type') == chartType) {
                $(this).addClass('active');
            }
        });

        $('.graph-type-control .btn').on('click', function () {
            chartType = $(this).attr('data-type');
            $('.graph-type-control .btn').removeClass('active');
            $(this).addClass('active');
            changeType(chartsList['graph-1'], chartType);
        });
    });
</script>