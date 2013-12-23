$(document).ready(function(){
    $('select:not(.dont-auto-render)').selectpicker();
    $('.footable').footable();
    
    var navTabs = $('.nav-pills a, .nav-pills a');
    
            var hash = window.location.hash;
            hash && navTabs.filter('[data-value="' + hash + '"]').tab('show');
    
            navTabs.on('shown', function (e) {
                var status = $(e.target).attr('data-value');
                window.location.hash = status;
            });
});