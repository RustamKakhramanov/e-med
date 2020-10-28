(function ($) {
    $.datepicker.regional['ru'] = {
        monthNames: ['Январь', 'Февраль', 'Март', 'Апрель',
            'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь',
            'Октябрь', 'Ноябрь', 'Декабрь'],
        dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
        monthNamesShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
        firstDay: 1
    };
    $.datepicker.setDefaults($.datepicker.regional['ru']);
})(jQuery);
$.fn.selectpicker.defaults = {
    style: 'btn-select',
    dropupAuto: false,
    noneSelectedText: 'Не выбрано',
    noneResultsText: 'Нет результатов по запросу {0}',
    countSelectedText: function (numSelected, numTotal) {
        return (numSelected == 1) ? "{0} выбрано" : "{0} выбрано";
    }
};
bootbox.addLocale('ru', {
    OK: 'Ок',
    CANCEL: 'Отменить',
    CONFIRM: 'Подтвердить'
});
bootbox.setLocale('ru');
moment.locale('ru');

toastr.options = {
    "closeButton": true,
    "debug": false,
    "progressBar": true,
    "preventDuplicates": false,
    "positionClass": "toast-bottom-right",
    "onclick": null,
    "showDuration": "400",
    "hideDuration": "1000",
    "timeOut": "7000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

Inputmask.extendAliases({
    num: {
        alias: 'numeric',
        allowPlus: false,
        allowMinus: true,
        autoGroup: true,
        groupSeparator: ' ',
        autoUnmask: true,
        removeMaskOnSubmit: true,
        digits: 2
    },
    rate: {
        alias: 'numeric',
        allowPlus: false,
        allowMinus: true,
        autoGroup: true,
        groupSeparator: ' ',
        autoUnmask: true,
        removeMaskOnSubmit: true,
        digits: 4
    },
    percent: {
        alias: 'numeric',
        allowPlus: false,
        allowMinus: false,
        autoGroup: true,
        groupSeparator: ' ',
        autoUnmask: true,
        removeMaskOnSubmit: true,
        digits: 4
    }
});

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1)
}

function number_format(number, decimals, dec_point, thousands_sep) {
//  discuss at: http://phpjs.org/functions/number_format/
// original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
// improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
// improved by: davook
// improved by: Brett Zamir (http://brett-zamir.me)
// improved by: Brett Zamir (http://brett-zamir.me)
// improved by: Theriault
// improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
// bugfixed by: Michael White (http://getsprink.com)
// bugfixed by: Benjamin Lupton
// bugfixed by: Allan Jensen (http://www.winternet.no)
// bugfixed by: Howard Yeend
// bugfixed by: Diogo Resende
// bugfixed by: Rival
// bugfixed by: Brett Zamir (http://brett-zamir.me)
//  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
//  revised by: Luke Smith (http://lucassmith.name)
//    input by: Kheang Hok Chin (http://www.distantia.ca/)
//    input by: Jay Klehr
//    input by: Amir Habibi (http://www.residence-mixte.com/)
//    input by: Amirouche
//   example 1: number_format(1234.56);
//   returns 1: '1,235'
//   example 2: number_format(1234.56, 2, ',', ' ');
//   returns 2: '1 234,56'
//   example 3: number_format(1234.5678, 2, '.', '');
//   returns 3: '1234.57'
//   example 4: number_format(67, 2, ',', '.');
//   returns 4: '67,00'
//   example 5: number_format(1000);
//   returns 5: '1,000'
//   example 6: number_format(67.311, 2);
//   returns 6: '67.31'
//   example 7: number_format(1000.55, 1);
//   returns 7: '1,000.6'
//   example 8: number_format(67000, 5, ',', '.');
//   returns 8: '67.000,00000'
//   example 9: number_format(0.9, 0);
//   returns 9: '1'
//  example 10: number_format('1.20', 2);
//  returns 10: '1.20'
//  example 11: number_format('1.20', 4);
//  returns 11: '1.2000'
//  example 12: number_format('1.2000', 3);
//  returns 12: '1.200'
//  example 13: number_format('1 000,50', 2, '.', ' ');
//  returns 13: '100 050.00'
//  example 14: number_format(1e-8, 8, '.', '');
//  returns 14: '0.00000001'

    number = (number + '')
            .replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + (Math.round(n * k) / k)
                        .toFixed(prec);
            };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
            .split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '')
            .length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1)
                .join('0');
    }
    return s.join(dec);
}

