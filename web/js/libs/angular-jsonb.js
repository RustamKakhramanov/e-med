function AngularJsonb(moduleApp, nameCtrl, dataStructure, dataValue)
{
    //variables
    var $this = this;
    var selectorForm = '[ng-controller="' + nameCtrl + '"]';
    var selectorSelectpicker = 'select[selectpicker][ng-model][required]';
    var selectorInputmask = 'input[input-mask][ng-model][required]';
    var selectorOtherField = 'input:not([input-mask]),textarea';

    //properties
    this.moduleApp = moduleApp;
    this.nameCtrl = nameCtrl;
    this.dataStructure = dataStructure;
    this.dataValue = dataValue;
    //elements
    this.form = $(selectorForm);
    this.selectpicker = this.form.find(selectorSelectpicker);
    this.inputmask = this.form.find(selectorInputmask);
    this.otherField = this.form.find(selectorOtherField);

    AngularJsonb.prototype.initModule = function(scope, $this) //this.__proto__.myFun
    {
        //init params
        scope.currentRowId = -1;
        scope.list = scope.thisClass.dataValue;

        //valid field on change
        var change = function(){
            //console.log('change');
            if ($(this).is('[required]')) $this.validField($(this), scope);
        }
        scope.thisClass.selectpicker.on('change', change);
        scope.thisClass.inputmask.on('change', change);
        scope.thisClass.otherField.on('change', change);

        //set fields default
        for (var i in scope.thisClass.dataStructure){
            scope[scope.thisClass.dataStructure[i].name] = scope.thisClass.dataStructure[i].default;
        }
    }
    AngularJsonb.prototype.initAfterModule = function(scope, $this){}
    AngularJsonb.prototype.setView = function(scope)
    {
        //scope.type_id = $('[ng-model="type_id"]').val();
        //scope.currency_id = $('[ng-model="currency_id"]').val();
    };
    AngularJsonb.prototype.renderView = function(scope)
    {
        $this.clearForm(scope);
        setTimeout(function(){
            scope.thisClass.form.find(selectorSelectpicker).selectpicker('refresh');
        }, 100);
    };
    AngularJsonb.prototype.saveValue = function(scope){};
    AngularJsonb.prototype.clearForm = function(scope)
    {
        //scope.thisClass.form.removeClass('ng-h-add');
        scope.thisClass.selectpicker.find('+ .bootstrap-select').removeClass('has-error has-success');
        scope.thisClass.inputmask.removeClass('has-error has-success');
        scope.thisClass.otherField.removeClass('has-error has-success');
    };
    AngularJsonb.prototype.validField = function(elem, scope)
    {
        var elemName = elem.attr('ng-model');
        var elemValue = elem.val();

        //once
        var once = true;
        for (var i in scope.thisClass.dataStructure) {
            var $fieldStructure = scope.thisClass.dataStructure[i];
            if ($fieldStructure.once && ($fieldStructure.name == elemName)){
                //console.log('once');
                for (var i in scope.list) {
                    var $field = scope.list[i];
                    if ((scope.currentRowId != i) && ($field[elemName] == elemValue)){ once = false; }
                }
            }
        }

        if (elem.find('+ .bootstrap-select').length){ //selectpicker
            if (elemValue && once){
                elem.find('+ .bootstrap-select').removeClass('has-error').addClass('has-success');
                return true;
            }else{
                elem.find('+ .bootstrap-select').removeClass('has-success').addClass('has-error');
                return false;
            }
        }else{ //inputmask and other field
            if ((elemValue || !elem.is('[required]')) && once){
                elem.removeClass('has-error').addClass('has-success');
                return true;
            }else{
                elem.removeClass('has-success').addClass('has-error');
                return false;
            }
        }
    };
    AngularJsonb.prototype.validForm = function(scope)
    {
        var validSelectpicker = validInputmask = validOtherField = true;
        $selectpicker = scope.thisClass.form.find(selectorSelectpicker);
        $inputmask = scope.thisClass.form.find(selectorInputmask);
        $otherField = scope.thisClass.form.find(selectorOtherField);

        //selectpicker
        $selectpicker.each(function(){
            if (!$this.validField($(this), scope)) validSelectpicker = false;
        });

        //inputmask
        $inputmask.each(function(){
            if (!$this.validField($(this), scope)) validInputmask = false;
        });

        //other field
        $otherField.each(function(){
            if (!$this.validField($(this), scope)) validOtherField = false;
        });

        //input, textarea
        if (validSelectpicker && validInputmask && validOtherField){
            $this.clearForm(scope);
            //scope.thisClass.form.removeClass('ng-h-add');
            return true;
        }else{
            //scope.thisClass.form.addClass('ng-h-add');
            return false;
        }
    };
    $this.moduleApp.controller(nameCtrl, function( $scope )
    {
        $scope.thisClass = $this;//todo: one property
        $scope.thisClass.initModule($scope, $scope.thisClass);

        function clearOnly($scope, name, value, is_only, type_id){
            if (is_only){
                for (var i in $scope.list){
                    if (!type_id) {
                        $scope.list[i][name] = value;
                    }else{
                        if (type_id == $scope.list[i]['type_id']) {
                            $scope.list[i][name] = value;
                        }
                    }
                }
            }
        }

        $scope.addNewRow = function() {
            if ($scope.thisClass.validForm($scope)) {
                $scope.thisClass.setView($scope);

                var row = {};
                var type_id = $scope['type_id'];
                for (var i in $scope.thisClass.dataStructure){
                    var $field = $scope.thisClass.dataStructure[i];

                    //only
                    if ($field.only && $scope[$field.name]) {
                        if (!$field.type) {
                            clearOnly($scope, $field.name, $field.default, $field.only);
                        }else{
                            clearOnly($scope, $field.name, $field.default, $field.only, type_id);
                        }
                    }

                    //save and default
                    row[$field.name] =
                        (!$scope[$field.name] && $scope[$field.name] !== false && $field.default) ? $field.default : $scope[$field.name];
                    $scope[$field.name] = $field.default;

                    //clear field type_id
                    if ($field.type_id && ($field.type_id != type_id)){
                        delete(row[$field.name]);
                    }
                }
                $scope.list.push(row);
                $scope.currentRowId = -1;

                $scope.$applyAsync();
                $scope.thisClass.renderView($scope);
                $scope.thisClass.saveValue($scope);
            }
        };
        $scope.saveRow = function() {
            if( $scope.currentRowId > -1 && $scope.thisClass.validForm($scope) ){
                $scope.thisClass.setView($scope);

                var id = $scope.currentRowId;
                var type_id = $scope['type_id'];
                for (var i in $scope.thisClass.dataStructure){
                    var $field = $scope.thisClass.dataStructure[i];

                    //only
                    if ($field.only && $scope[$field.name]) {
                        if (!$field.type) {
                            clearOnly($scope, $field.name, $field.default, $field.only);
                        }else{
                            clearOnly($scope, $field.name, $field.default, $field.only, type_id);
                        }
                    }

                    //save and default
                    $scope.list[id][$field.name] =
                        (!$scope[$field.name] && $scope[$field.name] !== false && $field.default) ? $field.default : $scope[$field.name];
                    $scope[$field.name] = $field.default;

                    //clear field type_id
                    if ($field.type_id && ($field.type_id != type_id)){
                        delete($scope.list[id][$field.name]);
                    }
                }
                $scope.currentRowId = -1;

                $scope.$applyAsync();
                $scope.thisClass.renderView($scope);
                $scope.thisClass.saveValue($scope);
            }
        };
        $scope.editRow = function ( id ) {
            $scope.currentRowId = id;

            for (var i in $scope.thisClass.dataStructure){
                var $field = $scope.thisClass.dataStructure[i];

                $scope[$field.name] = $scope.list[id][$field.name];
            }

            $scope.$applyAsync();
            $scope.thisClass.renderView($scope);
        };
        $scope.deleteRow = function( id ) {
            $scope.list.splice( id, 1 );
            if ($scope.currentRowId == id){
                $scope.currentRowId = -1;
            }

            $scope.$applyAsync();
            $scope.thisClass.saveValue($scope);
        };
        //$scope.$watch('type_id', function() {
        //  console.log('refresh');
        //}, true);

        $scope.thisClass.initAfterModule($scope, $scope.thisClass);
    });
    //inputmask
    moduleApp.directive('inputMask', function(){
        return {
            restrict: 'A',
            link: function($scope, el, attrs){
                $(el).inputmask($scope.$eval(attrs.inputMask));
                $(el).on('change', function(){
                    //console.log('change');
                    //if (attrs.required) $scope.thisClass.validField($(this));
                    $scope.$eval(attrs.ngModel + "='" + el.val() + "'");
                    $scope.$apply();
                });
            }
        };
    });
    //selectpicker
    moduleApp.directive('selectpicker', function(){
        return {
            restrict: 'A',
            link: function($scope, el, attrs){
                //if ($(el).closest('[ng-controller="' + $scope.thisClass.nameCtrl + '"]').length) {
                $(el).selectpicker($scope.$eval(attrs.selectpicker));
                $(el).on('change', function () {
                    //console.log('change'); //todo: one do
                    //if (attrs.required) $scope.thisClass.validField($(this));
                    $scope.$eval(attrs.ngModel + "='" + el.val() + "'");
                    $scope.$apply();
                });
                //}
            }
        };
    });
    //compile
    //moduleApp.directive('compile', ['$compile', function ($compile) { //example: <span compile="param"></span>
    //    return function(scope, element, attrs) {
    //        scope.$watch(
    //            function(scope) {
    //                // watch the 'compile' expression for changes
    //                return scope.$eval(attrs.compile);
    //            },
    //            function(value) {
    //                // when the 'compile' expression changes
    //                // assign it into the current DOM
    //                element.html(value);
    //
    //                // compile the new DOM and link it to the current
    //                // scope.
    //                // NOTE: we only compile .childNodes so that
    //                // we don't get into infinite loop compiling ourselves
    //                $compile(element.contents())(scope);
    //            }
    //        );
    //    };
    //}]);
    //compile-ajax
    moduleApp.directive('compileAjax', ['$compile', '$http', function ($compile, $http) {  //example: <div compile-ajax ng-cloak></div> (+ ajax response in controller)
        return function(scope, element, attrs) {
            element.on('pjax:complete', function() {
                //console.log('compile');
                var url = location.href;
                var $element = $(element);

                if (!$element.data('url') || $element.data('url') != location.href) {
                    $element.data('url', url);
                    //get new data
                    $.get(url, function(data){
                        //compile
                        scope.list = data;
                        scope.currentRowId = -1;
                        $compile(element.contents())(scope);
                        scope.$applyAsync();
                    });
                }
            });
        };
    }]);
    //filters
    moduleApp.filter('filterSelect', function () {
        return function (item, array) {
            return array[item];
        };
    });
}
