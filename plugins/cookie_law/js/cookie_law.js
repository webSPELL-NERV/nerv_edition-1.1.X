function createCookie(name, value, days) {
    var expires;

    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    } else {
        expires = "";
    }
    document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
}

function eraseCookie(name) {
    createCookie(name, "", -1);
}

$(document).ready(function(){
	//eraseCookie("eucookie");
	
	if( document.cookie.indexOf("eucookie") ===-1 ){
		$(".cookielaw").show();
	}else{
		$(".cookielaw").hide();
	}

	$("#removecookie").click(function () {
		createCookie('eucookie','eucookie',3);
		$(".cookielaw").remove();
	});
});