$(function() {

	$("#login , #password").keyup(function(e) {
		if(e.keyCode == 13) {
	  		Login();
		}
	});

	//Logout
	$("#LogOutMenu").click(function() {
		$.post("Controllers/Manager.php", {action: "Logout"}, function() {
			window.location.replace('./');
			document.location = "./";
		});
	});
	
	//Login
	$( "input#Login" ).click(function() { Login();	});

});

function Login()
{
	var bodyContent = $.ajax({
		url: "Controllers/Manager.php",
		type: "POST",
		data: { login: $("#login").val(), password: $("#password").val(), action: "Auth" },
		success: function(msg){
			if(msg == "1")
			{
				window.location.replace('./');
				document.location = "./";
			}
			else
			{
				alert(msg);
			}
		}
	});
}

function OpenWindow(title, url, width, height, left, top)
{	
	$.window.prepare({
	   dock: "bottom",
	   dockArea: $('#dockArea')
	});	
	
	var index_highest = 0;
	$('div').each(function()
	{
		var index_current = parseInt($(this).css("z-index"));
		if(index_current < 9999 && index_current > index_highest) {
			index_highest = index_current;
		}
	});
	
	if(!top)  var top  = 10;
	if(!left) var left = 10;
	
	var newWindow = $("#container").window({
	   title: title,
	   url: url,
	   x: left,
	   y: top,
	   width: width,
	   height: height,
	   maxWidth: -1,
   	   maxHeight: -1,
	   bookmarkable: true,
	   checkBoundary: true,
	   withinBrowserWindow: true,
	   z: index_highest+1
	});
	
	setTimeout(Layout, 200);
	
	return newWindow;
}