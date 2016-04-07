function ajaxExecute($url,$back_url){
	$("#loading-ajax").slideDown(50);
	$( "#result" ).load( $url, function() {
		$("#loading-ajax").slideUp(50, function(){
			window.location.href = $back_url;
		});
	});
	return false;
}

$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
	
	$('.horizontal .mobile-bars i').click(function(){
		var ul = $(".horizontal-bars").next("ul");
		if(ul.is(":visible")){
			ul.slideUp("fast");
		}else{
			ul.slideDown("fast");
		}
	});
});