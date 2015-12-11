function System(action,title,w,h)
{
	if($.window.getWindow('System'+action))
	{
		var opened = $.window.getWindow('System'+action);
		if(opened.isMinimized) opened.restore();
		opened.select();
		opened.refreshWindow();
	}
	else
	{
		var HDWindow = OpenWindow(title, "./Controllers/System.php?action="+action+"", w, h);
		HDWindow.setWindowId('System'+action);
	}
}

function Update()
{
	var thisWindow = $.window.getSelectedWindow();
	thisWindow.close();
	System("update", "Ferrarezi Web Update System", 400,270);
}