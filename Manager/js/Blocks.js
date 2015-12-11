function Blocks(action,title)
{
	if($.window.getWindow('Blocks'+action))
	{
		var opened = $.window.getWindow('Blocks'+action);
		if(opened.isMinimized) opened.restore();
		opened.select();
		opened.refreshWindow();
	}
	else
	{
		var HDWindow = OpenWindow(title, "./Controllers/Blocks.php?action="+action+"", 750, 550);
		HDWindow.setWindowId('Blocks'+action);
	}
}

function UnBlock(idx)
{
	var thisWindow = $.window.getSelectedWindow();
	if(confirm("Do you confirm?"))
	{
		$.post("./Controllers/Blocks.php?action=UnBlock", { idx:idx }, function(data) {
			$.Growl.show(data);
			thisWindow.refreshWindow();
		});
	}
}

function BlockUser()
{
	var ref = $("input[type=radio][name=ref]:checked").val();
	var value = $("#value").val();
	var cause = $("#cause").val();
	var duration = $("#duration").val();
	var image = $("#image").val();
	
	$.post("./Controllers/Blocks.php?action=BlockUser", { ref:ref, value:value, cause:cause, duration:duration, image:image }, function(data) {
		$.Growl.show(data);
		$("#value").val("");
	});	
}

function EditBlock(idx)
{
	OpenWindow("", "./Controllers/Blocks.php?action=EditBlock&idx="+idx+"", 500, 400);
}

function SaveBlock(idx)
{
	var cause = $("#cause").val();
	var duration = $("#duration").val();
	var image = $("#image").val();
	
	var thisWindow = $.window.getSelectedWindow();
	var myWindow = $.window.getWindow('Blockslist');
	
	$.post("./Controllers/Blocks.php?action=SaveBlock", { idx:idx, cause:cause, duration:duration, image:image }, function(data) {
		$.Growl.show(data);		
		thisWindow.close();		
		myWindow.refreshWindow();
	});	
}