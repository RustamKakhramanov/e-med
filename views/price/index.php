<?php
/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\widgets\ListView;
use yii\helpers\Html;

Yii::$app->view->params['bodyClass'] = 'bb-body';
$this->title = 'Прайс';
?>


<div class="row">
    <div class="col-md-12">
        <div class="bb-form-container">
            <h1><?= $this->title; ?></h1>

            <div class="price-search-ctr-form">
                <?=
                $this->render('_search', [
                    'model' => $searchModel
                ]);
                ?>
            </div>

            <div class="clearfix form-ctr price-form">
                <div class="col-xs-8 form-left">
                    <div class="clearfix">
                        <div class="pull-left">
                            <h3 class="pl10">
                                Услуги
                                <span class="subheader items-count-ctr"><?= $countFindRecord; ?></span>
                            </h3>
                        </div>
                        <div class="pull-right clearfix mt15 pr10">
                            <a href="/price/add" class="btn btn-sm btn-primary pull-right ml20"><i class="fa fa-plus mr5"></i>Добавить</a>
                            <a href="#" class="btn btn-sm btn-default pull-right ml20">Выгрузить</a>
                            <a href="#" class="btn btn-sm btn-default pull-right ml20">Импорт</a>
                        </div>
                    </div>

                    <div class="price-items-ctr">

                        <div class="screen list" style="display: block;">
                            <div class="head">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th class="col_name"><a href="#">Наименование</a></th>
                                            <th class="col_cost"><a href="#">Стоимость</a></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="content scroll-ctr">
                                <div class="items">
                                    <?=
                                    $this->render('_rows', [
                                        'dataProvider' => $dataProvider
                                    ]);
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="screen form">
                            <form>
                                <div class="content scroll-ctr">
                                    <div class="pl20 pr20">
                                        <div class="form-group">
                                            <label>Название</label>
                                            <input type="text" class="form-control data-required" name="title"/>
                                        </div>
                                        <div class="form-group">
                                            <label>Название для печати</label>
                                            <input type="text" class="form-control" name="title_print"/>
                                        </div>
                                        <div class="form-group">
                                            <label>Вид операции</label>
                                            <?=
                                            Html::dropDownList('type', null, $types, [
                                                'class' => 'selectpicker data-required',
                                                'prompt' => ' '
                                            ]);
                                            ?>
                                        </div>
                                        <div class="form-group">
                                            <label>Базовая стоимость</label>
                                            <input type="text" class="form-control data-required text-right" name="cost"/>
                                        </div>
                                    </div>

                                    <input type="hidden" name="id"/>
                                </div>
                                <div class="footer">
                                    <button type="submit" class="btn btn-primary">Сохранить</button>
                                    <div class="btn btn-default ml10 btn-cancel-handler">Отменить</div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                <div class="col-xs-4 form-right">
                    <form class="form-add-group">
                        <div class="clearfix">
                            <h3 class="pl10 pull-left">Группы</h3>
                            <div class="btn btn-default btn-create-handler pull-right mt15 mr20">Создать группу</div>
                        </div>

                        <div class="price-groups-ctr">

                            <div class="screen list" style="display: block;">
                                <div class="content scroll-ctr">
                                    <div class="items"></div>
                                </div>
                            </div>

                            <div class="screen form">
                                <div class="content scroll-ctr">
                                    <div class="pl20 pr20">
                                        <div class="form-group">
                                            <label>Название</label>
                                            <input type="text" class="form-control data-required" name="name"/>
                                        </div>
                                        <input type="hidden" name="id"/>
                                        <div class="clearfix">
                                            <button type="submit" class="btn btn-primary">Сохранить</button>
                                            <div class="btn btn-default ml10 btn-cancel-handler">Отменить</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="page-loader"></div>
    </div>
</div>