function num(input, precision) {
    if (typeof precision == 'undefined') {
        precision = 2;
    }
    var input_str = '' + input;
    var output = 0;
    var p = input_str.split('.');
    output = number_format(p[0]);
    if (p[1]) {
        output = number_format(input, precision);
    }

    return output;
}

function uniqid(prefix, more_entropy) {
//  discuss at: http://phpjs.org/functions/uniqid/
// original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
//  revised by: Kankrelune (http://www.webfaktory.info/)
//        note: Uses an internal counter (in php_js global) to avoid collision
//        test: skip
//   example 1: uniqid();
//   returns 1: 'a30285b160c14'
//   example 2: uniqid('foo');
//   returns 2: 'fooa30285b1cd361'
//   example 3: uniqid('bar', true);
//   returns 3: 'bara20285b23dfd1.31879087'

    if (typeof prefix === 'undefined') {
        prefix = '';
    }

    var retId;
    var formatSeed = function (seed, reqWidth) {
        seed = parseInt(seed, 10)
                .toString(16); // to hex str
        if (reqWidth < seed.length) {
            // so long we split
            return seed.slice(seed.length - reqWidth);
        }
        if (reqWidth > seed.length) {
            // so short we pad
            return Array(1 + (reqWidth - seed.length))
                    .join('0') + seed;
        }
        return seed;
    };
    // BEGIN REDUNDANT
    if (!this.php_js) {
        this.php_js = {};
    }
// END REDUNDANT
    if (!this.php_js.uniqidSeed) {
// init seed with big random int
        this.php_js.uniqidSeed = Math.floor(Math.random() * 0x75bcd15);
    }
    this.php_js.uniqidSeed++;
    // start with prefix, add current milliseconds hex string
    retId = prefix;
    retId += formatSeed(parseInt(new Date()
            .getTime() / 1000, 10), 8);
    // add seed hex string
    retId += formatSeed(this.php_js.uniqidSeed, 5);
    if (more_entropy) {
// for more entropy we add a float lower to 10
        retId += (Math.random() * 10)
                .toFixed(8)
                .toString();
    }

    return retId;
}

