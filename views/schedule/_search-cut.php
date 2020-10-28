<?php
/* @var $model \app\models\Event */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\Calendar\Calendar;
use app\components\CalendarWeek\CalendarWeek;
use yii\helpers\ArrayHelper;
?>
<?php
$formUid = uniqid();
$form = ActiveForm::begin([
            'id' => $formUid,
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
                    <li class="<?php if ($model->type == 'day') echo 'active'; ?>"><a href="#<?= $viewUid; ?> .day" data-toggle="tab" data-view="day">День</a></li>
                    <li class="<?php if ($model->type == 'week') echo 'active'; ?>"><a href="#<?= $viewUid; ?> .week" data-toggle="tab" data-view="week">Неделя</a></li>
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
                            'handler' => 'cut_weekHahdler'
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
        </tr>
    </table>
</div>

<?php ActiveForm::end(); ?>

<script>
    var cut_stype = 'day';
    var cut_scheduleXhr;
    var cut_scheduleAutoLoad;
    var cut_form = '#<?= $formUid; ?>';
    var cut_viewUid = '<?= $viewUid; ?>';
    //данные для таблицы расписания
    var cut_data = <?= json_encode($data); ?>;

    function cut_loadData() {
        if (typeof cut_scheduleXhr != 'undefined') {
            cut_scheduleXhr.abort();
        }

        $('#' + cut_viewUid + ' .schedule-loader').show();
        var search = $(cut_form).serialize();

        //history.pushState('', '', '/schedule?' + search);
        console.log(search);
        cut_scheduleXhr = $.ajax({
            url: '/schedule/cut-ajax/<?= $event->id; ?>',
            dataType: 'json',
            type: 'get',
            data: search,
            success: function (resp) {
                console.log(resp);
                console.log(cut_stype);
                cut_data = resp;
                cut_schedule.render(cut_stype);
                $('#' + cut_viewUid + ' .schedule-loader').hide();
            },
            error: function () {
                location.reload();
                //schedule._renderError('Не удалось получить данные');
            }
        });
    }

    function cut_weekHahdler(dateText) {
        var $parent = $(cut_form + ' .calend_week .input-datepicker');
        var mom = moment(dateText, 'DD.MM.YYYY').startOf('isoWeek');
        $('input[type="hidden"]', $parent).val(mom.format('DD.MM.YYYY'));
        var start = mom.format('DD MMM');
        var end = mom.add(6, 'days').format('DD MMM');
        $('input.week-text', $parent).val(start + ' - ' + end);
        cut_loadData();
    }

    $(document).ready(function () {

        $(cut_form + ' .calend_day .input-datepicker-ui').datepicker('destroy').datepicker({
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
                cut_loadData();
            }
        });

        $(cut_form + ' .calend_day .range-picker-ctr .control').on('click', function () {
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
            cut_loadData();
        });

        $(cut_form + ' .calend_week .range-picker-ctr .control').on('click', function () {
            var $p = $(this).closest('.range-picker-ctr');
            var val = moment($('input.datepicker', $p).val(), 'DD.MM.YYYY');
            console.log($p);
            if ($(this).hasClass('control-left')) {
                val.subtract(7, 'days');
                var minDate = moment($('.input-datepicker-ui', $p).datepicker('option', 'minDate'), 'DD.MM.YYYY');
                if (val < minDate) {
                    return false;
                }
            } else {
                val.add(7, 'days');
            }
            cut_weekHahdler(val.format('DD.MM.YYYY'));
        });


        cut_weekHahdler($('#schedulesearchmodal-date_week').val());

        $(document).on('mousemove', cut_form + ' .calend_week .ui-datepicker-calendar tr', function () {
            $(this).find('td').addClass('week-hover');
        });
        $(document).on('mouseleave', cut_form + ' .calend_week .ui-datepicker-calendar tr', function () {
            $(this).find('td').removeClass('week-hover');
        });

        $(cut_form + ' .view-switch a').on('shown.bs.tab', function (e) {
            cut_stype = ($(e.target).attr('data-view') == 'week') ? 'week' : 'day';
            var $docSelect = $('#schedulesearchmodal-week_doctor_id');            
            if (cut_stype == 'week') {
                $(cut_form + ' .calend_day, ' + cut_form + ' .calend_week').toggle();
                $('#schedulesearchmodal-type').val('week');

                $('#schedulesearchmodal-spec, #schedulesearchmodal-subdivision_id').prop('disabled', true).selectpicker('refresh');
                $('option:eq(0)', $docSelect).prop('disabled', true);

                if (!$docSelect.val()) {
                    $docSelect.val($('option:eq(1)', $docSelect).attr('value'));
                }
                $docSelect.selectpicker('refresh');

            } else {
                $(cut_form + ' .calend_day, ' + cut_form + ' .calend_week').toggle();
                $('#schedulesearchmodal-type').val('day');
                $('#schedulesearchmodal-spec, #schedulesearchmodal-subdivision_id').prop('disabled', false).selectpicker('refresh');
                $('option:eq(0)', $docSelect).prop('disabled', false);
                $docSelect.selectpicker('refresh');
            }
            
            cut_loadData();
        });

        //trigger week
//        if (cut_stype == 'week') {
//            var $docSelect = $('#schedulesearch-week_doctor_id');
//
//            $('#schedulesearch-spec, #schedulesearch-subdivision_id').prop('disabled', true).selectpicker('refresh');
//
//
//            if (!$docSelect.val()) {
//                $('option:eq(0)', $docSelect).prop('disabled', true);
//                $docSelect.val($('option:eq(1)', $docSelect).attr('value'));
//            }
//            $docSelect.selectpicker('refresh');
//        }

        $('#schedulesearchmodal-week_doctor_id').on('change', function () {
            if ($(this).val()) {
                $('option[value=""]', $(this)).remove();
                $(this).selectpicker('refresh');
                if (!$('#schedulesearchmodal-spec > option[value=""]').length) {
                    $('#schedulesearchmodal-spec').prepend('<option value="">Все</option>');
                }
                $('#schedulesearchmodal-spec').val('').selectpicker('refresh');
            }
        });

        $('#schedulesearchmodal-spec').on('change', function () {
            if ($(this).val()) {
                $('option[value=""]', $(this)).remove();
                $(this).selectpicker('refresh');
                if (!$('#schedulesearchmodal-week_doctor_id > option[value=""]').length) {
                    $('#schedulesearchmodal-week_doctor_id').prepend('<option value="">Все</option>');
                }
                $('#schedulesearchmodal-week_doctor_id').val('').selectpicker('refresh');
            }
        });

        $(cut_form + ' select').on('change', function () {
            cut_loadData();
        });
        
        

        //cut_loadData();
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
