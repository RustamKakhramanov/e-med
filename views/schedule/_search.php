<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\Calendar\Calendar;
use app\components\CalendarWeek\CalendarWeek;
use yii\helpers\ArrayHelper;
?>
<?php

$form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{hint}",
            ],
            'options' => [
                'class' => 'schedule-search-form',
            ],
        ]);
?>

<div class="row">
    <table class="form-table inner">
        <tr>
            <td style="width: 160px;">
                <label>&nbsp;</label>
                <ul class="tabs pull-left clearfix view-switch">
                    <li class="<?php if ($model->type == 'day') echo 'active'; ?>"><a href=".day" data-toggle="tab" data-view="day">День</a></li>
                    <li class="<?php if ($model->type == 'week') echo 'active'; ?>"><a href=".week" data-toggle="tab" data-view="week">Неделя</a></li>
                </ul>
                <div class="hidden">
                    <?=
                    $form->field($model, 'type')->hiddenInput()->label(false);
                    ?>
                </div>
            </td>
            <td style="width: 220px;">
                <div class="calend_day" style="display: <?= ($model->type == 'day') ? 'block' : 'none'; ?>;">
                    <?=
                    $form->field($model, 'date_day')->label('&nbsp;')->widget(Calendar::className(), [
                        'form' => $form,
                        'options' => [
                            'showArrows' => true,
                            'readonly' => true
                        ]
                    ]);
                    ?>
                </div>
                <div class="calend_week" style="display: <?= ($model->type == 'week') ? 'block' : 'none'; ?>;">
                    <?=
                    $form->field($model, 'date_day')->label('&nbsp;')->widget(CalendarWeek::className(), [
                        'form' => $form,
                        'options' => [
                            'showArrows' => true,
                            'handler' => 'weekHahdler'
                        ]
                    ]);
                    ?>
                </div>
            </td>
            <td>
                <?=
                $form->field($model, 'spec')->dropDownList(
                        ArrayHelper::map($specialities, 'id', 'name'), [
                    'class' => 'selectpicker',
                    'data-live-search' => 'true'
                        //'prompt' => 'Все'
                        ]
                );
                ?>
            </td>
            <td>
                <?=
                $form->field($model, 'subdivision_id')->dropDownList(
                        ArrayHelper::map($subdivisions, 'id', 'name'), [
                    'class' => 'selectpicker',
                    'prompt' => 'Все'
                        ]
                );
                ?>
            </td>
            <td>
                <?=
                $form->field($model, 'week_doctor_id')->dropDownList(
                        ArrayHelper::map($doctors, 'id', 'fio'), [
                    'class' => 'selectpicker',
                    'prompt' => 'Все',
                    //'disabled' => '',
                    'data-live-search' => 'true'
                        ]
                );
                ?>
            </td>

            <td style="width: 125px;">
                <div class="form-group">
                    <label>Авто-обновление</label>
                    <div class="input-group input-autoupdate">
                        <select class="selectpicker" id="schedule-autoupdate">
                            <option value="0">Отключено</option>
                            <option value="10">10 секунд</option>
                            <option value="30">30 секунд</option>
                            <option value="60" selected="">1 минута</option>
                        </select>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>

<?php ActiveForm::end(); ?>

