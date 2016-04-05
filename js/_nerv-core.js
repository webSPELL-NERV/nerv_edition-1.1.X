function ajaxExecute($url,$back_url){
	$("#loading-ajax").slideDown(50);
	$( "#result" ).load( $url, function() {
		$("#loading-ajax").slideUp(50, function(){
			window.location.href = $back_url;
		});
	});
	return false;
}