<script>

    var groups = <?= json_encode($groups); ?>;
    var selected_group = <?= $searchModel->group_id ? $searchModel->group_id : 'null'; ?>;
    var loadingMore = false; //флаг загрузки доп айтемов

    function loading(act) {
        if (typeof act != 'undefined' && !act) {
            $('.page-loader').hide();
        } else {
            $('.page-loader').show();
        }
    }

    function groupsRender() {
        var $screen = showScreen('list', $('.price-groups-ctr'));
        var $items = $('.items', $screen);
        var founded = false;
        $items.html('');

        $items.append('<div class="item"><div class="name">Все</div></div>');
        if (!selected_group) {
            $('.item', $items).addClass('active');
        }

        $.each(groups, function (k, group) {
            var $group = $('<div class="item"></div>');
            $group.append('<div class="name">' + group.name + ' <span>' + group.count + '</span></div>');
            $group.append('<div class="action-group clearfix"><a href="#" class="action action-edit" title="Редактировать"><span class="action-icon-edit"></span></a><a class="action action-delete" href="#" title="Удалить"><span class="action-icon-delete"></span></a></div>')

            if (selected_group && selected_group == group.id) {
                $group.addClass('active');
                founded = true;
            }
            $items.append($group);
        });
    }

    function groupAdd(data) {
        loading();
        $.ajax({
            url: '/price/add-group',
            data: {
                item: data,
                id: selected_group
            },
            type: 'post',
            dataType: 'json',
            success: function (resp) {
                groups = resp;
                groupsRender();
                loading(false);
            },
            error: function () {
                loading(false);
            }
        });
    }

    function formHeight() {
        var $ctr = $('.form-ctr');
        $ctr.height($(window).height() - $ctr.offset().top);
        //storageRender();
    }

    function showScreen(type, $parent) {
        $('.screen', $parent).hide();
        var $screen = $('.screen.' + type, $parent);
        $screen.show();
        var jsp = $('.scroll-ctr', $screen).data('jsp');

        if (jsp) {
            jsp.reinitialise();
        } else {
            $('.scroll-ctr', $screen).jScrollPane({
                autoReinitialise: true,
                verticalGutter: 0,
                hideFocus: true
            });
        }

        return $screen;
    }

    function validateForm($form) {
        var valid = true;
        $('.form-group', $form).removeClass('has-error');
        $('.data-required', $form).each(function () {
            if ($.trim($(this).val()) == '') {
                $(this).closest('.form-group').addClass('has-error');
                valid = false;
            }
        });

        return valid;
    }

    function loadMore() {
        //проверка что уже нет запроса
        if (!loadingMore) {
            //проверка что можно подгружать
            if ((currentPage + 1) <= lastPage) {
                loadingMore = true;
                $('.price-items-ctr .items').append('<div class="loading"></div>');
                var data = $('.price-search-form').serialize();
                $.ajax({
                    url: '/price?' + data + '&page=' + (currentPage + 1),
                    type: 'get',
                    success: function (resp) {
                        $('.price-items-ctr .items .loading').remove();
                        $('.price-items-ctr .items').append(resp);
                        loadingMore = false;
                    }
                });
            }
        }
    }

    $(document).ready(function () {

        formHeight();
        $(window).on('resize', function () {
            setTimeout(function () {
                formHeight();
            }, 400);
        });

        groupsRender();

        $('.btn-create-handler').on('click', function () {
            var $screen = showScreen('form', $('.price-groups-ctr'));
            $('input', $screen).val('');
            //$('input[name="id"]', $screen).val('');
        });

        $('.btn-cancel-handler').on('click', function () {
            var $screen = showScreen('list', $('.price-groups-ctr'));
        });

        $('.form-add-group').on('submit', function () {
            var $form = $(this).closest('form');
            if (validateForm($form)) {
                var data = $form.serializeObject();
                groupAdd(data);
            }

            return false;
        });

        $(document).on('click', '.price-groups-ctr .action-edit', function () {
            var $screen = showScreen('form', $('.price-groups-ctr'));
            var ind = $(this).closest('.item').index() - 1;
            $('input[name="id"]', $screen).val(groups[ind].id);
            $('input[name="name"]', $screen).val(groups[ind].name);

            return false;
        });

        $(document).on('click', '.price-groups-ctr .action-delete', function () {
            var ind = $(this).closest('.item').index() - 1;
            if (selected_group == groups[ind].id) {
                selected_group = null;
            }
            loading();
            $.ajax({
                url: '/price/delete-group',
                data: {
                    id: groups[ind].id
                },
                type: 'post',
                dataType: 'json',
                success: function (resp) {
                    groups = resp;
                    groupsRender();
                    loading(false);
                },
                error: function () {
                    loading(false);
                }
            });

            return false;
        });

        $(document).on('click', '.price-groups-ctr .items .item', function () {
            if (!$(this).hasClass('active')) {
                $('.price-groups-ctr .items .item').removeClass('active');
                $(this).addClass('active');
                var ind = $(this).closest('.item').index() - 1;
                if (ind >= 0) {
                    selected_group = groups[ind].id;
                } else {
                    selected_group = null;
                }
                $('#pricesearch-group_id').val(selected_group);
                $('.price-search-form').submit();
            }
        });

        $(document).on('click', '.price-items-ctr .action-delete', function () {

            bootbox.confirm('Подтвердите удаление', function (result) {
                if (result) {
                    loading();

                    var url = $(this).attr('href');
                    var $item = $(this).closest('.item');

                    $.ajax({
                        url: url,
                        type: 'post',
                        dataType: 'text',
                        success: function (resp) {
                            $item.remove();
                            $('.items-count-ctr').text($('.items-count-ctr').text() - 1);
                            $('.price-groups-ctr .item.active .name span').text($('.price-groups-ctr .item.active .name span').text() - 1);
                            loading(false);
                        }
                    });
                }
            });
            
            return false;
        });

        $('.price-items-ctr .scroll-ctr').jScrollPane({
            autoReinitialise: true,
            verticalGutter: 0,
            hideFocus: true
        }).on('jsp-scroll-y', function (event, scrollPositionY, isAtTop, isAtBottom) {
            if (isAtBottom) {
                loadMore();
            }
        });

    });
</script>