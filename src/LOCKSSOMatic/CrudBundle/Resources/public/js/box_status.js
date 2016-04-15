(function($){
	
	$(".details-toggle").click(function(){
		var id = $(this).data('autoggle');
		$("#" + id).toggle();
	});
	
})(jQuery);