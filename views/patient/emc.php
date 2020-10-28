<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\widgets\ListView;

$this->title = 'ЭМК пациента ' . $patient->initials;
?>

<script>
    var protocolData = {};
</script>
<div class="row">
    <div class="col-md-12">
        <div class="bb-form-container">
            <h1>
                <?= $this->title; ?>
                <span class="subheader"><?= $patient->birthdayPrint; ?></span>
            </h1>

            <?php if ($dirs) { ?>
                <div class="emc-ctr">
                    <div class="col-xs-8 form-left">
                        <?php if ($dirs) { ?>
                            <div class="view-protocol-ctr">
                                <div class="head ml20 mr20">
                                    <div class="top-panel">
                                        <div class="row">
                                            <div class="col-xs-8 text-left">
                                                &nbsp;
                                            </div>
                                            <div class="col-xs-4 text-right pt5">
                                                <div class="action-group action-group-inversed clearfix">
                                                    <a href="#" target="_blank" class="action action-print"><span class="action-icon-print"></span></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="content content-filling scroll-ctr">
                                    <div class="content-ctr clearfix"></div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="col-xs-4 form-right">

                        <div class="scroll-ctr">
                            <div class="protocol-history">
                                <?php if ($dirs) { ?>
                                    <?php foreach ($dirs as $item) { ?>
                                        <div class="item item-exist clearfix" data-id="<?= $item->id; ?>">
                                            <div class="pic pull-left">
                                                <span class="<?= $priceTypeData['icons'][$item->price->type]; ?>"></span>
                                            </div>
                                            <div class="content">
                                                <div class="clearfix">
                                                    <div class="col-xs-5">
                                                        <?= date('d.m.Y', strtotime($item->reception->created)); ?> в <?= date('H:i', strtotime($item->reception->created)); ?>
                                                    </div>
                                                    <div class="col-xs-7">
                                                        <?= $item->reception->doctor->initials; ?>
                                                    </div>
                                                </div>
                                                <div class="service">
                                                    <div>
                                                        <?= $item->price->title; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="protocol-html hidden">
                                                <?= $item->reception->html; ?>
                                            </div>
                                            <?php
                                            $vars = [];
                                            foreach ($item->reception->template->templateVars as $var) {
                                                $vars[$var['id']] = $var->toArray();
                                            };
                                            ?>
                                            <script>
                                                protocolData[<?= $item->id; ?>] = {
                                                    vars: <?= json_encode($vars); ?>,
                                                    values: <?= json_encode($item->reception->templateValues); ?>
                                                };
                                            </script>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </div>
            <?php } else { ?>
                <div class="ml20">нет записей</div>
            <?php } ?>
        </div>
    </div>
</div>

<style>
    .emc-ctr .form-left {
        background: #fff;
        height: 100%;
        position: relative;
    }

    .emc-ctr .form-right {
        height: 100%;
        position: relative;
        overflow: hidden;
        background: #F5F7F9;
    }

    .emc-ctr .view-protocol-ctr {
        position: absolute;
        left: 0px;
        top: 0px;
        bottom: 0px;
        right: 0px;
    }

    .emc-ctr .view-protocol-ctr .scroll-ctr {
        position: absolute;
        top: 48px;
        bottom: 0px;
        left: 20px;
        right: 20px;
    }

    .emc-ctr .view-protocol-ctr .scroll-ctr .jspPane {
        width: 100% !important;
    }

    .emc-ctr .view-protocol-ctr .scroll-ctr .jspVerticalBar {
        right: 0px;
    }

    .emc-ctr .view-protocol-ctr .content-ctr {
        border-left: 1px #ECEFF3 solid;
        border-right: 1px #ECEFF3 solid;
        border-bottom: 1px #ECEFF3 solid;
    }

    .emc-ctr .form-right .scroll-ctr {
        position: absolute;
        top: 0px;
        bottom: 0px;
        left: 0px;
        right: 15px;
    }

    .emc-ctr .form-right .scroll-ctr .jspPane {
        width: 100% !important;
    }

    .emc-ctr .form-right .scroll-ctr .jspVerticalBar {
        right: 0px;
    }
</style>

<script>

    function formHeight() {
        var $ctr = $('.emc-ctr');
        $ctr.height($(window).height() - $ctr.offset().top);
    }

    var emcProtocol = {
        _getValue: function (id, uid, dirId) {
            var value = false;
            if (protocolData[dirId].values.hasOwnProperty(uid)) {
                value = protocolData[dirId].values[uid].value;
            }
            return value;
        },
        _getType: function (id, dirId) {
            var type = false;
            if (protocolData[dirId].vars.hasOwnProperty(id)) {
                type = protocolData[dirId].vars[id].type;
            }
            return type;
        },
        render: function ($item) {
            var
                    that = this,
                    $ctr = $('.view-protocol-ctr .content-ctr'),
                    dirId = $item.attr('data-id');

            $ctr.html($('.protocol-html', $item).html());
            $('.view-protocol-ctr .head .text-left').text($('.service div', $item).text());
            $('.view-protocol-ctr .head .action-print').attr('href', '/reception/print/' + dirId);

            $('.varbox', $ctr).each(function () {
                var id = $(this).attr('data-id');
                var uid = $(this).attr('data-uid');
                if (!id || !uid) {
                    return;
                }
                if (!$(this).attr('data-name')) {
                    $(this).attr('data-name', $('.name', $(this)).text());
                    $('.name', $(this)).remove();
                }
                var name = $(this).attr('data-name');
                var value = that._getValue(id, uid, dirId);
                if (!$('.value', $(this)).length) {
                    $(this).append('<span class="value"></span>');
                }
                if (value !== false) {
                    if (that._getType(id, dirId) == 'textarea') {
                        value = value.replace(/\n/g, '<br/>');
                    }
                    $('.value', $(this)).html(value);
                } else {
                    $('.value', $(this)).text('<' + name + '>');
                }
            });
        }
    };

    $(document).ready(function () {

        if (Object.keys(protocolData).length) {

            formHeight();
            $(window).on('resize', function () {
                setTimeout(function () {
                    formHeight();
                }, 400);
            });

            $('.view-protocol-ctr .scroll-ctr').jScrollPane({
                autoReinitialise: true,
                verticalGutter: 0,
                hideFocus: true
            });

            $('.emc-ctr .form-right .scroll-ctr').jScrollPane({
                autoReinitialise: true,
                verticalGutter: 0,
                hideFocus: true
            });

            $('.protocol-history .item').on('click', function () {
                if (!$(this).hasClass('active')) {
                    $('.protocol-history .item').removeClass('active');
                    $(this).addClass('active');
                    emcProtocol.render($(this));
                }
            });

            if ($('.protocol-history .item').length) {
                $('.protocol-history .item:eq(0)').trigger('click');
            }

        }
    });
</script>
