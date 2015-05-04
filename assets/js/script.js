$(document).ready(function(){
    
    $('select:not(.dont-auto-render)').selectpicker();
    $('.footable').footable();
    
    var navTabs = $('a[data-toggle="tab"]');
    
	navTabs.on('shown', function (e) {
		var status = $(e.target).attr('href');
		window.location.hash = status;
		$('table.footable').trigger('footable_resize');
	});
    
	var hash = window.location.hash;
	hash && navTabs.filter('[href="' + hash + '"]').tab('show');
});