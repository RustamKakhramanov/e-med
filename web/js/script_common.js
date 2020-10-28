$(document).ready(function(){
    //data-pjax="0"
    $(document).on('pjax:click', 'a[data-pjax="0"]', function(event) {
        return false;
    })

    ////URL
    //window.url = new Url(location.href);
    //
    ////btn back
    ////if (location.href != document.referrer){ localStorage.setItem('backUrl', document.referrer); }
    //$('.j-back').click(function(){
    //    //history.back();
    //    //if (!localStorage.getItem('backUrl') || location.href == document.referrer) {
    //    //    location.href = '/';
    //    //}else {
    //    //    location.href = localStorage.getItem('backUrl');
    //    //}
    //
    //    location.href = '/message' + ((window.url.query.status) ? '?status=' + window.url.query.status : '');
    //    return false;
    //});
    //$('.j-btn-action').click(function(){
    //    var url = new Url($(this).prop('href'));
    //    if (window.url.query.status) url.query.status = window.url.query.status;
    //    location.href = url;
    //    return false;
    //});
    //
    ////href
    //$('.j-href').click(function(){
    //    location.href = $(this).data('href');
    //    return false;
    //});
    //
    ////click off
    //$('.j-click-off').click(function(){
    //    return false;
    //});
    //
    ////bootbox
    //bootbox.setDefaults({
    //    locale : 'ru'
    //});
    //yii.confirm = function (message, ok, cancel) {
    //    bootbox.confirm({
    //        message: message,
    //        callback: function (confirmed) {
    //            if (confirmed) {
    //                !ok || ok();
    //            } else {
    //                !cancel || cancel();
    //            }
    //        }
    //    });
    //    // confirm will always return false on the first call
    //    // to cancel click handler
    //    return false;
    //}
    //
    ////iCheck change
    //$(".iCheck-helper").click(function(){
    //    $(this).parent().children("input[type=checkbox]").trigger("change");
    //});

    //widget calendar write
    //if ($.inputmask) {
    //    $('.j-calendar_write').removeAttr('readonly');
    //    $('.j-calendar_write').inputmask("d.m.y", {"clearIncomplete": true});
    //}
});