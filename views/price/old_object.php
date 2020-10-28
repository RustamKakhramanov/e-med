//    var storage = {
//        data: <?= json_encode($data); ?>,
//        types: <?= json_encode($types); ?>,
//        adding_item: false,
//        adding_group: false,
//        selected_group: null,
//        render: function () {
//            this.renderLeft();
//            this.renderRight();
//            this.scrollInit();
//            this.bind();
//        },
//        renderLeft: function () {
//            var $ctr = $('.price-items-ctr');
//            $ctr.html('');
//
//            if (!this.data.length) {
//                $ctr.append('<div class="text-muted">Нет услуг</div>');
//            } else {
//
//            }
//
//            if (this.selected_group) {
//                if (this.adding_item) {
//
//                } else {
//                    $('.add-ctr', $ctr).html('<div class="btn btn-default add-item-handler">Добавить услугу</div>');
//                }
//            }
//        },
//        createItemForm: function () {
//            var $form = $('<form></form>');
//            $form.append('<div class="form-group"><label>Название</label><input type="text" class="form-control data-required" name="title"/></div>');
//            $form.append('<div class="form-group"><label>Название для печати</label><input type="text" class="form-control" name="title_print"/></div>');
//
//            var $select = $('<select class="selectpicker" name="type"></select>');
//            $.each(this.types, function (k, v) {
//                $select.append('<option value="' + k + '">' + v + '</option>');
//            });
//
//            var $group = $('<div class="form-group"><label>Вид операции</label></div>');
//            $group.append($select);
//            $form.append($group);
//            $form.append('<div class="form-group"><label>Базовая стоимость</label><input type="text" class="form-control data-required text-right" name="cost"/></div>');
//
//            return $form;
//        },
//        createGroupForm: function () {
//            var $form = $('<form></form>');
//            $form.append('<div class="form-group"><label>Название</label><input type="text" class="form-control data-required" name="title"/></div>');
//
//            return $form;
//        },
//        renderRight: function () {
//            var $ctr = $('.price-groups-ctr');
//            $ctr.html('');
//            var $content = $('<div class="content scroll-ctr"></div>');
//            var $footer = $('<div class="footer"></div>');
//            if (!this.data.length) {
//                this.adding_group = true;
//            }
//
//            if (this.adding_group) {
//                $content.append(this.createGroupForm());
//                $footer.append('<div class="btn btn-primary add-group-submit">Сохранить</div>');
//                $footer.append('<div class="btn btn-default ml10 add-group-cancel">Отменить</div>');
//            }
//
//            $ctr.append($content);
//            $ctr.append($footer);
//        },
//        validateForm: function ($form) {
//            var valid = true;
//            $('.form-group', $form).removeClass('has-error');
//            $('.data-required', $form).each(function () {
//                if ($.trim($(this).val()) == '') {
//                    $(this).closest('.form-group').addClass('has-error');
//                    valid = false;
//                }
//            });
//            
//            return valid;
//        },
//        bind: function () {
//            var that = this;
//            
//            $('.price-groups-ctr form').on('submit', function(){
//                if (that.validateForm($(this))) {
//                    
//                }
//                return false;
//            });
//            
//            $('.price-groups-ctr .add-group-submit').on('click', function () {
//                $('.price-groups-ctr form').submit();
//            });
//        },
//        scrollInit: function () {
//            $('.price-form .scroll-ctr').jScrollPane({
//                autoReinitialise: true,
//                verticalGutter: 0,
//                hideFocus: true
//            });
//        }
//    };