<script>
    var scheduleXhr;
    var scheduleAutoLoad;
    var viewUid = '<?=$viewUid;?>';

    function loadData() {
        
        if (typeof scheduleXhr != 'undefined') {
            scheduleXhr.abort();
        }
        
        $('#' + viewUid + ' .schedule-loader').show();
        var search = $('#' + viewUid + ' .schedule-search-form').serialize();

        history.pushState('', '', '/schedule?' + search);

        scheduleXhr = $.ajax({
            url: '/schedule',
            dataType: 'json',
            type: 'get',
            data: search,
            success: function (resp) {
                data = resp;
                schedule.render(stype);
                $('#' + viewUid + ' .schedule-loader').hide();
            },
            error: function () {
                location.reload();
                //schedule._renderError('Не удалось получить данные');
            }
        });
    }

    function weekHahdler(dateText) {
        var $parent = $('#' + viewUid + ' .calend_week .input-datepicker');
        var mom = moment(dateText, 'DD.MM.YYYY').startOf('isoWeek');
        $('input[type="hidden"]', $parent).val(mom.format('DD.MM.YYYY'));
        var start = mom.format('DD MMM');
        var end = mom.add(6, 'days').format('DD MMM');
        $('input.week-text', $parent).val(start + ' - ' + end);
        //$('.dropdown-handler', $parent).dropdown('toggle');
        loadData();
    }

    $(document).ready(function () {

        $('#' + viewUid + ' .calend_day .input-datepicker-ui').datepicker('destroy').datepicker({
            dateFormat: 'dd.mm.yy',
            prevText: '&larr;',
            nextText: '&rarr;',
            showOtherMonths: true,
            changeMonth: false,
            changeYear: false,
            minDate: '<?= date('d.m.Y'); ?>',
            onSelect: function (date) {
                var $parent = $(this).closest('.input-datepicker');
                $('input', $parent).val(date);
                $('.dropdown-handler', $parent).dropdown('toggle');
                loadData();
            }
        });

        $('#' + viewUid + ' .calend_day .range-picker-ctr .control').on('click', function () {
            var $p = $(this).closest('.range-picker-ctr');
            var val = moment($('input.datepicker', $p).val(), 'DD.MM.YYYY');

            if ($(this).hasClass('control-left')) {
                val.subtract(1, 'days');
                var minDate = moment($('.input-datepicker-ui', $p).datepicker('option', 'minDate'), 'DD.MM.YYYY');
                if (val < minDate) {
                    return false;
                }
            } else {
                val.add(1, 'days');
            }

            $('input.datepicker', $p).val(val.format('DD.MM.YYYY'));
            $('.input-datepicker-ui', $p).datepicker('setDate', val.format('DD.MM.YYYY'));
            loadData();
        });

        $('#' + viewUid + ' .calend_week .range-picker-ctr .control').on('click', function () {
            var $p = $(this).closest('.range-picker-ctr');
            var val = moment($('input.datepicker', $p).val(), 'DD.MM.YYYY');
            if ($(this).hasClass('control-left')) {
                val.subtract(7, 'days');
                var minDate = moment($('.input-datepicker-ui', $p).datepicker('option', 'minDate'), 'DD.MM.YYYY');
                if (val < minDate) {
                    return false;
                }
            } else {
                val.add(7, 'days');
            }
            weekHahdler(val.format('DD.MM.YYYY'));
        });

        weekHahdler($('#schedulesearch-date_week').val());

        $(document).on('mousemove', '#' + viewUid + ' .calend_week .ui-datepicker-calendar tr', function () {
            $(this).find('td').addClass('week-hover');
        });
        $(document).on('mouseleave', '#' + viewUid + ' .calend_week .ui-datepicker-calendar tr', function () {
            $(this).find('td').removeClass('week-hover');
        });

        $('#' + viewUid + ' .view-switch a').on('shown.bs.tab', function (e) {
            stype = ($(e.target).attr('data-view') == 'week') ? 'week' : 'day';
            var $docSelect = $('#schedulesearch-week_doctor_id');
            if (stype == 'week') {
                $('#' + viewUid + ' .calend_day, ' + '#' + viewUid + ' .calend_week').toggle();
                $('#schedulesearch-type').val('week');

                $('#schedulesearch-spec, #schedulesearch-subdivision_id').prop('disabled', true).selectpicker('refresh');
                $('option:eq(0)', $docSelect).prop('disabled', true);

                if (!$docSelect.val()) {
                    $docSelect.val($('option:eq(1)', $docSelect).attr('value'));
                }
                $docSelect.selectpicker('refresh');

            } else {
                $('#' + viewUid + ' .calend_day, ' + '#' + viewUid + ' .calend_week').toggle();
                $('#schedulesearch-type').val('day');
                $('#schedulesearch-spec, #schedulesearch-subdivision_id').prop('disabled', false).selectpicker('refresh');
                $('option:eq(0)', $docSelect).prop('disabled', false);
                $docSelect.selectpicker('refresh');
            }
            loadData();
        });

        //trigger week
        if (stype == 'week') {
            var $docSelect = $('#schedulesearch-week_doctor_id');

            $('#schedulesearch-spec, #schedulesearch-subdivision_id').prop('disabled', true).selectpicker('refresh');

            if (!$docSelect.val()) {
                $('option:eq(0)', $docSelect).prop('disabled', true);
                $docSelect.val($('option:eq(1)', $docSelect).attr('value'));
            }
            $docSelect.selectpicker('refresh');
        }

        $('#schedulesearch-week_doctor_id').on('change', function () {
            if ($(this).val()) {
                $('option[value=""]', $(this)).remove();
                $(this).selectpicker('refresh');
                if (!$('#schedulesearch-spec > option[value=""]').length) {
                    $('#schedulesearch-spec').prepend('<option value="">Все</option>');
                }
                $('#schedulesearch-spec').val('').selectpicker('refresh');
            }
        });

        $('#schedulesearch-spec').on('change', function () {
            if ($(this).val()) {
                $('option[value=""]', $(this)).remove();
                $(this).selectpicker('refresh');
                if (!$('#schedulesearch-week_doctor_id > option[value=""]').length) {
                    $('#schedulesearch-week_doctor_id').prepend('<option value="">Все</option>');
                }
                $('#schedulesearch-week_doctor_id').val('').selectpicker('refresh');
            }
        });

        $('#schedule-autoupdate').on('change', function (e) {
            var val = 1 * $(this).val();
            if (val) {
                scheduleAutoLoad = setInterval(function () {
                    loadData();
                }, val * 1000);
            } else {
                clearInterval(scheduleAutoLoad);
            }
        }).trigger('change');

        $('#' + viewUid +  ' .schedule-search-form select:not(#schedule-autoupdate)').on('change', function () {
            loadData();
        });
    });
</script>

<style>
    .calend_week .week-hover a {
        border-color: #fff !important;
    }

    .ui-datepicker td.week-hover span {
        border: 1px #6A859E solid;
        display: inline-block;
        width: 22px;
        height: 22px;
        line-height: 22px;
        border-radius: 22px;
    }

    .week-text.form-control[readonly] {
        background-color: #fff;
    }

    .field-schedulesearch-week_doctor_id .dropdown-menu {
        right: 0px;
    }

    .field-schedulesearch-week_doctor_id .bootstrap-select.btn-group .dropdown-menu.inner {
        overflow-x: hidden;
    }
</style>