(function ($) {
    $.fn.serializeObject = function () {

        var self = this,
                json = {},
                push_counters = {},
                patterns = {
                    "validate": /^[a-zA-Z][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
                    "key": /[a-zA-Z0-9_]+|(?=\[\])/g,
                    "push": /^$/,
                    "fixed": /^\d+$/,
                    "named": /^[a-zA-Z0-9_]+$/
                };
        this.build = function (base, key, value) {
            base[key] = value;
            return base;
        };
        this.push_counter = function (key) {
            if (push_counters[key] === undefined) {
                push_counters[key] = 0;
            }
            return push_counters[key]++;
        };
        $.each($(this).serializeArray(), function () {

            // skip invalid keys
            if (!patterns.validate.test(this.name)) {
                return;
            }

            var k,
                    keys = this.name.match(patterns.key),
                    merge = this.value,
                    reverse_key = this.name;
            while ((k = keys.pop()) !== undefined) {

                // adjust reverse_key
                reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');
                // push
                if (k.match(patterns.push)) {
                    merge = self.build([], self.push_counter(reverse_key), merge);
                }

                // fixed
                else if (k.match(patterns.fixed)) {
                    merge = self.build([], k, merge);
                }

                // named
                else if (k.match(patterns.named)) {
                    merge = self.build({}, k, merge);
                }
            }

            json = $.extend(true, json, merge);
        });
        return json;
    };
})(jQuery);

function firstToUpperCase(str) {
    return str.substr(0, 1).toUpperCase() + str.substr(1);
}

function openModal(user_options) {
    var options = $.extend({
        class: '',
        effect: 'slide-bottom',
        centered: true,
        html: '',
        url: null,
        ajax: {
            type: 'get',
            data: {}
        },
        uid: uniqid(),
        parentUid: '',
        closeOverlay: false,
        closeButton: true,
        onOpen: function () {
        },
        onClose: function () {
        }
    }, user_options);

    var that = this;
    var $modal = $('\n\
        <div class="modal-wrap ' + options.class + (options.centered ? ' centered' : '') + '" data-uid="' + options.uid + '" data-id="modal-' + options.uid + '" data-parent="' + options.parentUid + '">\n\
            <div class="modal-inner ' + options.effect + '"></div>\n\
        </div>\n\
    ');
    $modal.data('options', options);
    $('body').css('overflow', 'hidden');
    $('.modal-wrap').addClass('back');
    $('body').append($modal);

    function loadContent(firstTime) {
        if (options.url) {
            $modal.addClass('centered');
            $('.modal-inner', $modal).addClass('modal-inner-loading');
            setTimeout(function () {
                $('.modal-inner', $modal).addClass('showed');
            }, 50);
            $.ajax({
                url: options.url,
                type: options.ajax.type,
                data: options.ajax.data,
                dataType: 'html',
                success: function (resp) {
                    if (!options.centered) {
                        $modal.removeClass('centered');
                    }
                    if (firstTime) {
                        $('.modal-inner', $modal).removeClass('showed');
                    }
                    $('.modal-inner', $modal).html(resp);
                    if (options.closeButton) {
                        $('.modal-inner', $modal).prepend('<span class="modal-close-btn" title="Закрыть (Esc)"></span>');
                    }
                    if (firstTime) {
                        setTimeout(function () {
                            $('.modal-inner', $modal).addClass('showed');
                        }, 50);
                    }

                    $('.modal-inner', $modal).removeClass('modal-inner-loading');

                    options.onOpen.call(this, $modal);
                },
                error: function () {
                    $('.modal-inner', $modal).removeClass('modal-inner-loading');
                    $('.modal-inner', $modal).html('Произошла ошибка');
                }
            });
        } else {
            $('.modal-inner', $modal).html(options.html);
            if (options.closeButton) {
                $('.modal-inner', $modal).prepend('<span class="modal-close-btn"></span>');
            }
            setTimeout(function () {
                $('.modal-inner', $modal).addClass('showed');
            }, 50);

            options.onOpen.call(this, $modal);
        }
    }

    loadContent(true);

    $modal.on('reload', function () {
        var opt = $(this).data('options');
        if (opt.url) {
            loadContent(false);
        }
    });

    $modal.on('updateData', function (e, data) {
        var opt = $(this).data('options');
        $(this).data('options', $.extend(opt, data));
    });

    if (options.closeOverlay) {
        $modal.on('click', function (event) {
            if (event.target == this) {
                $modal.trigger('close')
            }
        });
    }
    $modal.on('close', function () {
        closeModal($modal, options);
        $modal.off('close');
    });

    $(document).off('keyup.modal-esc').on('keyup.modal-esc', function (event) {
        if (event.keyCode == 27) { //esc
            $('.modal-wrap').last().trigger('close');
        }
    });

    $(document).off('click.modal-close').on('click.modal-close', '.modal-wrap .modal-close-btn', function (event) {
        $('.modal-wrap').last().trigger('close');
    });

    return options.uid;
}

function closeModal($modal, options) {
    $modal.remove();
    options.onClose.call(this, $modal);
    if (!$('.modal-wrap').length) {
        $('body').css('overflow', 'auto');
    } else {
        $('.modal-wrap').last().removeClass('back');
    }
}

//function closeModal(uid) {
//    var $modal = (typeof uid != 'undefined') ? $('.modal-wrap[data-uid="'+uid+'"]') : $('.modal-wrap').last();
//    $modal.trigger('hide').off('hide');    
//    if (!$('.modal-wrap').size()){
//        $('body').css('overflow', 'auto');
//    }
//}

/**
 * компонент tpicker
 */
(function ($) {
    var _data = {
        component: null,
        editedKey: null,
        template: '',
        model: {}
    };
    var methods = {
        init: function (options) {
            var $this = this;
            $this.tpicker.settings = $.extend({
                source: null, //url
                storage: [],
                callbackPick: function () { //pick entity
                },
                renderView: function () { //row render model
                },
                renderEdit: function () { //row render model
                },
                sync: function () { //storage sync
                },
                editCtr: '<a href="#" class="action" data-action="edit"><span class="action-icon-edit"></span></a>',
                saveCtr: '<a href="#" class="action" data-action="save"><span class="action-icon-save"></span></a>',
                deleteCtr: '<a href="#" class="action" data-action="delete"><span class="action-icon-delete"></span></a>',
                cancelCtr: '<a href="#" class="action" data-action="cancel"><span class="action-icon-cancel"></span></a>'
            }, options);
            var data = $this.data('tpicker');
            if (!data) {
                if (!$this.tpicker.settings.source) {
                    $this.tpicker.settings.source = $this.attr('data-url');
                }
                this.data('tpicker', {
                    target: $this
                });
                _data.component = $this;
                this.addClass('loading');
                this.tpicker.settings.sync.call(this);
                $.ajax({
                    url: $this.tpicker.settings.source,
                    type: 'get',
                    dataType: 'html',
                    success: function (html) {
                        _data.template = html;
                        methods._renderItems.apply($this, []);

                        $this.removeClass('loading');
                    }
                });
                $('.add-table-picker-handler', $this).on('click', function () {
                    methods._addRow.apply($this, []);
                    return false;
                });
            }
        },
        pick: function (model) {
            _data.model = model;
        },
        update: function (data) {
            //_data.model = $.extend(data, _data.model);
            $.extend(_data.model, data);
        },
        _getEditedItem: function () {
            return _data.editedKey !== null ? $('.table-picker-items tbody tr:eq(' + _data.editedKey + ')', _data.component) : false;
        },
        _getRowKey: function ($row) {
            return $('.table-picker-items tbody tr', _data.component).index($row);
        },
        _addRow: function () {
            var $this = this;
            methods._storageAdd.apply(this, [{}]);
            $('.table-picker-items tbody', $this).append('<tr></tr>');
            var $row = $('.table-picker-items tbody tr:last', _data.component);
            $row = methods._renderItem.apply(this, [
                methods._getRowKey.call(this, $row),
                function ($row) {
                    $('.action[data-action="edit"]', $row).trigger('click');
                }
            ]);
        },
        _actionEdit: function ($row) {
            if (_data.editedKey !== null) {
                $('.action[data-action="cancel"]', methods._getEditedItem.apply(this, [])).trigger('click');
            }
            _data.editedKey = methods._getRowKey.call(this, $row);
            methods._renderItem.apply(this, [
                methods._getRowKey.call(this, $row)
            ]);
        },
        _actionSave: function ($row) {
            if (Object.keys(_data.model).length) {
                methods._storageEdit.apply(this, [
                    methods._getRowKey.call(this, $row),
                    _data.model
                ]);
            }
            _data.editedKey = null;
            _data.model = {};
            methods._renderItem.apply(this, [
                methods._getRowKey.call(this, $row)
            ]);
        },
        _actionDelete: function ($row) {
            methods._storageRemove.apply(this, [
                methods._getRowKey.call(this, $row)
            ]);
            $row.remove();
        },
        _actionCancel: function ($row) {
            _data.editedKey = null;
            methods._renderItem.apply(this, [
                methods._getRowKey.call(this, $row)
            ]);
        },
        _renderItems: function () {
            var $this = this;
            $.each(this.tpicker.settings.storage, function (key, entity) {
                $('.table-picker-items tbody', $this).append('<tr></tr>');
                methods._renderItem.apply($this, [key]);
            });
        },
        _renderItem: function (key, callback) {
            var $this = this,
                    $row = $('.table-picker-items tbody tr:eq(' + key + ')');
            $row.replaceWith($(_data.template));
            $row = $('.table-picker-items tbody tr:eq(' + key + ')');
            if (_data.editedKey === key) {
                $row.addClass('edit-mode');
                this.tpicker.settings.renderEdit.call(this, $row, $.extend({}, this.tpicker.settings.storage[key]));
                $('.action-group', $row).html('').append(this.tpicker.settings.saveCtr).append(this.tpicker.settings.cancelCtr);
            } else {
                this.tpicker.settings.renderView.call(this, $row, this.tpicker.settings.storage[key]);
                $('.action-group', $row).html('').append(this.tpicker.settings.editCtr).append(this.tpicker.settings.deleteCtr);
            }
            //bind actions
            $(document).on('click', '.table-picker-item .action-group .action', function () {
                if ($(this).closest('.table-picker-item').get(0) == $row.get(0)) {
                    var action = '_action' + capitalize($(this).attr('data-action'));
                    methods[action].apply($this, [$row]);
                    return false;
                }
            });
            methods._recalcCounter.apply(this, []);

            if (typeof callback !== 'undefined') {
                callback.call(this, $row);
            }
            return $row;
        },
        _recalcCounter: function () {
            var i = 0;
            $('.table-picker-items tbody tr', this).each(function () {
                i++;
                $('.td-counter', $(this)).text(i);
                $(this).removeClass('odd');
                if (i % 2 == 0) {
                    $(this).addClass('odd');
                }
            });
            $('.header-counter', this).text(i);
        },
        _storageAdd: function (entry) {
            this.tpicker.settings.storage.push(entry);
            this.tpicker.settings.sync.call(this);
        },
        _storageRemove: function (key) {
            this.tpicker.settings.storage.splice(key, 1);
            this.tpicker.settings.sync.call(this);
        },
        _storageEdit: function (key, entry) {
            $.extend(this.tpicker.settings.storage[key], entry);
            this.tpicker.settings.sync.call(this);
        }
    };
    $.fn.tpicker = function (method) {
        if (methods[method]) {
            return methods[ method ].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error(method);
        }
    };
    $.fn.tpicker.settings = {};
})(jQuery);

function pad(number, width) {
    width -= number.toString().length;
    if (width > 0) {
        return new Array(width + (/\./.test(number) ? 2 : 1)).join('0') + number;
    }
    return number + ""; // always return a string
}

(function ($) {
    var _data = {};
    var methods = {
        init: function (options) {
            var $this = this;
            $this.relationPicker.settings = $.extend({
                url_picker: null,
                url_ac: null,
                event_name: '',
                min_length: 1,
                callbackPick: function () {
                }
            }, options);
            var rlData = $this.data('relationPicker');
            if (!rlData) {
                rlData = {
                    uid: $this.attr('data-id'),
                    target: $this,
                    options: $this.relationPicker.settings
                };
                $this.data('relationPicker', rlData);
                $('.item-open-picker', $this).on('click', function () {
                    var u = rlData.options.url_picker;
                    u += u.indexOf('?') === -1 ? ('?target=' + rlData.uid) : ('&target=' + rlData.uid);
                    openModal({
                        url: u
                    });
                });
                $('.item-clear', $this).on('click', function () {
                    $('.target_value', $this).val('').attr('data-text', '').trigger('change');
                    $('.search_input', $this).val('');
                });
                $('.search_input', $this).each(function () {
                    var $elem = $(this);
                    var engine = new Bloodhound({
                        remote: {
                            url: $this.relationPicker.settings.url_ac,
                            wildcard: '_QUERY_',
                            rateLimitBy: 'throttle'
                        },
                        datumTokenizer: Bloodhound.tokenizers.whitespace,
                        queryTokenizer: Bloodhound.tokenizers.whitespace
                    });
                    $elem.typeahead({
                        minLength: rlData.options.min_length
                    }, {
                        source: engine.ttAdapter(),
                        name: 'usersList',
                        display: 'name',
                        templates: {
                            empty: function (context) {
                                return '<div class="list-group search-results-dropdown"><span class="list-group-item">Нет результатов по запросу &laquo;' + context.query + '&raquo;</span></div>';
                            },
                            header: [
                                '<div class="list-group search-results-dropdown">'
                            ],
                            suggestion: function (data) {
                                return '<span class="list-group-item">' + data.name + '</span>';
                            }
                        }
                    }).on('typeahead:select', function (ev, suggestion) {
                        var $e = $('.target_value', $this);
                        $e.val(suggestion.id).attr('data-text', suggestion.name).trigger('change');
                        //$e.typeahead('val', suggestion.id).attr('data-text', suggestion.name).trigger('change');
                    }).on('typeahead:asyncrequest', function () {
                        $(this).closest('.search_input-ctr').addClass('search_input-ctr__loading');
                    }).on('typeahead:asynccancel typeahead:asyncreceive', function () {
                        $(this).closest('.search_input-ctr').removeClass('search_input-ctr__loading');
                    }).on('typeahead:idle', function (ev) {
                        var $e = $('.target_value', $this);
                        if ($e.attr('data-text') != $(this).val()) {
                            $e.val('').attr('data-text', '').trigger('change');
                            $(this).val('');
                        }
                    });
                });
                $(document).on($this.relationPicker.settings.event_name + '.' + rlData.uid, function (e, data) {
                    $('.target_value', $this).val(data.id).attr('data-text', data.name).trigger('change');
                    $('.search_input', $this).typeahead('val', data.name);
                });
            }
        },
        destroy: function () {
            var $this = this;
            $('.item-open-picker, .item-clear', $this).off('click');
            $('.search_input', $this).off('typeahead:select blur');
            $('.search_input', $this).typeahead('destroy');
            var rlData = $this.data('relationPicker');
            $(document).off($this.relationPicker.settings.event_name + '.' + rlData.uid);
            $this.removeData('relationPicker');
        }
    };
    $.fn.relationPicker = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error(method);
        }
    };
    $.fn.relationPicker.settings = {};
})(jQuery);


jQuery(document).ready(function ($) {

    $('.input-datepicker-ui').datepicker({
        dateFormat: 'dd.mm.yy',
        prevText: '&larr;',
        nextText: '&rarr;',
        showOtherMonths: true,
        changeMonth: true,
        changeYear: true,
        yearRange: '1950:2020',
        onSelect: function (date) {
            var $parent = $(this).closest('.input-datepicker');
            $('input', $parent).val(date).trigger('change');
            $('.dropdown-handler', $parent).dropdown('toggle');
        }
    });
    $('.input-datepicker .dropdown-menu').on('click', function (e) {
        e.stopPropagation();
    });
    $('.selectpicker').selectpicker({
        style: 'btn-select'
    });
    $(document).on('click', '.form-submit-handler', function () {
        $(this).closest('form').submit();
    });
    $('#sidebar-wrapper').jScrollPane({
        autoReinitialise: true,
        verticalGutter: 0,
        hideFocus: true
    });

});

function declOfNum(number, titles) {  
    cases = [2, 0, 1, 1, 1, 2];
    return titles[ (number%100>4 && number%100<20)? 2 : cases[(number%10<5)?number%10:5] ];
}