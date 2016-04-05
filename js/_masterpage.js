
$(document).ready(function(){
	generateEmoticons(".wrapper content");

	function generateEmoticons(classname){
		String.prototype.replaceAll = function(search, replacement) {
			var target = this;
			return target.split(search).join(replacement);
		};

		var html = $("html").html();
		
		$(classname).addClass("emoticon-text");
		
		var images = new Array();
		var words = new Array();
		
		// :-P
		 words[0] = ":-\\\P";
		images[0] = "tongue.png";
		
		// :-D
		 words[1] = ":-\\\D";
		images[1] = "smile-big.png";
		
		// :-)
		 words[2] = ":-\\\)";
		images[2] = "smile.png";
		
		// ;-)
		 words[3] = ";-\\\)";
		images[3] = "twink.png";
		
		// :-O
		 words[4] = ":-\\\O";
		images[4] = "surprised.png";
		
		// :-(
		 words[5] = ":-\\\(";
		images[5] = "nothappy.png";
		
		// :-o
		 words[6] = ":-\\\o";
		images[6] = "surprised.png";

		// <3
		 words[7] = ":heart:";
		images[7] = "heart.gif";
		
		
		var emoLength = images.length;
		var html = $(classname).html();
		var reg;
		for (var i = 0; i < emoLength; i++) {
			html = html.replaceAll(RegExp(words[i], 'gi'),"<img class='emoticon' src='images/emoticons/"+images[i]+"' />");
		}
		$(classname).html(html);
	}
	
	
});

